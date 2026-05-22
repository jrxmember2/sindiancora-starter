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

    router.delete(`/superadmin/platform-users/${selectedItem.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Usuários da plataforma">
      <Head title="Usuários da plataforma" />

      <Card>
        <CardHeader
          title="Equipe da plataforma"
          description="Gerencie somente os usuários internos da Serratech com acesso de superadmin."
          action={(
            <Button href="/superadmin/platform-users/create">
              <Plus className="h-4 w-4" /> Novo usuário
            </Button>
          )}
        />

        <DataTable
          columns={[
            { key: 'name', label: 'Usuário' },
            { key: 'contact', label: 'Contato' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum usuário da plataforma cadastrado"
          emptyDescription="Cadastre membros do seu time que precisam atuar como superadmin."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{item.name}</p>
                <p className="text-xs text-slate-500">
                  {item.must_change_password ? 'Troca de senha pendente' : 'Acesso liberado'}
                </p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{item.email}</p>
                <p className="text-xs text-slate-400">{item.phone || 'Sem telefone'}</p>
              </td>
              <td className="px-4 py-4">
                <Badge tone={item.status === 'active' ? 'green' : 'gray'}>
                  {item.status === 'active' ? 'Ativo' : 'Inativo'}
                </Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/superadmin/platform-users/${item.id}/edit`} variant="soft" size="sm">Editar</Button>
                  {item.status === 'active' && (
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
        title="Inativar usuário da plataforma"
        description={`O usuário ${selectedItem?.name || ''} perderá o acesso de superadmin.`}
        confirmLabel="Inativar usuário"
      />
    </AppLayout>
  );
}
