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
import './shadcn.css'
import './pages/StorePages.css'
import './DarkTheme.css'
import './pages/ContentPage.css'
import BuilderPage from './pages/BuilderPage'
import './pages/BuilderPage.css'
import ContentPage from './pages/ContentPage'
import { contentPages, pageMeta } from './data/contentPages'

function App() {
  useStructuredData(localBusinessSchema)
  const location = useLocation()

  // Update page title and meta tags on route change
  useEffect(() => {
    const meta = pageMeta[location.pathname] || { title: 'Page Not Found | Glow Menu', description: 'This page could not be found. Explore Glow Menu collections or start a custom menu project.' }
    document.title = meta.title
    const url = new URL(location.pathname, 'https://glowmenu.co').href
    document.querySelector('link[rel="canonical"]')?.setAttribute('href', url)
    for (const [selector, content] of [
      ['meta[name="description"]', meta.description],
      ['meta[property="og:title"]', meta.title],
      ['meta[property="og:description"]', meta.description],
      ['meta[property="og:url"]', url],
      ['meta[name="twitter:title"]', meta.title],
      ['meta[name="twitter:description"]', meta.description],
      ['meta[name="twitter:url"]', url],
    ]) document.querySelector(selector)?.setAttribute('content', content)
    let robots = document.querySelector('meta[name="robots"]')
    if (!robots) { robots = document.createElement('meta'); robots.name = 'robots'; document.head.appendChild(robots) }
    robots.content = pageMeta[location.pathname] ? 'index,follow' : 'noindex,follow'
    let schema = document.getElementById('page-schema')
    if (!schema) { schema = document.createElement('script'); schema.id = 'page-schema'; schema.type = 'application/ld+json'; document.head.appendChild(schema) }
    schema.textContent = JSON.stringify({ '@context': 'https://schema.org', '@type': 'WebPage', name: meta.title, description: meta.description, url, isPartOf: { '@type': 'WebSite', name: 'Glow Menu', url: 'https://glowmenu.co/' } })

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
        <Route path="/custom" element={<BuilderPage key={location.search} />} />
        <Route path="/contact" element={<ContactPage />} />
        {contentPages.map(page => <Route key={page.path} path={page.path} element={<ContentPage page={page} />} />)}
        <Route path="*" element={<NotFoundPage />} />
      </Route>
    </Routes>
  )
}

export default App

import './pages/StudioRefresh.css'

import './SitePolish.css'
