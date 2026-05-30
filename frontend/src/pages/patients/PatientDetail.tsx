import { useParams, useNavigate, Link } from 'react-router-dom'
import { usePatient, useDeletePatient } from '../../hooks/usePatients'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Badge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useState } from 'react'
import { Pencil, Trash2, ArrowLeft } from 'lucide-react'

export default function PatientDetail() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const { data: patient, isLoading, error } = usePatient(uuid!)
  const deleteMutation = useDeletePatient()
  const [showDelete, setShowDelete] = useState(false)

  const handleDelete = () => {
    deleteMutation.mutate(uuid!, {
      onSuccess: () => navigate('/patients'),
    })
  }

  if (isLoading) {
    return (
      <div className="space-y-4">
        {/* Breadcrumb skeleton */}
        <div className="flex items-center gap-2">
          <div className="h-3 w-12 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <span className="text-slate-300 dark:text-slate-600">/</span>
          <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        {/* Title skeleton */}
        <div className="h-7 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        {/* Content skeleton */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Card className="md:col-span-2">
            <CardBody>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {Array.from({ length: 8 }).map((_, i) => (
                  <div key={i} className="space-y-1">
                    <div className="h-3 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                    <div className="h-4 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  </div>
                ))}
              </div>
            </CardBody>
          </Card>
          <Card>
            <CardBody>
              <div className="space-y-4">
                {Array.from({ length: 2 }).map((_, i) => (
                  <div key={i} className="space-y-1">
                    <div className="h-3 w-16 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                    <div className="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  </div>
                ))}
              </div>
            </CardBody>
          </Card>
        </div>
      </div>
    )
  }

  if (error || !patient) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data pasien</p>
        <Button variant="secondary" onClick={() => navigate('/patients')}>
          <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali ke Daftar Pasien
        </Button>
      </div>
    )
  }

  const mainFields = [
    { label: 'No. Rekam Medis', value: patient.medical_record_number },
    { label: 'NIK', value: patient.nik || '-' },
    { label: 'Tanggal Lahir', value: patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-' },
    { label: 'Jenis Kelamin', value: patient.gender === 'L' ? 'Laki-laki' : 'Perempuan' },
    { label: 'Telepon', value: patient.phone || '-' },
    { label: 'Alamat', value: patient.address || '-' },
    { label: 'Kontak Darurat', value: patient.emergency_contact || '-' },
    { label: 'Golongan Darah', value: patient.blood_type || '-' },
    { label: 'Alergi', value: Array.isArray(patient.allergies) ? patient.allergies.join(', ') : (patient.allergies || '-') },
  ]

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1.5">
            <Link to="/patients" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Pasien</Link>
            <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
            <span className="text-slate-600 dark:text-slate-400 truncate">{patient.name}</span>
          </nav>
          <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white truncate">{patient.name}</h1>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="ghost" onClick={() => navigate('/patients')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Kembali
          </Button>
          <Button variant="secondary" onClick={() => navigate(`/patients/${uuid}/edit`)} className="w-full sm:w-auto">
            <Pencil className="h-4 w-4 mr-1.5" /> Edit
          </Button>
          <Button variant="danger" onClick={() => setShowDelete(true)} className="w-full sm:w-auto">
            <Trash2 className="h-4 w-4 mr-1.5" /> Hapus
          </Button>
        </div>
      </div>

      {/* Content */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card className="md:col-span-2">
          <CardHeader>
            <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Informasi Pasien</h2>
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

        <Card>
          <CardHeader>
            <h2 className="text-sm font-semibold text-slate-900 dark:text-white">Status</h2>
          </CardHeader>
          <CardBody className="space-y-4">
            <div className="space-y-1">
              <dt className="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Asuransi</dt>
              <dd>
                <Badge variant={patient.insurance_type === 'bpjs' ? 'info' : patient.insurance_type === 'mandiri' ? 'default' : 'purple'}>
                  {patient.insurance_type?.toUpperCase() || '-'}
                </Badge>
              </dd>
            </div>
            <div className="space-y-1">
              <dt className="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Terdaftar</dt>
              <dd className="text-sm font-medium text-slate-900 dark:text-slate-100">
                {new Date(patient.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
              </dd>
            </div>
          </CardBody>
        </Card>
      </div>

      {/* Delete Modal */}
      <Modal isOpen={showDelete} onClose={() => setShowDelete(false)} title="Hapus Pasien" size="sm">
        <p className="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">
          Yakin ingin menghapus pasien <strong className="text-slate-900 dark:text-slate-100">{patient.name}</strong>?
        </p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowDelete(false)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
