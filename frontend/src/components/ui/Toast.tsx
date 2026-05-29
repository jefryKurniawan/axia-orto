import { useToastStore } from '../../stores/toastStore'
import { CheckCircle, XCircle, Info, X } from 'lucide-react'

const icons = {
  success: <CheckCircle className="w-5 h-5 text-green-500" />,
  error: <XCircle className="w-5 h-5 text-red-500" />,
  info: <Info className="w-5 h-5 text-blue-500" />,
}

const bgColors = {
  success: 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800',
  error: 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800',
  info: 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800',
}

export function ToastContainer() {
  const { toasts, removeToast } = useToastStore()

  if (!toasts.length) return null

  return (
    <div className="fixed top-4 right-4 z-[100] space-y-2 w-80 max-w-[calc(100vw-2rem)]">
      {toasts.map((toast) => (
        <div
          key={toast.id}
          className={`flex items-start gap-3 p-4 rounded-lg border shadow-lg animate-slide-in ${bgColors[toast.type]}`}
        >
          <span className="flex-shrink-0 mt-0.5">{icons[toast.type]}</span>
          <p className="flex-1 text-sm text-slate-700 dark:text-slate-300">{toast.message}</p>
          <button
            onClick={() => removeToast(toast.id)}
            className="flex-shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
          >
            <X className="w-4 h-4" />
          </button>
        </div>
      ))}
    </div>
  )
}
