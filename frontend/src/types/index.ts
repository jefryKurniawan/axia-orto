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
