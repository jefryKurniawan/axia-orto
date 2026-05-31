import { Button } from './Button'

interface PaginationProps {
  currentPage: number
  lastPage: number
  total: number
  perPage: number
  entityLabel: string
  onPageChange: (page: number) => void
}

export function Pagination({ currentPage, lastPage, total, perPage, entityLabel, onPageChange }: PaginationProps) {
  if (lastPage <= 1) return null
  const start = (currentPage - 1) * perPage + 1
  const end = Math.min(currentPage * perPage, total)

  return (
    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
      <p className="text-xs text-slate-400 dark:text-slate-500 text-center sm:text-left">
        Menampilkan {start}-{end} dari {total} {entityLabel}
      </p>
      <div className="flex items-center justify-center gap-1">
        <Button size="sm" variant="ghost" disabled={currentPage <= 1} onClick={() => onPageChange(currentPage - 1)}>
          &laquo;
        </Button>
        {Array.from({ length: Math.min(lastPage, 5) }, (_, i) => {
          const p = i + 1
          return (
            <button
              key={p}
              onClick={() => onPageChange(p)}
              className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
                p === currentPage
                  ? 'bg-blue-600 text-white'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
              }`}
            >
              {p}
            </button>
          )
        })}
        {lastPage > 5 && <span className="text-xs text-slate-400 px-1">...</span>}
        {lastPage > 5 && (
          <button
            onClick={() => onPageChange(lastPage)}
            className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
              lastPage === currentPage
                ? 'bg-blue-600 text-white'
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
            }`}
          >
            {lastPage}
          </button>
        )}
        <Button size="sm" variant="ghost" disabled={currentPage >= lastPage} onClick={() => onPageChange(currentPage + 1)}>
          &raquo;
        </Button>
      </div>
    </div>
  )
}
