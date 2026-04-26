'use server'

import { createClient } from '@/utils/supabase/server'
import { revalidatePath } from 'next/cache'

export async function uploadDocument(formData: FormData) {
  const supabase = await createClient()
  const { data: { user }, error: authError } = await supabase.auth.getUser()

  if (authError || !user) {
    return { error: 'Not authenticated' }
  }

  const file = formData.get('file') as File
  const title = formData.get('title') as string
  const documentType = formData.get('document_type') as string
  const projectId = formData.get('project_id') as string

  if (!file || !title || !documentType || !projectId) {
    return { error: 'All fields are required' }
  }

  if (file.size > 10 * 1024 * 1024) {
    return { error: 'File size must be under 10MB' }
  }

  // Generate a unique filename
  const ext = file.name.split('.').pop()
  const fileName = `${projectId}/${Date.now()}_${title.replace(/\s+/g, '_')}.${ext}`

  // Upload to Supabase Storage bucket "documents"
  const { error: uploadError } = await supabase.storage
    .from('documents')
    .upload(fileName, file, {
      cacheControl: '3600',
      upsert: false,
    })

  if (uploadError) {
    console.error('Storage upload error:', uploadError)
    return { error: 'Failed to upload file: ' + uploadError.message }
  }

  // Get public URL
  const { data: urlData } = supabase.storage.from('documents').getPublicUrl(fileName)

  // Insert document record
  const { error: insertError } = await supabase.from('documents').insert([{
    project_id: projectId,
    uploaded_by: user.id,
    document_type: documentType,
    title,
    file_url: urlData.publicUrl,
    status: documentType === 'pm_contract' ? 'pending_approval' : 'active',
  }])

  if (insertError) {
    console.error('Document insert error:', insertError)
    return { error: 'File uploaded but failed to create record: ' + insertError.message }
  }

  revalidatePath('/dashboard/documents')
  return { success: true }
}

export async function signContract(documentId: string) {
  const supabase = await createClient()
  const { data: { user }, error: authError } = await supabase.auth.getUser()

  if (authError || !user) {
    return { error: 'Not authenticated' }
  }

  // 1. Get the document to find the project_id
  const { data: doc, error: docError } = await supabase
    .from('documents')
    .select('project_id')
    .eq('id', documentId)
    .single()

  if (docError || !doc) {
    return { error: 'Document not found' }
  }

  // 2. Verify the user is the owner of the project
  const { data: project, error: projError } = await supabase
    .from('projects')
    .select('owner_id')
    .eq('id', doc.project_id)
    .single()

  if (projError || project?.owner_id !== user.id) {
    return { error: 'Unauthorized. Only the Project Owner can sign the contract.' }
  }

  // 3. Update the document status to active (signed)
  const { error: updateDocError } = await supabase
    .from('documents')
    .update({ status: 'active' })
    .eq('id', documentId)

  if (updateDocError) {
    return { error: 'Failed to update document status' }
  }

  // 4. Update the project status from planning to active_construction
  const { error: updateProjError } = await supabase
    .from('projects')
    .update({ status: 'active_construction' })
    .eq('id', doc.project_id)

  if (updateProjError) {
    console.error('Failed to update project status:', updateProjError)
  }

  // 5. Add an immutable log to communications for the signature audit trail
  await supabase
    .from('communications')
    .insert([
      {
        project_id: doc.project_id,
        sender_id: user.id,
        message: `AUDIT LOG: Project Owner digitally signed the PM Contract (Doc ID: ${documentId}) on ${new Date().toISOString()}`
      }
    ])

  revalidatePath('/dashboard/documents')
  revalidatePath('/dashboard')
  return { success: true }
}
