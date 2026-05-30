import { type InputHTMLAttributes, forwardRef } from 'react'
import { Search, X } from 'lucide-react'

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  label?: string
  error?: string
  helperText?: string
  search?: boolean
  onClear?: () => void
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ label, error, helperText, search, onClear, className = '', value, ...props }, ref) => (
    <div className="space-y-1.5">
      {label && (
        <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">
          {label}
          {props.required && <span className="text-red-500 ml-1">*</span>}
        </label>
      )}
      <div className="relative">
        {search && (
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500 pointer-events-none" />
        )}
        <input
          ref={ref}
          value={value}
          className={`block w-full rounded-lg border text-sm transition-all duration-150 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500
            focus:outline-none focus:ring-2 focus:ring-offset-0
            ${search ? 'pl-9 pr-8' : 'px-3'} py-2
            ${error
              ? 'border-red-300 dark:border-red-700 focus:ring-red-500 focus:border-red-500'
              : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500'
            } ${className}`}
          {...props}
        />
        {search && value && onClear && (
          <button
            type="button"
            onClick={onClear}
            className="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
          >
            <X className="w-3.5 h-3.5" />
          </button>
        )}
      </div>
      {error && <p className="text-xs text-red-600 dark:text-red-400">{error}</p>}
      {helperText && !error && <p className="text-xs text-slate-400 dark:text-slate-500">{helperText}</p>}
    </div>
  )
)
