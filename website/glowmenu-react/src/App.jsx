import { Route, Routes, useLocation } from 'react-router-dom'
import { useEffect } from 'react'
import Layout from './components/Layout.jsx'
import HomePage from './pages/HomePage.jsx'
import CollectionsPage from './pages/CollectionsPage.jsx'
import PricingPage from './pages/PricingPage.jsx'
import ContactPage from './pages/ContactPage.jsx'
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
    document.title = titles[location.pathname] || 'Glow Menu'
  }, [location.pathname])

  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<HomePage />} />
        <Route path="/collections" element={<CollectionsPage />} />
        <Route path="/pricing" element={<PricingPage />} />
        <Route path="/contact" element={<ContactPage />} />
        <Route path="*" element={<HomePage />} />
      </Route>
    </Routes>
  )
}

export default App
