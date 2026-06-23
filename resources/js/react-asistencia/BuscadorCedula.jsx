import React, { useState } from 'react';

export default function BuscadorCedula({ onSubmit, isLoading }) {
    const [cedula, setCedula] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        if (cedula.trim() !== '') {
            onSubmit(cedula.trim());
            setCedula('');
        }
    };

    return (
        <div className="manual-form-container">
            <style>{`
                .manual-form-container {
                    width: 100%;
                    max-width: 340px;
                }
                .manual-card {
                    background: rgba(255, 255, 255, 0.015);
                    border: 1px solid rgba(255, 255, 255, 0.04);
                    border-radius: 20px;
                    padding: 30px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                }
                .manual-header {
                    text-align: center;
                    margin-bottom: 25px;
                }
                .manual-icon {
                    font-size: 2.8rem;
                    color: #818cf8;
                    margin-bottom: 10px;
                    display: inline-block;
                }
                .manual-title {
                    font-size: 1.15rem;
                    font-weight: 750;
                    color: #ffffff;
                    margin: 0 0 5px 0;
                }
                .manual-desc {
                    font-size: 0.8rem;
                    color: #94a3b8;
                    line-height: 1.4;
                    margin: 0;
                }
                .input-group-custom {
                    position: relative;
                    margin-bottom: 25px;
                }
                .input-custom {
                    width: 100%;
                    padding: 14px 18px;
                    background: rgba(15, 23, 42, 0.4);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 12px;
                    color: #ffffff;
                    font-size: 0.95rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }
                .input-custom:focus {
                    outline: none;
                    border-color: #6366f1;
                    box-shadow: 0 0 15px rgba(99, 102, 241, 0.15);
                    background: rgba(15, 23, 42, 0.6);
                }
                .input-custom::placeholder {
                    color: rgba(255, 255, 255, 0.25);
                }
                .input-label {
                    position: absolute;
                    left: 14px;
                    top: -10px;
                    background: #0d1222;
                    padding: 0 8px;
                    font-size: 0.72rem;
                    font-weight: 700;
                    color: #818cf8;
                    letter-spacing: 0.05em;
                    text-transform: uppercase;
                    border-radius: 4px;
                    border: 1px solid rgba(255, 255, 255, 0.03);
                }
                .btn-submit-kiosk {
                    width: 100%;
                    padding: 14px;
                    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
                    color: #ffffff;
                    border: none;
                    border-radius: 12px;
                    font-size: 0.9rem;
                    font-weight: 700;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    transition: all 0.25s ease;
                }
                .btn-submit-kiosk:hover:not(:disabled) {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
                    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                }
                .btn-submit-kiosk:active:not(:disabled) {
                    transform: translateY(0);
                }
                .btn-submit-kiosk:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
            `}</style>

            <form onSubmit={handleSubmit} className="manual-card">
                <div className="manual-header">
                    <i className="ri-profile-line manual-icon"></i>
                    <h5 className="manual-title">Búsqueda Manual</h5>
                    <p className="manual-desc">Ingrese el documento de identidad o cédula del pastor para registrar la asistencia.</p>
                </div>
                
                <div className="input-group-custom">
                    <span className="input-label">Documento</span>
                    <input 
                        type="text" 
                        className="input-custom" 
                        placeholder="V-12345678"
                        value={cedula}
                        onChange={(e) => setCedula(e.target.value)}
                        disabled={isLoading}
                        required
                        autoComplete="off"
                    />
                </div>

                <button 
                    type="submit" 
                    className="btn-submit-kiosk"
                    disabled={isLoading || cedula.trim() === ''}
                >
                    {isLoading ? (
                        <>
                            <span className="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando...
                        </>
                    ) : (
                        <>
                            <i className="ri-search-line"></i> Registrar Asistencia
                        </>
                    )}
                </button>
            </form>
        </div>
    );
}
