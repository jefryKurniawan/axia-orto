import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import { addToQueue, getPendingCount } from '../lib/offlineQueue'
import { useAppStore } from '../stores/appStore'
import type { Consultation } from '../types'

export function useConsultations(page: number, search: string, status: string) {
  return useQuery({
    queryKey: ['consultations', page, search, status],
    queryFn: () => api.getPaginated<Consultation>(
      `/consultations?page=${page}&search=${encodeURIComponent(search)}&status=${status}`
    ),
  })
}

export function useConsultation(uuid: string) {
  return useQuery({
    queryKey: ['consultations', uuid],
    queryFn: async () => (await api.get<Consultation>(`/consultations/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function useTodayConsultations() {
  return useQuery({
    queryKey: ['consultations', 'today'],
    queryFn: async () => (await api.get<Consultation[]>('/consultations/today')).data,
  })
}

export function useCreateConsultation() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (data: Partial<Consultation>) => {
      if (!navigator.onLine) {
        await addToQueue('consultations', 'create', data as Record<string, unknown>)
        useAppStore.getState().setPendingSyncCount(await getPendingCount())
        return { success: true, data, message: 'Disimpan offline' } as unknown as Consultation
      }
      return api.post<Consultation>('/consultations', data)
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['consultations'] }),
  })
}

export function useUpdateConsultation() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async ({ uuid, data }: { uuid: string; data: Partial<Consultation> }) => {
      if (!navigator.onLine) {
        await addToQueue('consultations', 'update', { uuid, ...data } as Record<string, unknown>)
        useAppStore.getState().setPendingSyncCount(await getPendingCount())
        return { success: true, data, message: 'Disimpan offline' } as unknown as Consultation
      }
      return api.put<Consultation>(`/consultations/${uuid}`, data)
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['consultations'] }),
  })
}

export function useDeleteConsultation() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (uuid: string) => {
      if (!navigator.onLine) {
        await addToQueue('consultations', 'delete', { uuid })
        useAppStore.getState().setPendingSyncCount(await getPendingCount())
        return { success: true, message: 'Dihapus offline' }
      }
      return api.delete(`/consultations/${uuid}`)
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['consultations'] }),
  })
}
