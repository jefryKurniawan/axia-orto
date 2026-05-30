import { useState, type FormEvent } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { Eye, EyeOff, Save, ArrowLeft } from 'lucide-react'
import { api } from '../lib/api'
import { useToastStore } from '../stores/toastStore'
import { Button } from '../components/ui/Button'
import { Input } from '../components/ui/Input'

const roles = [
  { value: 'dokter', label: 'Dokter' },
  { value: 'staf_klinik', label: 'Staf Klinik' },
  { value: 'teknisi', label: 'Teknisi' },
]

export default function Register() {
  const navigate = useNavigate()
  const addToast = useToastStore((s) => s.addToast)

  const [form, setForm] = useState({
    name: '',
    email: '',
    password: '',
    role: 'dokter',
    specialization: '',
    phone: '',
  })
  const [showPassword, setShowPassword] = useState(false)
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [loading, setLoading] = useState(false)

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setForm((prev) => ({ ...prev, [name]: value }))
    if (errors[name]) setErrors((prev) => ({ ...prev, [name]: '' }))
  }

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setErrors({})
    setLoading(true)

    try {
      await api.post('/register', {
        ...form,
        specialization: form.specialization || undefined,
        phone: form.phone || undefined,
      })
      addToast('success', 'User berhasil dibuat.')
      navigate('/dashboard')
    } catch (err: unknown) {
      if (err && typeof err === 'object' && 'errors' in err) {
        const fieldErrors: Record<string, string> = {}
        const apiErrors = (err as { errors: Record<string, string[]> }).errors
        for (const [key, msgs] of Object.entries(apiErrors)) {
          fieldErrors[key] = msgs[0]
        }
        setErrors(fieldErrors)
      } else {
        const msg = err instanceof Error ? err.message : 'Gagal membuat user.'
        setErrors({ general: msg })
        addToast('error', msg)
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="max-w-lg mx-auto">
      <div className="mb-6">
        <nav className="text-xs text-slate-400 dark:text-slate-500 mb-1">
          <Link to="/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300 transition-colors">Dashboard</Link>
          <span className="text-slate-300 dark:text-slate-600 mx-1">/</span>
          <span className="text-slate-900 dark:text-slate-100">Buat User</span>
        </nav>
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Buat User Baru</h1>
        <p className="text-sm text-slate-500 dark:text-slate-400 mt-1">Tambahkan akun untuk dokter, staf, atau teknisi</p>
      </div>

      {errors.general && (
        <div className="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-400">
          {errors.general}
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <div className="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
          <div className="p-6 space-y-6">
            {/* Informasi Akun */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Informasi Akun</h3>
              <Input
                label="Nama Lengkap"
                name="name"
                required
                value={form.name}
                onChange={handleChange}
                error={errors.name}
                placeholder="Dr. Budi Santoso"
              />
              <Input
                label="Email"
                name="email"
                type="email"
                required
                value={form.email}
                onChange={handleChange}
                error={errors.email}
                placeholder="budi@axia.id"
              />
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Password <span className="text-red-500 ml-1">*</span>
                </label>
                <div className="relative">
                  <input
                    type={showPassword ? 'text' : 'password'}
                    name="password"
                    value={form.password}
                    onChange={handleChange}
                    required
                    minLength={8}
                    className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                    placeholder="Minimal 8 karakter"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                    tabIndex={-1}
                  >
                    {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                  </button>
                </div>
                {errors.password && <p className="text-xs text-red-600 dark:text-red-400">{errors.password}</p>}
              </div>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                  Role <span className="text-red-500 ml-1">*</span>
                </label>
                <select
                  name="role"
                  value={form.role}
                  onChange={handleChange}
                  className="block w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                >
                  {roles.map((r) => (
                    <option key={r.value} value={r.value}>{r.label}</option>
                  ))}
                </select>
                {errors.role && <p className="text-xs text-red-600 dark:text-red-400">{errors.role}</p>}
              </div>
            </div>

            {/* Detail Tambahan */}
            <div className="bg-slate-50/50 dark:bg-slate-800/30 rounded-lg p-4 space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Detail Tambahan</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input
                  label="Spesialisasi"
                  name="specialization"
                  value={form.specialization}
                  onChange={handleChange}
                  placeholder="Ortotik-Prostetik"
                />
                <Input
                  label="No. HP"
                  name="phone"
                  value={form.phone}
                  onChange={handleChange}
                  placeholder="08123456789"
                />
              </div>
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row justify-end gap-2 mt-4">
          <Button type="button" variant="subtle" onClick={() => navigate('/dashboard')} className="w-full sm:w-auto">
            <ArrowLeft className="h-4 w-4 mr-1.5" /> Batal
          </Button>
          <Button type="submit" loading={loading} className="w-full sm:w-auto">
            <Save className="h-4 w-4 mr-1.5" /> {loading ? 'Menyimpan...' : 'Buat User'}
          </Button>
        </div>
      </form>
    </div>
  )
}
