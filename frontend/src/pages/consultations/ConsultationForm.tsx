import { useState, useEffect, type FormEvent } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useConsultation, useCreateConsultation, useUpdateConsultation } from '../../hooks/useConsultations'
import { useToastStore } from '../../stores/toastStore'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { api } from '../../lib/api'
import type { Consultation } from '../../types'
import { Save, ArrowLeft } from 'lucide-react'

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

  useEffect(() => {
    api.get<Doctor[]>('/doctors').then((res) => setDoctors(res.data)).catch(() => {})
  }, [])

  useEffect(() => {
    api.getPaginated<PatientOption>(`/patients?page=1&search=${encodeURIComponent(patientSearch)}`)
      .then((res) => setPatients(res.data))
      .catch(() => {})
  }, [patientSearch])

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
        <div className="space-y-2">
          <div className="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <div className="h-7 w-56 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-6">
          <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
            <div className="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {Array.from({ length: 4 }).map((_, i) => (
                <div key={i} className="space-y-1.5">
                  <div className="h-3 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  <div className="h-10 w-full bg-slate-200 dark:bg-slate-700 rounded-lg animate-shimmer" />
                </div>
              ))}
            </div>
          </div>
          <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
            <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
            {Array.from({ length: 3 }).map((_, i) => (
              <div key={i} className="space-y-1.5">
                <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                <div className="h-20 w-full bg-slate-200 dark:bg-slate-700 rounded-lg animate-shimmer" />
              </div>
            ))}
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div>
        <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1">
          <Link to="/consultations" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Konsultasi</Link>
          <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
          {isEdit ? 'Edit Konsultasi' : 'Konsultasi Baru'}
        </h1>
      </div>

      <form onSubmit={handleSubmit}>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
          <div className="p-6 space-y-6">
            {errors.general && (
              <div className="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400">
                {errors.general}
              </div>
            )}

            {/* Informasi Konsultasi */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Informasi Konsultasi</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Pasien <span className="text-red-500 ml-1">*</span>
                  </label>
                  <input
                    type="text"
                    placeholder="Cari pasien..."
                    value={patientSearch}
                    onChange={(e) => setPatientSearch(e.target.value)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all mb-1"
                  />
                  <select
                    value={form.patient_id || ''}
                    onChange={(e) => update('patient_id', e.target.value ? Number(e.target.value) : undefined)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="">Pilih pasien...</option>
                    {patients.map((p) => (
                      <option key={p.id} value={p.id}>
                        {p.name} ({p.medical_record_number})
                      </option>
                    ))}
                  </select>
                  {errors.patient_id && <p className="text-xs text-red-600 dark:text-red-400">{errors.patient_id}</p>}
                </div>

                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Dokter <span className="text-red-500 ml-1">*</span>
                  </label>
                  <select
                    value={form.doctor_id || ''}
                    onChange={(e) => update('doctor_id', e.target.value ? Number(e.target.value) : undefined)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="">Pilih dokter...</option>
                    {doctors.map((d) => (
                      <option key={d.id} value={d.id}>
                        {d.name}{d.specialization ? ` — ${d.specialization}` : ''}
                      </option>
                    ))}
                  </select>
                  {errors.doctor_id && <p className="text-xs text-red-600 dark:text-red-400">{errors.doctor_id}</p>}
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
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Status <span className="text-red-500 ml-1">*</span>
                  </label>
                  <select
                    value={form.status || 'scheduled'}
                    onChange={(e) => update('status', e.target.value)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    {statusOptions.map((opt) => (
                      <option key={opt.value} value={opt.value}>{opt.label}</option>
                    ))}
                  </select>
                  {errors.status && <p className="text-xs text-red-600 dark:text-red-400">{errors.status}</p>}
                </div>
              </div>
            </div>

            {/* Detail Medis */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Detail Medis</h3>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Keluhan Pasien <span className="text-red-500 ml-1">*</span>
                </label>
                <textarea
                  value={form.complaint || ''}
                  onChange={(e) => update('complaint', e.target.value)}
                  rows={3}
                  placeholder="Deskripsikan keluhan pasien..."
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                />
                {errors.complaint && <p className="text-xs text-red-600 dark:text-red-400">{errors.complaint}</p>}
              </div>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Diagnosis</label>
                <textarea
                  value={form.diagnosis || ''}
                  onChange={(e) => update('diagnosis', e.target.value)}
                  rows={2}
                  placeholder="Diagnosis dokter..."
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                />
                {errors.diagnosis && <p className="text-xs text-red-600 dark:text-red-400">{errors.diagnosis}</p>}
              </div>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Rencana Perawatan</label>
                <textarea
                  value={form.treatment_plan || ''}
                  onChange={(e) => update('treatment_plan', e.target.value)}
                  rows={2}
                  placeholder="Rencana perawatan..."
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                />
              </div>
            </div>

            {/* Jadwal & Catatan */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Jadwal & Catatan</h3>
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
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row justify-end gap-2 mt-4">
          <Button type="button" variant="subtle" onClick={() => navigate('/consultations')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Batal
          </Button>
          <Button type="submit" loading={loading} className="w-full sm:w-auto">
            <Save className="h-4 w-4 mr-1.5" /> {isEdit ? 'Simpan Perubahan' : 'Tambah Konsultasi'}
          </Button>
        </div>
      </form>
    </div>
  )
}
