import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useConsultations, useDeleteConsultation } from '../../hooks/useConsultations'
import { useDebounce } from '../../hooks/useDebounce'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { StatusBadge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { TableSkeleton } from '../../components/ui/Skeleton'

const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'scheduled', label: 'Dijadwalkan' },
  { value: 'in_progress', label: 'Berlangsung' },
  { value: 'completed', label: 'Selesai' },
  { value: 'cancelled', label: 'Dibatalkan' },
]

export default function ConsultationList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('')
  const [deleteTarget, setDeleteTarget] = useState<{ uuid: string; patient: string } | null>(null)
  const debouncedSearch = useDebounce(search, 300)
  const { data, isLoading, error } = useConsultations(page, debouncedSearch, status)
  const deleteMutation = useDeleteConsultation()
  const addToast = useToastStore((s) => s.addToast)

  const handleDelete = () => {
    if (!deleteTarget) return
    deleteMutation.mutate(deleteTarget.uuid, {
      onSuccess: () => {
        addToast('success', 'Konsultasi berhasil dihapus.')
        setDeleteTarget(null)
      },
      onError: () => addToast('error', 'Gagal menghapus konsultasi.'),
    })
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Konsultasi</h1>
        <Button onClick={() => navigate('/consultations/create')} className="w-full sm:w-auto">
          + Konsultasi Baru
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <Input
              placeholder="Cari pasien atau keluhan..."
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1) }}
            />
            <select
              value={status}
              onChange={(e) => { setStatus(e.target.value); setPage(1) }}
              className="px-3 py-2 border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              {statusOptions.map((opt) => (
                <option key={opt.value} value={opt.value}>{opt.label}</option>
              ))}
            </select>
          </div>
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={5} />
          ) : error ? (
            <p className="text-center text-red-600 dark:text-red-400 py-8">Gagal memuat data</p>
          ) : !data?.data.length ? (
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Tidak ada konsultasi ditemukan</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Tanggal</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Pasien</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden md:table-cell">Dokter</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden lg:table-cell">Keluhan</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Status</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((c) => (
                      <tr key={c.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="py-3 px-2 text-xs text-slate-600 dark:text-slate-400 text-center">
                          {new Date(c.consultation_date).toLocaleDateString('id-ID')}
                        </td>
                        <td className="py-3 px-2 font-medium text-center">{c.patient_name || '-'}</td>
                        <td className="py-3 px-2 hidden md:table-cell text-slate-600 dark:text-slate-400 text-center">{c.doctor_name || '-'}</td>
                        <td className="py-3 px-2 hidden lg:table-cell text-slate-600 dark:text-slate-400 truncate max-w-[200px] text-center">{c.complaint}</td>
                        <td className="py-3 px-2 text-center"><StatusBadge status={c.status} /></td>
                        <td className="py-3 px-2">
                          <div className="flex justify-center gap-1">
                            <Button size="sm" variant="ghost" onClick={() => navigate(`/consultations/${c.uuid}`)}>Detail</Button>
                            <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: c.uuid, patient: c.patient_name || '-' })}>Hapus</Button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-3">
                {data.data.map((c) => (
                  <div key={c.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="text-xs text-slate-500 dark:text-slate-400">
                        {new Date(c.consultation_date).toLocaleDateString('id-ID')}
                      </span>
                      <StatusBadge status={c.status} />
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{c.patient_name || '-'}</p>
                    {c.doctor_name && <p className="text-xs text-slate-500 dark:text-slate-400">Dokter: {c.doctor_name}</p>}
                    {c.complaint && <p className="text-xs text-slate-600 dark:text-slate-400 truncate">{c.complaint}</p>}
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/consultations/${c.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: c.uuid, patient: c.patient_name || '-' })} className="flex-1">Hapus</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">
                    {data.meta.total} konsultasi ditemukan
                  </p>
                  <div className="flex items-center justify-center gap-2">
                    <Button size="sm" variant="secondary" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>Sebelumnya</Button>
                    <span className="flex items-center px-3 text-sm text-slate-600 dark:text-slate-400">{page} / {data.meta.last_page}</span>
                    <Button size="sm" variant="secondary" disabled={page >= data.meta.last_page} onClick={() => setPage((p) => p + 1)}>Selanjutnya</Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>

      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Konsultasi" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus konsultasi untuk <strong>{deleteTarget?.patient}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
