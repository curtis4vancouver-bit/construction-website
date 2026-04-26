'use client'

import { useState } from 'react'
import { toast } from 'sonner'
import { signContract } from './actions'

export function SignContractButton({ documentId }: { documentId: string }) {
  const [isPending, setIsPending] = useState(false)
  const [signed, setSigned] = useState(false)

  const handleSign = async () => {
    setIsPending(true)
    const result = await signContract(documentId)
    if (result?.error) {
      toast.error(result.error)
    } else {
      setSigned(true)
      toast.success('Contract signed successfully. Project status updated to Active Construction.')
    }
    setIsPending(false)
  }

  if (signed) {
    return (
      <span className="inline-flex items-center gap-1.5 text-emerald-500 font-medium text-xs">
        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg>
        Signed
      </span>
    )
  }

  return (
    <button 
      onClick={handleSign}
      disabled={isPending}
      className="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded text-xs transition-colors disabled:opacity-50"
    >
      {isPending ? 'Signing...' : 'Digitally Sign'}
    </button>
  )
}
