import React from 'react';
import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';

function plainLabel(label) {
  return label
    .replace(/&laquo;/g, '<<')
    .replace(/&raquo;/g, '>>')
    .replace(/<[^>]+>/g, '')
    .trim();
}

export default function Pagination({ links = [] }) {
  if (!links.length || links.length <= 3) {
    return null;
  }

  return (
    <nav className="flex flex-wrap items-center justify-end gap-2">
      {links.map((link, index) => {
        const label = plainLabel(link.label);
        const common = cn(
          'inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-semibold transition',
          link.active
            ? 'bg-slate-950 text-white'
            : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50',
          !link.url && 'cursor-not-allowed opacity-45 hover:border-slate-200 hover:bg-white'
        );

        if (!link.url) {
          return (
            <span key={`${label}-${index}`} className={common}>
              {label}
            </span>
          );
        }

        return (
          <Link key={`${label}-${index}`} href={link.url} className={common} preserveScroll>
            {label}
          </Link>
        );
      })}
    </nav>
  );
}
