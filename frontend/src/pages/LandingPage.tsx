import { useState, useEffect, useRef } from 'react'
import { Link } from 'react-router-dom'
import {
  Bone,
  Stethoscope,
  Activity,
  HeartPulse,
  Phone,
  Mail,
  MapPin,
  Clock,
  ChevronDown,
  MessageCircle,
  Sun,
  Moon,
} from 'lucide-react'
import { useTheme } from '../hooks/useTheme'

/* ── Data ─────────────────────────────────────────────────── */

const services = [
  {
    icon: Stethoscope,
    title: 'Konsultasi',
    desc: 'Pemeriksaan dan konsultasi gratis dengan tenaga ahli ortotik-prostetik berpengalaman.',
  },
  {
    icon: Bone,
    title: 'Ortesis',
    desc: 'Alat bantu untuk menopang, memperbaiki, atau mengganti fungsi bagian tubuh yang lemah.',
  },
  {
    icon: Activity,
    title: 'Protesis',
    desc: 'Alat ganti anggota tubuh yang hilang dengan desain custom sesuai kebutuhan pasien.',
  },
  {
    icon: HeartPulse,
    title: 'Terapi & Rehabilitasi',
    desc: 'Program rehabilitasi untuk memaksimalkan fungsi alat bantu dan pemulihan pasien.',
  },
]

const pricing = [
  { item: 'Konsultasi Awal', range: 'Gratis', note: 'Tanpa biaya', free: true },
  { item: 'Ortesis Ringan', range: 'Rp 500.000 – 1.500.000', note: 'Ankle brace, wrist support' },
  { item: 'Ortesis Berat', range: 'Rp 2.000.000 – 5.000.000', note: 'TLSO, KAFO, HKAFO' },
  { item: 'Protesis Bawah Lutut', range: 'Rp 3.000.000 – 8.000.000', note: 'Tergantung material' },
  { item: 'Protesis Atas Lutut', range: 'Rp 8.000.000 – 15.000.000', note: 'Socket custom' },
  { item: 'Terapi Rehabilitasi', range: 'Rp 100.000 – 250.000/sesi', note: '30-60 menit per sesi' },
]

const faqs = [
  {
    q: 'Bagaimana proses pembuatan ortesis atau prostesis?',
    a: 'Dimulai dari konsultasi gratis, pengukuran, pencetakan socket, fitting, hingga penyesuaian akhir. Proses biasanya 1-3 minggu tergantung tingkat kerumitan.',
  },
  {
    q: 'Berapa lama waktu pengerjaan?',
    a: 'Ortesis ringan: 3-5 hari kerja. Protesis: 1-3 minggu. Waktu bisa lebih cepat atau lambat tergantung tingkat kerumitan dan antrean.',
  },
  {
    q: 'Apakah ada garansi?',
    a: 'Ya, kami memberikan garansi perbaikan selama 6 bulan untuk prostesis dan 3 bulan untuk ortesis, termasuk penyesuaian gratis.',
  },
  {
    q: 'Siapa yang bisa berkonsultasi?',
    a: 'Semua orang yang membutuhkan alat bantu gerak — pasien pasca-amputasi, cedera tulang belakang, cerebral palsy, stroke, dan kondisi lainnya.',
  },
  {
    q: 'Apakah melayani kunjungan rumah?',
    a: 'Untuk area Magetan dan sekitarnya, kami bisa melakukan kunjungan rumah untuk pengukuran dan fitting. Silakan hubungi kami untuk jadwal.',
  },
]

const waLink = 'https://wa.me/6281234567890?text=Halo%20Axia%20Orto%2C%20saya%20ingin%20konsultasi'

/* ── Scroll reveal hook ───────────────────────────────────── */

function useReveal() {
  const ref = useRef<HTMLDivElement>(null)
  useEffect(() => {
    const el = ref.current
    if (!el) return
    const obs = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { el.classList.add('revealed'); obs.disconnect() } },
      { threshold: 0.1, rootMargin: '0px 0px -60px 0px' }
    )
    obs.observe(el)
    return () => obs.disconnect()
  }, [])
  return ref
}

function Reveal({ children, className = '', delay = 0 }: { children: React.ReactNode; className?: string; delay?: number }) {
  const ref = useReveal()
  return (
    <div ref={ref} className={`reveal ${className}`} style={{ transitionDelay: `${delay}ms` }}>
      {children}
    </div>
  )
}

/* ── CSS ──────────────────────────────────────────────────── */

const css = `
  .lp { font-family: 'Source Serif 4', Georgia, serif; }
  .lp h1,.lp h2,.lp h3,.lp h4,.lp button,.lp a,.lp th,.lp nav,.lp .ui { font-family: 'DM Sans', system-ui, sans-serif; }

  /* reveal on scroll — CSS-only transition, no JS animation frames */
  .reveal { opacity: 0; transform: translateY(16px); transition: opacity .5s ease, transform .5s ease; }
  .reveal.revealed { opacity: 1; transform: none; }

  /* hero entrance — pure CSS, no runtime JS */
  @keyframes rise { from { opacity:0; transform:translateY(20px) } to { opacity:1; transform:none } }
  .rise { animation: rise .6s ease both; }
  .rise-d1 { animation-delay: .08s }
  .rise-d2 { animation-delay: .16s }
  .rise-d3 { animation-delay: .24s }

  /* service card — GPU-accelerated only */
  .svc { will-change: transform; transition: transform .25s ease }
  .svc:hover { transform: translateY(-4px) }

  /* FAQ grid transition — no JS height calculation */
  .faq-body { display:grid; grid-template-rows:0fr; transition: grid-template-rows .3s ease }
  .faq-body.open { grid-template-rows:1fr }
  .faq-body>div { overflow:hidden }

  /* subtle link underline */
  .lp-link { position:relative; text-decoration:none }
  .lp-link::after { content:''; position:absolute; bottom:-1px; left:0; width:0; height:1.5px; background:#2563eb; transition:width .25s ease }
  .lp-link:hover::after { width:100% }

  /* price row hover — one color, no gradients */
  .pr:hover td:first-child { color:#1e40af }
  .dark .pr:hover td:first-child { color:#93c5fd }

  /* whatsapp icon pulse — minimal, 1 ring */
  @keyframes wa { 0%{box-shadow:0 0 0 0 rgba(34,197,94,.4)} 100%{box-shadow:0 0 0 10px rgba(34,197,94,0)} }
  .wa-glow { animation: wa 2s ease infinite }

  /* focus ring for a11y */
  .lp a:focus-visible,.lp button:focus-visible { outline:2px solid #2563eb; outline-offset:2px; border-radius:4px }

  /* dark mode toggle — inherited from app */
  .dark .lp { color-scheme: dark }
`

/* ── Component ────────────────────────────────────────────── */

export default function LandingPage() {
  const [openFaq, setOpenFaq] = useState<number | null>(null)
  const { theme, toggleTheme } = useTheme()

  return (
    <>
      <style>{css}</style>
      <div className="lp min-h-screen bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200">

        {/* ── Header ──────────────────────────────────────── */}
        <header className="sticky top-0 z-50 bg-white dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800/60">
          <div className="max-w-5xl mx-auto px-5 h-14 flex items-center justify-between">
            <a href="#" className="flex items-center gap-2 ui">
              <Bone className="w-5 h-5 text-blue-600" />
              <span className="text-[0.95rem] font-bold tracking-tight">Axia Orto</span>
            </a>
            <nav className="hidden md:flex items-center gap-7 text-[0.8rem] font-medium text-slate-500 dark:text-slate-400 ui">
              {['Layanan', 'Harga', 'FAQ', 'Kontak'].map((l) => (
                <a key={l} href={`#${l.toLowerCase()}`} className="lp-link hover:text-slate-900 dark:hover:text-slate-100 transition-colors">{l}</a>
              ))}
            </nav>
            <div className="flex items-center gap-1.5 ui">
              <button
                onClick={toggleTheme}
                className="p-2 rounded-md text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                aria-label="Toggle theme"
              >
                {theme === 'dark' ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
              </button>
              <a href={waLink} target="_blank" rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-md bg-blue-600 text-white text-[0.8rem] font-semibold hover:bg-blue-700 transition-colors active:scale-[0.97]">
                <MessageCircle className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Hubungi</span>
              </a>
              <Link to="/login" className="px-3 py-1.5 rounded-md text-[0.8rem] font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                Masuk
              </Link>
            </div>
          </div>
        </header>

        {/* ── Hero ────────────────────────────────────────── */}
        <section className="max-w-5xl mx-auto px-5 pt-16 pb-20 md:pt-24 md:pb-28">
          <div className="max-w-3xl">
            <p className="rise ui text-[0.7rem] font-semibold text-blue-600 tracking-[0.15em] uppercase mb-5">
              Klinik Ortotik & Prostetik &middot; Magetan
            </p>
            <h1 className="rise rise-d1 text-[2.5rem] sm:text-[3.5rem] md:text-[4rem] font-bold leading-[1.05] tracking-tight mb-6 ui">
              Bergerak Lebih Baik,{' '}
              <span className="text-slate-400 dark:text-slate-500">Hidup Lebih Penuh</span>
            </h1>
            <p className="rise rise-d2 text-lg sm:text-xl text-slate-500 dark:text-slate-400 leading-relaxed mb-10 max-w-lg">
              Ortesis dan prostesis berkualitas tinggi. Konsultasi gratis, harga terjangkau, ditangani tenaga ahli berpengalaman.
            </p>
            <div className="rise rise-d3 flex flex-col sm:flex-row items-start sm:items-center gap-3">
              <a href={waLink} target="_blank" rel="noopener noreferrer"
                className="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-semibold hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors active:scale-[0.97] ui">
                <MessageCircle className="w-4 h-4" />
                Konsultasi Gratis
              </a>
              <a href="#layanan" className="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors ui flex items-center gap-1.5">
                Lihat Layanan <span>&rarr;</span>
              </a>
            </div>
          </div>
        </section>

        {/* ── Trust ───────────────────────────────────────── */}
        <section className="border-y border-slate-100 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/30">
          <div className="max-w-5xl mx-auto px-5 py-5 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-[0.8rem] text-slate-500 dark:text-slate-400 ui">
            <span className="font-semibold text-slate-700 dark:text-slate-300">Axia Orto</span>
            <span className="hidden sm:inline text-slate-300 dark:text-slate-600">/</span>
            <span>Garansi 6 Bulan</span>
            <span className="hidden sm:inline text-slate-300 dark:text-slate-600">/</span>
            <span>Konsultasi Gratis</span>
            <span className="hidden sm:inline text-slate-300 dark:text-slate-600">/</span>
            <span>Tim Berpengalaman</span>
            <span className="hidden sm:inline text-slate-300 dark:text-slate-600">/</span>
            <span>Buka Setiap Hari</span>
          </div>
        </section>

        {/* ── Services ────────────────────────────────────── */}
        <section id="layanan" className="max-w-5xl mx-auto px-5 py-20 md:py-24">
          <Reveal>
            <h2 className="text-2xl font-bold tracking-tight mb-2 ui">Layanan</h2>
            <p className="text-slate-500 dark:text-slate-400 text-[0.95rem] mb-10 max-w-md">
              Solusi lengkap untuk kebutuhan ortotik dan prostetik Anda
            </p>
          </Reveal>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {services.map((s, i) => (
              <Reveal key={s.title} delay={i * 60}>
                <article className={`svc group h-full p-5 rounded-lg border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/50 ${
                  i === 0 ? 'sm:col-span-2 lg:col-span-2 sm:row-span-2' : ''
                }`}>
                  <s.icon className={`${i === 0 ? 'w-10 h-10' : 'w-8 h-8'} text-blue-600 mb-4`} strokeWidth={1.5} />
                  <h3 className={`${i === 0 ? 'text-xl' : 'text-[0.95rem]'} font-bold mb-1.5 ui`}>{s.title}</h3>
                  <p className={`${i === 0 ? 'text-[0.9rem]' : 'text-[0.82rem]'} text-slate-500 dark:text-slate-400 leading-relaxed`}>{s.desc}</p>
                </article>
              </Reveal>
            ))}
          </div>
        </section>

        {/* ── Pricing ─────────────────────────────────────── */}
        <section id="harga" className="bg-slate-50/60 dark:bg-slate-900/30 border-y border-slate-100 dark:border-slate-800/60">
          <div className="max-w-5xl mx-auto px-5 py-20 md:py-24">
            <Reveal>
              <h2 className="text-2xl font-bold tracking-tight mb-2 ui">Estimasi Biaya</h2>
              <p className="text-slate-500 dark:text-slate-400 text-[0.95rem] mb-10 max-w-md">
                Harga bervariasi sesuai kebutuhan. Konsultasi awal gratis.
              </p>
            </Reveal>
            <Reveal delay={100}>
              <div className="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 overflow-hidden">
                <table className="w-full text-[0.85rem] ui">
                  <thead>
                    <tr className="border-b border-slate-100 dark:border-slate-800">
                      <th className="text-left py-3 px-4 font-semibold text-slate-400 dark:text-slate-500 text-[0.7rem] uppercase tracking-wider">Layanan</th>
                      <th className="text-left py-3 px-4 font-semibold text-slate-400 dark:text-slate-500 text-[0.7rem] uppercase tracking-wider">Estimasi</th>
                      <th className="text-left py-3 px-4 font-semibold text-slate-400 dark:text-slate-500 text-[0.7rem] uppercase tracking-wider hidden sm:table-cell">Catatan</th>
                    </tr>
                  </thead>
                  <tbody>
                    {pricing.map((p, i) => (
                      <tr key={i} className={`pr border-t transition-colors ${
                        p.free
                          ? 'border-blue-200 dark:border-blue-800/40 bg-blue-50/30 dark:bg-blue-900/5'
                          : 'border-slate-100/80 dark:border-slate-800/50 hover:bg-blue-50/30 dark:hover:bg-blue-900/10'
                      }`}>
                        <td className="py-3 px-4 font-medium transition-colors">
                          {p.item}
                          {p.free && (
                            <span className="ml-2 text-[0.65rem] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Populer</span>
                          )}
                        </td>
                        <td className={`py-3 px-4 font-semibold ${p.free ? 'text-green-600 dark:text-green-400' : 'text-slate-700 dark:text-slate-300'}`}>{p.range}</td>
                        <td className="py-3 px-4 text-slate-400 dark:text-slate-500 hidden sm:table-cell">{p.note}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <p className="text-[0.75rem] text-slate-400 dark:text-slate-500 mt-3 ui">
                * Harga dapat berubah. Hubungi kami untuk estimasi pasti.
              </p>
            </Reveal>
          </div>
        </section>

        {/* ── FAQ ─────────────────────────────────────────── */}
        <section id="faq" className="max-w-5xl mx-auto px-5 py-20 md:py-24">
          <Reveal>
            <h2 className="text-2xl font-bold tracking-tight mb-2 ui">Pertanyaan Umum</h2>
            <p className="text-slate-500 dark:text-slate-400 text-[0.95rem] mb-10 max-w-md">
              Jawaban untuk pertanyaan yang sering diajukan
            </p>
          </Reveal>
          <div className="max-w-xl space-y-2">
            {faqs.map((faq, i) => (
              <Reveal key={i} delay={i * 40}>
                <article className="rounded-lg border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/50 overflow-hidden">
                  <button
                    onClick={() => setOpenFaq(openFaq === i ? null : i)}
                    className="w-full flex items-center justify-between px-4 py-3.5 text-left text-[0.9rem] font-semibold ui hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors"
                  >
                    <span className="pr-4">{faq.q}</span>
                    <ChevronDown className={`w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200 ${openFaq === i ? 'rotate-180' : ''}`} />
                  </button>
                  <div className={`faq-body ${openFaq === i ? 'open' : ''}`}>
                    <div>
                      <div className="px-4 pb-4 pt-0 text-[0.82rem] text-slate-500 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800/50 pt-2.5">
                        {faq.a}
                      </div>
                    </div>
                  </div>
                </article>
              </Reveal>
            ))}
          </div>
        </section>

        {/* ── Contact + Location ──────────────────────────── */}
        <section id="kontak" className="bg-slate-50/60 dark:bg-slate-900/30 border-y border-slate-100 dark:border-slate-800/60">
          <div className="max-w-5xl mx-auto px-5 py-20 md:py-24">
            <Reveal>
              <h2 className="text-2xl font-bold tracking-tight mb-2 ui">Hubungi Kami</h2>
              <p className="text-slate-500 dark:text-slate-400 text-[0.95rem] mb-10">
                WhatsApp untuk respon tercepat.
              </p>
            </Reveal>
            <div className="grid grid-cols-1 lg:grid-cols-5 gap-8">
              {/* Contact */}
              <Reveal className="lg:col-span-2" delay={60}>
                <div className="space-y-3">
                  <a href={waLink} target="_blank" rel="noopener noreferrer"
                    className="flex items-center gap-3.5 p-4 rounded-lg bg-green-50 dark:bg-green-900/15 border border-green-200/60 dark:border-green-800/30 hover:border-green-300 dark:hover:border-green-700 transition-colors group">
                    <div className="wa-glow w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                      <MessageCircle className="w-5 h-5 text-white" />
                    </div>
                    <div className="ui">
                      <p className="text-[0.85rem] font-bold text-green-800 dark:text-green-200">WhatsApp</p>
                      <p className="text-[0.8rem] text-green-600/80 dark:text-green-400/80">0812-3456-7890</p>
                    </div>
                  </a>
                  {[
                    { icon: Phone, l: 'Telepon', v: '(0351) 123-4567' },
                    { icon: Mail, l: 'Email', v: 'info@axiaorto.id' },
                    { icon: Clock, l: 'Jam Buka', v: 'Sen–Sab 08.00–16.00 · Minggu 09.00–13.00' },
                  ].map((c, i) => (
                    <div key={i} className="flex items-center gap-3 p-3.5 rounded-lg border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                      <c.icon className="w-4 h-4 text-slate-400 dark:text-slate-500 flex-shrink-0" />
                      <div className="ui">
                        <p className="text-[0.7rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{c.l}</p>
                        <p className="text-[0.82rem] text-slate-700 dark:text-slate-300">{c.v}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </Reveal>

              {/* Location */}
              <Reveal className="lg:col-span-3" delay={120}>
                <div className="space-y-3">
                  <div className="flex items-start gap-3 p-4 rounded-lg border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                    <MapPin className="w-4 h-4 text-slate-400 dark:text-slate-500 flex-shrink-0 mt-0.5" />
                    <div className="ui">
                      <p className="text-[0.7rem] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Alamat</p>
                      <p className="text-[0.82rem] text-slate-700 dark:text-slate-300 leading-relaxed">
                        Jl. Raya Solo-Magetan No. 123, Magetan, Jawa Timur 63312
                      </p>
                    </div>
                  </div>
                  <div className="rounded-lg overflow-hidden border border-slate-200/80 dark:border-slate-800 h-64">
                    <iframe
                      title="Lokasi Axia Orto"
                      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.56458910193!2d111.3!3d-7.6!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e79bf5%3A0x5030bfbca8302f0!2sMagetan%2C%20Magetan%20Regency%2C%20East%20Java!5e0!3m2!1sid!2sid!4v1"
                      width="100%" height="100%" style={{ border: 0 }} allowFullScreen loading="lazy"
                      referrerPolicy="no-referrer-when-downgrade"
                    />
                  </div>
                </div>
              </Reveal>
            </div>
          </div>
        </section>

        {/* ── Footer ──────────────────────────────────────── */}
        <footer className="border-t border-slate-100 dark:border-slate-800/60">
          <div className="max-w-5xl mx-auto px-5 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-4 text-[0.78rem] text-slate-400 dark:text-slate-500 ui">
              <span className="flex items-center gap-1.5 font-semibold text-slate-500 dark:text-slate-400">
                <Bone className="w-3.5 h-3.5 text-blue-600" />
                Axia Orto
              </span>
              <span>&copy; {new Date().getFullYear()}</span>
              <span className="hidden sm:inline">&middot;</span>
              <span className="hidden sm:inline">Magetan, Jawa Timur</span>
            </div>
            <div className="flex items-center gap-5 text-[0.78rem] text-slate-400 dark:text-slate-500 ui">
              {['Layanan', 'Harga', 'FAQ'].map((l) => (
                <a key={l} href={`#${l.toLowerCase()}`} className="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">{l}</a>
              ))}
              <Link to="/login" className="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Admin</Link>
            </div>
          </div>
        </footer>
      </div>
    </>
  )
}
