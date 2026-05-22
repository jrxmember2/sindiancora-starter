import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Select } from '@/Components/Form';

export default function Form({ item }) {
  const editing = Boolean(item);
  const { data, setData, post, processing, errors, transform } = useForm({
    name: item?.name || '',
    document: item?.document || '',
    email: item?.email || '',
    phone: item?.phone || '',
    slug: item?.slug || '',
    status: item?.status || 'active',
    logo: null,
    remove_logo: false,
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

    transform((current) => (editing ? { ...current, _method: 'put' } : current));

    post(editing ? `/app/condominiums/${item.id}` : '/app/condominiums', {
      forceFormData: true,
      onFinish: () => transform((current) => current),
    });
  };

  return (
    <AppLayout title={editing ? 'Editar condomínio' : 'Novo condomínio'}>
      <Head title={editing ? 'Editar condomínio' : 'Novo condomínio'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Dados cadastrais"
            description="Base principal do condomínio dentro da empresa ativa. Se o CNPJ já existir em outra empresa, o sistema abrirá uma solicitação formal em vez de duplicar o cadastro."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome do condomínio" error={errors.name}>
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} />
            </Field>
            <Field label="Slug" optional hint="Se vazio, será gerado automaticamente." error={errors.slug}>
              <Input value={data.slug} onChange={(event) => setData('slug', event.target.value)} placeholder="residencial-anchieta" />
            </Field>
            <Field label="Documento" optional hint="Use o CNPJ oficial para evitar duplicidades e facilitar a governança entre síndicas." error={errors.document}>
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
          <CardHeader
            title="Logo e identidade"
            description="Use uma imagem leve para facilitar listagens, relatórios e exportações futuras."
          />

          <div className="grid gap-4 md:grid-cols-[220px_1fr]">
            <div className="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-4">
              {item?.logo_url && !data.remove_logo ? (
                <img src={item.logo_url} alt={`Logo de ${item.name}`} className="h-40 w-full rounded-2xl object-cover" />
              ) : (
                <div className="flex h-40 items-center justify-center rounded-2xl bg-white text-sm font-semibold text-slate-400">
                  Sem logo
                </div>
              )}
            </div>

            <div className="space-y-4">
              <Field label="Arquivo da logo" optional hint="PNG, JPG ou WEBP até 3 MB." error={errors.logo}>
                <input
                  type="file"
                  accept=".png,.jpg,.jpeg,.webp"
                  className="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-xl file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
                  onChange={(event) => setData('logo', event.target.files?.[0] || null)}
                />
              </Field>

              {item?.logo_url && (
                <Checkbox
                  checked={data.remove_logo}
                  onChange={(event) => setData('remove_logo', event.target.checked)}
                  label="Remover logo atual"
                  hint="Se marcar e não enviar outro arquivo, o condomínio ficará sem logo."
                />
              )}
            </div>
          </div>
        </Card>

        <Card>
          <CardHeader title="Endereço e gestão" description="Dados operacionais para contato, administradora e período de mandato." />

          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Field label="CEP" optional error={errors.cep}>
              <Input value={data.cep} onChange={(event) => setData('cep', event.target.value)} />
            </Field>
            <Field label="Rua" optional className="xl:col-span-2" error={errors.street}>
              <Input value={data.street} onChange={(event) => setData('street', event.target.value)} />
            </Field>
            <Field label="Número" optional error={errors.number}>
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
            <Field label="Administradora" optional className="xl:col-span-2" error={errors.administrator_name}>
              <Input value={data.administrator_name} onChange={(event) => setData('administrator_name', event.target.value)} />
            </Field>
            <Field label="Início do mandato" optional error={errors.mandate_start}>
              <Input type="date" value={data.mandate_start} onChange={(event) => setData('mandate_start', event.target.value)} />
            </Field>
            <Field label="Fim do mandato" optional error={errors.mandate_end}>
              <Input type="date" value={data.mandate_end} onChange={(event) => setData('mandate_end', event.target.value)} />
            </Field>
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/app/condominiums" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>{editing ? 'Salvar condomínio' : 'Criar condomínio'}</Button>
        </div>
      </form>
    </AppLayout>
  );
}

function toDateInput(value) {
  return value ? String(value).slice(0, 10) : '';
}
