import type { ReactNode } from 'react'

type BadgeVariant = 'default' | 'success' | 'warning' | 'danger' | 'info' | 'purple'

interface BadgeProps {
  children: ReactNode
  variant?: BadgeVariant
  className?: string
}

const variants: Record<BadgeVariant, string> = {
  default: 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200',
  success: 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300',
  warning: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300',
  danger: 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300',
  info: 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300',
  purple: 'bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300',
}

export function Badge({ children, variant = 'default', className = '' }: BadgeProps) {
  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${variants[variant]} ${className}`}>
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
    failed: { label: 'Gagal', variant: 'danger' },
    refunded: { label: 'Refund', variant: 'default' },
  }

  const { label, variant } = map[status] ?? { label: status, variant: 'default' }
  return <Badge variant={variant}>{label}</Badge>
}
