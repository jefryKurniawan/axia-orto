import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Pencil, Trash2 } from 'lucide-react'
import { useOrders, useDeleteOrder } from '../../hooks/useOrders'
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
  { value: 'draft', label: 'Draft' },
  { value: 'confirmed', label: 'Dikonfirmasi' },
  { value: 'production', label: 'Produksi' },
  { value: 'ready', label: 'Siap' },
  { value: 'delivered', label: 'Dikirim' },
  { value: 'cancelled', label: 'Dibatalkan' },
]

export default function OrderList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [deleteTarget, setDeleteTarget] = useState<{ uuid: string; order_number: string } | null>(null)
  const debouncedSearch = useDebounce(search, 300)
  const { data, isLoading, error } = useOrders(page, debouncedSearch, statusFilter)
  const deleteMutation = useDeleteOrder()
  const addToast = useToastStore((s) => s.addToast)

  const handleDelete = () => {
    if (!deleteTarget) return
    deleteMutation.mutate(deleteTarget.uuid, {
      onSuccess: () => {
        addToast('success', `Order ${deleteTarget.order_number} berhasil dihapus.`)
        setDeleteTarget(null)
      },
      onError: () => addToast('error', 'Gagal menghapus order.'),
    })
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Order Perawatan</h1>
        <Button onClick={() => navigate('/orders/create')} className="w-full sm:w-auto">
          + Buat Order
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <Input
              placeholder="Cari order number atau pasien..."
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1) }}
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
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Tidak ada order ditemukan</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">No. Order</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Pasien</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden md:table-cell">Tanggal</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Total</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Status</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((order) => (
                      <tr key={order.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="py-3 px-2 font-mono text-xs text-slate-600 dark:text-slate-400 text-center">{order.order_number}</td>
                        <td className="py-3 px-2 font-medium text-center text-slate-900 dark:text-slate-100">{order.patient_name || '-'}</td>
                        <td className="py-3 px-2 hidden md:table-cell text-slate-600 dark:text-slate-400 text-center">
                          {new Date(order.order_date).toLocaleDateString('id-ID')}
                        </td>
                        <td className="py-3 px-2 text-center text-slate-900 dark:text-slate-100">
                          Rp {Number(order.total_amount).toLocaleString('id-ID')}
                        </td>
                        <td className="py-3 px-2 text-center">
                          <StatusBadge status={order.status} />
                        </td>
                        <td className="py-3 px-2">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/orders/${order.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-200"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => navigate(`/orders/${order.uuid}/edit`)}
                              className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-200"
                              title="Edit"
                            >
                              <Pencil className="w-4 h-4" />
                            </button>
                            {order.status === 'draft' && (
                              <button
                                onClick={() => setDeleteTarget({ uuid: order.uuid, order_number: order.order_number })}
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
                {data.data.map((order) => (
                  <div key={order.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-mono text-slate-500 dark:text-slate-400">{order.order_number}</span>
                      <StatusBadge status={order.status} />
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{order.patient_name || '-'}</p>
                    <p className="text-xs text-slate-600 dark:text-slate-400">
                      {new Date(order.order_date).toLocaleDateString('id-ID')} &middot; Rp {Number(order.total_amount).toLocaleString('id-ID')}
                    </p>
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/orders/${order.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/orders/${order.uuid}/edit`)} className="flex-1">Edit</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">
                    {data.meta.total} order ditemukan
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

      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Order" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus order <strong>{deleteTarget?.order_number}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
