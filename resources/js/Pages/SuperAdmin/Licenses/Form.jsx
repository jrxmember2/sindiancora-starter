import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Field, Input, Select, Textarea } from '@/Components/Form';

export default function Form({ license, companies, modules, enabledModules = [] }) {
  const editing = Boolean(license);
  const { data, setData, post, put, processing } = useForm({
    company_id: license?.company_id || '',
    contract_number: license?.contract_number || '',
    status: license?.status || 'active',
    financial_status: license?.financial_status || 'current',
    billing_type: license?.billing_type || 'monthly',
    monthly_amount: license?.monthly_amount || 0,
    setup_amount: license?.setup_amount || 0,
    billing_day: license?.billing_day || 10,
    max_condominiums: license?.max_condominiums || 1,
    max_internal_users: license?.max_internal_users || 1,
    max_storage_mb: license?.max_storage_mb || 1024,
    max_whatsapp_instances: license?.max_whatsapp_instances || 0,
    monthly_ai_credits: license?.monthly_ai_credits || 0,
    allow_overage: Boolean(license?.allow_overage),
    block_new_records_on_limit: license?.block_new_records_on_limit ?? true,
    read_only_when_expired: license?.read_only_when_expired ?? true,
    auto_suspend_when_overdue: Boolean(license?.auto_suspend_when_overdue),
    notes: license?.notes || '',
    modules: enabledModules,
  });

  const toggleModule = (id) => setData('modules', data.modules.includes(id) ? data.modules.filter((item) => item !== id) : [...data.modules, id]);

  const submit = (e) => {
    e.preventDefault();
    editing ? put(`/superadmin/licenses/${license.id}`) : post('/superadmin/licenses');
  };

  return (
    <AppLayout title={editing ? 'Editar licença' : 'Nova licença'}>
      <Head title="Licença" />
      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader title="Contrato e limites" description="Defina a licença personalizada do cliente." />
          <div className="grid gap-4 md:grid-cols-3">
            <Field label="Empresa"><Select value={data.company_id} onChange={(e) => setData('company_id', e.target.value)}><option value="">Selecione</option>{companies.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}</Select></Field>
            <Field label="Contrato nº"><Input value={data.contract_number} onChange={(e) => setData('contract_number', e.target.value)} /></Field>
            <Field label="Status"><Select value={data.status} onChange={(e) => setData('status', e.target.value)}><option value="active">Ativa</option><option value="trial">Teste</option><option value="pending">Pendente</option><option value="suspended">Suspensa</option><option value="blocked">Bloqueada</option><option value="read_only">Somente leitura</option><option value="canceled">Cancelada</option></Select></Field>
            <Field label="Valor mensal"><Input type="number" step="0.01" value={data.monthly_amount} onChange={(e) => setData('monthly_amount', e.target.value)} /></Field>
            <Field label="Condomínios"><Input type="number" value={data.max_condominiums} onChange={(e) => setData('max_condominiums', e.target.value)} /></Field>
            <Field label="Usuários internos"><Input type="number" value={data.max_internal_users} onChange={(e) => setData('max_internal_users', e.target.value)} /></Field>
          </div>
        </Card>

        <Card>
          <CardHeader title="Módulos liberados" description="Marque exatamente o que foi contratado." />
          <div className="grid gap-3 md:grid-cols-3 xl:grid-cols-4">
            {modules.map((module) => (
              <label key={module.id} className="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:bg-white">
                <input type="checkbox" checked={data.modules.includes(module.id)} onChange={() => toggleModule(module.id)} className="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                <span><span className="block text-sm font-bold text-slate-900">{module.name}</span><span className="text-xs text-slate-500">{module.category}</span></span>
              </label>
            ))}
          </div>
        </Card>

        <Card>
          <CardHeader title="Regras contratuais" />
          <div className="grid gap-4 md:grid-cols-2">
            {[['allow_overage', 'Permitir excedente'], ['block_new_records_on_limit', 'Bloquear novos cadastros ao atingir limite'], ['read_only_when_expired', 'Somente leitura ao vencer'], ['auto_suspend_when_overdue', 'Suspender automaticamente em atraso']].map(([key, label]) => (
              <label key={key} className="flex items-center gap-3 rounded-2xl border border-slate-200 p-4 text-sm font-semibold"><input type="checkbox" checked={data[key]} onChange={(e) => setData(key, e.target.checked)} className="rounded border-slate-300 text-blue-600" /> {label}</label>
            ))}
          </div>
          <div className="mt-4"><Field label="Observações"><Textarea value={data.notes} onChange={(e) => setData('notes', e.target.value)} /></Field></div>
          <div className="mt-6 flex justify-end"><Button disabled={processing}>{editing ? 'Salvar licença' : 'Criar licença'}</Button></div>
        </Card>
      </form>
    </AppLayout>
  );
}
