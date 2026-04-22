import React from 'react'
import { createRoot } from 'react-dom/client'
import SorteoApp from './SorteoApp.jsx'

const rootEl = document.getElementById('sorteo-root')
if (rootEl) {
  createRoot(rootEl).render(<SorteoApp />)
}
