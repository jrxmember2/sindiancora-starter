import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { KeyRound } from 'lucide-react';
import AuthLayout from '@/Layouts/AuthLayout';
import Button from '@/Components/Button';
import { Field, Input } from '@/Components/Form';

export default function FirstAccessPassword() {
  const { data, setData, put, processing, errors } = useForm({
    password: '',
    password_confirmation: '',
  });

  const submit = (event) => {
    event.preventDefault();
    put('/primeiro-acesso');
  };

  return (
    <AuthLayout>
      <Head title="Primeiro acesso" />

      <div className="mb-8 flex items-start justify-between gap-4">
        <div>
          <p className="text-sm font-bold text-blue-600">Primeiro acesso</p>
          <h1 className="mt-2 text-3xl font-black tracking-tight text-slate-950">Troque sua senha inicial</h1>
          <p className="mt-2 text-sm leading-6 text-slate-500">
            Seu acesso foi criado pela plataforma. Defina agora uma nova senha para liberar a operação.
          </p>
        </div>

        <div className="rounded-2xl bg-slate-950 p-3 text-white">
          <KeyRound className="h-5 w-5" />
        </div>
      </div>

      <form onSubmit={submit} className="space-y-4">
        <Field label="Nova senha" error={errors.password}>
          <Input
            type="password"
            value={data.password}
            onChange={(event) => setData('password', event.target.value)}
            autoFocus
            autoComplete="new-password"
            placeholder="Digite sua nova senha"
          />
        </Field>

        <Field label="Confirmar nova senha" error={errors.password_confirmation}>
          <Input
            type="password"
            value={data.password_confirmation}
            onChange={(event) => setData('password_confirmation', event.target.value)}
            autoComplete="new-password"
            placeholder="Repita a nova senha"
          />
        </Field>

        <Button type="submit" size="lg" disabled={processing} className="w-full">
          Atualizar senha e entrar
        </Button>
      </form>
    </AuthLayout>
  );
}
