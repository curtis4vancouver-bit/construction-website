import { claimAdmin } from './actions'

export default async function AdminSetupPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>
}) {
  const params = await searchParams;

  return (
    <div className="min-h-screen bg-zinc-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 className="mt-6 text-center text-3xl font-light tracking-tight text-white uppercase">
          Keystone <span className="font-bold text-emerald-500">Master</span>
        </h2>
        <p className="mt-2 text-center text-sm text-zinc-400 tracking-widest uppercase">
          Administrator Access
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div className="bg-zinc-900/50 border border-zinc-800 py-8 px-4 shadow sm:rounded-xl sm:px-10">
          <form action={claimAdmin} className="space-y-6">
            <div>
              <label className="block text-xs font-medium text-zinc-400 uppercase tracking-wider mb-1">
                Administrating Key
              </label>
              <input
                id="secretKey"
                name="secretKey"
                type="password"
                required
                className="appearance-none block w-full px-3 py-3 border border-zinc-700 rounded-lg shadow-sm placeholder-zinc-500 bg-zinc-950 text-white focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm transition-colors"
                placeholder="Enter the master key..."
              />
            </div>

            {params?.error && (
              <div className="bg-red-900/20 border border-red-900/50 text-red-400 p-3 rounded-lg text-sm text-center">
                {params.error}
              </div>
            )}

            <button
              type="submit"
              className="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 focus:outline-none transition-colors"
            >
              Elevate to Owner
            </button>
          </form>
        </div>
      </div>
    </div>
  )
}
