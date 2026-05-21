import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, LockKeyhole } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import Button from '@/Components/Button';
import { Field, Input } from '@/Components/Form';

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
          <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950">Acesse sua operacao</h1>
          <p className="mt-2 text-sm leading-6 text-slate-500">
            Entre para gerenciar empresas, licencas, condominios e chamados.
          </p>
        </div>

        <div className="rounded-2xl bg-slate-950 p-3 text-white">
          <LockKeyhole className="h-5 w-5" />
        </div>
      </div>

      <form onSubmit={submit} className="space-y-4">
        <Field label="E-mail" error={errors.email}>
          <Input
            type="email"
            value={data.email}
            onChange={(event) => setData('email', event.target.value)}
            autoFocus
          />
        </Field>

        <Field label="Senha" error={errors.password}>
          <Input
            type="password"
            value={data.password}
            onChange={(event) => setData('password', event.target.value)}
          />
        </Field>

        <label className="flex items-center gap-2 text-sm font-medium text-slate-600">
          <input
            type="checkbox"
            checked={data.remember}
            onChange={(event) => setData('remember', event.target.checked)}
            className="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
          />
          Manter conectado
        </label>

        <Button type="submit" disabled={processing} className="h-12 w-full rounded-2xl">
          Entrar <ArrowRight className="h-4 w-4" />
        </Button>
      </form>
    </AuthLayout>
  );
}
