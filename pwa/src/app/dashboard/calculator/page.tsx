export default function CalculatorPage() {
  return (
    <div className="p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex justify-between items-end border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-zinc-500 font-medium mb-2">Tools</p>
          <h2 className="text-3xl font-light text-white mb-2">Site Calculator</h2>
          <p className="text-zinc-400">Quick conversions, volume estimates, and material takeoffs.</p>
        </div>
      </header>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Concrete Volume */}
        <div className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl">
          <h3 className="text-lg font-medium text-white mb-4 border-b border-zinc-800 pb-2">Concrete Volume (Slab)</h3>
          <div className="space-y-4">
            <div>
              <label className="block text-xs uppercase text-zinc-500 mb-1">Length (ft)</label>
              <input type="number" className="w-full bg-zinc-950 border border-zinc-800 rounded p-2 text-white" placeholder="0" />
            </div>
            <div>
              <label className="block text-xs uppercase text-zinc-500 mb-1">Width (ft)</label>
              <input type="number" className="w-full bg-zinc-950 border border-zinc-800 rounded p-2 text-white" placeholder="0" />
            </div>
            <div>
              <label className="block text-xs uppercase text-zinc-500 mb-1">Thickness (inches)</label>
              <input type="number" className="w-full bg-zinc-950 border border-zinc-800 rounded p-2 text-white" placeholder="0" />
            </div>
            <div className="pt-4 border-t border-zinc-800 flex justify-between items-center">
              <span className="text-zinc-400">Total Cubic Yards:</span>
              <span className="text-2xl font-mono text-white">0.00</span>
            </div>
          </div>
        </div>

        {/* Linear Footage */}
        <div className="bg-zinc-900/50 border border-zinc-800 p-6 rounded-xl opacity-50">
          <h3 className="text-lg font-medium text-white mb-4 border-b border-zinc-800 pb-2">Lumber Takeoff</h3>
          <p className="text-sm text-zinc-400 py-8 text-center">Module Coming Soon</p>
        </div>
      </div>
    </div>
  );
}
