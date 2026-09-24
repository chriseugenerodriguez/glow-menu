import { collections } from './siteData'

export const collectionSlug = name => name.toLowerCase().replaceAll(' ', '-')
export const collectionBuilderUrl = name => `/custom?collection=${collectionSlug(name)}`
const text = (id, value, x, y, size, extra = {}) => ({ id, text: value, x, y, size, glow: false, ...extra })
const palettes = [
  ['#faf9f4', '#202923', '#384c36', 'sans', 'table-top'],
  ['#f5efdd', '#25271c', '#ada144', 'serif', 'half-letter'],
  ['#260c30', '#f9e8fa', '#e288ff', 'sans', 'table-top'],
  ['#e6cdb0', '#252f28', '#546f53', 'sans', 'half-letter'],
  ['#eee3ca', '#484134', '#99865b', 'serif', 'table-top'],
  ['#191a1a', '#faf6e9', '#c5b78d', 'serif', 'table-top'],
  ['#071c29', '#b6a386', '#c5a371', 'sans', 'table-top'],
  ['#075548', '#eff8dc', '#aad99c', 'sans', 'table-top'],
]
export function makeDesign(name) {
  const index = collections.findIndex(item => item.name === name)
  if (index < 0) return { front: [], back: [], frontImage: '', backImage: '', background: '#101b19', ink: '#f0efe3', accent: '#a9ca88', font: 'sans', decoration: 'none' }
  const [background, ink, accent, font] = palettes[index]
  const collection = collections[index]
  return {
    background, ink, accent, font, decoration: index % 3 === 0 ? 'frame' : index % 3 === 1 ? 'stripe' : 'rules',
    frontImage: '', backImage: '',
    front: [text('brand', 'YOUR VENUE', 50, 17, 16), text('title', collection.name.toUpperCase(), 50, 38, 34, { glow: true }), text('subtitle', 'A MENU FOR GOOD EVENINGS', 50, 65, 11), text('address', 'Your address · Your city', 50, 83, 11)],
    back: [text('menu-title', 'THE MENU', 50, 14, 26, { glow: true }), text('section', 'HOUSE FAVORITES', 50, 27, 13), text('items', 'Signature cocktail     14\nSeasonal spritz     12\nHouse classic     16\nZero-proof cooler     10', 50, 43, 15), text('second-section', 'SOMETHING MORE', 50, 69, 13, { glow: true }), text('more-items', 'A little something     8\nThe perfect finish     12', 50, 80, 14)],
  }
}
export function getTemplate(slug) {
  const index = collections.findIndex(item => collectionSlug(item.name) === slug)
  return index < 0 ? null : { ...collections[index], size: palettes[index][4] }
}
