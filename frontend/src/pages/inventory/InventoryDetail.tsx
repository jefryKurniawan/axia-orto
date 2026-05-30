import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { ArrowLeft, Pencil, Package, AlertTriangle } from 'lucide-react'
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
      <div className="flex items-center justify-center py-12">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600" />
      </div>
    )
  }

  if (error || !data) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400">Gagal memuat data inventory</p>
        <Button variant="secondary" onClick={() => navigate('/inventory')} className="mt-4">Kembali</Button>
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

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="flex items-center gap-3">
          <button onClick={() => navigate('/inventory')} className="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <ArrowLeft className="w-5 h-5 text-slate-500 dark:text-slate-400" />
          </button>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Detail Inventory</h1>
        </div>
        <Button variant="secondary" onClick={() => navigate(`/inventory/${uuid}/edit`)} className="w-full sm:w-auto">
          <Pencil className="w-4 h-4 mr-2" /> Edit
        </Button>
      </div>

      {/* Low stock warning */}
      {item.quantity <= item.reorder_level && (
        <div className="flex items-center gap-2 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800">
          <AlertTriangle className="w-5 h-5 text-orange-600 dark:text-orange-400" />
          <p className="text-sm text-orange-700 dark:text-orange-300">
            Stok rendah! Stok saat ini ({item.quantity}) sudah di bawah batas minimum ({item.reorder_level}).
          </p>
        </div>
      )}

      {/* Item info */}
      <Card>
        <CardHeader>
          <div className="flex items-center gap-2">
            <Package className="w-5 h-5 text-slate-500 dark:text-slate-400" />
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">{item.name}</h2>
          </div>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <p className="text-xs text-slate-500 dark:text-slate-400">Kode</p>
              <p className="font-mono text-sm text-slate-900 dark:text-slate-100">{item.code}</p>
            </div>
            <div>
              <p className="text-xs text-slate-500 dark:text-slate-400">Kategori</p>
              <Badge variant="default">{item.category === 'bahan_baku' ? 'Bahan Baku' : item.category === 'komponen' ? 'Komponen' : 'Alat Jadi'}</Badge>
            </div>
            <div>
              <p className="text-xs text-slate-500 dark:text-slate-400">Satuan</p>
              <p className="text-sm text-slate-900 dark:text-slate-100">{item.unit}</p>
            </div>
            <div>
              <p className="text-xs text-slate-500 dark:text-slate-400">Stok Saat Ini</p>
              <p className={`text-2xl font-bold ${item.quantity <= item.reorder_level ? 'text-orange-600 dark:text-orange-400' : 'text-slate-900 dark:text-slate-100'}`}>
                {item.quantity}
              </p>
            </div>
            <div>
              <p className="text-xs text-slate-500 dark:text-slate-400">Batas Minimum</p>
              <p className="text-sm text-slate-900 dark:text-slate-100">{item.reorder_level}</p>
            </div>
            <div>
              <p className="text-xs text-slate-500 dark:text-slate-400">Harga</p>
              <p className="text-sm text-slate-900 dark:text-slate-100">Rp {item.price.toLocaleString('id-ID')}</p>
            </div>
            {item.description && (
              <div className="sm:col-span-2 lg:col-span-3">
                <p className="text-xs text-slate-500 dark:text-slate-400">Deskripsi</p>
                <p className="text-sm text-slate-900 dark:text-slate-100">{item.description}</p>
              </div>
            )}
          </div>
        </CardBody>
      </Card>

      {/* Stock adjustment */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Adjust Stok</h2>
        </CardHeader>
        <CardBody>
          <div className="flex flex-col sm:flex-row gap-3">
            <select
              value={adjustType}
              onChange={(e) => setAdjustType(e.target.value)}
              className="px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm"
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
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Riwayat Stok</h2>
        </CardHeader>
        <CardBody>
          {loadingTx ? (
            <TableSkeleton rows={3} />
          ) : !transactions?.data.length ? (
            <p className="text-center text-slate-500 dark:text-slate-400 py-4">Belum ada riwayat transaksi</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Tanggal</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Tipe</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Jumlah</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Keterangan</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Oleh</th>
                    </tr>
                  </thead>
                  <tbody>
                    {transactions.data.map((tx) => (
                      <tr key={tx.id} className="border-b border-slate-100 dark:border-slate-800">
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
                  <span className="text-sm text-slate-600 dark:text-slate-400">{txPage} / {transactions.meta.last_page}</span>
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
