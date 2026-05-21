import React from 'react';
import { motion } from 'framer-motion';

export default function AuthLayout({ children }) {
  return (
    <main className="min-h-screen overflow-hidden bg-slate-950 text-white">
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(37,99,235,0.35),transparent_30rem),radial-gradient(circle_at_90%_70%,rgba(45,212,191,0.22),transparent_32rem)]" />

      <div className="relative grid min-h-screen lg:grid-cols-[1.15fr_0.85fr]">
        <section className="hidden flex-col justify-between p-12 lg:flex">
          <div className="inline-flex items-center gap-4">
            <div className="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/95 p-2 shadow-soft">
              <img src="/branding/app-logo.png" alt="Logo SindiAncora" className="h-full w-full object-contain" />
            </div>

            <div>
              <p className="text-xl font-extrabold tracking-tight">SindiAncora</p>
              <p className="text-sm text-blue-100/80">Gestão condominial inteligente</p>
            </div>
          </div>

          <div className="grid items-end gap-10 xl:grid-cols-[1fr_20rem]">
            <motion.div
              initial={{ opacity: 0, y: 24 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.55 }}
              className="max-w-2xl"
            >
              <p className="mb-5 inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm text-blue-50 backdrop-blur">
                SaaS multiempresa | licenciamento flexível | IA-ready
              </p>
              <h1 className="text-5xl font-black tracking-tight text-white xl:text-6xl">
                Uma operação condominial mais clara, segura e controlável.
              </h1>
              <p className="mt-6 max-w-xl text-lg leading-8 text-slate-300">
                Centralize chamados, documentos, fornecedores, prazos e licenças em uma plataforma
                preparada para crescer com cada empresa cliente.
              </p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 24 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.55, delay: 0.1 }}
              className="flex items-end justify-end"
            >
              <img
                src="/branding/login-hero.png"
                alt="Ilustração de atendimento SindiAncora"
                className="max-h-[34rem] w-auto object-contain drop-shadow-[0_20px_50px_rgba(15,23,42,0.35)]"
              />
            </motion.div>
          </div>

          <p className="text-sm text-slate-400">(c) {new Date().getFullYear()} Serratech</p>
        </section>

        <section className="flex items-center justify-center px-5 py-10">
          <motion.div
            initial={{ opacity: 0, scale: 0.98 }}
            animate={{ opacity: 1, scale: 1 }}
            className="w-full max-w-md rounded-[2rem] border border-white/12 bg-white p-8 text-slate-900 shadow-soft"
          >
            <div className="mb-6 flex items-center gap-3 lg:hidden">
              <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950/5 p-2">
                <img src="/branding/app-logo.png" alt="Logo SindiAncora" className="h-full w-full object-contain" />
              </div>

              <div>
                <p className="text-base font-extrabold tracking-tight text-slate-950">SindiAncora</p>
                <p className="text-sm text-slate-500">Gestão condominial inteligente</p>
              </div>
            </div>

            {children}
          </motion.div>
        </section>
      </div>
    </main>
  );
}
