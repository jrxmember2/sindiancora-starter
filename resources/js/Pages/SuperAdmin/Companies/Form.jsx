import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Select } from '@/Components/Form';

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
    primary_user_name: company?.primary_user_name || '',
    primary_user_email: company?.primary_user_email || '',
    primary_user_phone: company?.primary_user_phone || '',
    primary_user_password: '',
    primary_user_password_confirmation: '',
    primary_user_force_password_reset: company?.primary_user_force_password_reset ?? true,
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
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} placeholder="Serratech Gestão Condominial" />
            </Field>
            <Field label="Slug" hint="Usado como identificador amigável do tenant." error={errors.slug}>
              <Input value={data.slug} onChange={(event) => setData('slug', event.target.value)} placeholder="serratech-gestao" />
            </Field>
            <Field label="Documento" optional error={errors.document}>
              <Input value={data.document} onChange={(event) => setData('document', event.target.value)} placeholder="CNPJ ou CPF" />
            </Field>
            <Field label="E-mail principal da empresa" optional error={errors.email}>
              <Input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} placeholder="contato@empresa.com.br" />
            </Field>
            <Field label="Telefone" optional error={errors.phone}>
              <Input value={data.phone} onChange={(event) => setData('phone', event.target.value)} placeholder="(00) 00000-0000" />
            </Field>
            <Field label="Responsável comercial" optional error={errors.responsible_name}>
              <Input value={data.responsible_name} onChange={(event) => setData('responsible_name', event.target.value)} placeholder="Nome da pessoa responsável" />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Admin master da empresa"
            description="Esse usuário será o ponto inicial do cliente dentro do tenant. Ele administra apenas a empresa dele, nunca a plataforma."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome do admin master" error={errors.primary_user_name}>
              <Input value={data.primary_user_name} onChange={(event) => setData('primary_user_name', event.target.value)} placeholder="Andressa Silva" />
            </Field>
            <Field label="E-mail de acesso" error={errors.primary_user_email}>
              <Input type="email" value={data.primary_user_email} onChange={(event) => setData('primary_user_email', event.target.value)} placeholder="andressa@empresa.com.br" />
            </Field>
            <Field label="Telefone" optional error={errors.primary_user_phone}>
              <Input value={data.primary_user_phone} onChange={(event) => setData('primary_user_phone', event.target.value)} placeholder="(00) 00000-0000" />
            </Field>
            <Field
              label={editing ? 'Nova senha inicial' : 'Senha inicial'}
              hint={editing ? 'Preencha apenas se quiser redefinir a senha inicial do admin master.' : 'A senha será entregue ao cliente no onboarding comercial.'}
              error={errors.primary_user_password}
            >
              <Input
                type="password"
                value={data.primary_user_password}
                onChange={(event) => setData('primary_user_password', event.target.value)}
                placeholder="Digite a senha inicial"
              />
            </Field>
            <Field label="Confirmar senha" error={errors.primary_user_password_confirmation}>
              <Input
                type="password"
                value={data.primary_user_password_confirmation}
                onChange={(event) => setData('primary_user_password_confirmation', event.target.value)}
                placeholder="Repita a senha"
              />
            </Field>
          </div>

          <div className="mt-4">
            <Checkbox
              checked={data.primary_user_force_password_reset}
              onChange={(event) => setData('primary_user_force_password_reset', event.target.checked)}
              label="Exigir troca de senha no primeiro acesso"
              hint="Recomendado para todo admin master criado ou resetado pelo superadmin."
            />
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Status e identidade visual"
            description="As cores serão usadas nas próximas fases para personalização visual do tenant."
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
            <Field label="Cor secundária" optional error={errors.secondary_color}>
              <Input value={data.secondary_color} onChange={(event) => setData('secondary_color', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card className="bg-slate-950 text-white">
          <CardHeader
            title="Próximo passo comercial"
            description="Depois de salvar a empresa, finalize ou revise a licença contratual na área de Licenças para liberar módulos e limites."
          />
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/superadmin/companies" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>
            {editing ? 'Salvar alterações' : 'Criar empresa e admin master'}
          </Button>
        </div>
      </form>
    </AppLayout>
  );
}
