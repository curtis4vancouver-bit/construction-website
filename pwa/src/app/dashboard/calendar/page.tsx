import { createClient } from '@/utils/supabase/server'
import AddMilestoneForm from './AddMilestoneForm'
import { ShiftMilestoneModal } from './ShiftMilestoneModal'
import { TradeDateProposalModal } from './TradeDateProposalModal'
import { ProposalApprovalCard } from './ProposalApprovalCard'

export default async function CalendarPage() {
  const supabase = await createClient()
  
  // Get user role
  const { data: { user } } = await supabase.auth.getUser()
  const { data: userData } = await supabase.from('users').select('role').eq('id', user?.id).single()
  const role = userData?.role || 'owner'

  // Get the active project
  const { data: projects } = await supabase
    .from('projects')
    .select('*')
    .order('created_at', { ascending: false })
    
  const project = projects && projects.length > 0 ? projects[0] : null

  if (!project) {
    return <div className="p-8 text-zinc-500">No active project found. Create a project in the Overview first.</div>
  }

  // Fetch milestones
  const { data: milestones } = await supabase
    .from('milestones')
    .select('*')
    .eq('project_id', project.id)
    .order('sequence_order', { ascending: true })

  // Fetch pending proposals (for PM only)
  let proposals: any[] = []
  if (role === 'pm') {
    const { data: rawProposals } = await supabase
      .from('date_proposals')
      .select('*, milestones(title, project_id), users:trade_id(email)')
      .eq('status', 'pending')
      .order('created_at', { ascending: false })

    // Flatten the joined data
    proposals = (rawProposals || [])
      .filter((p: any) => p.milestones?.project_id === project.id)
      .map((p: any) => ({
        ...p,
        milestone_title: p.milestones?.title,
        trade_email: p.users?.email,
      }))
  }

  return (
    <div className="p-4 sm:p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-zinc-500 font-medium mb-2">Scheduling</p>
          <h2 className="text-3xl font-light text-white mb-2">Master Calendar</h2>
          <p className="text-zinc-400 text-sm">Track milestones, inspections, and project timelines.</p>
        </div>
        {role === 'pm' && <AddMilestoneForm projectId={project.id} />}
      </header>

      {/* PM: Pending Date Proposals */}
      {role === 'pm' && proposals.length > 0 && (
        <div className="space-y-3">
          <h3 className="text-sm font-medium text-cyan-400 flex items-center gap-2">
            <span className="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
            Pending Date Proposals ({proposals.length})
          </h3>
          {proposals.map((p: any) => (
            <ProposalApprovalCard key={p.id} proposal={p} />
          ))}
        </div>
      )}

      {milestones && milestones.length > 0 ? (
        <>
          {/* Mobile: card layout */}
          <div className="block sm:hidden space-y-3">
            {milestones.map((m: any) => (
              <div key={m.id} className="bg-zinc-950 border border-zinc-800 rounded-xl p-4 space-y-3">
                <div className="flex justify-between items-start">
                  <div>
                    <p className="text-xs text-zinc-500 font-mono mb-1">#{m.sequence_order}</p>
                    <p className="text-zinc-200 font-medium">{m.title}</p>
                  </div>
                  <span className={`text-[10px] px-2 py-0.5 rounded border capitalize ${
                    m.status === 'completed' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' :
                    m.status === 'in_progress' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                    'bg-zinc-800 text-zinc-400 border-zinc-700'
                  }`}>{m.status}</span>
                </div>
                <div className="flex justify-between text-sm text-zinc-400">
                  <span>{new Date(m.start_date).toLocaleDateString()}</span>
                  <span>{m.duration_days} Day{m.duration_days !== 1 ? 's' : ''}</span>
                </div>
                <div className="pt-2 border-t border-zinc-800/50">
                  {role === 'pm' ? (
                    <ShiftMilestoneModal milestone={m} />
                  ) : role === 'trade' ? (
                    <TradeDateProposalModal milestone={m} />
                  ) : (
                    <span className="text-zinc-600 text-xs">View Only</span>
                  )}
                </div>
              </div>
            ))}
          </div>

          {/* Desktop: table layout */}
          <div className="hidden sm:block bg-zinc-950 border border-zinc-800 rounded-xl overflow-hidden">
            <table className="w-full text-left text-sm">
              <thead className="bg-zinc-900 border-b border-zinc-800 text-zinc-400">
                <tr>
                  <th className="px-6 py-4 font-medium uppercase text-[10px] tracking-wider">Seq</th>
                  <th className="px-6 py-4 font-medium uppercase text-[10px] tracking-wider">Milestone</th>
                  <th className="px-6 py-4 font-medium uppercase text-[10px] tracking-wider">Start Date</th>
                  <th className="px-6 py-4 font-medium uppercase text-[10px] tracking-wider">Duration</th>
                  <th className="px-6 py-4 font-medium uppercase text-[10px] tracking-wider">Status</th>
                  <th className="px-6 py-4 font-medium uppercase text-[10px] tracking-wider text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-800/50">
                {milestones.map((m: any) => (
                  <tr key={m.id} className="hover:bg-zinc-900/50 transition-colors">
                    <td className="px-6 py-4 text-zinc-500 font-mono text-xs">{m.sequence_order}</td>
                    <td className="px-6 py-4 text-zinc-200">{m.title}</td>
                    <td className="px-6 py-4 text-zinc-400">
                      {new Date(m.start_date).toLocaleDateString()}
                    </td>
                    <td className="px-6 py-4 text-zinc-400">
                      {m.duration_days} Day{m.duration_days !== 1 ? 's' : ''}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`text-xs px-2 py-1 rounded border capitalize ${
                        m.status === 'completed' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' :
                        m.status === 'in_progress' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' :
                        'bg-zinc-800 text-zinc-400 border-zinc-700'
                      }`}>{m.status}</span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      {role === 'pm' ? (
                        <ShiftMilestoneModal milestone={m} />
                      ) : role === 'trade' ? (
                        <TradeDateProposalModal milestone={m} />
                      ) : (
                        <span className="text-zinc-600 text-xs">View Only</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </>
      ) : (
        <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-12 text-center">
          <div className="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-4">
            <svg className="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          </div>
          <h3 className="text-xl font-medium text-zinc-300 mb-2">No Milestones Yet</h3>
          <p className="text-zinc-500 mb-6 max-w-md mx-auto">Milestones will automatically populate here when trades are assigned, or you can add manual items above.</p>
        </div>
      )}
    </div>
  );
}
