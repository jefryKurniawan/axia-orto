import { create } from 'zustand'

interface AppState {
  sidebarOpen: boolean
  isMobile: boolean
  theme: 'light' | 'dark'
  isOnline: boolean
  pendingSyncCount: number
  toggleSidebar: () => void
  setSidebarOpen: (open: boolean) => void
  setIsMobile: (mobile: boolean) => void
  setTheme: (theme: 'light' | 'dark') => void
  setIsOnline: (online: boolean) => void
  setPendingSyncCount: (count: number) => void
}

const getIsMobile = () => window.innerWidth < 768

// Apply theme immediately on module load (prevents flash)
const savedTheme = (localStorage.getItem('theme') as 'light' | 'dark') || 'light'
if (savedTheme === 'dark') {
  document.documentElement.classList.add('dark')
  document.documentElement.setAttribute('data-theme', 'dark')
}

export const useAppStore = create<AppState>((set) => ({
  sidebarOpen: !getIsMobile(),
  isMobile: getIsMobile(),
  theme: savedTheme,
  isOnline: typeof navigator !== 'undefined' ? navigator.onLine : true,
  pendingSyncCount: 0,
  toggleSidebar: () => set((s) => ({ sidebarOpen: !s.sidebarOpen })),
  setSidebarOpen: (open) => set({ sidebarOpen: open }),
  setIsMobile: (mobile) => set({ isMobile: mobile }),
  setTheme: (theme) => {
    localStorage.setItem('theme', theme)
    const html = document.documentElement
    if (theme === 'dark') {
      html.classList.add('dark')
    } else {
      html.classList.remove('dark')
    }
    set({ theme })
  },
  setIsOnline: (online) => set({ isOnline: online }),
  setPendingSyncCount: (count) => set({ pendingSyncCount: count }),
}))
