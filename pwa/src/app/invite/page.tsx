import { createClient } from '@/utils/supabase/server'
import { redirect } from 'next/navigation'
import { acceptInvite } from './actions'
import Link from 'next/link'

export default async function InvitePage({
  searchParams,
}: {
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>
}) {
  const params = await searchParams
  const projectId = params.project_id as string
  
  if (!projectId) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center p-4">
        <div className="bg-zinc-900 border border-red-900/50 p-8 rounded-xl max-w-md w-full text-center">
          <h1 className="text-red-400 text-xl font-light mb-4">Invalid Invite Link</h1>
          <p className="text-zinc-500 text-sm">This link is missing a project identifier. Please request a new link from your Project Manager.</p>
        </div>
      </div>
    )
  }

  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()

  let projectData = null

  // Fetch project details to show what they are joining
  const { data: project } = await supabase
    .from('projects')
    .select('title, address, owner_id')
    .eq('id', projectId)
    .single()

  if (!project) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center p-4">
        <div className="bg-zinc-900 border border-zinc-800 p-8 rounded-xl max-w-md w-full text-center">
          <h1 className="text-white text-xl font-light mb-4">Project Not Found</h1>
          <p className="text-zinc-500 text-sm">The project associated with this invite no longer exists.</p>
        </div>
      </div>
    )
  }

  if (project.owner_id) {
    return (
      <div className="min-h-screen bg-black flex items-center justify-center p-4">
        <div className="bg-zinc-900 border border-emerald-900/50 p-8 rounded-xl max-w-md w-full text-center">
          <h1 className="text-emerald-400 text-xl font-light mb-4">Project Already Claimed</h1>
          <p className="text-zinc-500 text-sm mb-6">An owner has already connected to this project.</p>
          <Link href="/dashboard" className="text-emerald-500 hover:text-emerald-400 text-sm">Go to Dashboard</Link>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-black flex items-center justify-center p-4">
      <div className="bg-zinc-900/50 border border-zinc-800 p-8 rounded-xl max-w-md w-full">
        <div className="text-center mb-8">
          <h1 className="text-2xl font-light text-white mb-2">Project Invitation</h1>
          <p className="text-zinc-400 text-sm">
            You have been invited to become the Property Owner for:
          </p>
          <div className="mt-4 p-4 bg-black border border-zinc-800 rounded-lg">
            <h2 className="text-lg text-emerald-400 font-medium">{project.title}</h2>
            {project.address && <p className="text-zinc-500 text-sm mt-1">{project.address}</p>}
          </div>
        </div>

        {!user ? (
          <div className="space-y-4">
            <div className="bg-amber-900/20 border border-amber-900/50 p-4 rounded text-sm text-amber-500 mb-6 text-center">
              You must be logged in to accept this invitation.
            </div>
            <Link 
              href={`/login?redirect=/invite?project_id=${projectId}`}
              className="block w-full bg-white text-black text-center font-medium py-3 rounded-lg hover:bg-zinc-200 transition-colors"
            >
              Log In or Create Account
            </Link>
          </div>
        ) : (
          <form action={acceptInvite}>
            <input type="hidden" name="project_id" value={projectId} />
            <div className="bg-emerald-900/10 border border-emerald-900/30 p-4 rounded text-sm text-emerald-500 mb-6 text-center">
              You are logged in as {user.email}
            </div>
            <button 
              type="submit" 
              className="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-3 rounded-lg transition-colors"
            >
              Accept Invitation & Connect
            </button>
          </form>
        )}
      </div>
    </div>
  )
}
