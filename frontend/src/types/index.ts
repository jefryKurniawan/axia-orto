// User
export interface User {
  id: number
  uuid: string
  name: string
  email: string
  role: 'admin' | 'dokter' | 'staf_klinik' | 'teknisi'
  specialization?: string
  phone?: string
  is_active: boolean
}

// Patient
export interface Patient {
  id: number
  uuid: string
  medical_record_number: string
  nik?: string
  name: string
  date_of_birth: string
  gender: 'L' | 'P'
  phone?: string
  address?: string
  emergency_contact?: string
  insurance_type: 'bpjs' | 'mandiri' | 'asuransi'
  blood_type?: 'A' | 'B' | 'AB' | 'O'
  allergies?: string[]
  created_at: string
  updated_at: string
}

// Consultation
export interface Consultation {
  id: number
  uuid: string
  patient_id: number
  doctor_id: number
  consultation_date: string
  complaint: string
  diagnosis?: string
  treatment_plan?: string
  notes?: string
  follow_up_date?: string
  status: 'scheduled' | 'in_progress' | 'completed' | 'cancelled'
  patient_name?: string
  medical_record_number?: string
  doctor_name?: string
  created_at: string
  updated_at: string
}

// Service
export interface Service {
  id: number
  uuid: string
  code: string
  name: string
  description?: string
  service_type: 'konsultasi' | 'ortosis' | 'protesis' | 'terapi' | 'alat'
  price: number
  duration_days?: number
  is_active: boolean
}

// Order Item
export interface OrderItem {
  id: number
  treatment_order_id: number
  service_id: number
  quantity: number
  unit_price: number
  total_price: number
  specifications?: Record<string, unknown>
  service_name?: string
  service_code?: string
}

// Treatment Order
export interface TreatmentOrder {
  id: number
  uuid: string
  order_number: string
  patient_id: number
  consultation_id?: number
  order_date: string
  delivery_date?: string
  status: 'draft' | 'confirmed' | 'production' | 'ready' | 'delivered' | 'cancelled'
  total_amount: number
  notes?: string
  created_by: number
  patient_name?: string
  medical_record_number?: string
  created_by_name?: string
  patient?: Patient
  consultation?: Consultation
  order_items?: OrderItem[]
  payments?: Payment[]
  production_trackings?: ProductionTracking[]
  created_at: string
  updated_at: string
}

// Payment
export interface Payment {
  id: number
  uuid: string
  treatment_order_id: number
  payment_number: string
  payment_date: string
  payment_method: 'cash' | 'transfer' | 'debit_card' | 'credit_card'
  amount: number
  status: 'pending' | 'completed' | 'failed' | 'refunded'
  notes?: string
  created_by?: number
  order_number?: string
  patient_name?: string
  order?: TreatmentOrder
  created_at: string
  updated_at: string
}

// Production Tracking
export interface ProductionTracking {
  id: number
  uuid: string
  treatment_order_id: number
  step: string
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled'
  notes?: string
  assigned_to: number
  completed_by?: number
  started_at?: string
  completed_at?: string
  order_number?: string
  order_uuid?: string
  patient_name?: string
  assigned_to_name?: string
  order?: TreatmentOrder
  created_at: string
  updated_at: string
}

// Invoice
export interface Invoice {
  id: number
  uuid: string
  invoice_number: string
  treatment_order_id: number
  invoice_date: string
  due_date: string
  subtotal: number
  tax_amount: number
  discount_amount: number
  total_amount: number
  status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled'
  notes?: string
  created_by?: number
  order?: TreatmentOrder
  created_at: string
  updated_at: string
}

// Export Job
export interface ExportJob {
  id: number
  uuid: string
  requested_by: number
  report_type: 'revenue' | 'patients' | 'orders' | 'payments'
  parameters?: { date_from?: string; date_to?: string }
  status: 'pending' | 'processing' | 'completed' | 'failed'
  file_path?: string
  error_message?: string
  started_at?: string
  completed_at?: string
  created_at: string
}

// Daily Revenue Summary
export interface DailyRevenueSummary {
  date: string
  total_revenue: number
  cash_revenue: number
  transfer_revenue: number
  card_revenue: number
  total_transactions: number
  completed_transactions: number
  pending_transactions: number
}

// Dashboard
export interface DashboardStats {
  today: {
    total: number
    scheduled: number
    in_progress: number
    completed: number
    cancelled: number
  }
  total_patients: number
  active_doctors: number
  new_patients_month: number
  recent_consultations: Consultation[]
  low_stock_count: number
  low_stock_items: { uuid: string; name: string; code: string; quantity: number; reorder_level: number; unit: string }[]
}

// Inventory Item
export interface InventoryItem {
  id: number
  uuid: string
  code: string
  name: string
  description?: string
  category: 'bahan_baku' | 'komponen' | 'alat_jadi'
  quantity: number
  unit: string
  price: number
  reorder_level: number
  is_active: boolean
  created_at: string
  updated_at: string
}

// Inventory Transaction
export interface InventoryTransaction {
  id: number
  uuid: string
  inventory_item_id: number
  type: 'masuk' | 'keluar' | 'adjustment'
  quantity: number
  reference_type?: string
  reference_id?: number
  notes?: string
  created_by?: number
  created_by_name?: string
  item_name?: string
  created_at: string
}

// Inventory Stats
export interface InventoryStats {
  total_items: number
  active_items: number
  low_stock_count: number
  total_inventory_value: number
  by_category: Record<string, number>
}

// Audit Log
export interface AuditLog {
  id: number
  user_id?: number
  user_name?: string
  auditable_type: string
  auditable_id: number
  event: 'created' | 'updated' | 'deleted'
  old_values?: Record<string, unknown>
  new_values?: Record<string, unknown>
  ip_address?: string
  created_at: string
}

// API Response
export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
}

export interface PaginatedResponse<T> {
  success: boolean
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}
