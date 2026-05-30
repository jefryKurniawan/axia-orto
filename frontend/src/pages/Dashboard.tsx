import { useDashboard } from '../hooks/useDashboard'
import { useCountUp } from '../hooks/useCountUp'
import { Card, CardBody } from '../components/ui/Card'
import { StatusBadge } from '../components/ui/Badge'
import { Skeleton } from '../components/ui/Skeleton'
import { RevenueTrendChart, ConsultationTrendChart, OrderStatusChart, ProductionPipelineChart } from '../components/charts/DashboardCharts'
import { CalendarCheck, Users, UserCheck, UserPlus, ClipboardList, AlertTriangle, Package, TrendingUp, BarChart3, PieChart as PieChartIcon, Factory } from 'lucide-react'
import type { LucideIcon } from 'lucide-react'

function StatCard({ label, value, color, icon: Icon }: { label: string; value: number; color: string; icon: LucideIcon }) {
  const displayValue = useCountUp(value)
  const textColor = color.split(' ')[0]
  const bgColor = color.split(' ')[1]

  return (
    <Card>
      <CardBody>
        <div className="flex items-center justify-between">
          <p className="text-sm text-slate-500 dark:text-slate-400">{label}</p>
          <div className={`p-2 rounded-lg ${bgColor}`}>
            <Icon className={`w-5 h-5 ${textColor}`} />
          </div>
        </div>
        <p className={`text-3xl font-bold mt-2 tabular-nums ${textColor}`}>{displayValue}</p>
      </CardBody>
    </Card>
  )
}

function StatusRow({ label, value, total, color }: { label: string; value: number; total: number; color: string }) {
  const displayValue = useCountUp(value, 800)
  const pct = Math.round((value / total) * 100)

  return (
    <div className="space-y-1">
      <div className="flex justify-between items-center">
        <span className="text-sm text-slate-600 dark:text-slate-400">{label}</span>
        <span className="font-medium tabular-nums text-slate-900 dark:text-slate-100">{displayValue}</span>
      </div>
      <div className="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
        <div
          className={`h-full rounded-full transition-all duration-1000 ease-out ${color}`}
          style={{ width: `${pct}%` }}
        />
      </div>
    </div>
  )
}

export default function Dashboard() {
  const { data, isLoading, error } = useDashboard()

  if (isLoading) {
    return (
      <div className="space-y-6">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Dashboard</h1>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          {Array.from({ length: 4 }).map((_, i) => (
            <Card key={i}><CardBody><Skeleton className="h-20" /></CardBody></Card>
          ))}
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {Array.from({ length: 4 }).map((_, i) => (
            <Card key={`chart-${i}`}><CardBody><Skeleton className="h-64" /></CardBody></Card>
          ))}
        </div>
      </div>
    )
  }

  if (error || !data) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400">Gagal memuat dashboard</p>
      </div>
    )
  }

  const stats = [
    { label: 'Konsultasi Hari Ini', value: data.today.total, color: 'text-blue-600 bg-blue-50', icon: CalendarCheck },
    { label: 'Total Pasien', value: data.total_patients, color: 'text-green-600 bg-green-50', icon: Users },
    { label: 'Dokter Aktif', value: data.active_doctors, color: 'text-purple-600 bg-purple-50', icon: UserCheck },
    { label: 'Pasien Baru (Bulan Ini)', value: data.new_patients_month, color: 'text-orange-600 bg-orange-50', icon: UserPlus },
    { label: 'Stok Rendah', value: data.low_stock_count, color: 'text-red-600 bg-red-50', icon: AlertTriangle },
  ]

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Dashboard</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {stats.map((stat) => (
          <StatCard key={stat.label} {...stat} />
        ))}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <ClipboardList className="w-5 h-5 text-slate-500 dark:text-slate-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Status Konsultasi Hari Ini</h2>
            </div>
            <div className="space-y-3">
              {[
                { label: 'Dijadwalkan', value: data.today.scheduled, color: 'bg-yellow-400' },
                { label: 'Berlangsung', value: data.today.in_progress, color: 'bg-blue-500' },
                { label: 'Selesai', value: data.today.completed, color: 'bg-green-500' },
                { label: 'Dibatalkan', value: data.today.cancelled, color: 'bg-red-400' },
              ].map((item) => (
                <StatusRow key={item.label} {...item} total={data.today.total || 1} />
              ))}
            </div>
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <CalendarCheck className="w-5 h-5 text-slate-500 dark:text-slate-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Konsultasi Terbaru</h2>
            </div>
            {data.recent_consultations.length === 0 ? (
              <p className="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Belum ada konsultasi</p>
            ) : (
              <div className="space-y-3">
                {data.recent_consultations.map((c) => (
                  <div key={c.uuid} className="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800 last:border-0">
                    <div>
                      <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{c.patient_name}</p>
                      <p className="text-xs text-slate-500 dark:text-slate-400">{c.complaint}</p>
                    </div>
                    <StatusBadge status={c.status} />
                  </div>
                ))}
              </div>
            )}
          </CardBody>
        </Card>
      </div>

      {/* Low stock alert */}
      {data.low_stock_items && data.low_stock_items.length > 0 && (
        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <Package className="w-5 h-5 text-orange-500 dark:text-orange-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Stok Rendah</h2>
            </div>
            <div className="space-y-3">
              {data.low_stock_items.map((item) => (
                <div key={item.uuid} className="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800 last:border-0">
                  <div>
                    <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{item.name}</p>
                    <p className="text-xs text-slate-500 dark:text-slate-400 font-mono">{item.code}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-bold text-orange-600 dark:text-orange-400">{item.quantity} {item.unit}</p>
                    <p className="text-xs text-slate-500 dark:text-slate-400">Min: {item.reorder_level}</p>
                  </div>
                </div>
              ))}
            </div>
          </CardBody>
        </Card>
      )}

      {/* Analytics Charts */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <TrendingUp className="w-5 h-5 text-green-500 dark:text-green-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Pendapatan 30 Hari</h2>
            </div>
            <RevenueTrendChart data={data.revenue_trend} />
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <BarChart3 className="w-5 h-5 text-blue-500 dark:text-blue-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Konsultasi 30 Hari</h2>
            </div>
            <ConsultationTrendChart data={data.consultation_trend} />
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <PieChartIcon className="w-5 h-5 text-purple-500 dark:text-purple-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Status Order</h2>
            </div>
            <OrderStatusChart data={data.order_status_distribution} />
          </CardBody>
        </Card>

        <Card>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <Factory className="w-5 h-5 text-orange-500 dark:text-orange-400" />
              <h2 className="font-semibold text-slate-900 dark:text-slate-100">Pipeline Produksi</h2>
            </div>
            <ProductionPipelineChart data={data.production_pipeline} />
          </CardBody>
        </Card>
      </div>
    </div>
  )
}
