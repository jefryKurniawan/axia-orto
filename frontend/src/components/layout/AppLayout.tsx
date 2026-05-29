import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'
import { useAppStore } from '../../stores/appStore'
import { Sidebar } from './Sidebar'
import { Header } from './Header'

export function AppLayout() {
  const { user, loading } = useAuth()
  const sidebarOpen = useAppStore((s) => s.sidebarOpen)
  const isMobile = useAppStore((s) => s.isMobile)

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600" />
      </div>
    )
  }

  if (!user) {
    return <Navigate to="/login" replace />
  }

  const marginLeft = isMobile ? 0 : sidebarOpen ? 256 : 64

  return (
    <div className="min-h-screen bg-slate-50 dark:bg-slate-950">
      <Sidebar />
      <div className="transition-all duration-300" style={{ marginLeft }}>
        <Header />
        <main className="p-4 lg:p-6">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
