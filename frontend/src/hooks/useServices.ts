import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { Service } from '../types'

export function useServices(page: number, search: string) {
  return useQuery({
    queryKey: ['services', page, search],
    queryFn: () => api.getPaginated<Service>(`/services?page=${page}&search=${encodeURIComponent(search)}`),
  })
}

export function useService(uuid: string) {
  return useQuery({
    queryKey: ['services', uuid],
    queryFn: async () => (await api.get<Service>(`/services/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function useActiveServices() {
  return useQuery({
    queryKey: ['services', 'active'],
    queryFn: async () => (await api.get<Service[]>('/services/active')).data,
  })
}

export function useCreateService() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: Partial<Service>) => api.post<Service>('/services', data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['services'] }),
  })
}

export function useUpdateService() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, data }: { uuid: string; data: Partial<Service> }) =>
      api.put<Service>(`/services/${uuid}`, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['services'] }),
  })
}

export function useDeleteService() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (uuid: string) => api.delete(`/services/${uuid}`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['services'] }),
  })
}
