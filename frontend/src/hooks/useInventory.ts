import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import type { InventoryItem, InventoryTransaction, InventoryStats } from '../types'

export function useInventoryItems(page: number, search: string, category: string) {
  return useQuery({
    queryKey: ['inventory', page, search, category],
    queryFn: () => api.getPaginated<InventoryItem>(`/inventory?page=${page}&search=${encodeURIComponent(search)}&category=${category}`),
  })
}

export function useInventoryItem(uuid: string) {
  return useQuery({
    queryKey: ['inventory', uuid],
    queryFn: async () => (await api.get<{ item: InventoryItem; recent_transactions: InventoryTransaction[] }>(`/inventory/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function useInventoryStats() {
  return useQuery({
    queryKey: ['inventory', 'stats'],
    queryFn: async () => (await api.get<InventoryStats>('/inventory/stats')).data,
  })
}

export function useLowStockItems() {
  return useQuery({
    queryKey: ['inventory', 'low-stock'],
    queryFn: async () => (await api.get<InventoryItem[]>('/inventory/low-stock')).data,
  })
}

export function useInventoryTransactions(uuid: string, page: number) {
  return useQuery({
    queryKey: ['inventory', uuid, 'transactions', page],
    queryFn: () => api.getPaginated<InventoryTransaction>(`/inventory/${uuid}/transactions?page=${page}`),
    enabled: !!uuid,
  })
}

export function useCreateInventoryItem() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (data: Partial<InventoryItem>) => api.post<InventoryItem>('/inventory', data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['inventory'] }),
  })
}

export function useUpdateInventoryItem() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, data }: { uuid: string; data: Partial<InventoryItem> }) =>
      api.put<InventoryItem>(`/inventory/${uuid}`, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['inventory'] }),
  })
}

export function useDeleteInventoryItem() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: (uuid: string) => api.delete(`/inventory/${uuid}`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['inventory'] }),
  })
}

export function useAdjustStock() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: ({ uuid, data }: { uuid: string; data: { type: string; quantity: number; notes?: string } }) =>
      api.post<InventoryItem>(`/inventory/${uuid}/adjust`, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['inventory'] }),
  })
}
