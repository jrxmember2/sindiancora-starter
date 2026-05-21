import React from 'react';
import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

const variants = {
  primary: 'bg-slate-950 text-white hover:bg-slate-800 shadow-soft',
  soft: 'border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 shadow-line',
  ghost: 'text-slate-700 hover:bg-slate-100',
  danger: 'bg-rose-600 text-white hover:bg-rose-700',
};

const sizes = {
  sm: 'h-9 rounded-xl px-3 text-sm',
  md: 'h-10 rounded-2xl px-4 text-sm',
  lg: 'h-12 rounded-2xl px-5 text-sm',
};

export default function Button({ className, variant = 'primary', size = 'md', href, type = 'button', children, ...props }) {
  const classes = cn(
    'inline-flex items-center justify-center gap-2 font-semibold transition disabled:cursor-not-allowed disabled:opacity-60',
    variants[variant],
    sizes[size],
    className
  );

  if (href) {
    return <Link href={href} className={classes} {...props}>{children}</Link>;
  }

  return <button type={type} className={classes} {...props}>{children}</button>;
}
