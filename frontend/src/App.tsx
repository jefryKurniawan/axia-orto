import { Routes, Route } from 'react-router-dom'

function App() {
  return (
    <Routes>
      <Route path="/" element={<div className="min-h-screen flex items-center justify-center bg-slate-50"><h1 className="text-3xl font-bold text-slate-900">AxiaOrto Clinic ERP</h1></div>} />
      <Route path="/login" element={<div className="min-h-screen flex items-center justify-center bg-slate-50"><h1 className="text-2xl font-bold text-slate-900">Login</h1></div>} />
      <Route path="/dashboard" element={<div className="min-h-screen flex items-center justify-center bg-slate-50"><h1 className="text-2xl font-bold text-slate-900">Dashboard</h1></div>} />
      <Route path="*" element={<div className="min-h-screen flex items-center justify-center bg-slate-50"><h1 className="text-2xl font-bold text-red-600">404 - Not Found</h1></div>} />
    </Routes>
  )
}

export default App
