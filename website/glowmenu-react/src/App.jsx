import { Route, Routes, useLocation } from 'react-router-dom'
import { useEffect } from 'react'
import Layout from './components/Layout.jsx'
import HomePage from './pages/HomePage.jsx'
import CollectionsPage from './pages/CollectionsPage.jsx'
import PricingPage from './pages/PricingPage.jsx'
import ContactPage from './pages/ContactPage.jsx'
import NotFoundPage from './pages/NotFoundPage.jsx'
import { useStructuredData, localBusinessSchema } from './utils/structuredData.js'
import './App.css'

function App() {
  useStructuredData(localBusinessSchema)
  const location = useLocation()

  // Update page title and meta tags on route change
  useEffect(() => {
    const titles = {
      '/': 'Glow Menu | Premium Custom Menu Design',
      '/collections': 'Menu Collections | Glow Menu',
      '/pricing': 'Pricing | Glow Menu',
      '/contact': 'Contact Us | Glow Menu',
    }
    document.title = titles[location.pathname] || 'Page Not Found | Glow Menu'
    const canonical = document.querySelector('link[rel="canonical"]')
    if (canonical) canonical.href = new URL(location.pathname, 'https://glowmenu.co').href
  }, [location.pathname])

  useEffect(() => {
    const measurementId = import.meta.env.VITE_GA_MEASUREMENT_ID
    if (!measurementId || document.querySelector('script[data-glow-analytics]')) return
    const script = document.createElement('script')
    script.async = true
    script.dataset.glowAnalytics = 'true'
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(measurementId)}`
    document.head.appendChild(script)
    window.dataLayer = window.dataLayer || []
    window.gtag = function gtag() { window.dataLayer.push(arguments) }
    window.gtag('js', new Date())
    window.gtag('config', measurementId, { anonymize_ip: true, allow_google_signals: false })
  }, [])

  useEffect(() => {
    window.gtag?.('event', 'page_view', { page_path: location.pathname })
    window.scrollTo({ top: 0, behavior: 'instant' })
  }, [location.pathname])

  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<HomePage />} />
        <Route path="/collections" element={<CollectionsPage />} />
        <Route path="/pricing" element={<PricingPage />} />
        <Route path="/contact" element={<ContactPage />} />
        <Route path="*" element={<NotFoundPage />} />
      </Route>
    </Routes>
  )
}

export default App
