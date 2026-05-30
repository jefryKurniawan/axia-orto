import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useServices, useDeleteService } from '../../hooks/useServices'
import { useDebounce } from '../../hooks/useDebounce'
import { useToastStore } from '../../stores/toastStore'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { Badge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { TableSkeleton } from '../../components/ui/Skeleton'

const typeLabels: Record<string, string> = {
  konsultasi: 'Konsultasi',
  ortosis: 'Ortosis',
  protesis: 'Protesis',
  terapi: 'Terapi',
  alat: 'Alat',
}

export default function ServiceList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [deleteTarget, setDeleteTarget] = useState<{ uuid: string; name: string } | null>(null)
  const debouncedSearch = useDebounce(search, 300)
  const { data, isLoading, error } = useServices(page, debouncedSearch)
  const deleteMutation = useDeleteService()
  const addToast = useToastStore((s) => s.addToast)

  const formatPrice = (price: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price)

  const handleDelete = () => {
    if (!deleteTarget) return
    deleteMutation.mutate(deleteTarget.uuid, {
      onSuccess: () => {
        addToast('success', `Layanan ${deleteTarget.name} berhasil dihapus.`)
        setDeleteTarget(null)
      },
      onError: () => addToast('error', 'Gagal menghapus layanan.'),
    })
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Layanan</h1>
        <Button onClick={() => navigate('/services/create')} className="w-full sm:w-auto">
          + Tambah Layanan
        </Button>
      </div>

      <Card>
        <CardHeader>
          <Input
            placeholder="Cari nama atau kode layanan..."
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1) }}
          />
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={5} />
          ) : error ? (
            <p className="text-center text-red-600 dark:text-red-400 py-8">Gagal memuat data</p>
          ) : !data?.data.length ? (
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Tidak ada layanan ditemukan</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Kode</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Nama</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden md:table-cell">Tipe</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Harga</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden lg:table-cell">Durasi</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Status</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((s) => (
                      <tr key={s.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="py-3 px-2 font-mono text-xs text-center text-slate-600 dark:text-slate-400">{s.code}</td>
                        <td className="py-3 px-2 font-medium text-center text-slate-900 dark:text-slate-100">{s.name}</td>
                        <td className="py-3 px-2 hidden md:table-cell text-center">
                          <Badge variant="default">{typeLabels[s.service_type] || s.service_type}</Badge>
                        </td>
                        <td className="py-3 px-2 text-center font-medium text-slate-900 dark:text-slate-100">{formatPrice(s.price)}</td>
                        <td className="py-3 px-2 hidden lg:table-cell text-slate-600 dark:text-slate-400 text-center">
                          {s.duration_days ? `${s.duration_days} hari` : '-'}
                        </td>
                        <td className="py-3 px-2 text-center">
                          <Badge variant={s.is_active ? 'success' : 'default'}>{s.is_active ? 'Aktif' : 'Nonaktif'}</Badge>
                        </td>
                        <td className="py-3 px-2">
                          <div className="flex justify-center gap-1">
                            <Button size="sm" variant="ghost" onClick={() => navigate(`/services/${s.uuid}/edit`)}>Edit</Button>
                            <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: s.uuid, name: s.name })}>Hapus</Button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-3">
                {data.data.map((s) => (
                  <div key={s.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-xs text-slate-500 dark:text-slate-400">{s.code}</span>
                      <Badge variant={s.is_active ? 'success' : 'default'}>{s.is_active ? 'Aktif' : 'Nonaktif'}</Badge>
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{s.name}</p>
                    <p className="text-sm font-semibold text-slate-700 dark:text-slate-300">{formatPrice(s.price)}</p>
                    <div className="flex gap-2 text-xs text-slate-500 dark:text-slate-400">
                      <Badge variant="default">{typeLabels[s.service_type] || s.service_type}</Badge>
                      {s.duration_days && <span>{s.duration_days} hari</span>}
                    </div>
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/services/${s.uuid}/edit`)} className="flex-1">Edit</Button>
                      <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: s.uuid, name: s.name })} className="flex-1">Hapus</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">{data.meta.total} layanan ditemukan</p>
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

      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Layanan" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus layanan <strong>{deleteTarget?.name}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>
    </div>
  )
}
