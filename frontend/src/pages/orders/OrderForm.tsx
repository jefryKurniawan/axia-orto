import { useState, useEffect, type FormEvent } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useOrder, useCreateOrder, useUpdateOrder } from '../../hooks/useOrders'
import { usePatients } from '../../hooks/usePatients'
import { useActiveServices } from '../../hooks/useServices'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import type { OrderItem } from '../../types'

interface ServiceEntry {
  service_id: number
  quantity: number
  specifications?: Record<string, unknown>
}

export default function OrderForm() {
  const { uuid } = useParams<{ uuid: string }>()
  const navigate = useNavigate()
  const isEdit = !!uuid
  const { data: existing, isLoading: loadingExisting } = useOrder(uuid || '')
  const createMutation = useCreateOrder()
  const updateMutation = useUpdateOrder()
  const addToast = useToastStore((s) => s.addToast)

  const { data: patientsData } = usePatients(1, '')
  const { data: servicesList } = useActiveServices()

  const [form, setForm] = useState({
    patient_id: '',
    consultation_id: '',
    order_date: new Date().toISOString().split('T')[0],
    delivery_date: '',
    notes: '',
  })
  const [services, setServices] = useState<ServiceEntry[]>([
    { service_id: 0, quantity: 1 },
  ])
  const [errors, setErrors] = useState<Record<string, string>>({})

  useEffect(() => {
    if (existing) {
      setForm({
        patient_id: String(existing.patient_id),
        consultation_id: existing.consultation_id ? String(existing.consultation_id) : '',
        order_date: existing.order_date,
        delivery_date: existing.delivery_date || '',
        notes: existing.notes || '',
      })
      if (existing.order_items?.length) {
        setServices(existing.order_items.map((item: OrderItem) => ({
          service_id: item.service_id,
          quantity: item.quantity,
          specifications: item.specifications,
        })))
      }
    }
  }, [existing])

  const update = (field: string, value: string) => {
    setForm((prev) => ({ ...prev, [field]: value }))
    if (errors[field]) setErrors((prev) => { const e = { ...prev }; delete e[field]; return e })
  }

  const addService = () => {
    setServices((prev) => [...prev, { service_id: 0, quantity: 1 }])
  }

  const removeService = (index: number) => {
    setServices((prev) => prev.filter((_, i) => i !== index))
  }

  const updateService = (index: number, field: keyof ServiceEntry, value: unknown) => {
    setServices((prev) => prev.map((s, i) => i === index ? { ...s, [field]: value } : s))
  }

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setErrors({})

    const payload = {
      ...form,
      patient_id: Number(form.patient_id),
      consultation_id: form.consultation_id ? Number(form.consultation_id) : undefined,
      services: services.filter((s) => s.service_id > 0),
    }

    try {
      if (isEdit && uuid) {
        await updateMutation.mutateAsync({ uuid, data: payload as any })
        addToast('success', 'Order berhasil diperbarui.')
      } else {
        await createMutation.mutateAsync(payload as any)
        addToast('success', 'Order berhasil dibuat.')
      }
      navigate('/orders')
    } catch (err: any) {
      if (err.errors) {
        const flat: Record<string, string> = {}
        for (const [key, val] of Object.entries(err.errors)) {
          flat[key] = Array.isArray(val) ? val[0] : val as string
        }
        setErrors(flat)
        addToast('error', 'Ada kesalahan pada form. Periksa kembali isian Anda.')
      } else {
        addToast('error', err.message || 'Gagal menyimpan order.')
      }
    }
  }

  const patients = patientsData?.data || []
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
          <Link to="/orders" className="hover:text-blue-600">Order</Link>
          <span className="mx-2">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">
          {isEdit ? 'Edit Order' : 'Buat Order Baru'}
        </h1>
      </div>

      <form onSubmit={handleSubmit}>
        <Card>
          <CardHeader>
            <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Order</h2>
          </CardHeader>
          <CardBody className="space-y-4">
            {errors.general && (
              <div className="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400">
                {errors.general}
              </div>
            )}

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                Pasien <span className="text-red-500 ml-1">*</span>
              </label>
              <select
                value={form.patient_id}
                onChange={(e) => update('patient_id', e.target.value)}
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Pilih pasien...</option>
                {patients.map((p) => (
                  <option key={p.uuid} value={p.id}>{p.name} ({p.medical_record_number})</option>
                ))}
              </select>
              {errors.patient_id && <p className="text-sm text-red-600 dark:text-red-400">{errors.patient_id}</p>}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Input
                label="Tanggal Order"
                type="date"
                required
                value={form.order_date}
                onChange={(e) => update('order_date', e.target.value)}
                error={errors.order_date}
              />
              <Input
                label="Tanggal Pengiriman"
                type="date"
                value={form.delivery_date}
                onChange={(e) => update('delivery_date', e.target.value)}
                error={errors.delivery_date}
              />
            </div>

            <div className="space-y-1">
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan</label>
              <textarea
                value={form.notes}
                onChange={(e) => update('notes', e.target.value)}
                rows={3}
                placeholder="Catatan order..."
                className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </CardBody>
        </Card>

        <Card className="mt-4">
          <CardHeader>
            <div className="flex justify-between items-center">
              <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Layanan</h2>
              <Button type="button" size="sm" variant="ghost" onClick={addService}>+ Tambah</Button>
            </div>
          </CardHeader>
          <CardBody className="space-y-3">
            {services.map((svc, index) => (
              <div key={index} className="flex gap-3 items-end">
                <div className="flex-1 space-y-1">
                  <label className="block text-xs text-slate-500 dark:text-slate-400">Layanan</label>
                  <select
                    value={svc.service_id}
                    onChange={(e) => updateService(index, 'service_id', Number(e.target.value))}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value={0}>Pilih layanan...</option>
                    {(servicesList || []).map((s) => (
                      <option key={s.uuid} value={s.id}>{s.name} (Rp {Number(s.price).toLocaleString('id-ID')})</option>
                    ))}
                  </select>
                </div>
                <div className="w-24">
                  <label className="block text-xs text-slate-500 dark:text-slate-400">Jumlah</label>
                  <Input
                    type="number"
                    min={1}
                    value={String(svc.quantity)}
                    onChange={(e) => updateService(index, 'quantity', Number(e.target.value))}
                  />
                </div>
                {services.length > 1 && (
                  <Button type="button" size="sm" variant="danger" onClick={() => removeService(index)}>
                    Hapus
                  </Button>
                )}
              </div>
            ))}
            {errors.services && <p className="text-sm text-red-600 dark:text-red-400">{errors.services}</p>}
          </CardBody>
        </Card>

        <div className="flex justify-end gap-2 mt-4">
          <Button type="button" variant="secondary" onClick={() => navigate('/orders')}>Batal</Button>
          <Button type="submit" loading={loading}>
            {isEdit ? 'Simpan Perubahan' : 'Buat Order'}
          </Button>
        </div>
      </form>
    </div>
  )
}
