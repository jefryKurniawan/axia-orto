import { useAppStore } from '../stores/appStore'
import { processQueue, getPendingCount } from '../lib/offlineQueue'

export function useSyncStatus() {
  const isOnline = useAppStore((s) => s.isOnline)
  const pendingSyncCount = useAppStore((s) => s.pendingSyncCount)
  const setPendingSyncCount = useAppStore((s) => s.setPendingSyncCount)

  const syncNow = async () => {
    if (!isOnline) return { success: 0, failed: 0 }
    const result = await processQueue()
    setPendingSyncCount(await getPendingCount())
    if (result.success > 0) {
      window.dispatchEvent(new CustomEvent('sync-complete'))
    }
    return result
  }

  return { isOnline, pendingSyncCount, syncNow }
}
