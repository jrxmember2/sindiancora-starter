import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import ConfirmDialog from '@/Components/ConfirmDialog';
import DataTable from '@/Components/DataTable';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ issues }) {
  const [selectedIssue, setSelectedIssue] = useState(null);

  const closeDialog = () => setSelectedIssue(null);
  const confirmCancel = () => {
    if (!selectedIssue) {
      return;
    }

    router.delete(`/app/issues/${selectedIssue.id}`, {
      preserveScroll: true,
      onSuccess: closeDialog,
    });
  };

  return (
    <AppLayout title="Chamados">
      <Head title="Chamados" />

      <Card>
        <CardHeader
          title="Chamados"
          description="Base operacional de ocorrencias, tarefas e cobrancas internas."
          action={
            <Button href="/app/issues/create">
              <Plus className="h-4 w-4" /> Novo chamado
            </Button>
          }
        />

        <DataTable
          columns={[
            { key: 'subject', label: 'Chamado' },
            { key: 'condominium', label: 'Condominio' },
            { key: 'deadline', label: 'Prazo' },
            { key: 'status', label: 'Status' },
            { key: 'priority', label: 'Prioridade' },
            { key: 'actions', label: 'Acoes', align: 'right', className: 'w-40' },
          ]}
          rows={issues.data}
          meta={issues}
          emptyTitle="Nenhum chamado criado"
          emptyDescription="Abra o primeiro chamado para iniciar a operacao do tenant."
          renderRow={(issue) => (
            <tr key={issue.id} className="bg-white">
              <td className="px-4 py-4">
                <p className="font-bold text-slate-900">#{issue.id} - {issue.subject}</p>
                <p className="text-xs text-slate-500">{issue.shared_with_residents ? 'Compartilhavel com condominos' : 'Uso interno'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">{issue.condominium?.name || 'Sem condominio'}</td>
              <td className="px-4 py-4 text-slate-600">{issue.deadline_at ? formatDateTime(issue.deadline_at) : 'Sem prazo'}</td>
              <td className="px-4 py-4"><Badge tone={statusTone(issue.status)}>{issue.status}</Badge></td>
              <td className="px-4 py-4"><Badge tone={priorityTone(issue.priority)}>{issue.priority}</Badge></td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  <Button href={`/app/issues/${issue.id}/edit`} variant="soft" size="sm">Editar</Button>
                  {issue.status !== 'cancelado' && issue.status !== 'finalizado' && (
                    <Button type="button" variant="danger" size="sm" onClick={() => setSelectedIssue(issue)}>
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
        open={Boolean(selectedIssue)}
        onClose={closeDialog}
        onConfirm={confirmCancel}
        title="Cancelar chamado"
        description={`O chamado #${selectedIssue?.id || ''} sera marcado como cancelado.`}
        confirmLabel="Cancelar chamado"
      />
    </AppLayout>
  );
}

function formatDateTime(value) {
  if (!value) return 'Sem prazo';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
}

function statusTone(status) {
  if (status === 'finalizado') return 'green';
  if (status === 'cancelado') return 'gray';
  if (status === 'aguardando_assembleia') return 'yellow';
  return 'blue';
}

function priorityTone(priority) {
  if (priority === 'urgente') return 'red';
  if (priority === 'alta') return 'yellow';
  if (priority === 'media') return 'blue';
  return 'gray';
}
