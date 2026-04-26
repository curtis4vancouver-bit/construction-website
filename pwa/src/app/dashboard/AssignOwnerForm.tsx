'use client'

import { useState } from 'react'
import { assignOwner } from './assignOwnerAction'

export default function AssignOwnerForm({ projectId, currentOwnerId }: { projectId: string, currentOwnerId: string | null }) {
  const [isOpen, setIsOpen] = useState(false)
  const [message, setMessage] = useState('')

  if (currentOwnerId) {
    return (
      <div className="text-xs bg-zinc-900 text-emerald-400 px-3 py-1.5 rounded border border-zinc-800">
        ✓ Owner Assigned
      </div>
    )
  }

  if (!isOpen) {
    return (
      <button 
        onClick={() => setIsOpen(true)}
        className="text-xs bg-amber-600/20 text-amber-500 hover:bg-amber-600/30 px-3 py-1.5 rounded transition-colors border border-amber-500/20"
      >
        Assign Property Owner
      </button>
    )
  }

  async function action(formData: FormData) {
    setMessage('Assigning...')
    const result = await assignOwner(projectId, formData)
    if (result.error) {
      setMessage(result.error)
    } else {
      setMessage('Owner assigned successfully!')
      setIsOpen(false)
    }
  }

  return (
    <div className="bg-black p-4 rounded-lg border border-zinc-800 text-left w-64">
      <div className="flex justify-between items-center mb-2">
        <span className="text-xs uppercase text-zinc-500">Link Owner</span>
        <button onClick={() => setIsOpen(false)} className="text-zinc-500 hover:text-white">&times;</button>
      </div>
      <form action={action} className="space-y-3">
        <input 
          type="email" 
          name="email" 
          required 
          placeholder="owner@email.com" 
          className="w-full bg-zinc-900 border border-zinc-700 rounded px-2 py-1 text-sm text-white"
        />
        <button type="submit" className="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-1 rounded text-sm transition-colors">
          Save
        </button>
      </form>
      {message && <p className="text-xs text-amber-400 mt-2">{message}</p>}
    </div>
  )
}
