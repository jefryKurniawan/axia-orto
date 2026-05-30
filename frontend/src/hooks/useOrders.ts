import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { TreatmentOrder } from '../types'

export function useOrders(page: number, search: string, status: string) {
  return useQuery({
    queryKey: ['orders', page, search, status],
    queryFn: () => api.getPaginated<TreatmentOrder>(`/orders?page=${page}&search=${encodeURIComponent(search)}&status=${status}`),
  })
}

export function useOrder(uuid: string) {
  return useQuery({
    queryKey: ['orders', uuid],
    queryFn: async () => (await api.get<TreatmentOrder>(`/orders/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function useOrderStats() {
  return useQuery({
    queryKey: ['orders', 'stats'],
    queryFn: async () => (await api.get<Record<string, number>>(`/orders/stats`)).data,
  })
}

export function useCreateOrder() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: {
      patient_id: number
      consultation_id?: number
      order_date: string
      delivery_date?: string
      notes?: string
      services: { service_id: number; quantity: number; specifications?: Record<string, unknown> }[]
    }) => api.post<TreatmentOrder>('/orders', data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['orders'] }),
  })
}

export function useUpdateOrder() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, data }: { uuid: string; data: Partial<TreatmentOrder> & { services?: { service_id: number; quantity: number; specifications?: Record<string, unknown>[] } } }) =>
      api.put<TreatmentOrder>(`/orders/${uuid}`, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['orders'] }),
  })
}

export function useUpdateOrderStatus() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, status }: { uuid: string; status: string }) =>
      api.patch<TreatmentOrder>(`/orders/${uuid}/status`, { status }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['orders'] }),
  })
}

export function useDeleteOrder() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (uuid: string) => api.delete(`/orders/${uuid}`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['orders'] }),
  })
}
