import React from 'react';
import { Head } from '@inertiajs/react';
import { Boxes, CalendarDays, Fingerprint, ShieldCheck } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import Badge from '@/Components/Badge';
import { Card, CardHeader } from '@/Components/Card';

export default function Index({ currentVersion, history }) {
  return (
    <AppLayout title="Versões">
      <Head title="Versões" />

      <div className="grid gap-5 xl:grid-cols-[1.1fr_0.9fr]">
        <Card>
          <CardHeader
            title="Versionamento da plataforma"
            description="Painel interno para acompanhamento das releases publicadas. Visível apenas para o superadmin."
          />

          <div className="grid gap-4 sm:grid-cols-2">
            <Metric
              icon={Boxes}
              label="Versão atual"
              value={currentVersion?.number || 'não definida'}
              hint={currentVersion?.name || 'Sem nome de release'}
            />
            <Metric
              icon={ShieldCheck}
              label="Stage"
              value={labelStage(currentVersion?.stage)}
              hint="Controle de release"
            />
            <Metric
              icon={CalendarDays}
              label="Liberada em"
              value={formatDate(currentVersion?.released_at)}
              hint="Data de publicação"
            />
            <Metric
              icon={Fingerprint}
              label="Build SHA"
              value={shortSha(currentVersion?.build_sha)}
              hint={currentVersion?.build_sha || 'Não informado'}
            />
          </div>
        </Card>

        <Card>
          <CardHeader
            title="Política inicial"
            description="Base de governança para manter a plataforma rastreável desde o início."
          />

          <div className="space-y-3 text-sm leading-6 text-slate-600">
            <p>As versões ficam registradas em código e expostas apenas na área do superadmin.</p>
            <p>Cada release deve informar número, stage, data e principais entregas publicadas.</p>
            <p>Nas próximas fases, podemos evoluir isso para changelog persistido em banco e notas por deploy.</p>
          </div>
        </Card>
      </div>

      <div className="mt-8">
        <Card>
          <CardHeader
            title="Histórico de releases"
            description="Linha do tempo das entregas publicadas no sistema."
          />

          <div className="space-y-4">
            {history.map((release) => (
              <div key={`${release.number}-${release.released_at}`} className="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="text-lg font-bold tracking-tight text-slate-950">
                        {release.number} {release.name ? `- ${release.name}` : ''}
                      </h3>
                      <Badge tone={toneStage(release.stage)}>{labelStage(release.stage)}</Badge>
                    </div>

                    <p className="mt-1 text-sm text-slate-500">
                      Publicada em {formatDate(release.released_at)}
                    </p>
                  </div>

                  <Badge tone="blue">Somente Superadmin</Badge>
                </div>

                <div className="mt-4 space-y-2">
                  {release.highlights?.map((highlight) => (
                    <p key={highlight} className="rounded-2xl bg-white px-4 py-3 text-sm leading-6 text-slate-600 ring-1 ring-slate-200">
                      {highlight}
                    </p>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </Card>
      </div>
    </AppLayout>
  );
}

function Metric({ icon: Icon, label, value, hint }) {
  return (
    <div className="rounded-3xl border border-slate-200 bg-slate-50 p-5">
      <div className="flex items-start justify-between gap-3">
        <div>
          <p className="text-sm font-semibold text-slate-500">{label}</p>
          <p className="mt-3 text-2xl font-black tracking-tight text-slate-950">{value}</p>
          <p className="mt-2 text-xs font-medium text-slate-400">{hint}</p>
        </div>

        <div className="rounded-2xl bg-slate-950 p-2.5 text-white">
          <Icon className="h-4 w-4" />
        </div>
      </div>
    </div>
  );
}

function formatDate(value) {
  if (!value) {
    return 'Não informado';
  }

  const date = new Date(`${value}T00:00:00`);

  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(date);
}

function shortSha(value) {
  if (!value) {
    return 'Não informado';
  }

  return value.slice(0, 8);
}

function labelStage(stage) {
  const labels = {
    foundation: 'Foundation',
    development: 'Development',
    homologation: 'Homologação',
    production: 'Produção',
  };

  return labels[stage] || stage || 'Não informado';
}

function toneStage(stage) {
  const tones = {
    foundation: 'blue',
    development: 'yellow',
    homologation: 'yellow',
    production: 'green',
  };

  return tones[stage] || 'gray';
}
