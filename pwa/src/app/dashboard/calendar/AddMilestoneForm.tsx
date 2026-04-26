'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import { createMilestone } from './actions'

export default function AddMilestoneForm({ projectId }: { projectId: string }) {
  const [isOpen, setIsOpen] = useState(false)
  const [isPending, setIsPending] = useState(false)

  if (!isOpen) {
    return (
      <button 
        onClick={() => setIsOpen(true)}
        className="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2"
      >
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>
        Add Milestone
      </button>
    )
  }

  async function action(formData: FormData) {
    setIsPending(true)
    const result = await createMilestone(
      projectId, 
      formData.get('title') as string, 
      formData.get('startDate') as string, 
      parseInt(formData.get('durationDays') as string, 10),
      parseInt(formData.get('sequenceOrder') as string, 10)
    )
    if (result?.error) {
      toast.error(result.error)
    } else {
      toast.success('Milestone added to calendar.')
    }
    setIsPending(false)
    setIsOpen(false)
  }

  return (
    <div className="bg-zinc-900 border border-zinc-800 p-6 rounded-xl mt-4 w-full">
      <div className="flex justify-between items-center mb-4 border-b border-zinc-800 pb-2">
        <h3 className="text-lg text-emerald-400 font-medium">Add Manual Schedule Item</h3>
        <button onClick={() => setIsOpen(false)} className="text-zinc-500 hover:text-white">&times;</button>
      </div>
      <form action={action} className="space-y-4">
        <div>
          <label className="block text-xs uppercase text-zinc-500 mb-1">Title</label>
          <input type="text" name="title" required className="w-full bg-black border border-zinc-800 rounded px-3 py-2 text-white" placeholder="e.g. Framing Start" />
        </div>
        <div className="grid grid-cols-3 gap-4">
          <div>
            <label className="block text-xs uppercase text-zinc-500 mb-1">Start Date</label>
            <input type="date" name="startDate" required className="w-full bg-black border border-zinc-800 rounded px-3 py-2 text-white [color-scheme:dark]" />
          </div>
          <div>
            <label className="block text-xs uppercase text-zinc-500 mb-1">Duration (Days)</label>
            <input type="number" name="durationDays" required min="1" defaultValue="1" className="w-full bg-black border border-zinc-800 rounded px-3 py-2 text-white" />
          </div>
          <div>
            <label className="block text-xs uppercase text-zinc-500 mb-1">Sequence Order</label>
            <input type="number" name="sequenceOrder" required min="0" defaultValue="1" className="w-full bg-black border border-zinc-800 rounded px-3 py-2 text-white" />
          </div>
        </div>
        <button type="submit" disabled={isPending} className="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2 rounded font-medium disabled:opacity-50">
          {isPending ? 'Adding...' : 'Save to Master Calendar'}
        </button>
      </form>
    </div>
  )
}
