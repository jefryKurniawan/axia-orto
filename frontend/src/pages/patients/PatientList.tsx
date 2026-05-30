import { useState, useRef } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye, Trash2, Upload, FileSpreadsheet, Download, CheckCircle, AlertCircle, BarChart3, Search, X, Users } from 'lucide-react'
import { usePatients, useDeletePatient, useImportPatients } from '../../hooks/usePatients'
import { useToastStore } from '../../stores/toastStore'
import { useDebounce } from '../../hooks/useDebounce'
import { Card, CardBody, CardHeader } from '../../components/ui/Card'
import { Button } from '../../components/ui/Button'
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
  const [fileName, setFileName] = useState('')
  const [dragOver, setDragOver] = useState(false)
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

  const renderPagination = () => {
    if (!data?.meta || data.meta.last_page <= 1) return null
    const { current_page, last_page, total } = data.meta
    const start = (current_page - 1) * data.meta.per_page + 1
    const end = Math.min(current_page * data.meta.per_page, total)

    return (
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
        <p className="text-xs text-slate-400 dark:text-slate-500 text-center sm:text-left">
          Menampilkan {start}-{end} dari {total} pasien
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
  }

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Pasien</h1>
        <div className="flex gap-2 w-full sm:w-auto">
          <Button variant="subtle" onClick={() => setShowImport(true)} className="w-full sm:w-auto">
            <Upload className="w-4 h-4 mr-1.5" /> Import CSV
          </Button>
          <Button onClick={() => navigate('/patients/create')} className="w-full sm:w-auto">
            + Tambah Pasien
          </Button>
        </div>
      </div>

      {/* Table Card */}
      <Card>
        <CardHeader>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input
              type="text"
              placeholder="Cari nama, NIK, atau No. RM..."
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1) }}
              className="w-full pl-9 pr-8 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all"
            />
            {search && (
              <button
                onClick={() => { setSearch(''); setPage(1) }}
                className="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>
        </CardHeader>
        <CardBody>
          {isLoading ? (
            <TableSkeleton rows={5} columns={5} />
          ) : error ? (
            <div className="flex flex-col items-center py-10 gap-2">
              <AlertCircle className="w-6 h-6 text-red-400" />
              <p className="text-sm text-red-600 dark:text-red-400">Gagal memuat data</p>
            </div>
          ) : !data?.data.length ? (
            <div className="flex flex-col items-center py-12 gap-3">
              <Users className="w-10 h-10 text-slate-300 dark:text-slate-600" />
              <p className="text-sm text-slate-500 dark:text-slate-400">
                {search ? 'Tidak ada pasien ditemukan' : 'Belum ada pasien'}
              </p>
              {!search && (
                <Button size="sm" onClick={() => navigate('/patients/create')}>
                  + Tambah Pasien
                </Button>
              )}
            </div>
          ) : (
            <>
              {/* Desktop table */}
              <div className="hidden sm:block overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-slate-200 dark:border-slate-700">
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">No. RM</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nama</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden md:table-cell">Gender</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden lg:table-cell">Asuransi</th>
                      <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 hidden xl:table-cell">Telepon</th>
                      <th className="text-center py-3 px-4 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                    {data.data.map((p, i) => (
                      <tr
                        key={p.uuid}
                        className="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors animate-row-enter"
                        style={{ animationDelay: `${i * 30}ms` }}
                      >
                        <td className="py-3.5 px-4 font-mono text-xs text-slate-500 dark:text-slate-400">{p.medical_record_number}</td>
                        <td className="py-3.5 px-4 font-medium text-slate-900 dark:text-slate-100">{p.name}</td>
                        <td className="py-3.5 px-4 hidden md:table-cell text-slate-600 dark:text-slate-400">{p.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                        <td className="py-3.5 px-4 hidden lg:table-cell">
                          <Badge variant={p.insurance_type === 'bpjs' ? 'info' : p.insurance_type === 'mandiri' ? 'default' : 'purple'}>
                            {p.insurance_type?.toUpperCase() || '-'}
                          </Badge>
                        </td>
                        <td className="py-3.5 px-4 hidden xl:table-cell text-slate-600 dark:text-slate-400">{p.phone || '-'}</td>
                        <td className="py-3.5 px-4">
                          <div className="flex justify-center gap-1">
                            <button
                              onClick={() => navigate(`/patients/${p.uuid}`)}
                              className="p-1.5 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-150"
                              title="Detail"
                            >
                              <Eye className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => setDeleteTarget({ uuid: p.uuid, name: p.name })}
                              className="p-1.5 rounded-lg text-red-400 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:scale-110 active:scale-95 transition-all duration-150"
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
              <div className="block sm:hidden space-y-2">
                {data.data.map((p) => (
                  <div
                    key={p.uuid}
                    className="border border-slate-200 dark:border-slate-700 rounded-lg p-3 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer"
                    onClick={() => navigate(`/patients/${p.uuid}`)}
                  >
                    <div className="flex items-center justify-between">
                      <span className="font-mono text-xs text-slate-400 dark:text-slate-500">{p.medical_record_number}</span>
                      <Badge variant={p.insurance_type === 'bpjs' ? 'info' : p.insurance_type === 'mandiri' ? 'default' : 'purple'}>
                        {p.insurance_type?.toUpperCase() || '-'}
                      </Badge>
                    </div>
                    <p className="font-medium text-slate-900 dark:text-slate-100">{p.name}</p>
                    <div className="flex gap-2 text-xs text-slate-500 dark:text-slate-400">
                      <span>{p.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</span>
                      {p.phone && <span>- {p.phone}</span>}
                    </div>
                    <div className="flex gap-2 pt-1" onClick={(e) => e.stopPropagation()}>
                      <Button size="sm" variant="ghost" onClick={() => navigate(`/patients/${p.uuid}`)} className="flex-1">Detail</Button>
                      <Button size="sm" variant="danger" onClick={() => setDeleteTarget({ uuid: p.uuid, name: p.name })} className="flex-1">Hapus</Button>
                    </div>
                  </div>
                ))}
              </div>

              {renderPagination()}
            </>
          )}
        </CardBody>
      </Card>

      {/* Delete Modal */}
      <Modal isOpen={!!deleteTarget} onClose={() => setDeleteTarget(null)} title="Hapus Pasien" size="sm">
        <p className="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">Yakin ingin menghapus pasien <strong className="text-slate-900 dark:text-slate-100">{deleteTarget?.name}</strong>?</p>
        <div className="flex justify-end gap-2">
          <Button variant="secondary" onClick={() => setDeleteTarget(null)}>Batal</Button>
          <Button variant="danger" loading={deleteMutation.isPending} onClick={handleDelete}>Hapus</Button>
        </div>
      </Modal>

      {/* Import CSV Modal */}
      <Modal isOpen={showImport} onClose={() => { setShowImport(false); setImportResult(null); setFileName(''); if (fileRef.current) fileRef.current.value = '' }} title="Import Pasien dari CSV" size="md">
        <div className="space-y-5">
          <div className="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <FileSpreadsheet className="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
            <div className="flex-1 min-w-0">
              <p className="text-sm font-medium text-blue-900 dark:text-blue-300">Belum punya template?</p>
              <p className="text-xs text-blue-700 dark:text-blue-400 mt-0.5">Gunakan template agar format CSV sesuai</p>
            </div>
            <button onClick={downloadTemplate} className="flex items-center gap-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors flex-shrink-0">
              <Download className="w-4 h-4" />
              Template
            </button>
          </div>

          <div
            className={`relative border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 cursor-pointer ${
              dragOver
                ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500'
                : fileName
                  ? 'border-emerald-300 bg-emerald-50/50 dark:bg-emerald-900/10 dark:border-emerald-600'
                  : 'border-slate-300 dark:border-slate-600 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800/50'
            }`}
            onDragOver={(e) => { e.preventDefault(); setDragOver(true) }}
            onDragLeave={() => setDragOver(false)}
            onDrop={(e) => { e.preventDefault(); setDragOver(false); if (e.dataTransfer.files[0] && fileRef.current) { const dt = new DataTransfer(); dt.items.add(e.dataTransfer.files[0]); fileRef.current.files = dt.files; setFileName(e.dataTransfer.files[0].name) } }}
            onClick={() => fileRef.current?.click()}
          >
            <input ref={fileRef} type="file" accept=".csv,.txt" className="hidden" onChange={(e) => setFileName(e.target.files?.[0]?.name || '')} />
            {fileName ? (
              <div className="flex flex-col items-center gap-2">
                <CheckCircle className="w-8 h-8 text-emerald-500" />
                <div>
                  <p className="text-sm font-medium text-slate-900 dark:text-slate-100">{fileName}</p>
                  <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Klik untuk ganti file</p>
                </div>
              </div>
            ) : (
              <div className="flex flex-col items-center gap-2">
                <Upload className="w-8 h-8 text-slate-400 dark:text-slate-500" />
                <div>
                  <p className="text-sm font-medium text-slate-600 dark:text-slate-300">Seret file CSV ke sini</p>
                  <p className="text-xs text-slate-400 dark:text-slate-500 mt-0.5">atau klik untuk memilih file</p>
                </div>
              </div>
            )}
          </div>

          {importResult && (
            <div className="rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div className="flex items-center gap-2 px-4 py-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                <BarChart3 className="w-4 h-4 text-slate-500 dark:text-slate-400" />
                <span className="text-sm font-medium text-slate-700 dark:text-slate-300">Hasil Import</span>
              </div>
              <div className="p-4 space-y-2">
                <div className="flex items-center gap-2">
                  <CheckCircle className="w-4 h-4 text-emerald-500" />
                  <span className="text-sm text-emerald-700 dark:text-emerald-400">{importResult.imported} pasien berhasil diimport</span>
                </div>
                {importResult.skipped > 0 && (
                  <div className="flex items-center gap-2">
                    <AlertCircle className="w-4 h-4 text-amber-500" />
                    <span className="text-sm text-amber-700 dark:text-amber-400">{importResult.skipped} baris dilewati</span>
                  </div>
                )}
                {importResult.errors.length > 0 && (
                  <div className="mt-2 max-h-32 overflow-y-auto space-y-1">
                    {importResult.errors.map((e, i) => (
                      <p key={i} className="text-xs text-red-600 dark:text-red-400 pl-6">Baris {e.row}: {e.message}</p>
                    ))}
                  </div>
                )}
              </div>
            </div>
          )}

          <div className="flex justify-end gap-2 pt-1">
            <Button variant="secondary" onClick={() => { setShowImport(false); setImportResult(null); setFileName(''); if (fileRef.current) fileRef.current.value = '' }}>Tutup</Button>
            <Button loading={importMutation.isPending} onClick={handleImport} disabled={!fileName}>
              <Upload className="w-4 h-4 mr-1.5" />
              Import
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  )
}
