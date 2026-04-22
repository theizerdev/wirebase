import React, { useEffect, useMemo, useState, useCallback } from 'react'
import { BrowserRouter, Routes, Route, Link, Navigate, useParams, useNavigate } from 'react-router-dom'
import * as Dialog from '@radix-ui/react-dialog'
import { Toaster, toast } from 'sonner'
import {
  Search, ChevronLeft, ChevronRight, CalendarDays, ArrowRight,
  FileText, DollarSign, AlertTriangle, CheckCircle2, Clock, Filter,
  CreditCard, Send, Loader2, TrendingUp, Eye, User, Shield, Monitor,
  KeyRound, Save, MapPin, Mail, IdCard, MessageSquare, Users, BarChart3,
  Settings, Phone, Image, Paperclip, Mic, Smile, MoreVertical,
  Check, CheckCheck, Circle, Download, Upload, File, Video, MapPin as MapPinIcon,
  PhoneCall, Info, X, QrCode, RefreshCw
} from 'lucide-react'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { zodResolver } from '@hookform/resolvers/zod'
import Layout from './Layout.jsx'

/* ── Helpers ── */
const fmt = (v) => Number(v || 0).toLocaleString('es', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || ''
const apiFetch = (url, opts = {}) => fetch(url, {
  credentials: 'same-origin',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf(), ...opts.headers },
  ...opts,
})

const statusColors = {
  sent: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
  delivered: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
  read: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
  failed: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
  pending: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
}

function Badge({ status, children }) {
  return (
    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ${statusColors[status] || 'bg-gray-100 text-gray-600'}`}>
      {children}
    </span>
  )
}

function Spinner() {
  return <div className="flex items-center justify-center py-12"><Loader2 className="h-8 w-8 animate-spin text-blue-600" /></div>
}

function ErrorBox({ message }) {
  return <div className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">{message}</div>
}

function EmptyState({ icon: Icon, title, description, action }) {
  return (
    <div className="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 px-6 py-12 text-center dark:border-gray-800">
      <Icon className="mb-3 h-12 w-12 text-gray-300 dark:text-gray-600" />
      <h3 className="text-sm font-semibold text-gray-900 dark:text-white">{title}</h3>
      {description && <p className="mt-1 text-sm text-gray-500">{description}</p>}
      {action}
    </div>
  )
}

function useWhatsAppAPI() {
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  
  const apiCall = useCallback(async (endpoint, options = {}) => {
    try {
      setLoading(true)
      setError(null)
      
      const response = await apiFetch(`/admin/whatsapp/api${endpoint}`, {
        ...options,
        headers: {
          ...options.headers
        }
      })
      
      if (!response.ok) {
        let payload = null
        try {
          payload = await response.json()
        } catch {
          payload = null
        }
        const message =
          payload?.error ||
          payload?.message ||
          `API Error: ${response.status}`
        const err = new Error(message)
        err.status = response.status
        err.payload = payload
        throw err
      }
      
      const data = await response.json()
      return data
    } catch (err) {
      setError(err.message)
      throw err
    } finally {
      setLoading(false)
    }
  }, [])
  
  return { apiCall, loading, error }
}

/* ════════════════════════════════════════════════════
   WHATSAPP CHAT COMPONENT
   ════════════════════════════════════════════════════ */
function WhatsAppChat() {
  const [conversations, setConversations] = useState([])
  const [selectedConversation, setSelectedConversation] = useState(null)
  const [messages, setMessages] = useState([])
  const [searchQuery, setSearchQuery] = useState('')
  const [activeFilter, setActiveFilter] = useState('all')
  const [isLoading, setIsLoading] = useState(true)
  const [isSending, setIsSending] = useState(false)
  const [messageText, setMessageText] = useState('')
  const [connectionStatus, setConnectionStatus] = useState('disconnected')
  const [qrOpen, setQrOpen] = useState(false)
  const [qrLoading, setQrLoading] = useState(false)
  const [qrDataUrl, setQrDataUrl] = useState(null)
  const { apiCall } = useWhatsAppAPI()

  // Load conversations
  const loadConversations = useCallback(async () => {
    try {
      const data = await apiCall('/conversations')
      setConversations(data.conversations || [])
    } catch (err) {
      toast.error('Error al cargar conversaciones')
    }
  }, [apiCall])

  // Load messages for selected conversation
  const loadMessages = useCallback(async (peer) => {
    if (!peer) return
    
    try {
      const data = await apiCall(`/thread?peer=${encodeURIComponent(peer)}`)
      setMessages(data.messages || [])
    } catch (err) {
      toast.error('Error al cargar mensajes')
    }
  }, [apiCall])

  const refreshStatus = useCallback(async () => {
    try {
      const data = await apiCall('/status')
      setConnectionStatus(data.connectionState || 'disconnected')
      return data
    } catch (err) {
      setConnectionStatus('error')
      return null
    }
  }, [apiCall])

  const loadQr = useCallback(async () => {
    setQrLoading(true)
    try {
      const qrResp = await apiCall('/qr')
      if (qrResp?.success && qrResp?.qr) {
        setQrDataUrl(qrResp.qr)
      } else {
        setQrDataUrl(null)
        if (qrResp?.connectionState) {
          setConnectionStatus(qrResp.connectionState)
        }
      }
    } catch {
      setQrDataUrl(null)
    } finally {
      setQrLoading(false)
    }
  }, [apiCall])

  const openQr = useCallback(async () => {
    setQrOpen(true)
    const status = await refreshStatus()
    if (!status || status?.connectionState === 'connected') return

    try {
      await apiCall('/connect', { method: 'POST', body: JSON.stringify({}) })
    } catch (err) {
      toast.error(err?.message || 'No se pudo iniciar la conexión')
    }

    await loadQr()
  }, [apiCall, loadQr, refreshStatus])

  // Send message
  const sendMessage = async () => {
    if (!messageText.trim() || !selectedConversation) return
    if (connectionStatus !== 'connected') {
      toast.error('WhatsApp no está conectado')
      await openQr()
      return
    }

    setIsSending(true)
    try {
      await apiCall('/send', {
        method: 'POST',
        body: JSON.stringify({
          to: selectedConversation.peer,
          message: messageText.trim(),
          type: 'text'
        })
      })
      
      setMessageText('')
      loadMessages(selectedConversation.peer)
      toast.success('Mensaje enviado')
    } catch (err) {
      toast.error('Error al enviar mensaje')
    } finally {
      setIsSending(false)
    }
  }

  // Format phone number for display
  const formatPhoneNumber = (peer) => {
    const phone = peer.replace(/@.*$/, '')
    const cleanNumber = phone.replace(/\D/g, '')
    
    if (cleanNumber.length === 11 && cleanNumber.startsWith('58')) {
      return `+58 ${cleanNumber.slice(2, 5)} ${cleanNumber.slice(5, 8)} ${cleanNumber.slice(8)}`
    } else if (cleanNumber.length === 10) {
      return `${cleanNumber.slice(0, 3)} ${cleanNumber.slice(3, 6)} ${cleanNumber.slice(6)}`
    }
    
    return cleanNumber
  }

  // Get display name for conversation
  const getDisplayName = (conv) => {
    if (conv.name && conv.name !== conv.peer) return conv.name
    
    if (conv.peer.includes('@g.us')) return `Grupo ${conv.peer.replace(/@.*$/, '')}`
    if (conv.peer.includes('@newsletter')) return `Newsletter ${conv.peer.replace(/@.*$/, '')}`
    if (conv.peer.includes('@lid')) return `LID ${conv.peer.replace(/@.*$/, '')}`
    
    return formatPhoneNumber(conv.peer)
  }

  // Filter conversations
  const filteredConversations = useMemo(() => {
    let filtered = conversations
    
    if (searchQuery) {
      const query = searchQuery.toLowerCase()
      filtered = filtered.filter(conv => 
        getDisplayName(conv).toLowerCase().includes(query) ||
        (conv.lastMessage || '').toLowerCase().includes(query)
      )
    }
    
    if (activeFilter === 'unread') {
      filtered = filtered.filter(conv => (conv.unreadCount || 0) > 0)
    } else if (activeFilter === 'groups') {
      filtered = filtered.filter(conv => conv.peer.includes('@g.us'))
    } else if (activeFilter === 'contacts') {
      filtered = filtered.filter(conv => 
        !conv.peer.includes('@g.us') && 
        !conv.peer.includes('@newsletter') && 
        !conv.peer.includes('@lid')
      )
    }
    
    return filtered
  }, [conversations, searchQuery, activeFilter])

  // Initialize
  useEffect(() => {
    const init = async () => {
      setIsLoading(true)
      await Promise.all([
        refreshStatus(),
        loadConversations()
      ])
      setIsLoading(false)
    }
    init()
  }, [])

  // Load messages when conversation is selected
  useEffect(() => {
    if (selectedConversation) {
      loadMessages(selectedConversation.peer)
    }
  }, [selectedConversation])

  useEffect(() => {
    if (!qrOpen) return
    if (connectionStatus === 'connected') return
    if (qrDataUrl) return

    let cancelled = false
    const id = setInterval(async () => {
      if (cancelled) return
      const status = await refreshStatus()
      if (status?.connectionState === 'connected') return
      await loadQr()
    }, 1500)

    return () => {
      cancelled = true
      clearInterval(id)
    }
  }, [qrOpen, connectionStatus, qrDataUrl, loadQr, refreshStatus])

  const getStatusIcon = () => {
    switch (connectionStatus) {
      case 'connected': return <CheckCircle2 className="h-4 w-4 text-green-500" />
      case 'connecting': return <Loader2 className="h-4 w-4 animate-spin text-yellow-500" />
      case 'qr_ready': return <div className="h-4 w-4 bg-yellow-500 rounded" />
      default: return <Circle className="h-4 w-4 text-red-500" />
    }
  }

  return (
    <div className="flex h-screen bg-gray-50 dark:bg-gray-950">
      {/* Sidebar - Conversations */}
      <div className="w-80 border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 flex flex-col">
        {/* Header */}
        <div className="p-4 border-b border-gray-200 dark:border-gray-800">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">WhatsApp</h2>
            <div className="flex items-center gap-2">
              {getStatusIcon()}
              <span className="text-xs text-gray-500 capitalize">{connectionStatus}</span>
              <Dialog.Root open={qrOpen} onOpenChange={setQrOpen}>
                <Dialog.Trigger asChild>
                  <button
                    type="button"
                    onClick={openQr}
                    className="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                  >
                    <QrCode className="h-3.5 w-3.5" />
                    Conectar
                  </button>
                </Dialog.Trigger>
                <Dialog.Portal>
                  <Dialog.Overlay className="fixed inset-0 z-50 bg-black/50" />
                  <Dialog.Content className="fixed left-1/2 top-1/2 z-50 w-[92vw] max-w-md -translate-x-1/2 -translate-y-1/2 rounded-xl border border-gray-200 bg-white p-5 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                    <div className="flex items-start justify-between gap-3">
                      <div>
                        <Dialog.Title className="text-base font-semibold text-gray-900 dark:text-white">
                          Conectar WhatsApp
                        </Dialog.Title>
                        <Dialog.Description className="mt-1 text-sm text-gray-500">
                          Escanea el QR desde WhatsApp en tu teléfono.
                        </Dialog.Description>
                      </div>
                      <Dialog.Close asChild>
                        <button
                          type="button"
                          className="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800"
                        >
                          <X className="h-5 w-5" />
                        </button>
                      </Dialog.Close>
                    </div>

                    <div className="mt-4">
                      {qrDataUrl ? (
                        <div className="flex items-center justify-center rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                          <img src={qrDataUrl} alt="WhatsApp QR" className="h-64 w-64 rounded-lg" />
                        </div>
                      ) : (
                        <div className="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                          {qrLoading
                            ? 'Cargando QR...'
                            : connectionStatus === 'connecting'
                              ? 'Conectando... espera unos segundos y presiona “Actualizar”.'
                              : connectionStatus === 'qr_ready'
                                ? 'QR listo, presiona “Actualizar”.'
                                : connectionStatus === 'connected'
                                  ? 'WhatsApp ya está conectado.'
                                  : 'QR no disponible. Presiona “Actualizar”.'}
                        </div>
                      )}
                    </div>

                    <div className="mt-4 flex items-center justify-between gap-2">
                      <button
                        type="button"
                        onClick={async () => {
                          setQrDataUrl(null)
                          await refreshStatus()
                          await loadQr()
                        }}
                        disabled={qrLoading}
                        className="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                      >
                        {qrLoading ? <Loader2 className="h-4 w-4 animate-spin" /> : <RefreshCw className="h-4 w-4" />}
                        Actualizar
                      </button>
                      <button
                        type="button"
                        onClick={async () => {
                          try {
                            await apiCall('/disconnect', { method: 'DELETE' })
                            setQrDataUrl(null)
                            await refreshStatus()
                            toast.success('Desconectado')
                          } catch (err) {
                            toast.error(err?.message || 'No se pudo desconectar')
                          }
                        }}
                        className="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                      >
                        Desconectar
                      </button>
                    </div>
                  </Dialog.Content>
                </Dialog.Portal>
              </Dialog.Root>
            </div>
          </div>
          
          {/* Search */}
          <div className="relative">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input
              type="text"
              placeholder="Buscar conversación..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800"
            />
          </div>
          
          {/* Filters */}
          <div className="flex gap-2 mt-3">
            {[{id: 'all', label: 'Todos'}, {id: 'unread', label: 'No leídos'}, {id: 'groups', label: 'Grupos'}, {id: 'contacts', label: 'Contactos'}].map(filter => (
              <button
                key={filter.id}
                onClick={() => setActiveFilter(filter.id)}
                className={`px-3 py-1.5 text-xs rounded-lg transition ${
                  activeFilter === filter.id
                    ? 'bg-green-600 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'
                }`}
              >
                {filter.label}
              </button>
            ))}
          </div>
        </div>
        
        {/* Conversations List */}
        <div className="flex-1 overflow-y-auto">
          {isLoading ? (
            <div className="p-4 text-center text-gray-500">Cargando...</div>
          ) : filteredConversations.length === 0 ? (
            <EmptyState
              icon={MessageSquare}
              title="Sin conversaciones"
              description={searchQuery ? 'No se encontraron conversaciones' : 'No hay conversaciones disponibles'}
            />
          ) : (
            <div className="divide-y divide-gray-100 dark:divide-gray-800">
              {filteredConversations.map((conv) => (
                <div
                  key={conv.peer}
                  onClick={() => setSelectedConversation(conv)}
                  className={`p-4 cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-800 ${
                    selectedConversation?.peer === conv.peer ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-600' : ''
                  }`}
                >
                  <div className="flex items-start gap-3">
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-600 text-white font-semibold">
                      {getDisplayName(conv).charAt(0).toUpperCase()}
                    </div>
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between">
                        <h3 className="text-sm font-medium text-gray-900 dark:text-white truncate">
                          {getDisplayName(conv)}
                        </h3>
                        <span className="text-xs text-gray-500">
                          {conv.lastMessageTimestamp ? new Date(conv.lastMessageTimestamp).toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' }) : ''}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600 dark:text-gray-400 truncate mt-1">
                        {conv.lastMessage || 'Sin mensajes'}
                      </p>
                      {(conv.unreadCount || 0) > 0 && (
                        <div className="mt-2">
                          <Badge status="unread">{conv.unreadCount} no leídos</Badge>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
      
      {/* Chat Area */}
      <div className="flex-1 flex flex-col">
        {selectedConversation ? (
          <>
            {/* Chat Header */}
            <div className="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <div className="flex h-10 w-10 items-center justify-center rounded-full bg-green-600 text-white font-semibold">
                    {getDisplayName(selectedConversation).charAt(0).toUpperCase()}
                  </div>
                  <div>
                    <h3 className="text-sm font-medium text-gray-900 dark:text-white">
                      {getDisplayName(selectedConversation)}
                    </h3>
                    <p className="text-xs text-gray-500">
                      {selectedConversation.peer.includes('@g.us') ? 'Grupo' : 'Contacto'}
                    </p>
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  <button className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <Phone className="h-4 w-4" />
                  </button>
                  <button className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <Video className="h-4 w-4" />
                  </button>
                  <button className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <Info className="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>
            
            {/* Messages */}
            <div className="flex-1 overflow-y-auto p-4 space-y-4">
              {messages.length === 0 ? (
                <EmptyState
                  icon={MessageSquare}
                  title="Sin mensajes"
                  description="Envía un mensaje para iniciar la conversación"
                />
              ) : (
                messages.map((msg, index) => (
                  <div
                    key={index}
                    className={`flex ${msg.isOutgoing ? 'justify-end' : 'justify-start'}`}
                  >
                    <div className={`max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                      msg.isOutgoing
                        ? 'bg-green-600 text-white'
                        : 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                    }`}>
                      <p className="text-sm">{msg.text}</p>
                      <p className={`text-xs mt-1 ${
                        msg.isOutgoing ? 'text-green-100' : 'text-gray-500'
                      }`}>
                        {new Date(msg.timestamp).toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' })}
                      </p>
                    </div>
                  </div>
                ))
              )}
            </div>
            
            {/* Message Input */}
            <div className="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
              <div className="flex items-center gap-2">
                <button className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                  <Paperclip className="h-5 w-5 text-gray-500" />
                </button>
                <input
                  type="text"
                  value={messageText}
                  onChange={(e) => setMessageText(e.target.value)}
                  onKeyPress={(e) => e.key === 'Enter' && !e.shiftKey && sendMessage()}
                  placeholder="Escribe un mensaje..."
                  disabled={connectionStatus !== 'connected' || isSending}
                  className="flex-1 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-gray-700 dark:bg-gray-800 disabled:opacity-50"
                />
                <button
                  onClick={sendMessage}
                  disabled={!messageText.trim() || connectionStatus !== 'connected' || isSending}
                  className="p-2 rounded-lg bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                  {isSending ? (
                    <Loader2 className="h-5 w-5 animate-spin" />
                  ) : (
                    <Send className="h-5 w-5" />
                  )}
                </button>
              </div>
            </div>
          </>
        ) : (
          <div className="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-950">
            <EmptyState
              icon={MessageSquare}
              title="WhatsApp Chat"
              description="Selecciona una conversación para comenzar a chatear"
            />
          </div>
        )}
      </div>
    </div>
  )
}

/* ════════════════════════════════════════════════════
   CONTACTS COMPONENT
   ════════════════════════════════════════════════════ */
function Contacts() {
  const [contacts, setContacts] = useState([])
  const [searchQuery, setSearchQuery] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const { apiCall } = useWhatsAppAPI()

  const loadContacts = useCallback(async () => {
    try {
      const data = await apiCall('/contacts')
      setContacts(data.contacts || [])
    } catch (err) {
      toast.error('Error al cargar contactos')
    } finally {
      setIsLoading(false)
    }
  }, [apiCall])

  useEffect(() => {
    loadContacts()
  }, [])

  const filteredContacts = useMemo(() => {
    if (!searchQuery) return contacts
    const query = searchQuery.toLowerCase()
    return contacts.filter(contact =>
      (contact.name || '').toLowerCase().includes(query) ||
      (contact.number || '').includes(query)
    )
  }, [contacts, searchQuery])

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Contactos</h2>
        <p className="text-sm text-gray-500">Gestiona tus contactos de WhatsApp</p>
      </div>

      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          <input
            type="text"
            placeholder="Buscar contacto..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 sm:w-64"
          />
        </div>
      </div>

      {isLoading && <Spinner />}

      {!isLoading && filteredContacts.length === 0 && (
        <EmptyState
          icon={Users}
          title="Sin contactos"
          description={searchQuery ? 'No se encontraron contactos' : 'No hay contactos disponibles'}
        />
      )}

      {!isLoading && filteredContacts.length > 0 && (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          {filteredContacts.map((contact) => (
            <div key={contact.id} className="rounded-xl border border-gray-200 bg-white p-4 transition hover:border-green-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-green-600">
              <div className="flex items-center gap-3">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white font-semibold">
                  {(contact.name || contact.number || '?').charAt(0).toUpperCase()}
                </div>
                <div className="flex-1 min-w-0">
                  <h3 className="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {contact.name || 'Sin nombre'}
                  </h3>
                  <p className="text-sm text-gray-500">
                    {contact.number || 'Sin número'}
                  </p>
                </div>
              </div>
              <div className="mt-4 flex gap-2">
                <button className="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-green-700">
                  <MessageSquare className="h-4 w-4" />
                  Chatear
                </button>
                <button className="p-2 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                  <Phone className="h-4 w-4" />
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}

/* ════════════════════════════════════════════════════
   SEND MESSAGES COMPONENT
   ════════════════════════════════════════════════════ */
function SendMessages() {
  const [contacts, setContacts] = useState([])
  const [isLoading, setIsLoading] = useState(false)
  const [connectionStatus, setConnectionStatus] = useState(null)
  const [qrOpen, setQrOpen] = useState(false)
  const [qrLoading, setQrLoading] = useState(false)
  const [qrDataUrl, setQrDataUrl] = useState(null)
  const { apiCall } = useWhatsAppAPI()

  const schema = z.object({
    to: z.string().min(1, 'Selecciona un contacto'),
    message: z.string().min(2, 'Mínimo 2 caracteres').max(4096, 'Máximo 4096 caracteres'),
    type: z.enum(['text', 'image', 'document', 'audio']),
  })

  const { register, handleSubmit, setValue, watch, reset, formState: { errors, isSubmitting } } = useForm({ 
    resolver: zodResolver(schema),
    defaultValues: { type: 'text' }
  })

  const watchType = watch('type')

  const loadContacts = useCallback(async () => {
    try {
      const data = await apiCall('/contacts')
      setContacts(data.contacts || [])
    } catch (err) {
      toast.error('Error al cargar contactos')
    }
  }, [apiCall])

  useEffect(() => {
    loadContacts()
  }, [])

  const refreshStatus = useCallback(async () => {
    try {
      const status = await apiCall('/status')
      setConnectionStatus(status?.connectionState || null)
      return status
    } catch {
      setConnectionStatus('error')
      return null
    }
  }, [apiCall])

  const loadQr = useCallback(async () => {
    setQrLoading(true)
    try {
      const qrResp = await apiCall('/qr')
      if (qrResp?.success && qrResp?.qr) {
        setQrDataUrl(qrResp.qr)
      } else {
        setQrDataUrl(null)
        if (qrResp?.connectionState) {
          setConnectionStatus(qrResp.connectionState)
        }
        if (qrResp?.connectionState && ['connecting', 'qr_ready'].includes(qrResp.connectionState)) {
          return
        }
        toast.error(qrResp?.error || 'QR no disponible')
      }
    } catch (err) {
      setQrDataUrl(null)
      toast.error(err?.message || 'QR no disponible')
    } finally {
      setQrLoading(false)
    }
  }, [apiCall])

  const openQr = useCallback(async () => {
    setQrOpen(true)
    const status = await refreshStatus()
    if (!status || status?.connectionState === 'connected') return
    try {
      await apiCall('/connect', { method: 'POST', body: JSON.stringify({}) })
    } catch (err) {
      toast.error(err?.message || 'No se pudo iniciar la conexión')
    }
    await loadQr()
  }, [apiCall, loadQr, refreshStatus])

  useEffect(() => {
    refreshStatus()
  }, [])

  useEffect(() => {
    if (!qrOpen) return
    if (connectionStatus === 'connected') return
    if (qrDataUrl) return

    let cancelled = false
    const id = setInterval(async () => {
      if (cancelled) return
      const status = await refreshStatus()
      if (status?.connectionState === 'connected') return
      await loadQr()
    }, 1500)

    return () => {
      cancelled = true
      clearInterval(id)
    }
  }, [qrOpen, connectionStatus, qrDataUrl, loadQr, refreshStatus])

  const onSubmit = async (data) => {
    setIsLoading(true)
    try {
      const status = await refreshStatus()
      if (status?.connectionState !== 'connected') {
        toast.error('WhatsApp no está conectado')
        setQrOpen(true)
        await openQr()
        return
      }

      await apiCall('/send', {
        method: 'POST',
        body: JSON.stringify(data)
      })
      
      toast.success('Mensaje enviado exitosamente')
      reset()
    } catch (err) {
      toast.error(err?.message || 'Error al enviar mensaje')
    } finally {
      setIsLoading(false)
    }
  }

  const inputClass = "w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-gray-700 dark:bg-gray-900"
  const labelClass = "block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Enviar Mensaje</h2>
        <div className="mt-1 flex items-center justify-between gap-3">
          <p className="text-sm text-gray-500">Envía mensajes de WhatsApp a tus contactos</p>
          <div className="flex items-center gap-2">
            <span className="text-xs text-gray-500">
              Estado: <span className="capitalize">{connectionStatus || '...'}</span>
            </span>
            <Dialog.Root open={qrOpen} onOpenChange={setQrOpen}>
              <Dialog.Trigger asChild>
                <button
                  type="button"
                  onClick={openQr}
                  className="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                  <QrCode className="h-4 w-4" />
                  Conectar
                </button>
              </Dialog.Trigger>
              <Dialog.Portal>
                <Dialog.Overlay className="fixed inset-0 z-50 bg-black/50" />
                <Dialog.Content className="fixed left-1/2 top-1/2 z-50 w-[92vw] max-w-md -translate-x-1/2 -translate-y-1/2 rounded-xl border border-gray-200 bg-white p-5 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                  <div className="flex items-start justify-between gap-3">
                    <div>
                      <Dialog.Title className="text-base font-semibold text-gray-900 dark:text-white">
                        Conectar WhatsApp
                      </Dialog.Title>
                      <Dialog.Description className="mt-1 text-sm text-gray-500">
                        Escanea el QR desde WhatsApp en tu teléfono.
                      </Dialog.Description>
                    </div>
                    <Dialog.Close asChild>
                      <button
                        type="button"
                        className="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800"
                      >
                        <X className="h-5 w-5" />
                      </button>
                    </Dialog.Close>
                  </div>

                  <div className="mt-4">
                    {qrDataUrl ? (
                      <div className="flex items-center justify-center rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                        <img src={qrDataUrl} alt="WhatsApp QR" className="h-64 w-64 rounded-lg" />
                      </div>
                    ) : (
                      <div className="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                        {qrLoading
                          ? 'Cargando QR...'
                          : connectionStatus === 'connecting'
                            ? 'Conectando... espera unos segundos y presiona “Actualizar”.'
                            : connectionStatus === 'qr_ready'
                              ? 'QR listo, presiona “Actualizar”.'
                              : connectionStatus === 'connected'
                                ? 'WhatsApp ya está conectado.'
                                : 'QR no disponible. Presiona “Actualizar”.'}
                      </div>
                    )}
                  </div>

                  <div className="mt-4 flex items-center justify-between gap-2">
                    <button
                      type="button"
                      onClick={async () => {
                        setQrDataUrl(null)
                        await refreshStatus()
                        await loadQr()
                      }}
                      disabled={qrLoading}
                      className="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                    >
                      {qrLoading ? <Loader2 className="h-4 w-4 animate-spin" /> : <RefreshCw className="h-4 w-4" />}
                      Actualizar
                    </button>
                    <button
                      type="button"
                      onClick={async () => {
                        try {
                          await apiCall('/disconnect', { method: 'DELETE' })
                          setQrDataUrl(null)
                          await refreshStatus()
                          toast.success('Desconectado')
                        } catch (err) {
                          toast.error(err?.message || 'No se pudo desconectar')
                        }
                      }}
                      className="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                    >
                      Desconectar
                    </button>
                  </div>
                </Dialog.Content>
              </Dialog.Portal>
            </Dialog.Root>
          </div>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div className="space-y-5">
          <div>
            <label className={labelClass}>Tipo de Mensaje</label>
            <select {...register('type')} className={inputClass}>
              <option value="text">Texto</option>
              <option value="image">Imagen</option>
              <option value="document">Documento</option>
              <option value="audio">Audio</option>
            </select>
          </div>

          <div>
            <label className={labelClass}>Contacto</label>
            <select {...register('to')} className={inputClass}>
              <option value="">Seleccionar contacto...</option>
              {contacts.map((contact) => (
                <option key={contact.id} value={contact.number || contact.peer}>
                  {contact.name || contact.number}
                </option>
              ))}
            </select>
            {errors.to && <p className="mt-1 text-xs text-red-600">{errors.to.message}</p>}
          </div>

          <div>
            <label className={labelClass}>Mensaje</label>
            <textarea
              {...register('message')}
              rows={4}
              placeholder="Escribe tu mensaje aquí..."
              className={inputClass + ' resize-none'}
            />
            {errors.message && <p className="mt-1 text-xs text-red-600">{errors.message.message}</p>}
          </div>

          <div className="flex justify-end border-t border-gray-100 pt-5 dark:border-gray-800">
            <button
              type="submit"
              disabled={isSubmitting || isLoading}
              className="inline-flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-green-700 disabled:opacity-50"
            >
              {isLoading ? (
                <Loader2 className="h-4 w-4 animate-spin" />
              ) : (
                <Send className="h-4 w-4" />
              )}
              Enviar Mensaje
            </button>
          </div>
        </div>
      </form>
    </div>
  )
}

/* ════════════════════════════════════════════════════
   STATISTICS COMPONENT
   ════════════════════════════════════════════════════ */
function Statistics() {
  const [stats, setStats] = useState(null)
  const [isLoading, setIsLoading] = useState(true)
  const { apiCall } = useWhatsAppAPI()

  const loadStats = useCallback(async () => {
    try {
      const data = await apiCall('/stats')
      setStats(data.stats || {})
    } catch (err) {
      toast.error('Error al cargar estadísticas')
    } finally {
      setIsLoading(false)
    }
  }, [apiCall])

  useEffect(() => {
    loadStats()
  }, [])

  if (isLoading) return <Spinner />
  if (!stats) return <ErrorBox message="No se pudieron cargar las estadísticas" />

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Estadísticas</h2>
        <p className="text-sm text-gray-500">Métricas de WhatsApp</p>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div className="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Mensajes Enviados</p>
              <p className="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{stats.sent || 0}</p>
            </div>
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30">
              <Send className="h-5 w-5" />
            </div>
          </div>
        </div>

        <div className="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Mensajes Recibidos</p>
              <p className="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{stats.received || 0}</p>
            </div>
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900/30">
              <MessageSquare className="h-5 w-5" />
            </div>
          </div>
        </div>

        <div className="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Contactos Activos</p>
              <p className="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{stats.contacts || 0}</p>
            </div>
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/30">
              <Users className="h-5 w-5" />
            </div>
          </div>
        </div>

        <div className="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Tasa de Entrega</p>
              <p className="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{stats.deliveryRate || '0%'}</p>
            </div>
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30">
              <TrendingUp className="h-5 w-5" />
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

/* ════════════════════════════════════════════════════
   SETTINGS COMPONENT
   ════════════════════════════════════════════════════ */
function WhatsAppSettings() {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">Configuración</h2>
        <p className="text-sm text-gray-500">Configuración de WhatsApp</p>
      </div>

      <div className="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Configuración General</h3>
        <p className="text-gray-500">Las opciones de configuración aparecerán aquí.</p>
      </div>
    </div>
  )
}

/* ════════════════════════════════════════════════════
   APP ROOT
   ════════════════════════════════════════════════════ */
export default function App() {
  return (
    <BrowserRouter basename="/admin/whatsapp/chat">
      <Routes>
        <Route element={<Layout />}>
          <Route path="/" element={<WhatsAppChat />} />
          <Route path="/contacts" element={<Contacts />} />
          <Route path="/send" element={<SendMessages />} />
          <Route path="/stats" element={<Statistics />} />
          <Route path="/settings" element={<WhatsAppSettings />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
      <Toaster position="top-right" richColors />
    </BrowserRouter>
  )
}
