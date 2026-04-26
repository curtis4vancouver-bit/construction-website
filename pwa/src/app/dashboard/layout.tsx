import { createClient } from '@/utils/supabase/server';
import Link from "next/link";
import { redirect } from 'next/navigation';
import { logout } from '@/app/login/actions';

export default async function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const supabase = await createClient();
  const { data: { user }, error: authError } = await supabase.auth.getUser();

  if (!user || authError) {
    redirect('/login');
  }

  // Fetch the user's profile from public.users
  const { data: profile } = await supabase
    .from('users')
    .select('full_name, role, is_approved')
    .eq('id', user.id)
    .single();

  // Auto-fix: Admin emails always get PM access (no payment needed)
  const ADMIN_EMAILS = ['curtis4vancouver@gmail.com'];
  if (user.email && ADMIN_EMAILS.includes(user.email)) {
    if (!profile) {
      // Profile missing — create it
      await supabase.from('users').upsert({
        id: user.id,
        email: user.email,
        full_name: user.user_metadata?.full_name || 'Curtis',
        role: 'pm',
        is_approved: true,
      });
    } else if (!profile.is_approved || profile.role !== 'pm') {
      // Profile exists but wrong state — fix it
      await supabase.from('users').update({ role: 'pm', is_approved: true }).eq('id', user.id);
    }
  }

  // Re-fetch if we just fixed it
  const { data: finalProfile } = (profile && profile.is_approved && user.email && ADMIN_EMAILS.includes(user.email))
    ? { data: { ...profile, role: 'pm' as const, is_approved: true, full_name: profile.full_name } }
    : (!profile && user.email && ADMIN_EMAILS.includes(user.email))
      ? await supabase.from('users').select('full_name, role, is_approved').eq('id', user.id).single()
      : { data: profile };

  if (finalProfile && !finalProfile.is_approved) {
    redirect('/pending');
  }

  const fullName = finalProfile?.full_name || 'User';
  const role = finalProfile?.role || 'trade';
  const initials = fullName.split(' ').map((n: string) => n[0]).join('').substring(0, 2).toUpperCase();

  return (
    <div className="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col md:flex-row">
      {/* Sidebar */}
      <aside className="w-full md:w-64 bg-zinc-900/50 border-b md:border-b-0 md:border-r border-zinc-800 flex flex-col">
        <div className="p-6 border-b border-zinc-800">
          <h1 className="text-xl font-light tracking-widest uppercase">Keystone</h1>
          <p className="text-[10px] tracking-[0.2em] text-zinc-500 uppercase">Command Center</p>
        </div>
        
        <nav className="flex-1 p-4 space-y-2">
          <Link href="/dashboard" className="block px-4 py-3 text-sm rounded-lg bg-zinc-800/50 text-zinc-100 font-medium border border-zinc-700/50 transition-colors">
            Overview
          </Link>
          <Link href="/dashboard/calendar" className="block px-4 py-3 text-sm rounded-lg text-zinc-400 hover:bg-zinc-800/30 hover:text-zinc-200 transition-colors">
            Master Calendar
          </Link>
          <Link href="/dashboard/documents" className="block px-4 py-3 text-sm rounded-lg text-zinc-400 hover:bg-zinc-800/30 hover:text-zinc-200 transition-colors">
            Document Vault
          </Link>
          <Link href="/dashboard/inspections" className="block px-4 py-3 text-sm rounded-lg text-zinc-400 hover:bg-zinc-800/30 hover:text-zinc-200 transition-colors flex justify-between items-center">
            <span>Inspections & Reports</span>
            <span className="w-2 h-2 rounded-full bg-blue-500"></span>
          </Link>
          <Link href="/dashboard/financials" className="block px-4 py-3 text-sm rounded-lg text-zinc-400 hover:bg-zinc-800/30 hover:text-zinc-200 transition-colors">
            Financial Hub
          </Link>
          <Link href="/dashboard/trades" className="block px-4 py-3 text-sm rounded-lg text-zinc-400 hover:bg-zinc-800/30 hover:text-zinc-200 transition-colors">
            Trades & Teams
          </Link>
          <Link href="/dashboard/calculator" className="block px-4 py-3 text-sm rounded-lg text-zinc-400 hover:bg-zinc-800/30 hover:text-zinc-200 transition-colors flex justify-between items-center">
            <span>Site Calculator</span>
            <svg className="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
          </Link>
          {role === 'pm' && (
            <Link href="/dashboard/admin" className="block px-4 py-3 text-sm rounded-lg text-amber-500/70 hover:bg-amber-900/10 hover:text-amber-400 transition-colors flex justify-between items-center mt-4 border-t border-zinc-800/50 pt-4">
              <span>Admin Panel</span>
              <svg className="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </Link>
          )}
        </nav>
        
        <div className="p-4 border-t border-zinc-800">
          <div className="flex items-center gap-3 px-4 py-2">
            <div className="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-xs font-bold border border-zinc-700">
              {initials}
            </div>
            <div className="flex-1">
              <p className="text-sm font-medium text-zinc-200">{fullName}</p>
              <p className="text-xs text-zinc-500 capitalize">{role.replace('_', ' ')}</p>
            </div>
            <form action={logout}>
              <button 
                title="Sign Out" 
                className="p-2 text-zinc-500 hover:text-red-400 hover:bg-zinc-800 rounded-lg transition-colors"
              >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
              </button>
            </form>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-y-auto">
        {children}
      </main>
    </div>
  );
}
