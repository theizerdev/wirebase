import { useRef, useCallback } from 'react'

// Web Audio API based sound effects - no external files needed
export function useSoundFX() {
  const ctxRef = useRef(null)

  const getCtx = useCallback(() => {
    if (!ctxRef.current) {
      ctxRef.current = new (window.AudioContext || window.webkitAudioContext)()
    }
    if (ctxRef.current.state === 'suspended') {
      ctxRef.current.resume()
    }
    return ctxRef.current
  }, [])

  const playTone = useCallback((freq, duration, type = 'sine', volume = 0.3, delay = 0) => {
    const ctx = getCtx()
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()
    osc.type = type
    osc.frequency.value = freq
    gain.gain.setValueAtTime(0, ctx.currentTime + delay)
    gain.gain.linearRampToValueAtTime(volume, ctx.currentTime + delay + 0.02)
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + duration)
    osc.connect(gain)
    gain.connect(ctx.destination)
    osc.start(ctx.currentTime + delay)
    osc.stop(ctx.currentTime + delay + duration)
  }, [getCtx])

  const playNoise = useCallback((duration, volume = 0.1, delay = 0) => {
    const ctx = getCtx()
    const bufferSize = ctx.sampleRate * duration
    const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate)
    const data = buffer.getChannelData(0)
    for (let i = 0; i < bufferSize; i++) {
      data[i] = (Math.random() * 2 - 1) * 0.5
    }
    const source = ctx.createBufferSource()
    source.buffer = buffer
    const gain = ctx.createGain()
    const filter = ctx.createBiquadFilter()
    filter.type = 'lowpass'
    filter.frequency.value = 800
    gain.gain.setValueAtTime(volume, ctx.currentTime + delay)
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + duration)
    source.connect(filter)
    filter.connect(gain)
    gain.connect(ctx.destination)
    source.start(ctx.currentTime + delay)
  }, [getCtx])

  const spinSound = useCallback(() => {
    // Mechanical spinning sound - rapid clicks that slow down
    for (let i = 0; i < 40; i++) {
      const t = i * 0.06
      const freq = 200 + Math.random() * 300
      playTone(freq, 0.03, 'square', 0.05, t)
    }
    // Whirring base
    playTone(120, 2.5, 'sawtooth', 0.08)
    playNoise(2.5, 0.04)
  }, [playTone, playNoise])

  const tickSound = useCallback(() => {
    // Single mechanical tick for each digit lock
    playTone(800, 0.05, 'square', 0.15)
    playTone(1200, 0.03, 'sine', 0.1, 0.02)
    playNoise(0.05, 0.06)
  }, [playTone, playNoise])

  const revealSound = useCallback(() => {
    // Dramatic digit reveal - descending then ascending
    playTone(600, 0.15, 'sine', 0.2)
    playTone(900, 0.15, 'sine', 0.2, 0.05)
    playTone(1200, 0.2, 'triangle', 0.15, 0.1)
  }, [playTone])

  const victorySound = useCallback(() => {
    // Triumphant fanfare
    const notes = [523, 659, 784, 1047, 1319, 1568]
    notes.forEach((freq, i) => {
      playTone(freq, 0.4, 'sine', 0.2, i * 0.12)
      playTone(freq * 1.5, 0.3, 'triangle', 0.08, i * 0.12 + 0.05)
    })
    // Shimmering high notes
    for (let i = 0; i < 15; i++) {
      playTone(2000 + Math.random() * 2000, 0.15, 'sine', 0.04, 0.8 + i * 0.1)
    }
  }, [playTone])

  const ambientHum = useCallback(() => {
    const ctx = getCtx()
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()
    osc.type = 'sine'
    osc.frequency.value = 60
    gain.gain.value = 0.02
    osc.connect(gain)
    gain.connect(ctx.destination)
    osc.start()
    return () => { osc.stop(); osc.disconnect() }
  }, [getCtx])

  return { spinSound, tickSound, revealSound, victorySound, ambientHum }
}
