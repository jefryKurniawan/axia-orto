import { useNavigate } from 'react-router-dom'
import { useDashboard } from '../hooks/useDashboard'
import { useCountUp } from '../hooks/useCountUp'
import { Card, CardBody, CardHeader } from '../components/ui/Card'
import { StatusBadge } from '../components/ui/Badge'
import { Button } from '../components/ui/Button'
import { StatSkeleton } from '../components/ui/Skeleton'
import { RevenueTrendChart, ConsultationTrendChart, OrderStatusChart, ProductionPipelineChart } from '../components/charts/DashboardCharts'
import {
  CalendarCheck, Users, UserCheck, UserPlus, AlertTriangle,
  ClipboardList, Package, TrendingUp, BarChart3,
  PieChart as PieChartIcon, Factory, Plus, ArrowRight,
  CircleDot,
} from 'lucide-react'
import type { LucideIcon } from 'lucide-react'

interface StatCardProps {
  label: string
  value: number
  icon: LucideIcon
  accentColor: string
  iconColor: string
  subtitle?: string
}

function StatCard({ label, value, icon: Icon, accentColor, iconColor, subtitle }: StatCardProps) {
  const displayValue = useCountUp(value)

  return (
    <Card accent="left" accentColor={accentColor} hover>
      <CardBody compact>
        <div className="flex items-start justify-between">
          <div className="space-y-1">
            <p className="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">{label}</p>
            <p className={`text-3xl font-bold tracking-tight tabular-nums text-slate-900 dark:text-white`}>{displayValue}</p>
            {subtitle && (
              <p className="text-xs text-slate-400 dark:text-slate-500">{subtitle}</p>
            )}
          </div>
          <Icon className={`w-5 h-5 ${iconColor} flex-shrink-0 mt-0.5`} />
        </div>
      </CardBody>
    </Card>
  )
}

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
  const navigate = useNavigate()
  const { data, isLoading, error } = useDashboard()

  if (isLoading) {
    return (
      <div className="space-y-6">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard</h1>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
          {Array.from({ length: 5 }).map((_, i) => <StatSkeleton key={i} />)}
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-3">
          <Card className="lg:col-span-2"><CardBody><div className="h-64 animate-shimmer rounded-lg" /></CardBody></Card>
          <Card><CardBody><div className="h-64 animate-shimmer rounded-lg" /></CardBody></Card>
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

  const stats: StatCardProps[] = [
    { label: 'Konsultasi Hari Ini', value: data.today.total, icon: CalendarCheck, accentColor: 'border-l-blue-500', iconColor: 'text-blue-500', subtitle: `${data.today.completed} selesai` },
    { label: 'Total Pasien', value: data.total_patients, icon: Users, accentColor: 'border-l-emerald-500', iconColor: 'text-emerald-500', subtitle: `+${data.new_patients_month} bulan ini` },
    { label: 'Dokter Aktif', value: data.active_doctors, icon: UserCheck, accentColor: 'border-l-violet-500', iconColor: 'text-violet-500' },
    { label: 'Pasien Baru', value: data.new_patients_month, icon: UserPlus, accentColor: 'border-l-amber-500', iconColor: 'text-amber-500', subtitle: 'bulan ini' },
    { label: 'Stok Rendah', value: data.low_stock_count, icon: AlertTriangle, accentColor: 'border-l-red-500', iconColor: 'text-red-500', subtitle: data.low_stock_count > 0 ? 'perlu restock' : 'semua aman' },
  ]

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard</h1>
        <p className="text-xs text-slate-400 dark:text-slate-500">
          {new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
        </p>
      </div>

      {/* Stat Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
        {stats.map((stat, index) => (
          <div key={stat.label} className="animate-stagger-in" style={{ animationDelay: `${index * 60}ms` }}>
            <StatCard {...stat} />
          </div>
        ))}
      </div>

      {/* Quick Actions */}
      <div className="flex flex-wrap gap-2">
        <Button variant="subtle" size="sm" onClick={() => navigate('/patients/create')}>
          <Plus className="w-3.5 h-3.5 mr-1.5" /> Tambah Pasien
        </Button>
        <Button variant="subtle" size="sm" onClick={() => navigate('/consultations/create')}>
          <Plus className="w-3.5 h-3.5 mr-1.5" /> Tambah Konsultasi
        </Button>
        <Button variant="subtle" size="sm" onClick={() => navigate('/orders/create')}>
          <Plus className="w-3.5 h-3.5 mr-1.5" /> Tambah Order
        </Button>
      </div>

      {/* Main Content: Consultation Status + Recent Activity */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-3">
        {/* Consultation Status */}
        <Card accent="left" accentColor="border-l-blue-500" className="lg:col-span-2">
          <CardHeader>
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <ClipboardList className="w-4 h-4 text-slate-400" />
                <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Status Konsultasi Hari Ini</h2>
              </div>
              <span className="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{data.today.total}</span>
            </div>
          </CardHeader>
          <CardBody>
            <div className="space-y-3">
              <ConsultationStatusBar label="Dijadwalkan" value={data.today.scheduled} total={data.today.total || 1} color="bg-amber-400" />
              <ConsultationStatusBar label="Berlangsung" value={data.today.in_progress} total={data.today.total || 1} color="bg-blue-500" />
              <ConsultationStatusBar label="Selesai" value={data.today.completed} total={data.today.total || 1} color="bg-emerald-500" />
              <ConsultationStatusBar label="Dibatalkan" value={data.today.cancelled} total={data.today.total || 1} color="bg-red-400" />
            </div>
          </CardBody>
        </Card>

        {/* Recent Consultations Timeline */}
        <Card>
          <CardHeader>
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <CalendarCheck className="w-4 h-4 text-slate-400" />
                <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Konsultasi Terbaru</h2>
              </div>
              <button
                onClick={() => navigate('/consultations')}
                className="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 transition-colors"
              >
                Lihat semua <ArrowRight className="w-3 h-3" />
              </button>
            </div>
          </CardHeader>
          <CardBody>
            {data.recent_consultations.length === 0 ? (
              <p className="text-sm text-slate-400 dark:text-slate-500 text-center py-6">Belum ada konsultasi</p>
            ) : (
              <div className="relative">
                {/* Timeline line */}
                <div className="absolute left-[7px] top-2 bottom-2 w-px bg-slate-200 dark:bg-slate-700" />
                <div className="space-y-4">
                  {data.recent_consultations.slice(0, 5).map((c) => (
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

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <TrendingUp className="w-4 h-4 text-emerald-500" />
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Pendapatan 30 Hari</h2>
            </div>
          </CardHeader>
          <CardBody>
            <RevenueTrendChart data={data.revenue_trend} />
          </CardBody>
        </Card>

        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <BarChart3 className="w-4 h-4 text-blue-500" />
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Konsultasi 30 Hari</h2>
            </div>
          </CardHeader>
          <CardBody>
            <ConsultationTrendChart data={data.consultation_trend} />
          </CardBody>
        </Card>

        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <PieChartIcon className="w-4 h-4 text-violet-500" />
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Status Order</h2>
            </div>
          </CardHeader>
          <CardBody>
            <OrderStatusChart data={data.order_status_distribution} />
          </CardBody>
        </Card>

        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <Factory className="w-4 h-4 text-amber-500" />
              <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Pipeline Produksi</h2>
            </div>
          </CardHeader>
          <CardBody>
            <ProductionPipelineChart data={data.production_pipeline} />
          </CardBody>
        </Card>
      </div>

      {/* Low Stock Alerts */}
      {data.low_stock_items && data.low_stock_items.length > 0 && (
        <Card accent="top" accentColor="border-t-amber-500">
          <CardHeader>
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <Package className="w-4 h-4 text-amber-500" />
                <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Stok Rendah</h2>
              </div>
              <button
                onClick={() => navigate('/inventory')}
                className="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 transition-colors"
              >
                Lihat inventaris <ArrowRight className="w-3 h-3" />
              </button>
            </div>
          </CardHeader>
          <CardBody>
            <div className="flex gap-3 overflow-x-auto pb-1 -mx-1 px-1">
              {data.low_stock_items.map((item) => {
                const urgency = item.quantity <= Math.floor(item.reorder_level * 0.5) ? 'red' : 'amber'
                return (
                  <div
                    key={item.uuid}
                    className={`flex-shrink-0 w-48 p-3 rounded-lg border transition-colors ${
                      urgency === 'red'
                        ? 'border-red-200 dark:border-red-800/50 bg-red-50/50 dark:bg-red-900/10'
                        : 'border-amber-200 dark:border-amber-800/50 bg-amber-50/50 dark:bg-amber-900/10'
                    }`}
                  >
                    <p className="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{item.name}</p>
                    <p className="text-xs font-mono text-slate-400 dark:text-slate-500 mt-0.5">{item.code}</p>
                    <div className="flex items-end justify-between mt-2">
                      <div>
                        <p className={`text-lg font-bold tabular-nums ${urgency === 'red' ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400'}`}>
                          {item.quantity}
                        </p>
                        <p className="text-xs text-slate-400 dark:text-slate-500">{item.unit}</p>
                      </div>
                      <p className="text-xs text-slate-400 dark:text-slate-500">min: {item.reorder_level}</p>
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
