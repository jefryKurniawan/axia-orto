import { useState, Fragment } from 'react'
import { ShieldCheck, ChevronDown, ChevronUp, AlertCircle } from 'lucide-react'
import { useAuditLogs } from '../../hooks/useAuditLogs'
import { useAuth } from '../../contexts/AuthContext'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Badge } from '../../components/ui/Badge'
import { TableSkeleton } from '../../components/ui/Skeleton'
import type { AuditLog } from '../../types'

const eventTypeLabels: Record<string, string> = {
  created: 'Dibuat',
  updated: 'Diubah',
  deleted: 'Dihapus',
}

const eventTypeColors: Record<string, 'success' | 'warning' | 'danger'> = {
  created: 'success',
  updated: 'warning',
  deleted: 'danger',
}

const modelTypeLabels: Record<string, string> = {
  'App\\Models\\Patient': 'Pasien',
  'App\\Models\\Consultation': 'Konsultasi',
  'App\\Models\\TreatmentOrder': 'Order',
  'App\\Models\\Payment': 'Pembayaran',
  'App\\Models\\InventoryItem': 'Inventaris',
}

export default function AuditLogList() {
  const { user } = useAuth()
  const [page, setPage] = useState(1)
  const [expandedId, setExpandedId] = useState<number | null>(null)
  const [filters, setFilters] = useState({
    auditable_type: '',
    date_from: '',
    date_to: '',
  })
  const { data, isLoading, error } = useAuditLogs(page, filters)

  if (user?.role !== 'admin') {
    return (
      <div className="flex flex-col items-center justify-center py-20 gap-3">
        <ShieldCheck className="w-12 h-12 text-slate-300 dark:text-slate-600" />
        <p className="text-sm text-slate-500 dark:text-slate-400">Halaman ini hanya untuk admin.</p>
      </div>
    )
  }

  const toggleExpand = (id: number) => setExpandedId(expandedId === id ? null : id)

  const renderChanges = (log: AuditLog) => {
    if (log.event === 'created' && log.new_values) {
      return (
        <div className="text-xs space-y-1">
          {Object.entries(log.new_values).map(([k, v]) => (
            <div key={k}><span className="text-slate-500 dark:text-slate-400">{k}:</span> <span className="text-green-600 dark:text-green-400">{String(v)}</span></div>
          ))}
        </div>
      )
    }
    if (log.event === 'updated' && log.old_values && log.new_values) {
      const keys = new Set([...Object.keys(log.old_values), ...Object.keys(log.new_values)])
      return (
        <div className="text-xs space-y-1">
          {Array.from(keys).filter(k => log.old_values?.[k] !== log.new_values?.[k]).map(k => (
            <div key={k}>
              <span className="text-slate-500 dark:text-slate-400">{k}:</span>{' '}
              <span className="text-red-600 dark:text-red-400 line-through">{String(log.old_values?.[k] ?? '-')}</span>{' '}
              <span className="text-green-600 dark:text-green-400">{String(log.new_values?.[k] ?? '-')}</span>
            </div>
          ))}
        </div>
      )
    }
    if (log.event === 'deleted' && log.old_values) {
      return (
        <div className="text-xs space-y-1">
          {Object.entries(log.old_values).slice(0, 5).map(([k, v]) => (
            <div key={k}><span className="text-slate-500 dark:text-slate-400">{k}:</span> <span className="text-red-600 dark:text-red-400 line-through">{String(v)}</span></div>
          ))}
        </div>
      )
    }
    return <span className="text-xs text-slate-400">-</span>
  }

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Audit Log</h1>

      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <ShieldCheck className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
              <select
                value={filters.auditable_type}
                onChange={(e) => { setFilters(f => ({ ...f, auditable_type: e.target.value })); setPage(1) }}
                className="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all appearance-none"
              >
                <option value="">Semua Model</option>
                <option value="App\Models\Patient">Pasien</option>
                <option value="App\Models\Consultation">Konsultasi</option>
                <option value="App\Models\TreatmentOrder">Order</option>
                <option value="App\Models\Payment">Pembayaran</option>
                <option value="App\Models\InventoryItem">Inventaris</option>
              </select>
            </div>
            <div className="flex gap-2">
              <input
                type="date"
                value={filters.date_from}
                onChange={(e) => { setFilters(f => ({ ...f, date_from: e.target.value })); setPage(1) }}
                className="flex-1 sm:flex-none px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                placeholder="Dari"
              />
              <input
                type="date"
                value={filters.date_to}
                onChange={(e) => { setFilters(f => ({ ...f, date_to: e.target.value })); setPage(1) }}
                className="flex-1 sm:flex-none px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
                placeholder="Sampai"
              />
            </div>
          </div>
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={8} columns={6} />
          ) : error ? (
            <div className="flex flex-col items-center py-10 gap-2">
              <AlertCircle className="w-6 h-6 text-red-400" />
              <p className="text-sm text-red-600 dark:text-red-400">Gagal memuat data</p>
            </div>
          ) : !data?.data.length ? (
            <div className="flex flex-col items-center py-12 gap-3">
              <ShieldCheck className="w-10 h-10 text-slate-300 dark:text-slate-600" />
              <p className="text-sm text-slate-500 dark:text-slate-400">Belum ada log audit</p>
            </div>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 w-8"></th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Waktu</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">User</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Model</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Event</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">IP</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                    {data.data.map((log, i) => (
                      <Fragment key={log.id}>
                        <tr
                          className="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors cursor-pointer animate-row-enter"
                          style={{ animationDelay: `${i * 30}ms` }}
                          onClick={() => setExpandedId(expandedId === log.id ? null : log.id)}
                        >
                          <td className="py-3.5 px-4 text-slate-400">{expandedId === log.id ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}</td>
                          <td className="py-3.5 px-4 text-slate-900 dark:text-slate-100">{new Date(log.created_at).toLocaleString('id-ID')}</td>
                          <td className="py-3.5 px-4 text-slate-600 dark:text-slate-400">{log.user_name || '-'}</td>
                          <td className="py-3.5 px-4"><Badge variant="default">{modelTypeLabels[log.auditable_type] || log.auditable_type}</Badge></td>
                          <td className="py-3.5 px-4"><Badge variant={eventTypeColors[log.event]}>{eventTypeLabels[log.event]}</Badge></td>
                          <td className="py-3.5 px-4 text-xs text-slate-500 dark:text-slate-400">{log.ip_address || '-'}</td>
                        </tr>
                        {expandedId === log.id && (
                          <tr key={`${log.id}-detail`}>
                            <td colSpan={6} className="px-6 py-3 bg-slate-50/80 dark:bg-slate-800/30">
                              {renderChanges(log)}
                            </td>
                          </tr>
                        )}
                      </Fragment>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-2">
                {data.data.map((log) => (
                  <div
                    key={log.id}
                    className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
                    onClick={() => toggleExpand(log.id)}
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <Badge variant={eventTypeColors[log.event]}>{eventTypeLabels[log.event]}</Badge>
                        <Badge variant="default">{modelTypeLabels[log.auditable_type] || log.auditable_type}</Badge>
                      </div>
                      <button onClick={(e) => { e.stopPropagation(); toggleExpand(log.id) }} className="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        {expandedId === log.id ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                      </button>
                    </div>
                    <p className="text-xs text-slate-500 dark:text-slate-400">{new Date(log.created_at).toLocaleString('id-ID')} &middot; {log.user_name || 'System'}</p>
                    {expandedId === log.id && (
                      <div className="mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                        {renderChanges(log)}
                      </div>
                    )}
                  </div>
                ))}
              </div>

              {/* Pagination */}
              {data.meta.last_page > 1 && (() => {
                const { current_page, last_page, total, per_page } = data.meta
                const start = (current_page - 1) * per_page + 1
                const end = Math.min(current_page * per_page, total)
                return (
                  <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <p className="text-xs text-slate-400 dark:text-slate-500 text-center sm:text-left">
                      Menampilkan {start}-{end} dari {total} log
                    </p>
                    <div className="flex items-center justify-center gap-1">
                      <Button size="sm" variant="ghost" disabled={current_page <= 1} onClick={() => setPage((p) => p - 1)}>
                        &laquo;
                      </Button>
                      {Array.from({ length: Math.min(last_page, 5) }, (_, i) => {
                        const p = i + 1
                        return (
                          <button
                            key={p}
                            onClick={() => setPage(p)}
                            className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
                              p === current_page
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                            }`}
                          >
                            {p}
                          </button>
                        )
                      })}
                      {last_page > 5 && <span className="text-xs text-slate-400 px-1">...</span>}
                      {last_page > 5 && (
                        <button
                          onClick={() => setPage(last_page)}
                          className={`w-8 h-8 rounded-md text-xs font-medium transition-colors ${
                            last_page === current_page
                              ? 'bg-blue-600 text-white'
                              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                          }`}
                        >
                          {last_page}
                        </button>
                      )}
                      <Button size="sm" variant="ghost" disabled={current_page >= last_page} onClick={() => setPage((p) => p + 1)}>
                        &raquo;
                      </Button>
                    </div>
                  </div>
                )
              })()}
            </>
          )}
        </CardBody>
      </Card>
    </div>
  )
}
