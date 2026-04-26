import { createClient } from '@/utils/supabase/server';

export default async function FinancialsPage() {
  const supabase = await createClient();

  const { data: ledger } = await supabase
    .from('financial_ledger')
    .select('*')
    .order('created_at', { ascending: false });

  return (
    <div className="p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex justify-between items-end border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-emerald-500 font-medium mb-2">Accounting</p>
          <h2 className="text-3xl font-light text-white mb-2">Financial Hub</h2>
          <p className="text-zinc-400">Track invoices, change orders, and capital deployment.</p>
        </div>
        <button className="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm transition-colors">
          + Submit Invoice
        </button>
      </header>

      {/* Stats row */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl">
          <p className="text-xs uppercase text-zinc-500 mb-1">Total Budget</p>
          <p className="text-xl font-light text-white">TBD</p>
        </div>
        <div className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl">
          <p className="text-xs uppercase text-zinc-500 mb-1">Approved to Date</p>
          <p className="text-xl font-light text-emerald-400">$0.00</p>
        </div>
        <div className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl">
          <p className="text-xs uppercase text-zinc-500 mb-1">Pending Approval</p>
          <p className="text-xl font-light text-amber-400">$0.00</p>
        </div>
        <div className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl">
          <p className="text-xs uppercase text-zinc-500 mb-1">Remaining Budget</p>
          <p className="text-xl font-light text-zinc-300">TBD</p>
        </div>
      </div>

      <h3 className="text-xl font-light text-white pt-4">Ledger Activity</h3>
      
      {ledger && ledger.length > 0 ? (
        <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl overflow-hidden">
          <table className="w-full text-left text-sm text-zinc-400">
            <thead className="bg-zinc-950/50 text-xs uppercase text-zinc-500 border-b border-zinc-800">
              <tr>
                <th className="px-6 py-4 font-medium">Type</th>
                <th className="px-6 py-4 font-medium">Description</th>
                <th className="px-6 py-4 font-medium">Amount</th>
                <th className="px-6 py-4 font-medium">Status</th>
                <th className="px-6 py-4 font-medium">Date</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-800/50">
              {ledger.map((item: any) => (
                <tr key={item.id} className="hover:bg-zinc-900/50 transition-colors">
                  <td className="px-6 py-4 capitalize">{item.type.replace('_', ' ')}</td>
                  <td className="px-6 py-4 text-zinc-200">{item.description}</td>
                  <td className="px-6 py-4 font-mono">${Number(item.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                  <td className="px-6 py-4">
                    <span className={`px-2 py-1 rounded text-xs border ${
                      item.status === 'approved' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 
                      item.status === 'paid' ? 'bg-blue-500/10 text-blue-500 border-blue-500/20' : 
                      'bg-amber-500/10 text-amber-500 border-amber-500/20'
                    }`}>
                      {item.status}
                    </span>
                  </td>
                  <td className="px-6 py-4">{new Date(item.created_at).toLocaleDateString()}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      ) : (
        <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-12 text-center">
          <div className="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-4">
            <svg className="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <h3 className="text-xl font-medium text-zinc-300 mb-2">No Ledger Activity</h3>
          <p className="text-zinc-500 mb-6">Invoices and change orders will appear here.</p>
        </div>
      )}
    </div>
  );
}
