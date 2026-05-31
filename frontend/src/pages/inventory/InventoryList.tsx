import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Pencil, Trash2, Search, X, Package, AlertCircle, AlertTriangle } from 'lucide-react'
import { useInventoryItems, useDeleteInventoryItem, useInventoryStats } from '../../hooks/useInventory'
import { useToastStore } from '../../stores/toastStore'
import { useDebounce } from '../../hooks/useDebounce'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Badge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { TableSkeleton } from '../../components/ui/Skeleton'
import { Pagination } from '../../components/ui/Pagination'

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
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white animate-title-enter">
          Inventory
          {data?.meta?.total != null && (
            <span className="ml-2 text-sm font-normal text-slate-400 dark:text-slate-500">{data.meta.total}</span>
          )}
        </h1>
        <Button onClick={() => navigate('/inventory/create')} className="w-full sm:w-auto">
          + Tambah Item
        </Button>
      </div>

      {/* Stats cards */}
      {stats && (
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <Card accent="left" accentColor="border-l-blue-500" hover>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Total Item</p>
              <p className="text-xl font-bold text-slate-900 dark:text-white">{stats.total_items}</p>
            </CardBody>
          </Card>
          <Card accent="left" accentColor="border-l-emerald-500" hover>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Aktif</p>
              <p className="text-xl font-bold text-emerald-600 dark:text-emerald-400">{stats.active_items}</p>
            </CardBody>
          </Card>
          <Card accent="left" accentColor="border-l-amber-500" hover>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Stok Rendah</p>
              <p className="text-xl font-bold text-amber-600 dark:text-amber-400">{stats.low_stock_count}</p>
            </CardBody>
          </Card>
          <Card accent="left" accentColor="border-l-indigo-500" hover>
            <CardBody className="py-3">
              <p className="text-xs text-slate-500 dark:text-slate-400">Nilai Inventory</p>
              <p className="text-xl font-bold text-indigo-600 dark:text-indigo-400">Rp {stats.total_inventory_value.toLocaleString('id-ID')}</p>
            </CardBody>
          </Card>
        </div>
      )}

      {/* Table Card */}
      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
              <input
                type="text"
                placeholder="Cari kode atau nama item..."
                value={search}
                onChange={(e) => { setSearch(e.target.value); setPage(1) }}
                className="w-full pl-9 pr-8 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
              />
              {search && (
                <button
                  onClick={() => { setSearch(''); setPage(1) }}
                  className="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                >
                  <X className="w-3.5 h-3.5" />
                </button>
              )}
            </div>
            <select
              value={categoryFilter}
              onChange={(e) => { setCategoryFilter(e.target.value); setPage(1) }}
              className="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
            >
              {categoryOptions.map((opt) => (
                <option key={opt.value} value={opt.value}>{opt.label}</option>
              ))}
            </select>
          </div>
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={5} columns={8} />
          ) : error ? (
            <div className="flex flex-col items-center py-10 gap-2">
              <AlertCircle className="w-6 h-6 text-red-400" />
              <p className="text-sm text-red-600 dark:text-red-400">Gagal memuat data</p>
            </div>
          ) : !data?.data.length ? (
            <div className="flex flex-col items-center py-12 gap-3 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
              <Package className="w-8 h-8 text-slate-300 dark:text-slate-600" />
              <p className="text-sm text-slate-500 dark:text-slate-400">
                {search || categoryFilter ? 'Tidak ada item ditemukan' : 'Inventaris masih kosong'}
              </p>
              {!search && !categoryFilter && (
                <Button size="sm" onClick={() => navigate('/inventory/create')}>+ Tambah Item</Button>
              )}
            </div>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Kode</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nama</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden md:table-cell">Kategori</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Stok</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden lg:table-cell">Satuan</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden xl:table-cell">Harga</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Status</th>
                      <th className="text-center py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                    {data.data.map((item, i) => (
                      <tr
                        key={item.uuid}
                        className={`hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors animate-row-enter ${
                          item.quantity <= item.reorder_level ? 'border-l-2 border-l-red-400 dark:border-l-red-500' : ''
                        }`}
                        style={{ animationDelay: `${i * 30}ms` }}
                      >
                        <td className="py-3.5 px-4 font-mono text-xs text-slate-500 dark:text-slate-400">{item.code}</td>
                        <td className="py-3.5 px-4 font-medium text-slate-900 dark:text-slate-100">{item.name}</td>
                        <td className="py-3.5 px-4 hidden md:table-cell">
                          <Badge variant="default">{categoryLabels[item.category] || item.category}</Badge>
                        </td>
                        <td className="py-3.5 px-4">
                          <span className={item.quantity <= item.reorder_level ? 'text-red-600 dark:text-red-400 font-bold' : 'text-slate-900 dark:text-slate-100'}>
                            {item.quantity}
                          </span>
                        </td>
                        <td className="py-3.5 px-4 hidden lg:table-cell text-slate-600 dark:text-slate-400">{item.unit}</td>
                        <td className="py-3.5 px-4 hidden xl:table-cell text-slate-600 dark:text-slate-400">Rp {item.price.toLocaleString('id-ID')}</td>
                        <td className="py-3.5 px-4">
                          {item.quantity <= item.reorder_level ? (
                            <Badge variant="warning"><AlertTriangle className="w-3 h-3 inline mr-1" />Stok Rendah</Badge>
                          ) : (
                            <Badge variant="success">Normal</Badge>
                          )}
                        </td>
                        <td className="py-3.5 px-4">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/inventory/${item.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-150"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => navigate(`/inventory/${item.uuid}/edit`)}
                              className="p-1.5 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-150"
                              title="Edit"
                            >
                              <Pencil className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ uuid: item.uuid, name: item.name })}
                              className="p-1.5 rounded-lg text-red-400 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:scale-110 active:scale-95 transition-all duration-150"
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
              <div className="block sm:hidden space-y-2">
                {data.data.map((item) => (
                  <div
                    key={item.uuid}
                    className={`border rounded-lg p-3 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer ${
                      item.quantity <= item.reorder_level
                        ? 'border-l-4 border-l-red-400 dark:border-l-red-500 border-slate-200 dark:border-slate-700'
                        : 'border-slate-200 dark:border-slate-700'
                    }`}
                    onClick={() => navigate(`/inventory/${item.uuid}`)}
                  >
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-xs text-slate-400 dark:text-slate-500">{item.code}</span>
                      {item.quantity <= item.reorder_level ? (
                        <Badge variant="warning"><AlertTriangle className="w-3 h-3 inline mr-1" />Stok Rendah</Badge>
                      ) : (
                        <Badge variant="success">Normal</Badge>
                      )}
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{item.name}</p>
                    <div className="flex gap-2 text-xs text-slate-500 dark:text-slate-400">
                      <span>{categoryLabels[item.category]}</span>
                      <span>&middot;</span>
                      <span>Stok: {item.quantity} {item.unit}</span>
                    </div>
                    <div className="flex gap-2 pt-1" onClick={(e) => e.stopPropagation()}>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/inventory/${item.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/inventory/${item.uuid}/edit`)} className="flex-1">Edit</Button>
                      <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: item.uuid, name: item.name })} className="flex-1">Hapus</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data?.meta && (
                <Pagination
                  currentPage={data.meta.current_page}
                  lastPage={data.meta.last_page}
                  total={data.meta.total}
                  perPage={data.meta.per_page}
                  entityLabel="item"
                  onPageChange={setPage}
                />
              )}
            </>
          )}
        </CardBody>
      </Card>

      {/* Delete Modal */}
      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Item" size="sm">
        <p className="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">Yakin ingin menghapus item <strong className="text-slate-900 dark:text-slate-100">{deleteTarget?.name}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
