import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, CheckboxCard, Field, Input, Select } from '@/Components/Form';

export default function Form({ item, roleOptions = [], condominiums = [] }) {
  const editing = Boolean(item);
  const { data, setData, post, put, processing, errors } = useForm({
    name: item?.name || '',
    email: item?.email || '',
    phone: item?.phone || '',
    password: '',
    password_confirmation: '',
    role: item?.role || 'operacional',
    status: item?.status || 'active',
    can_access_whatsapp: Boolean(item?.can_access_whatsapp),
    only_responsible_issues: Boolean(item?.only_responsible_issues),
    condominium_ids: item?.condominium_ids || [],
  });

  const selectedRole = roleOptions.find((role) => role.value === data.role);

  const submit = (event) => {
    event.preventDefault();

    if (editing) {
      put(`/app/users/${item.id}`);
      return;
    }

    post('/app/users');
  };

  const toggleCondominium = (id) => {
    setData(
      'condominium_ids',
      data.condominium_ids.includes(id)
        ? data.condominium_ids.filter((itemId) => itemId !== id)
        : [...data.condominium_ids, id]
    );
  };

  return (
    <AppLayout title={editing ? 'Editar usuário' : 'Novo usuário'}>
      <Head title={editing ? 'Editar usuário' : 'Novo usuário'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Identidade e acesso"
            description="Crie um usuário novo ou vincule um e-mail já existente à empresa atual."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Nome" error={errors.name}>
              <Input value={data.name} onChange={(event) => setData('name', event.target.value)} />
            </Field>
            <Field
              label="E-mail"
              hint="Se o e-mail já existir na plataforma, o usuário será apenas vinculado a esta empresa."
              error={errors.email}
            >
              <Input type="email" value={data.email} onChange={(event) => setData('email', event.target.value)} />
            </Field>
            <Field label="Telefone" optional error={errors.phone}>
              <Input value={data.phone} onChange={(event) => setData('phone', event.target.value)} />
            </Field>
            <Field
              label={editing ? 'Nova senha' : 'Senha'}
              optional={editing}
              hint={editing ? 'Preencha apenas se quiser redefinir a senha deste usuário.' : 'Obrigatória para criar um usuário novo.'}
              error={errors.password}
            >
              <Input type="password" value={data.password} onChange={(event) => setData('password', event.target.value)} />
            </Field>
            <Field label="Confirmar senha" optional={editing} error={errors.password_confirmation}>
              <Input type="password" value={data.password_confirmation} onChange={(event) => setData('password_confirmation', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Papel e regras operacionais"
            description={selectedRole?.description || 'Defina o papel do usuário e o comportamento operacional deste vínculo.'}
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Field label="Papel" error={errors.role}>
              <Select value={data.role} onChange={(event) => setData('role', event.target.value)}>
                {roleOptions.map((role) => (
                  <option key={role.value} value={role.value}>{role.label}</option>
                ))}
              </Select>
            </Field>

            <Field label="Status" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </Select>
            </Field>
          </div>

          <div className="mt-4 grid gap-4 md:grid-cols-2">
            <Checkbox
              checked={data.can_access_whatsapp}
              onChange={(event) => setData('can_access_whatsapp', event.target.checked)}
              label="Pode acessar WhatsApp"
              hint="Prepara o vínculo para a fase de integração operacional com mensagens."
            />
            <Checkbox
              checked={data.only_responsible_issues}
              onChange={(event) => setData('only_responsible_issues', event.target.checked)}
              label="Visualiza apenas chamados atribuídos"
              hint="Útil para perfis operacionais com atuação mais enxuta."
            />
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Escopo por condomínio"
            description="Se nenhum condomínio for marcado, o usuário terá acesso a todos os condomínios ativos da empresa."
          />

          <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            {condominiums.map((condominium) => (
              <CheckboxCard
                key={condominium.id}
                checked={data.condominium_ids.includes(condominium.id)}
                onChange={() => toggleCondominium(condominium.id)}
                label={condominium.name}
                hint="Vínculo operacional"
              />
            ))}
          </div>

          {errors.condominium_ids && <p className="mt-3 text-xs font-medium text-rose-600">{errors.condominium_ids}</p>}
          {errors['condominium_ids.*'] && <p className="mt-3 text-xs font-medium text-rose-600">{errors['condominium_ids.*']}</p>}
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/app/users" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>Salvar usuário</Button>
        </div>
      </form>
    </AppLayout>
  );
}
