import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { Payment } from '../types'

export function usePayments(page: number, search: string, status: string) {
  return useQuery({
    queryKey: ['payments', page, search, status],
    queryFn: () => api.getPaginated<Payment>(`/payments?page=${page}&search=${encodeURIComponent(search)}&status=${status}`),
  })
}

export function usePayment(uuid: string) {
  return useQuery({
    queryKey: ['payments', uuid],
    queryFn: async () => (await api.get<Payment>(`/payments/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function usePaymentStats() {
  return useQuery({
    queryKey: ['payments', 'stats'],
    queryFn: async () => (await api.get<Record<string, unknown>>(`/payments/stats`)).data,
  })
}

export function usePaymentsByOrder(orderUuid: string) {
  return useQuery({
    queryKey: ['payments', 'order', orderUuid],
    queryFn: async () => (await api.get<Payment[]>(`/payments/order/${orderUuid}`)).data,
    enabled: !!orderUuid,
  })
}

export function useCreatePayment() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: {
      treatment_order_id: number
      payment_date: string
      payment_method: string
      amount: number
      notes?: string
    }) => api.post<Payment>('/payments', data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export function useUpdatePayment() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, data }: { uuid: string; data: Partial<Payment> }) =>
      api.put<Payment>(`/payments/${uuid}`, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export function useUpdatePaymentStatus() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, status }: { uuid: string; status: string }) =>
      api.patch<Payment>(`/payments/${uuid}/status`, { status }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export function useDeletePayment() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (uuid: string) => api.delete(`/payments/${uuid}`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}
