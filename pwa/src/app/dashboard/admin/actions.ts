'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'

export async function approveUser(userId: string) {
  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return { error: 'Not authenticated' }

  // Verify caller is PM
  const { data: profile } = await supabase
    .from('users')
    .select('role')
    .eq('id', user.id)
    .single()

  if (profile?.role !== 'pm') return { error: 'Only PMs can approve users' }

  const { error } = await supabase
    .from('users')
    .update({ is_approved: true })
    .eq('id', userId)

  if (error) return { error: error.message }

  revalidatePath('/dashboard/admin')
  return { success: true }
}

export async function revokeUser(userId: string) {
  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return { error: 'Not authenticated' }

  const { data: profile } = await supabase
    .from('users')
    .select('role')
    .eq('id', user.id)
    .single()

  if (profile?.role !== 'pm') return { error: 'Only PMs can revoke users' }

  const { error } = await supabase
    .from('users')
    .update({ is_approved: false })
    .eq('id', userId)

  if (error) return { error: error.message }

  revalidatePath('/dashboard/admin')
  return { success: true }
}
