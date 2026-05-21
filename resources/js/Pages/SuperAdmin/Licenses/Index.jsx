import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import ConfirmDialog from '@/Components/ConfirmDialog';
import DataTable from '@/Components/DataTable';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ licenses }) {
  const [selectedLicense, setSelectedLicense] = useState(null);

  const closeDialog = () => setSelectedLicense(null);
  const confirmCancel = () => {
    if (!selectedLicense) {
      return;
    }

    router.delete(`/superadmin/licenses/${selectedLicense.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Licenças">
      <Head title="Licenças" />

      <Card>
        <CardHeader
          title="Licenciamento contratual"
          description="Cada cliente opera com limites, módulos e regras comerciais personalizadas."
          action={
            <Button href="/superadmin/licenses/create">
              <Plus className="h-4 w-4" /> Nova licença
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'contract', label: 'Contrato' },
            { key: 'company', label: 'Empresa' },
            { key: 'limits', label: 'Limites' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-44' },
          ]}
          rows={licenses.data}
          meta={licenses}
          emptyTitle="Nenhuma licença cadastrada"
          emptyDescription="Crie a primeira licença contratual para começar a comercialização."
          renderRow={(license) => (
            <tr key={license.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{license.contract_number}</p>
                <p className="text-xs text-slate-500">{licenseBillingTypeLabel(license.billing_type)} | financeiro {licenseFinancialStatusLabel(license.financial_status)}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">{license.company?.name || 'Sem empresa'}</td>
              <td className="px-4 py-4 text-slate-600">
                <p>{license.max_condominiums} condomínios</p>
                <p className="text-xs text-slate-400">{license.max_internal_users} usuários internos</p>
              </td>
              <td className="px-4 py-4">
                <Badge tone={license.status === 'active' ? 'green' : license.status === 'trial' ? 'blue' : 'yellow'}>
                  {licenseStatusLabel(license.status)}
                </Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/superadmin/licenses/${license.id}/edit`} variant="soft" size="sm">
                    Editar
                  </Button>
                  {license.status !== 'canceled' && (
                    <Button type="button" variant="danger" size="sm" onClick={() => setSelectedLicense(license)}>
                      Cancelar
                    </Button>
                  )}
                </div>
              </td>
            </tr>
          )}
        />
      </Card>

      <ConfirmDialog
        open={Boolean(selectedLicense)}
        onClose={closeDialog}
        onConfirm={confirmCancel}
        title="Cancelar licença"
        description={`A licença ${selectedLicense?.contract_number || ''} será marcada como cancelada.`}
        confirmLabel="Cancelar licença"
      />
    </AppLayout>
  );
}

function licenseStatusLabel(status) {
  const labels = {
    active: 'Ativa',
    trial: 'Teste',
    pending: 'Pendente',
    expired: 'Expirada',
    suspended: 'Suspensa',
    blocked: 'Bloqueada',
    read_only: 'Somente leitura',
    canceled: 'Cancelada',
  };

  return labels[status] || status;
}

function licenseBillingTypeLabel(type) {
  const labels = {
    monthly: 'Mensal',
    quarterly: 'Trimestral',
    yearly: 'Anual',
    custom: 'Personalizada',
  };

  return labels[type] || type;
}

function licenseFinancialStatusLabel(status) {
  const labels = {
    current: 'em dia',
    due: 'a vencer',
    overdue: 'em atraso',
    negotiated: 'negociado',
    suspended: 'suspenso',
    canceled: 'cancelado',
  };

  return labels[status] || status;
}
