import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import Badge from '@/Components/Badge';
import EmptyState from '@/Components/EmptyState';
import { Card, CardHeader } from '@/Components/Card';
import { Plus } from 'lucide-react';

export default function Index({ companies }) {
  return (
    <AppLayout title="Empresas">
      <Head title="Empresas" />
      <Card>
        <CardHeader title="Empresas clientes" description="Controle os tenants/clientes que usam a plataforma." action={<Button href="/superadmin/companies/create"><Plus className="h-4 w-4" /> Nova empresa</Button>} />
        {companies.data.length === 0 ? <EmptyState /> : (
          <div className="overflow-hidden rounded-3xl border border-slate-200">
            <table className="w-full text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr><th className="px-4 py-3">Nome</th><th className="px-4 py-3">Documento</th><th className="px-4 py-3">E-mail</th><th className="px-4 py-3">Status</th><th className="px-4 py-3"></th></tr>
              </thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {companies.data.map((company) => (
                  <tr key={company.id}>
                    <td className="px-4 py-3 font-bold text-slate-900">{company.name}<p className="text-xs font-medium text-slate-500">/{company.slug}</p></td>
                    <td className="px-4 py-3 text-slate-600">{company.document || '—'}</td>
                    <td className="px-4 py-3 text-slate-600">{company.email || '—'}</td>
                    <td className="px-4 py-3"><Badge tone={company.status === 'active' ? 'green' : 'gray'}>{company.status}</Badge></td>
                    <td className="px-4 py-3 text-right"><Link href={`/superadmin/companies/${company.id}/edit`} className="font-semibold text-blue-600">Editar</Link></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Card>
    </AppLayout>
  );
}
