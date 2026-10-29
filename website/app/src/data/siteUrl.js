export const siteUrl = (import.meta.env.VITE_SITE_URL || 'https://chriseugenerodriguez-glowmenu.web.app').replace(/\/$/, '')

export const pageUrl = path => new URL(path, `${siteUrl}/`).href
