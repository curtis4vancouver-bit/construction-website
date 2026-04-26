export default function InspectionsPage() {
  return (
    <div className="p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex justify-between items-end border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-blue-500 font-medium mb-2">Quality Assurance</p>
          <h2 className="text-3xl font-light text-white mb-2">Inspections & Reports</h2>
          <p className="text-zinc-400">Log site conditions, safety audits, and city inspection results.</p>
        </div>
        <button className="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm transition-colors">
          + New Report
        </button>
      </header>

      <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-12 text-center">
        <div className="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-4">
          <svg className="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <h3 className="text-xl font-medium text-zinc-300 mb-2">No Reports Found</h3>
        <p className="text-zinc-500 mb-6 max-w-md mx-auto">City inspection documents and site photos will appear here once the project breaks ground.</p>
      </div>
    </div>
  );
}
