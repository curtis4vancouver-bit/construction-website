'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'
import { redirect } from 'next/navigation'

export async function acceptInvite(formData: FormData) {
  const projectId = formData.get('project_id') as string
  if (!projectId) {
    redirect('/invite?error=Invalid+invite+link')
  }

  const supabase = await createClient()
  const { data: { user }, error: authError } = await supabase.auth.getUser()

  if (authError || !user) {
    redirect('/login')
  }

  // Find the project
  const { data: project, error: projError } = await supabase
    .from('projects')
    .select('id, owner_id')
    .eq('id', projectId)
    .single()

  if (projError || !project) {
    redirect('/invite?error=Project+not+found')
  }

  if (project.owner_id) {
    if (project.owner_id === user.id) {
      redirect('/dashboard') // Already the owner
    }
    redirect('/invite?error=This+project+already+has+an+assigned+owner')
  }

  // Update the project to set the logged-in user as the owner
  const { error: updateError } = await supabase
    .from('projects')
    .update({ owner_id: user.id })
    .eq('id', projectId)

  if (updateError) {
    redirect('/invite?error=Failed+to+accept+invite')
  }

  // Also make sure their role in public.users is set to 'owner' if it's not already
  await supabase
    .from('users')
    .update({ role: 'owner', is_approved: true })
    .eq('id', user.id)

  revalidatePath('/dashboard')
  redirect('/dashboard')
}
