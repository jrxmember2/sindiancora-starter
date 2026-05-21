import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import Badge from '@/Components/Badge';
import EmptyState from '@/Components/EmptyState';
import { Card, CardHeader } from '@/Components/Card';
import { Plus } from 'lucide-react';

export default function Index({ issues }) {
  return (
    <AppLayout title="Chamados">
      <Head title="Chamados" />
      <Card>
        <CardHeader title="Chamados" description="O coração operacional do sistema." action={<Button href="/app/issues/create"><Plus className="h-4 w-4" /> Novo chamado</Button>} />
        {issues.data.length === 0 ? <EmptyState /> : (
          <div className="overflow-hidden rounded-3xl border border-slate-200">
            <table className="w-full text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th className="px-4 py-3">Assunto</th><th className="px-4 py-3">Condomínio</th><th className="px-4 py-3">Status</th><th className="px-4 py-3">Prioridade</th><th className="px-4 py-3"></th></tr></thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {issues.data.map((issue) => <tr key={issue.id}><td className="px-4 py-3 font-bold">#{issue.id} — {issue.subject}</td><td className="px-4 py-3 text-slate-600">{issue.condominium?.name || '—'}</td><td className="px-4 py-3"><Badge tone="blue">{issue.status}</Badge></td><td className="px-4 py-3 text-slate-600">{issue.priority}</td><td className="px-4 py-3 text-right"><Link href={`/app/issues/${issue.id}/edit`} className="font-semibold text-blue-600">Editar</Link></td></tr>)}
              </tbody>
            </table>
          </div>
        )}
      </Card>
    </AppLayout>
  );
}
