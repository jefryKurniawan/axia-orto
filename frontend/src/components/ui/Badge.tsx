import type { ReactNode } from 'react'

type BadgeVariant = 'default' | 'success' | 'warning' | 'danger' | 'info' | 'purple'

interface BadgeProps {
  children: ReactNode
  variant?: BadgeVariant
  className?: string
  dot?: boolean
}

const variants: Record<BadgeVariant, string> = {
  default: 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300',
  success: 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300',
  warning: 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
  danger: 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300',
  info: 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
  purple: 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
}

const dotColors: Record<BadgeVariant, string> = {
  default: 'bg-slate-400 dark:bg-slate-500',
  success: 'bg-green-500',
  warning: 'bg-amber-500',
  danger: 'bg-red-500',
  info: 'bg-blue-500',
  purple: 'bg-purple-500',
}

export function Badge({ children, variant = 'default', className = '', dot = false }: BadgeProps) {
  return (
    <span className={`inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium animate-pop ${variants[variant]} ${className}`}>
      {dot && <span className={`w-1.5 h-1.5 rounded-full ${dotColors[variant]}`} />}
      {children}
    </span>
  )
}

export function StatusBadge({ status }: { status: string }) {
  const map: Record<string, { label: string; variant: BadgeVariant }> = {
    // Consultation statuses
    scheduled: { label: 'Dijadwalkan', variant: 'warning' },
    in_progress: { label: 'Berlangsung', variant: 'info' },
    completed: { label: 'Selesai', variant: 'success' },
    cancelled: { label: 'Dibatalkan', variant: 'danger' },
    active: { label: 'Aktif', variant: 'success' },
    inactive: { label: 'Nonaktif', variant: 'default' },
    // Order statuses
    draft: { label: 'Draft', variant: 'default' },
    confirmed: { label: 'Dikonfirmasi', variant: 'info' },
    production: { label: 'Produksi', variant: 'warning' },
    ready: { label: 'Siap', variant: 'success' },
    delivered: { label: 'Dikirim', variant: 'success' },
    // Payment statuses
    pending: { label: 'Pending', variant: 'warning' },
    paid: { label: 'Dibayar', variant: 'success' },
    failed: { label: 'Gagal', variant: 'danger' },
    refunded: { label: 'Refund', variant: 'default' },
  }

  const { label, variant } = map[status] ?? { label: status, variant: 'default' }
  return <Badge variant={variant} dot>{label}</Badge>
}
