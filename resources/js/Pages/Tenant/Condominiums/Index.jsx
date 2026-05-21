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

    router.delete(`/app/condominiums/${selectedItem.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Condominios">
      <Head title="Condominios" />

      <Card>
        <CardHeader
          title="Condominios"
          description="Gerencie a carteira ativa e inativa da empresa selecionada."
          action={
            <Button href="/app/condominiums/create">
              <Plus className="h-4 w-4" /> Novo condominio
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'name', label: 'Condominio' },
            { key: 'contact', label: 'Contato' },
            { key: 'city', label: 'Cidade' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Acoes', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum condominio cadastrado"
          emptyDescription="Cadastre o primeiro condominio da empresa para iniciar a operacao."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{item.name}</p>
                <p className="text-xs text-slate-500">{item.document || 'Documento nao informado'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{item.email || 'Sem e-mail'}</p>
                <p className="text-xs text-slate-400">{item.phone || 'Sem telefone'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                {item.city ? `${item.city}${item.state ? `/${item.state}` : ''}` : 'Nao informado'}
              </td>
              <td className="px-4 py-4">
                <Badge tone={item.status === 'active' ? 'green' : 'gray'}>{item.status}</Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/app/condominiums/${item.id}/edit`} variant="soft" size="sm">Editar</Button>
                  {item.status !== 'inactive' && (
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
        title="Inativar condominio"
        description={`O condominio ${selectedItem?.name || ''} deixara de contar como ativo na licenca.`}
        confirmLabel="Inativar condominio"
      />
    </AppLayout>
  );
}
