import { lazy, Suspense, useEffect } from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './contexts/AuthContext'
import { AppLayout } from './components/layout/AppLayout'
import { ToastContainer } from './components/ui/Toast'
import { useAppStore } from './stores/appStore'

const Login = lazy(() => import('./pages/Login'))
const Dashboard = lazy(() => import('./pages/Dashboard'))
const PatientList = lazy(() => import('./pages/patients/PatientList'))
const PatientDetail = lazy(() => import('./pages/patients/PatientDetail'))
const PatientForm = lazy(() => import('./pages/patients/PatientForm'))
const ConsultationList = lazy(() => import('./pages/consultations/ConsultationList'))
const ConsultationDetail = lazy(() => import('./pages/consultations/ConsultationDetail'))
const ConsultationForm = lazy(() => import('./pages/consultations/ConsultationForm'))
const ServiceList = lazy(() => import('./pages/services/ServiceList'))
const ServiceForm = lazy(() => import('./pages/services/ServiceForm'))
const Register = lazy(() => import('./pages/Register'))
const OrderList = lazy(() => import('./pages/orders/OrderList'))
const OrderForm = lazy(() => import('./pages/orders/OrderForm'))
const OrderDetail = lazy(() => import('./pages/orders/OrderDetail'))
const PaymentList = lazy(() => import('./pages/payments/PaymentList'))
const PaymentForm = lazy(() => import('./pages/payments/PaymentForm'))
const ProductionList = lazy(() => import('./pages/production/ProductionList'))
const ProductionDetail = lazy(() => import('./pages/production/ProductionDetail'))
const ProductionForm = lazy(() => import('./pages/production/ProductionForm'))
const ReportsPage = lazy(() => import('./pages/reports/ReportsPage'))
const InventoryList = lazy(() => import('./pages/inventory/InventoryList'))
const InventoryForm = lazy(() => import('./pages/inventory/InventoryForm'))
const InventoryDetail = lazy(() => import('./pages/inventory/InventoryDetail'))
const AuditLogList = lazy(() => import('./pages/audit/AuditLogList'))

function Loading() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950">
      <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600" />
    </div>
  )
}

export default function App() {
  const theme = useAppStore((s) => s.theme)

  useEffect(() => {
    document.documentElement.classList.toggle('dark', theme === 'dark')
  }, [theme])

  return (
    <AuthProvider>
      <ToastContainer />
      <Suspense fallback={<Loading />}>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route element={<AppLayout />}>
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route path="/dashboard" element={<Dashboard />} />
            <Route path="/patients" element={<PatientList />} />
            <Route path="/patients/create" element={<PatientForm />} />
            <Route path="/patients/:uuid" element={<PatientDetail />} />
            <Route path="/patients/:uuid/edit" element={<PatientForm />} />
            <Route path="/consultations" element={<ConsultationList />} />
            <Route path="/consultations/create" element={<ConsultationForm />} />
            <Route path="/consultations/:uuid" element={<ConsultationDetail />} />
            <Route path="/consultations/:uuid/edit" element={<ConsultationForm />} />
            <Route path="/services" element={<ServiceList />} />
            <Route path="/services/create" element={<ServiceForm />} />
            <Route path="/services/:uuid/edit" element={<ServiceForm />} />
            <Route path="/orders" element={<OrderList />} />
            <Route path="/orders/create" element={<OrderForm />} />
            <Route path="/orders/:uuid" element={<OrderDetail />} />
            <Route path="/orders/:uuid/edit" element={<OrderForm />} />
            <Route path="/payments" element={<PaymentList />} />
            <Route path="/payments/create" element={<PaymentForm />} />
            <Route path="/payments/:uuid" element={<PaymentForm />} />
            <Route path="/production" element={<ProductionList />} />
            <Route path="/production/create" element={<ProductionForm />} />
            <Route path="/production/:uuid" element={<ProductionDetail />} />
            <Route path="/production/:uuid/edit" element={<ProductionForm />} />
            <Route path="/reports" element={<ReportsPage />} />
            <Route path="/inventory" element={<InventoryList />} />
            <Route path="/inventory/create" element={<InventoryForm />} />
            <Route path="/inventory/:uuid" element={<InventoryDetail />} />
            <Route path="/inventory/:uuid/edit" element={<InventoryForm />} />
            <Route path="/audit-logs" element={<AuditLogList />} />
            <Route path="/register" element={<Register />} />
          </Route>
          <Route path="*" element={<Navigate to="/dashboard" replace />} />
        </Routes>
      </Suspense>
    </AuthProvider>
  )
}
