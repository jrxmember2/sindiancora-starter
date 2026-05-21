import React from 'react';
import { AlertCircle, CheckCircle2, Info, X } from 'lucide-react';
import { cn } from '@/lib/utils';

const tones = {
  success: {
    wrapper: 'border-emerald-200 bg-emerald-50/95',
    icon: 'text-emerald-600',
    title: 'text-emerald-900',
    body: 'text-emerald-700',
    Icon: CheckCircle2,
  },
  error: {
    wrapper: 'border-rose-200 bg-rose-50/95',
    icon: 'text-rose-600',
    title: 'text-rose-900',
    body: 'text-rose-700',
    Icon: AlertCircle,
  },
  info: {
    wrapper: 'border-slate-200 bg-white/95',
    icon: 'text-slate-600',
    title: 'text-slate-900',
    body: 'text-slate-600',
    Icon: Info,
  },
};

export default function ToastRegion({ toasts = [], onDismiss }) {
  if (!toasts.length) {
    return null;
  }

  return (
    <div className="pointer-events-none fixed right-4 top-4 z-[60] flex w-full max-w-sm flex-col gap-3">
      {toasts.map((toast) => {
        const tone = tones[toast.tone] || tones.info;
        const Icon = tone.Icon;

        return (
          <div
            key={toast.id}
            className={cn(
              'pointer-events-auto rounded-3xl border px-4 py-4 shadow-soft backdrop-blur',
              tone.wrapper
            )}
          >
            <div className="flex items-start gap-3">
              <div className={cn('mt-0.5', tone.icon)}>
                <Icon className="h-5 w-5" />
              </div>

              <div className="min-w-0 flex-1">
                <p className={cn('text-sm font-bold', tone.title)}>{toast.title}</p>
                <p className={cn('mt-1 text-sm leading-6', tone.body)}>{toast.message}</p>
              </div>

              <button
                type="button"
                onClick={() => onDismiss?.(toast.id)}
                className="rounded-2xl p-1 text-slate-400 transition hover:bg-white/70 hover:text-slate-700"
              >
                <X className="h-4 w-4" />
              </button>
            </div>
          </div>
        );
      })}
    </div>
  );
}
