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
    secondary_color: company?.secondary_color || '#14b8a6',
    status: company?.status || 'active',
  });

  const submit = (event) => {
    event.preventDefault();
    if (editing) {
      put(`/superadmin/companies/${company.id}`);
      return;
    }

    post('/superadmin/companies');
  };

  return (
    <AppLayout title={editing ? 'Editar empresa' : 'Nova empresa'}>
      <Head title={editing ? 'Editar empresa' : 'Nova empresa'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Identidade comercial"
            description="Cada empresa cadastrada aqui se torna um tenant isolado na plataforma."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome da empresa" error={errors.name}>
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} placeholder="Serratech Gestao Condominial" />
            </Field>
            <Field label="Slug" hint="Usado como identificador amigavel do tenant." error={errors.slug}>
              <Input value={data.slug} onChange={(event) => setData('slug', event.target.value)} placeholder="serratech-gestao" />
            </Field>
            <Field label="Documento" optional error={errors.document}>
              <Input value={data.document} onChange={(event) => setData('document', event.target.value)} placeholder="CNPJ ou CPF" />
            </Field>
            <Field label="E-mail principal" optional error={errors.email}>
              <Input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} placeholder="contato@empresa.com.br" />
            </Field>
            <Field label="Telefone" optional error={errors.phone}>
              <Input value={data.phone} onChange={(event) => setData('phone', event.target.value)} placeholder="(00) 00000-0000" />
            </Field>
            <Field label="Responsavel" optional error={errors.responsible_name}>
              <Input value={data.responsible_name} onChange={(event) => setData('responsible_name', event.target.value)} placeholder="Nome da pessoa responsavel" />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Status e identidade visual"
            description="As cores serao usadas nas proximas fases para personalizacao do tenant."
          />

          <div className="grid gap-4 md:grid-cols-3">
            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                <option value="active">Ativa</option>
                <option value="inactive">Inativa</option>
                <option value="suspended">Suspensa</option>
              </Select>
            </Field>
            <Field label="Cor principal" optional error={errors.primary_color}>
              <Input value={data.primary_color} onChange={(event) => setData('primary_color', event.target.value)} />
            </Field>
            <Field label="Cor secundaria" optional error={errors.secondary_color}>
              <Input value={data.secondary_color} onChange={(event) => setData('secondary_color', event.target.value)} />
            </Field>
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/superadmin/companies" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>
            {editing ? 'Salvar alteracoes' : 'Criar empresa'}
          </Button>
        </div>
      </form>
    </AppLayout>
  );
}
