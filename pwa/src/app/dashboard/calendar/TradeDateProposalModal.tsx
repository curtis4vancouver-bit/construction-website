'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import { proposeDate } from './actions'

export function TradeDateProposalModal({ milestone }: { milestone: any }) {
  const [isOpen, setIsOpen] = useState(false)
  const [isPending, setIsPending] = useState(false)
  const [newDate, setNewDate] = useState(milestone.start_date)

  const handlePropose = async () => {
    setIsPending(true)
    const result = await proposeDate(milestone.id, newDate)
    if (result?.error) {
      toast.error(result.error)
    } else {
      toast.success('Date proposal sent to Project Manager for review.')
    }
    setIsPending(false)
    setIsOpen(false)
  }

  if (!isOpen) {
    return (
      <button 
        onClick={() => setIsOpen(true)}
        className="text-cyan-500 hover:text-cyan-400 text-xs px-2 py-1 rounded border border-cyan-500/20 bg-cyan-500/10 transition-colors"
      >
        Propose Date Change
      </button>
    )
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" onClick={() => setIsOpen(false)}>
      <div className="bg-zinc-950 border border-zinc-800 p-6 rounded-xl w-full max-w-sm text-left" onClick={e => e.stopPropagation()}>
        <h3 className="text-xl font-light text-white mb-2">Propose New Date</h3>
        <p className="text-zinc-400 text-sm mb-4">
          If you have a scheduling conflict, propose an alternate start date for <strong className="text-white">{milestone.title}</strong>. The PM will review your request.
        </p>

        <div className="space-y-4">
          <div>
            <label className="block text-xs uppercase text-zinc-500 mb-1">Proposed Start Date</label>
            <input 
              type="date" 
              value={newDate} 
              onChange={(e) => setNewDate(e.target.value)} 
              className="w-full bg-black border border-zinc-800 rounded px-3 py-2 text-white [color-scheme:dark]" 
            />
          </div>

          <div className="flex gap-3 mt-6">
            <button 
              onClick={() => setIsOpen(false)}
              className="flex-1 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded text-sm transition-colors"
            >
              Cancel
            </button>
            <button 
              onClick={handlePropose}
              disabled={isPending || newDate === milestone.start_date}
              className="flex-1 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded text-sm transition-colors disabled:opacity-50"
            >
              {isPending ? 'Sending...' : 'Send Proposal'}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
