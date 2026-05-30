import { useState, useEffect } from 'react'
import { useNavigate, useParams, Link } from 'react-router-dom'
import { useService, useCreateService, useUpdateService } from '../../hooks/useServices'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'

const serviceTypes = [
  { value: 'konsultasi', label: 'Konsultasi' },
  { value: 'ortosis', label: 'Ortosis' },
  { value: 'protesis', label: 'Protesis' },
  { value: 'terapi', label: 'Terapi' },
  { value: 'alat', label: 'Alat' },
]

export default function ServiceForm() {
  const { uuid } = useParams()
  const isEdit = !!uuid
  const navigate = useNavigate()
  const addToast = useToastStore((s) => s.addToast)

  const { data: existing, isLoading: loadingExisting } = useService(uuid || '')
  const createMutation = useCreateService()
  const updateMutation = useUpdateService()

  const [form, setForm] = useState({
    name: '',
    description: '',
    service_type: 'konsultasi',
    price: '',
    duration_days: '',
    is_active: true,
  })
  const [errors, setErrors] = useState<Record<string, string>>({})

  useEffect(() => {
    if (existing) {
      setForm({
        name: existing.name,
        description: existing.description || '',
        service_type: existing.service_type,
        price: String(existing.price),
        duration_days: existing.duration_days ? String(existing.duration_days) : '',
        is_active: existing.is_active,
      })
    }
  }, [existing])

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value, type } = e.target
    setForm((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? (e.target as HTMLInputElement).checked : value,
    }))
    if (errors[name]) setErrors((prev) => ({ ...prev, [name]: '' }))
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setErrors({})

    const payload = {
      name: form.name,
      description: form.description || undefined,
      service_type: form.service_type as 'konsultasi' | 'ortosis' | 'protesis' | 'terapi' | 'alat',
      price: Number(form.price),
      duration_days: form.duration_days ? Number(form.duration_days) : undefined,
      is_active: form.is_active,
    }

    try {
      if (isEdit) {
        await updateMutation.mutateAsync({ uuid: uuid!, data: payload })
        addToast('success', 'Layanan berhasil diperbarui.')
      } else {
        await createMutation.mutateAsync(payload)
        addToast('success', 'Layanan berhasil ditambahkan.')
      }
      navigate('/services')
    } catch (err: unknown) {
      if (err && typeof err === 'object' && 'errors' in err) {
        const flat: Record<string, string> = {}
        for (const [k, v] of Object.entries((err as { errors: Record<string, string[]> }).errors)) {
          flat[k] = (v as string[])[0]
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
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        <div className="h-64 bg-slate-200 dark:bg-slate-700 rounded-xl animate-shimmer" />
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div>
        <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
          <Link to="/services" className="hover:text-blue-600">Layanan</Link>
          <span className="mx-2">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">
          {isEdit ? 'Edit Layanan' : 'Tambah Layanan'}
        </h1>
      </div>

      {errors.general && (
        <div className="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
          {errors.general}
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <Card>
          <CardHeader>
            <h2 className="font-semibold text-slate-900 dark:text-slate-100">Informasi Layanan</h2>
          </CardHeader>
          <CardBody>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <Input
                  label="Nama Layanan *"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  error={errors.name}
                  placeholder="Contoh: Konsultasi Ortotik"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                  Tipe Layanan *
                </label>
                <select
                  name="service_type"
                  value={form.service_type}
                  onChange={handleChange}
                  className="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  {serviceTypes.map((t) => (
                    <option key={t.value} value={t.value}>{t.label}</option>
                  ))}
                </select>
                {errors.service_type && <p className="text-sm text-red-600 dark:text-red-400 mt-1">{errors.service_type}</p>}
              </div>

              <div>
                <Input
                  label="Harga (Rp) *"
                  name="price"
                  type="number"
                  value={form.price}
                  onChange={handleChange}
                  error={errors.price}
                  placeholder="150000"
                  min="0"
                />
              </div>

              <div>
                <Input
                  label="Durasi (hari)"
                  name="duration_days"
                  type="number"
                  value={form.duration_days}
                  onChange={handleChange}
                  error={errors.duration_days}
                  placeholder="7"
                  min="1"
                />
              </div>

              <div className="flex items-center gap-2 pt-6">
                <input
                  type="checkbox"
                  name="is_active"
                  id="is_active"
                  checked={form.is_active}
                  onChange={handleChange}
                  className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                />
                <label htmlFor="is_active" className="text-sm text-slate-700 dark:text-slate-300">
                  Layanan aktif
                </label>
              </div>

              <div className="sm:col-span-2">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                  Deskripsi
                </label>
                <textarea
                  name="description"
                  value={form.description}
                  onChange={handleChange}
                  rows={3}
                  className="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Deskripsi layanan (opsional)"
                />
                {errors.description && <p className="text-sm text-red-600 dark:text-red-400 mt-1">{errors.description}</p>}
              </div>
            </div>

            <div className="flex gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
              <Button type="submit" disabled={loading}>
                {loading ? 'Menyimpan...' : isEdit ? 'Simpan Perubahan' : 'Tambah Layanan'}
              </Button>
              <Button type="button" variant="secondary" onClick={() => navigate('/services')}>
                Batal
              </Button>
            </div>
          </CardBody>
        </Card>
      </form>
    </div>
  )
}
