import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Select, Textarea } from '@/Components/Form';

const typeOptions = [
  { value: 'ata', label: 'Ata' },
  { value: 'contrato', label: 'Contrato' },
  { value: 'cartao_cnpj', label: 'Cartão CNPJ' },
  { value: 'conclusao_obra', label: 'Conclusão de obra' },
  { value: 'convencao', label: 'Convenção' },
  { value: 'regimento_interno', label: 'Regimento interno' },
  { value: 'orcamento', label: 'Orçamento' },
  { value: 'orcamento_anual', label: 'Orçamento anual' },
  { value: 'planta', label: 'Planta' },
  { value: 'prestacao_contas', label: 'Prestação de contas' },
  { value: 'processo_judicial', label: 'Processo judicial' },
  { value: 'reforma_particular', label: 'Reforma particular' },
  { value: 'outros', label: 'Outros' },
];

const statusOptions = [
  { value: 'valido', label: 'Válido' },
  { value: 'vencido', label: 'Vencido' },
  { value: 'proximo_vencimento', label: 'Próximo do vencimento' },
  { value: 'sem_vigencia', label: 'Sem vigência' },
];

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
            title="Classificação do documento"
            description="Nesta fase o upload real ainda não foi habilitado, então usamos a referência do arquivo."
          />

          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Field label="Condomínio" optional error={errors.condominium_id}>
              <Select value={data.condominium_id} onChange={(event) => setData('condominium_id', event.target.value)}>
                <option value="">Documento geral da empresa</option>
                {condominiums.map((condominium) => (
                  <option key={condominium.id} value={condominium.id}>{condominium.name}</option>
                ))}
              </Select>
            </Field>
            <Field label="Título" error={errors.title}>
              <Input value={data.title} onChange={(event) => setData('title', event.target.value)} />
            </Field>
            <Field label="Tipo" error={errors.document_type}>
              <Select value={data.document_type} onChange={(event) => setData('document_type', event.target.value)}>
                {typeOptions.map((type) => <option key={type.value} value={type.value}>{type.label}</option>)}
              </Select>
            </Field>
            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                {statusOptions.map((status) => <option key={status.value} value={status.value}>{status.label}</option>)}
              </Select>
            </Field>
            <Field label="Valor" optional error={errors.amount}>
              <Input type="number" step="0.01" min="0" value={data.amount} onChange={(event) => setData('amount', event.target.value)} />
            </Field>
            <Field label="Referência do arquivo" optional error={errors.file_path}>
              <Input value={data.file_path} onChange={(event) => setData('file_path', event.target.value)} placeholder="storage/documentos/arquivo.pdf" />
            </Field>
            <Field label="Válido até" optional error={errors.valid_until}>
              <Input type="date" value={data.valid_until} onChange={(event) => setData('valid_until', event.target.value)} />
            </Field>
            <Field label="Renovação" optional error={errors.renewal_date}>
              <Input type="date" value={data.renewal_date} onChange={(event) => setData('renewal_date', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader title="Visibilidade e observações" description="Controles preparatórios para app e IA nas fases futuras." />

          <div className="grid gap-4 md:grid-cols-2">
            <Checkbox
              checked={data.available_to_residents}
              onChange={(event) => setData('available_to_residents', event.target.checked)}
              label="Disponível para condôminos"
            />
            <Checkbox
              checked={data.added_to_ai_assistant}
              onChange={(event) => setData('added_to_ai_assistant', event.target.checked)}
              label="Disponibilizar para IA futuramente"
            />
          </div>

          <div className="mt-4">
            <Field label="Observação" optional error={errors.observation}>
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
