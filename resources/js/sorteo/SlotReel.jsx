import React, { useState, useEffect, useRef } from 'react'

const REEL_DIGITS = '0123456789'

export default function SlotReel({ index, digit, isSpinning, isRevealed, isComplete, isIdle }) {
  const [displayDigit, setDisplayDigit] = useState('?')
  const intervalRef = useRef(null)

  // Spinning animation - rapidly cycle through random digits
  useEffect(() => {
    if (isSpinning && !isRevealed) {
      let speed = 50 + index * 15 // Stagger speed per reel
      intervalRef.current = setInterval(() => {
        setDisplayDigit(REEL_DIGITS[Math.floor(Math.random() * 10)])
      }, speed)
      return () => clearInterval(intervalRef.current)
    }
  }, [isSpinning, isRevealed, index])

  // After spinning but before reveal: keep cycling slower
  useEffect(() => {
    if (!isSpinning && !isRevealed && !isIdle) {
      intervalRef.current = setInterval(() => {
        setDisplayDigit(REEL_DIGITS[Math.floor(Math.random() * 10)])
      }, 100)
      return () => clearInterval(intervalRef.current)
    }
  }, [isSpinning, isRevealed, isIdle])

  // When revealed, show the actual digit
  useEffect(() => {
    if (isRevealed) {
      if (intervalRef.current) clearInterval(intervalRef.current)
      setDisplayDigit(digit)
    }
  }, [isRevealed, digit])

  // Reset to ? when idle
  useEffect(() => {
    if (isIdle) {
      if (intervalRef.current) clearInterval(intervalRef.current)
      setDisplayDigit('?')
    }
  }, [isIdle])

  const revealed = isRevealed
  const glowing = isComplete

  return (
    <div style={{
      position: 'relative',
      width: 'clamp(55px, 12vw, 95px)',
      height: 'clamp(75px, 16vw, 130px)',
      perspective: '500px',
    }}>
      {/* Glow effect behind */}
      <div style={{
        position: 'absolute',
        inset: '-4px',
        borderRadius: '18px',
        background: revealed
          ? 'linear-gradient(135deg, rgba(168, 85, 247, 0.6), rgba(34, 211, 238, 0.4))'
          : 'linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(139, 92, 246, 0.05))',
        filter: revealed ? 'blur(8px)' : 'blur(4px)',
        opacity: glowing ? 1 : 0.7,
        transition: 'all 0.6s ease',
        animation: glowing ? 'reelGlow 1.5s ease-in-out infinite alternate' : 'none',
      }} />

      {/* Main reel body */}
      <div style={{
        position: 'relative',
        width: '100%',
        height: '100%',
        borderRadius: '14px',
        background: revealed
          ? 'linear-gradient(180deg, #1e103a 0%, #150a28 50%, #1e103a 100%)'
          : 'linear-gradient(180deg, #120822 0%, #0d0618 50%, #120822 100%)',
        border: `2px solid ${revealed ? 'rgba(168, 85, 247, 0.5)' : 'rgba(139, 92, 246, 0.15)'}`,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        overflow: 'hidden',
        boxShadow: revealed
          ? 'inset 0 0 30px rgba(168, 85, 247, 0.2), 0 4px 20px rgba(0, 0, 0, 0.4)'
          : 'inset 0 0 15px rgba(0, 0, 0, 0.5), 0 4px 15px rgba(0, 0, 0, 0.3)',
        transition: 'all 0.5s ease',
        animation: isSpinning ? `reelShake ${0.05 + index * 0.01}s linear infinite` : 'none',
      }}>
        {/* Top shadow gradient (slot machine window effect) */}
        <div style={{
          position: 'absolute', top: 0, left: 0, right: 0, height: '30%',
          background: 'linear-gradient(180deg, rgba(0,0,0,0.5), transparent)',
          pointerEvents: 'none', zIndex: 2,
        }} />

        {/* Bottom shadow gradient */}
        <div style={{
          position: 'absolute', bottom: 0, left: 0, right: 0, height: '30%',
          background: 'linear-gradient(0deg, rgba(0,0,0,0.5), transparent)',
          pointerEvents: 'none', zIndex: 2,
        }} />

        {/* Center highlight line */}
        <div style={{
          position: 'absolute', top: '50%', left: '5%', right: '5%',
          height: '2px', transform: 'translateY(-50%)',
          background: revealed
            ? 'linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.4), transparent)'
            : 'linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.1), transparent)',
          zIndex: 3, transition: 'all 0.5s',
        }} />

        {/* The digit */}
        <span style={{
          fontFamily: "'Orbitron', sans-serif",
          fontSize: 'clamp(30px, 7vw, 56px)',
          fontWeight: 900,
          color: revealed ? '#e9d5ff' : 'rgba(139, 92, 246, 0.4)',
          textShadow: revealed
            ? '0 0 20px rgba(168, 85, 247, 0.8), 0 0 40px rgba(168, 85, 247, 0.3)'
            : 'none',
          transition: isSpinning ? 'none' : 'all 0.4s ease',
          zIndex: 5,
          animation: isSpinning ? 'digitSpin 0.08s linear infinite' : (revealed ? 'digitReveal 0.5s ease' : 'none'),
          transform: revealed ? 'scale(1)' : 'scale(0.9)',
          opacity: isIdle ? 0.3 : 1,
        }}>
          {displayDigit}
        </span>

        {/* Scanning line effect during spin */}
        {isSpinning && (
          <div style={{
            position: 'absolute',
            top: 0, left: 0, right: 0,
            height: '4px',
            background: 'linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.6), transparent)',
            animation: 'scanLine 0.3s linear infinite',
            zIndex: 4,
          }} />
        )}
      </div>

      {/* Bottom indicator dot */}
      <div style={{
        width: '6px', height: '6px', borderRadius: '50%',
        margin: '8px auto 0',
        background: revealed ? '#a855f7' : 'rgba(139, 92, 246, 0.2)',
        boxShadow: revealed ? '0 0 10px rgba(168, 85, 247, 0.6)' : 'none',
        transition: 'all 0.5s ease',
      }} />

      <style>{`
        @keyframes reelShake {
          0%, 100% { transform: translateY(0); }
          25% { transform: translateY(-2px); }
          75% { transform: translateY(2px); }
        }
        @keyframes digitSpin {
          0% { transform: translateY(-100%) scale(0.8); opacity: 0.3; }
          50% { transform: translateY(0) scale(1); opacity: 1; }
          100% { transform: translateY(100%) scale(0.8); opacity: 0.3; }
        }
        @keyframes digitReveal {
          0% { transform: scale(1.5) rotateX(90deg); opacity: 0; }
          50% { transform: scale(1.2) rotateX(-10deg); opacity: 0.8; }
          100% { transform: scale(1) rotateX(0deg); opacity: 1; }
        }
        @keyframes scanLine {
          0% { top: 0; }
          100% { top: 100%; }
        }
        @keyframes reelGlow {
          0% { opacity: 0.6; }
          100% { opacity: 1; }
        }
      `}</style>
    </div>
  )
}
