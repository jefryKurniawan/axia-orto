import { useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { useTheme } from '../hooks/useTheme'
import { Eye, EyeOff, Bone, ArrowLeft, Sun, Moon } from 'lucide-react'

export default function Login() {
  const { login } = useAuth()
  const { theme, toggleTheme } = useTheme()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      await login(email, password)
      window.location.href = '/app/dashboard'
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Login gagal')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex bg-white dark:bg-slate-950">
      {/* ── Left: Brand (hidden on mobile) ──────────────── */}
      <div className="hidden lg:flex lg:w-5/12 xl:w-[45%] bg-slate-50 dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 flex-col justify-between p-10">
        <div>
          <Link to="/" className="flex items-center gap-2 text-slate-500 dark:text-slate-300 hover:text-slate-700 dark:hover:text-white transition-colors text-[0.8rem] mb-16">
            <ArrowLeft className="w-3.5 h-3.5" />
            Kembali ke beranda
          </Link>
          <div className="flex items-center gap-2.5 mb-6">
            <Bone className="w-7 h-7 text-blue-600" />
            <span className="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Axia Orto</span>
          </div>
          <h1 className="text-[1.6rem] font-bold leading-tight tracking-tight mb-3 text-slate-900 dark:text-white">
            Sistem Informasi<br />Klinik Ortotik-Prostetik
          </h1>
          <p className="text-[0.9rem] text-slate-600 dark:text-slate-300 leading-relaxed max-w-sm">
            Kelola pasien, konsultasi, order, produksi, dan inventori dalam satu platform terpadu.
          </p>
        </div>
        <p className="text-[0.75rem] text-slate-500 dark:text-slate-400">
          Magetan, Jawa Timur
        </p>
      </div>

      {/* ── Right: Form ─────────────────────────────────── */}
      <div className="flex-1 flex flex-col">
        {/* Top bar */}
        <div className="flex items-center justify-between px-5 py-4">
          <Link to="/" className="lg:hidden flex items-center gap-1.5 text-[0.8rem] text-slate-500 dark:text-slate-300 hover:text-slate-700 dark:hover:text-white transition-colors">
            <ArrowLeft className="w-3.5 h-3.5" />
            Beranda
          </Link>
          <div className="lg:hidden flex items-center gap-2">
            <Bone className="w-5 h-5 text-blue-600" />
            <span className="text-[0.9rem] font-bold tracking-tight text-slate-900 dark:text-white">Axia Orto</span>
          </div>
          <button
            onClick={toggleTheme}
            className="p-2 rounded-md text-slate-400 hover:text-slate-600 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors ml-auto"
            aria-label="Toggle theme"
          >
            {theme === 'dark' ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
          </button>
        </div>

        {/* Form center */}
        <div className="flex-1 flex items-center justify-center px-5 pb-12">
          <div className="w-full max-w-sm">
            <div className="mb-8">
              <h2 className="text-xl font-bold tracking-tight mb-1 text-slate-900 dark:text-white">Masuk</h2>
              <p className="text-[0.85rem] text-slate-600 dark:text-slate-300">
                Masukkan akun untuk mengakses dashboard
              </p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4">
              {error && (
                <div className="p-3 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 text-[0.82rem] text-red-700 dark:text-red-400">
                  {error}
                </div>
              )}

              <div>
                <label className="block text-[0.8rem] font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  className="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-md text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-colors"
                  placeholder="nama@email.com"
                />
              </div>

              <div>
                <label className="block text-[0.8rem] font-medium text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                <div className="relative">
                  <input
                    type={showPassword ? 'text' : 'password'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                    className="w-full px-3 py-2 pr-10 border border-slate-200 dark:border-slate-700 rounded-md text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan password"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-slate-300 dark:hover:text-white transition-colors"
                    tabIndex={-1}
                  >
                    {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                  </button>
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full py-2.5 px-4 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:ring-offset-2 dark:focus:ring-offset-slate-950 disabled:opacity-50 disabled:cursor-not-allowed transition-colors active:scale-[0.98]"
              >
                {loading ? 'Masuk...' : 'Masuk'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}
