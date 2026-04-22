import React, { useState, useEffect } from 'react'

const API_BASE = '/api/sorteo'

export default function HistorialModal({ empresaId, onClose }) {
  const [sorteos, setSorteos] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch(`${API_BASE}/historial?empresa_id=${empresaId}`)
      .then(r => r.json())
      .then(d => {
        if (d.success) setSorteos(d.data?.data || [])
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [empresaId])

  return (
    <div
      style={{
        position: 'fixed', inset: 0, zIndex: 200,
        background: 'rgba(0, 0, 0, 0.7)',
        backdropFilter: 'blur(8px)',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        padding: '20px',
        animation: 'fadeIn 0.3s ease',
      }}
      onClick={onClose}
      role="dialog"
      aria-modal="true"
      aria-label="Historial de Sorteos"
    >
      <div
        style={{
          background: 'linear-gradient(180deg, #1a0e30 0%, #0f0820 100%)',
          border: '1px solid rgba(139, 92, 246, 0.3)',
          borderRadius: '20px',
          padding: '30px',
          maxWidth: '700px',
          width: '100%',
          maxHeight: '80vh',
          overflow: 'auto',
          boxShadow: '0 0 40px rgba(139, 92, 246, 0.2), 0 20px 60px rgba(0, 0, 0, 0.5)',
          animation: 'slideUp 0.4s ease',
        }}
        onClick={e => e.stopPropagation()}
      >
        {/* Header */}
        <div style={{
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
          marginBottom: '24px',
        }}>
          <h2 style={{
            fontFamily: "'Orbitron', sans-serif",
            fontSize: '20px', fontWeight: 700,
            color: '#e9d5ff', margin: 0,
          }}>
            📋 Historial de Sorteos
          </h2>
          <button
            onClick={onClose}
            aria-label="Cerrar historial"
            style={{
              width: '36px', height: '36px', borderRadius: '10px',
              background: 'rgba(239, 68, 68, 0.15)',
              border: '1px solid rgba(239, 68, 68, 0.3)',
              color: '#fca5a5', cursor: 'pointer',
              fontSize: '18px',
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              transition: 'all 0.2s',
            }}
            onMouseEnter={e => { e.target.style.background = 'rgba(239, 68, 68, 0.3)' }}
            onMouseLeave={e => { e.target.style.background = 'rgba(239, 68, 68, 0.15)' }}
          >
            ✕
          </button>
        </div>

        {/* Content */}
        {loading && (
          <div style={{ textAlign: 'center', padding: '40px 0', color: '#a78bfa' }}>
            <div style={{
              width: '30px', height: '30px', border: '3px solid rgba(139, 92, 246, 0.3)',
              borderTop: '3px solid #a855f7', borderRadius: '50%',
              margin: '0 auto 12px',
              animation: 'spin 1s linear infinite',
            }} />
            Cargando historial...
          </div>
        )}

        {!loading && sorteos.length === 0 && (
          <div style={{
            textAlign: 'center', padding: '40px 0',
            color: '#a78bfa', fontSize: '14px',
          }}>
            🎰 No se han realizado sorteos aún.
          </div>
        )}

        {!loading && sorteos.length > 0 && (
          <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
            {sorteos.map((s, i) => (
              <div key={s.id} style={{
                background: 'rgba(139, 92, 246, 0.06)',
                border: '1px solid rgba(139, 92, 246, 0.15)',
                borderRadius: '14px',
                padding: '16px 20px',
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                gap: '12px',
                flexWrap: 'wrap',
                transition: 'all 0.2s',
              }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '14px' }}>
                  <div style={{
                    width: '40px', height: '40px', borderRadius: '12px',
                    background: 'linear-gradient(135deg, #7c3aed, #a855f7)',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    fontFamily: "'Orbitron', sans-serif",
                    fontSize: '14px', fontWeight: 700, color: '#fff',
                    flexShrink: 0,
                  }}>
                    #{sorteos.length - i}
                  </div>
                  <div>
                    <div style={{
                      fontFamily: "'Orbitron', sans-serif",
                      fontSize: '18px', fontWeight: 700,
                      color: '#e9d5ff', letterSpacing: '3px',
                    }}>
                      {s.numero_contrato_ganador}
                    </div>
                    <div style={{
                      color: 'rgba(167, 139, 250, 0.6)', fontSize: '12px',
                      marginTop: '2px',
                    }}>
                      {s.nombre || 'Sorteo'}
                    </div>
                  </div>
                </div>
                <div style={{ textAlign: 'right' }}>
                  <div style={{ color: '#c4b5fd', fontSize: '13px', fontWeight: 500 }}>
                    {new Date(s.fecha_sorteo).toLocaleDateString('es', {
                      day: '2-digit', month: 'short', year: 'numeric',
                      hour: '2-digit', minute: '2-digit',
                    })}
                  </div>
                  <div style={{
                    color: 'rgba(167, 139, 250, 0.4)', fontSize: '10px',
                    fontFamily: 'monospace', marginTop: '2px',
                  }}>
                    {s.hash_validacion?.slice(0, 12)}...
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }
        @keyframes slideUp {
          from { opacity: 0; transform: translateY(30px) scale(0.97); }
          to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes spin {
          to { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  )
}
