'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'

export async function acceptTradeInvite(projectId: string, category: string) {
  const supabase = await createClient()

  // 1. Check if user is logged in
  const { data: { user }, error: authError } = await supabase.auth.getUser()
  if (authError || !user) {
    return { error: 'You must be logged in to accept this invitation.' }
  }

  // 2. Validate project exists
  const { data: project, error: projError } = await supabase
    .from('projects')
    .select('id')
    .eq('id', projectId)
    .single()

  if (projError || !project) {
    return { error: 'Invalid project link or project no longer exists.' }
  }

  // 3. Make sure they aren't already assigned to this category
  const { data: existingTrade } = await supabase
    .from('project_trades')
    .select('id')
    .eq('project_id', projectId)
    .eq('user_id', user.id)
    .eq('trade_category', category)
    .single()

  if (existingTrade) {
    return { error: 'You have already joined this project as ' + category }
  }

  // 4. Ensure the user role is 'trade' and they are approved
  await supabase
    .from('users')
    .update({ role: 'trade', is_approved: true })
    .eq('id', user.id)

  // 5. Insert into project_trades
  const { error: insertError } = await supabase
    .from('project_trades')
    .insert([{
      project_id: projectId,
      user_id: user.id,
      trade_category: category,
      status: 'active'
    }])

  if (insertError) {
    console.error(insertError)
    return { error: 'Failed to assign trade to project.' }
  }

  // 6. Add Audit Log
  await supabase.from('communications').insert([{
    project_id: projectId,
    sender_id: user.id,
    message: `AUDIT LOG: User joined the project as the ${category} trade partner.`
  }])

  revalidatePath('/dashboard')
  return { success: true }
}
