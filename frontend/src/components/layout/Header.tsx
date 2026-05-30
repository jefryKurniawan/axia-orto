import { useState } from 'react'
import { useAuth } from '../../contexts/AuthContext'
import { useAppStore } from '../../stores/appStore'
import { useSyncStatus } from '../../hooks/useSyncStatus'
import { useTheme } from '../../hooks/useTheme'
import { Modal } from '../ui/Modal'
import { Button } from '../ui/Button'
import { PanelLeftClose, PanelLeft, Sun, Moon, LogOut, Cloud, CloudOff, RefreshCw } from 'lucide-react'

export function Header() {
  const { logout } = useAuth()
  const { sidebarOpen, toggleSidebar } = useAppStore()
  const { theme, toggleTheme } = useTheme()
  const { isOnline, pendingSyncCount, syncNow } = useSyncStatus()
  const [showLogout, setShowLogout] = useState(false)
  const [loggingOut, setLoggingOut] = useState(false)
  const [syncing, setSyncing] = useState(false)

  const handleThemeToggle = (e: React.MouseEvent<HTMLButtonElement>) => {
    const btn = e.currentTarget
    const ripple = btn.querySelector('.theme-ripple') as HTMLElement
    if (ripple) {
      ripple.style.animation = 'none'
      ripple.offsetHeight // force reflow
      ripple.style.animation = 'theme-ripple-expand 1.5s cubic-bezier(0.22, 0.61, 0.36, 1) both'
    }
    toggleTheme()
  }

  const handleLogout = async () => {
    setLoggingOut(true)
    await logout()
  }

  const handleSync = async () => {
    setSyncing(true)
    try {
      await syncNow()
    } finally {
      setSyncing(false)
    }
  }

  return (
    <>
      <header className="sticky top-0 z-30 h-14 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 lg:px-6">
        <div className="flex items-center gap-2">
          <button
            onClick={toggleSidebar}
            className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-105 active:scale-95 transition-all duration-150"
            aria-label="Toggle sidebar"
          >
            {sidebarOpen ? <PanelLeftClose className="w-[18px] h-[18px]" /> : <PanelLeft className="w-[18px] h-[18px]" />}
          </button>

          {/* Sync status */}
          <div className="flex items-center gap-1.5 ml-2">
            {isOnline ? (
              <Cloud className="w-3.5 h-3.5 text-emerald-500" />
            ) : (
              <CloudOff className="w-3.5 h-3.5 text-red-500" />
            )}
            {pendingSyncCount > 0 && (
              <button
                onClick={handleSync}
                disabled={syncing}
                className="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors"
              >
                <RefreshCw className={`w-3 h-3 ${syncing ? 'animate-spin' : ''}`} />
                <span className="hidden sm:inline">{pendingSyncCount} pending</span>
              </button>
            )}
          </div>
        </div>

        <div className="flex items-center gap-1">
          {/* Theme toggle */}
          <button
            onClick={handleThemeToggle}
            className="relative p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-105 active:scale-95 transition-all duration-150 overflow-hidden"
            aria-label="Toggle theme"
          >
            <span className="theme-ripple absolute inset-0 rounded-lg pointer-events-none" />
            {theme === 'light' ? <Moon className="w-[18px] h-[18px]" /> : <Sun className="w-[18px] h-[18px]" />}
          </button>

          {/* Logout */}
          <button
            onClick={() => setShowLogout(true)}
            className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:scale-105 active:scale-95 transition-all duration-150"
            aria-label="Logout"
          >
            <LogOut className="w-[18px] h-[18px]" />
          </button>
        </div>
      </header>

      {/* Logout confirmation */}
      <Modal isOpen={showLogout} onClose={() => setShowLogout(false)} title="Keluar?" size="sm">
        <div className="text-center">
          <p className="text-sm text-slate-600 dark:text-slate-400 mb-6">Yakin ingin keluar dari aplikasi?</p>
          <div className="flex justify-end gap-2">
            <Button variant="secondary" onClick={() => setShowLogout(false)}>Batal</Button>
            <Button variant="danger" loading={loggingOut} onClick={handleLogout}>
              <LogOut className="w-4 h-4 mr-1.5" /> Keluar
            </Button>
          </div>
        </div>
      </Modal>
    </>
  )
}
