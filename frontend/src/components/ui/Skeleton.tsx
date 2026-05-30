export function Skeleton({ className = '' }: { className?: string }) {
  return <div className={`rounded-md animate-shimmer ${className}`} />
}

export function CardSkeleton() {
  return (
    <div className="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-5 space-y-3">
      <div className="flex items-center justify-between">
        <Skeleton className="h-3 w-24" />
        <Skeleton className="h-8 w-8 rounded-lg" />
      </div>
      <Skeleton className="h-8 w-20" />
      <Skeleton className="h-3 w-32" />
    </div>
  )
}

export function TableSkeleton({ rows = 5, columns = 5 }: { rows?: number; columns?: number }) {
  const widths = ['w-16', 'w-32', 'w-24', 'w-20', 'w-28', 'w-16', 'w-20']
  return (
    <div className="space-y-0">
      {/* Header skeleton */}
      <div className="flex items-center gap-4 py-3 px-4 border-b border-slate-200 dark:border-slate-700">
        {Array.from({ length: columns }).map((_, i) => (
          <Skeleton key={i} className={`h-3 ${widths[i % widths.length]}`} />
        ))}
      </div>
      {/* Row skeletons */}
      {Array.from({ length: rows }).map((_, row) => (
        <div key={row} className="flex items-center gap-4 py-3.5 px-4 border-b border-slate-100 dark:border-slate-800">
          {Array.from({ length: columns }).map((_, col) => (
            <Skeleton key={col} className={`h-4 ${widths[(row + col) % widths.length]}`} />
          ))}
        </div>
      ))}
    </div>
  )
}

export function StatSkeleton() {
  return (
    <div className="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 p-5">
      <div className="flex items-start justify-between">
        <div className="space-y-2 flex-1">
          <Skeleton className="h-3 w-28" />
          <Skeleton className="h-9 w-16" />
          <Skeleton className="h-3 w-20" />
        </div>
        <Skeleton className="h-5 w-5 rounded" />
      </div>
    </div>
  )
}
