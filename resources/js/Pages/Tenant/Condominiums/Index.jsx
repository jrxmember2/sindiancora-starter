import React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Plus, Search, X } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import ConfirmDialog from '@/Components/ConfirmDialog';
import DataTable from '@/Components/DataTable';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Select } from '@/Components/Form';

export default function Index({ items, filters, summary }) {
  const [selectedItem, setSelectedItem] = React.useState(null);
  const { data, setData, get, processing, reset } = useForm({
    search: filters?.search || '',
    status: filters?.status || '',
  });

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

  const submitFilters = (event) => {
    event.preventDefault();

    get('/app/condominiums', {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  };

  const clearFilters = () => {
    reset();

    router.get('/app/condominiums', {}, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    });
  };

  return (
    <AppLayout title="Condomínios">
      <Head title="Condomínios" />

      <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <SummaryCard label="Ativos na licença" value={summary.active} helper={`Limite contratado: ${summary.limit}`} tone="green" />
        <SummaryCard label="Vagas restantes" value={summary.remaining} helper="Novos cadastros ativos disponíveis" tone="blue" />
        <SummaryCard label="Inativos arquivados" value={summary.inactive} helper="Mantidos fora da contagem ativa" tone="slate" />
        <SummaryCard label="Portfólio total" value={summary.active + summary.inactive} helper="Carteira visível na empresa ativa" tone="amber" />
      </div>

      <Card>
        <CardHeader
          title="Condomínios"
          description="Gerencie a carteira ativa e inativa da empresa selecionada, com logo, mandato, vínculo principal ou solidário e filtros operacionais."
          action={(
            <Button href="/app/condominiums/create">
              <Plus className="h-4 w-4" /> Novo condomínio
            </Button>
          )}
        />

        <form onSubmit={submitFilters} className="mb-6 grid gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-4 lg:grid-cols-[1.5fr_220px_auto]">
          <Field label="Busca" hint="Nome, documento, cidade ou administradora">
            <Input
              value={data.search}
              onChange={(event) => setData('search', event.target.value)}
              placeholder="Ex.: Residencial Anchieta"
            />
          </Field>

          <Field label="Status">
            <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
              <option value="">Todos</option>
              <option value="active">Ativos</option>
              <option value="inactive">Inativos</option>
            </Select>
          </Field>

          <div className="flex flex-wrap items-end gap-3">
            <Button type="submit" disabled={processing}>
              <Search className="h-4 w-4" /> Filtrar
            </Button>
            <Button type="button" variant="soft" onClick={clearFilters}>
              <X className="h-4 w-4" /> Limpar
            </Button>
          </div>
        </form>

        <DataTable
          columns={[
            { key: 'name', label: 'Condomínio' },
            { key: 'contact', label: 'Contato' },
            { key: 'management', label: 'Gestão' },
            { key: 'status', label: 'Status' },
            { key: 'actions', label: 'Ações', align: 'right', className: 'w-40' },
          ]}
          rows={items.data}
          meta={items}
          emptyTitle="Nenhum condomínio encontrado"
          emptyDescription="Ajuste os filtros ou cadastre um novo condomínio para iniciar a operação."
          renderRow={(item) => (
            <tr key={item.id} className="bg-white">
              <td className="px-4 py-4">
                <div className="flex items-center gap-3">
                  {item.logo_url ? (
                    <img src={item.logo_url} alt={`Logo de ${item.name}`} className="h-12 w-12 rounded-2xl border border-slate-200 object-cover" />
                  ) : (
                    <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xs font-black text-slate-700">
                      {item.initials || 'SC'}
                    </div>
                  )}

                  <div>
                    <p className="font-bold text-slate-900">{item.name}</p>
                    <p className="text-xs text-slate-500">{item.document || 'Documento não informado'}</p>
                    <p className="text-xs text-slate-400">{item.city ? `${item.city}${item.state ? `/${item.state}` : ''}` : 'Cidade não informada'}</p>
                  </div>
                </div>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{item.email || 'Sem e-mail'}</p>
                <p className="text-xs text-slate-400">{item.phone || 'Sem telefone'}</p>
              </td>
              <td className="px-4 py-4 text-slate-600">
                <p>{item.administrator_name || 'Administradora não informada'}</p>
                <div className="mt-2 flex flex-wrap items-center gap-2">
                  <Badge tone={item.relationship_type === 'principal' ? 'blue' : 'slate'}>
                    {item.relationship_label}
                  </Badge>
                  <span className="text-xs text-slate-400">
                    {item.mandate_start || item.mandate_end
                      ? `Mandato: ${item.mandate_start || '--'} até ${item.mandate_end || '--'}`
                      : 'Mandato não informado'}
                  </span>
                </div>
              </td>
              <td className="px-4 py-4">
                <Badge tone={item.status === 'active' ? 'green' : 'gray'}>{item.status_label}</Badge>
              </td>
              <td className="px-4 py-4">
                <div className="flex justify-end gap-2">
                  {item.can_manage_registry ? (
                    <Button href={`/app/condominiums/${item.id}/edit`} variant="soft" size="sm">Editar</Button>
                  ) : (
                    <span className="inline-flex items-center rounded-xl bg-slate-100 px-3 text-xs font-semibold text-slate-500">
                      Cadastro mestre protegido
                    </span>
                  )}
                  {item.status !== 'inactive' && item.can_manage_registry && (
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
        title="Inativar condomínio"
        description={`O condomínio ${selectedItem?.name || ''} deixará de contar como ativo na licença, mas seus dados permanecerão arquivados.`}
        confirmLabel="Inativar condomínio"
      />
    </AppLayout>
  );
}

function SummaryCard({ label, value, helper, tone }) {
  const toneClasses = {
    green: 'border-emerald-200 bg-emerald-50 text-emerald-900',
    blue: 'border-blue-200 bg-blue-50 text-blue-900',
    slate: 'border-slate-200 bg-slate-50 text-slate-900',
    amber: 'border-amber-200 bg-amber-50 text-amber-900',
  };

  return (
    <div className={`rounded-3xl border p-5 ${toneClasses[tone] || toneClasses.slate}`}>
      <p className="text-xs font-semibold uppercase tracking-wide opacity-70">{label}</p>
      <p className="mt-3 text-3xl font-black tracking-tight">{value}</p>
      <p className="mt-2 text-sm opacity-80">{helper}</p>
    </div>
  );
}
