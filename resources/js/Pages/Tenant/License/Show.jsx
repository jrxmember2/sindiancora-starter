import React from 'react';
import { Head } from '@inertiajs/react';
import { CalendarClock, CircleAlert, HardDrive, MessageSquareMore, ShieldCheck, Sparkles, Users, Building2 } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import { Card, CardHeader } from '@/Components/Card';

export default function Show({ license, statusSummary, usage, alerts }) {
  const moduleGroups = groupModules(license?.modules || []);

  return (
    <AppLayout title="Minha licença">
      <Head title="Minha licença" />

      <div className="grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
        <Card>
          <CardHeader
            title="Leitura contratual"
            description="Resumo do contrato ativo da empresa atual e do que pode ou não ser usado na plataforma."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <Metric label="Contrato" value={license?.contract_number || 'Não configurado'} icon={ShieldCheck} />
            <Metric label="Status da licença" value={statusSummary?.label || 'Sem licença'} icon={CircleAlert} badgeTone={statusTone(statusSummary?.code)} />
            <Metric label="Status financeiro" value={financialStatusLabel(license?.financial_status)} icon={CalendarClock} />
            <Metric label="Cobrança" value={billingTypeLabel(license?.billing_type)} icon={CalendarClock} />
            <Metric label="Início" value={formatDate(license?.starts_at)} icon={CalendarClock} />
            <Metric label="Fim" value={formatDate(license?.ends_at)} icon={CalendarClock} />
          </div>

          <div className="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <p className="text-sm font-semibold text-slate-500">Mensagem contratual</p>
            <p className="mt-2 text-sm leading-6 text-slate-700">{statusSummary?.message || 'Sem mensagem disponível.'}</p>
          </div>

          {license?.notes && (
            <div className="mt-4 rounded-3xl border border-slate-200 bg-white p-5">
              <p className="text-sm font-semibold text-slate-500">Observações comerciais</p>
              <p className="mt-2 text-sm leading-6 text-slate-700">{license.notes}</p>
            </div>
          )}
        </Card>

        <Card>
          <CardHeader
            title="Uso e limites"
            description="Acompanhamento do consumo contratado da empresa ativa."
          />

          <div className="space-y-5">
            <UsageMeter label="Condomínios ativos" metric={usage?.condominiums} icon={Building2} />
            <UsageMeter label="Usuários internos" metric={usage?.internal_users} icon={Users} />
            <UsageMeter label="Storage" metric={usage?.storage} icon={HardDrive} unit="MB" />
            <UsageMeter label="Instâncias WhatsApp" metric={usage?.whatsapp} icon={MessageSquareMore} />
            <UsageMeter label="Créditos de IA do mês" metric={usage?.ai} icon={Sparkles} />
          </div>

          <p className="mt-5 text-xs text-slate-400">
            Última sincronização registrada: {usage?.synced_at ? formatDateTime(usage.synced_at) : 'ainda não registrada'}
          </p>
        </Card>
      </div>

      <div className="mt-8 grid gap-5 xl:grid-cols-[0.95fr_1.05fr]">
        <Card>
          <CardHeader
            title="Alertas contratuais"
            description="Sinais de vencimento, limite e restrições da empresa atual."
          />

          <div className="space-y-3">
            {alerts?.length ? alerts.map((alert) => (
              <div key={`${alert.title}-${alert.message}`} className="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <div className="flex items-center gap-3">
                  <Badge tone={alert.tone || 'gray'}>{alert.title}</Badge>
                </div>
                <p className="mt-3 text-sm leading-6 text-slate-600">{alert.message}</p>
              </div>
            )) : (
              <div className="rounded-3xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">
                Nenhum alerta contratual no momento.
              </div>
            )}
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Módulos liberados"
            description="Catálogo do que está disponível para a empresa nesta licença."
          />

          <div className="space-y-5">
            {Object.keys(moduleGroups).length ? Object.entries(moduleGroups).map(([category, modules]) => (
              <div key={category}>
                <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">{category}</p>
                <div className="mt-3 flex flex-wrap gap-2">
                  {modules.map((module) => (
                    <Badge key={module.key} tone="blue">{module.name}</Badge>
                  ))}
                </div>
              </div>
            )) : (
              <div className="rounded-3xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">
                Nenhum módulo foi liberado nesta licença.
              </div>
            )}
          </div>
        </Card>
      </div>
    </AppLayout>
  );
}

function Metric({ label, value, icon: Icon, badgeTone }) {
  return (
    <div className="rounded-3xl border border-slate-200 bg-slate-50 p-5">
      <div className="flex items-start justify-between gap-3">
        <div>
          <p className="text-sm font-semibold text-slate-500">{label}</p>
          {badgeTone ? (
            <div className="mt-3"><Badge tone={badgeTone}>{value}</Badge></div>
          ) : (
            <p className="mt-3 text-2xl font-black tracking-tight text-slate-950">{value}</p>
          )}
        </div>
        <div className="rounded-2xl bg-slate-950 p-2.5 text-white">
          <Icon className="h-4 w-4" />
        </div>
      </div>
    </div>
  );
}

function UsageMeter({ label, metric, icon: Icon, unit = '' }) {
  const percent = metric?.percent || 0;
  const tone = percent >= 100 ? 'bg-rose-500' : percent >= 80 ? 'bg-amber-500' : 'bg-slate-950';
  const suffix = unit ? ` ${unit}` : '';

  return (
    <div>
      <div className="mb-2 flex items-center justify-between gap-3 text-sm">
        <div className="flex items-center gap-2 font-semibold text-slate-700">
          <Icon className="h-4 w-4 text-slate-500" />
          <span>{label}</span>
        </div>
        <span className="text-slate-500">
          {metric?.used ?? 0}{suffix} de {metric?.limit ?? 0}{suffix}
        </span>
      </div>
      <div className="h-3 overflow-hidden rounded-full bg-slate-100">
        <div className={`h-full rounded-full transition-all ${tone}`} style={{ width: `${Math.min(percent, 100)}%` }} />
      </div>
    </div>
  );
}

function groupModules(modules) {
  return modules.reduce((groups, module) => {
    const category = module.category || 'Outros';

    if (!groups[category]) {
      groups[category] = [];
    }

    groups[category].push(module);

    return groups;
  }, {});
}

function formatDate(value) {
  if (!value) {
    return 'Não informado';
  }

  const date = new Date(`${value}T00:00:00`);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(date);
}

function formatDateTime(value) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
}

function billingTypeLabel(value) {
  const labels = {
    monthly: 'Mensal',
    quarterly: 'Trimestral',
    yearly: 'Anual',
    custom: 'Personalizada',
  };

  return labels[value] || 'Não informado';
}

function financialStatusLabel(value) {
  const labels = {
    current: 'Em dia',
    due: 'A vencer',
    overdue: 'Em atraso',
    negotiated: 'Negociado',
    suspended: 'Suspenso',
    canceled: 'Cancelado',
  };

  return labels[value] || 'Não informado';
}

function statusTone(code) {
  if (['active', 'trial'].includes(code)) return 'green';
  if (['read_only', 'expired_read_only'].includes(code)) return 'yellow';
  return 'red';
}
