import { createClient } from '@/utils/supabase/server'
import { acceptTradeInvite } from './actions'
import { redirect } from 'next/navigation'
import Link from 'next/link'

export default async function TradeInvitePage({ searchParams }: { searchParams: Promise<{ project_id?: string, category?: string }> }) {
  const params = await searchParams
  const projectId = params.project_id
  const category = params.category

  if (!projectId || !category) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center p-4">
        <div className="bg-zinc-900 border border-zinc-800 p-8 rounded-xl max-w-md w-full text-center space-y-4">
          <h1 className="text-xl font-medium text-white">Invalid Invitation</h1>
          <p className="text-zinc-400">This link is missing required parameters. Please ask your Project Manager for a new link.</p>
        </div>
      </div>
    )
  }

  const supabase = await createClient()
  
  // 1. Verify Project
  const { data: project } = await supabase
    .from('projects')
    .select('title')
    .eq('id', projectId)
    .single()

  if (!project) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center p-4">
        <div className="bg-zinc-900 border border-zinc-800 p-8 rounded-xl max-w-md w-full text-center space-y-4">
          <h1 className="text-xl font-medium text-white">Project Not Found</h1>
          <p className="text-zinc-400">The project for this invitation could not be found.</p>
        </div>
      </div>
    )
  }

  // 2. Check Auth Status
  const { data: { user } } = await supabase.auth.getUser()

  return (
    <div className="min-h-screen bg-black flex items-center justify-center p-4 relative overflow-hidden">
      <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-900/20 via-black to-black"></div>
      
      <div className="bg-zinc-950 border border-zinc-800 p-8 rounded-xl max-w-md w-full relative z-10 shadow-2xl">
        <div className="mb-8">
          <p className="text-[10px] uppercase tracking-[0.25em] text-amber-500 font-medium mb-2">Trade Partner Invitation</p>
          <h1 className="text-3xl font-light text-white mb-2">Join {project.title}</h1>
          <p className="text-zinc-400 text-sm">
            You have been invited as the <strong className="text-emerald-400 font-medium">{category}</strong> partner for this project.
          </p>
        </div>

        {!user ? (
          <div className="space-y-4">
            <div className="bg-amber-950/30 border border-amber-900/50 p-4 rounded-lg">
              <p className="text-sm text-amber-200">You must sign in or create an account to accept this invitation and access your schedule.</p>
            </div>
            <Link 
              href="/login" 
              className="block w-full py-3 text-center bg-white text-black font-medium rounded hover:bg-zinc-200 transition-colors"
            >
              Log In / Sign Up
            </Link>
          </div>
        ) : (
          <form action={async () => {
            'use server'
            const result = await acceptTradeInvite(projectId, category)
            if (result.success) {
              redirect('/dashboard')
            } else {
              // Note: using redirect to show error is tricky, but in a real app we'd use a client component form here.
              // For simplicity of this MVP, we'll let it error out or redirect to dashboard.
              redirect('/dashboard?error=' + encodeURIComponent(result.error || 'Unknown error'))
            }
          }}>
            <button 
              type="submit" 
              className="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded transition-colors"
            >
              Accept Invitation
            </button>
            <p className="text-center text-xs text-zinc-500 mt-4">
              Signed in as {user.email}
            </p>
          </form>
        )}
      </div>
    </div>
  )
}
