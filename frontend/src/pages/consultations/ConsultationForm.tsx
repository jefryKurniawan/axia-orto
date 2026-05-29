import { useState, useEffect, type FormEvent } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useConsultation, useCreateConsultation, useUpdateConsultation } from '../../hooks/useConsultations'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { api } from '../../lib/api'
import type { Consultation } from '../../types'

type FormData = Partial<Consultation>

interface Doctor { id: number; name: string; specialization: string | null }
interface PatientOption { id: number; name: string; medical_record_number: string }

const statusOptions = [
  { value: 'scheduled', label: 'Dijadwalkan' },
  { value: 'in_progress', label: 'Berlangsung' },
  { value: 'completed', label: 'Selesai' },
  { value: 'cancelled', label: 'Dibatalkan' },
]

export default function ConsultationForm() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const isEdit = !!uuid
  const { data: existing, isLoading: loadingExisting } = useConsultation(uuid || '')
  const createMutation = useCreateConsultation()
  const updateMutation = useUpdateConsultation()
  const addToast = useToastStore((s) => s.addToast)

  const [form, setForm] = useState<FormData>({
    patient_id: undefined,
    doctor_id: undefined,
    consultation_date: '',
    complaint: '',
    diagnosis: '',
    treatment_plan: '',
    notes: '',
    follow_up_date: '',
    status: 'scheduled',
  })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [doctors, setDoctors] = useState<Doctor[]>([])
  const [patients, setPatients] = useState<PatientOption[]>([])
  const [patientSearch, setPatientSearch] = useState('')

  // Fetch doctors
  useEffect(() => {
    api.get<Doctor[]>('/doctors').then((res) => setDoctors(res.data)).catch(() => {})
  }, [])

  // Fetch patients for dropdown
  useEffect(() => {
    api.getPaginated<PatientOption>(`/patients?page=1&search=${encodeURIComponent(patientSearch)}`)
      .then((res) => setPatients(res.data))
      .catch(() => {})
  }, [patientSearch])

  // Populate form when existing data loads
  useEffect(() => {
    if (existing) {
      setForm({
        patient_id: existing.patient_id,
        doctor_id: existing.doctor_id,
        consultation_date: existing.consultation_date?.split('T')[0] || existing.consultation_date?.split(' ')[0] || '',
        complaint: existing.complaint || '',
        diagnosis: existing.diagnosis || '',
        treatment_plan: existing.treatment_plan || '',
        notes: existing.notes || '',
        follow_up_date: existing.follow_up_date?.split('T')[0] || '',
        status: existing.status || 'scheduled',
      })
    }
  }, [existing])

  const update = (field: keyof FormData, value: string | number | undefined) => {
    setForm((prev) => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors((prev) => { const e = { ...prev }; delete e[field]; return e })
  }

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setErrors({})

    try {
      if (isEdit) {
        await updateMutation.mutateAsync({ uuid: uuid!, data: form })
        addToast('success', 'Konsultasi berhasil diperbarui.')
      } else {
        await createMutation.mutateAsync(form)
        addToast('success', 'Konsultasi baru berhasil ditambahkan.')
      }
      navigate('/consultations')
    } catch (err: unknown) {
      if (err && typeof err === 'object' && 'errors' in err && err.errors) {
        const flat: Record<string, string> = {}
        for (const [key, val] of Object.entries(err.errors as Record<string, string[]>)) {
          flat[key] = Array.isArray(val) ? val[0] : val
        }
        setErrors(flat)
        addToast('error', 'Ada kesalahan pada form. Periksa kembali isian Anda.')
      } else {
        const msg = err instanceof Error ? err.message : 'Terjadi kesalahan'
        setErrors({ general: msg })
        addToast('error', msg)
      }
    }
  }

  const loading = createMutation.isPending || updateMutation.isPending

  if (isEdit && loadingExisting) {
    return (
      <div className="space-y-4">
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-pulse" />
        <div className="h-96 bg-slate-200 dark:bg-slate-700 rounded-xl animate-pulse" />
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div>
        <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
          <Link to="/consultations" className="hover:text-blue-600">Konsultasi</Link>
          <span className="mx-2">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">
          {isEdit ? 'Edit Konsultasi' : 'Konsultasi Baru'}
        </h1>
      </div>

      <form onSubmit={handleSubmit}>
        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Data Konsultasi</h2>
          </CardHeader>
          <CardBody className="space-y-4">
            {errors.general && (
              <div className="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400">
                {errors.general}
              </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Pasien <span className="text-red-500 ml-1">*</span>
                </label>
                <input
                  type="text"
                  placeholder="Cari pasien..."
                  value={patientSearch}
                  onChange={(e) => setPatientSearch(e.target.value)}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-1"
                />
                <select
                  value={form.patient_id || ''}
                  onChange={(e) => update('patient_id', e.target.value ? Number(e.target.value) : undefined)}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Pilih pasien...</option>
                  {patients.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.name} ({p.medical_record_number})
                    </option>
                  ))}
                </select>
                {errors.patient_id && <p className="text-sm text-red-600 dark:text-red-400">{errors.patient_id}</p>}
              </div>

              <div className="space-y-1">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Dokter <span className="text-red-500 ml-1">*</span>
                </label>
                <select
                  value={form.doctor_id || ''}
                  onChange={(e) => update('doctor_id', e.target.value ? Number(e.target.value) : undefined)}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Pilih dokter...</option>
                  {doctors.map((d) => (
                    <option key={d.id} value={d.id}>
                      {d.name}{d.specialization ? ` — ${d.specialization}` : ''}
                    </option>
                  ))}
                </select>
                {errors.doctor_id && <p className="text-sm text-red-600 dark:text-red-400">{errors.doctor_id}</p>}
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Input
                label="Tanggal Konsultasi"
                type="date"
                required
                value={form.consultation_date || ''}
                onChange={(e) => update('consultation_date', e.target.value)}
                error={errors.consultation_date}
              />
              <div className="space-y-1">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Status <span className="text-red-500 ml-1">*</span>
                </label>
                <select
                  value={form.status || 'scheduled'}
                  onChange={(e) => update('status', e.target.value)}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  {statusOptions.map((opt) => (
                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                  ))}
                </select>
                {errors.status && <p className="text-sm text-red-600 dark:text-red-400">{errors.status}</p>}
              </div>
            </div>

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Keluhan Pasien <span className="text-red-500 ml-1">*</span>
              </label>
              <textarea
                value={form.complaint || ''}
                onChange={(e) => update('complaint', e.target.value)}
                rows={3}
                placeholder="Deskripsikan keluhan pasien..."
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              {errors.complaint && <p className="text-sm text-red-600 dark:text-red-400">{errors.complaint}</p>}
            </div>

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Diagnosis</label>
              <textarea
                value={form.diagnosis || ''}
                onChange={(e) => update('diagnosis', e.target.value)}
                rows={2}
                placeholder="Diagnosis dokter..."
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              {errors.diagnosis && <p className="text-sm text-red-600 dark:text-red-400">{errors.diagnosis}</p>}
            </div>

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Rencana Perawatan</label>
              <textarea
                value={form.treatment_plan || ''}
                onChange={(e) => update('treatment_plan', e.target.value)}
                rows={2}
                placeholder="Rencana perawatan..."
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Input
                label="Tanggal Kontrol Ulang"
                type="date"
                value={form.follow_up_date || ''}
                onChange={(e) => update('follow_up_date', e.target.value)}
                error={errors.follow_up_date}
              />
              <Input
                label="Catatan Tambahan"
                value={form.notes || ''}
                onChange={(e) => update('notes', e.target.value)}
                error={errors.notes}
                placeholder="Catatan opsional"
              />
            </div>
          </CardBody>
        </Card>

        <div className="flex justify-end gap-2 mt-4">
          <Button type="button" variant="secondary" onClick={() => navigate('/consultations')}>
            Batal
          </Button>
          <Button type="submit" loading={loading}>
            {isEdit ? 'Simpan Perubahan' : 'Tambah Konsultasi'}
          </Button>
        </div>
      </form>
    </div>
  )
}
