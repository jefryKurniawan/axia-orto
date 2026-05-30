import { useState, Fragment } from 'react'
import { ShieldCheck, ChevronDown, ChevronUp } from 'lucide-react'
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
      <div className="text-center py-12">
        <ShieldCheck className="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" />
        <p className="text-slate-500 dark:text-slate-400">Halaman ini hanya untuk admin.</p>
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
      <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Audit Log</h1>

      <Card>
        <CardHeader className="flex flex-col sm:flex-row gap-3">
          <select
            value={filters.auditable_type}
            onChange={(e) => { setFilters(f => ({ ...f, auditable_type: e.target.value })); setPage(1) }}
            className="px-3 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 rounded-lg text-sm"
          >
            <option value="">Semua Model</option>
            <option value="App\Models\Patient">Pasien</option>
            <option value="App\Models\Consultation">Konsultasi</option>
            <option value="App\Models\TreatmentOrder">Order</option>
            <option value="App\Models\Payment">Pembayaran</option>
            <option value="App\Models\InventoryItem">Inventaris</option>
          </select>
          <input
            type="date"
            value={filters.date_from}
            onChange={(e) => { setFilters(f => ({ ...f, date_from: e.target.value })); setPage(1) }}
            className="px-3 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 rounded-lg text-sm"
            placeholder="Dari"
          />
          <input
            type="date"
            value={filters.date_to}
            onChange={(e) => { setFilters(f => ({ ...f, date_to: e.target.value })); setPage(1) }}
            className="px-3 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 rounded-lg text-sm"
            placeholder="Sampai"
          />
        </CardHeader>
        <CardBody>
          {isLoading ? <TableSkeleton rows={8} />
           : error ? <p className="text-center text-red-600 dark:text-red-400 py-8">Gagal memuat data</p>
           : !data?.data.length ? (
            <div className="text-center py-12">
              <ShieldCheck className="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" />
              <p className="text-slate-500 dark:text-slate-400">Belum ada log audit</p>
            </div>
           ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-2 px-3 font-medium text-slate-600 dark:text-slate-400 w-8"></th>
                      <th className="text-center py-2 px-3 font-medium text-slate-600 dark:text-slate-400">Waktu</th>
                      <th className="text-center py-2 px-3 font-medium text-slate-600 dark:text-slate-400">User</th>
                      <th className="text-center py-2 px-3 font-medium text-slate-600 dark:text-slate-400">Model</th>
                      <th className="text-center py-2 px-3 font-medium text-slate-600 dark:text-slate-400">Event</th>
                      <th className="text-center py-2 px-3 font-medium text-slate-600 dark:text-slate-400">IP</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((log) => (
                      <Fragment key={log.id}>
                        <tr className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer" onClick={() => setExpandedId(expandedId === log.id ? null : log.id)}>
                          <td className="text-center py-2 px-3 text-slate-400">{expandedId === log.id ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}</td>
                          <td className="text-center py-2 px-3 text-slate-900 dark:text-slate-100">{new Date(log.created_at).toLocaleString('id-ID')}</td>
                          <td className="text-center py-2 px-3 text-slate-600 dark:text-slate-400">{log.user_name || '-'}</td>
                          <td className="text-center py-2 px-3"><Badge variant="default">{modelTypeLabels[log.auditable_type] || log.auditable_type}</Badge></td>
                          <td className="text-center py-2 px-3"><Badge variant={eventTypeColors[log.event]}>{eventTypeLabels[log.event]}</Badge></td>
                          <td className="text-center py-2 px-3 text-xs text-slate-500 dark:text-slate-400">{log.ip_address || '-'}</td>
                        </tr>
                        {expandedId === log.id && (
                          <tr key={`${log.id}-detail`}>
                            <td colSpan={6} className="px-6 py-3 bg-slate-50 dark:bg-slate-800/50">
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
              <div className="block sm:hidden space-y-3">
                {data.data.map((log) => (
                  <div key={log.id} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                    <div className="flex items-center justify-between mb-2">
                      <div className="flex items-center gap-2">
                        <Badge variant={eventTypeColors[log.event]}>{eventTypeLabels[log.event]}</Badge>
                        <Badge variant="default">{modelTypeLabels[log.auditable_type] || log.auditable_type}</Badge>
                      </div>
                      <button onClick={() => toggleExpand(log.id)} className="text-slate-400">
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
              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">{data.meta.total} log ditemukan</p>
                  <div className="flex items-center justify-center gap-2">
                    <Button size="sm" variant="secondary" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>Sebelumnya</Button>
                    <span className="text-sm text-slate-600 dark:text-slate-400">{page} / {data.meta.last_page}</span>
                    <Button size="sm" variant="secondary" disabled={page >= data.meta.last_page} onClick={() => setPage((p) => p + 1)}>Selanjutnya</Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>
    </div>
  )
}
