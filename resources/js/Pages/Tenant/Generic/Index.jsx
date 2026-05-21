import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import EmptyState from '@/Components/EmptyState';
import { Card, CardHeader } from '@/Components/Card';
import { Plus } from 'lucide-react';

export default function Index({ items }) {
  const path = window.location.pathname.split('/')[2];
  const title = {
    condominiums: 'Condomínios',
    suppliers: 'Fornecedores',
    documents: 'Documentos',
  }[path] || 'Registros';

  return (
    <AppLayout title={title}>
      <Head title={title} />
      <Card>
        <CardHeader title={title} description="Cadastro operacional isolado por empresa." action={<Button href={`/app/${path}/create`}><Plus className="h-4 w-4" /> Novo</Button>} />
        {items.data.length === 0 ? <EmptyState /> : (
          <div className="overflow-hidden rounded-3xl border border-slate-200">
            <table className="w-full text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th className="px-4 py-3">Nome/Título</th><th className="px-4 py-3">Status</th><th className="px-4 py-3"></th></tr></thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {items.data.map((item) => <tr key={item.id}><td className="px-4 py-3 font-bold">{item.name || item.title || `#${item.id}`}</td><td className="px-4 py-3 text-slate-600">{item.status || (item.active ? 'ativo' : '—')}</td><td className="px-4 py-3 text-right"><Link href={`/app/${path}/${item.id}/edit`} className="font-semibold text-blue-600">Editar</Link></td></tr>)}
              </tbody>
            </table>
          </div>
        )}
      </Card>
    </AppLayout>
  );
}
