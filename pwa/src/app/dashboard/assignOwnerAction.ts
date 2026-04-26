'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'

export async function assignOwner(projectId: string, formData: FormData) {
  const supabase = await createClient()
  const email = formData.get('email') as string
  
  if (!email) {
    return { error: 'Email is required' }
  }

  // Find the user by email
  const { data: users, error: userError } = await supabase
    .from('users')
    .select('id, role')
    .eq('email', email)
    .single()

  if (userError || !users) {
    return { error: 'No user found with that email. Make sure they have signed up.' }
  }

  // Update the project
  const { error: updateError } = await supabase
    .from('projects')
    .update({ owner_id: users.id })
    .eq('id', projectId)

  if (updateError) {
    return { error: 'Failed to assign owner to project.' }
  }

  revalidatePath('/dashboard')
  return { success: true }
}
