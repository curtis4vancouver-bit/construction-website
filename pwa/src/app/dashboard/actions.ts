'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'

export async function createProject(formData: FormData) {
  const title = formData.get('title') as string
  const address = formData.get('address') as string
  const total_budget = formData.get('total_budget') ? parseFloat(formData.get('total_budget') as string) : null
  const pm_fee_percentage = formData.get('pm_fee_percentage') ? parseFloat(formData.get('pm_fee_percentage') as string) : 12.00

  if (!title) {
    return { error: 'Project title is required' }
  }

  const supabase = await createClient()
  
  // Get current user to set as PM
  const { data: { user }, error: authError } = await supabase.auth.getUser()
  
  if (authError || !user) {
    return { error: 'Not authenticated' }
  }

  // Insert project
  const { data, error } = await supabase
    .from('projects')
    .insert([
      {
        pm_id: user.id,
        title,
        address,
        total_budget,
        pm_fee_percentage,
        status: 'planning'
      }
    ])
    .select('id')
    .single()

  if (error || !data) {
    console.error('Project creation error:', error)
    return { error: 'Failed to create project: ' + (error?.message || 'Unknown error') }
  }

  revalidatePath('/dashboard')
  return { success: true, projectId: data.id }
}

export async function assignTrade(formData: FormData) {
  const supabase = await createClient()
  const email = formData.get('email') as string
  const project_id = formData.get('project_id') as string
  const trade_category = formData.get('trade_category') as string

  // 1. Find user by email
  const { data: userData, error: userError } = await supabase
    .from('users')
    .select('id')
    .eq('email', email)
    .single()

  if (userError || !userData) {
    return { error: 'Could not find a user with that email. Make sure they have signed up first.' }
  }

  // 2. Insert into project_trades
  const { error: insertError } = await supabase
    .from('project_trades')
    .insert([
      {
        project_id,
        user_id: userData.id,
        trade_category,
        status: 'pending'
      }
    ])

  if (insertError) {
    // If unique constraint violation
    if (insertError.code === '23505') {
      return { error: 'This user is already assigned to this trade category on this project.' }
    }
    return { error: 'Failed to assign trade: ' + insertError.message }
  }

  revalidatePath('/dashboard')
  return { success: true }
}

export async function forceRefreshRole() {
  revalidatePath('/', 'layout')
}
