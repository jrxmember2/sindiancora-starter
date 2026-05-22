import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import ConfirmDialog from '@/Components/ConfirmDialog';
import DataTable from '@/Components/DataTable';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ companies }) {
  const [selectedCompany, setSelectedCompany] = useState(null);

  const closeDialog = () => setSelectedCompany(null);
  const confirmInactivate = () => {
    if (!selectedCompany) {
      return;
    }

    router.delete(`/superadmin/companies/${selectedCompany.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Empresas">
      <Head title="Empresas" />

      <Card>
        <CardHeader
          title="Empresas clientes"
          description="Controle os tenants, faça o onboarding comercial e mantenha o admin master inicial separado dos usuários da plataforma."
          action={(
            <Button href="/superadmin/companies/create">
              <Plus className="h-4 w-4" /> Nova empresa
            </Button>
          )}
        />

        <DataTable
          columns={[
            { key: 'name', label: 'Empresa' },
            { key: 'contact', label: 'Contato da empresa' },
            { key: 'primary_admin', label: 'Admin master' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-48' },
          ]}
          rows={companies.data}
          meta={companies}
          emptyTitle="Nenhuma empresa cadastrada"
          emptyDescription="Cadastre o primeiro cliente para iniciar o uso comercial da plataforma."
          renderRow={(company) => (
            <tr key={company.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{company.name}</p>
                <p className="text-xs font-medium text-slate-500">/{company.slug}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{company.email || 'Sem e-mail'}</p>
                <p className="text-xs text-slate-400">{company.phone || 'Sem telefone'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{company.primary_company_user?.user?.name || 'Não configurado'}</p>
                <p className="text-xs text-slate-400">{company.primary_company_user?.user?.email || 'Sem e-mail de acesso'}</p>
              </td>
              <td className="px-4 py-4">
                <Badge tone={company.status === 'active' ? 'green' : company.status === 'suspended' ? 'yellow' : 'gray'}>
                  {companyStatusLabel(company.status)}
                </Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/superadmin/companies/${company.id}/edit`} variant="soft" size="sm">
                    Editar
                  </Button>
                  <Button href={`/superadmin/licenses/create?company_id=${company.id}`} variant="soft" size="sm">
                    Licença
                  </Button>
                  {company.status !== 'inactive' && (
                    <Button type="button" variant="danger" size="sm" onClick={() => setSelectedCompany(company)}>
                      Inativar
                    </Button>
                  )}
                </div>
              </td>
            </tr>
          )}
        />
      </Card>

      <ConfirmDialog
        open={Boolean(selectedCompany)}
        onClose={closeDialog}
        onConfirm={confirmInactivate}
        title="Inativar empresa"
        description={`A empresa ${selectedCompany?.name || ''} deixará de operar como tenant ativo.`}
        confirmLabel="Inativar empresa"
      />
    </AppLayout>
  );
}

function companyStatusLabel(status) {
  const labels = {
    active: 'Ativa',
    inactive: 'Inativa',
    suspended: 'Suspensa',
  };

  return labels[status] || status;
}
