import React, { useEffect } from 'react';
import { X } from 'lucide-react';

export default function Modal({ open, onClose, title, description, children, footer, size = 'md' }) {
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

  if (!open) {
    return null;
  }

  const sizes = {
    sm: 'max-w-md',
    md: 'max-w-xl',
    lg: 'max-w-3xl',
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <button type="button" className="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" onClick={onClose} aria-label="Fechar" />

      <div className={`relative z-10 w-full ${sizes[size]} rounded-[2rem] border border-slate-200 bg-white shadow-soft`}>
        <div className="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
          <div>
            <h2 className="text-lg font-bold tracking-tight text-slate-950">{title}</h2>
            {description && <p className="mt-1 text-sm leading-6 text-slate-500">{description}</p>}
          </div>
          <button type="button" onClick={onClose} className="rounded-2xl border border-slate-200 p-2 text-slate-500 transition hover:text-slate-900">
            <X className="h-4 w-4" />
          </button>
        </div>

        <div className="px-6 py-5">{children}</div>

        {footer && <div className="flex flex-wrap justify-end gap-3 border-t border-slate-200 px-6 py-5">{footer}</div>}
      </div>
    </div>
  );
}
