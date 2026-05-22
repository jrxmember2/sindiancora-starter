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
  const confirmDeactivate = () => {
    if (!selectedItem) {
      return;
    }

    router.delete(`/app/users/${selectedItem.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Usuários">
      <Head title="Usuários" />

      <Card>
        <CardHeader
          title="Usuários internos"
          description="Gerencie acessos, papéis e escopo por condomínio da empresa ativa."
          action={
            <Button href="/app/users/create">
              <Plus className="h-4 w-4" /> Novo usuário
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'user', label: 'Usuário' },
            { key: 'role', label: 'Papel' },
            { key: 'scope', label: 'Escopo' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum usuário interno cadastrado"
          emptyDescription="Crie ou vincule o primeiro usuário para começar a distribuir acessos na empresa."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{item.name}</p>
                <p className="text-sm text-slate-600">{item.email}</p>
                <p className="text-xs text-slate-400">{item.phone || 'Sem telefone informado'}</p>
              </td>
              <td className="px-4 py-4">
                <div className="flex flex-wrap gap-2">
                  <Badge tone="blue">{item.role_label}</Badge>
                  {item.can_access_whatsapp && <Badge tone="green">WhatsApp</Badge>}
                  {item.only_responsible_issues && <Badge tone="yellow">Somente atribuídos</Badge>}
                </div>
              </td>
              <td className="px-4 py-4 text-slate-600">
                {item.has_full_condominium_access ? (
                  <p>Todos os condomínios</p>
                ) : (
                  <div className="space-y-1">
                    <p>{item.condominiums.length} condomínio(s) vinculado(s)</p>
                    <p className="text-xs text-slate-400">{item.condominiums.map((condominium) => condominium.name).join(', ')}</p>
                  </div>
                )}
              </td>
              <td className="px-4 py-4">
                <Badge tone={item.status === 'active' ? 'green' : 'gray'}>
                  {item.status === 'active' ? 'Ativo' : 'Inativo'}
                </Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/app/users/${item.id}/edit`} variant="soft" size="sm">Editar</Button>
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
        onConfirm={confirmDeactivate}
        title="Inativar usuário interno"
        description={`O usuário ${selectedItem?.name || ''} perderá o acesso operacional desta empresa.`}
        confirmLabel="Inativar usuário"
      />
    </AppLayout>
  );
}
