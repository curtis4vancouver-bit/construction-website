import { createClient } from '@/utils/supabase/server';

export default async function TradesPage() {
  const supabase = await createClient();

  const { data: trades } = await supabase
    .from('project_trades')
    .select(`
      id,
      trade_category,
      status,
      locked_bid,
      user_id,
      users ( full_name, company_name, email )
    `)
    .order('created_at', { ascending: false });

  return (
    <div className="p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex justify-between items-end border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-zinc-500 font-medium mb-2">Team Management</p>
          <h2 className="text-3xl font-light text-white mb-2">Trades & Teams</h2>
          <p className="text-zinc-400">Manage subcontractors, assign tasks, and review bids.</p>
        </div>
      </header>

      {trades && trades.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {trades.map((trade: any) => (
            <div key={trade.id} className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl hover:border-zinc-700 transition-colors">
              <div className="flex justify-between items-start mb-4">
                <span className="px-2 py-1 bg-zinc-800 text-zinc-300 text-xs rounded border border-zinc-700">
                  {trade.trade_category}
                </span>
                <span className={`w-2 h-2 rounded-full ${trade.status === 'active' ? 'bg-emerald-500' : 'bg-amber-500'}`}></span>
              </div>
              <h3 className="text-lg font-medium text-white mb-1">
                {trade.users?.company_name || trade.users?.full_name || 'Unknown User'}
              </h3>
              <p className="text-sm text-zinc-500 mb-4">{trade.users?.email}</p>
              
              <div className="pt-4 border-t border-zinc-800 flex justify-between items-center">
                <div>
                  <p className="text-xs text-zinc-500">Locked Bid</p>
                  <p className="text-sm font-medium text-zinc-300">
                    {trade.locked_bid ? `$${trade.locked_bid.toLocaleString()}` : 'Pending'}
                  </p>
                </div>
                <button className="text-xs text-blue-400 hover:text-blue-300 transition-colors">
                  View Workspace &rarr;
                </button>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-12 text-center">
          <div className="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-4">
            <svg className="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
          <h3 className="text-xl font-medium text-zinc-300 mb-2">No Trades Assigned</h3>
          <p className="text-zinc-500 mb-6">Use the Assign Trades widget on the overview page to invite subcontractors.</p>
        </div>
      )}
    </div>
  );
}
