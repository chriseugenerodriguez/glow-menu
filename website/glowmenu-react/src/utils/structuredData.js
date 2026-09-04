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
  priceRange: '$$$',
  sameAs: [
    'https://instagram.com/glowmenu',
    'https://twitter.com/glowmenu',
  ],
  image: 'https://glowmenu.co/og-image.jpg',
}

export const localBusinessSchema = {
  '@context': 'https://schema.org',
  '@type': 'ProfessionalService',
  name: 'Glow Menu',
  description: 'Custom menu design for hospitality brands',
  image: 'https://glowmenu.co/og-image.jpg',
  url: 'https://glowmenu.co',
  telephone: '+1-XXX-XXX-XXXX',
  email: 'hello@glowmenu.co',
  priceRange: '$49-$249',
  aggregateRating: {
    '@type': 'AggregateRating',
    ratingValue: '4.9',
    ratingCount: '120',
  },
  offers: [
    {
      '@type': 'Offer',
      name: 'Starter Menu',
      description: 'Single layout option with print-ready export',
      price: '49',
      priceCurrency: 'USD',
    },
    {
      '@type': 'Offer',
      name: 'Signature Collection',
      description: 'Multi-page concept set with brand styling',
      price: '129',
      priceCurrency: 'USD',
    },
    {
      '@type': 'Offer',
      name: 'Custom Hospitality Suite',
      description: 'Full branded package with inserts and launch-ready files',
      price: '249',
      priceCurrency: 'USD',
    },
  ],
}
