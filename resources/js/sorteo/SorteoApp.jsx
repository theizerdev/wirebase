import React, { useState, useEffect, useRef, useCallback } from 'react'
import { useConfetti } from './useConfetti.js'
import { useSoundFX } from './useSoundFX.js'
import SlotReel from './SlotReel.jsx'
import HistorialModal from './HistorialModal.jsx'

const API_BASE = '/api/sorteo'
const DIGIT_COUNT = 6
const REVEAL_INTERVAL = 5000 // 5 seconds between digits
const SPIN_DURATION = 3000  // 3 seconds spinning animation

// Phase states
const PHASE_IDLE = 'idle'
const PHASE_SPINNING = 'spinning'
const PHASE_REVEALING = 'revealing'
const PHASE_COMPLETE = 'complete'

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || ''

export default function SorteoApp() {
  const [phase, setPhase] = useState(PHASE_IDLE)
  const [winningNumber, setWinningNumber] = useState('')
  const [revealedCount, setRevealedCount] = useState(0)
  const [sorteoData, setSorteoData] = useState(null)
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)
  const [eligibleCount, setEligibleCount] = useState(null)
  const [showHistorial, setShowHistorial] = useState(false)
  const [empresaId, setEmpresaId] = useState(1)
  const [pulseBtn, setPulseBtn] = useState(true)

  const revealTimerRef = useRef(null)
  const { canvasRef, launch: launchConfetti, stop: stopConfetti } = useConfetti()
  const { spinSound, tickSound, revealSound, victorySound } = useSoundFX()

  // Fetch eligible count on mount
  useEffect(() => {
    fetch(`${API_BASE}/elegibles?empresa_id=${empresaId}`)
      .then(r => r.json())
      .then(d => { if (d.success) setEligibleCount(d.data.total) })
      .catch(() => {})
  }, [empresaId])

  // Cleanup timers
  useEffect(() => {
    return () => { if (revealTimerRef.current) clearInterval(revealTimerRef.current) }
  }, [])

  const startSorteo = useCallback(async () => {
    if (phase !== PHASE_IDLE) return
    setError(null)
    setLoading(true)
    setPulseBtn(false)

    try {
      const resp = await fetch(`${API_BASE}/ejecutar`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf(),
        },
        body: JSON.stringify({ empresa_id: empresaId }),
      })
      const data = await resp.json()

      if (!resp.ok || !data.success) {
        setError(data.message || 'Error al ejecutar el sorteo')
        setLoading(false)
        setPulseBtn(true)
        return
      }

      const numero = String(data.data.numero_contrato_ganador).padStart(DIGIT_COUNT, '0')
      setWinningNumber(numero)
      setSorteoData(data.data)
      setRevealedCount(0)
      setPhase(PHASE_SPINNING)
      setLoading(false)

      // Play spin sound
      spinSound()

      // After spin animation, start revealing digits
      setTimeout(() => {
        setPhase(PHASE_REVEALING)
        setRevealedCount(1)
        revealSound()
        tickSound()

        let count = 1
        revealTimerRef.current = setInterval(() => {
          count++
          setRevealedCount(count)
          revealSound()
          tickSound()

          if (count >= DIGIT_COUNT) {
            clearInterval(revealTimerRef.current)
            revealTimerRef.current = null
            setTimeout(() => {
              setPhase(PHASE_COMPLETE)
              victorySound()
              launchConfetti(7000)
              // Refresh eligible count
              fetch(`${API_BASE}/elegibles?empresa_id=${empresaId}`)
                .then(r => r.json())
                .then(d => { if (d.success) setEligibleCount(d.data.total) })
                .catch(() => {})
            }, 400)
          }
        }, REVEAL_INTERVAL)
      }, SPIN_DURATION)
    } catch (e) {
      setError('Error de conexión con el servidor')
      setLoading(false)
      setPulseBtn(true)
    }
  }, [phase, empresaId, spinSound, revealSound, tickSound, victorySound, launchConfetti])

  const reset = useCallback(() => {
    if (revealTimerRef.current) {
      clearInterval(revealTimerRef.current)
      revealTimerRef.current = null
    }
    stopConfetti()
    setPhase(PHASE_IDLE)
    setWinningNumber('')
    setRevealedCount(0)
    setSorteoData(null)
    setError(null)
    setPulseBtn(true)
  }, [stopConfetti])

  const digits = winningNumber.split('')

  return (
    <div style={{
      minHeight: '100vh',
      width: '100vw',
      background: 'radial-gradient(ellipse at 50% 0%, #1a0533 0%, #0a0015 40%, #050008 100%)',
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
      justifyContent: 'center',
      fontFamily: "'Poppins', sans-serif",
      position: 'relative',
      overflow: 'hidden',
    }}>
      {/* Animated background particles */}
      <BackgroundParticles />

      {/* Confetti canvas */}
      <canvas ref={canvasRef} style={{
        position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh',
        pointerEvents: 'none', zIndex: 100,
      }} />

      {/* Top bar */}
      <div style={{
        position: 'absolute', top: 0, left: 0, right: 0,
        display: 'flex', justifyContent: 'space-between', alignItems: 'center',
        padding: '20px 30px', zIndex: 10,
      }}>
        <div style={{
          fontFamily: "'Orbitron', sans-serif",
          fontSize: '14px', color: '#8b5cf6', letterSpacing: '3px',
          textTransform: 'uppercase', fontWeight: 700,
        }}>
          ⚡ Inversiones Danger 3000 C.A Sorteo
        </div>
        <button
          onClick={() => setShowHistorial(true)}
          style={{
            background: 'rgba(139, 92, 246, 0.15)',
            border: '1px solid rgba(139, 92, 246, 0.3)',
            color: '#c4b5fd', borderRadius: '12px',
            padding: '8px 18px', cursor: 'pointer',
            fontFamily: "'Poppins', sans-serif", fontSize: '13px',
            fontWeight: 500, transition: 'all 0.3s',
          }}
          onMouseEnter={e => {
            e.target.style.background = 'rgba(139, 92, 246, 0.3)'
            e.target.style.borderColor = 'rgba(139, 92, 246, 0.6)'
          }}
          onMouseLeave={e => {
            e.target.style.background = 'rgba(139, 92, 246, 0.15)'
            e.target.style.borderColor = 'rgba(139, 92, 246, 0.3)'
          }}
        >
          📋 Historial
        </button>
      </div>

      {/* Main content */}
      <div style={{
        display: 'flex', flexDirection: 'column', alignItems: 'center',
        gap: '40px', zIndex: 5, width: '100%', maxWidth: '900px',
        padding: '0 20px',
      }}>
        {/* Title */}
        <div style={{ textAlign: 'center' }}>
          <h1 style={{
            fontFamily: "'Orbitron', sans-serif",
            fontSize: 'clamp(28px, 5vw, 52px)',
            fontWeight: 900,
            background: 'linear-gradient(135deg, #c084fc, #818cf8, #22d3ee, #c084fc)',
            backgroundSize: '300% 300%',
            WebkitBackgroundClip: 'text',
            WebkitTextFillColor: 'transparent',
            animation: 'gradientShift 4s ease infinite',
            margin: 0,
            textShadow: 'none',
            filter: 'drop-shadow(0 0 30px rgba(139, 92, 246, 0.3))',
          }}>
            SORTEO INTERACTIVO
          </h1>
          <p style={{
            color: '#a78bfa', fontSize: '16px', marginTop: '8px',
            fontWeight: 300, letterSpacing: '2px',
          }}>
            {phase === PHASE_IDLE && '¡Presiona el botón para iniciar!'}
            {phase === PHASE_SPINNING && '🎰 Girando los rodillos...'}
            {phase === PHASE_REVEALING && `Revelando dígito ${revealedCount} de ${DIGIT_COUNT}...`}
            {phase === PHASE_COMPLETE && '🎉 ¡CONTRATO GANADOR!'}
          </p>
        </div>

        {/* Slot Machine Frame */}
        <div style={{
          background: 'linear-gradient(180deg, #1e1033 0%, #130a22 100%)',
          borderRadius: '24px',
          padding: '4px',
          boxShadow: phase === PHASE_COMPLETE
            ? '0 0 60px rgba(168, 85, 247, 0.5), 0 0 120px rgba(139, 92, 246, 0.2), inset 0 0 60px rgba(168, 85, 247, 0.1)'
            : '0 0 40px rgba(139, 92, 246, 0.15), 0 20px 60px rgba(0, 0, 0, 0.5)',
          border: `2px solid ${phase === PHASE_COMPLETE ? 'rgba(168, 85, 247, 0.6)' : 'rgba(139, 92, 246, 0.2)'}`,
          transition: 'all 0.5s ease',
          width: '100%', maxWidth: '750px',
        }}>
          {/* Inner machine body */}
          <div style={{
            background: 'linear-gradient(180deg, #0f0820 0%, #0a0518 100%)',
            borderRadius: '20px',
            padding: '30px 20px',
          }}>
            {/* Reels container */}
            <div style={{
              display: 'flex',
              justifyContent: 'center',
              gap: 'clamp(8px, 2vw, 16px)',
              marginBottom: '20px',
            }}>
              {Array.from({ length: DIGIT_COUNT }).map((_, i) => (
                <SlotReel
                  key={i}
                  index={i}
                  digit={digits[i] || '0'}
                  isSpinning={phase === PHASE_SPINNING}
                  isRevealed={phase === PHASE_REVEALING ? i < revealedCount : phase === PHASE_COMPLETE}
                  isComplete={phase === PHASE_COMPLETE}
                  isIdle={phase === PHASE_IDLE}
                />
              ))}
            </div>

            {/* Status bar under reels */}
            <div style={{
              display: 'flex', justifyContent: 'center', alignItems: 'center',
              gap: '6px', marginTop: '10px',
            }}>
              {Array.from({ length: DIGIT_COUNT }).map((_, i) => (
                <div key={i} style={{
                  width: '12px', height: '4px', borderRadius: '2px',
                  background: (phase === PHASE_REVEALING && i < revealedCount) || phase === PHASE_COMPLETE
                    ? '#a855f7' : 'rgba(139, 92, 246, 0.2)',
                  transition: 'background 0.5s ease',
                  boxShadow: (phase === PHASE_REVEALING && i < revealedCount) || phase === PHASE_COMPLETE
                    ? '0 0 8px rgba(168, 85, 247, 0.6)' : 'none',
                }} />
              ))}
            </div>
          </div>
        </div>

        {/* Winner info */}
        {phase === PHASE_COMPLETE && sorteoData && (
          <div style={{
            textAlign: 'center',
            animation: 'fadeInUp 0.8s ease',
          }}>
            {sorteoData.cliente?.nombre && (
              <p style={{
                color: '#e9d5ff', fontSize: '20px', fontWeight: 600,
                margin: '0 0 8px 0',
              }}>
                🏆 {sorteoData.cliente.nombre} {sorteoData.cliente.apellido}
              </p>
            )}
            <p style={{
              color: '#a78bfa', fontSize: '13px', fontWeight: 400,
              margin: 0, letterSpacing: '1px',
            }}>
              Hash: {sorteoData.hash_validacion?.slice(0, 16)}...
            </p>
            <p style={{
              color: 'rgba(167, 139, 250, 0.6)', fontSize: '12px',
              marginTop: '4px',
            }}>
              {sorteoData.total_elegibles} contratos participaron
            </p>
          </div>
        )}

        {/* Eligible count badge */}
        {phase === PHASE_IDLE && eligibleCount !== null && (
          <div style={{
            background: 'rgba(139, 92, 246, 0.1)',
            border: '1px solid rgba(139, 92, 246, 0.2)',
            borderRadius: '16px', padding: '10px 24px',
            color: '#c4b5fd', fontSize: '14px', fontWeight: 500,
            textAlign: 'center',
          }}>
            📊 <strong>{eligibleCount}</strong> contratos elegibles para el sorteo
          </div>
        )}

        {/* Error message */}
        {error && (
          <div style={{
            background: 'rgba(239, 68, 68, 0.15)',
            border: '1px solid rgba(239, 68, 68, 0.3)',
            borderRadius: '12px', padding: '12px 24px',
            color: '#fca5a5', fontSize: '14px', textAlign: 'center',
          }}>
            ⚠️ {error}
          </div>
        )}

        {/* Action buttons */}
        <div style={{ display: 'flex', gap: '16px', justifyContent: 'center', flexWrap: 'wrap' }}>
          {phase === PHASE_IDLE && (
            <button
              onClick={startSorteo}
              disabled={loading || eligibleCount === 0}
              aria-label="Iniciar Sorteo"
              style={{
                fontFamily: "'Orbitron', sans-serif",
                fontSize: 'clamp(16px, 3vw, 22px)',
                fontWeight: 800,
                padding: '18px 50px',
                borderRadius: '16px',
                border: '2px solid rgba(168, 85, 247, 0.6)',
                background: 'linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #7c3aed 100%)',
                backgroundSize: '200% 200%',
                color: '#fff',
                cursor: loading || eligibleCount === 0 ? 'not-allowed' : 'pointer',
                opacity: loading || eligibleCount === 0 ? 0.5 : 1,
                letterSpacing: '3px',
                textTransform: 'uppercase',
                boxShadow: '0 0 30px rgba(139, 92, 246, 0.4), 0 8px 30px rgba(0, 0, 0, 0.3)',
                transition: 'all 0.3s ease',
                animation: pulseBtn ? 'btnPulse 2s ease-in-out infinite' : 'none',
                position: 'relative',
                overflow: 'hidden',
              }}
              onMouseEnter={e => {
                if (!loading && eligibleCount !== 0) {
                  e.target.style.transform = 'scale(1.05)'
                  e.target.style.boxShadow = '0 0 50px rgba(139, 92, 246, 0.6), 0 8px 40px rgba(0, 0, 0, 0.4)'
                }
              }}
              onMouseLeave={e => {
                e.target.style.transform = 'scale(1)'
                e.target.style.boxShadow = '0 0 30px rgba(139, 92, 246, 0.4), 0 8px 30px rgba(0, 0, 0, 0.3)'
              }}
              onMouseDown={e => {
                if (!loading && eligibleCount !== 0) {
                  e.target.style.transform = 'scale(0.97)'
                }
              }}
              onMouseUp={e => {
                e.target.style.transform = 'scale(1.05)'
              }}
            >
              {loading ? '⏳ PROCESANDO...' : '🎰 INICIAR SORTEO'}
            </button>
          )}

          {phase === PHASE_COMPLETE && (
            <button
              onClick={reset}
              aria-label="Nuevo Sorteo"
              style={{
                fontFamily: "'Orbitron', sans-serif",
                fontSize: '16px',
                fontWeight: 700,
                padding: '14px 40px',
                borderRadius: '14px',
                border: '2px solid rgba(34, 211, 238, 0.4)',
                background: 'linear-gradient(135deg, #0891b2 0%, #06b6d4 100%)',
                color: '#fff',
                cursor: 'pointer',
                letterSpacing: '2px',
                textTransform: 'uppercase',
                boxShadow: '0 0 25px rgba(6, 182, 212, 0.3), 0 6px 20px rgba(0, 0, 0, 0.3)',
                transition: 'all 0.3s ease',
              }}
              onMouseEnter={e => {
                e.target.style.transform = 'scale(1.05)'
                e.target.style.boxShadow = '0 0 40px rgba(6, 182, 212, 0.5), 0 8px 30px rgba(0, 0, 0, 0.4)'
              }}
              onMouseLeave={e => {
                e.target.style.transform = 'scale(1)'
                e.target.style.boxShadow = '0 0 25px rgba(6, 182, 212, 0.3), 0 6px 20px rgba(0, 0, 0, 0.3)'
              }}
            >
              🔄 NUEVO SORTEO
            </button>
          )}
        </div>

        {/* Countdown timer during reveal */}
        {phase === PHASE_REVEALING && revealedCount < DIGIT_COUNT && (
          <CountdownTimer key={revealedCount} seconds={5} />
        )}
      </div>

      {/* Historial Modal */}
      {showHistorial && (
        <HistorialModal
          empresaId={empresaId}
          onClose={() => setShowHistorial(false)}
        />
      )}

      {/* Global styles */}
      <style>{`
        @keyframes gradientShift {
          0%, 100% { background-position: 0% 50%; }
          50% { background-position: 100% 50%; }
        }
        @keyframes btnPulse {
          0%, 100% { box-shadow: 0 0 30px rgba(139, 92, 246, 0.4), 0 8px 30px rgba(0, 0, 0, 0.3); }
          50% { box-shadow: 0 0 50px rgba(139, 92, 246, 0.7), 0 8px 40px rgba(0, 0, 0, 0.4); }
        }
        @keyframes fadeInUp {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-10px); }
        }
      `}</style>
    </div>
  )
}

/* ── Background animated particles ── */
function BackgroundParticles() {
  const particles = useRef(
    Array.from({ length: 40 }, (_, i) => ({
      id: i,
      x: Math.random() * 100,
      y: Math.random() * 100,
      size: Math.random() * 3 + 1,
      duration: Math.random() * 20 + 15,
      delay: Math.random() * 10,
      opacity: Math.random() * 0.3 + 0.05,
    }))
  ).current

  return (
    <div style={{ position: 'absolute', inset: 0, overflow: 'hidden', pointerEvents: 'none' }}>
      {particles.map(p => (
        <div key={p.id} style={{
          position: 'absolute',
          left: `${p.x}%`,
          top: `${p.y}%`,
          width: `${p.size}px`,
          height: `${p.size}px`,
          borderRadius: '50%',
          background: `rgba(168, 85, 247, ${p.opacity})`,
          boxShadow: `0 0 ${p.size * 3}px rgba(168, 85, 247, ${p.opacity * 0.5})`,
          animation: `float ${p.duration}s ease-in-out ${p.delay}s infinite`,
        }} />
      ))}
    </div>
  )
}

/* ── Countdown timer between reveals ── */
function CountdownTimer({ seconds }) {
  const [remaining, setRemaining] = useState(seconds)

  useEffect(() => {
    if (remaining <= 0) return
    const timer = setTimeout(() => setRemaining(r => r - 1), 1000)
    return () => clearTimeout(timer)
  }, [remaining])

  return (
    <div style={{
      display: 'flex', flexDirection: 'column', alignItems: 'center',
      gap: '6px', animation: 'fadeInUp 0.3s ease',
    }}>
      <div style={{
        fontFamily: "'Orbitron', sans-serif",
        fontSize: '28px', fontWeight: 700,
        color: '#c084fc',
        textShadow: '0 0 20px rgba(192, 132, 252, 0.5)',
      }}>
        {remaining}s
      </div>
      <div style={{
        width: '120px', height: '4px',
        background: 'rgba(139, 92, 246, 0.2)',
        borderRadius: '2px', overflow: 'hidden',
      }}>
        <div style={{
          height: '100%',
          background: 'linear-gradient(90deg, #a855f7, #c084fc)',
          borderRadius: '2px',
          width: `${(remaining / seconds) * 100}%`,
          transition: 'width 1s linear',
          boxShadow: '0 0 10px rgba(168, 85, 247, 0.5)',
        }} />
      </div>
      <p style={{ color: 'rgba(167, 139, 250, 0.5)', fontSize: '11px', margin: 0 }}>
        Próximo dígito en...
      </p>
    </div>
  )
}
