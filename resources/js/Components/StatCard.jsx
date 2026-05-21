import React from 'react';

export default function StatCard({ label, value, icon: Icon, hint }) {
  return (
    <div className="thin-card p-5">
      <div className="flex items-center justify-between gap-4">
        <div>
          <p className="text-sm font-medium text-slate-500">{label}</p>
          <p className="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">{value}</p>
        </div>
        {Icon && <div className="rounded-2xl bg-slate-950 p-3 text-white"><Icon className="h-5 w-5" /></div>}
      </div>
      {hint && <p className="mt-4 text-xs text-slate-500">{hint}</p>}
    </div>
  );
}
