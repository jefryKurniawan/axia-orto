import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Pencil, Trash2, Search, X, Factory, AlertCircle } from 'lucide-react'
import { useProductionList, useDeleteProductionTracking } from '../../hooks/useProduction'
import { useToastStore } from '../../stores/toastStore'
import { useDebounce } from '../../hooks/useDebounce'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { TableSkeleton } from '../../components/ui/Skeleton'

const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'pending', label: 'Pending' },
  { value: 'in_progress', label: 'Sedang Dikerjakan' },
  { value: 'completed', label: 'Selesai' },
  { value: 'cancelled', label: 'Dibatalkan' },
]

export default function ProductionList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [orderSearch, setOrderSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [deleteTarget, setDeleteTarget] = useState<{ uuid: string; step: string } | null>(null)
  const debouncedOrder = useDebounce(orderSearch, 300)
  const { data, isLoading, error } = useProductionList(page, debouncedOrder, statusFilter)
  const deleteMutation = useDeleteProductionTracking()
  const addToast = useToastStore((s) => s.addToast)

  const handleDelete = () => {
    if (!deleteTarget) return
    deleteMutation.mutate(deleteTarget.uuid, {
      onSuccess: () => {
        addToast('success', `Tracking "${deleteTarget.step}" berhasil dihapus.`)
        setDeleteTarget(null)
      },
      onError: () => addToast('error', 'Gagal menghapus tracking.'),
    })
  }

  const renderPagination = () => {
    if (!data?.meta || data.meta.last_page <= 1) return null
    const { current_page, last_page, total } = data.meta
    const start = (current_page - 1) * data.meta.per_page + 1
    const end = Math.min(current_page * data.meta.per_page, total)

    return (
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
        <p className="text-xs text-slate-400 dark:text-slate-500 text-center sm:text-left">
          Menampilkan {start}-{end} dari {total} tracking
        </p>
        <div className="flex items-center justify-center gap-1">
          <Button size="sm" variant="ghost" disabled={current_page <= 1} onClick={() => setPage((p) => p - 1)}>
            &laquo;
          </Button>
          {Array.from({ length: Math.min(last_page, 5) }, (_, i) => {
            const p = i + 1
            return (
              <button
                key={p}
                onClick={() => setPage(p)}
                className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
                  p === current_page
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`}
              >
                {p}
              </button>
            )
          })}
          {last_page > 5 && <span className="text-xs text-slate-400 px-1">...</span>}
          {last_page > 5 && (
            <button
              onClick={() => setPage(last_page)}
              className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
                last_page === current_page
                  ? 'bg-blue-600 text-white'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
              }`}
            >
              {last_page}
            </button>
          )}
          <Button size="sm" variant="ghost" disabled={current_page >= last_page} onClick={() => setPage((p) => p + 1)}>
            &raquo;
          </Button>
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tracking Produksi</h1>
        <Button onClick={() => navigate('/production/create')} className="w-full sm:w-auto">
          + Tambah Tracking
        </Button>
      </div>

      {/* Table Card */}
      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
              <input
                type="text"
                placeholder="Cari order number..."
                value={orderSearch}
                onChange={(e) => { setOrderSearch(e.target.value); setPage(1) }}
                className="w-full pl-9 pr-8 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
              />
              {orderSearch && (
                <button
                  onClick={() => { setOrderSearch(''); setPage(1) }}
                  className="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                >
                  <X className="w-3.5 h-3.5" />
                </button>
              )}
            </div>
            <select
              value={statusFilter}
              onChange={(e) => { setStatusFilter(e.target.value); setPage(1) }}
              className="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
            >
              {statusOptions.map((opt) => (
                <option key={opt.value} value={opt.value}>{opt.label}</option>
              ))}
            </select>
          </div>
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={5} columns={6} />
          ) : error ? (
            <div className="flex flex-col items-center py-10 gap-2">
              <AlertCircle className="w-6 h-6 text-red-400" />
              <p className="text-sm text-red-600 dark:text-red-400">Gagal memuat data</p>
            </div>
          ) : !data?.data.length ? (
            <div className="flex flex-col items-center py-12 gap-3">
              <Factory className="w-10 h-10 text-slate-300 dark:text-slate-600" />
              <p className="text-sm text-slate-500 dark:text-slate-400">
                {orderSearch || statusFilter ? 'Tidak ada tracking ditemukan' : 'Belum ada tracking produksi'}
              </p>
              {!orderSearch && !statusFilter && (
                <Button size="sm" onClick={() => navigate('/production/create')}>
                  + Tambah Tracking
                </Button>
              )}
            </div>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Order</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Pasien</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Langkah</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden md:table-cell">Teknisi</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Status</th>
                      <th className="text-center py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                    {data.data.map((tracking, i) => (
                      <tr
                        key={tracking.uuid}
                        className="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors animate-row-enter"
                        style={{ animationDelay: `${i * 30}ms` }}
                      >
                        <td className="py-3.5 px-4 font-mono text-xs text-slate-500 dark:text-slate-400">{tracking.order_number}</td>
                        <td className="py-3.5 px-4 font-medium text-slate-900 dark:text-slate-100">{tracking.patient_name || '-'}</td>
                        <td className="py-3.5 px-4 text-slate-900 dark:text-slate-100">{tracking.step}</td>
                        <td className="py-3.5 px-4 hidden md:table-cell text-slate-600 dark:text-slate-400">{tracking.assigned_to_name}</td>
                        <td className="py-3.5 px-4">
                          <StatusBadge status={tracking.status} />
                        </td>
                        <td className="py-3.5 px-4">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/production/${tracking.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-150"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => navigate(`/production/${tracking.uuid}/edit`)}
                              className="p-1.5 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-150"
                              title="Edit"
                            >
                              <Pencil className="w-4 h-4" />
                            </button>
                            {tracking.status !== 'completed' && (
                              <button
                                onClick={() => setDeleteTarget({ uuid: tracking.uuid, step: tracking.step })}
                                className="p-1.5 rounded-lg text-red-400 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:scale-110 active:scale-95 transition-all duration-150"
                                title="Hapus"
                              >
                                <Trash2 className="w-4 h-4" />
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-2">
                {data.data.map((tracking) => (
                  <div
                    key={tracking.uuid}
                    className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
                    onClick={() => navigate(`/production/${tracking.uuid}`)}
                  >
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-xs text-slate-400 dark:text-slate-500">{tracking.order_number}</span>
                      <StatusBadge status={tracking.status} />
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{tracking.step}</p>
                    <p className="text-xs text-slate-500 dark:text-slate-400">
                      {tracking.patient_name} &middot; {tracking.assigned_to_name}
                    </p>
                    <div className="flex gap-2 pt-1" onClick={(e) => e.stopPropagation()}>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/production/${tracking.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/production/${tracking.uuid}/edit`)} className="flex-1">Edit</Button>
                    </div>
                  </div>
                ))}
              </div>

              {renderPagination()}
            </>
          )}
        </CardBody>
      </Card>

      {/* Delete Modal */}
      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Tracking" size="sm">
        <p className="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">Yakin ingin menghapus tracking <strong className="text-slate-900 dark:text-slate-100">{deleteTarget?.step}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
