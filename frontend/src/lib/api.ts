const BASE_URL = '/api'

function getCsrfToken(): string {
  const meta = document.querySelector('meta[name="csrf-token"]')
  return meta?.getAttribute('content') ?? ''
}

async function request<T>(url: string, options: RequestInit = {}): Promise<T> {
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': getCsrfToken(),
    ...(options.headers as Record<string, string> ?? {}),
  }

  const res = await fetch(`${BASE_URL}${url}`, {
    ...options,
    headers,
    credentials: 'same-origin',
  })

  const data = await res.json()

  if (!res.ok) {
    const err = new Error(data.message || `HTTP ${res.status}`) as Error & { errors?: Record<string, string[]>; status?: number }
    err.errors = data.errors
    err.status = res.status
    throw err
  }

  return data as T
}

export const api = {
  get: <T>(url: string) =>
    request<ApiResponse<T>>(url),

  getPaginated: <T>(url: string) =>
    request<PaginatedResponse<T>>(url),

  post: <T>(url: string, body?: unknown) =>
    request<ApiResponse<T>>(url, {
      method: 'POST',
      body: body ? JSON.stringify(body) : undefined,
    }),

  put: <T>(url: string, body?: unknown) =>
    request<ApiResponse<T>>(url, {
      method: 'PUT',
      body: body ? JSON.stringify(body) : undefined,
    }),

  delete: <T>(url: string) =>
    request<ApiResponse<T>>(url, { method: 'DELETE' }),
}

interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
}

interface PaginatedResponse<T> {
  success: boolean
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}
