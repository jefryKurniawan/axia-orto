import { useState } from 'react'
import { Link } from 'react-router-dom'
import { BarChart3, Users, ClipboardList, CreditCard, Download, Loader2, AlertCircle, FileSpreadsheet } from 'lucide-react'
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
    title: 'Laporan Pendapatan',
    description: 'Ringkasan pendapatan harian berdasarkan metode pembayaran',
    icon: BarChart3,
    color: 'text-emerald-600 dark:text-emerald-400',
    bg: 'bg-emerald-50 dark:bg-emerald-900/20',
  },
  {
    type: 'patients',
    title: 'Laporan Pasien & Konsultasi',
    description: 'Data pasien baru dan riwayat konsultasi',
    icon: Users,
    color: 'text-blue-600 dark:text-blue-400',
    bg: 'bg-blue-50 dark:bg-blue-900/20',
  },
  {
    type: 'orders',
    title: 'Laporan Order & Produksi',
    description: 'Status order perawatan dan tracking produksi',
    icon: ClipboardList,
    color: 'text-violet-600 dark:text-violet-400',
    bg: 'bg-violet-50 dark:bg-violet-900/20',
  },
  {
    type: 'payments',
    title: 'Laporan Pembayaran',
    description: 'Transaksi pembayaran masuk dan outstanding',
    icon: CreditCard,
    color: 'text-amber-600 dark:text-amber-400',
    bg: 'bg-amber-50 dark:bg-amber-900/20',
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
    <div className="space-y-6">
      {/* Breadcrumb */}
      <nav className="text-sm text-slate-500 dark:text-slate-400">
        <Link to="/dashboard" className="hover:text-blue-600 dark:hover:text-blue-400">Dashboard</Link>
        <span className="mx-2">/</span>
        <span className="text-slate-900 dark:text-slate-100">Laporan</span>
      </nav>

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Laporan</h1>
      </div>

      {/* Date Range Filter */}
      <Card>
        <CardBody>
          <div className="flex flex-col sm:flex-row gap-3 items-end">
            <div className="flex-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Dari Tanggal</label>
              <input
                type="date"
                value={dateFrom}
                onChange={(e) => setDateFrom(e.target.value)}
                className="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"
              />
            </div>
            <div className="flex-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal</label>
              <input
                type="date"
                value={dateTo}
                onChange={(e) => setDateTo(e.target.value)}
                className="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-900 dark:text-slate-100"
              />
            </div>
          </div>
        </CardBody>
      </Card>

      {/* Report Type Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {reportTypes.map((report) => {
          const Icon = report.icon
          return (
            <Card key={report.type}>
              <CardBody>
                <div className="flex items-start gap-4">
                  <div className={`p-3 rounded-xl ${report.bg}`}>
                    <Icon className={`w-6 h-6 ${report.color}`} />
                  </div>
                  <div className="flex-1 min-w-0">
                    <h3 className="font-semibold text-slate-900 dark:text-slate-100">{report.title}</h3>
                    <p className="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{report.description}</p>
                    <Button
                      size="sm"
                      className="mt-3"
                      onClick={() => handleExport(report.type)}
                      loading={createExport.isPending}
                    >
                      <FileSpreadsheet className="w-4 h-4 mr-1.5" />
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
          <h2 className="font-semibold text-slate-900 dark:text-slate-100">Riwayat Export</h2>
        </CardHeader>
        <CardBody>
          {exportsLoading ? (
            <TableSkeleton rows={3} />
          ) : !exportsData?.data.length ? (
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Belum ada export</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Tipe</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Tanggal</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Status</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {exportsData.data.map((job) => (
                      <tr key={job.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="text-center py-3 px-2 text-slate-900 dark:text-slate-100">
                          {reportTypes.find((r) => r.type === job.report_type)?.title ?? job.report_type}
                        </td>
                        <td className="text-center py-3 px-2 text-slate-600 dark:text-slate-400">
                          {new Date(job.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </td>
                        <td className="text-center py-3 px-2">
                          <StatusBadge status={job.status} />
                        </td>
                        <td className="text-center py-3 px-2">
                          {job.status === 'completed' ? (
                            <button
                              onClick={() => handleDownload(job)}
                              className="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline"
                            >
                              <Download className="w-4 h-4" />
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
              <div className="block sm:hidden space-y-3">
                {exportsData.data.map((job) => (
                  <div key={job.uuid} className="bg-slate-50 dark:bg-slate-800 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="font-medium text-slate-900 dark:text-slate-100 text-sm">
                        {reportTypes.find((r) => r.type === job.report_type)?.title ?? job.report_type}
                      </span>
                      <StatusBadge status={job.status} />
                    </div>
                    <div className="text-xs text-slate-500 dark:text-slate-400">
                      {new Date(job.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </div>
                    {job.status === 'completed' && (
                      <button
                        onClick={() => handleDownload(job)}
                        className="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 text-sm hover:underline"
                      >
                        <Download className="w-4 h-4" />
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
              {exportsData.meta.last_page > 1 && (
                <div className="flex items-center justify-between mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => setPage((p) => Math.max(1, p - 1))}
                    disabled={page === 1}
                  >
                    Sebelumnya
                  </Button>
                  <span className="text-sm text-slate-600 dark:text-slate-400">
                    Halaman {exportsData.meta.current_page} dari {exportsData.meta.last_page}
                  </span>
                  <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => setPage((p) => Math.min(exportsData.meta.last_page, p + 1))}
                    disabled={page === exportsData.meta.last_page}
                  >
                    Selanjutnya
                  </Button>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>
    </div>
  )
}
