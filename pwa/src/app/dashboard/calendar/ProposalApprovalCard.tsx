'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import { approveProposal, denyProposal } from './actions'

export function ProposalApprovalCard({ proposal }: { proposal: any }) {
  const [isPending, setIsPending] = useState(false)
  const [resolved, setResolved] = useState(proposal.status !== 'pending')

  const handleApprove = async () => {
    setIsPending(true)
    const result = await approveProposal(proposal.id)
    if ('error' in result) {
      toast.error(result.error)
    } else {
      toast.success(`Proposal approved. ${result.shifted} milestone(s) shifted.`)
      setResolved(true)
    }
    setIsPending(false)
  }

  const handleDeny = async () => {
    setIsPending(true)
    const result = await denyProposal(proposal.id)
    if ('error' in result) {
      toast.error(result.error)
    } else {
      toast.info('Proposal denied.')
      setResolved(true)
    }
    setIsPending(false)
  }

  if (resolved) {
    return (
      <div className="flex items-center gap-2 text-xs text-zinc-500">
        <span className={`px-2 py-1 rounded border ${
          proposal.status === 'approved' 
            ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' 
            : 'bg-red-500/10 text-red-400 border-red-500/20'
        }`}>
          {proposal.status}
        </span>
      </div>
    )
  }

  return (
    <div className="bg-cyan-950/20 border border-cyan-900/30 p-4 rounded-lg">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <p className="text-sm text-white font-medium">{proposal.milestone_title || 'Milestone'}</p>
          <p className="text-xs text-zinc-400 mt-1">
            Proposed: <span className="text-cyan-400">{new Date(proposal.proposed_start_date).toLocaleDateString()}</span>
            <span className="text-zinc-600 mx-2">&bull;</span>
            By: <span className="text-zinc-300">{proposal.trade_email || 'Trade Partner'}</span>
          </p>
        </div>
        <div className="flex gap-2 shrink-0">
          <button 
            onClick={handleApprove} 
            disabled={isPending}
            className="px-3 py-1.5 text-xs bg-emerald-600 hover:bg-emerald-500 text-white rounded transition-colors disabled:opacity-50"
          >
            {isPending ? '...' : 'Approve & Shift'}
          </button>
          <button 
            onClick={handleDeny} 
            disabled={isPending}
            className="px-3 py-1.5 text-xs bg-zinc-800 hover:bg-zinc-700 text-white rounded transition-colors disabled:opacity-50"
          >
            Deny
          </button>
        </div>
      </div>
    </div>
  )
}
