import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import ConfirmDialog from '@/Components/ConfirmDialog';
import DataTable from '@/Components/DataTable';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ items }) {
  const [selectedItem, setSelectedItem] = useState(null);

  const closeDialog = () => setSelectedItem(null);
  const confirmInactivate = () => {
    if (!selectedItem) {
      return;
    }

    router.delete(`/app/suppliers/${selectedItem.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Fornecedores">
      <Head title="Fornecedores" />

      <Card>
        <CardHeader
          title="Fornecedores"
          description="Cadastro central de parceiros e prestadores de serviço."
          action={
            <Button href="/app/suppliers/create">
              <Plus className="h-4 w-4" /> Novo fornecedor
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'name', label: 'Fornecedor' },
            { key: 'contact', label: 'Contato' },
            { key: 'region', label: 'Atuação' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum fornecedor cadastrado"
          emptyDescription="Cadastre fornecedores para usá-los em chamados e operações futuras."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{item.name}</p>
                <p className="text-xs text-slate-500">{item.document || 'Documento não informado'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{item.responsible_name || 'Sem responsável'}</p>
                <p className="text-xs text-slate-400">{item.mobile || item.phone || 'Sem telefone'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                {item.city ? `${item.city}${item.state ? `/${item.state}` : ''}` : 'Não informado'}
              </td>
              <td className="px-4 py-4">
                <Badge tone={item.active ? 'green' : 'gray'}>{item.active ? 'ativo' : 'inativo'}</Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/app/suppliers/${item.id}/edit`} variant="soft" size="sm">Editar</Button>
                  {item.active && (
                    <Button type="button" variant="danger" size="sm" onClick={() => setSelectedItem(item)}>
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
        open={Boolean(selectedItem)}
        onClose={closeDialog}
        onConfirm={confirmInactivate}
        title="Inativar fornecedor"
        description={`O fornecedor ${selectedItem?.name || ''} deixará de ficar disponível na operação ativa.`}
        confirmLabel="Inativar fornecedor"
      />
    </AppLayout>
  );
}
