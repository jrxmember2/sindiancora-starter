import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import { Card, CardHeader } from '@/Components/Card';
import { Checkbox, CheckboxCard, Field, Input, Select, Textarea } from '@/Components/Form';

const statusOptions = ['active', 'trial', 'pending', 'expired', 'suspended', 'blocked', 'read_only', 'canceled'];
const financialStatusOptions = ['current', 'due', 'overdue', 'negotiated', 'suspended', 'canceled'];
const billingTypeOptions = ['monthly', 'quarterly', 'yearly', 'custom'];

export default function Form({ license, companies, modules, enabledModules = [] }) {
  const editing = Boolean(license);
  const { data, setData, post, put, processing, errors } = useForm({
    company_id: license?.company_id || '',
    contract_number: license?.contract_number || '',
    status: license?.status || 'active',
    financial_status: license?.financial_status || 'current',
    billing_type: license?.billing_type || 'monthly',
    monthly_amount: license?.monthly_amount || 0,
    setup_amount: license?.setup_amount || 0,
    billing_day: license?.billing_day || 10,
    starts_at: toDateInput(license?.starts_at),
    ends_at: toDateInput(license?.ends_at),
    renews_at: toDateInput(license?.renews_at),
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

  const toggleModule = (id) => {
    setData(
      'modules',
      data.modules.includes(id) ? data.modules.filter((item) => item !== id) : [...data.modules, id]
    );
  };

  const submit = (event) => {
    event.preventDefault();

    if (editing) {
      put(`/superadmin/licenses/${license.id}`);
      return;
    }

    post('/superadmin/licenses');
  };

  return (
    <AppLayout title={editing ? 'Editar licenca' : 'Nova licenca'}>
      <Head title={editing ? 'Editar licenca' : 'Nova licenca'} />

      <form onSubmit={submit} className="space-y-6">
        <Card>
          <CardHeader
            title="Contrato e ciclo comercial"
            description="Defina empresa, status, cobranca e datas de referencia da licenca."
          />

          <div className="grid gap-4 md:grid-cols-3">
            <Field label="Empresa" error={errors.company_id}>
              <Select value={data.company_id} onChange={(event) => setData('company_id', event.target.value)}>
                <option value="">Selecione uma empresa</option>
                {companies.map((company) => (
                  <option key={company.id} value={company.id}>{company.name}</option>
                ))}
              </Select>
            </Field>

            <Field label="Contrato" error={errors.contract_number}>
              <Input value={data.contract_number} onChange={(event) => setData('contract_number', event.target.value)} placeholder="CON-2026-001" />
            </Field>

            <Field label="Status da licenca" error={errors.status}>
              <Select value={data.status} onChange={(event) => setData('status', event.target.value)}>
                {statusOptions.map((status) => <option key={status} value={status}>{status}</option>)}
              </Select>
            </Field>

            <Field label="Status financeiro" error={errors.financial_status}>
              <Select value={data.financial_status} onChange={(event) => setData('financial_status', event.target.value)}>
                {financialStatusOptions.map((status) => <option key={status} value={status}>{status}</option>)}
              </Select>
            </Field>

            <Field label="Tipo de cobranca" error={errors.billing_type}>
              <Select value={data.billing_type} onChange={(event) => setData('billing_type', event.target.value)}>
                {billingTypeOptions.map((type) => <option key={type} value={type}>{type}</option>)}
              </Select>
            </Field>

            <Field label="Dia de cobranca" optional error={errors.billing_day}>
              <Input type="number" min="1" max="31" value={data.billing_day} onChange={(event) => setData('billing_day', event.target.value)} />
            </Field>

            <Field label="Inicio" optional error={errors.starts_at}>
              <Input type="date" value={data.starts_at} onChange={(event) => setData('starts_at', event.target.value)} />
            </Field>

            <Field label="Fim" optional error={errors.ends_at}>
              <Input type="date" value={data.ends_at} onChange={(event) => setData('ends_at', event.target.value)} />
            </Field>

            <Field label="Renovacao" optional error={errors.renews_at}>
              <Input type="date" value={data.renews_at} onChange={(event) => setData('renews_at', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Limites contratados"
            description="Esses limites alimentam o bloqueio operacional da plataforma."
          />

          <div className="grid gap-4 md:grid-cols-3">
            <Field label="Condominios" error={errors.max_condominiums}>
              <Input type="number" min="0" value={data.max_condominiums} onChange={(event) => setData('max_condominiums', event.target.value)} />
            </Field>
            <Field label="Usuarios internos" error={errors.max_internal_users}>
              <Input type="number" min="0" value={data.max_internal_users} onChange={(event) => setData('max_internal_users', event.target.value)} />
            </Field>
            <Field label="Storage (MB)" error={errors.max_storage_mb}>
              <Input type="number" min="0" value={data.max_storage_mb} onChange={(event) => setData('max_storage_mb', event.target.value)} />
            </Field>
            <Field label="Instancias WhatsApp" error={errors.max_whatsapp_instances}>
              <Input type="number" min="0" value={data.max_whatsapp_instances} onChange={(event) => setData('max_whatsapp_instances', event.target.value)} />
            </Field>
            <Field label="Creditos IA por mes" error={errors.monthly_ai_credits}>
              <Input type="number" min="0" value={data.monthly_ai_credits} onChange={(event) => setData('monthly_ai_credits', event.target.value)} />
            </Field>
            <Field label="Valor mensal" optional error={errors.monthly_amount}>
              <Input type="number" step="0.01" min="0" value={data.monthly_amount} onChange={(event) => setData('monthly_amount', event.target.value)} />
            </Field>
            <Field label="Valor de implantacao" optional error={errors.setup_amount}>
              <Input type="number" step="0.01" min="0" value={data.setup_amount} onChange={(event) => setData('setup_amount', event.target.value)} />
            </Field>
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Modulos liberados"
            description="Marque exatamente os recursos contratados para este cliente."
          />

          <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            {modules.map((module) => (
              <CheckboxCard
                key={module.id}
                checked={data.modules.includes(module.id)}
                onChange={() => toggleModule(module.id)}
                label={module.name}
                hint={`${module.key} | ${module.category}`}
              />
            ))}
          </div>
          {errors.modules && <p className="mt-3 text-xs font-medium text-rose-600">{errors.modules}</p>}
        </Card>

        <Card>
          <CardHeader
            title="Regras contratuais"
            description="Controles de bloqueio e tolerancia quando a licenca encostar nos limites."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Checkbox
              checked={data.allow_overage}
              onChange={(event) => setData('allow_overage', event.target.checked)}
              label="Permitir excedente"
              hint="Permite seguir operando acima do limite quando os demais bloqueios nao impedirem."
            />
            <Checkbox
              checked={data.block_new_records_on_limit}
              onChange={(event) => setData('block_new_records_on_limit', event.target.checked)}
              label="Bloquear novos cadastros ao atingir limite"
            />
            <Checkbox
              checked={data.read_only_when_expired}
              onChange={(event) => setData('read_only_when_expired', event.target.checked)}
              label="Modo somente leitura ao vencer"
            />
            <Checkbox
              checked={data.auto_suspend_when_overdue}
              onChange={(event) => setData('auto_suspend_when_overdue', event.target.checked)}
              label="Suspender automaticamente em atraso"
            />
          </div>

          <div className="mt-4">
            <Field label="Observacoes comerciais" optional error={errors.notes}>
              <Textarea value={data.notes} onChange={(event) => setData('notes', event.target.value)} placeholder="Observacoes internas do contrato e da negociacao." />
            </Field>
          </div>
        </Card>

        <div className="flex flex-wrap justify-end gap-3">
          <Button href="/superadmin/licenses" variant="soft">Voltar</Button>
          <Button type="submit" disabled={processing}>
            {editing ? 'Salvar licenca' : 'Criar licenca'}
          </Button>
        </div>
      </form>
    </AppLayout>
  );
}

function toDateInput(value) {
  if (!value) {
    return '';
  }

  return String(value).slice(0, 10);
}
