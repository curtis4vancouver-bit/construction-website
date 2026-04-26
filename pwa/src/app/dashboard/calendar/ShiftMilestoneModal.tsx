'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import { shiftMilestoneDates } from './actions'

export function ShiftMilestoneModal({ milestone }: { milestone: any }) {
  const [isOpen, setIsOpen] = useState(false)
  const [isPending, setIsPending] = useState(false)
  const [newDate, setNewDate] = useState(milestone.start_date)

  const handleShift = async () => {
    setIsPending(true)
    const result = await shiftMilestoneDates(milestone.id, newDate)
    if (result?.error) {
      toast.error(result.error)
    } else {
      toast.success(`Schedule shifted. ${result.shifted} milestone(s) updated.`)
    }
    setIsPending(false)
    setIsOpen(false)
  }

  if (!isOpen) {
    return (
      <button 
        onClick={() => setIsOpen(true)}
        className="text-amber-500 hover:text-amber-400 text-xs px-2 py-1 rounded border border-amber-500/20 bg-amber-500/10 transition-colors"
      >
        Shift Date
      </button>
    )
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" onClick={() => setIsOpen(false)}>
      <div className="bg-zinc-950 border border-zinc-800 p-6 rounded-xl w-full max-w-sm" onClick={e => e.stopPropagation()}>
        <h3 className="text-xl font-light text-white mb-2">Shift Schedule</h3>
        <p className="text-zinc-400 text-sm mb-4">
          Changing the start date for <strong className="text-white">{milestone.title}</strong> will automatically shift all subsequent dependent milestones by the same number of days.
        </p>

        <div className="space-y-4">
          <div>
            <label className="block text-xs uppercase text-zinc-500 mb-1">New Start Date</label>
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
              onClick={handleShift}
              disabled={isPending || newDate === milestone.start_date}
              className="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-sm transition-colors disabled:opacity-50"
            >
              {isPending ? 'Shifting...' : 'Apply Shift'}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
