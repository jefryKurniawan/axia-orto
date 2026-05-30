import type { ReactNode } from 'react'

interface CardProps {
  children: ReactNode
  className?: string
  accent?: 'none' | 'left' | 'top'
  accentColor?: string
  hover?: boolean
  interactive?: boolean
}

export function Card({
  children,
  className = '',
  accent = 'none',
  accentColor = 'border-l-blue-500',
  hover = false,
  interactive = false,
}: CardProps) {
  const accentClass = accent === 'left'
    ? `border-l-[3px] ${accentColor}`
    : accent === 'top'
      ? `border-t-[3px] ${accentColor}`
      : ''

  const hoverClass = hover
    ? 'hover:-translate-y-0.5 hover:shadow-md transition-all duration-200'
    : ''

  const interactiveClass = interactive
    ? 'cursor-pointer active:scale-[0.99] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 focus-visible:ring-offset-2'
    : ''

  return (
    <div
      className={`bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm animate-stagger-in ${accentClass} ${hoverClass} ${interactiveClass} ${className}`}
      tabIndex={interactive ? 0 : undefined}
      role={interactive ? 'button' : undefined}
    >
      {children}
    </div>
  )
}

export function CardHeader({ children, className = '' }: { children: ReactNode; className?: string }) {
  return (
    <div className={`px-5 py-3.5 border-b border-slate-200 dark:border-slate-700 ${className}`}>
      {children}
    </div>
  )
}

export function CardBody({ children, className = '', compact = false }: { children: ReactNode; className?: string; compact?: boolean }) {
  return (
    <div className={`${compact ? 'px-4 py-3' : 'px-5 py-4'} ${className}`}>
      {children}
    </div>
  )
}
