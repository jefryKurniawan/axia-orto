import { useParams, useNavigate, Link } from 'react-router-dom'
import { useProductionTracking, useUpdateProductionTracking, useDeleteProductionTracking } from '../../hooks/useProduction'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useState } from 'react'
import { Pencil, Trash2, ArrowLeft, Play, CheckCircle } from 'lucide-react'

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
        {/* Breadcrumb skeleton */}
        <div className="flex items-center gap-2">
          <div className="h-3 w-14 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <span className="text-slate-300 dark:text-slate-600">/</span>
          <div className="h-3 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        {/* Title skeleton */}
        <div className="h-7 w-36 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        {/* Content skeleton */}
        <Card>
          <CardBody>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {Array.from({ length: 6 }).map((_, i) => (
                <div key={i} className="space-y-1">
                  <div className="h-3 w-16 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  <div className="h-4 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                </div>
              ))}
            </div>
          </CardBody>
        </Card>
      </div>
    )
  }

  if (error || !tracking) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data tracking</p>
        <Button variant="secondary" onClick={() => navigate('/production')}>
          <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali ke Daftar
        </Button>
      </div>
    )
  }

  const mainFields = [
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
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1.5">
            <Link to="/production" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Produksi</Link>
            <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
            <span className="text-slate-600 dark:text-slate-400 truncate">{tracking.step}</span>
          </nav>
          <div className="flex items-center gap-3">
            <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white truncate">{tracking.step}</h1>
            <StatusBadge status={tracking.status} />
          </div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => navigate('/production')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali
          </Button>
          {nextStatusMap[tracking.status] && (
            <Button onClick={handleAdvanceStatus} loading={updateMutation.isPending} className="w-full sm:w-auto">
              {tracking.status === 'pending' ? <Play className="h-4 w-4 mr-1.5" /> : <CheckCircle className="h-4 w-4 mr-1.5" />}
              {nextStatusLabel[tracking.status]}
            </Button>
          )}
          {tracking.status !== 'completed' && (
            <Button variant="secondary" onClick={() => navigate(`/production/${uuid}/edit`)} className="w-full sm:w-auto">
              <Pencil className="h-4 w-4 mr-1.5" /> Edit
            </Button>
          )}
          <Button variant="danger" onClick={() => setShowDelete(true)} className="w-full sm:w-auto">
            <Trash2 className="h-4 w-4 mr-1.5" /> Hapus
          </Button>
        </div>
      </div>

      {/* Content */}
      <Card>
        <CardHeader>
          <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Informasi Tracking</h2>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {mainFields.map((item) => (
              <div key={item.label} className="space-y-1">
                <dt className="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">{item.label}</dt>
                <dd className="text-sm font-medium text-slate-900 dark:text-slate-100">{item.value}</dd>
              </div>
            ))}
          </div>
        </CardBody>
      </Card>

      {/* Delete Modal */}
      <Modal isOpen={showDelete} onClose={() => setShowDelete(false)} title="Hapus Tracking" size="sm">
        <p className="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">
          Yakin ingin menghapus tracking <strong className="text-slate-900 dark:text-slate-100">{tracking.step}</strong>?
        </p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowDelete(false)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
