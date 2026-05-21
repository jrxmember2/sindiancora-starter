import React from 'react';
import { motion } from 'framer-motion';

export default function AuthLayout({ children }) {
  return (
    <main className="min-h-screen overflow-hidden bg-slate-950 text-white">
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(37,99,235,0.45),transparent_30rem),radial-gradient(circle_at_90%_70%,rgba(14,165,233,0.25),transparent_32rem)]" />
      <div className="relative grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
        <section className="hidden flex-col justify-between p-12 lg:flex">
          <div className="inline-flex items-center gap-3">
            <div className="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-slate-950 shadow-soft">
              <span className="text-xl font-black">S</span>
            </div>
            <div>
              <p className="text-lg font-extrabold tracking-tight">SindiÂncora</p>
              <p className="text-sm text-blue-100/80">Gestão condominial inteligente</p>
            </div>
          </div>

          <motion.div initial={{ opacity: 0, y: 24 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.55 }} className="max-w-2xl">
            <p className="mb-5 inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm text-blue-50 backdrop-blur">SaaS multiempresa • licenciamento flexível • IA-ready</p>
            <h1 className="text-5xl font-black tracking-tight text-white xl:text-6xl">Uma operação condominial mais leve, organizada e mensurável.</h1>
            <p className="mt-6 max-w-xl text-lg leading-8 text-slate-300">Controle chamados, documentos, fornecedores, relatórios, prazos e módulos por licença em uma plataforma limpa e segura.</p>
          </motion.div>

          <p className="text-sm text-slate-400">© {new Date().getFullYear()} Serratech</p>
        </section>

        <section className="flex items-center justify-center px-5 py-10">
          <motion.div initial={{ opacity: 0, scale: 0.98 }} animate={{ opacity: 1, scale: 1 }} className="w-full max-w-md rounded-4xl border border-white/12 bg-white p-8 text-slate-900 shadow-soft">
            {children}
          </motion.div>
        </section>
      </div>
    </main>
  );
}
