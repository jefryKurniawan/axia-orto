import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Pencil, Trash2 } from 'lucide-react'
import { useProductionList, useDeleteProductionTracking } from '../../hooks/useProduction'
import { useToastStore } from '../../stores/toastStore'
import { useDebounce } from '../../hooks/useDebounce'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
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

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Tracking Produksi</h1>
        <Button onClick={() => navigate('/production/create')} className="w-full sm:w-auto">
          + Tambah Tracking
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <Input
              placeholder="Cari order number..."
              value={orderSearch}
              onChange={(e) => { setOrderSearch(e.target.value); setPage(1) }}
            />
            <select
              value={statusFilter}
              onChange={(e) => { setStatusFilter(e.target.value); setPage(1) }}
              className="px-3 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              {statusOptions.map((opt) => (
                <option key={opt.value} value={opt.value}>{opt.label}</option>
              ))}
            </select>
          </div>
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={5} />
          ) : error ? (
            <p className="text-center text-red-600 dark:text-red-400 py-8">Gagal memuat data</p>
          ) : !data?.data.length ? (
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Tidak ada tracking ditemukan</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Order</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Pasien</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Langkah</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden md:table-cell">Teknisi</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Status</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((tracking) => (
                      <tr key={tracking.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="py-3 px-2 font-mono text-xs text-slate-600 dark:text-slate-400 text-center">{tracking.order_number}</td>
                        <td className="py-3 px-2 font-medium text-center text-slate-900 dark:text-slate-100">{tracking.patient_name || '-'}</td>
                        <td className="py-3 px-2 text-center text-slate-900 dark:text-slate-100">{tracking.step}</td>
                        <td className="py-3 px-2 hidden md:table-cell text-slate-600 dark:text-slate-400 text-center">{tracking.assigned_to_name}</td>
                        <td className="py-3 px-2 text-center">
                          <StatusBadge status={tracking.status} />
                        </td>
                        <td className="py-3 px-2">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/production/${tracking.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => navigate(`/production/${tracking.uuid}/edit`)}
                              className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                              title="Edit"
                            >
                              <Pencil className="w-4 h-4" />
                            </button>
                            {tracking.status !== 'completed' && (
                              <button
                                onClick={() => setDeleteTarget({ uuid: tracking.uuid, step: tracking.step })}
                                className="p-1.5 rounded-lg text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
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
              <div className="block sm:hidden space-y-3">
                {data.data.map((tracking) => (
                  <div key={tracking.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-mono text-slate-500 dark:text-slate-400">{tracking.order_number}</span>
                      <StatusBadge status={tracking.status} />
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{tracking.step}</p>
                    <p className="text-xs text-slate-600 dark:text-slate-400">
                      {tracking.patient_name} &middot; {tracking.assigned_to_name}
                    </p>
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/production/${tracking.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/production/${tracking.uuid}/edit`)} className="flex-1">Edit</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">
                    {data.meta.total} tracking ditemukan
                  </p>
                  <div className="flex items-center justify-center gap-2">
                    <Button size="sm" variant="secondary" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>Sebelumnya</Button>
                    <span className="flex items-center px-3 text-sm text-slate-600 dark:text-slate-400">{page} / {data.meta.last_page}</span>
                    <Button size="sm" variant="secondary" disabled={page >= data.meta.last_page} onClick={() => setPage((p) => p + 1)}>Selanjutnya</Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>

      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Tracking" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus tracking <strong>{deleteTarget?.step}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
