import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Select } from '@/Components/Form';

export default function Form({ company }) {
  const editing = Boolean(company);
  const { data, setData, post, put, processing, errors } = useForm({
    name: company?.name || '',
    document: company?.document || '',
    email: company?.email || '',
    phone: company?.phone || '',
    responsible_name: company?.responsible_name || '',
    slug: company?.slug || '',
    primary_color: company?.primary_color || '#0f172a',
    secondary_color: company?.secondary_color || '#2563eb',
    status: company?.status || 'active',
  });

  const submit = (e) => {
    e.preventDefault();
    editing ? put(`/superadmin/companies/${company.id}`) : post('/superadmin/companies');
  };

  return (
    <AppLayout title={editing ? 'Editar empresa' : 'Nova empresa'}>
      <Head title={editing ? 'Editar empresa' : 'Nova empresa'} />
      <form onSubmit={submit}>
        <Card>
          <CardHeader title="Dados da empresa" description="Esta empresa será um tenant isolado dentro da plataforma." />
          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome" error={errors.name}><Input value={data.name} onChange={(e) => setData('name', e.target.value)} /></Field>
            <Field label="Slug" error={errors.slug}><Input value={data.slug} onChange={(e) => setData('slug', e.target.value)} placeholder="sindica-andressa" /></Field>
            <Field label="Documento" error={errors.document}><Input value={data.document} onChange={(e) => setData('document', e.target.value)} /></Field>
            <Field label="E-mail" error={errors.email}><Input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} /></Field>
            <Field label="Telefone" error={errors.phone}><Input value={data.phone} onChange={(e) => setData('phone', e.target.value)} /></Field>
            <Field label="Responsável" error={errors.responsible_name}><Input value={data.responsible_name} onChange={(e) => setData('responsible_name', e.target.value)} /></Field>
            <Field label="Cor principal"><Input value={data.primary_color} onChange={(e) => setData('primary_color', e.target.value)} /></Field>
            <Field label="Status"><Select value={data.status} onChange={(e) => setData('status', e.target.value)}><option value="active">Ativa</option><option value="inactive">Inativa</option><option value="suspended">Suspensa</option></Select></Field>
          </div>
          <div className="mt-6 flex justify-end"><Button disabled={processing}>{editing ? 'Salvar alterações' : 'Criar empresa'}</Button></div>
        </Card>
      </form>
    </AppLayout>
  );
}
