import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import StatCard from '@/Components/StatCard';
import { Card, CardHeader } from '@/Components/Card';
import { Building2, ShieldCheck, TicketCheck, TimerReset } from 'lucide-react';

export default function Dashboard({ stats }) {
  const { tenant } = usePage().props;
  const usage = tenant?.licenseUsage;

  return (
    <AppLayout title="Dashboard">
      <Head title="Dashboard" />
      <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Empresas" value={stats.companies ?? '—'} icon={Building2} hint="Visível para Superadmin" />
        <StatCard label="Chamados abertos" value={stats.issues_open ?? 0} icon={TicketCheck} hint="Pendentes e em andamento" />
        <StatCard label="Chamados atrasados" value={stats.issues_late ?? 0} icon={TimerReset} hint="Prazo anterior à data atual" />
        <StatCard label="Licença" value={tenant?.currentCompany ? 'Ativa' : 'Geral'} icon={ShieldCheck} hint="Controle contratual por empresa" />
      </div>

      <div className="mt-8 grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
        <Card>
          <CardHeader title="Operação do dia" description="Aqui entram chamados a vencer, documentos vencendo e pendências críticas." />
          <div className="grid gap-3 sm:grid-cols-3">
            {['Chamados a vencer', 'Sem prazo', 'Aguardando assembleia'].map((label) => (
              <div key={label} className="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p className="text-sm font-semibold text-slate-500">{label}</p>
                <p className="mt-3 text-3xl font-black text-slate-950">0</p>
              </div>
            ))}
          </div>
        </Card>

        <Card>
          <CardHeader title="Uso da licença" description="Acompanhamento dos limites contratados desta empresa." />
          <div className="space-y-5">
            <Meter label="Condomínios" used={usage?.condominiums?.used || 0} limit={usage?.condominiums?.limit || 0} />
            <Meter label="Usuários internos" used={usage?.internal_users?.used || 0} limit={usage?.internal_users?.limit || 0} />
          </div>
        </Card>
      </div>
    </AppLayout>
  );
}

function Meter({ label, used, limit }) {
  const pct = limit > 0 ? Math.min(100, Math.round((used / limit) * 100)) : 0;

  return (
    <div>
      <div className="mb-2 flex items-center justify-between text-sm">
        <span className="font-semibold text-slate-700">{label}</span>
        <span className="text-slate-500">{used} de {limit}</span>
      </div>
      <div className="h-3 overflow-hidden rounded-full bg-slate-100">
        <div className="h-full rounded-full bg-slate-950 transition-all" style={{ width: `${pct}%` }} />
      </div>
    </div>
  );
}
