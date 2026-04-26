import { createClient } from '@/utils/supabase/server';
import { SignContractButton } from './SignContractButton';
import { UploadDocumentForm } from './UploadDocumentForm';

export default async function DocumentVaultPage() {
  const supabase = await createClient();
  const { data: { user } } = await supabase.auth.getUser();

  // Get user role
  const { data: userData } = await supabase.from('users').select('role').eq('id', user?.id).single();
  const isOwner = userData?.role === 'owner';
  const isPM = userData?.role === 'pm';

  // Get the active project
  const { data: projects } = await supabase
    .from('projects')
    .select('id')
    .order('created_at', { ascending: false });
  const project = projects && projects.length > 0 ? projects[0] : null;

  // Fetch project docs
  const { data: documents } = await supabase
    .from('documents')
    .select('*')
    .order('created_at', { ascending: false });

  return (
    <div className="p-4 sm:p-8 max-w-6xl mx-auto space-y-8">
      <header className="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 border-b border-zinc-800 pb-6">
        <div>
          <p className="text-[10px] uppercase tracking-[0.25em] text-zinc-500 font-medium mb-2">Compliance & Storage</p>
          <h2 className="text-3xl font-light text-white mb-2">Document Vault</h2>
          <p className="text-zinc-400 text-sm">Manage WCB, Liability Insurance, and Subcontractor Agreements.</p>
        </div>
        {(isPM || isOwner) && project && <UploadDocumentForm projectId={project.id} />}
      </header>

      {documents && documents.length > 0 ? (
        <div className="bg-zinc-900/50 border border-zinc-800 rounded-xl overflow-hidden">
          <table className="w-full text-left text-sm text-zinc-400">
            <thead className="bg-zinc-950/50 text-xs uppercase text-zinc-500 border-b border-zinc-800">
              <tr>
                <th className="px-6 py-4 font-medium">Document Title</th>
                <th className="px-6 py-4 font-medium">Type</th>
                <th className="px-6 py-4 font-medium">Status</th>
                <th className="px-6 py-4 font-medium text-right">Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-800/50">
              {documents.map((doc: any) => (
                <tr key={doc.id} className="hover:bg-zinc-900/50 transition-colors">
                  <td className="px-6 py-4 text-zinc-200">{doc.title}</td>
                  <td className="px-6 py-4 capitalize">{doc.document_type.replace('_', ' ')}</td>
                  <td className="px-6 py-4">
                    <span className={`px-2 py-1 rounded text-xs border ${
                      doc.status === 'active' 
                        ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' 
                        : 'bg-amber-500/10 text-amber-500 border-amber-500/20'
                    }`}>
                      {doc.status.replace('_', ' ')}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-right flex items-center justify-end gap-4">
                    <a href={doc.file_url} target="_blank" rel="noreferrer" className="text-zinc-400 hover:text-white transition-colors">View</a>
                    {doc.document_type === 'pm_contract' && doc.status === 'pending_approval' && isOwner && (
                      <SignContractButton documentId={doc.id} />
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      ) : (
        <div className="bg-zinc-900/30 border border-zinc-800/50 rounded-xl p-12 text-center">
          <div className="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mx-auto mb-4">
            <svg className="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </div>
          <h3 className="text-xl font-medium text-zinc-300 mb-2">Vault is Empty</h3>
          <p className="text-zinc-500 mb-6">No documents have been uploaded to this workspace yet.</p>
        </div>
      )}
    </div>
  );
}
