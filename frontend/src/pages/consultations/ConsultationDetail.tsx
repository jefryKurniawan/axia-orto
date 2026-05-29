import { useParams, useNavigate, Link } from 'react-router-dom'
import { useConsultation, useDeleteConsultation } from '../../hooks/useConsultations'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useToastStore } from '../../stores/toastStore'
import { useState } from 'react'

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
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-pulse" />
        <div className="h-64 bg-slate-200 dark:bg-slate-700 rounded-xl animate-pulse" />
      </div>
    )
  }

  if (error || !consultation) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data konsultasi</p>
        <Button variant="secondary" onClick={() => navigate('/consultations')}>
          Kembali ke Daftar Konsultasi
        </Button>
      </div>
    )
  }

  const infoItems = [
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
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
            <Link to="/consultations" className="hover:text-blue-600">Konsultasi</Link>
            <span className="mx-2">/</span>
            <span className="text-slate-900 dark:text-slate-100 truncate">{consultation.patient_name}</span>
          </nav>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Detail Konsultasi</h1>
        </div>
        <div className="flex gap-2 items-center flex-shrink-0 flex-wrap">
          <StatusBadge status={consultation.status} />
          <Button variant="secondary" onClick={() => navigate(`/consultations/${uuid}/edit`)} className="flex-1 sm:flex-none">
            Edit
          </Button>
          <Button variant="danger" onClick={() => setShowDelete(true)} className="flex-1 sm:flex-none">
            Hapus
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Konsultasi</h2>
        </CardHeader>
        <CardBody>
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

      <Modal isOpen={showDelete} onClose={() => setShowDelete(false)} title="Hapus Konsultasi" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus konsultasi ini?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowDelete(false)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
