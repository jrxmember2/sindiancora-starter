import React, { useEffect } from 'react';
import { X } from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Drawer({ open, onClose, title, description, children, side = 'left' }) {
  useEffect(() => {
    if (!open) {
      return undefined;
    }

    const handleEscape = (event) => {
      if (event.key === 'Escape') {
        onClose?.();
      }
    };

    window.addEventListener('keydown', handleEscape);

    return () => window.removeEventListener('keydown', handleEscape);
  }, [open, onClose]);

  return (
    <div className={cn('fixed inset-0 z-50 transition', open ? 'pointer-events-auto' : 'pointer-events-none')}>
      <button
        type="button"
        onClick={onClose}
        aria-label="Fechar menu"
        className={cn('absolute inset-0 bg-slate-950/45 backdrop-blur-sm transition', open ? 'opacity-100' : 'opacity-0')}
      />

      <div
        className={cn(
          'absolute inset-y-0 w-full max-w-sm border-slate-200 bg-white shadow-soft transition duration-300',
          side === 'right' ? 'right-0 border-l' : 'left-0 border-r',
          open ? 'translate-x-0' : side === 'right' ? 'translate-x-full' : '-translate-x-full'
        )}
      >
        <div className="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-5">
          <div>
            <h2 className="text-base font-bold tracking-tight text-slate-950">{title}</h2>
            {description && <p className="mt-1 text-sm text-slate-500">{description}</p>}
          </div>

          <button type="button" onClick={onClose} className="rounded-2xl border border-slate-200 p-2 text-slate-500 transition hover:text-slate-900">
            <X className="h-4 w-4" />
          </button>
        </div>

        <div className="h-[calc(100%-5.25rem)] overflow-y-auto px-5 py-5">{children}</div>
      </div>
    </div>
  );
}
