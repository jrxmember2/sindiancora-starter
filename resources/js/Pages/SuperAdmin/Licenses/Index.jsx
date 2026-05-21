import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import Badge from '@/Components/Badge';
import EmptyState from '@/Components/EmptyState';
import { Card, CardHeader } from '@/Components/Card';
import { Plus } from 'lucide-react';

export default function Index({ licenses }) {
  return (
    <AppLayout title="Licenças">
      <Head title="Licenças" />
      <Card>
        <CardHeader title="Licenciamento contratual" description="Cada cliente tem uma licença personalizada, sem planos engessados." action={<Button href="/superadmin/licenses/create"><Plus className="h-4 w-4" /> Nova licença</Button>} />
        {licenses.data.length === 0 ? <EmptyState /> : (
          <div className="overflow-hidden rounded-3xl border border-slate-200">
            <table className="w-full text-left text-sm">
              <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th className="px-4 py-3">Contrato</th><th className="px-4 py-3">Empresa</th><th className="px-4 py-3">Limites</th><th className="px-4 py-3">Status</th><th className="px-4 py-3"></th></tr></thead>
              <tbody className="divide-y divide-slate-200 bg-white">
                {licenses.data.map((license) => (
                  <tr key={license.id}>
                    <td className="px-4 py-3 font-bold">{license.contract_number}</td>
                    <td className="px-4 py-3">{license.company?.name}</td>
                    <td className="px-4 py-3 text-slate-600">{license.max_condominiums} condomínios • {license.max_internal_users} usuários</td>
                    <td className="px-4 py-3"><Badge tone={license.status === 'active' ? 'green' : 'yellow'}>{license.status}</Badge></td>
                    <td className="px-4 py-3 text-right"><Link href={`/superadmin/licenses/${license.id}/edit`} className="font-semibold text-blue-600">Editar</Link></td>
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
