import { useState } from 'react'
import { Link } from 'react-router-dom'
import { BarChart3, Users, ClipboardList, CreditCard, Download, Loader2, AlertCircle, FileSpreadsheet, FileBarChart } from 'lucide-react'
import { useExportJobs, useCreateExport } from '../../hooks/useReports'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { StatusBadge } from '../../components/ui/Badge'
import { TableSkeleton } from '../../components/ui/Skeleton'
import type { ExportJob } from '../../types'

const reportTypes = [
  {
    type: 'revenue',
    title: 'Pendapatan',
    description: 'Ringkasan pendapatan harian berdasarkan metode pembayaran',
    icon: BarChart3,
    accentColor: 'border-l-emerald-500',
    iconColor: 'text-emerald-500',
  },
  {
    type: 'patients',
    title: 'Pasien & Konsultasi',
    description: 'Data pasien baru dan riwayat konsultasi',
    icon: Users,
    accentColor: 'border-l-blue-500',
    iconColor: 'text-blue-500',
  },
  {
    type: 'orders',
    title: 'Order & Produksi',
    description: 'Status order perawatan dan tracking produksi',
    icon: ClipboardList,
    accentColor: 'border-l-violet-500',
    iconColor: 'text-violet-500',
  },
  {
    type: 'payments',
    title: 'Pembayaran',
    description: 'Transaksi pembayaran masuk dan outstanding',
    icon: CreditCard,
    accentColor: 'border-l-amber-500',
    iconColor: 'text-amber-500',
  },
] as const

export default function ReportsPage() {
  const [page, setPage] = useState(1)
  const [dateFrom, setDateFrom] = useState(() => {
    const d = new Date()
    d.setDate(d.getDate() - 30)
    return d.toISOString().split('T')[0]
  })
  const [dateTo, setDateTo] = useState(() => new Date().toISOString().split('T')[0])

  const { data: exportsData, isLoading: exportsLoading } = useExportJobs(page)
  const createExport = useCreateExport()
  const addToast = useToastStore((s) => s.addToast)

  const handleExport = (reportType: string) => {
    createExport.mutate(
      { report_type: reportType, date_from: dateFrom, date_to: dateTo },
      {
        onSuccess: () => addToast('success', 'Export sedang diproses. Silakan tunggu.'),
        onError: () => addToast('error', 'Gagal membuat export.'),
      }
    )
  }

  const handleDownload = (job: ExportJob) => {
    window.open(`/api/exports/${job.uuid}/download`, '_blank')
  }

  return (
    <div className="space-y-5">
      {/* Breadcrumb */}
      <nav className="text-xs text-slate-400 dark:text-slate-500">
        <Link to="/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Dashboard</Link>
        <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
        <span className="text-slate-600 dark:text-slate-300">Laporan</span>
      </nav>

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Laporan</h1>
      </div>

      {/* Date Range Filter — compact inline */}
      <Card>
        <CardBody compact>
          <div className="flex flex-col sm:flex-row gap-3 items-end">
            <div className="flex-1">
              <label className="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Dari</label>
              <input
                type="date"
                value={dateFrom}
                onChange={(e) => setDateFrom(e.target.value)}
                className="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
              />
            </div>
            <div className="flex-1">
              <label className="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sampai</label>
              <input
                type="date"
                value={dateTo}
                onChange={(e) => setDateTo(e.target.value)}
                className="w-full rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
              />
            </div>
          </div>
        </CardBody>
      </Card>

      {/* Report Type Cards — horizontal layout */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        {reportTypes.map((report) => {
          const Icon = report.icon
          return (
            <Card key={report.type} accent="left" accentColor={report.accentColor} hover>
              <CardBody compact>
                <div className="flex items-start gap-3">
                  <Icon className={`w-5 h-5 ${report.iconColor} flex-shrink-0 mt-0.5`} />
                  <div className="flex-1 min-w-0">
                    <h3 className="text-sm font-semibold text-slate-900 dark:text-slate-100">{report.title}</h3>
                    <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{report.description}</p>
                    <Button
                      size="sm"
                      variant="subtle"
                      className="mt-2"
                      onClick={() => handleExport(report.type)}
                      loading={createExport.isPending}
                    >
                      <FileSpreadsheet className="w-3.5 h-3.5 mr-1.5" />
                      Export CSV
                    </Button>
                  </div>
                </div>
              </CardBody>
            </Card>
          )
        })}
      </div>

      {/* Recent Exports */}
      <Card>
        <CardHeader>
          <div className="flex items-center gap-2">
            <FileBarChart className="w-4 h-4 text-slate-400" />
            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wide">Riwayat Export</h2>
          </div>
        </CardHeader>
        <CardBody>
          {exportsLoading ? (
            <TableSkeleton rows={3} columns={4} />
          ) : !exportsData?.data?.length ? (
            <div className="flex flex-col items-center py-12 gap-3">
              <FileSpreadsheet className="w-10 h-10 text-slate-300 dark:text-slate-600" />
              <p className="text-sm text-slate-500 dark:text-slate-400">Belum ada export</p>
            </div>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tipe</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tanggal</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Status</th>
                      <th className="text-center py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                    {exportsData?.data?.map((job, i) => (
                      <tr key={job.uuid} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors animate-row-enter" style={{ animationDelay: `${i * 30}ms` }}>
                        <td className="py-3.5 px-4 font-medium text-slate-900 dark:text-slate-100">
                          {reportTypes.find((r) => r.type === job.report_type)?.title ?? job.report_type}
                        </td>
                        <td className="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                          {new Date(job.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </td>
                        <td className="py-3.5 px-4">
                          <StatusBadge status={job.status} />
                        </td>
                        <td className="py-3.5 px-4 text-center">
                          {job.status === 'completed' ? (
                            <button
                              onClick={() => handleDownload(job)}
                              className="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors"
                            >
                              <Download className="w-3.5 h-3.5" />
                              Download
                            </button>
                          ) : job.status === 'processing' || job.status === 'pending' ? (
                            <Loader2 className="w-4 h-4 animate-spin text-slate-400 mx-auto" />
                          ) : job.status === 'failed' ? (
                            <span className="text-red-500 dark:text-red-400 text-xs flex items-center gap-1 justify-center">
                              <AlertCircle className="w-3.5 h-3.5" />
                              {job.error_message ?? 'Gagal'}
                            </span>
                          ) : null}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile cards */}
              <div className="block sm:hidden space-y-2">
                {exportsData?.data?.map((job) => (
                  <div key={job.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <div className="flex items-center justify-between">
                      <span className="text-sm font-medium text-slate-900 dark:text-slate-100">
                        {reportTypes.find((r) => r.type === job.report_type)?.title ?? job.report_type}
                      </span>
                      <StatusBadge status={job.status} />
                    </div>
                    <p className="text-xs text-slate-400 dark:text-slate-500">
                      {new Date(job.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </p>
                    {job.status === 'completed' && (
                      <button
                        onClick={() => handleDownload(job)}
                        className="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 text-xs font-medium hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
                      >
                        <Download className="w-3.5 h-3.5" />
                        Download CSV
                      </button>
                    )}
                    {job.status === 'failed' && (
                      <p className="text-red-500 dark:text-red-400 text-xs">{job.error_message ?? 'Export gagal'}</p>
                    )}
                  </div>
                ))}
              </div>

              {/* Pagination */}
              {exportsData?.meta && exportsData.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-xs text-slate-400 dark:text-slate-500 text-center sm:text-left">
                    Halaman {exportsData.meta.current_page} dari {exportsData.meta.last_page}
                  </p>
                  <div className="flex items-center justify-center gap-1">
                    <Button size="sm" variant="ghost" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>
                      &laquo;
                    </Button>
                    {Array.from({ length: exportsData.meta.last_page }, (_, i) => i + 1).map((p) => (
                      <button
                        key={p}
                        onClick={() => setPage(p)}
                        className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
                          p === page
                            ? 'bg-blue-600 text-white'
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                        }`}
                      >
                        {p}
                      </button>
                    ))}
                    <Button size="sm" variant="ghost" disabled={page >= exportsData.meta.last_page} onClick={() => setPage((p) => p + 1)}>
                      &raquo;
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>
    </div>
  )
}
