import { useState, useRef } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Trash2, Upload } from 'lucide-react'
import { usePatients, useDeletePatient, useImportPatients } from '../../hooks/usePatients'
import { useToastStore } from '../../stores/toastStore'
import { useDebounce } from '../../hooks/useDebounce'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
import { Input } from '../../components/ui/Input'
import { Badge } from '../../components/ui/Badge'
import { Modal } from '../../components/ui/Modal'
import { TableSkeleton } from '../../components/ui/Skeleton'

export default function PatientList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [deleteTarget, setDeleteTarget] = useState<{ uuid: string; name: string } | null>(null)
  const [showImport, setShowImport] = useState(false)
  const [importResult, setImportResult] = useState<{ imported: number; skipped: number; errors: { row: number; message: string }[] } | null>(null)
  const fileRef = useRef<HTMLInputElement>(null)
  const debouncedSearch = useDebounce(search, 300)
  const { data, isLoading, error } = usePatients(page, debouncedSearch)
  const deleteMutation = useDeletePatient()
  const importMutation = useImportPatients()
  const addToast = useToastStore((s) => s.addToast)

  const handleDelete = () => {
    if (!deleteTarget) return
    deleteMutation.mutate(deleteTarget.uuid, {
      onSuccess: () => {
        addToast('success', `Pasien ${deleteTarget.name} berhasil dihapus.`)
        setDeleteTarget(null)
      },
      onError: () => {
        addToast('error', 'Gagal menghapus pasien.')
      },
    })
  }

  const handleImport = async () => {
    const file = fileRef.current?.files?.[0]
    if (!file) return
    setImportResult(null)
    try {
      const res = await importMutation.mutateAsync(file)
      setImportResult(res.data)
      addToast('success', res.message)
    } catch (err: unknown) {
      const error = err as { message?: string }
      addToast('error', error.message || 'Gagal import CSV.')
    }
  }

  const downloadTemplate = () => {
    const header = 'name,nik,medical_record_number,date_of_birth,gender,phone,address,insurance_type,blood_type\n'
    const blob = new Blob([header], { type: 'text/csv' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'template_import_pasien.csv'
    a.click()
    URL.revokeObjectURL(url)
  }

  return (
    <div className="space-y-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">Pasien</h1>
        <div className="flex gap-2 w-full sm:w-auto">
          <Button variant="secondary" onClick={() => setShowImport(true)} className="w-full sm:w-auto">
            <Upload className="w-4 h-4 mr-2" /> Import CSV
          </Button>
          <Button onClick={() => navigate('/patients/create')} className="w-full sm:w-auto">
            + Tambah Pasien
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <Input
            placeholder="Cari nama, NIK, atau No. RM..."
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
            <p className="text-center text-slate-500 dark:text-slate-400 py-8">Tidak ada pasien ditemukan</p>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">No. RM</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Nama</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden md:table-cell">Gender</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden lg:table-cell">Asuransi</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400 hidden xl:table-cell">Telepon</th>
                      <th className="text-center py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {data.data.map((p) => (
                      <tr key={p.uuid} className="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td className="py-3 px-2 font-mono text-xs text-center text-slate-600 dark:text-slate-400">{p.medical_record_number}</td>
                        <td className="py-3 px-2 font-medium text-center text-slate-900 dark:text-slate-100">{p.name}</td>
                        <td className="py-3 px-2 hidden md:table-cell text-center text-slate-600 dark:text-slate-400">{p.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                        <td className="py-3 px-2 hidden lg:table-cell text-center">
                          <Badge variant={p.insurance_type === 'bpjs' ? 'info' : p.insurance_type === 'mandiri' ? 'default' : 'purple'}>
                            {p.insurance_type?.toUpperCase() || '-'}
                          </Badge>
                        </td>
                        <td className="py-3 px-2 hidden xl:table-cell text-slate-600 dark:text-slate-400 text-center">{p.phone || '-'}</td>
                        <td className="py-3 px-2">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/patients/${p.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ uuid: p.uuid, name: p.name })}
                              className="p-1.5 rounded-lg text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                              title="Hapus"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Mobile card view */}
              <div className="block sm:hidden space-y-3">
                {data.data.map((p) => (
                  <div key={p.uuid} className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-xs text-slate-500 dark:text-slate-400">{p.medical_record_number}</span>
                      <Badge variant={p.insurance_type === 'bpjs' ? 'info' : p.insurance_type === 'mandiri' ? 'default' : 'purple'}>
                        {p.insurance_type?.toUpperCase() || '-'}
                      </Badge>
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{p.name}</p>
                    <div className="flex gap-2 text-xs text-slate-500 dark:text-slate-400">
                      <span>{p.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</span>
                      {p.phone && <span>- {p.phone}</span>}
                    </div>
                    <div className="flex gap-2 pt-1">
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/patients/${p.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: p.uuid, name: p.name })} className="flex-1">Hapus</Button>
                    </div>
                  </div>
                ))}
              </div>

              {data.meta.last_page > 1 && (
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                  <p className="text-sm text-slate-500 dark:text-slate-400 text-center sm:text-left">
                    {data.meta.total} pasien ditemukan
                  </p>
                  <div className="flex items-center justify-center gap-2">
                    <Button size="sm" variant="secondary" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>
                      Sebelumnya
                    </Button>
                    <span className="flex items-center px-3 text-sm text-slate-600 dark:text-slate-400">
                      {page} / {data.meta.last_page}
                    </span>
                    <Button size="sm" variant="secondary" disabled={page >= data.meta.last_page} onClick={() => setPage((p) => p + 1)}>
                      Selanjutnya
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardBody>
      </Card>

      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Pasien" size="sm">
        <p className="text-slate-600 dark:text-slate-400 mb-6">Yakin ingin menghapus pasien <strong>{deleteTarget?.name}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>

      {/* Import CSV Modal */}
      <Modal isOpen={showImport} onClose={() => { setShowImport(false); setImportResult(null); if (fileRef.current) fileRef.current.value = '' }} title="Import Pasien dari CSV" size="md">
        <div className="space-y-4">
          <div>
            <p className="text-sm text-slate-600 dark:text-slate-400 mb-2">
              Upload file CSV dengan format yang sesuai.{' '}
              <button onClick={downloadTemplate} className="text-blue-600 dark:text-blue-400 hover:underline">Download template</button>
            </p>
            <input ref={fileRef} type="file" accept=".csv,.txt" className="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-900/30 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50" />
          </div>

          {importResult && (
            <div className="p-3 bg-slate-50 dark:bg-slate-800 rounded-lg text-sm space-y-2">
              <p className="text-green-700 dark:text-green-400 font-medium">{importResult.imported} pasien berhasil diimport</p>
              {importResult.skipped > 0 && <p className="text-amber-700 dark:text-amber-400">{importResult.skipped} baris dilewati</p>}
              {importResult.errors.length > 0 && (
                <div className="max-h-40 overflow-y-auto">
                  {importResult.errors.map((e, i) => (
                    <p key={i} className="text-red-600 dark:text-red-400 text-xs">Baris {e.row}: {e.message}</p>
                  ))}
                </div>
              )}
            </div>
          )}

          <div className="flex justify-end gap-2">
            <Button variant="secondary" onClick={() => { setShowImport(false); setImportResult(null); if (fileRef.current) fileRef.current.value = '' }}>Tutup</Button>
            <Button loading={importMutation.isPending} onClick={handleImport}>Import</Button>
          </div>
        </div>
      </Modal>
    </div>
  )
}
