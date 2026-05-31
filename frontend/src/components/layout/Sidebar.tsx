import { useEffect } from 'react'
import { NavLink } from 'react-router-dom'
import { useAppStore } from '../../stores/appStore'
import { LayoutDashboard, Users, Stethoscope, Building2, ClipboardList, CreditCard, Factory, FileBarChart, Package, ShieldCheck, X, Bone } from 'lucide-react'
import { useAuth } from '../../contexts/AuthContext'

const navItems = [
  { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { to: '/patients', label: 'Pasien', icon: Users },
  { to: '/consultations', label: 'Konsultasi', icon: Stethoscope },
  { to: '/services', label: 'Layanan', icon: Building2 },
  { to: '/orders', label: 'Order', icon: ClipboardList },
  { to: '/payments', label: 'Pembayaran', icon: CreditCard },
  { to: '/production', label: 'Produksi', icon: Factory },
  { to: '/inventory', label: 'Inventaris', icon: Package },
  { to: '/reports', label: 'Laporan', icon: FileBarChart },
]

export function Sidebar() {
  const { user } = useAuth()
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

      {/* Sidebar */}
      <aside
        className={`
          fixed top-0 left-0 z-40 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700
          transition-all duration-200 ease-in-out flex flex-col
          ${isMobile
            ? `w-64 ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}`
            : `${sidebarOpen ? 'w-56' : 'w-16'}`
          }
        `}
      >
        {/* Logo */}
        <div className={`flex items-center h-16 px-4 border-b border-slate-200 dark:border-slate-700 ${sidebarOpen ? 'gap-3' : 'justify-center'}`}>
          <Bone className="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0" />
          {sidebarOpen && (
            <span className="text-sm font-bold tracking-tight text-slate-900 dark:text-white whitespace-nowrap">
              Axia Orto
            </span>
          )}
          {isMobile && sidebarOpen && (
            <button
              onClick={() => setSidebarOpen(false)}
              className="ml-auto p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
            >
              <X className="w-4 h-4" />
            </button>
          )}
        </div>

        {/* Navigation */}
        <nav className="flex-1 py-3 px-2 space-y-0.5 overflow-y-auto">
          {/* Core */}
          {navItems.slice(0, 4).map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              onClick={handleNavClick}
              className={({ isActive }) =>
                `group flex items-center gap-3 rounded-lg transition-all duration-150 relative ${
                  sidebarOpen ? 'px-3 py-2' : 'justify-center py-2.5'
                } ${
                  isActive
                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100'
                }`
              }
            >
              {({ isActive }) => (
                <>
                  {isActive && (
                    <div className="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-blue-600 dark:bg-blue-400" />
                  )}
                  <item.icon className={`w-[18px] h-[18px] flex-shrink-0 ${isActive ? 'text-blue-600 dark:text-blue-400' : ''}`} />
                  {sidebarOpen && (
                    <span className={`text-sm whitespace-nowrap ${isActive ? 'font-semibold' : 'font-medium'}`}>
                      {item.label}
                    </span>
                  )}
                </>
              )}
            </NavLink>
          ))}

          {/* Divider */}
          <div className="my-2 mx-3 border-t border-slate-100 dark:border-slate-800" />

          {/* Operations */}
          {navItems.slice(4).map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              onClick={handleNavClick}
              className={({ isActive }) =>
                `group flex items-center gap-3 rounded-lg transition-all duration-150 relative ${
                  sidebarOpen ? 'px-3 py-2' : 'justify-center py-2.5'
                } ${
                  isActive
                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100'
                }`
              }
            >
              {({ isActive }) => (
                <>
                  {isActive && (
                    <div className="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-blue-600 dark:bg-blue-400" />
                  )}
                  <item.icon className={`w-[18px] h-[18px] flex-shrink-0 ${isActive ? 'text-blue-600 dark:text-blue-400' : ''}`} />
                  {sidebarOpen && (
                    <span className={`text-sm whitespace-nowrap ${isActive ? 'font-semibold' : 'font-medium'}`}>
                      {item.label}
                    </span>
                  )}
                </>
              )}
            </NavLink>
          ))}

          {/* Admin divider */}
          {user?.role === 'admin' && (
            <div className="my-2 mx-3 border-t border-slate-100 dark:border-slate-800" />
          )}

          {/* Audit Log — admin only */}
          {user?.role === 'admin' && (
            <NavLink
              to="/audit-logs"
              onClick={handleNavClick}
              className={({ isActive }) =>
                `group flex items-center gap-3 rounded-lg transition-all duration-150 relative ${
                  sidebarOpen ? 'px-3 py-2' : 'justify-center py-2.5'
                } ${
                  isActive
                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-100'
                }`
              }
            >
              {({ isActive }) => (
                <>
                  {isActive && (
                    <div className="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full bg-blue-600 dark:bg-blue-400" />
                  )}
                  <ShieldCheck className={`w-[18px] h-[18px] flex-shrink-0 ${isActive ? 'text-blue-600 dark:text-blue-400' : ''}`} />
                  {sidebarOpen && (
                    <span className={`text-sm whitespace-nowrap ${isActive ? 'font-semibold' : 'font-medium'}`}>
                      Audit Log
                    </span>
                  )}
                </>
              )}
            </NavLink>
          )}
        </nav>

        {/* User info at bottom */}
        {sidebarOpen && user && (
          <div className="p-3 border-t border-slate-200 dark:border-slate-700">
            <div className="flex items-center gap-3 px-2 py-1.5">
              <div className="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                <span className="text-xs font-bold text-blue-700 dark:text-blue-300">
                  {user.name?.charAt(0)?.toUpperCase()}
                </span>
              </div>
              <div className="min-w-0">
                <p className="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{user.name}</p>
                <p className="text-xs text-slate-400 dark:text-slate-500 capitalize">{user.role?.replace('_', ' ')}</p>
              </div>
            </div>
          </div>
        )}
      </aside>
    </>
  )
}
