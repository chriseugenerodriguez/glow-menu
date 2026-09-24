export const siteUrl = (import.meta.env.VITE_SITE_URL || 'https://glowmenu-1efe4.web.app').replace(/\/$/, '')

export const pageUrl = path => new URL(path, `${siteUrl}/`).href
