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
          description="Organize contratos, convenções, regimentos e demais arquivos do condomínio."
          action={
            <Button href="/app/documents/create">
              <Plus className="h-4 w-4" /> Novo documento
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'title', label: 'Documento' },
            { key: 'condominium', label: 'Condomínio' },
            { key: 'validity', label: 'Vigência' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum documento cadastrado"
          emptyDescription="Cadastre o primeiro documento para iniciar o acervo digital da empresa."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">{item.title}</p>
                <p className="text-xs text-slate-500">{documentTypeLabel(item.document_type)}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">{item.condominium?.name || 'Geral da empresa'}</td>
              <td className="px-4 py-4 text-slate-600">
                <p>{formatDate(item.valid_until)}</p>
                <p className="text-xs text-slate-400">Renovação: {formatDate(item.renewal_date)}</p>
              </td>
              <td className="px-4 py-4">
                <Badge tone={statusTone(item.status)}>{documentStatusLabel(item.status)}</Badge>
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
        description={`O documento ${selectedItem?.title || ''} será excluído da base atual.`}
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

function documentStatusLabel(status) {
  const labels = {
    valido: 'Válido',
    vencido: 'Vencido',
    proximo_vencimento: 'Próximo do vencimento',
    sem_vigencia: 'Sem vigência',
  };

  return labels[status] || status || 'Não informado';
}

function documentTypeLabel(type) {
  const labels = {
    ata: 'Ata',
    contrato: 'Contrato',
    cartao_cnpj: 'Cartão CNPJ',
    conclusao_obra: 'Conclusão de obra',
    convencao: 'Convenção',
    regimento_interno: 'Regimento interno',
    orcamento: 'Orçamento',
    orcamento_anual: 'Orçamento anual',
    planta: 'Planta',
    prestacao_contas: 'Prestação de contas',
    processo_judicial: 'Processo judicial',
    reforma_particular: 'Reforma particular',
    outros: 'Outros',
  };

  return labels[type] || 'Tipo não informado';
}
