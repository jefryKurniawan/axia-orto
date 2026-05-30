import { useState, useEffect, type FormEvent } from 'react'
import { useParams, useNavigate, useSearchParams, Link } from 'react-router-dom'
import { useProductionTracking, useCreateProductionTracking, useUpdateProductionTracking } from '../../hooks/useProduction'
import { useOrders } from '../../hooks/useOrders'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { api } from '../../lib/api'

interface UserOption { id: number; name: string; role: string }

export default function ProductionForm() {
  const { uuid } = useParams<{ uuid: string }>()
  const [searchParams] = useSearchParams()
  const orderFromUrl = searchParams.get('order') || ''
  const navigate = useNavigate()
  const isEdit = !!uuid
  const { data: existing, isLoading: loadingExisting } = useProductionTracking(uuid || '')
  const createMutation = useCreateProductionTracking()
  const updateMutation = useUpdateProductionTracking()
  const addToast = useToastStore((s) => s.addToast)

  const { data: ordersData } = useOrders(1, '', '')
  const [technicians, setTechnicians] = useState<UserOption[]>([])

  const [form, setForm] = useState({
    treatment_order_id: '',
    step: '',
    assigned_to: '',
    notes: '',
  })
  const [errors, setErrors] = useState<Record<string, string>>({})

  // Fetch technicians
  useEffect(() => {
    api.get<UserOption[]>('/doctors').then((res) => setTechnicians(res.data)).catch(() => {})
  }, [])

  useEffect(() => {
    if (existing) {
      setForm({
        treatment_order_id: String(existing.treatment_order_id),
        step: existing.step,
        assigned_to: String(existing.assigned_to),
        notes: existing.notes || '',
      })
    }
  }, [existing])

  useEffect(() => {
    if (orderFromUrl && ordersData?.data) {
      const match = ordersData.data.find((o) => o.uuid === orderFromUrl)
      if (match) {
        setForm((prev) => ({ ...prev, treatment_order_id: String(match.id) }))
      }
    }
  }, [orderFromUrl, ordersData])

  const update = (field: string, value: string) => {
    setForm((prev) => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors((prev) => { const e = { ...prev }; delete e[field]; return e })
  }

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setErrors({})

    const payload = {
      treatment_order_id: Number(form.treatment_order_id),
      step: form.step,
      assigned_to: Number(form.assigned_to),
      notes: form.notes || undefined,
    }

    try {
      if (isEdit && uuid) {
        await updateMutation.mutateAsync({ uuid, data: payload as any })
        addToast('success', 'Tracking berhasil diperbarui.')
      } else {
        await createMutation.mutateAsync(payload as any)
        addToast('success', 'Tracking berhasil dibuat.')
      }
      navigate('/production')
    } catch (err: any) {
      if (err.errors) {
        const flat: Record<string, string> = {}
        for (const [key, val] of Object.entries(err.errors)) {
          flat[key] = Array.isArray(val) ? val[0] : val as string
        }
        setErrors(flat)
        addToast('error', 'Ada kesalahan pada form. Periksa kembali isian Anda.')
      } else {
        addToast('error', err.message || 'Gagal menyimpan tracking.')
      }
    }
  }

  const orders = ordersData?.data || []
  const loading = createMutation.isPending || updateMutation.isPending

  if (isEdit && loadingExisting) {
    return (
      <div className="space-y-4">
        <div className="h-8 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        <div className="h-96 bg-slate-200 dark:bg-slate-700 rounded-xl animate-shimmer" />
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div>
        <nav className="text-sm text-slate-500 dark:text-slate-400 mb-1">
          <Link to="/production" className="hover:text-blue-600">Produksi</Link>
          <span className="mx-2">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">
          {isEdit ? 'Edit Tracking Produksi' : 'Tambah Tracking Produksi'}
        </h1>
      </div>

      <form onSubmit={handleSubmit}>
        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Tracking</h2>
          </CardHeader>
          <CardBody className="space-y-4">
            {errors.general && (
              <div className="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400">
                {errors.general}
              </div>
            )}

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Order <span className="text-red-500 ml-1">*</span>
              </label>
              <select
                value={form.treatment_order_id}
                onChange={(e) => update('treatment_order_id', e.target.value)}
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Pilih order...</option>
                {orders.map((o) => (
                  <option key={o.uuid} value={o.id}>{o.order_number} - {o.patient_name}</option>
                ))}
              </select>
              {errors.treatment_order_id && <p className="text-sm text-red-600 dark:text-red-400">{errors.treatment_order_id}</p>}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Input
                label="Langkah Produksi"
                placeholder="cth: Cetak, Assembly, Finishing..."
                required
                value={form.step}
                onChange={(e) => update('step', e.target.value)}
                error={errors.step}
              />
              <div className="space-y-1">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Teknisi <span className="text-red-500 ml-1">*</span>
                </label>
                <select
                  value={form.assigned_to}
                  onChange={(e) => update('assigned_to', e.target.value)}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">Pilih teknisi...</option>
                  {technicians.map((t) => (
                    <option key={t.id} value={t.id}>{t.name}</option>
                  ))}
                </select>
                {errors.assigned_to && <p className="text-sm text-red-600 dark:text-red-400">{errors.assigned_to}</p>}
              </div>
            </div>

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan</label>
              <textarea
                value={form.notes}
                onChange={(e) => update('notes', e.target.value)}
                rows={3}
                placeholder="Catatan tracking..."
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </CardBody>
        </Card>

        <div className="flex justify-end gap-2 mt-4">
          <Button type="button" variant="secondary" onClick={() => navigate('/production')}>Batal</Button>
          <Button type="submit" loading={loading}>
            {isEdit ? 'Simpan Perubahan' : 'Tambah Tracking'}
          </Button>
        </div>
      </form>
    </div>
  )
}
