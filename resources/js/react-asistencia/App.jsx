import React, { useState, useEffect, useRef } from 'react';
import ScannerQR from './ScannerQR';
import BuscadorCedula from './BuscadorCedula';
import axios from 'axios';

export default function App() {
    const [actividadId, setActividadId] = useState('');
    const [actividades, setActividades] = useState([]);
    const [activeTab, setActiveTab] = useState('qr');
    const [statusMessage, setStatusMessage] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isLoadingActividades, setIsLoadingActividades] = useState(true);
    const [estadisticas, setEstadisticas] = useState({ total: 0, recientes: [] });

    // Fallback Registration Form States
    const isDedicatedCreateMode = window.location.pathname === '/pastor/create';
    const [showRegisterForm, setShowRegisterForm] = useState(window.location.pathname === '/pastor/create');
    const [searchDocumento, setSearchDocumento] = useState('');
    const [registerError, setRegisterError] = useState('');
    const [registerIsSaving, setRegisterIsSaving] = useState(false);

    // Form inputs
    const [registerNombres, setRegisterNombres] = useState('');
    const [registerApellidos, setRegisterApellidos] = useState('');
    const [registerDocumento, setRegisterDocumento] = useState('');
    const [registerNivelMinisterial, setRegisterNivelMinisterial] = useState('');
    const [registerZona, setRegisterZona] = useState('');
    const [registerDistrito, setRegisterDistrito] = useState('');
    const [registerGenero, setRegisterGenero] = useState('');
    const [registerEdad, setRegisterEdad] = useState('');
    const [registerFeNacimiento, setRegisterFeNacimiento] = useState('');
    const [registerTelefono, setRegisterTelefono] = useState('');

    // Webcam capture states
    const [cameraStream, setCameraStream] = useState(null);
    const [capturedPhoto, setCapturedPhoto] = useState(null);

    const videoRef = useRef(null);
    const canvasRef = useRef(null);
    const successAudio = useRef(null);

    useEffect(() => {
        // Pre-load audio
        successAudio.current = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');

        const fetchActividades = async () => {
            try {
                const response = await axios.get('/api/actividades/activas');
                if (response.data && response.data.actividades) {
                    setActividades(response.data.actividades);

                    const params = new URLSearchParams(window.location.search);
                    const paramId = params.get('actividad_id');
                    if (paramId && response.data.actividades.some(a => a.id == paramId)) {
                        setActividadId(paramId);
                    } else if (response.data.actividades.length === 1) {
                        setActividadId(response.data.actividades[0].id.toString());
                    }
                }
            } catch (error) {
                console.error('Error fetching actividades:', error);
            } finally {
                setIsLoadingActividades(false);
            }
        };
        fetchActividades();
    }, []);

    useEffect(() => {
        if (actividadId) {
            axios.get(`/api/asistencias/estadisticas?actividad_id=${actividadId}`)
                .then(res => {
                    if (res.data.success) setEstadisticas(res.data.data);
                })
                .catch(err => console.error(err));
        }
    }, [actividadId]);

    // Handle Camera stream activation/deactivation
    useEffect(() => {
        let activeStream = null;

        if (showRegisterForm) {
            navigator.mediaDevices.getUserMedia({
                video: { width: 350, height: 350, facingMode: 'user' }
            })
                .then(stream => {
                    activeStream = stream;
                    setCameraStream(stream);
                    if (videoRef.current) {
                        videoRef.current.srcObject = stream;
                        videoRef.current.play().catch(err => console.error("Video play error:", err));
                    }
                })
                .catch(err => {
                    console.error("Camera access error:", err);
                    setCameraStream(null);
                });
        }

        return () => {
            if (activeStream) {
                activeStream.getTracks().forEach(track => track.stop());
            }
        };
    }, [showRegisterForm]);

    // Ensure camera stream is bound to the video element when it mounts/renders
    useEffect(() => {
        if (cameraStream && !capturedPhoto && videoRef.current) {
            if (videoRef.current.srcObject !== cameraStream) {
                videoRef.current.srcObject = cameraStream;
            }
            videoRef.current.play().catch(err => console.error("Video play error:", err));
        }
    }, [cameraStream, capturedPhoto]);

    const playSuccessSound = () => {
        if (successAudio.current) {
            successAudio.current.currentTime = 0;
            successAudio.current.play().catch(e => console.log('Audio play error:', e));
        }
    };

    const handleRegister = async (data) => {
        if (!actividadId || isLoading) return;

        setIsLoading(true);
        setStatusMessage(null);

        // Cache the query term for pre-filling registration document
        if (data.cedula) {
            setSearchDocumento(data.cedula);
        } else {
            setSearchDocumento('');
        }

        try {
            const response = await axios.post(`/api/asistencias/registrar`, { ...data, actividad_id: actividadId });

            playSuccessSound();

            setStatusMessage({ type: 'success', data: response.data });

            if (response.data.estadisticas) {
                setEstadisticas(response.data.estadisticas);
            }

            setTimeout(() => {
                setStatusMessage(null);
            }, 3000);
        } catch (error) {
            setStatusMessage({
                type: 'error',
                data: error.response?.data || { message: 'Error de red o servidor.' }
            });
        } finally {
            setIsLoading(false);
        }
    };

    // Camera Capture Logic
    const capturePhoto = () => {
        const video = videoRef.current;
        const canvas = canvasRef.current;
        if (video && canvas) {
            const context = canvas.getContext('2d');
            canvas.width = 400;
            canvas.height = 400;
            context.drawImage(video, 0, 0, 400, 400);
            const dataUrl = canvas.toDataURL('image/jpeg');
            setCapturedPhoto(dataUrl);
        }
    };

    const retakePhoto = () => {
        setCapturedPhoto(null);
        if (cameraStream && videoRef.current) {
            videoRef.current.srcObject = cameraStream;
        }
    };

    const handleBirthdateChange = (e) => {
        const val = e.target.value;
        setRegisterFeNacimiento(val);
        if (val) {
            const birth = new Date(val);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const m = today.getMonth() - birth.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            setRegisterEdad(age > 0 ? age.toString() : '0');
        }
    };

    const resetRegisterForm = () => {
        setRegisterNombres('');
        setRegisterApellidos('');
        setRegisterDocumento('');
        setRegisterNivelMinisterial('');
        setRegisterZona('');
        setRegisterDistrito('');
        setRegisterGenero('');
        setRegisterEdad('');
        setRegisterFeNacimiento('');
        setRegisterTelefono('');
        setCapturedPhoto(null);
        setRegisterError('');
    };

    const handleRegisterNewPastor = async (e) => {
        e.preventDefault();
        if (registerIsSaving) return;

        setRegisterIsSaving(true);
        setRegisterError('');

        const payload = {
            actividad_id: actividadId,
            nombres: registerNombres,
            apellidos: registerApellidos,
            documento: registerDocumento,
            nivel_ministerial: registerNivelMinisterial || null,
            zona: registerZona || null,
            distrito: registerDistrito || null,
            genero: registerGenero || null,
            edad: registerEdad ? parseInt(registerEdad) : null,
            fe_nacimiento: registerFeNacimiento || null,
            telefono_tlf: registerTelefono || null,
            foto_base64: capturedPhoto || null
        };

        try {
            const response = await axios.post('/api/asistencias/registrar-nuevo-pastor', payload);

            playSuccessSound();

            setStatusMessage({ type: 'success', data: response.data });

            if (response.data.estadisticas) {
                setEstadisticas(response.data.estadisticas);
            }

            // Close form and clean up
            if (!isDedicatedCreateMode) {
                setShowRegisterForm(false);
            }
            resetRegisterForm();

            setTimeout(() => {
                setStatusMessage(null);
            }, 3000);
        } catch (error) {
            setRegisterError(error.response?.data?.message || 'Ocurrió un error al registrar al pastor. Intente nuevamente.');
        } finally {
            setRegisterIsSaving(false);
        }
    };

    return (
        <div className="asistencia-kiosk-wrapper">
            <div className="ambient-glow glow-1"></div>
            <div className="ambient-glow glow-2"></div>

            {/* Custom Embedded CSS Stylesheet */}
            <style>{`
                .asistencia-kiosk-wrapper {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    z-index: 9999;
                    background: radial-gradient(circle at 50% 50%, #0c101d 0%, #06080f 100%);
                    color: #e2e8f0;
                    font-family: 'Inter', system-ui, -apple-system, sans-serif;
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                }

                .ambient-glow {
                    position: absolute;
                    border-radius: 50%;
                    filter: blur(140px);
                    opacity: 0.1;
                    pointer-events: none;
                    z-index: 1;
                }
                .glow-1 {
                    width: 600px;
                    height: 600px;
                    background: #4f46e5;
                    top: -200px;
                    left: -200px;
                }
                .glow-2 {
                    width: 500px;
                    height: 500px;
                    background: #db2777;
                    bottom: -150px;
                    right: -150px;
                }

                .kiosk-header {
                    height: 85px;
                    padding: 0 40px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    background: rgba(10, 15, 30, 0.75);
                    backdrop-filter: blur(20px);
                    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                    z-index: 10;
                    position: relative;
                }
                .header-left, .header-right {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    width: 35%;
                }
                .header-right {
                    justify-content: flex-end;
                }
                .header-center {
                    width: 30%;
                    text-align: center;
                }
                .header-center h1 {
                    font-size: 1.35rem;
                    font-weight: 800;
                    letter-spacing: 0.06em;
                    text-transform: uppercase;
                    background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    margin: 0;
                }

                .btn-back {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    color: #94a3b8;
                    text-decoration: none;
                    padding: 10px 18px;
                    border-radius: 12px;
                    background: rgba(255, 255, 255, 0.03);
                    border: 1px solid rgba(255, 255, 255, 0.06);
                    font-weight: 500;
                    font-size: 0.88rem;
                    transition: all 0.2s ease;
                    cursor: pointer;
                }
                .btn-back:hover {
                    color: #ffffff;
                    background: rgba(255, 255, 255, 0.08);
                    border-color: rgba(255, 255, 255, 0.12);
                    transform: translateY(-1px);
                }

                .live-indicator {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 6px 14px;
                    border-radius: 20px;
                    background: rgba(16, 185, 129, 0.08);
                    border: 1px solid rgba(16, 185, 129, 0.15);
                }
                .pulse-dot {
                    width: 8px;
                    height: 8px;
                    background: #10b981;
                    border-radius: 50%;
                    animation: pulse-kf 1.8s infinite;
                }
                .live-text {
                    font-size: 0.72rem;
                    color: #34d399;
                    font-weight: 700;
                    letter-spacing: 0.08em;
                }

                @keyframes pulse-kf {
                    0% { transform: scale(0.95); opacity: 0.5; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
                    70% { transform: scale(1.1); opacity: 1; box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
                    100% { transform: scale(0.95); opacity: 0.5; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
                }

                .event-selector-wrapper {
                    position: relative;
                    display: flex;
                    align-items: center;
                    width: 100%;
                    max-width: 320px;
                }
                .event-selector-icon {
                    position: absolute;
                    left: 16px;
                    color: #6366f1;
                    font-size: 1.1rem;
                    pointer-events: none;
                }
                .event-select {
                    width: 100%;
                    padding: 10px 40px 10px 46px;
                    background: rgba(15, 23, 42, 0.5);
                    border: 1px solid rgba(99, 102, 241, 0.25);
                    border-radius: 12px;
                    color: #ffffff;
                    font-size: 0.88rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    appearance: none;
                }
                .event-select:focus {
                    outline: none;
                    border-color: #6366f1;
                    box-shadow: 0 0 15px rgba(99, 102, 241, 0.15);
                }
                .event-select-arrow {
                    position: absolute;
                    right: 16px;
                    color: #94a3b8;
                    pointer-events: none;
                    font-size: 0.9rem;
                }

                .kiosk-main {
                    flex: 1;
                    display: grid;
                    grid-template-columns: 1.25fr 1fr;
                    gap: 30px;
                    padding: 30px 40px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                }

                .kiosk-panel {
                    background: rgba(16, 22, 40, 0.45);
                    backdrop-filter: blur(25px);
                    border: 1px solid rgba(255, 255, 255, 0.05);
                    border-radius: 24px;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                    position: relative;
                }

                .scanner-panel {
                    justify-content: center;
                    align-items: center;
                    padding: 40px;
                }

                .kiosk-tabs {
                    background: rgba(255, 255, 255, 0.03);
                    padding: 5px;
                    border-radius: 50px;
                    display: inline-flex;
                    margin-bottom: 30px;
                    border: 1px solid rgba(255, 255, 255, 0.04);
                }
                .kiosk-tab-btn {
                    border: none;
                    background: transparent;
                    color: #94a3b8;
                    font-size: 0.88rem;
                    font-weight: 700;
                    padding: 10px 24px;
                    border-radius: 50px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.25s ease;
                }
                .kiosk-tab-btn.active {
                    background: #4f46e5;
                    color: #ffffff;
                    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.35);
                }

                .side-column {
                    display: flex;
                    flex-direction: column;
                    gap: 25px;
                    overflow: hidden;
                }

                .stats-panel {
                    background: linear-gradient(135deg, rgba(79, 70, 229, 0.12) 0%, rgba(16, 22, 40, 0.4) 100%);
                    border: 1px solid rgba(99, 102, 241, 0.2);
                    padding: 24px 30px;
                    text-align: center;
                    border-radius: 24px;
                    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
                }
                .stats-title {
                    font-size: 0.8rem;
                    text-transform: uppercase;
                    color: #a5b4fc;
                    font-weight: 700;
                    letter-spacing: 0.12em;
                    margin: 0 0 6px 0;
                }
                .stats-count {
                    font-size: 4.5rem;
                    font-weight: 900;
                    color: #ffffff;
                    line-height: 1;
                    margin: 0;
                    text-shadow: 0 0 30px rgba(99, 102, 241, 0.35);
                }

                .feed-panel {
                    flex: 1;
                    padding: 25px;
                    display: flex;
                    flex-direction: column;
                    min-height: 0;
                }
                .feed-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                    padding-bottom: 15px;
                }
                .feed-title {
                    font-size: 1rem;
                    font-weight: 700;
                    color: #f1f5f9;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin: 0;
                }
                .feed-title i {
                    color: #38bdf8;
                }
                .feed-count-badge {
                    font-size: 0.72rem;
                    background: rgba(56, 189, 248, 0.1);
                    color: #38bdf8;
                    padding: 4px 10px;
                    border-radius: 12px;
                    font-weight: 700;
                }

                .feed-list {
                    flex: 1;
                    overflow-y: auto;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    padding-right: 5px;
                }

                .feed-list::-webkit-scrollbar {
                    width: 5px;
                }
                .feed-list::-webkit-scrollbar-track {
                    background: transparent;
                }
                .feed-list::-webkit-scrollbar-thumb {
                    background: rgba(255, 255, 255, 0.06);
                    border-radius: 10px;
                }
                .feed-list::-webkit-scrollbar-thumb:hover {
                    background: rgba(255, 255, 255, 0.12);
                }

                .feed-item {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    padding: 10px 15px;
                    background: rgba(255, 255, 255, 0.02);
                    border: 1px solid rgba(255, 255, 255, 0.02);
                    border-radius: 14px;
                    transition: all 0.2s ease;
                }
                .feed-item:hover {
                    background: rgba(255, 255, 255, 0.04);
                    border-color: rgba(255, 255, 255, 0.05);
                    transform: translateX(4px);
                }

                .feed-photo {
                    width: 42px;
                    height: 42px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid rgba(255, 255, 255, 0.08);
                }
                .feed-avatar-placeholder {
                    width: 42px;
                    height: 42px;
                    border-radius: 50%;
                    background: rgba(99, 102, 241, 0.15);
                    color: #a5b4fc;
                    font-weight: 700;
                    font-size: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 2px solid rgba(99, 102, 241, 0.2);
                }

                .feed-details {
                    flex: 1;
                    min-width: 0;
                }
                .feed-name {
                    font-size: 0.9rem;
                    font-weight: 600;
                    color: #e2e8f0;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    margin: 0;
                }
                .feed-meta {
                    font-size: 0.78rem;
                    color: #94a3b8;
                    margin: 2px 0 0 0;
                }
                .feed-time {
                    font-size: 0.7rem;
                    color: #818cf8;
                    background: rgba(99, 102, 241, 0.08);
                    padding: 4px 8px;
                    border-radius: 10px;
                    font-weight: 600;
                }

                .kiosk-overlay {
                    position: absolute;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 100;
                    animation: fadeInOverlay-kf 0.22s ease-out forwards;
                }
                .overlay-success-bg {
                    background: rgba(5, 46, 22, 0.88);
                    backdrop-filter: blur(12px);
                }
                .overlay-error-bg {
                    background: rgba(69, 10, 10, 0.88);
                    backdrop-filter: blur(12px);
                }

                @keyframes fadeInOverlay-kf {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }

                .overlay-modal {
                    background: #090d1a;
                    border: 1px solid rgba(255, 255, 255, 0.06);
                    border-radius: 24px;
                    padding: 30px;
                    width: 90%;
                    max-width: 420px;
                    text-align: center;
                    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6);
                    transform: scale(0.9);
                    animation: scaleUpModal-kf 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
                }

                @keyframes scaleUpModal-kf {
                    to { transform: scale(1); }
                }

                .overlay-badge {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 72px;
                    height: 72px;
                    border-radius: 50%;
                    margin-bottom: 18px;
                }
                .overlay-badge.success {
                    background: rgba(16, 185, 129, 0.1);
                    color: #10b981;
                    border: 2px solid rgba(16, 185, 129, 0.25);
                }
                .overlay-badge.error {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                    border: 2px solid rgba(239, 68, 68, 0.25);
                }

                .overlay-msg {
                    font-size: 1.3rem;
                    font-weight: 800;
                    margin: 0 0 18px 0;
                    color: #ffffff;
                    line-height: 1.25;
                }

                .overlay-card {
                    background: rgba(255, 255, 255, 0.015);
                    border: 1px solid rgba(255, 255, 255, 0.04);
                    border-radius: 16px;
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    width: 100%;
                }
                .overlay-avatar {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-bottom: 12px;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
                }
                .overlay-avatar.success {
                    border: 3px solid #10b981;
                }
                .overlay-avatar.error {
                    border: 3px solid #ef4444;
                }

                .overlay-avatar-placeholder {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    font-size: 2rem;
                    font-weight: 700;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 12px;
                    color: #ffffff;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
                }
                .overlay-avatar-placeholder.success {
                    background: #10b981;
                    border: 3px solid #10b981;
                }
                .overlay-avatar-placeholder.error {
                    background: #ef4444;
                    border: 3px solid #ef4444;
                }

                .overlay-name {
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #ffffff;
                    margin: 0;
                }
                .overlay-doc {
                    font-size: 0.82rem;
                    color: #94a3b8;
                    margin: 4px 0 0 0;
                }

                .empty-state-card {
                    text-align: center;
                    max-width: 400px;
                    padding: 40px;
                    background: rgba(255, 255, 255, 0.015);
                    border: 1px solid rgba(255, 255, 255, 0.03);
                    border-radius: 20px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                .empty-state-icon {
                    font-size: 3.5rem;
                    color: #6366f1;
                    margin-bottom: 15px;
                    display: inline-block;
                    animation: floatIcon-kf 3s ease-in-out infinite;
                }

                @keyframes floatIcon-kf {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-8px); }
                }

                .empty-state-text {
                    font-size: 0.9rem;
                    color: #94a3b8;
                    line-height: 1.5;
                    margin-top: 10px;
                }

                /* Focus frame corners for Camera capture */
                .scanner-corner {
                    position: absolute;
                    width: 20px;
                    height: 20px;
                    border: 3px solid transparent;
                    z-index: 10;
                    pointer-events: none;
                }
                .corner-tl {
                    top: 15px;
                    left: 15px;
                    border-top-color: #6366f1;
                    border-left-color: #6366f1;
                    border-top-left-radius: 6px;
                }
                .corner-tr {
                    top: 15px;
                    right: 15px;
                    border-top-color: #6366f1;
                    border-right-color: #6366f1;
                    border-top-right-radius: 6px;
                }
                .corner-bl {
                    bottom: 15px;
                    left: 15px;
                    border-bottom-color: #6366f1;
                    border-left-color: #6366f1;
                    border-bottom-left-radius: 6px;
                }
                .corner-br {
                    bottom: 15px;
                    right: 15px;
                    border-bottom-color: #6366f1;
                    border-right-color: #6366f1;
                    border-bottom-right-radius: 6px;
                }

                /* Floating custom input styling */
                .input-group-custom {
                    position: relative;
                    margin-bottom: 15px;
                    width: 100%;
                }
                .input-custom {
                    width: 100%;
                    padding: 12px 16px;
                    background: rgba(15, 23, 42, 0.45);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 12px;
                    color: #ffffff;
                    font-size: 0.9rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }
                .input-custom:focus {
                    outline: none;
                    border-color: #6366f1;
                    box-shadow: 0 0 15px rgba(99, 102, 241, 0.15);
                    background: rgba(15, 23, 42, 0.6);
                }
                .input-label {
                    position: absolute;
                    left: 12px;
                    top: -10px;
                    background: #0d1222;
                    padding: 0 6px;
                    font-size: 0.7rem;
                    font-weight: 700;
                    color: #818cf8;
                    letter-spacing: 0.05em;
                    text-transform: uppercase;
                    border-radius: 4px;
                    border: 1px solid rgba(255, 255, 255, 0.03);
                }
            `}</style>

            <header className="kiosk-header">
                <div className="header-left">
                    <a href="/admin/actividades" className="btn-back">
                        <i className="ri-arrow-left-line"></i>
                        <span>Volver al Panel</span>
                    </a>
                    {actividadId && (
                        <div className="live-indicator">
                            <span className="pulse-dot"></span>
                            <span className="live-text">TERMINAL ACTIVA</span>
                        </div>
                    )}
                </div>

                <div className="header-center">
                    <h1>Control de Asistencias</h1>
                </div>

                <div className="header-right">
                    {!isLoadingActividades && actividades.length > 0 && (
                        <div className="event-selector-wrapper">
                            <i className="ri-calendar-event-line event-selector-icon"></i>
                            <select
                                className="event-select"
                                value={actividadId}
                                onChange={(e) => setActividadId(e.target.value)}
                                disabled={showRegisterForm && !isDedicatedCreateMode}
                            >
                                <option value="">-- Seleccionar Actividad --</option>
                                {actividades.map(act => (
                                    <option key={act.id} value={act.id}>
                                        {act.nombre}
                                    </option>
                                ))}
                            </select>
                            <i className="ri-arrow-down-s-line event-select-arrow"></i>
                        </div>
                    )}
                </div>
            </header>

            <main className="kiosk-main" style={showRegisterForm ? { gridTemplateColumns: '1fr' } : {}}>
                {isLoadingActividades ? (
                    <div className="col-span-2 d-flex flex-column align-items-center justify-content-center w-100" style={{ gridColumn: '1 / -1' }}>
                        <div className="spinner-border text-primary" role="status" style={{ width: '3rem', height: '3rem' }}></div>
                        <h5 className="mt-4 text-muted">Inicializando terminal...</h5>
                    </div>
                ) : actividades.length === 0 ? (
                    <div className="col-span-2 d-flex align-items-center justify-content-center w-100" style={{ gridColumn: '1 / -1' }}>
                        <div className="empty-state-card">
                            <i className="ri-alert-line empty-state-icon text-warning"></i>
                            <h4 className="text-white fw-bold">Sin actividades activas</h4>
                            <p className="empty-state-text">No hay actividades activas registradas para tomar asistencia hoy. Vuelva al panel para habilitar una actividad.</p>
                        </div>
                    </div>
                ) : !actividadId ? (
                    <div className="col-span-2 d-flex align-items-center justify-content-center w-100" style={{ gridColumn: '1 / -1' }}>
                        <div className="empty-state-card">
                            <i className="ri-hand-pointer-line empty-state-icon"></i>
                            <h4 className="text-white fw-bold">Seleccione Actividad</h4>
                            <p className="empty-state-text">Por favor, elija el evento o actividad correspondiente en el selector superior para habilitar el escáner.</p>
                        </div>
                    </div>
                ) : showRegisterForm ? (
                    /* Left/Right Split Express Registration Panel */
                    <div className="kiosk-panel registration-panel p-4" style={{ height: '100%', overflowY: 'auto' }}>
                        <div className="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                            <h4 className="text-white fw-bold m-0 d-flex align-items-center">
                                <i className="ri-user-add-line text-primary me-2 fs-4"></i> Registrar Nuevo Pastor y Asistencia
                            </h4>
                            {!isDedicatedCreateMode && (
                                <button
                                    type="button"
                                    className="btn-back"
                                    onClick={() => { setShowRegisterForm(false); resetRegisterForm(); }}
                                >
                                    <i className="ri-close-line"></i> Cancelar
                                </button>
                            )}
                        </div>

                        {registerError && (
                            <div className="alert alert-danger mb-4 p-3 border-0 bg-danger bg-opacity-10 text-danger rounded-3">
                                <i className="ri-error-warning-line me-2"></i> {registerError}
                            </div>
                        )}

                        <form onSubmit={handleRegisterNewPastor} className="row g-4">
                            {/* Left column: form controls (7/12) */}
                            <div className="col-12 col-lg-7">
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Nombres</span>
                                            <input
                                                type="text"
                                                className="input-custom text-white"
                                                required
                                                value={registerNombres}
                                                onChange={e => setRegisterNombres(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Apellidos</span>
                                            <input
                                                type="text"
                                                className="input-custom text-white"
                                                required
                                                value={registerApellidos}
                                                onChange={e => setRegisterApellidos(e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Documento</span>
                                            <input
                                                type="text"
                                                className="input-custom text-white"
                                                required
                                                value={registerDocumento}
                                                onChange={e => setRegisterDocumento(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Nivel Ministerial</span>
                                            <select
                                                className="input-custom text-white bg-dark"
                                                value={registerNivelMinisterial}
                                                onChange={e => setRegisterNivelMinisterial(e.target.value)}
                                            >
                                                <option value="">Seleccione Nivel</option>
                                                <option value="Colaborador">Colaborador</option>
                                                <option value="Laico">Laico</option>
                                                <option value="Licenciado">Licenciado</option>
                                                <option value="Ministro Ordenado">Ministro Ordenado</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Zona</span>
                                            <input
                                                type="text"
                                                className="input-custom text-white"
                                                value={registerZona}
                                                onChange={e => setRegisterZona(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Distrito</span>
                                            <input
                                                type="text"
                                                className="input-custom text-white"
                                                value={registerDistrito}
                                                onChange={e => setRegisterDistrito(e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Género</span>
                                            <select
                                                className="input-custom text-white bg-dark"
                                                value={registerGenero}
                                                onChange={e => setRegisterGenero(e.target.value)}
                                            >
                                                <option value="">Seleccione Género</option>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Teléfono Celular</span>
                                            <input
                                                type="text"
                                                className="input-custom text-white"
                                                placeholder="04121234567"
                                                value={registerTelefono}
                                                onChange={e => setRegisterTelefono(e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Fecha Nacimiento</span>
                                            <input
                                                type="date"
                                                className="input-custom text-white"
                                                value={registerFeNacimiento}
                                                onChange={handleBirthdateChange}
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="input-group-custom">
                                            <span className="input-label">Edad</span>
                                            <input
                                                type="number"
                                                className="input-custom text-white"
                                                value={registerEdad}
                                                onChange={e => setRegisterEdad(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Right column: webcam capture tool (5/12) */}
                            <div className="col-12 col-lg-5 d-flex flex-column align-items-center justify-content-center border-start border-secondary border-opacity-25 ps-lg-4">
                                <span className="text-white small fw-bold mb-3 text-uppercase tracking-wider">
                                    Fotografía Tipo Carnet
                                </span>

                                <div className="camera-box-wrapper mb-3" style={{
                                    width: '240px',
                                    height: '240px',
                                    borderRadius: '20px',
                                    overflow: 'hidden',
                                    position: 'relative',
                                    border: '2px solid rgba(255,255,255,0.08)',
                                    background: '#000'
                                }}>
                                    <div className="scanner-corner corner-tl"></div>
                                    <div className="scanner-corner corner-tr"></div>
                                    <div className="scanner-corner corner-bl"></div>
                                    <div className="scanner-corner corner-br"></div>

                                    {capturedPhoto ? (
                                        <img
                                            src={capturedPhoto}
                                            alt="Foto capturada"
                                            style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                        />
                                    ) : cameraStream ? (
                                        <video
                                            ref={(node) => {
                                                videoRef.current = node;
                                                if (node && cameraStream && node.srcObject !== cameraStream) {
                                                    node.srcObject = cameraStream;
                                                    node.play().catch(err => console.error("Video play error:", err));
                                                }
                                            }}
                                            autoPlay
                                            playsInline
                                            muted
                                            style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                        />
                                    ) : (
                                        <div className="d-flex flex-column align-items-center justify-content-center h-100 text-muted p-3 text-center">
                                            <i className="ri-camera-off-line fs-2 mb-2 text-secondary"></i>
                                            <small className="small opacity-75" style={{ fontSize: '0.75rem' }}>Cámara no conectada o sin permisos de acceso</small>
                                        </div>
                                    )}
                                </div>

                                <canvas ref={canvasRef} style={{ display: 'none' }}></canvas>

                                {capturedPhoto ? (
                                    <button type="button" className="btn btn-outline-info btn-sm px-4" onClick={retakePhoto}>
                                        <i className="ri-restart-line me-1"></i> Tomar Otra Foto
                                    </button>
                                ) : (
                                    <button
                                        type="button"
                                        className="btn btn-primary btn-sm px-4"
                                        onClick={capturePhoto}
                                        disabled={!cameraStream}
                                    >
                                        <i className="ri-camera-line me-1"></i> Capturar Foto
                                    </button>
                                )}
                            </div>

                            {/* Actions block */}
                            <div className="col-12 border-top border-secondary border-opacity-25 pt-4 d-flex justify-content-end gap-3">
                                {!isDedicatedCreateMode && (
                                    <button
                                        type="button"
                                        className="btn btn-outline-light"
                                        onClick={() => { setShowRegisterForm(false); resetRegisterForm(); }}
                                        disabled={registerIsSaving}
                                    >
                                        Cancelar
                                    </button>
                                )}
                                <button
                                    type="submit"
                                    className="btn btn-primary"
                                    disabled={registerIsSaving}
                                >
                                    {registerIsSaving ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Guardando Registro...
                                        </>
                                    ) : (
                                        <>
                                            <i className="ri-save-line me-1"></i> Registrar y Tomar Asistencia
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>
                ) : (
                    <>
                        {/* Left column: Scanner card */}
                        <div className="kiosk-panel scanner-panel">
                            <div className="kiosk-tabs">
                                <button
                                    className={`kiosk-tab-btn ${activeTab === 'qr' ? 'active' : ''}`}
                                    onClick={() => setActiveTab('qr')}
                                >
                                    <i className="ri-qr-code-line"></i> Escáner QR
                                </button>
                                <button
                                    className={`kiosk-tab-btn ${activeTab === 'manual' ? 'active' : ''}`}
                                    onClick={() => setActiveTab('manual')}
                                >
                                    <i className="ri-keyboard-line"></i> Entrada Manual
                                </button>
                            </div>

                            <div className="w-100 d-flex justify-content-center align-items-center" style={{ minHeight: '380px' }}>
                                {activeTab === 'qr' ? (
                                    <ScannerQR
                                        onScan={(qr_data) => handleRegister({ metodo: 'QR', qr_data })}
                                        isPaused={isLoading || statusMessage?.type === 'success'}
                                    />
                                ) : (
                                    <BuscadorCedula
                                        onSubmit={(cedula) => handleRegister({ metodo: 'Manual', cedula })}
                                        isLoading={isLoading}
                                    />
                                )}
                            </div>

                            {/* Full layout status response overlay */}
                            {statusMessage && (
                                <div className={`kiosk-overlay ${statusMessage.type === 'success' ? 'overlay-success-bg' : 'overlay-error-bg'}`}>
                                    <div className="overlay-modal">
                                        <div className={`overlay-badge ${statusMessage.type === 'success' ? 'success' : 'error'}`}>
                                            <i className={`ri-${statusMessage.type === 'success' ? 'checkbox-circle-fill' : 'error-warning-fill'}`} style={{ fontSize: '3rem' }}></i>
                                        </div>
                                        <h3 className="overlay-msg">{statusMessage.data.message}</h3>

                                        {statusMessage.data.pastor && (
                                            <div className="overlay-card">
                                                {statusMessage.data.pastor.foto ? (
                                                    <img
                                                        src={statusMessage.data.pastor.foto}
                                                        alt="Foto Pastor"
                                                        className={`overlay-avatar ${statusMessage.type === 'success' ? 'success' : 'error'}`}
                                                    />
                                                ) : (
                                                    <div className={`overlay-avatar-placeholder ${statusMessage.type === 'success' ? 'success' : 'error'}`}>
                                                        {statusMessage.data.pastor.nombre.charAt(0)}
                                                    </div>
                                                )}
                                                <h4 className="overlay-name">{statusMessage.data.pastor.nombre}</h4>
                                                <span className="overlay-doc">{statusMessage.data.pastor.documento}</span>
                                            </div>
                                        )}

                                        {/* Dynamic Fallback Action Button when Pastor is not found (404) */}
                                        {statusMessage.type === 'error' && statusMessage.data.message.includes('not found') || statusMessage.data.message.includes('encontrado') ? (
                                            <div className="mt-4 d-flex justify-content-center gap-3">
                                                <button
                                                    type="button"
                                                    className="btn btn-primary"
                                                    onClick={() => {
                                                        setRegisterDocumento(searchDocumento || '');
                                                        setShowRegisterForm(true);
                                                        setStatusMessage(null);
                                                    }}
                                                >
                                                    <i className="ri-user-add-line me-1"></i> Registrar Pastor
                                                </button>
                                                <button
                                                    type="button"
                                                    className="btn btn-outline-light"
                                                    onClick={() => setStatusMessage(null)}
                                                >
                                                    Cerrar
                                                </button>
                                            </div>
                                        ) : statusMessage.type === 'error' ? (
                                            <div className="mt-4">
                                                <button
                                                    type="button"
                                                    className="btn btn-outline-light btn-sm"
                                                    onClick={() => setStatusMessage(null)}
                                                >
                                                    Cerrar
                                                </button>
                                            </div>
                                        ) : null}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Right column: Stats and recent logs */}
                        <div className="side-column">
                            <div className="kiosk-panel stats-panel">
                                <h6 className="stats-title">Asistentes Registrados</h6>
                                <div className="stats-count">{estadisticas.total}</div>
                                <span className="text-muted small">Total acumulado en tiempo real</span>
                            </div>

                            <div className="kiosk-panel feed-panel">
                                <div className="feed-header">
                                    <h6 className="feed-title">
                                        <i className="ri-history-line"></i> Últimos Registros
                                    </h6>
                                    <span className="feed-count-badge">Historial</span>
                                </div>

                                <div className="feed-list">
                                    {estadisticas.recientes.length === 0 ? (
                                        <div className="text-muted text-center py-4 small">
                                            <i className="ri-inbox-line d-block fs-3 mb-2 opacity-50"></i>
                                            Sin registros cargados aún
                                        </div>
                                    ) : (
                                        estadisticas.recientes.map((pastor, idx) => (
                                            <div key={idx} className="feed-item">
                                                {pastor.foto ? (
                                                    <img src={pastor.foto} alt="Avatar" className="feed-photo" />
                                                ) : (
                                                    <div className="feed-avatar-placeholder">
                                                        {pastor.nombre.charAt(0)}
                                                    </div>
                                                )}
                                                <div className="feed-details">
                                                    <h6 className="feed-name">{pastor.nombre}</h6>
                                                    <p className="feed-meta">{pastor.documento}</p>
                                                </div>
                                                <div className="feed-time">
                                                    {pastor.fecha_hora}
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </main>
        </div>
    );
}
