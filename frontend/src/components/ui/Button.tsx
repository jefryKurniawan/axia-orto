import { type ButtonHTMLAttributes, forwardRef } from 'react'
import { Loader2 } from 'lucide-react'

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'subtle' | 'icon'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
}

const variants = {
  primary: 'bg-blue-600 text-white hover:bg-blue-700 hover:-translate-y-px focus:ring-blue-500 shadow-sm',
  secondary: 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 focus:ring-slate-500',
  danger: 'bg-red-600 text-white hover:bg-red-700 hover:-translate-y-px focus:ring-red-500 shadow-sm',
  ghost: 'bg-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-100 focus:ring-slate-500',
  subtle: 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-800 dark:hover:text-slate-200 focus:ring-slate-500',
  icon: 'bg-transparent text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 focus:ring-slate-500 p-2',
}

const sizes = {
  sm: 'px-3 py-1.5 text-xs',
  md: 'px-4 py-2 text-sm',
  lg: 'px-5 py-2.5 text-sm',
}

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  ({ variant = 'primary', size = 'md', loading, disabled, children, className = '', ...props }, ref) => {
    const isIcon = variant === 'icon'
    return (
      <button
        ref={ref}
        type="button"
        disabled={disabled || loading}
        className={`inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none ${variants[variant]} ${isIcon ? '' : sizes[size]} ${className}`}
        {...props}
      >
        {loading && <Loader2 className="animate-spin -ml-1 mr-2 h-4 w-4" />}
        {children}
      </button>
    )
  }
)
