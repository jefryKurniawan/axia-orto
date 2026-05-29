import { useEffect } from 'react'
import { NavLink } from 'react-router-dom'
import { useAppStore } from '../../stores/appStore'
import { LayoutDashboard, Users, Stethoscope, Building2, Bone, X } from 'lucide-react'

const navItems = [
  { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/patients', label: 'Pasien', icon: Users },
  { to: '/consultations', label: 'Konsultasi', icon: Stethoscope },
  { to: '/services', label: 'Layanan', icon: Building2 },
]

export function Sidebar() {
  const sidebarOpen = useAppStore((s) => s.sidebarOpen)
  const isMobile = useAppStore((s) => s.isMobile)
  const setSidebarOpen = useAppStore((s) => s.setSidebarOpen)
  const setIsMobile = useAppStore((s) => s.setIsMobile)

  useEffect(() => {
    const handleResize = () => {
      const mobile = window.innerWidth < 768
      setIsMobile(mobile)
      if (mobile) setSidebarOpen(false)
    }
    window.addEventListener('resize', handleResize)
    handleResize()
    return () => window.removeEventListener('resize', handleResize)
  }, [setIsMobile, setSidebarOpen])

  const handleNavClick = () => {
    if (isMobile) setSidebarOpen(false)
  }

  return (
    <>
      {/* Backdrop overlay on mobile */}
      {isMobile && sidebarOpen && (
        <div
          className="fixed inset-0 z-30 bg-black/40 transition-opacity"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside
        className={`fixed left-0 top-0 z-40 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 transition-all duration-300 ${
          isMobile
            ? sidebarOpen ? 'w-64' : '-translate-x-full w-64'
            : sidebarOpen ? 'w-64' : 'w-16'
        }`}
      >
        <div className="flex items-center justify-between h-16 px-4 border-b border-slate-200 dark:border-slate-700">
          {sidebarOpen ? (
            <>
              <h1 className="text-lg font-bold text-slate-900 dark:text-slate-100">AxiaOrto</h1>
              {isMobile && (
                <button
                  onClick={() => setSidebarOpen(false)}
                  className="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                  <X className="w-5 h-5" />
                </button>
              )}
            </>
          ) : (
            !isMobile && <Bone className="w-6 h-6 text-blue-600 mx-auto" />
          )}
        </div>

        <nav className="mt-4 space-y-1 px-2">
          {navItems.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              onClick={handleNavClick}
              className={({ isActive }) =>
                `flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors ${
                  isActive
                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`
              }
            >
              <item.icon className="w-5 h-5 flex-shrink-0" />
              {sidebarOpen && <span>{item.label}</span>}
            </NavLink>
          ))}
        </nav>
      </aside>
    </>
  )
}
