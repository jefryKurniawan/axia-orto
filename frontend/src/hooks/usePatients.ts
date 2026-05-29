import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { api } from '../lib/api'
import { addToQueue, getPendingCount } from '../lib/offlineQueue'
import { useAppStore } from '../stores/appStore'
import type { Patient } from '../types'

export function usePatients(page: number, search: string) {
  return useQuery({
    queryKey: ['patients', page, search],
    queryFn: () => api.getPaginated<Patient>(`/patients?page=${page}&search=${encodeURIComponent(search)}`),
  })
}

export function usePatient(uuid: string) {
  return useQuery({
    queryKey: ['patients', uuid],
    queryFn: async () => (await api.get<Patient>(`/patients/${uuid}`)).data,
    enabled: !!uuid,
  })
}

export function useCreatePatient() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (data: Partial<Patient>) => {
      if (!navigator.onLine) {
        await addToQueue('patients', 'create', data as Record<string, unknown>)
        useAppStore.getState().setPendingSyncCount(await getPendingCount())
        return { success: true, data, message: 'Disimpan offline' } as unknown as Patient
      }
      return api.post<Patient>('/patients', data)
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['patients'] }),
  })
}

export function useUpdatePatient() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async ({ uuid, data }: { uuid: string; data: Partial<Patient> }) => {
      if (!navigator.onLine) {
        await addToQueue('patients', 'update', { uuid, ...data } as Record<string, unknown>)
        useAppStore.getState().setPendingSyncCount(await getPendingCount())
        return { success: true, data, message: 'Disimpan offline' } as unknown as Patient
      }
      return api.put<Patient>(`/patients/${uuid}`, data)
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['patients'] }),
  })
}

export function useDeletePatient() {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (uuid: string) => {
      if (!navigator.onLine) {
        await addToQueue('patients', 'delete', { uuid })
        useAppStore.getState().setPendingSyncCount(await getPendingCount())
        return { success: true, message: 'Dihapus offline' }
      }
      return api.delete(`/patients/${uuid}`)
    },
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['patients'] }),
  })
}
