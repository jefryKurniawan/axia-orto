import { useState, useEffect, useRef } from 'react'

export function useCountUp(target: number, duration = 1200) {
  const [count, setCount] = useState(0)
  const prevTarget = useRef(0)

  useEffect(() => {
    const start = prevTarget.current
    prevTarget.current = target
    if (target === start) { setCount(target); return }

    const startTime = performance.now()

    function step(now: number) {
      const elapsed = now - startTime
      const progress = Math.min(elapsed / duration, 1)
      // easeOutExpo — cepat di awal, lambat di akhir
      const eased = 1 - Math.pow(2, -10 * progress)
      setCount(Math.round(start + (target - start) * eased))
      if (progress < 1) requestAnimationFrame(step)
    }

    requestAnimationFrame(step)
  }, [target, duration])

  return count
}
