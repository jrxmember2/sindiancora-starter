import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, Field, Input, Select } from '@/Components/Form';

export default function Form({ item }) {
  const editing = Boolean(item);
  const { data, setData, post, put, processing, errors } = useForm({
    name: item?.name || '',
    email: item?.email || '',
    phone: item?.phone || '',
    status: item?.status || 'active',
    password: '',
    password_confirmation: '',
    must_change_password: item?.must_change_password ?? true,
  });

  const submit = (event) => {
    event.preventDefault();

    if (editing) {
      put(`/superadmin/platform-users/${item.id}`);
      return;
    }

    post('/superadmin/platform-users');
  };

  return (
    <AppLayout title={editing ? 'Editar usuário da plataforma' : 'Novo usuário da plataforma'}>
      <Head title={editing ? 'Editar usuário da plataforma' : 'Novo usuário da plataforma'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Acesso de superadmin"
            description="Esse cadastro é exclusivo para a equipe da plataforma e não se mistura com usuários internos dos clientes."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome" error={errors.name}>
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} placeholder="Nome do colaborador" />
            </Field>
            <Field label="E-mail" error={errors.email}>
              <Input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} placeholder="email@serratech.tec.br" />
            </Field>
            <Field label="Telefone" optional error={errors.phone}>
              <Input value={data.phone} onChange={(event) => setData('phone', event.target.value)} placeholder="(00) 00000-0000" />
            </Field>
            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </Select>
            </Field>
            <Field label={editing ? 'Nova senha' : 'Senha inicial'} error={errors.password}>
              <Input type="password" value={data.password} onChange={(event) => setData('password', event.target.value)} placeholder="Digite a senha" />
            </Field>
            <Field label="Confirmar senha" error={errors.password_confirmation}>
              <Input type="password" value={data.password_confirmation} onChange={(event) => setData('password_confirmation', event.target.value)} placeholder="Repita a senha" />
            </Field>
          </div>

          <div className="mt-4">
            <Checkbox
              checked={data.must_change_password}
              onChange={(event) => setData('must_change_password', event.target.checked)}
              label="Exigir troca de senha no primeiro acesso"
              hint="Use esta opção quando quiser que o colaborador redefina a senha inicial por conta própria."
            />
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/superadmin/platform-users" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>
            {editing ? 'Salvar alterações' : 'Criar usuário da plataforma'}
          </Button>
        </div>
      </form>
    </AppLayout>
  );
}
