import { useState, useEffect, type FormEvent } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { usePatient, useCreatePatient, useUpdatePatient } from '../../hooks/usePatients'
import { useToastStore } from '../../stores/toastStore'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import type { Patient } from '../../types'
import { Save, ArrowLeft } from 'lucide-react'

type FormData = Partial<Patient>

export default function PatientForm() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const isEdit = !!uuid
  const { data: existing, isLoading: loadingExisting } = usePatient(uuid || '')
  const createMutation = useCreatePatient()
  const updateMutation = useUpdatePatient()
  const addToast = useToastStore((s) => s.addToast)

  const [form, setForm] = useState<FormData>({
    name: '',
    nik: '',
    date_of_birth: '',
    gender: 'L',
    phone: '',
    address: '',
    emergency_contact: '',
    insurance_type: 'bpjs',
    blood_type: undefined,
    allergies: undefined,
  })
  const [errors, setErrors] = useState<Record<string, string>>({})

  useEffect(() => {
    if (existing) {
      setForm({
        name: existing.name,
        nik: existing.nik || '',
        date_of_birth: existing.date_of_birth,
        gender: existing.gender,
        phone: existing.phone || '',
        address: existing.address || '',
        emergency_contact: existing.emergency_contact || '',
        insurance_type: existing.insurance_type,
        blood_type: existing.blood_type,
        allergies: existing.allergies,
      })
    }
  }, [existing])

  const update = (field: keyof FormData, value: string | undefined) => {
    setForm((prev) => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors((prev) => { const e = { ...prev }; delete e[field]; return e })
  }

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setErrors({})

    try {
      if (isEdit) {
        await updateMutation.mutateAsync({ uuid: uuid!, data: form })
        addToast('success', 'Data pasien berhasil diperbarui.')
      } else {
        await createMutation.mutateAsync(form)
        addToast('success', 'Pasien baru berhasil ditambahkan.')
      }
      navigate('/patients')
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
          <div className="h-7 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-6">
          <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
            <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
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
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div>
        <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1">
          <Link to="/patients" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Pasien</Link>
          <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
          {isEdit ? 'Edit Pasien' : 'Tambah Pasien Baru'}
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

            {/* Informasi Dasar */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Informasi Dasar</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input
                  label="Nama Lengkap"
                  required
                  value={form.name || ''}
                  onChange={(e) => update('name', e.target.value)}
                  error={errors.name}
                  placeholder="Masukkan nama pasien"
                />
                <Input
                  label="NIK"
                  value={form.nik || ''}
                  onChange={(e) => update('nik', e.target.value)}
                  error={errors.nik}
                  placeholder="16 digit NIK"
                  maxLength={16}
                />
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input
                  label="Tanggal Lahir"
                  type="date"
                  required
                  value={form.date_of_birth || ''}
                  onChange={(e) => update('date_of_birth', e.target.value)}
                  error={errors.date_of_birth}
                />
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Jenis Kelamin <span className="text-red-500 ml-1">*</span>
                  </label>
                  <select
                    value={form.gender || 'L'}
                    onChange={(e) => update('gender', e.target.value)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                  {errors.gender && <p className="text-xs text-red-600 dark:text-red-400">{errors.gender}</p>}
                </div>
              </div>
            </div>

            {/* Kontak & Alamat */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Kontak & Alamat</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input
                  label="Telepon"
                  value={form.phone || ''}
                  onChange={(e) => update('phone', e.target.value)}
                  error={errors.phone}
                  placeholder="08xxxxxxxxxx"
                />
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Asuransi <span className="text-red-500 ml-1">*</span>
                  </label>
                  <select
                    value={form.insurance_type || 'bpjs'}
                    onChange={(e) => update('insurance_type', e.target.value)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="bpjs">BPJS</option>
                    <option value="mandiri">Mandiri</option>
                    <option value="asuransi">Asuransi</option>
                  </select>
                  {errors.insurance_type && <p className="text-xs text-red-600 dark:text-red-400">{errors.insurance_type}</p>}
                </div>
              </div>
              <Input
                label="Alamat"
                value={form.address || ''}
                onChange={(e) => update('address', e.target.value)}
                error={errors.address}
                placeholder="Alamat lengkap"
              />
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input
                  label="Kontak Darurat"
                  value={form.emergency_contact || ''}
                  onChange={(e) => update('emergency_contact', e.target.value)}
                  error={errors.emergency_contact}
                  placeholder="Nama dan nomor telepon"
                />
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Golongan Darah</label>
                  <select
                    value={form.blood_type || ''}
                    onChange={(e) => update('blood_type', e.target.value || undefined)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="">-</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row justify-end gap-2 mt-4">
          <Button type="button" variant="subtle" onClick={() => navigate('/patients')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Batal
          </Button>
          <Button type="submit" loading={loading} className="w-full sm:w-auto">
            <Save className="h-4 w-4 mr-1.5" /> {isEdit ? 'Simpan Perubahan' : 'Tambah Pasien'}
          </Button>
        </div>
      </form>
    </div>
  )
}
