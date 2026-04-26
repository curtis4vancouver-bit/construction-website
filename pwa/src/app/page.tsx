import Link from "next/link";

export default function Home() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-zinc-950 relative overflow-hidden">
      {/* Background gradients for a cinematic, premium feel */}
      <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-900/20 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-amber-600/10 rounded-full blur-[120px] pointer-events-none" />
      
      <main className="w-full max-w-md z-10 px-6">
        <div className="bg-zinc-900/40 backdrop-blur-xl border border-zinc-800 p-8 rounded-2xl shadow-2xl">
          <div className="text-center mb-10">
            <h1 className="text-3xl font-light tracking-widest text-zinc-100 uppercase mb-2">
              Keystone
            </h1>
            <p className="text-sm tracking-[0.2em] text-zinc-500 uppercase">
              Possibilities
            </p>
          </div>

          <form className="space-y-6">
            <div>
              <label className="block text-xs uppercase tracking-wider text-zinc-400 mb-2">Email Address</label>
              <input 
                type="email" 
                className="w-full bg-zinc-950/50 border border-zinc-800 rounded-lg px-4 py-3 text-zinc-100 focus:outline-none focus:border-zinc-600 focus:ring-1 focus:ring-zinc-600 transition-all placeholder:text-zinc-700"
                placeholder="client@example.com"
              />
            </div>
            <div>
              <label className="block text-xs uppercase tracking-wider text-zinc-400 mb-2">Password</label>
              <input 
                type="password" 
                className="w-full bg-zinc-950/50 border border-zinc-800 rounded-lg px-4 py-3 text-zinc-100 focus:outline-none focus:border-zinc-600 focus:ring-1 focus:ring-zinc-600 transition-all placeholder:text-zinc-700"
                placeholder="••••••••"
              />
            </div>

            <button 
              type="button"
              className="w-full bg-zinc-100 hover:bg-white text-zinc-900 font-medium py-3 rounded-lg transition-colors duration-200 mt-4"
            >
              Sign In
            </button>
            
            <div className="text-center mt-6">
              <Link href="/dashboard" className="text-xs text-zinc-500 hover:text-zinc-300 transition-colors underline underline-offset-4">
                Bypass to Dashboard (Dev Mode)
              </Link>
            </div>
          </form>
        </div>
        
        <p className="text-center text-zinc-600 text-xs mt-8">
          Secure, Encrypted Client Portal
        </p>
      </main>
    </div>
  );
}
