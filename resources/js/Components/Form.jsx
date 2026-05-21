import React from 'react';
import { cn } from '@/lib/utils';

export function Field({ label, error, children }) {
  return (
    <label className="block space-y-2">
      {label && <span className="field-label">{label}</span>}
      {children}
      {error && <span className="text-xs font-medium text-rose-600">{error}</span>}
    </label>
  );
}

export function Input({ className, ...props }) {
  return <input className={cn('focus-ring h-11 w-full rounded-2xl border-slate-200 bg-white px-4 text-sm shadow-sm', className)} {...props} />;
}

export function Textarea({ className, ...props }) {
  return <textarea className={cn('focus-ring min-h-32 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm', className)} {...props} />;
}

export function Select({ className, children, ...props }) {
  return <select className={cn('focus-ring h-11 w-full rounded-2xl border-slate-200 bg-white px-4 text-sm shadow-sm', className)} {...props}>{children}</select>;
}
