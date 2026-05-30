import { useParams, useNavigate, Link } from 'react-router-dom'
import { useConsultation, useDeleteConsultation } from '../../hooks/useConsultations'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useToastStore } from '../../stores/toastStore'
import { useState } from 'react'
import { Pencil, Trash2, ArrowLeft } from 'lucide-react'

export default function ConsultationDetail() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const { data: consultation, isLoading, error } = useConsultation(uuid!)
  const deleteMutation = useDeleteConsultation()
  const addToast = useToastStore((s) => s.addToast)
  const [showDelete, setShowDelete] = useState(false)

  const handleDelete = () => {
    deleteMutation.mutate(uuid!, {
      onSuccess: () => {
        addToast('success', 'Konsultasi berhasil dihapus.')
        navigate('/consultations')
      },
    })
  }

  if (isLoading) {
    return (
      <div className="space-y-4">
        {/* Breadcrumb skeleton */}
        <div className="flex items-center gap-2">
          <div className="h-3 w-16 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <span className="text-slate-300 dark:text-slate-600">/</span>
          <div className="h-3 w-28 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        {/* Title skeleton */}
        <div className="h-7 w-40 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        {/* Content skeleton */}
        <Card>
          <CardBody>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {Array.from({ length: 8 }).map((_, i) => (
                <div key={i} className="space-y-1">
                  <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  <div className="h-4 w-36 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                </div>
              ))}
            </div>
          </CardBody>
        </Card>
      </div>
    )
  }

  if (error || !consultation) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data konsultasi</p>
        <Button variant="secondary" onClick={() => navigate('/consultations')}>
          <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali ke Daftar Konsultasi
        </Button>
      </div>
    )
  }

  const mainFields = [
    { label: 'Tanggal Konsultasi', value: new Date(consultation.consultation_date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) },
    { label: 'Pasien', value: consultation.patient_name || '-' },
    { label: 'No. RM', value: consultation.medical_record_number || '-' },
    { label: 'Dokter', value: consultation.doctor_name || '-' },
    { label: 'Keluhan', value: consultation.complaint || '-' },
    { label: 'Diagnosis', value: consultation.diagnosis || '-' },
    { label: 'Rencana Perawatan', value: consultation.treatment_plan || '-' },
    { label: 'Catatan', value: consultation.notes || '-' },
    { label: 'Tanggal Kontrol Ulang', value: consultation.follow_up_date ? new Date(consultation.follow_up_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-' },
  ]

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1.5">
            <Link to="/consultations" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Konsultasi</Link>
            <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
            <span className="text-slate-600 dark:text-slate-400 truncate">{consultation.patient_name}</span>
          </nav>
          <div className="flex items-center gap-3">
            <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Detail Konsultasi</h1>
            <StatusBadge status={consultation.status} />
          </div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => navigate('/consultations')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali
          </Button>
          <Button variant="secondary" onClick={() => navigate(`/consultations/${uuid}/edit`)} className="w-full sm:w-auto">
            <Pencil className="h-4 w-4 mr-1.5" /> Edit
          </Button>
          <Button variant="danger" onClick={() => setShowDelete(true)} className="w-full sm:w-auto">
            <Trash2 className="h-4 w-4 mr-1.5" /> Hapus
          </Button>
        </div>
      </div>

      {/* Content */}
      <Card>
        <CardHeader>
          <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Informasi Konsultasi</h2>
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
      <Modal isOpen={showDelete} onClose={() => setShowDelete(false)} title="Hapus Konsultasi" size="sm">
        <p className="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">
          Yakin ingin menghapus konsultasi ini?
        </p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowDelete(false)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
