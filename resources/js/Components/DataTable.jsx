import React from 'react';
import EmptyState from '@/Components/EmptyState';
import Pagination from '@/Components/Pagination';
import { cn } from '@/lib/utils';

export default function DataTable({
  columns,
  rows,
  renderRow,
  emptyTitle,
  emptyDescription,
  className,
  meta,
}) {
  if (!rows?.length) {
    return <EmptyState title={emptyTitle} description={emptyDescription} />;
  }

  return (
    <div className={cn('overflow-hidden rounded-3xl border border-slate-200 bg-white', className)}>
      <div className="overflow-x-auto">
        <table className="w-full text-left text-sm">
          <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
            <tr>
              {columns.map((column) => (
                <th key={column.key} className={cn('px-4 py-3 font-semibold', column.className, column.align === 'right' && 'text-right')}>
                  {column.label}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-200">{rows.map(renderRow)}</tbody>
        </table>
      </div>

      {meta && (
        <div className="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
          <p>
            Mostrando <span className="font-semibold text-slate-800">{meta.from ?? 0}</span> a{' '}
            <span className="font-semibold text-slate-800">{meta.to ?? 0}</span> de{' '}
            <span className="font-semibold text-slate-800">{meta.total ?? 0}</span> registros
          </p>
          <Pagination links={meta.links} />
        </div>
      )}
    </div>
  );
}
