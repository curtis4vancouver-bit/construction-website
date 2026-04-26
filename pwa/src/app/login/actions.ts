'use server'

import { revalidatePath } from 'next/cache'
import { redirect } from 'next/navigation'
import { createClient } from '@/utils/supabase/server'

export async function login(formData: FormData) {
  const supabase = await createClient()

  const email = (formData.get('email') as string).trim()
  const password = formData.get('password') as string

  const { error } = await supabase.auth.signInWithPassword({
    email,
    password,
  })

  if (error) {
    console.error('SUPABASE LOGIN ERROR:', error.message)
    return redirect('/login?message=Could not authenticate user: ' + error.message)
  }

  revalidatePath('/', 'layout')
  redirect('/dashboard')
}

export async function signup(formData: FormData) {
  const supabase = await createClient()

  const email = (formData.get('email') as string).trim()
  const password = formData.get('password') as string

  const { data, error } = await supabase.auth.signUp({
    email,
    password,
    options: {
      data: {
        full_name: 'New User',
      }
    }
  })

  if (error) {
    console.error('SUPABASE SIGNUP ERROR:', error.message)
    return redirect('/login?message=Could not create user: ' + error.message)
  }

  // If email confirmation is ON, session is null.
  if (!data.session) {
    // If identities is empty, the email already exists
    if (data.user && data.user.identities && data.user.identities.length === 0) {
      return redirect('/login?message=An account with this email already exists. Please sign in.')
    }
    return redirect('/login?message=Success! Please check your email to confirm your account.')
  }

  // If email confirmation is OFF in Supabase, this works instantly.
  revalidatePath('/', 'layout')
  redirect('/dashboard')
}

export async function logout() {
  const supabase = await createClient()
  await supabase.auth.signOut()
  redirect('/login')
}
