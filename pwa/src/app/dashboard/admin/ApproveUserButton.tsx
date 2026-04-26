'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import { approveUser } from './actions'

export function ApproveUserButton({ userId, userName }: { userId: string, userName: string }) {
  const [isPending, setIsPending] = useState(false)
  const [approved, setApproved] = useState(false)

  const handleApprove = async () => {
    setIsPending(true)
    const result = await approveUser(userId)
    if ('error' in result) {
      toast.error(result.error)
    } else {
      toast.success(`${userName} has been approved.`)
      setApproved(true)
    }
    setIsPending(false)
  }

  if (approved) {
    return (
      <span className="text-xs px-3 py-1.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded">
        ✓ Approved
      </span>
    )
  }

  return (
    <button
      onClick={handleApprove}
      disabled={isPending}
      className="px-4 py-2 text-sm bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition-colors disabled:opacity-50"
    >
      {isPending ? 'Approving...' : 'Approve Access'}
    </button>
  )
}
