import React from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
  Building2,
  CalendarDays,
  ChevronDown,
  FileText,
  Gauge,
  Home,
  LogOut,
  Settings,
  ShieldCheck,
  TicketCheck,
  Wrench,
} from 'lucide-react';
import { cn } from '@/lib/utils';

const nav = [
  { label: 'Dashboard', href: '/dashboard', icon: Gauge },
  { label: 'Chamados', href: '/app/issues', icon: TicketCheck },
  { label: 'Condominios', href: '/app/condominiums', icon: Building2 },
  { label: 'Fornecedores', href: '/app/suppliers', icon: Wrench },
  { label: 'Documentos', href: '/app/documents', icon: FileText },
  { label: 'Cronograma', href: '#', icon: CalendarDays, locked: true },
];

const superNav = [
  { label: 'Empresas', href: '/superadmin/companies', icon: Home },
  { label: 'Licencas', href: '/superadmin/licenses', icon: ShieldCheck },
  { label: 'Modulos', href: '/superadmin/modules', icon: Settings },
];

export default function AppLayout({ title, children }) {
  const page = usePage();
  const { auth, tenant, flash } = page.props;
  const { url } = page;
  const isSuperadmin = auth?.user?.is_superadmin;
  const items = isSuperadmin ? [...nav, ...superNav] : nav;

  const switchCompany = (event) => {
    const companyId = event.target.value;

    if (companyId) {
      router.post('/trocar-empresa', { company_id: companyId }, { preserveScroll: true });
    }
  };

  return (
    <div className="min-h-screen bg-slate-50">
      <aside className="fixed inset-y-0 left-0 z-30 hidden w-72 border-r border-slate-200/80 bg-white/85 backdrop-blur-xl lg:block">
        <div className="flex h-20 items-center gap-3 px-6">
          <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white p-2 shadow-soft ring-1 ring-slate-200/80">
            <img src="/branding/app-logo.png" alt="Logo SindiAncora" className="h-full w-full object-contain" />
          </div>

          <div>
            <p className="text-lg font-extrabold tracking-tight text-slate-950">SindiAncora</p>
            <p className="text-xs font-medium text-slate-500">SaaS condominial</p>
          </div>
        </div>

        <nav className="space-y-1 px-4">
          {items.map((item) => {
            const Icon = item.icon;
            const active = item.href !== '#' && url?.startsWith(item.href);

            return (
              <Link
                key={item.label}
                href={item.href}
                className={cn(
                  'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition',
                  active ? 'bg-slate-950 text-white shadow-soft' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950'
                )}
              >
                <Icon className="h-4 w-4" />
                <span className="flex-1">{item.label}</span>
                {item.locked && (
                  <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500">
                    em breve
                  </span>
                )}
              </Link>
            );
          })}
        </nav>
      </aside>

      <div className="lg:pl-72">
        <header className="sticky top-0 z-20 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl">
          <div className="flex h-20 items-center justify-between gap-4 px-5 lg:px-8">
            <div>
              <p className="text-sm font-medium text-slate-500">
                {tenant?.currentCompany?.name || 'Ambiente geral'}
              </p>
              <h1 className="text-xl font-black tracking-tight text-slate-950">{title}</h1>
            </div>

            <div className="flex items-center gap-3">
              {tenant?.companies?.length > 0 && (
                <label className="relative hidden md:block">
                  <select
                    onChange={switchCompany}
                    defaultValue={tenant?.currentCompany?.id || ''}
                    className="h-10 rounded-2xl border-slate-200 bg-white pl-4 pr-9 text-sm font-semibold text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500/20"
                  >
                    <option value="">Selecionar empresa</option>
                    {tenant.companies.map((company) => (
                      <option key={company.id} value={company.id}>
                        {company.name}
                      </option>
                    ))}
                  </select>
                  <ChevronDown className="pointer-events-none absolute right-3 top-3 h-4 w-4 text-slate-400" />
                </label>
              )}

              <div className="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm sm:flex">
                <div className="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-xs font-bold text-slate-700">
                  {auth?.user?.name?.slice(0, 1)}
                </div>

                <div className="leading-tight">
                  <p className="text-sm font-bold text-slate-900">{auth?.user?.name}</p>
                  <p className="text-xs text-slate-500">{isSuperadmin ? 'Superadmin' : 'Usuario'}</p>
                </div>
              </div>

              <button
                onClick={() => router.post('/logout')}
                className="rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:text-rose-600"
              >
                <LogOut className="h-4 w-4" />
              </button>
            </div>
          </div>
        </header>

        <main className="px-5 py-8 lg:px-8">
          {flash?.success && (
            <div className="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
              {flash.success}
            </div>
          )}

          {flash?.error && (
            <div className="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
              {flash.error}
            </div>
          )}

          {children}
        </main>
      </div>
    </div>
  );
}
