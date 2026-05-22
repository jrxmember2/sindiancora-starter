import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import DataTable from '@/Components/DataTable';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Select } from '@/Components/Form';

export default function Index({ requests, companies, condominiums }) {
  const { data, setData, post, processing, reset } = useForm({
    condominium_id: '',
    target_company_id: '',
    decision_notes: '',
  });

  const submitForceTransfer = (event) => {
    event.preventDefault();

    if (!data.condominium_id) {
      return;
    }

    post(`/superadmin/condominium-governance/${data.condominium_id}/force-transfer`, {
      preserveScroll: true,
      onSuccess: () => reset(),
    });
  };

  return (
    <AppLayout title="Governança de condomínios">
      <Head title="Governança de condomínios" />

      <div className="space-y-6">
        <Card>
          <CardHeader
            title="Transferência forçada pelo superadmin"
            description="Use apenas quando for necessário resolver conflito comercial ou sucessão de síndico com intervenção da plataforma."
          />

          <form onSubmit={submitForceTransfer} className="grid gap-4 md:grid-cols-3">
            <Field label="Condomínio canônico">
              <Select value={data.condominium_id} onChange={(event) => setData('condominium_id', event.target.value)}>
                <option value="">Selecionar condomínio</option>
                {condominiums.map((condominium) => (
                  <option key={condominium.id} value={condominium.id}>
                    {condominium.name} {condominium.document ? `• ${condominium.document}` : ''}
                  </option>
                ))}
              </Select>
            </Field>

            <Field label="Nova empresa principal">
              <Select value={data.target_company_id} onChange={(event) => setData('target_company_id', event.target.value)}>
                <option value="">Selecionar empresa</option>
                {companies.map((company) => (
                  <option key={company.id} value={company.id}>
                    {company.name}
                  </option>
                ))}
              </Select>
            </Field>

            <Field label="Observações" optional>
              <Input value={data.decision_notes} onChange={(event) => setData('decision_notes', event.target.value)} placeholder="Motivo da intervenção" />
            </Field>

            <div className="md:col-span-3 flex justify-end">
              <Button type="submit" disabled={processing}>Aplicar transferência forçada</Button>
            </div>
          </form>
        </Card>

        <Card>
          <CardHeader
            title="Histórico de solicitações"
            description="Visão macro das solicitações de vínculo, mescla, transferência e recusa entre empresas."
          />

          <DataTable
            columns={[
              { key: 'condominium', label: 'Condomínio' },
              { key: 'companies', label: 'Empresas' },
              { key: 'status', label: 'Status' },
              { key: 'dates', label: 'Datas' },
            ]}
            rows={requests.data}
            meta={requests}
            emptyTitle="Nenhuma solicitação registrada"
            emptyDescription="As pendências e decisões de governança aparecerão aqui."
            renderRow={(item) => (
              <tr key={item.id} className="bg-white">
                <td className="px-4 py-4">
                  <p className="font-bold text-slate-900">{item.condominium?.name || 'Condomínio removido'}</p>
                  <p className="text-xs text-slate-500">{item.condominium?.document || 'Documento não informado'}</p>
                </td>
                <td className="px-4 py-4 text-slate-600">
                  <p><span className="font-semibold text-slate-800">Solicitante:</span> {item.requesting_company?.name || '—'}</p>
                  <p className="text-xs text-slate-400"><span className="font-semibold">Principal:</span> {item.current_primary_company?.name || '—'}</p>
                </td>
                <td className="px-4 py-4">
                  <Badge tone={badgeTone(item.status)}>{statusLabel(item.status)}</Badge>
                  {item.decision_type && (
                    <p className="mt-2 text-xs text-slate-500">Decisão: {item.decision_type}</p>
                  )}
                </td>
                <td className="px-4 py-4 text-slate-600">
                  <p>{item.created_at}</p>
                  <p className="text-xs text-slate-400">{item.resolved_at || 'Ainda pendente'}</p>
                </td>
              </tr>
            )}
          />
        </Card>
      </div>
    </AppLayout>
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
