import { useQuery } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { DashboardStats } from '../types'

export function useDashboard() {
  return useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const res = await api.get<DashboardStats>('/dashboard')
      return res.data
    },
  })
}
