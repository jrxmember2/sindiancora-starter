import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Select, Textarea } from '@/Components/Form';

const typeOptions = [
  'ata',
  'contrato',
  'cartao_cnpj',
  'conclusao_obra',
  'convencao',
  'regimento_interno',
  'orcamento',
  'orcamento_anual',
  'planta',
  'prestacao_contas',
  'processo_judicial',
  'reforma_particular',
  'outros',
];

const statusOptions = ['valido', 'vencido', 'proximo_vencimento', 'sem_vigencia'];

export default function Form({ item, condominiums = [] }) {
  const editing = Boolean(item);
  const { data, setData, post, put, processing, errors } = useForm({
    condominium_id: item?.condominium_id || '',
    title: item?.title || '',
    document_type: item?.document_type || 'outros',
    amount: item?.amount || '',
    valid_until: toDateInput(item?.valid_until),
    renewal_date: toDateInput(item?.renewal_date),
    status: item?.status || 'sem_vigencia',
    available_to_residents: Boolean(item?.available_to_residents),
    added_to_ai_assistant: Boolean(item?.added_to_ai_assistant),
    observation: item?.observation || '',
    file_path: item?.file_path || '',
  });

  const submit = (event) => {
    event.preventDefault();
    if (editing) {
      put(`/app/documents/${item.id}`);
      return;
    }

    post('/app/documents');
  };

  return (
    <AppLayout title={editing ? 'Editar documento' : 'Novo documento'}>
      <Head title={editing ? 'Editar documento' : 'Novo documento'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Classificacao do documento"
            description="Nesta fase o upload real ainda nao foi habilitado, entao usamos a referencia do arquivo."
          />

          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Field label="Condominio" optional error={errors.condominium_id}>
              <Select value={data.condominium_id} onChange={(event) => setData('condominium_id', event.target.value)}>
                <option value="">Documento geral da empresa</option>
                {condominiums.map((condominium) => (
                  <option key={condominium.id} value={condominium.id}>{condominium.name}</option>
                ))}
              </Select>
            </Field>
            <Field label="Titulo" error={errors.title}>
              <Input value={data.title} onChange={(event) => setData('title', event.target.value)} />
            </Field>
            <Field label="Tipo" error={errors.document_type}>
              <Select value={data.document_type} onChange={(event) => setData('document_type', event.target.value)}>
                {typeOptions.map((type) => <option key={type} value={type}>{type}</option>)}
              </Select>
            </Field>
            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                {statusOptions.map((status) => <option key={status} value={status}>{status}</option>)}
              </Select>
            </Field>
            <Field label="Valor" optional error={errors.amount}>
              <Input type="number" step="0.01" min="0" value={data.amount} onChange={(event) => setData('amount', event.target.value)} />
            </Field>
            <Field label="Referencia do arquivo" optional error={errors.file_path}>
              <Input value={data.file_path} onChange={(event) => setData('file_path', event.target.value)} placeholder="storage/documentos/arquivo.pdf" />
            </Field>
            <Field label="Valido ate" optional error={errors.valid_until}>
              <Input type="date" value={data.valid_until} onChange={(event) => setData('valid_until', event.target.value)} />
            </Field>
            <Field label="Renovacao" optional error={errors.renewal_date}>
              <Input type="date" value={data.renewal_date} onChange={(event) => setData('renewal_date', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader title="Visibilidade e observacoes" description="Controles preparatorios para app e IA nas fases futuras." />

          <div className="grid gap-4 md:grid-cols-2">
            <Checkbox
              checked={data.available_to_residents}
              onChange={(event) => setData('available_to_residents', event.target.checked)}
              label="Disponivel para condominos"
            />
            <Checkbox
              checked={data.added_to_ai_assistant}
              onChange={(event) => setData('added_to_ai_assistant', event.target.checked)}
              label="Disponibilizar para IA futuramente"
            />
          </div>

          <div className="mt-4">
            <Field label="Observacao" optional error={errors.observation}>
              <Textarea value={data.observation} onChange={(event) => setData('observation', event.target.value)} />
            </Field>
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/app/documents" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>{editing ? 'Salvar documento' : 'Criar documento'}</Button>
        </div>
      </form>
    </AppLayout>
  );
}

function toDateInput(value) {
  return value ? String(value).slice(0, 10) : '';
}
