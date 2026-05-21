import React, { useEffect, useRef, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
  Building2,
  CalendarDays,
  ChevronDown,
  FileText,
  Gauge,
  History,
  Home,
  LogOut,
  Menu,
  Settings,
  ShieldCheck,
  TicketCheck,
  Wrench,
} from 'lucide-react';
import Drawer from '@/Components/Drawer';
import ToastRegion from '@/Components/ToastRegion';
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
  { label: 'Versoes', href: '/superadmin/versions', icon: History },
];

export default function AppLayout({ title, children }) {
  const page = usePage();
  const { auth, tenant, flash } = page.props;
  const { url } = page;
  const isSuperadmin = auth?.user?.is_superadmin;
  const items = isSuperadmin ? [...nav, ...superNav] : nav;
  const [menuOpen, setMenuOpen] = useState(false);
  const [toasts, setToasts] = useState([]);
  const lastFlashRef = useRef('');

  useEffect(() => {
    setMenuOpen(false);
  }, [url]);

  useEffect(() => {
    const success = flash?.success || '';
    const error = flash?.error || '';
    const signature = `${success}|${error}`;

    if (!success && !error) {
      return;
    }

    if (lastFlashRef.current === signature) {
      return;
    }

    lastFlashRef.current = signature;

    const next = [
      success && { id: `success-${Date.now()}`, tone: 'success', title: 'Tudo certo', message: success },
      error && { id: `error-${Date.now() + 1}`, tone: 'error', title: 'Algo precisa de atencao', message: error },
    ].filter(Boolean);

    setToasts((current) => [...current, ...next]);

    next.forEach((toast) => {
      window.setTimeout(() => {
        setToasts((current) => current.filter((item) => item.id !== toast.id));
      }, 4200);
    });
  }, [flash?.error, flash?.success]);

  const switchCompany = (event) => {
    const companyId = event.target.value;

    if (companyId) {
      router.post('/trocar-empresa', { company_id: companyId }, { preserveScroll: true });
    }
  };

  const logout = () => {
    router.post('/logout');
  };

  const dismissToast = (id) => {
    setToasts((current) => current.filter((toast) => toast.id !== id));
  };

  return (
    <div className="min-h-screen bg-slate-50">
      <ToastRegion toasts={toasts} onDismiss={dismissToast} />

      <aside className="fixed inset-y-0 left-0 z-30 hidden w-72 border-r border-slate-200/80 bg-white/85 backdrop-blur-xl lg:block">
        <SidebarHeader />
        <SidebarNav items={items} url={url} />
      </aside>

      <Drawer
        open={menuOpen}
        onClose={() => setMenuOpen(false)}
        title="Menu principal"
        description="Navegue entre os modulos liberados do painel."
      >
        <SidebarHeader compact />
        <div className="mt-5">
          <SidebarNav items={items} url={url} mobile />
        </div>

        {tenant?.companies?.length > 0 && (
          <div className="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-4">
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Empresa ativa</p>
            <label className="relative mt-3 block">
              <select
                onChange={switchCompany}
                defaultValue={tenant?.currentCompany?.id || ''}
                className="h-11 w-full rounded-2xl border-slate-200 bg-white pl-4 pr-10 text-sm font-semibold text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500/20"
              >
                <option value="">Selecionar empresa</option>
                {tenant.companies.map((company) => (
                  <option key={company.id} value={company.id}>
                    {company.name}
                  </option>
                ))}
              </select>
              <ChevronDown className="pointer-events-none absolute right-3 top-3.5 h-4 w-4 text-slate-400" />
            </label>
          </div>
        )}
      </Drawer>

      <div className="lg:pl-72">
        <header className="sticky top-0 z-20 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl">
          <div className="flex min-h-20 items-center justify-between gap-4 px-5 py-4 lg:px-8">
            <div className="flex items-start gap-3">
              <button
                type="button"
                onClick={() => setMenuOpen(true)}
                className="rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-600 shadow-sm transition hover:text-slate-950 lg:hidden"
              >
                <Menu className="h-4 w-4" />
              </button>

              <div>
                <p className="text-sm font-medium text-slate-500">
                  {tenant?.currentCompany?.name || 'Ambiente geral'}
                </p>
                <h1 className="text-xl font-black tracking-tight text-slate-950">{title}</h1>
              </div>
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
                  <p className="text-xs text-slate-500">{isSuperadmin ? 'Superadmin' : 'Usuario interno'}</p>
                </div>
              </div>

              <button
                type="button"
                onClick={logout}
                className="rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:text-rose-600"
              >
                <LogOut className="h-4 w-4" />
              </button>
            </div>
          </div>
        </header>

        <main className="px-5 py-8 lg:px-8">{children}</main>
      </div>
    </div>
  );
}

function SidebarHeader({ compact = false }) {
  return (
    <div className={cn('flex items-center gap-3 px-6', compact ? 'h-auto px-0' : 'h-20')}>
      <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white p-2 shadow-soft ring-1 ring-slate-200/80">
        <img src="/branding/app-logo.png" alt="Logo SindiAncora" className="h-full w-full object-contain" />
      </div>

      <div>
        <p className="text-lg font-extrabold tracking-tight text-slate-950">SindiAncora</p>
        <p className="text-xs font-medium text-slate-500">SaaS condominial</p>
      </div>
    </div>
  );
}

function SidebarNav({ items, url, mobile = false }) {
  return (
    <nav className={cn('space-y-1', mobile ? 'px-0' : 'px-4')}>
      {items.map((item) => {
        const Icon = item.icon;
        const active = item.href !== '#' && url?.startsWith(item.href);
        const classes = cn(
          'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition',
          active ? 'bg-slate-950 text-white shadow-soft' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950',
          item.locked && 'cursor-not-allowed opacity-70'
        );

        if (item.locked || item.href === '#') {
          return (
            <div key={item.label} className={classes}>
              <Icon className="h-4 w-4" />
              <span className="flex-1">{item.label}</span>
              <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-500">em breve</span>
            </div>
          );
        }

        return (
          <Link key={item.label} href={item.href} className={classes}>
            <Icon className="h-4 w-4" />
            <span className="flex-1">{item.label}</span>
          </Link>
        );
      })}
    </nav>
  );
}
