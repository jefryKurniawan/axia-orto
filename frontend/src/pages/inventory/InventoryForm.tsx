import { useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { Package } from 'lucide-react'
import { useInventoryItem, useCreateInventoryItem, useUpdateInventoryItem } from '../../hooks/useInventory'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'

interface FormData {
  code: string
  name: string
  description: string
  category: 'bahan_baku' | 'komponen' | 'alat_jadi'
  unit: string
  quantity: number
  reorder_level: number
  price: number
  is_active: boolean
}

export default function InventoryForm() {
  const navigate = useNavigate()
  const { uuid } = useParams()
  const isEdit = !!uuid
  const { data, isLoading: loadingItem } = useInventoryItem(uuid || '')
  const createMutation = useCreateInventoryItem()
  const updateMutation = useUpdateInventoryItem()
  const addToast = useToastStore((s) => s.addToast)

  const { register, handleSubmit, reset, formState: { errors } } = useForm<FormData>({
    defaultValues: {
      code: '',
      name: '',
      description: '',
      category: 'bahan_baku',
      unit: '',
      quantity: 0,
      reorder_level: 0,
      price: 0,
      is_active: true,
    },
  })

  useEffect(() => {
    if (isEdit && data?.item) {
      reset({
        code: data.item.code,
        name: data.item.name,
        description: data.item.description || '',
        category: data.item.category,
        unit: data.item.unit,
        quantity: data.item.quantity,
        reorder_level: data.item.reorder_level,
        price: data.item.price,
        is_active: data.item.is_active,
      })
    }
  }, [isEdit, data, reset])

  const onSubmit = (data: FormData) => {
    if (isEdit && uuid) {
      updateMutation.mutate(
        { uuid, data },
        {
          onSuccess: () => {
            addToast('success', 'Item inventory berhasil diperbarui.')
            navigate('/inventory')
          },
          onError: () => addToast('error', 'Gagal memperbarui item inventory.'),
        }
      )
    } else {
      createMutation.mutate(data, {
        onSuccess: () => {
          addToast('success', 'Item inventory berhasil ditambahkan.')
          navigate('/inventory')
        },
        onError: () => addToast('error', 'Gagal menambahkan item inventory.'),
      })
    }
  }

  const loading = createMutation.isPending || updateMutation.isPending

  if (isEdit && loadingItem) {
    return (
      <div className="flex items-center justify-center py-12">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600" />
      </div>
    )
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-3">
        <button onClick={() => navigate('/inventory')} className="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
          <Package className="w-5 h-5 text-slate-500 dark:text-slate-400" />
        </button>
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">
          {isEdit ? 'Edit Item Inventory' : 'Tambah Item Inventory'}
        </h1>
      </div>

      <Card>
        <CardHeader>
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">Informasi Item</h2>
        </CardHeader>
        <CardBody>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kode *</label>
                <Input {...register('code', { required: 'Kode wajib diisi' })} placeholder="INV-001" />
                {errors.code && <p className="text-red-500 text-xs mt-1">{errors.code.message}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama *</label>
                <Input {...register('name', { required: 'Nama wajib diisi' })} placeholder="Nama item" />
                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name.message}</p>}
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
              <textarea
                {...register('description')}
                placeholder="Deskripsi item (opsional)"
                rows={3}
                className="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori *</label>
                <select
                  {...register('category', { required: 'Kategori wajib dipilih' })}
                  className="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm"
                >
                  <option value="bahan_baku">Bahan Baku</option>
                  <option value="komponen">Komponen</option>
                  <option value="alat_jadi">Alat Jadi</option>
                </select>
                {errors.category && <p className="text-red-500 text-xs mt-1">{errors.category.message}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Satuan *</label>
                <Input {...register('unit', { required: 'Satuan wajib diisi' })} placeholder="pcs, kg, meter" />
                {errors.unit && <p className="text-red-500 text-xs mt-1">{errors.unit.message}</p>}
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stok Awal *</label>
                <Input type="number" {...register('quantity', { required: 'Stok wajib diisi', min: { value: 0, message: 'Minimal 0' } })} />
                {errors.quantity && <p className="text-red-500 text-xs mt-1">{errors.quantity.message}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Batas Minimum *</label>
                <Input type="number" {...register('reorder_level', { required: 'Batas minimum wajib diisi', min: { value: 0, message: 'Minimal 0' } })} />
                {errors.reorder_level && <p className="text-red-500 text-xs mt-1">{errors.reorder_level.message}</p>}
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga (Rp) *</label>
                <Input type="number" {...register('price', { required: 'Harga wajib diisi', min: { value: 0, message: 'Minimal 0' } })} />
                {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price.message}</p>}
              </div>
            </div>

            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="is_active"
                {...register('is_active')}
                className="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500"
              />
              <label htmlFor="is_active" className="text-sm text-slate-700 dark:text-slate-300">Item Aktif</label>
            </div>

            <div className="flex justify-end gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
              <Button variant="secondary" type="button" onClick={() => navigate('/inventory')}>Batal</Button>
              <Button type="submit" loading={loading}>
                {isEdit ? 'Simpan Perubahan' : 'Tambah Item'}
              </Button>
            </div>
          </form>
        </CardBody>
      </Card>
    </div>
  )
}
