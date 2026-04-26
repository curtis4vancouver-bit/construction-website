'use server'

import { createClient } from '@/utils/supabase/server'
import { redirect } from 'next/navigation'
import { revalidatePath } from 'next/cache'

export async function claimAdmin(formData: FormData) {
  const supabase = await createClient()
  const secretKey = formData.get('secretKey') as string

  const { data, error } = await supabase.rpc('claim_admin_role', {
    secret_key: secretKey
  })

  if (error || !data) {
    return redirect('/admin-setup?error=Invalid administrating key')
  }

  // Success, revalidate layout and go to dashboard
  revalidatePath('/', 'layout')
  redirect('/dashboard')
}
