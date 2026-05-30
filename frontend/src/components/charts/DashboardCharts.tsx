import {
  AreaChart, Area, BarChart, Bar, PieChart, Pie, Cell,
  XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend,
} from 'recharts'

const COLORS = {
  blue: '#3b82f6',
  green: '#22c55e',
  yellow: '#eab308',
  red: '#ef4444',
  purple: '#a855f7',
  orange: '#f97316',
  cyan: '#06b6d4',
  slate: '#94a3b8',
}

const STATUS_COLORS: Record<string, string> = {
  draft: COLORS.slate,
  confirmed: COLORS.blue,
  production: COLORS.yellow,
  ready: COLORS.cyan,
  delivered: COLORS.green,
  cancelled: COLORS.red,
  pending: COLORS.slate,
  in_progress: COLORS.blue,
  completed: COLORS.green,
}

const STEP_LABELS: Record<string, string> = {
  measurement: 'Pengukuran',
  design: 'Desain',
  fabrication: 'Fabrikasi',
  assembly: 'Perakitan',
  finishing: 'Finishing',
  quality_check: 'QC',
  delivery: 'Pengiriman',
}

function formatCurrency(value: number): string {
  if (value >= 1_000_000_000) return `${(value / 1_000_000_000).toFixed(1)}M`
  if (value >= 1_000_000) return `${(value / 1_000_000).toFixed(1)}jt`
  if (value >= 1_000) return `${(value / 1_000).toFixed(0)}rb`
  return value.toString()
}

function formatDate(dateStr: string): string {
  const d = new Date(dateStr)
  return `${d.getDate()}/${d.getMonth() + 1}`
}

interface ChartTooltipProps {
  active?: boolean
  payload?: Array<{ name: string; value: number; color: string }>
  label?: string
  isCurrency?: boolean
}

function ChartTooltip({ active, payload, label, isCurrency }: ChartTooltipProps) {
  if (!active || !payload?.length) return null
  return (
    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg px-3 py-2 text-sm">
      <p className="font-medium text-slate-900 dark:text-slate-100 mb-1">{label}</p>
      {payload.map((entry, i) => (
        <p key={i} className="text-slate-600 dark:text-slate-400">
          <span className="inline-block w-2.5 h-2.5 rounded-full mr-1.5" style={{ backgroundColor: entry.color }} />
          {entry.name}: <span className="font-medium text-slate-900 dark:text-slate-100">
            {isCurrency ? `Rp ${formatCurrency(entry.value)}` : entry.value}
          </span>
        </p>
      ))}
    </div>
  )
}

// --- Revenue Trend ---
export function RevenueTrendChart({ data }: { data: { date: string; total_revenue: number }[] }) {
  if (!data.length) {
    return (
      <div className="flex items-center justify-center h-48 text-sm text-slate-400 dark:text-slate-500">
        Belum ada data pendapatan
      </div>
    )
  }

  const chartData = data.map(d => ({ ...d, date: formatDate(d.date) }))

  return (
    <ResponsiveContainer width="100%" height={220}>
      <AreaChart data={chartData} margin={{ top: 5, right: 5, left: 0, bottom: 0 }}>
        <defs>
          <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="5%" stopColor={COLORS.green} stopOpacity={0.3} />
            <stop offset="95%" stopColor={COLORS.green} stopOpacity={0} />
          </linearGradient>
        </defs>
        <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" className="dark:stroke-slate-700" />
        <XAxis dataKey="date" tick={{ fontSize: 11 }} stroke="#94a3b8" />
        <YAxis tickFormatter={formatCurrency} tick={{ fontSize: 11 }} stroke="#94a3b8" width={50} />
        <Tooltip content={<ChartTooltip isCurrency />} />
        <Area
          type="monotone"
          dataKey="total_revenue"
          name="Pendapatan"
          stroke={COLORS.green}
          strokeWidth={2}
          fill="url(#revenueGradient)"
        />
      </AreaChart>
    </ResponsiveContainer>
  )
}

// --- Consultation Trend ---
export function ConsultationTrendChart({ data }: { data: { date: string; total: number; completed: number; cancelled: number }[] }) {
  if (!data.length) {
    return (
      <div className="flex items-center justify-center h-48 text-sm text-slate-400 dark:text-slate-500">
        Belum ada data konsultasi
      </div>
    )
  }

  const chartData = data.map(d => ({ ...d, date: formatDate(d.date) }))

  return (
    <ResponsiveContainer width="100%" height={220}>
      <BarChart data={chartData} margin={{ top: 5, right: 5, left: 0, bottom: 0 }}>
        <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" className="dark:stroke-slate-700" />
        <XAxis dataKey="date" tick={{ fontSize: 11 }} stroke="#94a3b8" />
        <YAxis allowDecimals={false} tick={{ fontSize: 11 }} stroke="#94a3b8" width={30} />
        <Tooltip content={<ChartTooltip />} />
        <Legend
          wrapperStyle={{ fontSize: 12 }}
          formatter={(value: string) => {
            const labels: Record<string, string> = { total: 'Total', completed: 'Selesai', cancelled: 'Batal' }
            return labels[value] || value
          }}
        />
        <Bar dataKey="total" name="total" fill={COLORS.blue} radius={[3, 3, 0, 0]} />
        <Bar dataKey="completed" name="completed" fill={COLORS.green} radius={[3, 3, 0, 0]} />
        <Bar dataKey="cancelled" name="cancelled" fill={COLORS.red} radius={[3, 3, 0, 0]} />
      </BarChart>
    </ResponsiveContainer>
  )
}

// --- Order Status Distribution ---
export function OrderStatusChart({ data }: { data: { status: string; count: number }[] }) {
  if (!data.length) {
    return (
      <div className="flex items-center justify-center h-48 text-sm text-slate-400 dark:text-slate-500">
        Belum ada data order
      </div>
    )
  }

  const STATUS_LABELS: Record<string, string> = {
    draft: 'Draft',
    confirmed: 'Dikonfirmasi',
    production: 'Produksi',
    ready: 'Siap',
    delivered: 'Dikirim',
    cancelled: 'Dibatalkan',
  }

  const chartData = data.map(d => ({
    ...d,
    name: STATUS_LABELS[d.status] || d.status,
  }))

  return (
    <ResponsiveContainer width="100%" height={220}>
      <PieChart>
        <Pie
          data={chartData}
          cx="50%"
          cy="50%"
          innerRadius={50}
          outerRadius={80}
          paddingAngle={2}
          dataKey="count"
          nameKey="name"
          stroke="none"
        >
          {chartData.map((entry) => (
            <Cell key={entry.status} fill={STATUS_COLORS[entry.status] || COLORS.slate} />
          ))}
        </Pie>
        <Tooltip
          // eslint-disable-next-line @typescript-eslint/no-explicit-any
          content={({ active, payload }: any) => {
            if (!active || !payload?.length) return null
            const d = payload[0].payload
            return (
              <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg px-3 py-2 text-sm">
                <p className="font-medium text-slate-900 dark:text-slate-100">{d.name}</p>
                <p className="text-slate-600 dark:text-slate-400">{d.count} order</p>
              </div>
            )
          }}
        />
        <Legend
          wrapperStyle={{ fontSize: 12 }}
          formatter={(value: string) => <span className="text-slate-700 dark:text-slate-300">{value}</span>}
        />
      </PieChart>
    </ResponsiveContainer>
  )
}

// --- Production Pipeline ---
export function ProductionPipelineChart({ data }: { data: { step: string; status: string; count: number }[] }) {
  if (!data.length) {
    return (
      <div className="flex items-center justify-center h-48 text-sm text-slate-400 dark:text-slate-500">
        Belum ada data produksi
      </div>
    )
  }

  // Group by step
  const steps = [...new Set(data.map(d => d.step))]
  const chartData = steps.map(step => {
    const row: Record<string, string | number> = { step: STEP_LABELS[step] || step }
    data.filter(d => d.step === step).forEach(d => {
      row[d.status] = d.count
    })
    return row
  })

  return (
    <ResponsiveContainer width="100%" height={220}>
      <BarChart data={chartData} margin={{ top: 5, right: 5, left: 0, bottom: 0 }}>
        <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" className="dark:stroke-slate-700" />
        <XAxis dataKey="step" tick={{ fontSize: 11 }} stroke="#94a3b8" />
        <YAxis allowDecimals={false} tick={{ fontSize: 11 }} stroke="#94a3b8" width={30} />
        <Tooltip content={<ChartTooltip />} />
        <Legend
          wrapperStyle={{ fontSize: 12 }}
          formatter={(value: string) => {
            const labels: Record<string, string> = { pending: 'Menunggu', in_progress: 'Proses', completed: 'Selesai' }
            return labels[value] || value
          }}
        />
        <Bar dataKey="pending" name="pending" stackId="a" fill={COLORS.slate} radius={[0, 0, 0, 0]} />
        <Bar dataKey="in_progress" name="in_progress" stackId="a" fill={COLORS.blue} radius={[0, 0, 0, 0]} />
        <Bar dataKey="completed" name="completed" stackId="a" fill={COLORS.green} radius={[3, 3, 0, 0]} />
      </BarChart>
    </ResponsiveContainer>
  )
}
