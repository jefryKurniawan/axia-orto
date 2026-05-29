import { useEffect } from 'react'
import { useAppStore } from '../stores/appStore'
import { getPendingCount, processQueue } from '../lib/offlineQueue'

export function useOnlineStatus() {
  const isOnline = useAppStore((s) => s.isOnline)
  const setIsOnline = useAppStore((s) => s.setIsOnline)
  const setPendingSyncCount = useAppStore((s) => s.setPendingSyncCount)

  useEffect(() => {
    setIsOnline(navigator.onLine)

    const handleOnline = async () => {
      setIsOnline(true)
      // Auto-sync when coming back online
      const count = await getPendingCount()
      if (count > 0) {
        const result = await processQueue()
        if (result.success > 0) {
          // Refresh queries after sync
          window.dispatchEvent(new CustomEvent('sync-complete'))
        }
        setPendingSyncCount(await getPendingCount())
      }
    }

    const handleOffline = () => {
      setIsOnline(false)
    }

    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)

    // Initial pending count
    getPendingCount().then(setPendingSyncCount)

    return () => {
      window.removeEventListener('online', handleOnline)
      window.removeEventListener('offline', handleOffline)
    }
  }, [setIsOnline, setPendingSyncCount])

  return { isOnline }
}
