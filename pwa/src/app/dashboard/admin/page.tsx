import { createClient } from '@/utils/supabase/server'
import { redirect } from 'next/navigation'
import { ApproveUserButton } from './ApproveUserButton'

export default async function AdminPage() {
  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  // Only PMs can access admin
  const { data: profile } = await supabase
    .from('users')
    .select('role')
    .eq('id', user.id)
    .single()

  if (profile?.role !== 'pm') {
    return (
      <div className="p-8 text-center">
        <h2 className="text-xl text-red-400">Access Denied</h2>
        <p className="text-zinc-500 mt-2">Only Project Managers can access the admin panel.</p>
      </div>
    )
  }

  // Get all users
  const { data: allUsers } = await supabase
    .from('users')
    .select('id, email, full_name, role, is_approved, created_at')
    .order('created_at', { ascending: false })

  const pendingUsers = (allUsers || []).filter(u => !u.is_approved)
  const approvedUsers = (allUsers || []).filter(u => u.is_approved)

  return (
    <div className="p-4 sm:p-8 max-w-5xl mx-auto space-y-8">
      <header className="border-b border-zinc-800 pb-6">
        <p className="text-[10px] uppercase tracking-[0.25em] text-amber-500 font-medium mb-2">Administration</p>
        <h2 className="text-3xl font-light text-white mb-2">User Management</h2>
        <p className="text-zinc-400 text-sm">Approve new users after payment is confirmed. Denied users stay on the waiting screen.</p>
      </header>

      {/* Pending Approvals */}
      <div className="space-y-4">
        <h3 className="text-sm font-medium text-amber-400 flex items-center gap-2">
          <span className="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
          Pending Approval ({pendingUsers.length})
        </h3>
        
        {pendingUsers.length === 0 ? (
          <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-8 text-center">
            <p className="text-zinc-500">No users waiting for approval.</p>
          </div>
        ) : (
          <div className="space-y-3">
            {pendingUsers.map(u => (
              <div key={u.id} className="bg-zinc-950 border border-zinc-800 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <p className="text-white font-medium">{u.full_name || u.email}</p>
                  <p className="text-xs text-zinc-500">{u.email} &bull; Role: <span className="text-zinc-400">{u.role || 'pending'}</span></p>
                  <p className="text-xs text-zinc-600 mt-1">Signed up: {new Date(u.created_at).toLocaleDateString()}</p>
                </div>
                <div className="flex gap-2 shrink-0">
                  <ApproveUserButton userId={u.id} userName={u.full_name || u.email} />
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Approved Users */}
      <div className="space-y-4">
        <h3 className="text-sm font-medium text-emerald-400 flex items-center gap-2">
          <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
          Active Users ({approvedUsers.length})
        </h3>
        
        <div className="bg-zinc-950 border border-zinc-800 rounded-xl overflow-hidden">
          <table className="w-full text-left text-sm">
            <thead className="bg-zinc-900 border-b border-zinc-800 text-zinc-400">
              <tr>
                <th className="px-4 py-3 font-medium uppercase text-[10px] tracking-wider">Name</th>
                <th className="px-4 py-3 font-medium uppercase text-[10px] tracking-wider">Email</th>
                <th className="px-4 py-3 font-medium uppercase text-[10px] tracking-wider">Role</th>
                <th className="px-4 py-3 font-medium uppercase text-[10px] tracking-wider">Since</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-800/50">
              {approvedUsers.map(u => (
                <tr key={u.id} className="hover:bg-zinc-900/50 transition-colors">
                  <td className="px-4 py-3 text-zinc-200">{u.full_name || '—'}</td>
                  <td className="px-4 py-3 text-zinc-400">{u.email}</td>
                  <td className="px-4 py-3">
                    <span className={`text-xs px-2 py-1 rounded border capitalize ${
                      u.role === 'pm' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                      u.role === 'owner' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                      'bg-amber-500/10 text-amber-400 border-amber-500/20'
                    }`}>{u.role}</span>
                  </td>
                  <td className="px-4 py-3 text-zinc-500 text-xs">{new Date(u.created_at).toLocaleDateString()}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
