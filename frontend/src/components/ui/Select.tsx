import { type SelectHTMLAttributes, forwardRef } from 'react'

interface SelectOption {
  value: string | number
  label: string
  disabled?: boolean
}

interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
  label?: string
  error?: string
  helperText?: string
  options: SelectOption[]
  placeholder?: string
}

export const Select = forwardRef<HTMLSelectElement, SelectProps>(
  ({ label, error, helperText, options, placeholder, className = '', ...props }, ref) => (
    <div className="space-y-1">
      {label && (
        <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
          {label}
          {props.required && <span className="text-red-500 ml-1">*</span>}
        </label>
      )}
      <select
        ref={ref}
        className={`block w-full rounded-lg border px-3 py-2 text-sm transition-colors bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100
          focus:outline-none focus:ring-2 focus:ring-offset-0
          ${error
            ? 'border-red-300 dark:border-red-700 focus:ring-red-500 focus:border-red-500'
            : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500 focus:border-blue-500'
          } ${className}`}
        {...props}
      >
        {placeholder && (
          <option value="" disabled>
            {placeholder}
          </option>
        )}
        {options.map((opt) => (
          <option key={opt.value} value={opt.value} disabled={opt.disabled}>
            {opt.label}
          </option>
        ))}
      </select>
      {error && <p className="text-sm text-red-600 dark:text-red-400">{error}</p>}
      {helperText && !error && <p className="text-sm text-slate-500 dark:text-slate-400">{helperText}</p>}
    </div>
  )
)
