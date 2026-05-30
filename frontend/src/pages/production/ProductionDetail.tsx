import { useParams, useNavigate, Link } from 'react-router-dom'
import { useProductionTracking, useUpdateProductionTracking, useDeleteProductionTracking } from '../../hooks/useProduction'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useState } from 'react'

const nextStatusMap: Record<string, string> = {
  pending: 'in_progress',
  in_progress: 'completed',
}

const nextStatusLabel: Record<string, string> = {
  pending: 'Mulai',
  in_progress: 'Selesai',
}

export default function ProductionDetail() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const { data: tracking, isLoading, error } = useProductionTracking(uuid || '')
  const updateMutation = useUpdateProductionTracking()
  const deleteMutation = useDeleteProductionTracking()
  const addToast = useToastStore((s) => s.addToast)
  const [showDelete, setShowDelete] = useState(false)

  const handleAdvanceStatus = () => {
    if (!tracking || !nextStatusMap[tracking.status]) return
    const newStatus = nextStatusMap[tracking.status]
    updateMutation.mutate(
      { uuid: tracking.uuid, data: { status: newStatus as any } },
      {
        onSuccess: () => addToast('success', 'Status tracking berhasil diperbarui.'),
        onError: () => addToast('error', 'Gagal memperbarui status.'),
      }
    )
  }

  const handleDelete = () => {
    if (!tracking) return
    deleteMutation.mutate(tracking.uuid, {
      onSuccess: () => { addToast('success', 'Tracking berhasil dihapus.'); navigate('/production') },
      onError: () => addToast('error', 'Gagal menghapus tracking.'),
    })
  }

  if (isLoading) {
    return (
      <div className="space-y-4">
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        <div className="h-64 bg-slate-200 dark:bg-slate-700 rounded-xl animate-shimmer" />
      </div>
    )
  }

  if (error || !tracking) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data tracking</p>
        <Button variant="secondary" onClick={() => navigate('/production')}>Kembali ke Daftar</Button>
      </div>
    )
  }

  const infoItems = [
    { label: 'Order', value: tracking.order_number || '-' },
    { label: 'Pasien', value: tracking.patient_name || '-' },
    { label: 'Langkah', value: tracking.step },
    { label: 'Teknisi', value: tracking.assigned_to_name || '-' },
    { label: 'Catatan', value: tracking.notes || '-' },
    { label: 'Mulai', value: tracking.started_at ? new Date(tracking.started_at).toLocaleString('id-ID') : '-' },
    { label: 'Selesai', value: tracking.completed_at ? new Date(tracking.completed_at).toLocaleString('id-ID') : '-' },
  ]

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
            <Link to="/production" className="hover:text-blue-600">Produksi</Link>
            <span className="mx-2">/</span>
            <span className="text-slate-900 dark:text-slate-100 truncate">{tracking.step}</span>
          </nav>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100 truncate">{tracking.step}</h1>
        </div>
        <div className="flex gap-2 flex-shrink-0">
          {nextStatusMap[tracking.status] && (
            <Button onClick={handleAdvanceStatus} loading={updateMutation.isPending}>
              {nextStatusLabel[tracking.status]}
            </Button>
          )}
          {tracking.status !== 'completed' && (
            <Button variant="secondary" onClick={() => navigate(`/production/${uuid}/edit`)}>Edit</Button>
          )}
          <Button variant="danger" onClick={() => setShowDelete(true)}>Hapus</Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Tracking</h2>
        </CardHeader>
        <CardBody>
          <div className="flex items-center gap-2 mb-4">
            <StatusBadge status={tracking.status} />
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

      <div className="flex justify-start">
        <Button variant="secondary" onClick={() => navigate('/production')}>Kembali ke Daftar</Button>
      </div>

      <Modal isOpen={showDelete} onClose={() => setShowDelete(false)} title="Hapus Tracking" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus tracking <strong>{tracking.step}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowDelete(false)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
