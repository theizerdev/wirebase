import React, { useEffect, useMemo, useState, useCallback } from 'react'
import { BrowserRouter, Routes, Route, Link, Navigate, useParams, useNavigate } from 'react-router-dom'
import * as Dialog from '@radix-ui/react-dialog'
import { Toaster, toast } from 'sonner'
import {
  Search, ChevronLeft, ChevronRight, CalendarDays, ArrowRight,
  FileText, DollarSign, AlertTriangle, CheckCircle2, Clock, Filter,
  CreditCard, Send, Loader2, TrendingUp, Eye, User, Shield, Monitor,
  KeyRound, Save, MapPin, Mail, Phone, IdCard
} from 'lucide-react'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import Layout from './Layout.jsx'

/* ── Helpers ── */
const fmt = (v) => Number(v || 0).toLocaleString('es', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || ''
const apiFetch = (url, opts = {}) => fetch(url, {
  credentials: 'same-origin',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf(), ...opts.headers },
  ...opts,
})

const statusColors = {
  activo: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
  completado: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
  borrador: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
  mora: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
  cancelado: 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
  reposicion: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
  pagado: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
  pendiente: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
  parcial: 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
  vencido: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
}

function Badge({ status }) {
  return (
    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ${statusColors[status] || 'bg-gray-100 text-gray-600'}`}>
      {status?.charAt(0).toUpperCase() + status?.slice(1)}
    </span>
  )
}

function useFetch(url, deps = []) {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const load = useCallback(async (overrideUrl) => {
    try {
      setLoading(true)
      setError(null)
      const resp = await fetch(overrideUrl || url, { credentials: 'same-origin' })
      if (!resp.ok) throw new Error('Error al cargar datos')
      const json = await resp.json()
      setData(json.data || [])
    } catch (e) {
      setError(e.message)
    } finally {
      setLoading(false)
    }
  }, [url])
  useEffect(() => { load() }, deps)
  return { data, loading, error, reload: load }
}

function EmptyState({ icon: Icon, title, description, action }) {
  return (
    <div className="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 px-6 py-12 text-center dark:border-gray-800">
      <Icon className="mb-3 h-12 w-12 text-gray-300 dark:text-gray-600" />
      <h3 className="text-sm font-semibold text-gray-900 dark:text-white">{title}</h3>
      {description && <p className="mt-1 text-sm text-gray-500">{description}</p>}
      {action}
    </div>
  )
}

function Spinner() {
  return <div className="flex items-center justify-center py-12"><Loader2 className="h-8 w-8 animate-spin text-blue-600" /></div>
}

function ErrorBox({ message }) {
  return <div className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">{message}</div>
}

/* ════════════════════════════════════════════════════
   HOME
   ════════════════════════════════════════════════════ */
function Home() {
  const { data: contracts, loading: cl } = useFetch('/cliente/api/contracts', [])
  const today = new Date().toISOString().slice(0, 10)
  const future = new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10)
  const { data: cuotas, loading: tl } = useFetch(`/cliente/api/timeline?from=${today}&to=${future}`, [])

  const stats = useMemo(() => {
    if (!contracts) return null
    const activos = contracts.filter(c => c.estado === 'activo' || c.estado === 'mora').length
    const saldo = contracts.reduce((s, c) => s + (c.saldo_pendiente || 0), 0)
    const proximas = cuotas?.filter(c => c.estado === 'pendiente' || c.estado === 'parcial').length || 0
    const vencidas = cuotas?.filter(c => c.estado === 'vencido').length || 0
    return [
      { title: 'Contratos Activos', value: activos, icon: FileText, color: 'text-blue-600 bg-blue-50 dark:bg-blue-900/30' },
      { title: 'Saldo Pendiente', value: `$${fmt(saldo)}`, icon: DollarSign, color: 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30' },
      { title: 'Próximas Cuotas', value: proximas, icon: Clock, color: 'text-amber-600 bg-amber-50 dark:bg-amber-900/30' },
      { title: 'Cuotas Vencidas', value: vencidas, icon: AlertTriangle, color: vencidas > 0 ? 'text-red-600 bg-red-50 dark:bg-red-900/30' : 'text-gray-500 bg-gray-50 dark:bg-gray-800' },
    ]
  }, [contracts, cuotas])

  const loading = cl || tl

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Bienvenido</h2>
        <p className="text-gray-500">Resumen de tu cuenta de financiamiento</p>
      </div>

      {loading && <Spinner />}

      {!loading && stats && (
        <>
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {stats.map((s, i) => (
              <div key={i} className="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-gray-500">{s.title}</p>
                    <p className="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{s.value}</p>
                  </div>
                  <div className={`flex h-11 w-11 items-center justify-center rounded-xl ${s.color}`}>
                    <s.icon className="h-5 w-5" />
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {/* Próximas cuotas */}
            <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
              <div className="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h3 className="font-semibold text-gray-900 dark:text-white">Próximas Cuotas</h3>
                <Link to="/cuotas" className="text-sm font-medium text-blue-600 hover:underline">Ver todas</Link>
              </div>
              <div className="divide-y divide-gray-100 dark:divide-gray-800">
                {cuotas?.filter(c => c.estado !== 'pagado').slice(0, 4).map(c => (
                  <div key={c.id} className="flex items-center justify-between px-5 py-3">
                    <div>
                      <p className="text-sm font-medium text-gray-900 dark:text-white">Cuota #{c.numero_cuota}</p>
                      <p className="text-xs text-gray-500">Vence: {c.fecha_vencimiento}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-semibold text-gray-900 dark:text-white">${fmt(c.monto_total)}</p>
                      <Badge status={c.estado} />
                    </div>
                  </div>
                ))}
                {(!cuotas || cuotas.filter(c => c.estado !== 'pagado').length === 0) && (
                  <p className="px-5 py-6 text-center text-sm text-gray-500">No hay cuotas próximas</p>
                )}
              </div>
            </div>

            {/* Acciones rápidas */}
            <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
              <div className="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h3 className="font-semibold text-gray-900 dark:text-white">Acciones Rápidas</h3>
              </div>
              <div className="space-y-3 p-5">
                <Link to="/contratos" className="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 transition hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-600 dark:hover:bg-blue-900/20">
                  <div className="flex items-center gap-3">
                    <FileText className="h-5 w-5 text-blue-600" />
                    <div>
                      <p className="text-sm font-medium text-gray-900 dark:text-white">Ver Contratos</p>
                      <p className="text-xs text-gray-500">Consulta tus contratos activos</p>
                    </div>
                  </div>
                  <ArrowRight className="h-4 w-4 text-gray-400" />
                </Link>
                <Link to="/pagos" className="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 transition hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-600 dark:hover:bg-blue-900/20">
                  <div className="flex items-center gap-3">
                    <CreditCard className="h-5 w-5 text-emerald-600" />
                    <div>
                      <p className="text-sm font-medium text-gray-900 dark:text-white">Reportar Pago</p>
                      <p className="text-xs text-gray-500">Registra una referencia de pago</p>
                    </div>
                  </div>
                  <ArrowRight className="h-4 w-4 text-gray-400" />
                </Link>
                <Link to="/cuotas" className="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 transition hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-600 dark:hover:bg-blue-900/20">
                  <div className="flex items-center gap-3">
                    <CalendarDays className="h-5 w-5 text-amber-600" />
                    <div>
                      <p className="text-sm font-medium text-gray-900 dark:text-white">Próximas Cuotas</p>
                      <p className="text-xs text-gray-500">Revisa tu calendario de pagos</p>
                    </div>
                  </div>
                  <ArrowRight className="h-4 w-4 text-gray-400" />
                </Link>
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  )
}

/* ════════════════════════════════════════════════════
   CONTRATOS
   ════════════════════════════════════════════════════ */
function Contratos() {
  const { data: items, loading, error } = useFetch('/cliente/api/contracts', [])
  const [q, setQ] = useState('')
  const [page, setPage] = useState(1)
  const pageSize = 8

  const filtered = useMemo(() => {
    if (!items) return { rows: [], total: 0 }
    const f = q ? items.filter(c => (c.numero || '').toLowerCase().includes(q.toLowerCase()) || (c.estado || '').includes(q.toLowerCase())) : items
    const start = (page - 1) * pageSize
    return { rows: f.slice(start, start + pageSize), total: f.length, pages: Math.ceil(f.length / pageSize) }
  }, [items, q, page])

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Mis Contratos</h2>
          <p className="text-sm text-gray-500">Listado de contratos de financiamiento</p>
        </div>
        <div className="relative">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          <input placeholder="Buscar contrato..." value={q} onChange={e => { setQ(e.target.value); setPage(1) }}
            className="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 sm:w-64" />
        </div>
      </div>

      {loading && <Spinner />}
      {error && <ErrorBox message={error} />}

      {!loading && !error && items?.length === 0 && (
        <EmptyState icon={FileText} title="Sin contratos" description="No tienes contratos registrados aún." />
      )}

      {!loading && !error && filtered.total > 0 && (
        <>
          {/* Mobile cards */}
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:hidden">
            {filtered.rows.map(c => (
              <Link key={c.id} to={`/contratos/${c.id}`} className="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-blue-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-600">
                <div className="mb-2 flex items-center justify-between">
                  <span className="text-sm font-bold text-gray-900 dark:text-white">{c.numero}</span>
                  <Badge status={c.estado} />
                </div>
                <div className="space-y-1 text-xs text-gray-500">
                  <div className="flex justify-between"><span>Inicio</span><span className="font-medium text-gray-700 dark:text-gray-300">{c.fecha_inicio || '-'}</span></div>
                  <div className="flex justify-between"><span>Fin estimado</span><span className="font-medium text-gray-700 dark:text-gray-300">{c.fecha_fin_estimada || '-'}</span></div>
                  <div className="flex justify-between"><span>Saldo</span><span className="font-semibold text-gray-900 dark:text-white">${fmt(c.saldo_pendiente)}</span></div>
                </div>
              </Link>
            ))}
          </div>

          {/* Desktop table */}
          <div className="hidden overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 lg:block">
            <table className="w-full">
              <thead>
                <tr className="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                  <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contrato</th>
                  <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                  <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Inicio</th>
                  <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fin Estimado</th>
                  <th className="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Saldo</th>
                  <th className="px-5 py-3" />
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                {filtered.rows.map(c => (
                  <tr key={c.id} className="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <td className="px-5 py-3 text-sm font-semibold text-gray-900 dark:text-white">{c.numero}</td>
                    <td className="px-5 py-3"><Badge status={c.estado} /></td>
                    <td className="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">{c.fecha_inicio || '-'}</td>
                    <td className="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">{c.fecha_fin_estimada || '-'}</td>
                    <td className="px-5 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">${fmt(c.saldo_pendiente)}</td>
                    <td className="px-5 py-3 text-right">
                      <Link to={`/contratos/${c.id}`} className="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <Eye className="h-3.5 w-3.5" /> Ver
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {filtered.pages > 1 && (
            <div className="flex items-center justify-between">
              <p className="text-sm text-gray-500">{filtered.total} contrato(s)</p>
              <div className="flex items-center gap-2">
                <button disabled={page === 1} onClick={() => setPage(p => p - 1)} className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white transition hover:bg-gray-50 disabled:opacity-40 dark:border-gray-700 dark:bg-gray-900"><ChevronLeft className="h-4 w-4" /></button>
                <span className="text-sm text-gray-600">{page} / {filtered.pages}</span>
                <button disabled={page >= filtered.pages} onClick={() => setPage(p => p + 1)} className="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white transition hover:bg-gray-50 disabled:opacity-40 dark:border-gray-700 dark:bg-gray-900"><ChevronRight className="h-4 w-4" /></button>
              </div>
            </div>
          )}
        </>
      )}
    </div>
  )
}

/* ════════════════════════════════════════════════════
   CONTRATO DETALLE
   ════════════════════════════════════════════════════ */
function ContratoDetalle() {
  const { id } = useParams()
  const { data, loading, error } = useFetch(`/cliente/api/contracts/${id}`, [id])

  const progress = useMemo(() => {
    if (!data?.plan_pagos) return 0
    const paid = data.plan_pagos.filter(p => p.estado === 'pagado').length
    return data.plan_pagos.length > 0 ? Math.round((paid / data.plan_pagos.length) * 100) : 0
  }, [data])

  return (
    <div className="space-y-6">
      <Link to="/contratos" className="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800">
        <ChevronLeft className="h-4 w-4" /> Volver
      </Link>

      {loading && <Spinner />}
      {error && <ErrorBox message={error} />}

      {!loading && !error && data && (
        <>
          <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div className="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
              <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h2 className="text-lg font-bold text-gray-900 dark:text-white">Contrato {data.numero}</h2>
                <Badge status={data.estado} />
              </div>
            </div>
            <div className="grid grid-cols-2 gap-4 p-5 sm:grid-cols-4">
              <div><p className="text-xs text-gray-500">Inicio</p><p className="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">{data.fecha_inicio || '-'}</p></div>
              <div><p className="text-xs text-gray-500">Fin Estimado</p><p className="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">{data.fecha_fin_estimada || '-'}</p></div>
              <div><p className="text-xs text-gray-500">Saldo Pendiente</p><p className="mt-0.5 text-sm font-semibold text-gray-900 dark:text-white">${fmt(data.saldo_pendiente)}</p></div>
              <div><p className="text-xs text-gray-500">Frecuencia</p><p className="mt-0.5 text-sm font-medium capitalize text-gray-900 dark:text-white">{data.frecuencia_pago || 'Mensual'}</p></div>
            </div>
            {/* Progress */}
            <div className="border-t border-gray-100 px-5 py-3 dark:border-gray-800">
              <div className="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                <span>Progreso de pagos</span>
                <span className="font-semibold text-gray-900 dark:text-white">{progress}%</span>
              </div>
              <div className="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                <div className="h-full rounded-full bg-blue-600 transition-all duration-500" style={{ width: `${progress}%` }} />
              </div>
            </div>
          </div>

          <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div className="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
              <h3 className="font-semibold text-gray-900 dark:text-white">Plan de Pagos</h3>
            </div>
            {(!data.plan_pagos || data.plan_pagos.length === 0) ? (
              <p className="px-5 py-8 text-center text-sm text-gray-500">No hay cuotas registradas.</p>
            ) : (
              <>
                {/* Mobile cards */}
                <div className="divide-y divide-gray-100 dark:divide-gray-800 lg:hidden">
                  {data.plan_pagos.map(p => (
                    <div key={p.id} className="flex items-center justify-between px-5 py-3">
                      <div>
                        <p className="text-sm font-medium text-gray-900 dark:text-white">Cuota #{p.n}</p>
                        <p className="text-xs text-gray-500">{p.fecha_vencimiento}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-semibold text-gray-900 dark:text-white">${fmt(p.monto_total)}</p>
                        <Badge status={p.estado} />
                      </div>
                    </div>
                  ))}
                </div>
                {/* Desktop table */}
                <div className="hidden lg:block">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <th className="px-5 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                        <th className="px-5 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Vencimiento</th>
                        <th className="px-5 py-2.5 text-right text-xs font-semibold uppercase text-gray-500">Monto</th>
                        <th className="px-5 py-2.5 text-right text-xs font-semibold uppercase text-gray-500">Pagado</th>
                        <th className="px-5 py-2.5 text-center text-xs font-semibold uppercase text-gray-500">Estado</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                      {data.plan_pagos.map(p => (
                        <tr key={p.id} className="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                          <td className="px-5 py-2.5 text-sm font-medium text-gray-900 dark:text-white">{p.n}</td>
                          <td className="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-400">{p.fecha_vencimiento}</td>
                          <td className="px-5 py-2.5 text-right text-sm font-medium text-gray-900 dark:text-white">${fmt(p.monto_total)}</td>
                          <td className="px-5 py-2.5 text-right text-sm text-gray-600 dark:text-gray-400">${fmt(p.monto_pagado)}</td>
                          <td className="px-5 py-2.5 text-center"><Badge status={p.estado} /></td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </>
            )}
          </div>
        </>
      )}
    </div>
  )
}

/* ════════════════════════════════════════════════════
   CUOTAS (Timeline)
   ════════════════════════════════════════════════════ */
function Cuotas() {
  const [range, setRange] = useState(15)
  const today = new Date().toISOString().slice(0, 10)
  const to = new Date(Date.now() + range * 86400000).toISOString().slice(0, 10)
  const { data: items, loading, error, reload } = useFetch(`/cliente/api/timeline?from=${today}&to=${to}`, [])

  useEffect(() => { reload(`/cliente/api/timeline?from=${today}&to=${to}`) }, [range])

  const urgency = (item) => {
    if (item.estado === 'vencido') return 'border-l-red-500'
    const diff = (new Date(item.fecha_vencimiento) - new Date()) / 86400000
    if (diff <= 3) return 'border-l-amber-500'
    return 'border-l-emerald-500'
  }

  const quickFilters = [
    { label: 'Hoy', value: 0 },
    { label: '7 días', value: 7 },
    { label: '15 días', value: 15 },
    { label: '30 días', value: 30 },
  ]

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Próximas Cuotas</h2>
        <p className="text-sm text-gray-500">Cuotas pendientes de tus contratos</p>
      </div>

      <div className="flex flex-wrap gap-2">
        {quickFilters.map(f => (
          <button key={f.value} onClick={() => setRange(f.value)}
            className={`rounded-lg px-3 py-1.5 text-sm font-medium transition ${range === f.value ? 'bg-blue-600 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800'}`}>
            {f.label}
          </button>
        ))}
      </div>

      {loading && <Spinner />}
      {error && <ErrorBox message={error} />}

      {!loading && !error && items?.length === 0 && (
        <EmptyState icon={CalendarDays} title="Sin cuotas" description="No hay cuotas en el rango seleccionado." />
      )}

      {!loading && !error && items?.length > 0 && (
        <div className="space-y-3">
          {items.map(it => (
            <div key={it.id} className={`rounded-xl border border-gray-200 border-l-4 bg-white p-4 transition dark:border-gray-800 dark:bg-gray-900 ${urgency(it)}`}>
              <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div className="space-y-1">
                  <p className="text-sm font-semibold text-gray-900 dark:text-white">Contrato #{it.contrato_id} — Cuota #{it.numero_cuota}</p>
                  <div className="flex items-center gap-2 text-xs text-gray-500">
                    <CalendarDays className="h-3.5 w-3.5" />
                    <span>Vence: {it.fecha_vencimiento}</span>
                  </div>
                </div>
                <div className="flex items-center gap-4">
                  <div className="text-right">
                    <p className="text-lg font-bold text-gray-900 dark:text-white">${fmt(it.monto_total)}</p>
                    {it.monto_pagado > 0 && <p className="text-xs text-gray-500">Pagado: ${fmt(it.monto_pagado)}</p>}
                  </div>
                  <Badge status={it.estado} />
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

/* ════════════════════════════════════════════════════
   REPORTAR PAGO
   ════════════════════════════════════════════════════ */
function ReportarPago() {
  const { data: contracts } = useFetch('/cliente/api/contracts', [])
  const [selectedContract, setSelectedContract] = useState('')
  const [cuotas, setCuotas] = useState([])
  const [loadingCuotas, setLoadingCuotas] = useState(false)

  const schema = z.object({
    contrato_id: z.string().min(1, 'Selecciona un contrato'),
    cuota_id: z.string().min(1, 'Selecciona una cuota'),
    metodo: z.enum(['pago_movil', 'transferencia', 'efectivo', 'punto_de_venta'], { required_error: 'Selecciona un método' }),
    referencia: z.string().min(4, 'Mínimo 4 caracteres'),
    monto: z.coerce.number().positive('Monto debe ser mayor a 0'),
    notas: z.string().optional(),
  })

  const { register, handleSubmit, setValue, watch, reset, formState: { errors, isSubmitting } } = useForm({ resolver: zodResolver(schema) })
  const watchContrato = watch('contrato_id')

  useEffect(() => {
    if (!watchContrato) { setCuotas([]); return }
    setLoadingCuotas(true)
    fetch(`/cliente/api/contracts/${watchContrato}`, { credentials: 'same-origin' })
      .then(r => r.json())
      .then(json => {
        const pending = (json.data?.plan_pagos || []).filter(p => p.estado !== 'pagado')
        setCuotas(pending)
      })
      .catch(() => setCuotas([]))
      .finally(() => setLoadingCuotas(false))
  }, [watchContrato])

  const onSubmit = async (data) => {
    await new Promise(r => setTimeout(r, 800))
    toast.success('Pago reportado exitosamente. Será revisado por administración.')
    reset()
    setCuotas([])
  }

  const inputClass = "w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900"
  const labelClass = "block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
  const errClass = "mt-1 text-xs text-red-600"

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Reportar Pago</h2>
        <p className="text-sm text-gray-500">Registra la referencia de tu pago para validación</p>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div className="space-y-5">
          <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
              <label className={labelClass}>Contrato</label>
              <select {...register('contrato_id')} className={inputClass}>
                <option value="">Seleccionar...</option>
                {contracts?.filter(c => c.estado === 'activo' || c.estado === 'mora').map(c => (
                  <option key={c.id} value={c.id}>{c.numero}</option>
                ))}
              </select>
              {errors.contrato_id && <p className={errClass}>{errors.contrato_id.message}</p>}
            </div>
            <div>
              <label className={labelClass}>Cuota</label>
              <select {...register('cuota_id')} className={inputClass} disabled={loadingCuotas || cuotas.length === 0}>
                <option value="">{loadingCuotas ? 'Cargando...' : 'Seleccionar...'}</option>
                {cuotas.map(c => (
                  <option key={c.id} value={c.id}>#{c.n} — ${fmt(c.monto_total)} ({c.estado})</option>
                ))}
              </select>
              {errors.cuota_id && <p className={errClass}>{errors.cuota_id.message}</p>}
            </div>
          </div>

          <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
              <label className={labelClass}>Método de Pago</label>
              <select {...register('metodo')} className={inputClass}>
                <option value="">Seleccionar...</option>
                <option value="pago_movil">Pago Móvil</option>
                <option value="transferencia">Transferencia</option>
                <option value="efectivo">Efectivo</option>
                <option value="punto_de_venta">Punto de Venta</option>
              </select>
              {errors.metodo && <p className={errClass}>{errors.metodo.message}</p>}
            </div>
            <div>
              <label className={labelClass}>Referencia</label>
              <input {...register('referencia')} placeholder="Ej: 00012345" className={inputClass} />
              {errors.referencia && <p className={errClass}>{errors.referencia.message}</p>}
            </div>
          </div>

          <div>
            <label className={labelClass}>Monto ($)</label>
            <input type="number" step="0.01" {...register('monto')} placeholder="0.00" className={inputClass} />
            {errors.monto && <p className={errClass}>{errors.monto.message}</p>}
          </div>

          <div>
            <label className={labelClass}>Notas (opcional)</label>
            <textarea {...register('notas')} rows={3} placeholder="Información adicional..." className={inputClass + ' resize-none'} />
          </div>

          <div className="flex flex-col gap-3 border-t border-gray-100 pt-5 dark:border-gray-800 sm:flex-row sm:justify-end">
            <button type="button" onClick={() => { reset(); setCuotas([]) }} className="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
              Limpiar
            </button>
            <button type="submit" disabled={isSubmitting} className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-50">
              {isSubmitting ? <><Loader2 className="h-4 w-4 animate-spin" /> Enviando...</> : <><Send className="h-4 w-4" /> Reportar Pago</>}
            </button>
          </div>
        </div>
      </form>
    </div>
  )
}

/* ════════════════════════════════════════════════════
   PERFIL
   ════════════════════════════════════════════════════ */
function Perfil() {
  const [profile, setProfile] = useState(null)
  const [sessions, setSessions] = useState([])
  const [loading, setLoading] = useState(true)
  const [tab, setTab] = useState('datos')

  useEffect(() => {
    Promise.all([
      apiFetch('/cliente/api/me').then(r => r.json()),
      apiFetch('/cliente/api/me/sessions').then(r => r.json()),
    ]).then(([me, sess]) => {
      setProfile(me.data)
      setSessions(sess.data || [])
    }).finally(() => setLoading(false))
  }, [])

  // Profile form
  const [saving, setSaving] = useState(false)
  const [profileForm, setProfileForm] = useState({ name: '', email: '', telefono: '', direccion: '' })
  useEffect(() => {
    if (profile) setProfileForm({
      name: profile.user?.name || '',
      email: profile.user?.email || '',
      telefono: profile.cliente?.telefono || '',
      direccion: profile.cliente?.direccion || '',
    })
  }, [profile])

  const saveProfile = async (e) => {
    e.preventDefault()
    setSaving(true)
    try {
      const resp = await apiFetch('/cliente/api/me', { method: 'PUT', body: JSON.stringify(profileForm) })
      const json = await resp.json()
      if (!resp.ok) { toast.error(json.message || 'Error al guardar'); return }
      toast.success(json.message || 'Datos actualizados')
      window.__USER__ = { ...window.__USER__, name: profileForm.name, email: profileForm.email, initials: profileForm.name.charAt(0).toUpperCase() }
    } catch { toast.error('Error de conexión') }
    finally { setSaving(false) }
  }

  // Password form
  const [pwForm, setPwForm] = useState({ current_password: '', password: '', password_confirmation: '' })
  const [savingPw, setSavingPw] = useState(false)
  const savePassword = async (e) => {
    e.preventDefault()
    if (pwForm.password !== pwForm.password_confirmation) { toast.error('Las contraseñas no coinciden'); return }
    setSavingPw(true)
    try {
      const resp = await apiFetch('/cliente/api/me/password', { method: 'PUT', body: JSON.stringify(pwForm) })
      const json = await resp.json()
      if (!resp.ok) { toast.error(json.message || 'Error al cambiar contraseña'); return }
      toast.success(json.message || 'Contraseña actualizada')
      setPwForm({ current_password: '', password: '', password_confirmation: '' })
    } catch { toast.error('Error de conexión') }
    finally { setSavingPw(false) }
  }

  const tabs = [
    { id: 'datos', label: 'Mis Datos', icon: User },
    { id: 'seguridad', label: 'Seguridad', icon: Shield },
    { id: 'accesos', label: 'Últimos Accesos', icon: Monitor },
  ]

  const inputClass = "w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900"
  const labelClass = "block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"

  if (loading) return <Spinner />

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Mi Perfil</h2>
        <p className="text-sm text-gray-500">Administra tu información personal y seguridad</p>
      </div>

      {/* User card */}
      <div className="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div className="flex flex-col items-center gap-4 sm:flex-row">
          <div className="flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white">
            {profile?.user?.name?.charAt(0)?.toUpperCase() || 'C'}
          </div>
          <div className="text-center sm:text-left">
            <h3 className="text-lg font-bold text-gray-900 dark:text-white">{profile?.user?.name}</h3>
            <p className="text-sm text-gray-500">{profile?.user?.email}</p>
            {profile?.cliente?.documento && (
              <p className="mt-0.5 text-xs text-gray-400">{profile?.cliente?.tipo_documento}: {profile?.cliente?.documento}</p>
            )}
            <p className="mt-1 text-xs text-gray-400">Miembro desde: {profile?.user?.created_at}</p>
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex gap-1 rounded-xl border border-gray-200 bg-gray-50 p-1 dark:border-gray-800 dark:bg-gray-900">
        {tabs.map(t => (
          <button key={t.id} onClick={() => setTab(t.id)}
            className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition ${tab === t.id ? 'bg-white text-blue-600 shadow-sm dark:bg-gray-800 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'}`}>
            <t.icon className="h-4 w-4" />
            <span className="hidden sm:inline">{t.label}</span>
          </button>
        ))}
      </div>

      {/* Tab: Datos */}
      {tab === 'datos' && (
        <form onSubmit={saveProfile} className="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
          <h3 className="mb-5 text-base font-semibold text-gray-900 dark:text-white">Información Personal</h3>
          <div className="space-y-5">
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
              <div>
                <label className={labelClass}><User className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Nombre completo</label>
                <input value={profileForm.name} onChange={e => setProfileForm(p => ({ ...p, name: e.target.value }))} className={inputClass} required />
              </div>
              <div>
                <label className={labelClass}><Mail className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Correo electrónico</label>
                <input type="email" value={profileForm.email} onChange={e => setProfileForm(p => ({ ...p, email: e.target.value }))} className={inputClass} required />
              </div>
            </div>
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
              <div>
                <label className={labelClass}><Phone className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Teléfono</label>
                <input value={profileForm.telefono} onChange={e => setProfileForm(p => ({ ...p, telefono: e.target.value }))} className={inputClass} placeholder="04XX-XXXXXXX" />
              </div>
              <div>
                <label className={labelClass}><MapPin className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Dirección</label>
                <input value={profileForm.direccion} onChange={e => setProfileForm(p => ({ ...p, direccion: e.target.value }))} className={inputClass} placeholder="Ciudad, Estado" />
              </div>
            </div>
            {profile?.cliente && (
              <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label className={labelClass}><IdCard className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Documento</label>
                  <input value={`${profile.cliente.tipo_documento || 'CI'}: ${profile.cliente.documento || ''}`} className={inputClass + ' bg-gray-50 dark:bg-gray-800'} disabled />
                </div>
                <div>
                  <label className={labelClass}><User className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Nombre en contrato</label>
                  <input value={`${profile.cliente.nombre || ''} ${profile.cliente.apellido || ''}`} className={inputClass + ' bg-gray-50 dark:bg-gray-800'} disabled />
                </div>
              </div>
            )}
            <div className="flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">
              <button type="submit" disabled={saving} className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-50">
                {saving ? <Loader2 className="h-4 w-4 animate-spin" /> : <Save className="h-4 w-4" />} Guardar Cambios
              </button>
            </div>
          </div>
        </form>
      )}

      {/* Tab: Seguridad */}
      {tab === 'seguridad' && (
        <form onSubmit={savePassword} className="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
          <h3 className="mb-5 text-base font-semibold text-gray-900 dark:text-white">Cambiar Contraseña</h3>
          <div className="max-w-md space-y-5">
            <div>
              <label className={labelClass}><KeyRound className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Contraseña actual</label>
              <input type="password" value={pwForm.current_password} onChange={e => setPwForm(p => ({ ...p, current_password: e.target.value }))} className={inputClass} required />
            </div>
            <div>
              <label className={labelClass}><Shield className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Nueva contraseña</label>
              <input type="password" value={pwForm.password} onChange={e => setPwForm(p => ({ ...p, password: e.target.value }))} className={inputClass} required minLength={8} />
              <p className="mt-1 text-xs text-gray-400">Mínimo 8 caracteres</p>
            </div>
            <div>
              <label className={labelClass}><Shield className="mb-0.5 mr-1 inline h-3.5 w-3.5" />Confirmar nueva contraseña</label>
              <input type="password" value={pwForm.password_confirmation} onChange={e => setPwForm(p => ({ ...p, password_confirmation: e.target.value }))} className={inputClass} required minLength={8} />
            </div>
            <div className="flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">
              <button type="submit" disabled={savingPw} className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-50">
                {savingPw ? <Loader2 className="h-4 w-4 animate-spin" /> : <KeyRound className="h-4 w-4" />} Cambiar Contraseña
              </button>
            </div>
          </div>
        </form>
      )}

      {/* Tab: Accesos */}
      {tab === 'accesos' && (
        <div className="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
          <div className="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <h3 className="font-semibold text-gray-900 dark:text-white">Últimos Accesos al Sistema</h3>
            <p className="text-xs text-gray-500 mt-0.5">Últimas 10 sesiones registradas</p>
          </div>
          {sessions.length === 0 ? (
            <p className="px-5 py-8 text-center text-sm text-gray-500">No hay sesiones registradas</p>
          ) : (
            <>
              {/* Mobile */}
              <div className="divide-y divide-gray-100 dark:divide-gray-800 lg:hidden">
                {sessions.map(s => (
                  <div key={s.id} className="px-5 py-3">
                    <div className="flex items-center justify-between mb-1">
                      <span className="text-sm font-medium text-gray-900 dark:text-white">{s.ip_address}</span>
                      <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ${s.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'}`}>
                        {s.is_active ? 'Activa' : 'Finalizada'}
                      </span>
                    </div>
                    <p className="text-xs text-gray-500">{s.login_at}</p>
                    {s.location && <p className="text-xs text-gray-400 mt-0.5"><MapPin className="inline h-3 w-3 mr-0.5" />{s.location}</p>}
                    <p className="text-xs text-gray-400 mt-0.5 truncate">{s.user_agent}</p>
                  </div>
                ))}
              </div>
              {/* Desktop */}
              <div className="hidden lg:block">
                <table className="w-full">
                  <thead>
                    <tr className="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                      <th className="px-5 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">IP</th>
                      <th className="px-5 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Fecha</th>
                      <th className="px-5 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Ubicación</th>
                      <th className="px-5 py-2.5 text-left text-xs font-semibold uppercase text-gray-500">Dispositivo</th>
                      <th className="px-5 py-2.5 text-center text-xs font-semibold uppercase text-gray-500">Estado</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                    {sessions.map(s => (
                      <tr key={s.id} className="transition hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td className="px-5 py-2.5 text-sm font-mono text-gray-900 dark:text-white">{s.ip_address}</td>
                        <td className="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-400">{s.login_at}</td>
                        <td className="px-5 py-2.5 text-sm text-gray-600 dark:text-gray-400">{s.location || '-'}</td>
                        <td className="px-5 py-2.5 text-sm text-gray-500 max-w-[200px] truncate">{s.user_agent}</td>
                        <td className="px-5 py-2.5 text-center">
                          <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ${s.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'}`}>
                            {s.is_active ? 'Activa' : 'Finalizada'}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </>
          )}
        </div>
      )}
    </div>
  )
}

/* ════════════════════════════════════════════════════
   APP ROOT
   ════════════════════════════════════════════════════ */
export default function App() {
  return (
    <BrowserRouter basename="/cliente/app">
      <Routes>
        <Route element={<Layout />}>
          <Route path="/" element={<Home />} />
          <Route path="/contratos" element={<Contratos />} />
          <Route path="/contratos/:id" element={<ContratoDetalle />} />
          <Route path="/cuotas" element={<Cuotas />} />
          <Route path="/pagos" element={<ReportarPago />} />
          <Route path="/perfil" element={<Perfil />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
      <Toaster position="top-right" richColors />
    </BrowserRouter>
  )
}
