import Dexie, { type Table } from 'dexie'

export interface OfflineQueueItem {
  id?: number
  entity: 'patients' | 'consultations'
  action: 'create' | 'update' | 'delete'
  data: Record<string, unknown>
  timestamp: number
}

class AxiaOfflineDB extends Dexie {
  offlineQueue!: Table<OfflineQueueItem, number>

  constructor() {
    super('axia-offline')
    this.version(1).stores({
      offlineQueue: '++id, entity, action, timestamp',
    })
  }
}

export const db = new AxiaOfflineDB()
