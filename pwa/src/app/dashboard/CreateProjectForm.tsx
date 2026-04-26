'use client'

import { useActionState, useState } from 'react'
import { createProject } from './actions'

const initialState = { error: '', success: false, projectId: '' }

export function CreateProjectForm() {
  const [state, formAction, isPending] = useActionState(async (prevState: any, formData: FormData) => {
    const result = await createProject(formData)
    if (result?.error) {
      return { error: result.error, success: false, projectId: '' }
    }
    return { error: '', success: true, projectId: result.projectId }
  }, initialState)

  const [copied, setCopied] = useState(false)

  const handleCopy = () => {
    if (state.projectId) {
      const inviteUrl = `${window.location.origin}/invite?project_id=${state.projectId}`
      navigator.clipboard.writeText(inviteUrl)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    }
  }

  if (state.success && state.projectId) {
    const inviteUrl = typeof window !== 'undefined' ? `${window.location.origin}/invite?project_id=${state.projectId}` : ''
    return (
      <div className="bg-zinc-900/50 border border-zinc-800 p-8 rounded-xl max-w-2xl mx-auto mt-8 text-center">
        <h2 className="text-2xl font-light text-white mb-2 text-emerald-400">Project Initialized!</h2>
        <p className="text-zinc-400 mb-6 text-sm">
          Your project workspace has been created. Send this secure invite link to the Property Owner so they can connect their account.
        </p>
        <div className="flex items-center gap-2 bg-zinc-950 border border-zinc-800 rounded p-3 mb-6">
          <input 
            type="text" 
            readOnly 
            value={inviteUrl} 
            className="flex-1 bg-transparent text-zinc-300 text-sm focus:outline-none"
          />
          <button 
            onClick={handleCopy}
            className="bg-emerald-600 hover:bg-emerald-500 text-white text-xs px-4 py-2 rounded transition-colors whitespace-nowrap"
          >
            {copied ? 'Copied!' : 'Copy Link'}
          </button>
        </div>
        <button onClick={() => window.location.reload()} className="text-zinc-500 hover:text-white text-sm underline">
          Create Another Project
        </button>
      </div>
    )
  }

  return (
    <div className="bg-zinc-900/50 border border-zinc-800 p-8 rounded-xl max-w-2xl mx-auto mt-8">
      <h2 className="text-2xl font-light text-white mb-2">Initialize New Project</h2>
      <p className="text-zinc-500 mb-6 text-sm">
        Set up the master configuration for a new build. Once created, you can upload contracts, invite trades, and unlock the financial ledger.
      </p>

      <form action={formAction} className="space-y-6">
        <div className="space-y-4">
          <div>
            <label htmlFor="title" className="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Project Name *</label>
            <input 
              type="text" 
              name="title" 
              id="title" 
              required
              className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              placeholder="e.g. 1234 Skyview Drive"
            />
          </div>

          <div>
            <label htmlFor="address" className="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Site Address</label>
            <input 
              type="text" 
              name="address" 
              id="address" 
              className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              placeholder="e.g. Vancouver, BC"
            />
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label htmlFor="total_budget" className="block text-xs uppercase tracking-wider text-zinc-400 mb-1">Total Capital ($)</label>
              <input 
                type="number" 
                name="total_budget" 
                id="total_budget" 
                step="0.01"
                className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500 transition-colors"
                placeholder="1500000"
              />
            </div>
            <div>
              <label htmlFor="pm_fee_percentage" className="block text-xs uppercase tracking-wider text-zinc-400 mb-1">PM Fee (%)</label>
              <input 
                type="number" 
                name="pm_fee_percentage" 
                id="pm_fee_percentage" 
                step="0.01"
                defaultValue="12.00"
                className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-emerald-500 transition-colors"
              />
            </div>
          </div>
        </div>

        {state?.error && (
          <div className="text-red-400 text-sm bg-red-900/20 p-3 rounded border border-red-900/50">
            {state.error}
          </div>
        )}

        <button 
          type="submit" 
          disabled={isPending}
          className="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-3 rounded-lg transition-colors disabled:opacity-50"
        >
          {isPending ? 'Creating...' : 'Initialize Project'}
        </button>
      </form>
    </div>
  )
}
