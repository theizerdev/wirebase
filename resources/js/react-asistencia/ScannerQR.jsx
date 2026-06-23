import React, { useEffect, useState, useRef } from 'react';
import { Html5Qrcode } from 'html5-qrcode';

export default function ScannerQR({ onScan, isPaused }) {
    const [hasCameraError, setHasCameraError] = useState(false);
    const isScanningRef = useRef(false);

    useEffect(() => {
        const scannerId = "qr-reader";
        let html5QrCode;

        const startScanner = async () => {
            try {
                html5QrCode = new Html5Qrcode(scannerId);
                await html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 15,
                        qrbox: (width, height) => {
                            const size = Math.min(width, height) * 0.75;
                            return { width: size, height: size };
                        },
                    },
                    (decodedText) => {
                        if (!isScanningRef.current) {
                            onScan(decodedText);
                        }
                    },
                    (errorMessage) => {
                        // ignore background scanning errors
                    }
                );
            } catch (err) {
                console.error("Error iniciando cámara", err);
                setHasCameraError(true);
            }
        };

        startScanner();

        return () => {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => {
                    console.error("Error deteniendo cámara", err);
                });
            }
        };
    }, []); // eslint-disable-line react-hooks/exhaustive-deps

    useEffect(() => {
        isScanningRef.current = isPaused;
    }, [isPaused]);

    return (
        <div className="scanner-container">
            <style>{`
                .scanner-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    width: 100%;
                }
                .scanner-frame {
                    position: relative;
                    width: 100%;
                    max-width: 320px;
                    aspect-ratio: 1 / 1;
                    border-radius: 24px;
                    overflow: hidden;
                    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
                    background: #000000;
                    border: 1px solid rgba(255, 255, 255, 0.08);
                }
                #qr-reader {
                    width: 100% !important;
                    height: 100% !important;
                    border: none !important;
                }
                #qr-reader video {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                }
                /* Hide HTML5-QRCode default UI elements */
                #qr-reader__dashboard_section_csr,
                #qr-reader__status_span,
                #qr-reader__header,
                #qr-reader img {
                    display: none !important;
                }
                .scanner-corner {
                    position: absolute;
                    width: 28px;
                    height: 28px;
                    border: 3.5px solid transparent;
                    z-index: 10;
                    pointer-events: none;
                }
                .corner-tl {
                    top: 20px;
                    left: 20px;
                    border-top-color: #6366f1;
                    border-left-color: #6366f1;
                    border-top-left-radius: 8px;
                }
                .corner-tr {
                    top: 20px;
                    right: 20px;
                    border-top-color: #6366f1;
                    border-right-color: #6366f1;
                    border-top-right-radius: 8px;
                }
                .corner-bl {
                    bottom: 20px;
                    left: 20px;
                    border-bottom-color: #6366f1;
                    border-left-color: #6366f1;
                    border-bottom-left-radius: 8px;
                }
                .corner-br {
                    bottom: 20px;
                    right: 20px;
                    border-bottom-color: #6366f1;
                    border-right-color: #6366f1;
                    border-bottom-right-radius: 8px;
                }
                .laser-line {
                    position: absolute;
                    left: 20px;
                    right: 20px;
                    height: 2.5px;
                    background: linear-gradient(90deg, transparent, #818cf8, #db2777, #818cf8, transparent);
                    box-shadow: 0 0 12px 3px rgba(99, 102, 241, 0.7);
                    z-index: 5;
                    pointer-events: none;
                    animation: laserMove-kf 2.5s infinite ease-in-out;
                }
                @keyframes laserMove-kf {
                    0% { top: 20px; }
                    50% { top: calc(100% - 20px); }
                    100% { top: 20px; }
                }
                .scanner-msg {
                    margin-top: 25px;
                    font-size: 0.88rem;
                    font-weight: 500;
                    color: #94a3b8;
                    text-align: center;
                    letter-spacing: 0.02em;
                }
                .scanner-msg.processing {
                    color: #a5b4fc;
                    animation: pulse-txt-kf 1.2s infinite;
                }
                @keyframes pulse-txt-kf {
                    0%, 100% { opacity: 0.6; }
                    50% { opacity: 1; }
                }
                .kiosk-alert-warning {
                    background: rgba(217, 119, 6, 0.1);
                    border: 1px solid rgba(217, 119, 6, 0.2);
                    color: #fbbf24;
                    padding: 20px;
                    border-radius: 16px;
                    text-align: center;
                    font-size: 0.9rem;
                    line-height: 1.6;
                    max-width: 320px;
                }
            `}</style>

            {hasCameraError ? (
                <div className="kiosk-alert-warning">
                    <i className="ri-camera-off-line d-block fs-2 mb-2"></i>
                    No se pudo acceder a la cámara. Asegúrese de otorgar los permisos en el navegador o cambie a la pestaña de <strong>Entrada Manual</strong>.
                </div>
            ) : (
                <>
                    <div className="scanner-frame">
                        <div className="scanner-corner corner-tl"></div>
                        <div className="scanner-corner corner-tr"></div>
                        <div className="scanner-corner corner-bl"></div>
                        <div className="scanner-corner corner-br"></div>
                        
                        <div id="qr-reader"></div>
                        
                        {!isPaused && <div className="laser-line"></div>}
                    </div>
                    
                    <p className={`scanner-msg ${isPaused ? 'processing' : ''}`}>
                        {isPaused ? (
                            <>
                                <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Registrando asistencia...
                            </>
                        ) : (
                            'Apunte la cámara al código QR de la credencial del Pastor'
                        )}
                    </p>
                </>
            )}
        </div>
    );
}
