import { createClient } from '@/utils/supabase/server';
import { redirect } from 'next/navigation';
import { logout } from '@/app/login/actions';

export default async function PendingPage() {
  const supabase = await createClient();
  const { data: { user }, error: authError } = await supabase.auth.getUser();

  if (!user || authError) {
    redirect('/login');
  }

  const { data: profile } = await supabase
    .from('users')
    .select('is_approved, full_name')
    .eq('id', user.id)
    .single();

  if (profile?.is_approved) {
    redirect('/dashboard');
  }

  return (
    <div className="min-h-screen bg-zinc-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 className="mt-6 text-center text-3xl font-light tracking-tight text-white uppercase">
          Keystone <span className="font-bold text-emerald-500">Possibilities</span>
        </h2>
        <p className="mt-2 text-center text-sm text-zinc-400 tracking-widest uppercase">
          Authorization Pending
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="bg-zinc-900/50 border border-zinc-800 py-8 px-8 shadow sm:rounded-xl text-center">
          <svg className="mx-auto h-12 w-12 text-emerald-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          
          <h3 className="text-xl font-medium text-white mb-2">Account Restricted</h3>
          <p className="text-sm text-zinc-400 mb-6 leading-relaxed">
            Your account ({user.email}) has been created, but it requires authorization to access the Command Center. 
            <br /><br />
            If you need to pay the entrance fee or establish a contract, please contact your project manager or use the payment portal. Once authorized, your access will be automatically granted.
          </p>

          <form action={logout}>
            <button className="w-full flex justify-center py-3 px-4 border border-zinc-700 rounded-lg shadow-sm text-sm font-medium text-zinc-300 bg-zinc-800 hover:bg-zinc-700 transition-colors">
              Sign Out
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
