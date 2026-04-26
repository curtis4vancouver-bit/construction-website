import { createClient } from '@/utils/supabase/server';
import { CreateProjectForm } from './CreateProjectForm';
import { AssignTradeForm } from './AssignTradeForm';
import AssignOwnerForm from './AssignOwnerForm';
import { forceRefreshRole } from './actions';

export default async function DashboardPage() {
  const supabase = await createClient();
  
  // Get the current user to check their role
  const { data: { user } } = await supabase.auth.getUser();
  if (!user) return null;

  // Get user profile to check role explicitly
  const { data: profile } = await supabase
    .from('users')
    .select('role')
    .eq('id', user.id)
    .single();

  const isPrivileged = profile?.role === 'owner' || profile?.role === 'pm';

  // RLS automatically filters this so the user ONLY sees their own version/projects
  const { data: projects, error } = await supabase
    .from('projects')
    .select('*')
    .order('created_at', { ascending: false });

  if (error) {
    console.error('Error fetching projects:', error);
  }

  const project = projects && projects.length > 0 ? projects[0] : null;

  if (!project) {
    if (isPrivileged) {
      return (
        <div className="p-8 max-w-6xl mx-auto min-h-[60vh]">
          <CreateProjectForm />
        </div>
      );
    } else {
      return (
        <div className="p-8 max-w-6xl mx-auto flex flex-col items-center justify-center min-h-[60vh] text-center">
          <div className="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4">
            <svg className="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
          <h2 className="text-2xl font-light text-zinc-300 mb-2">No Active Assignments</h2>
          <p className="text-zinc-500 max-w-md">
            You are approved for the platform, but you have not been assigned to any project workspaces yet. Please wait for the Project Manager to link your account.
          </p>
          <div className="mt-8 bg-zinc-900/50 p-4 rounded text-left font-mono text-sm border border-zinc-800 max-w-md w-full">
            <p className="font-bold mb-2 text-zinc-300">Diagnostic Info:</p>
            <p className="text-zinc-400">Database Role: <strong className="text-white">{profile?.role || 'null/undefined'}</strong></p>
            <p className="text-zinc-400">User ID: <span className="text-zinc-500">{user.id}</span></p>
            
            {(!profile || profile?.role === 'trade') && (
              <form action={async () => {
                'use server'
                const supabase = await createClient();
                // If profile is missing, automatically attempt to claim admin to run the UPSERT
                if (!profile) {
                  await supabase.rpc('claim_admin_role', { secret_key: 'keystone_master_2026' });
                }
                const { revalidatePath } = require('next/cache');
                revalidatePath('/', 'layout');
                const { redirect } = require('next/navigation');
                redirect('/dashboard');
              }} className="mt-4 pt-4 border-t border-zinc-800">
                <p className="text-xs text-amber-500 mb-2">
                  {!profile 
                    ? "Your profile is missing from the database. Click here to auto-fix and claim the Owner role:"
                    : "If you just ran Admin Setup, click here to force-refresh the dashboard cache:"}
                </p>
                <button type="submit" className="w-full bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold py-3 rounded transition-colors">
                  {!profile ? "Fix Profile & Claim Owner" : "Force Refresh Dashboard"}
                </button>
              </form>
            )}
          </div>
        </div>
      );
    }
  }

  return (
    <div className="p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex justify-between items-end border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-zinc-500 font-medium mb-2">Command Center</p>
          <h2 className="text-3xl font-light text-white mb-2">{project.title}</h2>
          <p className="text-zinc-400">{project.address}</p>
        </div>
        <div className="text-right">
          <p className="text-xs tracking-wider uppercase text-zinc-500 mb-1">Status</p>
          <div className="inline-flex items-center px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-sm border border-emerald-500/20 capitalize">
            <span className="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
            {project.status.replace('_', ' ')}
          </div>
        </div>
      </header>

      {/* Dynamic Hub based on Privileges */}
      {isPrivileged ? (
        <div className="bg-gradient-to-br from-zinc-900 to-zinc-950 border border-zinc-800 p-6 rounded-xl shadow-lg relative overflow-hidden">
          <div className="absolute top-0 right-0 p-8 opacity-5">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22h20L12 2zm0 3.8l7.2 14.4H4.8L12 5.8z"/></svg>
          </div>
          <h3 className="text-xl font-light text-emerald-400 mb-6 flex items-center gap-3">
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Keystone Financial Hub
          </h3>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
              <p className="text-xs uppercase tracking-wider text-zinc-500 mb-1">Total Authorized Capital</p>
              <p className="text-3xl font-light text-white">{project.total_budget ? `$${project.total_budget.toLocaleString()}` : 'TBD'}</p>
            </div>
            <div>
              <p className="text-xs uppercase tracking-wider text-zinc-500 mb-1">PM Fee Allocation ({project.pm_fee_percentage}%)</p>
              <p className="text-3xl font-light text-zinc-300">
                {project.total_budget ? `$${(project.total_budget * (project.pm_fee_percentage / 100)).toLocaleString()}` : 'TBD'}
              </p>
            </div>
            <div>
              <p className="text-xs uppercase tracking-wider text-zinc-500 mb-1">Invoices Pending Approval</p>
              <p className="text-3xl font-light text-amber-400">0</p>
              <button className="text-xs text-emerald-500 mt-2 hover:underline">View Ledger &rarr;</button>
            </div>
            <div className="bg-zinc-950/50 p-4 rounded-lg border border-zinc-800/80">
              <p className="text-xs uppercase tracking-wider text-zinc-500 mb-1">Owner-PM Contract</p>
              <div className="flex items-center text-amber-500 text-sm font-medium mb-2">
                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Signature Needed
              </div>
              <button className="text-xs bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded transition-colors w-full">
                Upload & Send to Owner
              </button>
            </div>
          </div>
        </div>
      ) : (
        <div className="bg-zinc-900/40 border border-zinc-800 p-6 rounded-xl">
          <h3 className="text-xl font-light text-white mb-6 flex items-center gap-3">
            <svg className="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Trade Contract & Compliance
          </h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="p-4 bg-zinc-950 rounded-lg border border-zinc-800/50">
              <p className="text-xs uppercase text-zinc-500 mb-2">Locked Bid Amount</p>
              <p className="text-xl font-semibold text-white">Pending Upload</p>
            </div>
            <div className="p-4 bg-zinc-950 rounded-lg border border-zinc-800/50">
              <p className="text-xs uppercase text-zinc-500 mb-2">WCB Status</p>
              <div className="flex items-center text-amber-500 text-sm font-medium">
                <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Upload Required
              </div>
            </div>
            <div className="p-4 bg-zinc-950 rounded-lg border border-zinc-800/50">
              <p className="text-xs uppercase text-zinc-500 mb-2">Subcontractor Agreement</p>
              <button className="text-sm bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded transition-colors w-full">
                Review & Sign
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Main Content Area */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">
        
        {/* Interactive Calendar / Timeline */}
        <div className="lg:col-span-2 space-y-6">
          <div className="flex justify-between items-center border-b border-zinc-800 pb-4">
             <h3 className="text-xl font-light">Interactive Master Calendar</h3>
             {isPrivileged && (
               <button className="text-xs bg-zinc-800 hover:bg-zinc-700 text-white px-3 py-1.5 rounded transition-colors">
                 + Add Schedule Item
               </button>
             )}
          </div>
          
          {/* Calendar Placeholder */}
          <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-8 cursor-text hover:bg-zinc-900/50 transition-colors group">
            <div className="text-center">
              <svg className="w-12 h-12 mx-auto text-zinc-600 group-hover:text-zinc-400 transition-colors mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              <p className="text-zinc-500">Click anywhere to drop a new milestone or inspection onto the timeline.</p>
              <p className="text-zinc-600 text-sm mt-2">Calendar data will automatically sync with assigned trades.</p>
            </div>
          </div>
        </div>

        {/* Sidebar Widgets */}
        <div className="space-y-6">
          <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-6">
             <div className="flex justify-between items-start mb-4">
               <h3 className="text-lg font-medium">Project Team</h3>
               {isPrivileged && <AssignOwnerForm projectId={project.id} currentOwnerId={project.owner_id} />}
             </div>
             <ul className="space-y-4">
                {project.pm_id ? (
                  <li className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded bg-blue-900/30 border border-blue-800/50 flex items-center justify-center text-blue-400 text-xs font-bold">PM</div>
                    <div>
                      <p className="text-sm text-zinc-300">Assigned PM</p>
                      <p className="text-xs text-zinc-500">Project Manager</p>
                    </div>
                  </li>
                ) : null}
                {project.owner_id ? (
                  <li className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded bg-emerald-900/30 border border-emerald-800/50 flex items-center justify-center text-emerald-400 text-xs font-bold">OW</div>
                    <div>
                      <p className="text-sm text-zinc-300">Property Owner</p>
                      <p className="text-xs text-zinc-500">Client</p>
                    </div>
                  </li>
                ) : null}
             </ul>
             {!project.pm_id && !project.owner_id && (
               <p className="text-sm text-zinc-500">Team members will be assigned during planning phase.</p>
             )}
             <button className="w-full mt-6 py-2 bg-zinc-800 hover:bg-zinc-700 transition-colors text-sm rounded-lg text-zinc-300">
               Message Team
             </button>
          </div>

          {/* NEW: Trade Assignment Widget for Privileged users */}
          {isPrivileged && (
            <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-6">
               <h3 className="text-lg font-medium mb-2">Assign Trades</h3>
               <p className="text-xs text-zinc-500 mb-4">Invite registered subcontractors to this workspace to unlock their portals.</p>
               <AssignTradeForm projectId={project.id} />
            </div>
          )}
        </div>

      </div>
    </div>
  );
}
