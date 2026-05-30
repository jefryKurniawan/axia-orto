import { useState, useEffect, type FormEvent } from 'react'
import { useParams, useNavigate, useSearchParams, Link } from 'react-router-dom'
import { usePayment, useCreatePayment, useUpdatePayment } from '../../hooks/usePayments'
import { useOrders } from '../../hooks/useOrders'
import { useToastStore } from '../../stores/toastStore'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { Save, ArrowLeft } from 'lucide-react'

export default function PaymentForm() {
  const { uuid } = useParams<{ uuid: string }>()
  const [searchParams] = useSearchParams()
  const orderFromUrl = searchParams.get('order') || ''
  const navigate = useNavigate()
  const isEdit = !!uuid
  const { data: existing, isLoading: loadingExisting } = usePayment(uuid || '')
  const createMutation = useCreatePayment()
  const updateMutation = useUpdatePayment()
  const addToast = useToastStore((s) => s.addToast)

  const { data: ordersData } = useOrders(1, '', '')

  const [form, setForm] = useState({
    treatment_order_id: '',
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: 'cash',
    amount: '',
    notes: '',
  })
  const [errors, setErrors] = useState<Record<string, string>>({})

  useEffect(() => {
    if (existing) {
      setForm({
        treatment_order_id: String(existing.treatment_order_id),
        payment_date: existing.payment_date,
        payment_method: existing.payment_method,
        amount: String(existing.amount),
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
      payment_date: form.payment_date,
      payment_method: form.payment_method,
      amount: Number(form.amount),
      notes: form.notes || undefined,
    }

    try {
      if (isEdit && uuid) {
        await updateMutation.mutateAsync({ uuid, data: payload as any })
        addToast('success', 'Pembayaran berhasil diperbarui.')
      } else {
        await createMutation.mutateAsync(payload)
        addToast('success', 'Pembayaran berhasil dicatat.')
      }
      navigate('/payments')
    } catch (err: any) {
      if (err.errors) {
        const flat: Record<string, string> = {}
        for (const [key, val] of Object.entries(err.errors)) {
          flat[key] = Array.isArray(val) ? val[0] : val as string
        }
        setErrors(flat)
        addToast('error', 'Ada kesalahan pada form. Periksa kembali isian Anda.')
      } else {
        addToast('error', err.message || 'Gagal menyimpan pembayaran.')
      }
    }
  }

  const orders = ordersData?.data || []
  const loading = createMutation.isPending || updateMutation.isPending

  if (isEdit && loadingExisting) {
    return (
      <div className="space-y-4">
        <div className="space-y-2">
          <div className="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <div className="h-7 w-52 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-6">
          <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
            <div className="h-3 w-40 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
            <div className="space-y-1.5">
              <div className="h-3 w-16 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
              <div className="h-10 w-full bg-slate-200 dark:bg-slate-700 rounded-lg animate-shimmer" />
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {Array.from({ length: 2 }).map((_, i) => (
                <div key={i} className="space-y-1.5">
                  <div className="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
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
          <Link to="/payments" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Pembayaran</Link>
          <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
          {isEdit ? 'Edit Pembayaran' : 'Tambah Pembayaran'}
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

            {/* Informasi Pembayaran */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Informasi Pembayaran</h3>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Order <span className="text-red-500 ml-1">*</span>
                </label>
                <select
                  value={form.treatment_order_id}
                  onChange={(e) => update('treatment_order_id', e.target.value)}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                >
                  <option value="">Pilih order...</option>
                  {orders.map((o) => (
                    <option key={o.uuid} value={o.id}>{o.order_number} - {o.patient_name}</option>
                  ))}
                </select>
                {errors.treatment_order_id && <p className="text-xs text-red-600 dark:text-red-400">{errors.treatment_order_id}</p>}
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input
                  label="Tanggal Pembayaran"
                  type="date"
                  required
                  value={form.payment_date}
                  onChange={(e) => update('payment_date', e.target.value)}
                  error={errors.payment_date}
                />
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Metode Pembayaran <span className="text-red-500 ml-1">*</span>
                  </label>
                  <select
                    value={form.payment_method}
                    onChange={(e) => update('payment_method', e.target.value)}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="debit_card">Kartu Debit</option>
                    <option value="credit_card">Kartu Kredit</option>
                  </select>
                  {errors.payment_method && <p className="text-xs text-red-600 dark:text-red-400">{errors.payment_method}</p>}
                </div>
              </div>
              <Input
                label="Jumlah (Rp)"
                type="number"
                min={1}
                required
                value={form.amount}
                onChange={(e) => update('amount', e.target.value)}
                error={errors.amount}
              />
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan</label>
                <textarea
                  value={form.notes}
                  onChange={(e) => update('notes', e.target.value)}
                  rows={3}
                  placeholder="Catatan pembayaran..."
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                />
              </div>
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row justify-end gap-2 mt-4">
          <Button type="button" variant="subtle" onClick={() => navigate('/payments')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Batal
          </Button>
          <Button type="submit" loading={loading} className="w-full sm:w-auto">
            <Save className="h-4 w-4 mr-1.5" /> Simpan
          </Button>
        </div>
      </form>
    </div>
  )
}
