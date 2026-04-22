import { useRef, useCallback } from 'react'

const COLORS = [
  '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
  '#DDA0DD', '#FF9FF3', '#54A0FF', '#5F27CD', '#01a3a4',
  '#f368e0', '#ff9f43', '#ee5a24', '#0abde3', '#10ac84',
  '#FFC312', '#C4E538', '#12CBC4', '#FDA7DF', '#ED4C67',
]

function createParticle(canvas) {
  return {
    x: canvas.width / 2 + (Math.random() - 0.5) * 200,
    y: canvas.height / 2 - 100,
    vx: (Math.random() - 0.5) * 18,
    vy: -(Math.random() * 14 + 4),
    color: COLORS[Math.floor(Math.random() * COLORS.length)],
    size: Math.random() * 10 + 4,
    rotation: Math.random() * 360,
    rotationSpeed: (Math.random() - 0.5) * 12,
    gravity: 0.25 + Math.random() * 0.15,
    drag: 0.98 + Math.random() * 0.015,
    opacity: 1,
    shape: Math.floor(Math.random() * 3), // 0=rect, 1=circle, 2=triangle
    wobble: Math.random() * 10,
    wobbleSpeed: Math.random() * 0.1 + 0.03,
  }
}

export function useConfetti() {
  const canvasRef = useRef(null)
  const animRef = useRef(null)

  const launch = useCallback((durationMs = 6000) => {
    const canvas = canvasRef.current
    if (!canvas) return
    const ctx = canvas.getContext('2d')
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight

    let particles = []
    const startTime = performance.now()
    const emitEnd = startTime + 1500

    function emit() {
      for (let i = 0; i < 12; i++) {
        particles.push(createParticle(canvas))
      }
    }

    function drawParticle(p) {
      ctx.save()
      ctx.translate(p.x, p.y)
      ctx.rotate((p.rotation * Math.PI) / 180)
      ctx.globalAlpha = p.opacity
      ctx.fillStyle = p.color

      if (p.shape === 0) {
        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6)
      } else if (p.shape === 1) {
        ctx.beginPath()
        ctx.arc(0, 0, p.size / 2, 0, Math.PI * 2)
        ctx.fill()
      } else {
        ctx.beginPath()
        ctx.moveTo(0, -p.size / 2)
        ctx.lineTo(p.size / 2, p.size / 2)
        ctx.lineTo(-p.size / 2, p.size / 2)
        ctx.closePath()
        ctx.fill()
      }
      ctx.restore()
    }

    function animate(now) {
      const elapsed = now - startTime
      ctx.clearRect(0, 0, canvas.width, canvas.height)

      if (now < emitEnd) emit()

      particles = particles.filter(p => {
        p.vy += p.gravity
        p.vx *= p.drag
        p.x += p.vx + Math.sin(p.wobble) * 1.5
        p.y += p.vy
        p.rotation += p.rotationSpeed
        p.wobble += p.wobbleSpeed

        if (elapsed > durationMs - 1500) {
          p.opacity -= 0.02
        }

        drawParticle(p)
        return p.opacity > 0 && p.y < canvas.height + 50
      })

      if (elapsed < durationMs && particles.length > 0) {
        animRef.current = requestAnimationFrame(animate)
      } else {
        ctx.clearRect(0, 0, canvas.width, canvas.height)
      }
    }

    if (animRef.current) cancelAnimationFrame(animRef.current)
    animRef.current = requestAnimationFrame(animate)
  }, [])

  const stop = useCallback(() => {
    if (animRef.current) {
      cancelAnimationFrame(animRef.current)
      animRef.current = null
    }
    if (canvasRef.current) {
      const ctx = canvasRef.current.getContext('2d')
      ctx.clearRect(0, 0, canvasRef.current.width, canvasRef.current.height)
    }
  }, [])

  return { canvasRef, launch, stop }
}
