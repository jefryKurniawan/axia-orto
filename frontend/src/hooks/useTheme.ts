import { useEffect } from 'react'
import { useAppStore } from '../stores/appStore'

const LIGHT_BG = '#f8fafc'
const DARK_BG = '#0f172a'

export function useTheme() {
  const theme = useAppStore((s) => s.theme)
  const setTheme = useAppStore((s) => s.setTheme)

  useEffect(() => {
    const root = document.documentElement
    if (theme === 'dark') {
      root.classList.add('dark')
    } else {
      root.classList.remove('dark')
    }
  }, [theme])

  const toggleTheme = () => {
    const btn = document.querySelector('[aria-label="Toggle theme"]')
    const rect = btn?.getBoundingClientRect()
    const x = rect ? rect.left + rect.width / 2 : window.innerWidth
    const y = rect ? rect.top + rect.height / 2 : 0

    const newTheme = theme === 'dark' ? 'light' : 'dark'
    const newColor = newTheme === 'dark' ? DARK_BG : LIGHT_BG

    // 1. Create ripple circle at button position
    const size = Math.ceil(Math.hypot(window.innerWidth, window.innerHeight)) * 2
    const ripple = document.createElement('div')
    ripple.style.position = 'fixed'
    ripple.style.zIndex = '99999'
    ripple.style.pointerEvents = 'none'
    ripple.style.width = size + 'px'
    ripple.style.height = size + 'px'
    ripple.style.left = (x - size / 2) + 'px'
    ripple.style.top = (y - size / 2) + 'px'
    ripple.style.borderRadius = '50%'
    ripple.style.background = newColor
    ripple.style.transform = 'scale(0)'
    ripple.style.opacity = '1'
    document.body.appendChild(ripple)

    // 2. Force reflow, then animate
    void ripple.offsetWidth
    ripple.style.transition = 'transform 0.5s cubic-bezier(0.22, 0.61, 0.36, 1), opacity 0.5s ease-out 0.3s'
    ripple.style.transform = 'scale(1)'
    ripple.style.opacity = '0'

    // 3. Switch theme halfway
    setTimeout(() => setTheme(newTheme), 150)

    // 4. Remove after transition (guaranteed)
    setTimeout(() => { ripple.remove() }, 900)
  }

  return { theme, setTheme, toggleTheme }
}
