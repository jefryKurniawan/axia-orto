import { useState } from 'react'
import { useAuth } from '../../contexts/AuthContext'
import { useAppStore } from '../../stores/appStore'
import { useSyncStatus } from '../../hooks/useSyncStatus'
import { useTheme } from '../../hooks/useTheme'
import { Modal } from '../ui/Modal'
import { Button } from '../ui/Button'
import { PanelLeftClose, PanelLeft, Sun, Moon, LogOut, Cloud, CloudOff, RefreshCw } from 'lucide-react'

export function Header() {
  const { user, logout } = useAuth()
  const { sidebarOpen, toggleSidebar } = useAppStore()
  const { theme, toggleTheme } = useTheme()
  const { isOnline, pendingSyncCount, syncNow } = useSyncStatus()
  const [showLogout, setShowLogout] = useState(false)
  const [loggingOut, setLoggingOut] = useState(false)
  const [syncing, setSyncing] = useState(false)

  const handleLogout = async () => {
    setLoggingOut(true)
    await logout()
  }

  return (
    <>
      <header className="sticky top-0 z-30 h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 lg:px-6">
        <div className="flex items-center gap-3">
          <button
            onClick={toggleSidebar}
            className="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            aria-label="Toggle sidebar"
          >
            {sidebarOpen ? <PanelLeftClose className="w-5 h-5" /> : <PanelLeft className="w-5 h-5" />}
          </button>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={toggleTheme}
            className="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            aria-label="Toggle theme"
          >
            {theme === 'dark' ? <Sun className="w-5 h-5" /> : <Moon className="w-5 h-5" />}
          </button>

          {/* Sync status badge */}
          <div className="flex items-center gap-1.5">
            {!isOnline ? (
              <span className="flex items-center gap-1 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded-full">
                <CloudOff className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Offline</span>
              </span>
            ) : pendingSyncCount > 0 ? (
              <button
                onClick={async () => {
                  setSyncing(true)
                  await syncNow()
                  setSyncing(false)
                }}
                disabled={syncing}
                className="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded-full hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors"
              >
                <RefreshCw className={`w-3.5 h-3.5 ${syncing ? 'animate-spin' : ''}`} />
                <span className="hidden sm:inline">{pendingSyncCount} pending</span>
              </button>
            ) : (
              <span className="flex items-center gap-1 text-xs text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full">
                <Cloud className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Online</span>
              </span>
            )}
          </div>

          <div className="flex items-center gap-2 pl-3 border-l border-slate-200 dark:border-slate-700">
            <div className="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-sm font-medium text-blue-700 dark:text-blue-400">
              {user?.name?.charAt(0).toUpperCase()}
            </div>
            <div className="hidden sm:block">
              <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{user?.name}</p>
              <p className="text-xs text-slate-500 dark:text-slate-400 capitalize">{user?.role?.replace('_', ' ')}</p>
            </div>
            <button
              onClick={() => setShowLogout(true)}
              className="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
              aria-label="Logout"
            >
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>
      </header>

      <Modal isOpen={showLogout} onClose={() => setShowLogout(false)} title="Konfirmasi Logout" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin keluar dari aplikasi?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setShowLogout(false)}>Batal</Button>
          <Button variant="danger" loading={loggingOut} onClick={handleLogout}>Logout</Button>
        </div>
      </Modal>
    </>
  )
}
