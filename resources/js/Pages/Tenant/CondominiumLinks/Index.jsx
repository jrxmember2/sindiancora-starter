import React from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import EmptyState from '@/Components/EmptyState';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ incoming, outgoing }) {
  const decide = (requestId, decision) => {
    router.post(`/app/condominium-link-requests/${requestId}/decide`, { decision }, { preserveScroll: true });
  };

  return (
    <AppLayout title="Solicitações de condomínio">
      <Head title="Solicitações de condomínio" />

      <div className="grid gap-6 xl:grid-cols-2">
        <Card>
          <CardHeader
            title="Solicitações recebidas"
            description="Avalie quando outra empresa tentar vincular um condomínio que hoje está sob a gestão principal da sua empresa."
          />

          {incoming.length === 0 ? (
            <EmptyState title="Nenhuma solicitação recebida" description="Quando outra empresa tentar vincular um condomínio da sua carteira, a pendência aparecerá aqui." />
          ) : (
            <div className="space-y-4">
              {incoming.map((item) => (
                <RequestCard
                  key={item.id}
                  item={item}
                  footer={item.status === 'pending' ? (
                    <div className="flex flex-wrap gap-2">
                      <Button type="button" size="sm" onClick={() => decide(item.id, 'share')}>Mesclar</Button>
                      <Button type="button" size="sm" variant="soft" onClick={() => decide(item.id, 'transfer')}>Transferir</Button>
                      <Button type="button" size="sm" variant="danger" onClick={() => decide(item.id, 'reject')}>Recusar</Button>
                    </div>
                  ) : null}
                />
              ))}
            </div>
          )}
        </Card>

        <Card>
          <CardHeader
            title="Solicitações enviadas"
            description="Acompanhe as solicitações abertas pela sua empresa quando o CNPJ do condomínio já existir na plataforma."
          />

          {outgoing.length === 0 ? (
            <EmptyState title="Nenhuma solicitação enviada" description="Quando um condomínio já existir em outra empresa, o pedido aparecerá aqui até ser decidido." />
          ) : (
            <div className="space-y-4">
              {outgoing.map((item) => (
                <RequestCard key={item.id} item={item} />
              ))}
            </div>
          )}
        </Card>
      </div>
    </AppLayout>
  );
}

function RequestCard({ item, footer = null }) {
  return (
    <div className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="flex flex-wrap items-start justify-between gap-3">
        <div>
          <p className="text-base font-bold text-slate-900">{item.condominium?.name}</p>
          <p className="text-sm text-slate-500">
            {item.condominium?.document || 'Documento não informado'}
            {item.condominium?.city ? ` • ${item.condominium.city}${item.condominium.state ? `/${item.condominium.state}` : ''}` : ''}
          </p>
        </div>

        <Badge tone={badgeTone(item.status)}>
          {statusLabel(item.status)}
        </Badge>
      </div>

      <div className="mt-4 space-y-2 text-sm text-slate-600">
        {item.requesting_company && <p><span className="font-semibold text-slate-800">Empresa solicitante:</span> {item.requesting_company.name}</p>}
        {item.current_primary_company && <p><span className="font-semibold text-slate-800">Empresa principal atual:</span> {item.current_primary_company.name}</p>}
        {item.requested_by && <p><span className="font-semibold text-slate-800">Solicitado por:</span> {item.requested_by}</p>}
        <p><span className="font-semibold text-slate-800">Enviado em:</span> {item.created_at}</p>
        {item.resolved_at && <p><span className="font-semibold text-slate-800">Resolvido em:</span> {item.resolved_at}</p>}
        {item.request_notes && <p><span className="font-semibold text-slate-800">Observações do pedido:</span> {item.request_notes}</p>}
        {item.decision_notes && <p><span className="font-semibold text-slate-800">Observações da decisão:</span> {item.decision_notes}</p>}
      </div>

      {footer && <div className="mt-4">{footer}</div>}
    </div>
  );
}

function statusLabel(status) {
  return {
    pending: 'Pendente',
    shared: 'Mesclado',
    transferred: 'Transferido',
    rejected: 'Recusado',
  }[status] || status;
}

function badgeTone(status) {
  return {
    pending: 'yellow',
    shared: 'blue',
    transferred: 'green',
    rejected: 'gray',
  }[status] || 'gray';
}
