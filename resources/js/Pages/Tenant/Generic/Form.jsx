import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Textarea } from '@/Components/Form';

export default function Form({ item }) {
  const path = window.location.pathname.split('/')[2];
  const editing = Boolean(item);
  const { data, setData, post, put, processing } = useForm({
    name: item?.name || '',
    title: item?.title || '',
    document: item?.document || '',
    email: item?.email || '',
    phone: item?.phone || '',
    description: item?.description || '',
    status: item?.status || 'active',
  });

  const submit = (e) => {
    e.preventDefault();
    editing ? put(`/app/${path}/${item.id}`) : post(`/app/${path}`);
  };

  return (
    <AppLayout title={editing ? 'Editar registro' : 'Novo registro'}>
      <Head title="Registro" />
      <form onSubmit={submit}>
        <Card>
          <CardHeader title="Dados básicos" description="Primeira versão do formulário. O Codex deve expandir conforme cada módulo." />
          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome"><Input value={data.name} onChange={(e) => setData('name', e.target.value)} /></Field>
            <Field label="Título"><Input value={data.title} onChange={(e) => setData('title', e.target.value)} /></Field>
            <Field label="Documento"><Input value={data.document} onChange={(e) => setData('document', e.target.value)} /></Field>
            <Field label="E-mail"><Input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} /></Field>
            <Field label="Telefone"><Input value={data.phone} onChange={(e) => setData('phone', e.target.value)} /></Field>
          </div>
          <div className="mt-4"><Field label="Descrição"><Textarea value={data.description} onChange={(e) => setData('description', e.target.value)} /></Field></div>
          <div className="mt-6 flex justify-end"><Button disabled={processing}>Salvar</Button></div>
        </Card>
      </form>
    </AppLayout>
  );
}
