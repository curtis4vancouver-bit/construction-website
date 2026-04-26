'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'

/**
 * Shifts a milestone and all subsequent milestones in the project by a specific number of days.
 */
export async function shiftMilestoneDates(milestoneId: string, newStartDateStr: string) {
  const supabase = await createClient()

  // 1. Get the target milestone
  const { data: targetMilestone, error: fetchError } = await supabase
    .from('milestones')
    .select('*')
    .eq('id', milestoneId)
    .single()

  if (fetchError || !targetMilestone) {
    return { error: 'Milestone not found' }
  }

  // Calculate day difference
  const oldDate = new Date(targetMilestone.start_date)
  const newDate = new Date(newStartDateStr)
  
  // Set time to midnight to avoid timezone issues when calculating diff
  oldDate.setUTCHours(0, 0, 0, 0)
  newDate.setUTCHours(0, 0, 0, 0)

  const diffTime = Math.abs(newDate.getTime() - oldDate.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  const isForward = newDate > oldDate
  const daysToShift = isForward ? diffDays : -diffDays

  if (daysToShift === 0) {
    return { success: true } // No change needed
  }

  // 2. Fetch all milestones for this project that have sequence_order >= target's sequence_order
  const { data: dependentMilestones, error: depError } = await supabase
    .from('milestones')
    .select('*')
    .eq('project_id', targetMilestone.project_id)
    .gte('sequence_order', targetMilestone.sequence_order)
    .order('sequence_order', { ascending: true })

  if (depError || !dependentMilestones) {
    return { error: 'Failed to fetch dependent milestones' }
  }

  // 3. Calculate new dates and update sequentially
  for (const ms of dependentMilestones) {
    const msStart = new Date(ms.start_date)
    msStart.setUTCDate(msStart.getUTCDate() + daysToShift)
    
    const msEnd = new Date(ms.end_date)
    msEnd.setUTCDate(msEnd.getUTCDate() + daysToShift)

    await supabase
      .from('milestones')
      .update({
        start_date: msStart.toISOString().split('T')[0],
        end_date: msEnd.toISOString().split('T')[0]
      })
      .eq('id', ms.id)
  }

  // 4. Add an audit log to communications
  const { data: { user } } = await supabase.auth.getUser()
  if (user) {
    await supabase
      .from('communications')
      .insert([
        {
          project_id: targetMilestone.project_id,
          sender_id: user.id,
          message: `AUDIT LOG: Milestone "${targetMilestone.title}" and subsequent phases were shifted by ${daysToShift} days.`
        }
      ])
  }

  revalidatePath('/dashboard/calendar')
  revalidatePath('/dashboard')
  
  return { success: true, shifted: dependentMilestones.length }
}

/**
 * Utility action to create a new milestone with a sequence order
 */
export async function createMilestone(projectId: string, title: string, startDateStr: string, durationDays: number, sequenceOrder: number) {
  const supabase = await createClient()

  const startDate = new Date(startDateStr)
  const endDate = new Date(startDateStr)
  endDate.setUTCDate(endDate.getUTCDate() + durationDays - 1)

  const { error } = await supabase.from('milestones').insert([{
    project_id: projectId,
    title,
    start_date: startDate.toISOString().split('T')[0],
    end_date: endDate.toISOString().split('T')[0],
    duration_days: durationDays,
    sequence_order: sequenceOrder,
    status: 'pending'
  }])

  if (error) {
    console.error(error)
    return { error: 'Failed to create milestone' }
  }

  revalidatePath('/dashboard/calendar')
  return { success: true }
}

/**
 * Trade action: Propose a new date for a milestone.
 */
export async function proposeDate(milestoneId: string, proposedStartDateStr: string) {
  const supabase = await createClient()

  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return { error: 'Not authenticated' }

  const { error } = await supabase.from('date_proposals').insert([{
    milestone_id: milestoneId,
    trade_id: user.id,
    proposed_start_date: proposedStartDateStr,
    status: 'pending'
  }])

  if (error) {
    return { error: 'Failed to submit proposal' }
  }

  revalidatePath('/dashboard/calendar')
  return { success: true }
}

/**
 * PM action: Approve a date proposal and cascade the shift.
 */
export async function approveProposal(proposalId: string) {
  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return { error: 'Not authenticated' }

  // Get the proposal
  const { data: proposal, error: pErr } = await supabase
    .from('date_proposals')
    .select('*, milestones(id, title, project_id)')
    .eq('id', proposalId)
    .single()

  if (pErr || !proposal) return { error: 'Proposal not found' }

  // Verify user is PM of the project
  const milestone = proposal.milestones as any
  const { data: project } = await supabase
    .from('projects')
    .select('pm_id')
    .eq('id', milestone.project_id)
    .single()

  if (project?.pm_id !== user.id) return { error: 'Only the PM can approve proposals' }

  // Apply the shift
  const result = await shiftMilestoneDates(proposal.milestone_id, proposal.proposed_start_date)
  if (result.error) return result

  // Mark proposal as approved
  await supabase.from('date_proposals').update({ status: 'approved' }).eq('id', proposalId)

  revalidatePath('/dashboard/calendar')
  return { success: true, shifted: result.shifted }
}

/**
 * PM action: Deny a date proposal.
 */
export async function denyProposal(proposalId: string) {
  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return { error: 'Not authenticated' }

  await supabase.from('date_proposals').update({ status: 'denied' }).eq('id', proposalId)

  revalidatePath('/dashboard/calendar')
  return { success: true }
}
