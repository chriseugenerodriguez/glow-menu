import { useEffect } from 'react'

export function useStructuredData(data) {
  useEffect(() => {
    const script = document.createElement('script')
    script.type = 'application/ld+json'
    script.innerHTML = JSON.stringify(data)
    document.head.appendChild(script)

    return () => {
      if (document.head.contains(script)) {
        document.head.removeChild(script)
      }
    }
  }, [data])
}

export const organizationSchema = {
  '@context': 'https://schema.org',
  '@type': 'LocalBusiness',
  name: 'Glow Menu',
  description: 'Premium custom menu design and hospitality branding',
  url: 'https://glowmenu.co',
  email: 'hello@glowmenu.co',
  areaServed: ['US', 'CA'],
  serviceType: 'Menu Design',
  image: 'https://glowmenu.co/hero-reference.png',
}

export const localBusinessSchema = {
  '@context': 'https://schema.org',
  '@type': 'ProfessionalService',
  name: 'Glow Menu',
  description: 'Custom menu design for hospitality brands',
  image: 'https://glowmenu.co/hero-reference.png',
  url: 'https://glowmenu.co',
  email: 'hello@glowmenu.co',
  serviceType: 'Menu Design',
}
