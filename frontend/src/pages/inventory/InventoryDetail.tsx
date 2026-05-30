import { useState } from 'react'
import { useNavigate, useParams, Link } from 'react-router-dom'
import { ArrowLeft, Pencil, AlertTriangle } from 'lucide-react'
import { useInventoryItem, useInventoryTransactions, useAdjustStock } from '../../hooks/useInventory'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { Badge } from '../../components/ui/Badge'
import { TableSkeleton } from '../../components/ui/Skeleton'

const typeLabels: Record<string, string> = {
  masuk: 'Masuk',
  keluar: 'Keluar',
  adjustment: 'Adjustment',
}

const typeBadgeVariant: Record<string, 'success' | 'danger' | 'info'> = {
  masuk: 'success',
  keluar: 'danger',
  adjustment: 'info',
}

export default function InventoryDetail() {
  const navigate = useNavigate()
  const { uuid } = useParams()
  const [txPage, setTxPage] = useState(1)
  const [adjustType, setAdjustType] = useState<string>('masuk')
  const [adjustQty, setAdjustQty] = useState('')
  const [adjustNotes, setAdjustNotes] = useState('')
  const { data, isLoading, error } = useInventoryItem(uuid || '')
  const { data: transactions, isLoading: loadingTx } = useInventoryTransactions(uuid || '', txPage)
  const adjustMutation = useAdjustStock()
  const addToast = useToastStore((s) => s.addToast)

  if (isLoading) {
    return (
      <div className="space-y-4">
        {/* Breadcrumb skeleton */}
        <div className="flex items-center gap-2">
          <div className="h-3 w-14 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <span className="text-slate-300 dark:text-slate-600">/</span>
          <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        {/* Title skeleton */}
        <div className="h-7 w-40 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        {/* Content skeleton */}
        <Card>
          <CardBody>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {Array.from({ length: 6 }).map((_, i) => (
                <div key={i} className="space-y-1">
                  <div className="h-3 w-16 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  <div className="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                </div>
              ))}
            </div>
          </CardBody>
        </Card>
      </div>
    )
  }

  if (error || !data) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data inventory</p>
        <Button variant="secondary" onClick={() => navigate('/inventory')}>
          <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali
        </Button>
      </div>
    )
  }

  const item = data.item

  const handleAdjust = () => {
    if (!uuid || !adjustQty) return
    adjustMutation.mutate(
      {
        uuid,
        data: { type: adjustType, quantity: parseInt(adjustQty), notes: adjustNotes || undefined },
      },
      {
        onSuccess: () => {
          addToast('success', 'Stok berhasil diperbarui.')
          setAdjustQty('')
          setAdjustNotes('')
        },
        onError: () => addToast('error', 'Gagal memperbarui stok.'),
      }
    )
  }

  const infoFields = [
    { label: 'Kode', value: <span className="font-mono">{item.code}</span> },
    { label: 'Kategori', value: <Badge variant="default">{item.category === 'bahan_baku' ? 'Bahan Baku' : item.category === 'komponen' ? 'Komponen' : 'Alat Jadi'}</Badge> },
    { label: 'Satuan', value: item.unit },
    { label: 'Stok Saat Ini', value: <span className={`text-2xl font-bold ${item.quantity <= item.reorder_level ? 'text-orange-600 dark:text-orange-400' : ''}`}>{item.quantity}</span> },
    { label: 'Batas Minimum', value: item.reorder_level },
    { label: 'Harga', value: `Rp ${item.price.toLocaleString('id-ID')}` },
  ]

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1.5">
            <Link to="/inventory" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Inventory</Link>
            <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
            <span className="text-slate-600 dark:text-slate-400 truncate">{item.name}</span>
          </nav>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white truncate">{item.name}</h1>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => navigate('/inventory')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali
          </Button>
          <Button variant="secondary" onClick={() => navigate(`/inventory/${uuid}/edit`)} className="w-full sm:w-auto">
            <Pencil className="w-4 h-4 mr-1.5" /> Edit
          </Button>
        </div>
      </div>

      {/* Low stock warning */}
      {item.quantity <= item.reorder_level && (
        <div className="flex items-center gap-2 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800">
          <AlertTriangle className="w-5 h-5 text-orange-600 dark:text-orange-400 flex-shrink-0" />
          <p className="text-sm text-orange-700 dark:text-orange-300">
            Stok rendah! Stok saat ini ({item.quantity}) sudah di bawah batas minimum ({item.reorder_level}).
          </p>
        </div>
      )}

      {/* Item info */}
      <Card>
        <CardHeader>
          <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Informasi Item</h2>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {infoFields.map((field) => (
              <div key={field.label} className="space-y-1">
                <p className="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">{field.label}</p>
                <div className="text-sm font-medium text-slate-900 dark:text-slate-100">{field.value}</div>
              </div>
            ))}
            {item.description && (
              <div className="sm:col-span-2 lg:col-span-3 space-y-1">
                <p className="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Deskripsi</p>
                <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{item.description}</p>
              </div>
            )}
          </div>
        </CardBody>
      </Card>

      {/* Stock adjustment */}
      <Card>
        <CardHeader>
          <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Adjust Stok</h2>
        </CardHeader>
        <CardBody>
          <div className="flex flex-col sm:flex-row gap-3">
            <select
              value={adjustType}
              onChange={(e) => setAdjustType(e.target.value)}
              className="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
            >
              <option value="masuk">Stok Masuk</option>
              <option value="keluar">Stok Keluar</option>
              <option value="adjustment">Adjustment</option>
            </select>
            <Input
              type="number"
              placeholder="Jumlah"
              value={adjustQty}
              onChange={(e) => setAdjustQty(e.target.value)}
              min={1}
              className="sm:w-32"
            />
            <Input
              placeholder="Keterangan (opsional)"
              value={adjustNotes}
              onChange={(e) => setAdjustNotes(e.target.value)}
              className="flex-1"
            />
            <Button onClick={handleAdjust} loading={adjustMutation.isPending} disabled={!adjustQty}>
              Simpan
            </Button>
          </div>
        </CardBody>
      </Card>

      {/* Transaction history */}
      <Card>
        <CardHeader>
          <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Riwayat Stok</h2>
        </CardHeader>
        <CardBody>
          {loadingTx ? (
            <TableSkeleton rows={3} />
          ) : !transactions?.data.length ? (
            <p className="text-xs text-slate-400 dark:text-slate-500 text-center py-4">Belum ada riwayat transaksi</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tanggal</th>
                      <th className="text-center py-3 px-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tipe</th>
                      <th className="text-center py-3 px-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jumlah</th>
                      <th className="text-center py-3 px-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Keterangan</th>
                      <th className="text-center py-3 px-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Oleh</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                    {transactions.data.map((tx) => (
                      <tr key={tx.id}>
                        <td className="py-3 px-2 text-center text-slate-600 dark:text-slate-400">
                          {new Date(tx.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </td>
                        <td className="py-3 px-2 text-center">
                          <Badge variant={typeBadgeVariant[tx.type] || 'default'}>{typeLabels[tx.type]}</Badge>
                        </td>
                        <td className="py-3 px-2 text-center font-medium text-slate-900 dark:text-slate-100">
                          {tx.quantity > 0 ? '+' : ''}{tx.quantity}
                        </td>
                        <td className="py-3 px-2 text-center text-slate-600 dark:text-slate-400">{tx.notes || '-'}</td>
                        <td className="py-3 px-2 text-center text-slate-600 dark:text-slate-400">{tx.created_by_name || '-'}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-3">
                {transactions.data.map((tx) => (
                  <div key={tx.id} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-1">
                    <div className="flex items-center justify-between">
                      <Badge variant={typeBadgeVariant[tx.type] || 'default'}>{typeLabels[tx.type]}</Badge>
                      <span className="font-medium text-slate-900 dark:text-slate-100">{tx.quantity > 0 ? '+' : ''}{tx.quantity}</span>
                    </div>
                    <p className="text-xs text-slate-500 dark:text-slate-400">
                      {new Date(tx.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </p>
                    {tx.notes && <p className="text-sm text-slate-600 dark:text-slate-400">{tx.notes}</p>}
                  </div>
                ))}
              </div>

              {transactions.meta.last_page > 1 && (
                <div className="flex items-center justify-between mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <Button size="sm" variant="secondary" disabled={txPage <= 1} onClick={() => setTxPage((p) => p - 1)}>Sebelumnya</Button>
                  <span className="text-xs text-slate-400 dark:text-slate-500">{txPage} / {transactions.meta.last_page}</span>
                  <Button size="sm" variant="secondary" disabled={txPage >= transactions.meta.last_page} onClick={() => setTxPage((p) => p + 1)}>Selanjutnya</Button>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>
    </div>
  )
}
