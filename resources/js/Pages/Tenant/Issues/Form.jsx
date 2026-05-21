import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Select, Textarea } from '@/Components/Form';

export default function Form({ issue, condominiums }) {
  const editing = Boolean(issue);
  const { data, setData, post, put, processing, errors } = useForm({
    condominium_id: issue?.condominium_id || '',
    subject: issue?.subject || '',
    description: issue?.description || '',
    status: issue?.status || 'pendente',
    priority: issue?.priority || 'media',
    deadline_at: toDateTimeInput(issue?.deadline_at),
    shared_with_residents: Boolean(issue?.shared_with_residents),
  });

  const submit = (event) => {
    event.preventDefault();
    if (editing) {
      put(`/app/issues/${issue.id}`);
      return;
    }

    post('/app/issues');
  };

  return (
    <AppLayout title={editing ? 'Editar chamado' : 'Novo chamado'}>
      <Head title={editing ? 'Editar chamado' : 'Novo chamado'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
        <CardHeader
          title="Dados do chamado"
          description="Registre ocorrências, demandas e tarefas operacionais."
        />

        <div className="grid gap-4 md:grid-cols-2">
            <Field label="Condomínio" error={errors.condominium_id}>
              <Select value={data.condominium_id} onChange={(event) => setData('condominium_id', event.target.value)}>
                <option value="">Selecione um condomínio</option>
                {condominiums.map((condominium) => (
                  <option key={condominium.id} value={condominium.id}>{condominium.name}</option>
                ))}
              </Select>
            </Field>

            <Field label="Assunto" error={errors.subject}>
              <Input value={data.subject} onChange={(event) => setData('subject', event.target.value)} />
            </Field>

            <Field label="Prazo estimado" optional error={errors.deadline_at}>
              <Input type="datetime-local" value={data.deadline_at} onChange={(event) => setData('deadline_at', event.target.value)} />
            </Field>

            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                <option value="pendente">Pendente</option>
                <option value="em_andamento">Em andamento</option>
                <option value="aguardando_assembleia">Aguardando assembleia</option>
                <option value="finalizado">Finalizado</option>
                <option value="cancelado">Cancelado</option>
              </Select>
            </Field>

            <Field label="Prioridade" error={errors.priority}>
              <Select value={data.priority} onChange={(event) => setData('priority', event.target.value)}>
                <option value="baixa">Baixa</option>
                <option value="media">Média</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
              </Select>
            </Field>
          </div>

          <div className="mt-4">
            <Field label="Descrição" error={errors.description}>
              <Textarea value={data.description} onChange={(event) => setData('description', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Visibilidade"
            description="Preparação para o portal/app do condômino nas fases futuras."
          />

          <Checkbox
            checked={data.shared_with_residents}
            onChange={(event) => setData('shared_with_residents', event.target.checked)}
            label="Compartilhar futuramente com condôminos"
            hint="O chamado continua interno nesta fase, mas já fica marcado para uso futuro."
          />
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/app/issues" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>Salvar chamado</Button>
        </div>
      </form>
    </AppLayout>
  );
}

function toDateTimeInput(value) {
  if (!value) {
    return '';
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return String(value).slice(0, 16);
  }

  const local = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
  return local.toISOString().slice(0, 16);
}
