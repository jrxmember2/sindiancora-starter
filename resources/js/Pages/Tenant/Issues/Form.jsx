import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Select, Textarea } from '@/Components/Form';

export default function Form({ issue, condominiums }) {
  const editing = Boolean(issue);
  const { data, setData, post, put, processing } = useForm({
    condominium_id: issue?.condominium_id || '',
    subject: issue?.subject || '',
    description: issue?.description || '',
    status: issue?.status || 'pendente',
    priority: issue?.priority || 'media',
    deadline_at: issue?.deadline_at || '',
    shared_with_residents: Boolean(issue?.shared_with_residents),
  });

  const submit = (e) => {
    e.preventDefault();
    editing ? put(`/app/issues/${issue.id}`) : post('/app/issues');
  };

  return (
    <AppLayout title={editing ? 'Editar chamado' : 'Novo chamado'}>
      <Head title="Chamado" />
      <form onSubmit={submit}>
        <Card>
          <CardHeader title="Dados do chamado" description="Registre ocorrências, demandas e tarefas operacionais." />
          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Condomínio"><Select value={data.condominium_id} onChange={(e) => setData('condominium_id', e.target.value)}><option value="">Selecione</option>{condominiums.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}</Select></Field>
            <Field label="Assunto"><Input value={data.subject} onChange={(e) => setData('subject', e.target.value)} /></Field>
            <Field label="Prazo"><Input type="datetime-local" value={data.deadline_at || ''} onChange={(e) => setData('deadline_at', e.target.value)} /></Field>
            <Field label="Status"><Select value={data.status} onChange={(e) => setData('status', e.target.value)}><option value="pendente">Pendente</option><option value="em_andamento">Em andamento</option><option value="aguardando_assembleia">Aguardando assembleia</option><option value="finalizado">Finalizado</option><option value="cancelado">Cancelado</option></Select></Field>
            <Field label="Prioridade"><Select value={data.priority} onChange={(e) => setData('priority', e.target.value)}><option value="baixa">Baixa</option><option value="media">Média</option><option value="alta">Alta</option><option value="urgente">Urgente</option></Select></Field>
          </div>
          <div className="mt-4"><Field label="Descrição"><Textarea value={data.description} onChange={(e) => setData('description', e.target.value)} /></Field></div>
          <label className="mt-4 flex items-center gap-3 rounded-2xl border border-slate-200 p-4 text-sm font-semibold"><input type="checkbox" checked={data.shared_with_residents} onChange={(e) => setData('shared_with_residents', e.target.checked)} className="rounded border-slate-300 text-blue-600" /> Compartilhar futuramente com condôminos</label>
          <div className="mt-6 flex justify-end"><Button disabled={processing}>Salvar chamado</Button></div>
        </Card>
      </form>
    </AppLayout>
  );
}
