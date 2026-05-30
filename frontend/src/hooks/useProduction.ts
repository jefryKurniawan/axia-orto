import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { ProductionTracking } from '../types'

export function useProductionList(page: number, orderUuid: string, status: string) {
  return useQuery({
    queryKey: ['production', page, orderUuid, status],
    queryFn: () => api.getPaginated<ProductionTracking>(`/production?page=${page}&order_uuid=${encodeURIComponent(orderUuid)}&status=${status}`),
  })
}

export function useProductionTracking(uuid: string) {
  return useQuery({
    queryKey: ['production', uuid],
    queryFn: async () => (await api.get<ProductionTracking>(`/production/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function useProductionByOrder(orderUuid: string) {
  return useQuery({
    queryKey: ['production', 'order', orderUuid],
    queryFn: async () => (await api.get<ProductionTracking[]>(`/production/order/${orderUuid}`)).data,
    enabled: !!orderUuid,
  })
}

export function useCreateProductionTracking() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: {
      treatment_order_id: number
      step: string
      assigned_to: number
      notes?: string
    }) => api.post<ProductionTracking>('/production', data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['production'] }),
  })
}

export function useUpdateProductionTracking() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, data }: { uuid: string; data: Partial<ProductionTracking> }) =>
      api.put<ProductionTracking>(`/production/${uuid}`, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['production'] }),
  })
}

export function useDeleteProductionTracking() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (uuid: string) => api.delete(`/production/${uuid}`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['production'] }),
  })
}
