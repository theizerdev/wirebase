import React, { useState, useEffect, useCallback } from 'react'
import { Outlet, NavLink, Link, useLocation } from 'react-router-dom'
import * as DropdownMenu from '@radix-ui/react-dropdown-menu'
import {
  Menu,
  X,
  Sun,
  Moon,
  MessageSquare,
  Users,
  Send,
  BarChart3,
  Settings,
  LogOut,
  ChevronLeft,
  MessageCircle,
  Phone,
  User,
  Shield,
  Monitor,
  KeyRound,
  Save,
  MapPin,
  Mail,
  IdCard
} from 'lucide-react'

const navItems = [
  { label: 'Chat', icon: MessageSquare, to: '/' },
  { label: 'Contactos', icon: Users, to: '/contacts' },
  { label: 'Enviar Mensajes', icon: Send, to: '/send' },
  { label: 'Estadísticas', icon: BarChart3, to: '/stats' },
  { label: 'Configuración', icon: Settings, to: '/settings' },
]

function useTheme() {
  const [dark, setDark] = useState(() => {
    if (typeof window === 'undefined') return false
    const stored = localStorage.getItem('theme')
    if (stored) return stored === 'dark'
    return document.documentElement.classList.contains('dark')
  })

  useEffect(() => {
    const el = document.documentElement
    if (dark) {
      el.classList.add('dark')
    } else {
      el.classList.remove('dark')
    }
    localStorage.setItem('theme', dark ? 'dark' : 'light')
  }, [dark])

  const toggle = useCallback(() => setDark(v => !v), [])
  return { dark, toggle }
}

export default function Layout() {
  const [sidebarOpen, setSidebarOpen] = useState(true)
  const [mobileOpen, setMobileOpen] = useState(false)
  const { dark, toggle: toggleTheme } = useTheme()
  const location = useLocation()

  // Close mobile sidebar on route change
  useEffect(() => {
    setMobileOpen(false)
  }, [location.pathname])

  // Close mobile sidebar on resize to desktop
  useEffect(() => {
    const mq = window.matchMedia('(min-width: 1024px)')
    const handler = (e) => {
      if (e.matches) setMobileOpen(false)
    }
    mq.addEventListener('change', handler)
    return () => mq.removeEventListener('change', handler)
  }, [])

  const navLinkClass = ({ isActive }) =>
    `group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 ${
      isActive
        ? 'bg-green-600 text-white shadow-sm'
        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800'
    }`

  function SidebarContent({ collapsed = false, onLinkClick }) {
    return (
      <div className="flex h-full flex-col">
        {/* Logo */}
        <div className="flex h-16 items-center gap-3 border-b border-gray-200 px-4 dark:border-gray-800">
          <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-600 text-white">
            <MessageCircle className="h-5 w-5" />
          </div>
          {!collapsed && (
            <span className="text-lg font-bold tracking-tight text-gray-900 dark:text-white">
              WhatsApp
            </span>
          )}
        </div>

        {/* Navigation */}
        <nav className="flex-1 space-y-1 px-3 py-4">
          {navItems.map(({ label, icon: Icon, to }) => (
            <NavLink
              key={to}
              to={to}
              end={to === '/'}
              className={navLinkClass}
              onClick={onLinkClick}
              title={collapsed ? label : undefined}
            >
              <Icon className="h-5 w-5 shrink-0" />
              {!collapsed && <span>{label}</span>}
            </NavLink>
          ))}
        </nav>

        {/* Collapse toggle — desktop only */}
        {!onLinkClick && (
          <div className="border-t border-gray-200 px-3 py-3 dark:border-gray-800">
            <button
              onClick={() => setSidebarOpen(v => !v)}
              className="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800"
              aria-label={sidebarOpen ? 'Colapsar menú' : 'Expandir menú'}
            >
              <ChevronLeft
                className={`h-4 w-4 transition-transform duration-200 ${
                  !sidebarOpen ? 'rotate-180' : ''
                }`}
              />
              {!collapsed && <span>Colapsar</span>}
            </button>
          </div>
        )}
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors">
      {/* ── Mobile backdrop ── */}
      {mobileOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity lg:hidden"
          onClick={() => setMobileOpen(false)}
          aria-hidden="true"
        />
      )}

      {/* ── Mobile sidebar ── */}
      <aside
        className={`fixed inset-y-0 left-0 z-50 w-64 transform bg-white shadow-2xl transition-transform duration-300 ease-in-out dark:bg-gray-900 lg:hidden ${
          mobileOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="absolute right-2 top-3">
          <button
            onClick={() => setMobileOpen(false)}
            className="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
            aria-label="Cerrar menú"
          >
            <X className="h-5 w-5" />
          </button>
        </div>
        <SidebarContent onLinkClick={() => setMobileOpen(false)} />
      </aside>

      {/* ── Desktop sidebar ── */}
      <aside
        className={`fixed inset-y-0 left-0 z-30 hidden border-r border-gray-200 bg-white transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900 lg:block ${
          sidebarOpen ? 'w-64' : 'w-16'
        }`}
      >
        <SidebarContent collapsed={!sidebarOpen} />
      </aside>

      {/* ── Main wrapper ── */}
      <div
        className={`transition-all duration-300 ease-in-out lg:ml-64 ${
          sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'
        }`}
      >
        {/* ── Top navbar ── */}
        <header className="sticky top-0 z-30 border-b border-gray-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:border-gray-800 dark:bg-gray-900/80">
          <div className="flex h-16 items-center gap-4 px-4 sm:px-6">
            {/* Hamburger */}
            <button
              onClick={() => {
                if (window.innerWidth < 1024) {
                  setMobileOpen(v => !v)
                } else {
                  setSidebarOpen(v => !v)
                }
              }}
              className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
              aria-label="Alternar menú"
            >
              <Menu className="h-5 w-5" />
            </button>

            {/* Brand — mobile only */}
            <div className="flex items-center gap-2 lg:hidden">
              <MessageCircle className="h-5 w-5 text-green-600" />
              <span className="font-bold text-gray-900 dark:text-white">WhatsApp</span>
            </div>

            {/* Spacer */}
            <div className="flex-1" />

            {/* Right actions */}
            <div className="flex items-center gap-2">
              {/* Theme toggle */}
              <button
                onClick={toggleTheme}
                className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                aria-label="Cambiar tema"
              >
                {dark ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
              </button>

              {/* User dropdown */}
              <DropdownMenu.Root>
                <DropdownMenu.Trigger asChild>
                  <button
                    className="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                    aria-label="Menú de usuario"
                  >
                    <div className="flex h-7 w-7 items-center justify-center rounded-full bg-green-600 text-xs font-bold text-white">
                      {window.__USER__?.initials || 'W'}
                    </div>
                    <span className="hidden font-medium text-gray-700 dark:text-gray-200 sm:inline">
                      {window.__USER__?.name || 'Usuario'}
                    </span>
                  </button>
                </DropdownMenu.Trigger>
                <DropdownMenu.Portal>
                  <DropdownMenu.Content
                    align="end"
                    sideOffset={8}
                    className="z-50 min-w-[220px] rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-700 dark:bg-gray-900"
                  >
                    <div className="px-3 py-2">
                      <p className="text-sm font-semibold text-gray-900 dark:text-white">{window.__USER__?.name || 'Usuario'}</p>
                      <p className="text-xs text-gray-500">{window.__USER__?.email || ''}</p>
                    </div>
                    <DropdownMenu.Separator className="my-1 h-px bg-gray-100 dark:bg-gray-800" />
                    <DropdownMenu.Item asChild>
                      <Link
                        to="/settings"
                        className="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                      >
                        <Settings className="h-4 w-4" />
                        Configuración
                      </Link>
                    </DropdownMenu.Item>
                    <DropdownMenu.Separator className="my-1 h-px bg-gray-100 dark:bg-gray-800" />
                    <DropdownMenu.Item
                      className="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 outline-none transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                      onSelect={(e) => {
                        e.preventDefault()
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content
                        fetch('/whatsapp/api/auth/logout', {
                          method: 'POST',
                          credentials: 'same-origin',
                          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        }).finally(() => { window.location.href = '/' })
                      }}
                    >
                      <LogOut className="h-4 w-4" />
                      Cerrar Sesión
                    </DropdownMenu.Item>
                  </DropdownMenu.Content>
                </DropdownMenu.Portal>
              </DropdownMenu.Root>
            </div>
          </div>
        </header>

        {/* ── Page content ── */}
        <main className="p-4 sm:p-6">
          <Outlet />
        </main>
      </div>
    </div>
  )
}