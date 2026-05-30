import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Pencil, Trash2, AlertTriangle } from 'lucide-react'
import { useInventoryItems, useDeleteInventoryItem, useInventoryStats } from '../../hooks/useInventory'
import { useToastStore } from '../../stores/toastStore'
import { useDebounce } from '../../hooks/useDebounce'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { Badge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { TableSkeleton } from '../../components/ui/Skeleton'

const categoryOptions = [
  { value: '', label: 'Semua Kategori' },
  { value: 'bahan_baku', label: 'Bahan Baku' },
  { value: 'komponen', label: 'Komponen' },
  { value: 'alat_jadi', label: 'Alat Jadi' },
]

const categoryLabels: Record<string, string> = {
  bahan_baku: 'Bahan Baku',
  komponen: 'Komponen',
  alat_jadi: 'Alat Jadi',
}

export default function InventoryList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [categoryFilter, setCategoryFilter] = useState('')
  const [deleteTarget, setDeleteTarget] = useState<{ uuid: string; name: string } | null>(null)
  const debouncedSearch = useDebounce(search, 300)
  const { data, isLoading, error } = useInventoryItems(page, debouncedSearch, categoryFilter)
  const { data: stats } = useInventoryStats()
  const deleteMutation = useDeleteInventoryItem()
  const addToast = useToastStore((s) => s.addToast)

  const handleDelete = () => {
    if (!deleteTarget) return
    deleteMutation.mutate(deleteTarget.uuid, {
      onSuccess: () => {
        addToast('success', `Item ${deleteTarget.name} berhasil dihapus.`)
        setDeleteTarget(null)
      },
      onError: () => {
        addToast('error', 'Gagal menghapus item inventory.')
      },
    })
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Inventory</h1>
        <Button onClick={() => navigate('/inventory/create')} className="w-full sm:w-auto">
          + Tambah Item
        </Button>
      </div>

      {/* Stats cards */}
      {stats && (
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <Card>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Total Item</p>
              <p className="text-xl font-bold text-slate-900 dark:text-slate-100">{stats.total_items}</p>
            </CardBody>
          </Card>
          <Card>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Aktif</p>
              <p className="text-xl font-bold text-green-600 dark:text-green-400">{stats.active_items}</p>
            </CardBody>
          </Card>
          <Card>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Stok Rendah</p>
              <p className="text-xl font-bold text-orange-600 dark:text-orange-400">{stats.low_stock_count}</p>
            </CardBody>
          </Card>
          <Card>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Nilai Inventory</p>
              <p className="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {stats.total_inventory_value.toLocaleString('id-ID')}</p>
            </CardBody>
          </Card>
        </div>
      )}

      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <Input
              placeholder="Cari kode atau nama item..."
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1) }}
              className="flex-1"
            />
            <select
              value={categoryFilter}
              onChange={(e) => { setCategoryFilter(e.target.value); setPage(1) }}
              className="px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm"
            >
              {categoryOptions.map((opt) => (
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
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Tidak ada item inventory ditemukan</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Kode</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Nama</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden md:table-cell">Kategori</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Stok</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden lg:table-cell">Satuan</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden xl:table-cell">Harga</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Status</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((item) => (
                      <tr key={item.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="py-3 px-2 font-mono text-xs text-center text-slate-600 dark:text-slate-400">{item.code}</td>
                        <td className="py-3 px-2 font-medium text-center text-slate-900 dark:text-slate-100">{item.name}</td>
                        <td className="py-3 px-2 hidden md:table-cell text-center">
                          <Badge variant="default">{categoryLabels[item.category] || item.category}</Badge>
                        </td>
                        <td className="py-3 px-2 text-center">
                          <span className={item.quantity <= item.reorder_level ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-slate-900 dark:text-slate-100'}>
                            {item.quantity}
                          </span>
                        </td>
                        <td className="py-3 px-2 hidden lg:table-cell text-center text-slate-600 dark:text-slate-400">{item.unit}</td>
                        <td className="py-3 px-2 hidden xl:table-cell text-center text-slate-600 dark:text-slate-400">Rp {item.price.toLocaleString('id-ID')}</td>
                        <td className="py-3 px-2 text-center">
                          {item.quantity <= item.reorder_level ? (
                            <Badge variant="warning"><AlertTriangle className="w-3 h-3 inline mr-1" />Stok Rendah</Badge>
                          ) : (
                            <Badge variant="success">Normal</Badge>
                          )}
                        </td>
                        <td className="py-3 px-2">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/inventory/${item.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => navigate(`/inventory/${item.uuid}/edit`)}
                              className="p-1.5 rounded-lg text-blue-500 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                              title="Edit"
                            >
                              <Pencil className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ uuid: item.uuid, name: item.name })}
                              className="p-1.5 rounded-lg text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                              title="Hapus"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-3">
                {data.data.map((item) => (
                  <div key={item.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-xs text-slate-500 dark:text-slate-400">{item.code}</span>
                      {item.quantity <= item.reorder_level ? (
                        <Badge variant="warning"><AlertTriangle className="w-3 h-3 inline mr-1" />Stok Rendah</Badge>
                      ) : (
                        <Badge variant="success">Normal</Badge>
                      )}
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{item.name}</p>
                    <div className="flex gap-2 text-xs text-slate-500 dark:text-slate-400">
                      <span>{categoryLabels[item.category]}</span>
                      <span>-</span>
                      <span>Stok: {item.quantity} {item.unit}</span>
                    </div>
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/inventory/${item.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/inventory/${item.uuid}/edit`)} className="flex-1">Edit</Button>
                      <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: item.uuid, name: item.name })} className="flex-1">Hapus</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">
                    {data.meta.total} item ditemukan
                  </p>
                  <div className="flex items-center justify-center gap-2">
                    <Button size="sm" variant="secondary" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>
                      Sebelumnya
                    </Button>
                    <span className="flex items-center px-3 text-sm text-slate-600 dark:text-slate-400">
                      {page} / {data.meta.last_page}
                    </span>
                    <Button size="sm" variant="secondary" disabled={page >= data.meta.last_page} onClick={() => setPage((p) => p + 1)}>
                      Selanjutnya
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>

      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Item" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus item <strong>{deleteTarget?.name}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
