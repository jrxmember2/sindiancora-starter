import React from 'react';
import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

const variants = {
  primary: 'bg-slate-950 text-white hover:bg-slate-800 shadow-soft',
  soft: 'bg-white text-slate-800 border border-slate-200 hover:bg-slate-50 shadow-line',
  ghost: 'text-slate-700 hover:bg-slate-100',
  danger: 'bg-rose-600 text-white hover:bg-rose-700',
};

export default function Button({ className, variant = 'primary', href, children, ...props }) {
  const classes = cn('inline-flex h-10 items-center justify-center gap-2 rounded-2xl px-4 text-sm font-semibold transition disabled:opacity-60', variants[variant], className);

  if (href) return <Link href={href} className={classes} {...props}>{children}</Link>;

  return <button className={classes} {...props}>{children}</button>;
}
