import React from 'react';
import { cn } from '@/lib/utils';

export function Card({ children, className }) {
  return <div className={cn('thin-card p-6', className)}>{children}</div>;
}

export function CardHeader({ title, description, action }) {
  return (
    <div className="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <h2 className="text-lg font-bold tracking-tight text-slate-950">{title}</h2>
        {description && <p className="mt-1 text-sm leading-6 text-slate-500">{description}</p>}
      </div>
      {action}
    </div>
  );
}
