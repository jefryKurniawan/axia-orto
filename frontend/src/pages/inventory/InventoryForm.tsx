import { useEffect } from 'react'
import { useNavigate, useParams, Link } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { Save, ArrowLeft } from 'lucide-react'
import { useInventoryItem, useCreateInventoryItem, useUpdateInventoryItem } from '../../hooks/useInventory'
import { useToastStore } from '../../stores/toastStore'
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
      <div className="space-y-4">
        <div className="space-y-2">
          <div className="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
          <div className="h-7 w-52 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
        </div>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-6">
          <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
            <div className="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {Array.from({ length: 2 }).map((_, i) => (
                <div key={i} className="space-y-1.5">
                  <div className="h-3 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
                  <div className="h-10 w-full bg-slate-200 dark:bg-slate-700 rounded-lg animate-shimmer" />
                </div>
              ))}
            </div>
          </div>
          <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
            <div className="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-shimmer" />
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              {Array.from({ length: 3 }).map((_, i) => (
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
          <Link to="/inventory" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Inventory</Link>
          <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
          <span className="text-slate-900 dark:text-slate-100">{isEdit ? 'Edit' : 'Tambah'}</span>
        </nav>
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
          {isEdit ? 'Edit Item Inventory' : 'Tambah Item Inventory'}
        </h1>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
          <div className="p-6 space-y-6">
            {/* Informasi Dasar */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Informasi Dasar</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Kode <span className="text-red-500 ml-1">*</span>
                  </label>
                  <Input {...register('code', { required: 'Kode wajib diisi' })} placeholder="INV-001" />
                  {errors.code && <p className="text-xs text-red-600 dark:text-red-400">{errors.code.message}</p>}
                </div>
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Nama <span className="text-red-500 ml-1">*</span>
                  </label>
                  <Input {...register('name', { required: 'Nama wajib diisi' })} placeholder="Nama item" />
                  {errors.name && <p className="text-xs text-red-600 dark:text-red-400">{errors.name.message}</p>}
                </div>
              </div>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</label>
                <textarea
                  {...register('description')}
                  placeholder="Deskripsi item (opsional)"
                  rows={3}
                  className="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                />
              </div>
            </div>

            {/* Kategori & Stok */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Kategori & Stok</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Kategori <span className="text-red-500 ml-1">*</span>
                  </label>
                  <select
                    {...register('category', { required: 'Kategori wajib dipilih' })}
                    className="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                  >
                    <option value="bahan_baku">Bahan Baku</option>
                    <option value="komponen">Komponen</option>
                    <option value="alat_jadi">Alat Jadi</option>
                  </select>
                  {errors.category && <p className="text-xs text-red-600 dark:text-red-400">{errors.category.message}</p>}
                </div>
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Satuan <span className="text-red-500 ml-1">*</span>
                  </label>
                  <Input {...register('unit', { required: 'Satuan wajib diisi' })} placeholder="pcs, kg, meter" />
                  {errors.unit && <p className="text-xs text-red-600 dark:text-red-400">{errors.unit.message}</p>}
                </div>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Stok Awal <span className="text-red-500 ml-1">*</span>
                  </label>
                  <Input type="number" {...register('quantity', { required: 'Stok wajib diisi', min: { value: 0, message: 'Minimal 0' } })} />
                  {errors.quantity && <p className="text-xs text-red-600 dark:text-red-400">{errors.quantity.message}</p>}
                </div>
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Batas Minimum <span className="text-red-500 ml-1">*</span>
                  </label>
                  <Input type="number" {...register('reorder_level', { required: 'Batas minimum wajib diisi', min: { value: 0, message: 'Minimal 0' } })} />
                  {errors.reorder_level && <p className="text-xs text-red-600 dark:text-red-400">{errors.reorder_level.message}</p>}
                </div>
                <div className="space-y-1.5">
                  <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    Harga (Rp) <span className="text-red-500 ml-1">*</span>
                  </label>
                  <Input type="number" {...register('price', { required: 'Harga wajib diisi', min: { value: 0, message: 'Minimal 0' } })} />
                  {errors.price && <p className="text-xs text-red-600 dark:text-red-400">{errors.price.message}</p>}
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
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row justify-end gap-2 mt-4">
          <Button type="button" variant="subtle" onClick={() => navigate('/inventory')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Batal
          </Button>
          <Button type="submit" loading={loading} className="w-full sm:w-auto">
            <Save className="h-4 w-4 mr-1.5" /> {isEdit ? 'Simpan Perubahan' : 'Tambah Item'}
          </Button>
        </div>
      </form>
    </div>
  )
}
