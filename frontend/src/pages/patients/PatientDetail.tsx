import { useParams, useNavigate, Link } from 'react-router-dom'
import { usePatient, useDeletePatient } from '../../hooks/usePatients'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Badge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { useState } from 'react'

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
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-pulse" />
        <div className="h-64 bg-slate-200 dark:bg-slate-700 rounded-xl animate-pulse" />
      </div>
    )
  }

  if (error || !patient) {
    return (
      <div className="text-center py-12">
        <p className="text-red-600 dark:text-red-400 mb-4">Gagal memuat data pasien</p>
        <Button variant="secondary" onClick={() => navigate('/patients')}>
          Kembali ke Daftar Pasien
        </Button>
      </div>
    )
  }

  const infoItems = [
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
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="min-w-0">
          <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
            <Link to="/patients" className="hover:text-blue-600">Pasien</Link>
            <span className="mx-2">/</span>
            <span className="text-slate-900 dark:text-slate-100 truncate">{patient.name}</span>
          </nav>
          <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100 truncate">{patient.name}</h1>
        </div>
        <div className="flex gap-2 flex-shrink-0">
          <Button variant="secondary" onClick={() => navigate(`/patients/${uuid}/edit`)} className="flex-1 sm:flex-none">
            Edit
          </Button>
          <Button variant="danger" onClick={() => setShowDelete(true)} className="flex-1 sm:flex-none">
            Hapus
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card className="lg:col-span-2">
          <CardHeader>
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Pasien</h2>
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

        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Status</h2>
          </CardHeader>
          <CardBody className="space-y-4">
            <div>
              <dt className="text-sm text-slate-500 dark:text-slate-400">Asuransi</dt>
              <dd className="mt-1">
                <Badge variant={patient.insurance_type === 'bpjs' ? 'info' : patient.insurance_type === 'mandiri' ? 'default' : 'purple'}>
                  {patient.insurance_type?.toUpperCase() || '-'}
                </Badge>
              </dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500 dark:text-slate-400">Terdaftar</dt>
              <dd className="mt-1 text-sm text-slate-900 dark:text-slate-100">
                {new Date(patient.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
              </dd>
            </div>
          </CardBody>
        </Card>
      </div>

      <Modal isOpen={showDelete} onClose={() => setShowDelete(false)} title="Hapus Pasien" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus pasien <strong>{patient.name}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowDelete(false)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
