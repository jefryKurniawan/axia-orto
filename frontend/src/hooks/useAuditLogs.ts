import { useQuery } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { AuditLog } from '../types'

export function useAuditLogs(page: number, filters: { auditable_type?: string; user_id?: string; date_from?: string; date_to?: string }) {
  const params = new URLSearchParams({ page: String(page) })
  if (filters.auditable_type) params.set('auditable_type', filters.auditable_type)
  if (filters.user_id) params.set('user_id', filters.user_id)
  if (filters.date_from) params.set('date_from', filters.date_from)
  if (filters.date_to) params.set('date_to', filters.date_to)

  return useQuery({
    queryKey: ['audit-logs', page, filters],
    queryFn: () => api.getPaginated<AuditLog>(`/audit-logs?${params.toString()}`),
  })
}
