import React from 'react';
import { cn } from '@/lib/utils';

export function Field({ label, hint, error, optional = false, className, children }) {
  return (
    <label className={cn('block space-y-2', className)}>
      {(label || hint) && (
        <span className="block">
          {label && (
            <span className="field-label inline-flex items-center gap-2">
              {label}
              {optional && <span className="text-xs font-medium text-slate-400">opcional</span>}
            </span>
          )}
          {hint && <span className="mt-1 block text-xs leading-5 text-slate-500">{hint}</span>}
        </span>
      )}

      {children}

      {error && <span className="text-xs font-medium text-rose-600">{error}</span>}
    </label>
  );
}

export function Input({ className, ...props }) {
  return (
    <input
      className={cn(
        'focus-ring h-11 w-full rounded-2xl border-slate-200 bg-white px-4 text-sm shadow-sm placeholder:text-slate-400',
        className
      )}
      {...props}
    />
  );
}

export function Textarea({ className, ...props }) {
  return (
    <textarea
      className={cn(
        'focus-ring min-h-32 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm shadow-sm placeholder:text-slate-400',
        className
      )}
      {...props}
    />
  );
}

export function Select({ className, children, ...props }) {
  return (
    <select
      className={cn(
        'focus-ring h-11 w-full rounded-2xl border-slate-200 bg-white px-4 text-sm shadow-sm',
        className
      )}
      {...props}
    >
      {children}
    </select>
  );
}

export function Checkbox({ label, hint, className, ...props }) {
  return (
    <label className={cn('flex items-start gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm', className)}>
      <input
        type="checkbox"
        className="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
        {...props}
      />
      <span className="space-y-1">
        <span className="block font-semibold text-slate-800">{label}</span>
        {hint && <span className="block text-xs leading-5 text-slate-500">{hint}</span>}
      </span>
    </label>
  );
}

export function CheckboxCard({ checked, label, hint, className, ...props }) {
  return (
    <label
      className={cn(
        'flex cursor-pointer items-start gap-3 rounded-2xl border px-4 py-4 text-sm transition',
        checked ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white hover:border-slate-300',
        className
      )}
    >
      <input
        type="checkbox"
        checked={checked}
        className="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
        {...props}
      />
      <span className="space-y-1">
        <span className={cn('block font-semibold', checked ? 'text-white' : 'text-slate-900')}>{label}</span>
        {hint && <span className={cn('block text-xs leading-5', checked ? 'text-slate-200' : 'text-slate-500')}>{hint}</span>}
      </span>
    </label>
  );
}
