import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, LockKeyhole } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import Button from '@/Components/Button';
import { Checkbox, Field, Input } from '@/Components/Form';

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: true,
  });

  const submit = (event) => {
    event.preventDefault();
    post('/login');
  };

  return (
    <AuthLayout>
      <Head title="Entrar" />

      <div className="mb-8 flex items-start justify-between gap-4">
        <div>
          <p className="text-sm font-bold text-blue-600">Bem-vindo</p>
          <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950">Acesse sua operação</h1>
          <p className="mt-2 text-sm leading-6 text-slate-500">
            Entre para gerenciar empresas, licenças, condomínios e chamados.
          </p>
        </div>

        <div className="rounded-2xl bg-slate-950 p-3 text-white">
          <LockKeyhole className="h-5 w-5" />
        </div>
      </div>

      <form onSubmit={submit} className="space-y-4">
        <Field
          label="E-mail"
          hint="Use o endereço associado ao seu usuário interno ou superadmin."
          error={errors.email}
        >
          <Input
            type="email"
            value={data.email}
            onChange={(event) => setData('email', event.target.value)}
            autoFocus
            autoComplete="email"
            placeholder="voce@empresa.com.br"
          />
        </Field>

        <Field label="Senha" error={errors.password}>
          <Input
            type="password"
            value={data.password}
            onChange={(event) => setData('password', event.target.value)}
            autoComplete="current-password"
            placeholder="Digite sua senha"
          />
        </Field>

        <Checkbox
          checked={data.remember}
          onChange={(event) => setData('remember', event.target.checked)}
          label="Manter conectado"
          hint="Recomendado apenas em equipamentos confiáveis."
        />

        <Button type="submit" size="lg" disabled={processing} className="w-full">
          Entrar <ArrowRight className="h-4 w-4" />
        </Button>
      </form>
    </AuthLayout>
  );
}
