import { createServer } from 'vite'
import { readFile, writeFile, mkdir } from 'node:fs/promises'
import { resolve } from 'node:path'

const server = await createServer({ server: { middlewareMode: true }, appType: 'custom' })
const escape = value => value.replaceAll('&', '&amp;').replaceAll('"', '&quot;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
try {
  const { render, pageMeta, siteUrl, pageUrl } = await server.ssrLoadModule('/src/prerender.jsx')
  const template = (await readFile(resolve('dist/index.html'), 'utf8')).replaceAll('https://glowmenu-1efe4.web.app', siteUrl)
  for (const [path, meta] of Object.entries(pageMeta)) {
    const url = pageUrl(path)
    let html = template.replace(/<title>.*?<\/title>/, `<title>${escape(meta.title)}</title>`)
    const fields = { description: meta.description, 'og:title': meta.title, 'og:description': meta.description, 'og:url': url, 'twitter:title': meta.title, 'twitter:description': meta.description, 'twitter:url': url }
    for (const [name, value] of Object.entries(fields)) {
      html = html.replace(new RegExp(`(<meta (?:name|property)="${name}" content=")[^"]*("[^>]*>)`), (_, before, after) => before + escape(value) + after)
    }
    html = html.replace(/(<link rel="canonical" href=")[^"]*("[^>]*>)/, (_, before, after) => before + escape(url) + after)
    html = html.replace('<div id="root"></div>', () => `<div id="root">${render(path)}</div>`)
    const structuredData = { '@context': 'https://schema.org', '@type': 'WebPage', name: meta.title, description: meta.description, url, isPartOf: { '@type': 'WebSite', name: 'Glow Menu', url: `${siteUrl}/` } }
    html = html.replace('</head>', () => `<script id="page-schema" type="application/ld+json">${JSON.stringify(structuredData).replaceAll('<', '\\u003c')}</script>\n</head>`)
    const directory = resolve('dist', path.slice(1))
    await mkdir(directory, { recursive: true })
    await writeFile(resolve(directory, 'index.html'), html)
  }
  const sitemap = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${Object.keys(pageMeta).map(path => `  <url><loc>${pageUrl(path)}</loc></url>`).join('\n')}\n</urlset>\n`
  await writeFile(resolve('dist/sitemap.xml'), sitemap)
  const robots = (await readFile(resolve('public/robots.txt'), 'utf8')).replaceAll('https://glowmenu-1efe4.web.app', siteUrl)
  await writeFile(resolve('dist/robots.txt'), robots)
  console.log(`Prerendered ${Object.keys(pageMeta).length} routes and generated sitemap.`)
} finally {
  await server.close()
}
