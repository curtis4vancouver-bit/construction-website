'use client'

import { useState } from 'react'
import { toast } from 'sonner'

export function AssignTradeForm({ projectId }: { projectId: string }) {
  const [category, setCategory] = useState('')
  const [inviteLink, setInviteLink] = useState('')

  function handleGenerateLink(e: React.FormEvent) {
    e.preventDefault()
    if (!category.trim()) return

    // Generate link (we encode the category to handle spaces like "General Contractor")
    const baseUrl = window.location.origin
    const link = `${baseUrl}/invite/trade?project_id=${projectId}&category=${encodeURIComponent(category.trim())}`
    setInviteLink(link)
  }

  const copyToClipboard = () => {
    navigator.clipboard.writeText(inviteLink)
    toast.success('Invite link copied to clipboard!')
  }

  if (inviteLink) {
    return (
      <div className="mt-4 bg-emerald-950/30 border border-emerald-900/50 p-4 rounded-xl text-center">
        <h3 className="text-emerald-400 font-medium mb-2">Trade Invite Generated!</h3>
        <p className="text-sm text-zinc-400 mb-4">Send this link to your {category} subcontractor.</p>
        <div className="flex bg-black border border-zinc-800 rounded p-2 items-center gap-2 mb-4">
          <input 
            readOnly 
            value={inviteLink} 
            className="bg-transparent flex-1 text-xs text-zinc-300 outline-none"
          />
        </div>
        <div className="flex gap-2">
          <button 
            onClick={copyToClipboard}
            className="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white text-sm py-2 rounded transition-colors"
          >
            Copy Link
          </button>
          <button 
            onClick={() => { setInviteLink(''); setCategory(''); }}
            className="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white text-sm py-2 rounded transition-colors"
          >
            Generate Another
          </button>
        </div>
      </div>
    )
  }

  return (
    <form onSubmit={handleGenerateLink} className="mt-4 space-y-4">
      <div>
         <label className="block text-[10px] uppercase tracking-wider text-zinc-500 mb-1">Trade Category</label>
         <input 
           type="text" 
           required 
           value={category}
           onChange={(e) => setCategory(e.target.value)}
           className="w-full bg-zinc-950 border border-zinc-800 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 transition-colors"
           placeholder="e.g. Electrical, Plumbing"
         />
      </div>

      <button 
        type="submit" 
        className="w-full py-2 bg-emerald-600 hover:bg-emerald-500 transition-colors text-sm rounded text-white"
      >
        Generate Invite Link
      </button>
    </form>
  )
}
