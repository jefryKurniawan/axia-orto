import { useNavigate } from 'react-router-dom'
import { useDashboard } from '../hooks/useDashboard'
import { useCountUp } from '../hooks/useCountUp'
import { Card, CardBody, CardHeader } from '../components/ui/Card'
import { StatusBadge } from '../components/ui/Badge'
import { Button } from '../components/ui/Button'
import { StatSkeleton } from '../components/ui/Skeleton'
import { RevenueTrendChart, ConsultationTrendChart, OrderStatusChart, ProductionPipelineChart } from '../components/charts/DashboardCharts'
import {
  AlertTriangle, Plus, ArrowRight, CircleDot, Package,
} from 'lucide-react'
import type { DashboardStats } from '../types'

function ConsultationStatusBar({ label, value, total, color }: { label: string; value: number; total: number; color: string }) {
  const displayValue = useCountUp(value, 800)
  const pct = total > 0 ? Math.round((value / total) * 100) : 0

  return (
    <div className="flex items-center gap-3">
      <div className={`w-2 h-2 rounded-full ${color} flex-shrink-0`} />
      <span className="text-sm text-slate-600 dark:text-slate-400 flex-1">{label}</span>
      <span className="text-sm font-semibold tabular-nums text-slate-900 dark:text-slate-100 w-8 text-right">{displayValue}</span>
      <div className="w-20 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
        <div
          className={`h-full rounded-full transition-all duration-1000 ease-out ${color}`}
          style={{ width: `${pct}%` }}
        />
      </div>
      <span className="text-xs text-slate-400 dark:text-slate-500 w-8 text-right">{pct}%</span>
    </div>
  )
}

export default function Dashboard() {
  const { data, isLoading, error } = useDashboard()

  if (isLoading) {
    return (
      <div className="space-y-5">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard</h1>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <Card><CardBody><div className="h-40 animate-shimmer rounded-lg" /></CardBody></Card>
          <div className="lg:col-span-2 grid grid-cols-2 gap-3">
            {Array.from({ length: 4 }).map((_, i) => <StatSkeleton key={i} />)}
          </div>
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-6 gap-4">
          <Card className="lg:col-span-4"><CardBody><div className="h-64 animate-shimmer rounded-lg" /></CardBody></Card>
          <Card className="lg:col-span-2"><CardBody><div className="h-64 animate-shimmer rounded-lg" /></CardBody></Card>
        </div>
      </div>
    )
  }

  if (error || !data) {
    return (
      <div className="flex flex-col items-center justify-center py-20 gap-3">
        <AlertTriangle className="w-8 h-8 text-red-400" />
        <p className="text-sm text-red-600 dark:text-red-400">Gagal memuat dashboard</p>
        <Button variant="secondary" size="sm" onClick={() => window.location.reload()}>Coba lagi</Button>
      </div>
    )
  }

  return <DashboardContent data={data} />
}

function DashboardContent({ data }: { data: DashboardStats }) {
  const navigate = useNavigate()
  const todayTotal = useCountUp(data.today?.total ?? 0)
  const completionPct = (data.today?.total ?? 0) > 0
    ? Math.round(((data.today?.completed ?? 0) / (data.today?.total ?? 1)) * 100)
    : 0
  const totalRevenue = (data.revenue_trend ?? []).reduce((s, d) => s + Number(d.total_revenue), 0)

  return (
    <div className="space-y-5">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard</h1>
        <p className="text-xs text-slate-400 dark:text-slate-500">
          {new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
        </p>
      </div>

      {/* Stats: hero + satellite */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {/* Hero stat — today's consultations */}
        <Card accent="left" accentColor="border-l-blue-500" hover>
          <CardBody>
            <p className="text-xs font-medium text-slate-400 dark:text-slate-500 mb-1 uppercase tracking-wider">Konsultasi Hari Ini</p>
            <p className="text-5xl font-bold tracking-tight tabular-nums text-slate-900 dark:text-white">{todayTotal}</p>
            <p className="text-sm text-slate-500 dark:text-slate-400 mt-1">
              {data.today?.completed ?? 0} selesai dari {data.today?.total ?? 0} jadwal
            </p>
            <div className="mt-3 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
              <div
                className="h-full bg-blue-500 rounded-full transition-all duration-1000 ease-out"
                style={{ width: `${completionPct}%` }}
              />
            </div>
          </CardBody>
        </Card>

        {/* 4 satellite stats */}
        <div className="lg:col-span-2 grid grid-cols-2 gap-3">
          <div className="p-4 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p className="text-[11px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Pasien</p>
            <p className="text-2xl font-bold tabular-nums text-slate-900 dark:text-white mt-1">{data.total_patients ?? 0}</p>
            <p className="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">+{data.new_patients_month ?? 0} bulan ini</p>
          </div>
          <div className="p-4 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p className="text-[11px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Dokter Aktif</p>
            <p className="text-2xl font-bold tabular-nums text-slate-900 dark:text-white mt-1">{data.active_doctors ?? 0}</p>
          </div>
          <div className="p-4 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p className="text-[11px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pasien Baru</p>
            <p className="text-2xl font-bold tabular-nums text-amber-600 dark:text-amber-400 mt-1">{data.new_patients_month ?? 0}</p>
            <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">bulan ini</p>
          </div>
          <div className={`p-4 rounded-lg border shadow-sm ${
            (data.low_stock_count ?? 0) > 0
              ? 'bg-red-50/50 dark:bg-red-900/10 border-red-200 dark:border-red-800/40'
              : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700'
          }`}>
            <p className="text-[11px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wider">Stok Rendah</p>
            <p className={`text-2xl font-bold tabular-nums mt-1 ${
              (data.low_stock_count ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white'
            }`}>{data.low_stock_count ?? 0}</p>
            <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
              {(data.low_stock_count ?? 0) > 0 ? 'perlu restock' : 'semua aman'}
            </p>
          </div>
        </div>
      </div>

      {/* Quick Actions — visual hierarchy */}
      <div className="flex flex-wrap items-center gap-2 pt-1">
        <Button onClick={() => navigate('/consultations/create')}>
          <Plus className="w-3.5 h-3.5 mr-1.5" /> Konsultasi Baru
        </Button>
        <Button variant="secondary" size="sm" onClick={() => navigate('/patients/create')}>
          Tambah Pasien
        </Button>
        <Button variant="ghost" size="sm" onClick={() => navigate('/orders/create')}>
          Buat Order
        </Button>
      </div>

      {/* Consultation Status + Recent Activity */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {/* Consultation Status — data-forward header */}
        <Card accent="left" accentColor="border-l-blue-500" className="lg:col-span-2">
          <CardHeader variant="minimal">
            <div className="flex items-center justify-between">
              <div>
                <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Status Konsultasi</h2>
                <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Hari ini</p>
              </div>
              <span className="text-3xl font-bold tabular-nums text-slate-900 dark:text-white">{data.today?.total ?? 0}</span>
            </div>
          </CardHeader>
          <CardBody>
            <div className="space-y-3">
              <ConsultationStatusBar label="Dijadwalkan" value={data.today?.scheduled ?? 0} total={data.today?.total || 1} color="bg-amber-400" />
              <ConsultationStatusBar label="Berlangsung" value={data.today?.in_progress ?? 0} total={data.today?.total || 1} color="bg-blue-500" />
              <ConsultationStatusBar label="Selesai" value={data.today?.completed ?? 0} total={data.today?.total || 1} color="bg-emerald-500" />
              <ConsultationStatusBar label="Dibatalkan" value={data.today?.cancelled ?? 0} total={data.today?.total || 1} color="bg-red-400" />
            </div>
          </CardBody>
        </Card>

        {/* Recent Consultations Timeline */}
        <Card>
          <CardHeader variant="minimal">
            <div className="flex items-center justify-between">
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Konsultasi Terbaru</h2>
              <button
                onClick={() => navigate('/consultations')}
                className="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 transition-colors"
              >
                Lihat semua <ArrowRight className="w-3 h-3" />
              </button>
            </div>
          </CardHeader>
          <CardBody>
            {!(data.recent_consultations ?? []).length ? (
              <p className="text-sm text-slate-400 dark:text-slate-500 text-center py-6">Belum ada konsultasi</p>
            ) : (
              <div className="relative">
                <div className="absolute left-[7px] top-2 bottom-2 w-px bg-slate-200 dark:bg-slate-700" />
                <div className="space-y-4">
                  {(data.recent_consultations ?? []).slice(0, 5).map((c) => (
                    <div key={c.uuid} className="relative flex gap-3 pl-6">
                      <CircleDot className="absolute left-0 top-1 w-[15px] h-[15px] text-blue-500 bg-white dark:bg-slate-900" />
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center justify-between gap-2">
                          <p className="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{c.patient_name}</p>
                          <StatusBadge status={c.status} />
                        </div>
                        <p className="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5">{c.complaint}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </CardBody>
        </Card>
      </div>

      {/* Charts — asymmetric grid, no icons */}
      <div className="grid grid-cols-1 lg:grid-cols-6 gap-4 pt-2">
        <Card className="lg:col-span-4">
          <CardHeader variant="minimal">
            <div className="flex items-center justify-between">
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Pendapatan 30 Hari</h2>
              <span className="text-xs font-mono text-slate-400 dark:text-slate-500">
                Rp {totalRevenue.toLocaleString('id-ID')}
              </span>
            </div>
          </CardHeader>
          <CardBody>
            <RevenueTrendChart data={data.revenue_trend ?? []} />
          </CardBody>
        </Card>

        <Card className="lg:col-span-2">
          <CardHeader variant="minimal">
            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Konsultasi 30 Hari</h2>
          </CardHeader>
          <CardBody>
            <ConsultationTrendChart data={data.consultation_trend ?? []} />
          </CardBody>
        </Card>

        <Card className="lg:col-span-3">
          <CardHeader variant="minimal">
            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Status Order</h2>
          </CardHeader>
          <CardBody>
            <OrderStatusChart data={data.order_status_distribution ?? []} />
          </CardBody>
        </Card>

        <Card className="lg:col-span-3">
          <CardHeader variant="minimal">
            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Pipeline Produksi</h2>
          </CardHeader>
          <CardBody>
            <ProductionPipelineChart data={data.production_pipeline ?? []} />
          </CardBody>
        </Card>
      </div>

      {/* Low Stock — compact list */}
      {(data.low_stock_items ?? []).length > 0 && (
        <Card accent="top" accentColor="border-t-amber-500">
          <CardHeader variant="accent">
            <div className="flex items-center justify-between">
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">
                Stok Rendah
                <span className="ml-2 text-xs font-normal text-slate-400 dark:text-slate-500">
                  {(data.low_stock_items ?? []).length} item
                </span>
              </h2>
              <button
                onClick={() => navigate('/inventory')}
                className="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 transition-colors"
              >
                Lihat inventaris <ArrowRight className="w-3 h-3" />
              </button>
            </div>
          </CardHeader>
          <CardBody compact>
            <div className="divide-y divide-slate-100 dark:divide-slate-800">
              {(data.low_stock_items ?? []).map((item) => {
                const urgency = item.quantity <= Math.floor(item.reorder_level * 0.5)
                return (
                  <div key={item.uuid} className="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center gap-2">
                        <Package className="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 flex-shrink-0" />
                        <p className="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{item.name}</p>
                      </div>
                      <p className="text-xs text-slate-400 dark:text-slate-500 font-mono ml-5.5">{item.code}</p>
                    </div>
                    <div className="flex items-baseline gap-1.5 ml-4">
                      <span className={`text-sm font-bold tabular-nums ${urgency ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400'}`}>
                        {item.quantity}
                      </span>
                      <span className="text-xs text-slate-400 dark:text-slate-500">/ {item.reorder_level} {item.unit}</span>
                    </div>
                  </div>
                )
              })}
            </div>
          </CardBody>
        </Card>
      )}
    </div>
  )
}
