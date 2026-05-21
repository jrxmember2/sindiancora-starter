import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Select } from '@/Components/Form';

export default function Form({ item }) {
  const editing = Boolean(item);
  const { data, setData, post, put, processing, errors } = useForm({
    name: item?.name || '',
    document: item?.document || '',
    email: item?.email || '',
    phone: item?.phone || '',
    slug: item?.slug || '',
    status: item?.status || 'active',
    cep: item?.cep || '',
    street: item?.street || '',
    number: item?.number || '',
    complement: item?.complement || '',
    district: item?.district || '',
    city: item?.city || '',
    state: item?.state || '',
    mandate_start: toDateInput(item?.mandate_start),
    mandate_end: toDateInput(item?.mandate_end),
    administrator_name: item?.administrator_name || '',
  });

  const submit = (event) => {
    event.preventDefault();
    if (editing) {
      put(`/app/condominiums/${item.id}`);
      return;
    }

    post('/app/condominiums');
  };

  return (
    <AppLayout title={editing ? 'Editar condominio' : 'Novo condominio'}>
      <Head title={editing ? 'Editar condominio' : 'Novo condominio'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Dados cadastrais"
            description="Base principal do condominio dentro da empresa ativa."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome do condominio" error={errors.name}>
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} />
            </Field>
            <Field label="Slug" optional error={errors.slug}>
              <Input value={data.slug} onChange={(event) => setData('slug', event.target.value)} placeholder="residencial-anchieta" />
            </Field>
            <Field label="Documento" optional error={errors.document}>
              <Input value={data.document} onChange={(event) => setData('document', event.target.value)} />
            </Field>
            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </Select>
            </Field>
            <Field label="E-mail" optional error={errors.email}>
              <Input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} />
            </Field>
            <Field label="Telefone" optional error={errors.phone}>
              <Input value={data.phone} onChange={(event) => setData('phone', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader title="Endereco e gestao" description="Dados operacionais para contato e mandato." />

          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Field label="CEP" optional error={errors.cep}>
              <Input value={data.cep} onChange={(event) => setData('cep', event.target.value)} />
            </Field>
            <Field label="Rua" optional className="xl:col-span-2" error={errors.street}>
              <Input value={data.street} onChange={(event) => setData('street', event.target.value)} />
            </Field>
            <Field label="Numero" optional error={errors.number}>
              <Input value={data.number} onChange={(event) => setData('number', event.target.value)} />
            </Field>
            <Field label="Complemento" optional error={errors.complement}>
              <Input value={data.complement} onChange={(event) => setData('complement', event.target.value)} />
            </Field>
            <Field label="Bairro" optional error={errors.district}>
              <Input value={data.district} onChange={(event) => setData('district', event.target.value)} />
            </Field>
            <Field label="Cidade" optional error={errors.city}>
              <Input value={data.city} onChange={(event) => setData('city', event.target.value)} />
            </Field>
            <Field label="UF" optional error={errors.state}>
              <Input value={data.state} onChange={(event) => setData('state', event.target.value)} maxLength={2} />
            </Field>
            <Field label="Administrador" optional className="xl:col-span-2" error={errors.administrator_name}>
              <Input value={data.administrator_name} onChange={(event) => setData('administrator_name', event.target.value)} />
            </Field>
            <Field label="Inicio do mandato" optional error={errors.mandate_start}>
              <Input type="date" value={data.mandate_start} onChange={(event) => setData('mandate_start', event.target.value)} />
            </Field>
            <Field label="Fim do mandato" optional error={errors.mandate_end}>
              <Input type="date" value={data.mandate_end} onChange={(event) => setData('mandate_end', event.target.value)} />
            </Field>
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/app/condominiums" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>{editing ? 'Salvar condominio' : 'Criar condominio'}</Button>
        </div>
      </form>
    </AppLayout>
  );
}

function toDateInput(value) {
  return value ? String(value).slice(0, 10) : '';
}
