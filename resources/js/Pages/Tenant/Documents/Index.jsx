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
  const confirmDelete = () => {
    if (!selectedItem) {
      return;
    }

    router.delete(`/app/documents/${selectedItem.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Documentos">
      <Head title="Documentos" />

      <Card>
        <CardHeader
          title="Documentos"
          description="Organize contratos, convencoes, regimentos e demais arquivos do condominio."
          action={
            <Button href="/app/documents/create">
              <Plus className="h-4 w-4" /> Novo documento
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'title', label: 'Documento' },
            { key: 'condominium', label: 'Condominio' },
            { key: 'validity', label: 'Vigencia' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Acoes', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum documento cadastrado"
          emptyDescription="Cadastre o primeiro documento para iniciar o acervo digital da empresa."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{item.title}</p>
                <p className="text-xs text-slate-500">{item.document_type || 'tipo nao informado'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">{item.condominium?.name || 'Geral da empresa'}</td>
              <td className="px-4 py-4 text-slate-600">
                <p>{formatDate(item.valid_until)}</p>
                <p className="text-xs text-slate-400">Renovacao: {formatDate(item.renewal_date)}</p>
              </td>
              <td className="px-4 py-4">
                <Badge tone={statusTone(item.status)}>{item.status}</Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/app/documents/${item.id}/edit`} variant="soft" size="sm">Editar</Button>
                  <Button type="button" variant="danger" size="sm" onClick={() => setSelectedItem(item)}>
                    Remover
                  </Button>
                </div>
              </td>
            </tr>
          )}
        />
      </Card>

      <ConfirmDialog
        open={Boolean(selectedItem)}
        onClose={closeDialog}
        onConfirm={confirmDelete}
        title="Remover documento"
        description={`O documento ${selectedItem?.title || ''} sera excluido da base atual.`}
        confirmLabel="Remover documento"
      />
    </AppLayout>
  );
}

function formatDate(value) {
  return value ? String(value).slice(0, 10) : 'Sem data';
}

function statusTone(status) {
  if (status === 'valido') return 'green';
  if (status === 'proximo_vencimento') return 'yellow';
  if (status === 'vencido') return 'red';
  return 'gray';
}
