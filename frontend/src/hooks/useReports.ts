import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { ExportJob } from '../types'

export function useExportJobs(page = 1) {
  return useQuery({
    queryKey: ['exports', page],
    queryFn: () => api.getPaginated<ExportJob>(`/exports?page=${page}`),
  })
}

export function useExportJob(uuid: string) {
  return useQuery({
    queryKey: ['exports', uuid],
    queryFn: async () => (await api.get<ExportJob>(`/exports/${uuid}`)).data,
    enabled: !!uuid,
    refetchInterval: (query) => {
      const status = query.state.data?.status
      if (status === 'pending' || status === 'processing') return 2000
      return false
    },
  })
}

export function useCreateExport() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: { report_type: string; date_from?: string; date_to?: string }) =>
      api.post<ExportJob>('/exports', data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['exports'] }),
  })
}
