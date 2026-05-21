import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ modules }) {
  return (
    <AppLayout title="Modulos">
      <Head title="Modulos" />

      <Card>
        <CardHeader
          title="Catalogo de modulos"
          description="Estes modulos podem ser liberados individualmente nas licencas contratuais."
        />

        <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
          {modules.map((module) => (
            <div key={module.id} className="rounded-3xl border border-slate-200 bg-slate-50 p-5">
              <div className="mb-3 flex items-start justify-between gap-3">
                <h3 className="font-bold text-slate-950">{module.name}</h3>
                <Badge tone={module.active ? 'green' : 'gray'}>{module.active ? 'ativo' : 'inativo'}</Badge>
              </div>
              <p className="text-xs font-semibold uppercase tracking-wide text-blue-600">{module.key}</p>
              <p className="mt-2 text-sm leading-6 text-slate-500">{module.description}</p>
              <p className="mt-3 text-xs font-medium text-slate-400">{module.category}</p>
            </div>
          ))}
        </div>
      </Card>
    </AppLayout>
  );
}
