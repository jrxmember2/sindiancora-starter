import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Building2, Clock3, TicketCheck, TimerReset } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Button from '@/Components/Button';
import StatCard from '@/Components/StatCard';
import { Card, CardHeader } from '@/Components/Card';

export default function Dashboard({ stats }) {
  const { tenant, auth } = usePage().props;
  const usage = tenant?.licenseUsage;
  const isSuperadmin = auth?.user?.is_superadmin;

  return (
    <AppLayout title="Dashboard">
      <Head title="Dashboard" />

      <section className="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <StatCard
          label="Empresas"
          value={isSuperadmin ? stats.companies ?? 0 : tenant?.companies?.length ?? 0}
          icon={Building2}
          hint={isSuperadmin ? 'Visivel para Superadmin' : 'Empresas vinculadas ao seu usuario'}
        />
        <StatCard
          label="Chamados abertos"
          value={stats.issues_open ?? 0}
          icon={TicketCheck}
          hint="Pendentes e em andamento"
        />
        <StatCard
          label="Chamados atrasados"
          value={stats.issues_late ?? 0}
          icon={TimerReset}
          hint="Prazo anterior a data atual"
        />
        <StatCard
          label="Documentos vencendo"
          value={stats.documents_due ?? 0}
          icon={Clock3}
          hint="Janela dos proximos 30 dias"
        />
      </section>

      <section className="mt-8 grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
        <Card>
          <CardHeader
            title="Painel de arranque"
            description="Atalhos para os fluxos que mais importam no dia a dia."
          />

          <div className="grid gap-4 md:grid-cols-2">
            <QuickAction
              href="/app/issues"
              title="Gerenciar chamados"
              description="Acompanhe demandas, prioridades e prazos operacionais."
            />
            <QuickAction
              href="/app/condominiums"
              title="Atualizar condominios"
              description="Mantenha a base ativa e alinhada aos limites da licenca."
            />
            <QuickAction
              href="/app/documents"
              title="Organizar documentos"
              description="Consolide vigencias, tipos e disponibilidade futura."
            />
            {isSuperadmin && (
              <QuickAction
                href="/superadmin/licenses"
                title="Revisar licencas"
                description="Ajuste contratos, modulos liberados e limites por cliente."
              />
            )}
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Uso da licenca"
            description="Leitura rapida do contrato ativo da empresa selecionada."
          />

          <div className="space-y-5">
            <Meter label="Condominios ativos" used={usage?.condominiums?.used || 0} limit={usage?.condominiums?.limit || 0} />
            <Meter label="Usuarios internos" used={usage?.internal_users?.used || 0} limit={usage?.internal_users?.limit || 0} />
          </div>

          <div className="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4">
            <div className="flex items-start justify-between gap-3">
              <div>
                <p className="text-sm font-semibold text-slate-500">Contexto atual</p>
                <p className="mt-2 text-lg font-black tracking-tight text-slate-950">
                  {tenant?.currentCompany?.name || 'Sem empresa selecionada'}
                </p>
                <p className="mt-1 text-sm leading-6 text-slate-500">
                  {tenant?.currentCompany
                    ? 'Os dados exibidos no painel respeitam a empresa ativa da sessao.'
                    : 'Selecione uma empresa para ver os dados operacionais e de licenciamento.'}
                </p>
              </div>

              {isSuperadmin && (
                <Button href="/superadmin/versions" variant="soft" size="sm">
                  Ver release <ArrowRight className="h-4 w-4" />
                </Button>
              )}
            </div>
          </div>
        </Card>
      </section>
    </AppLayout>
  );
}

function QuickAction({ href, title, description }) {
  return (
    <Link href={href} className="group rounded-3xl border border-slate-200 bg-white p-5 transition hover:border-slate-300 hover:bg-slate-50">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h3 className="text-base font-bold text-slate-950">{title}</h3>
          <p className="mt-2 text-sm leading-6 text-slate-500">{description}</p>
        </div>
        <div className="rounded-2xl bg-slate-950 p-2 text-white transition group-hover:translate-x-0.5">
          <ArrowRight className="h-4 w-4" />
        </div>
      </div>
    </Link>
  );
}

function Meter({ label, used, limit }) {
  const pct = limit > 0 ? Math.min(100, Math.round((used / limit) * 100)) : 0;
  const tone = pct >= 100 ? 'bg-rose-500' : pct >= 80 ? 'bg-amber-500' : 'bg-slate-950';

  return (
    <div>
      <div className="mb-2 flex items-center justify-between text-sm">
        <span className="font-semibold text-slate-700">{label}</span>
        <span className="text-slate-500">
          {used} de {limit}
        </span>
      </div>
      <div className="h-3 overflow-hidden rounded-full bg-slate-100">
        <div className={`h-full rounded-full transition-all ${tone}`} style={{ width: `${pct}%` }} />
      </div>
    </div>
  );
}
