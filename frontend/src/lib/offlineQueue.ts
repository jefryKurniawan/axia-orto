import { db, type OfflineQueueItem } from '../db/database'
import { api } from './api'

export async function addToQueue(
  entity: OfflineQueueItem['entity'],
  action: OfflineQueueItem['action'],
  data: Record<string, unknown>
) {
  await db.offlineQueue.add({
    entity,
    action,
    data,
    timestamp: Date.now(),
  })
}

export async function getPendingCount(): Promise<number> {
  return db.offlineQueue.count()
}

export async function getPendingItems(): Promise<OfflineQueueItem[]> {
  return db.offlineQueue.toArray()
}

export async function clearQueue() {
  await db.offlineQueue.clear()
}

export async function processQueue(): Promise<{ success: number; failed: number }> {
  const items = await db.offlineQueue.orderBy('id').toArray()
  if (items.length === 0) return { success: 0, failed: 0 }

  try {
    const operations = items.map((item) => ({
      entity: item.entity,
      action: item.action,
      data: item.data,
    }))

    const result = await api.post<{ results: { success: boolean }[] }>('/sync/batch', { operations })

    const successCount = result.data.results.filter((r) => r.success).length
    const failedCount = items.length - successCount

    // Remove synced items (keep failed ones)
    const syncedIds = items
      .filter((_, i) => result.data.results[i]?.success)
      .map((item) => item.id!)
      .filter(Boolean)

    if (syncedIds.length > 0) {
      await db.offlineQueue.bulkDelete(syncedIds)
    }

    return { success: successCount, failed: failedCount }
  } catch {
    return { success: 0, failed: items.length }
  }
}
