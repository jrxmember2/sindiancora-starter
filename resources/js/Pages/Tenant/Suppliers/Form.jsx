import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Textarea } from '@/Components/Form';

export default function Form({ item }) {
  const editing = Boolean(item);
  const { data, setData, post, put, processing, errors } = useForm({
    name: item?.name || '',
    document: item?.document || '',
    email: item?.email || '',
    responsible_name: item?.responsible_name || '',
    mobile: item?.mobile || '',
    phone: item?.phone || '',
    website: item?.website || '',
    rating: item?.rating || '',
    cep: item?.cep || '',
    street: item?.street || '',
    number: item?.number || '',
    complement: item?.complement || '',
    district: item?.district || '',
    city: item?.city || '',
    state: item?.state || '',
    country: item?.country || 'Brasil',
    notes: item?.notes || '',
    active: item?.active ?? true,
  });

  const submit = (event) => {
    event.preventDefault();
    if (editing) {
      put(`/app/suppliers/${item.id}`);
      return;
    }

    post('/app/suppliers');
  };

  return (
    <AppLayout title={editing ? 'Editar fornecedor' : 'Novo fornecedor'}>
      <Head title={editing ? 'Editar fornecedor' : 'Novo fornecedor'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader title="Dados principais" description="Cadastro base do fornecedor para uso nos modulos operacionais." />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome" error={errors.name}>
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} />
            </Field>
            <Field label="Documento" optional error={errors.document}>
              <Input value={data.document} onChange={(event) => setData('document', event.target.value)} />
            </Field>
            <Field label="E-mail" optional error={errors.email}>
              <Input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} />
            </Field>
            <Field label="Responsavel" optional error={errors.responsible_name}>
              <Input value={data.responsible_name} onChange={(event) => setData('responsible_name', event.target.value)} />
            </Field>
            <Field label="Celular" optional error={errors.mobile}>
              <Input value={data.mobile} onChange={(event) => setData('mobile', event.target.value)} />
            </Field>
            <Field label="Telefone" optional error={errors.phone}>
              <Input value={data.phone} onChange={(event) => setData('phone', event.target.value)} />
            </Field>
            <Field label="Site" optional error={errors.website}>
              <Input value={data.website} onChange={(event) => setData('website', event.target.value)} placeholder="https://..." />
            </Field>
            <Field label="Avaliacao" optional error={errors.rating}>
              <Input type="number" min="0" max="5" value={data.rating} onChange={(event) => setData('rating', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader title="Endereco e observacoes" description="Informacoes de localizacao e notas operacionais." />

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
            <Field label="Pais" optional error={errors.country}>
              <Input value={data.country} onChange={(event) => setData('country', event.target.value)} />
            </Field>
          </div>

          <div className="mt-4 space-y-4">
            <Field label="Observacoes" optional error={errors.notes}>
              <Textarea value={data.notes} onChange={(event) => setData('notes', event.target.value)} />
            </Field>
            <Checkbox
              checked={data.active}
              onChange={(event) => setData('active', event.target.checked)}
              label="Fornecedor ativo"
              hint="Quando inativo, deixa de aparecer como opcao operacional."
            />
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/app/suppliers" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>{editing ? 'Salvar fornecedor' : 'Criar fornecedor'}</Button>
        </div>
      </form>
    </AppLayout>
  );
}
