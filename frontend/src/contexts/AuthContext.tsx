import { createContext, useContext, useState, useEffect, useCallback, type ReactNode } from 'react'
import { api } from '../lib/api'
import type { User } from '../types'

interface AuthContextValue {
  user: User | null
  loading: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get<User>('/me')
      .then((res) => setUser(res.data))
      .catch(() => setUser(null))
      .finally(() => setLoading(false))
  }, [])

  const login = useCallback(async (email: string, password: string) => {
    // Sanctum SPA: must fetch CSRF cookie first
    await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' })
    const res = await api.post<{ user: User; token: string }>('/login', { email, password })
    setUser(res.data.user)
  }, [])

  const logout = useCallback(async () => {
    await api.post('/logout')
    setUser(null)
    window.location.href = '/app/login'
  }, [])

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (!context) throw new Error('useAuth must be used within AuthProvider')
  return context
}
