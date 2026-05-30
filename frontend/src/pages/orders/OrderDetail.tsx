import { useParams, useNavigate, Link } from 'react-router-dom'
import { useOrder, useUpdateOrderStatus } from '../../hooks/useOrders'
import { usePaymentsByOrder } from '../../hooks/usePayments'
import { useProductionByOrder } from '../../hooks/useProduction'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Badge, StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useState } from 'react'
import { Eye } from 'lucide-react'

const nextStatusMap: Record<string, string> = {
  draft: 'confirmed',
  confirmed: 'production',
  production: 'ready',
  ready: 'delivered',
}

const nextStatusLabel: Record<string, string> = {
  draft: 'Konfirmasi',
  confirmed: 'Mulai Produksi',
  production: 'Tandai Siap',
  ready: 'Kirim',
}

export default function OrderDetail() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const { data: order, isLoading, error } = useOrder(uuid || '')
  const { data: payments } = usePaymentsByOrder(uuid || '')
  const { data: trackings } = useProductionByOrder(uuid || '')
  const statusMutation = useUpdateOrderStatus()
  const addToast = useToastStore((s) => s.addToast)
  const [showCancel, setShowCancel] = useState(false)

  const handleAdvanceStatus = () => {
    if (!order || !nextStatusMap[order.status]) return
    statusMutation.mutate(
      { uuid: order.uuid, status: nextStatusMap[order.status] },
      {
        onSuccess: () => addToast('success', 'Status order berhasil diperbarui.'),
        onError: () => addToast('error', 'Gagal memperbarui status.'),
      }
    )
  }

  const handleCancel = () => {
    if (!order) return
    statusMutation.mutate(
      { uuid: order.uuid, status: 'cancelled' },
      {
        onSuccess: () => { addToast('success', 'Order dibatalkan.'); setShowCancel(false) },
        onError: () => addToast('error', 'Gagal membatalkan order.'),
      }
    )
  }

  if (isLoading) {
    return (
      <div className="space-y-4">
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-pulse" />
        <div className="h-64 bg-slate-200 dark:bg-slate-700 rounded-xl animate-pulse" />
      </div>
    )
  }

  if (error || !order) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data order</p>
        <Button variant="secondary" onClick={() => navigate('/orders')}>Kembali ke Daftar Order</Button>
      </div>
    )
  }

  const infoItems = [
    { label: 'No. Order', value: order.order_number },
    { label: 'Pasien', value: order.patient?.name || '-' },
    { label: 'No. RM', value: order.patient?.medical_record_number || '-' },
    { label: 'Tanggal Order', value: new Date(order.order_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) },
    { label: 'Tanggal Kirim', value: order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-' },
    { label: 'Total', value: `Rp ${Number(order.total_amount).toLocaleString('id-ID')}` },
    { label: 'Dibuat oleh', value: order.created_by_name || '-' },
    { label: 'Catatan', value: order.notes || '-' },
  ]

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
            <Link to="/orders" className="hover:text-blue-600">Order</Link>
            <span className="mx-2">/</span>
            <span className="text-slate-900 dark:text-slate-100 truncate">{order.order_number}</span>
          </nav>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100 truncate">{order.order_number}</h1>
        </div>
        <div className="flex gap-2 flex-shrink-0">
          {nextStatusMap[order.status] && (
            <Button onClick={handleAdvanceStatus} loading={statusMutation.isPending}>
              {nextStatusLabel[order.status]}
            </Button>
          )}
          {order.status === 'draft' && (
            <Button variant="secondary" onClick={() => navigate(`/orders/${order.uuid}/edit`)}>Edit</Button>
          )}
          {order.status !== 'cancelled' && order.status !== 'delivered' && (
            <Button variant="danger" onClick={() => setShowCancel(true)}>Batalkan</Button>
          )}
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card className="lg:col-span-2">
          <CardHeader>
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Order</h2>
          </CardHeader>
          <CardBody>
            <div className="flex items-center gap-2 mb-4">
              <StatusBadge status={order.status} />
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {infoItems.map((item) => (
                <div key={item.label}>
                  <dt className="text-sm text-slate-500 dark:text-slate-400">{item.label}</dt>
                  <dd className="mt-1 text-sm text-slate-900 dark:text-slate-100">{item.value}</dd>
                </div>
              ))}
            </div>
          </CardBody>
        </Card>

        <div className="space-y-4">
          {/* Payments */}
          <Card>
            <CardHeader>
              <div className="flex justify-between items-center">
                <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Pembayaran</h2>
                <Button size="sm" variant="ghost" onClick={() => navigate(`/payments/create?order=${order.uuid}`)}>+ Tambah</Button>
              </div>
            </CardHeader>
            <CardBody>
              {!payments?.length ? (
                <p className="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Belum ada pembayaran</p>
              ) : (
                <div className="space-y-2">
                  {payments.map((p) => (
                    <div key={p.uuid} className="flex justify-between items-center p-2 bg-slate-50 dark:bg-slate-800 rounded-lg">
                      <div>
                        <p className="text-xs font-mono text-slate-600 dark:text-slate-400">{p.payment_number}</p>
                        <p className="text-xs text-slate-500 dark:text-slate-400">{p.payment_method}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-medium text-slate-900 dark:text-slate-100">Rp {Number(p.amount).toLocaleString('id-ID')}</p>
                        <Badge variant={p.status === 'completed' ? 'success' : p.status === 'failed' ? 'danger' : 'warning'} className="text-xs">
                          {p.status}
                        </Badge>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </CardBody>
          </Card>

          {/* Production */}
          <Card>
            <CardHeader>
              <div className="flex justify-between items-center">
                <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Produksi</h2>
                <Button size="sm" variant="ghost" onClick={() => navigate(`/production/create?order=${order.uuid}`)}>+ Tambah</Button>
              </div>
            </CardHeader>
            <CardBody>
              {!trackings?.length ? (
                <p className="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Belum ada tracking</p>
              ) : (
                <div className="space-y-2">
                  {trackings.map((t) => (
                    <div key={t.uuid} className="flex justify-between items-center p-2 bg-slate-50 dark:bg-slate-800 rounded-lg">
                      <div>
                        <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{t.step}</p>
                        <p className="text-xs text-slate-500 dark:text-slate-400">{t.assigned_to_name}</p>
                      </div>
                      <div className="flex items-center gap-2">
                        <Badge variant={t.status === 'completed' ? 'success' : t.status === 'in_progress' ? 'warning' : 'default'}>
                          {t.status}
                        </Badge>
                        <button
                          onClick={() => navigate(`/production/${t.uuid}`)}
                          className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                          title="Detail"
                        >
                          <Eye className="w-4 h-4" />
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </CardBody>
          </Card>
        </div>
      </div>

      {/* Item Order */}
      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Item Order</h2>
        </CardHeader>
        <CardBody>
          {!order.order_items?.length ? (
            <p className="text-sm text-slate-500 dark:text-slate-400 text-center py-4">Tidak ada item</p>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-slate-200 dark:border-slate-700">
                    <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Layanan</th>
                    <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Qty</th>
                    <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Harga Satuan</th>
                    <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Total</th>
                  </tr>
                </thead>
                <tbody>
                  {order.order_items.map((item) => (
                    <tr key={item.id} className="border-b border-slate-100 dark:border-slate-800">
                      <td className="py-3 px-2 text-center text-slate-900 dark:text-slate-100">{item.service_name || `Service #${item.service_id}`}</td>
                      <td className="py-3 px-2 text-center text-slate-600 dark:text-slate-400">{item.quantity}</td>
                      <td className="py-3 px-2 text-center text-slate-600 dark:text-slate-400">Rp {Number(item.unit_price).toLocaleString('id-ID')}</td>
                      <td className="py-3 px-2 text-center text-slate-900 dark:text-slate-100">Rp {Number(item.total_price).toLocaleString('id-ID')}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </CardBody>
      </Card>

      <div className="flex justify-start">
        <Button variant="secondary" onClick={() => navigate('/orders')}>Kembali ke Daftar</Button>
      </div>

      {/* Cancel confirmation */}
      <Modal isOpen={showCancel} onClose={() => setShowCancel(false)} title="Batalkan Order" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin membatalkan order <strong>{order.order_number}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowCancel(false)}>Batal</Button>
          <Button variant="danger" loading={statusMutation.isPending} onClick={handleCancel}>Batalkan Order</Button>
        </div>
      </Modal>
    </div>
  )
}
