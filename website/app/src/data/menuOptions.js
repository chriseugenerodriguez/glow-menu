export const sizes = [
  { id: 'table-top', label: 'Table top', width: 5, height: 7 },
  { id: 'square', label: 'Square', width: 5, height: 5 },
  { id: 'slim', label: 'Slim', width: 4, height: 6 },
  { id: 'half-letter', label: 'Half letter', width: 4.25, height: 11 },
  { id: 'half-legal', label: 'Half legal', width: 4.25, height: 14 },
]
export const finishes = [
  { id: 'stock', label: 'Stock', detail: 'Uncoated surface' },
  { id: 'gloss', label: 'Gloss', detail: 'Reflective surface' },
  { id: 'laminated', label: 'Laminated', detail: 'Protective outer layer' },
]
export const thicknesses = [{ id: '10pt', label: '10 pt', detail: 'Light card' }, { id: '14pt', label: '14 pt', detail: 'Standard card' }, { id: '16pt', label: '16 pt', detail: 'Thick card' }]
export const glowColors = [
  { id: 'green', label: 'Green', hex: '#5ade55' }, { id: 'blue', label: 'Blue', hex: '#4280e5' },
  { id: 'red', label: 'Red', hex: '#fd5a5b' }, { id: 'yellow', label: 'Yellow', hex: '#ffdf55' }, { id: 'orange', label: 'Orange', hex: '#fe8b3a' },
]
export const defaultConfig = { size: 'table-top', finish: 'laminated', thickness: '14pt', glow: 'green', quantity: 20 }
export function normalizeConfig(value = {}) {
  const result = { quantity: Math.max(1, Math.min(500, Math.floor(Number(value.quantity) || 20))) }
  for (const [key, options] of Object.entries({ size: sizes, finish: finishes, thickness: thicknesses, glow: glowColors })) result[key] = options.some(item => item.id === value[key]) ? value[key] : defaultConfig[key]
  return result
}
export function describeConfig(config) {
  const size = sizes.find(item => item.id === config.size)
  return `${config.quantity} menus, ${size.width} x ${size.height} in, ${finishes.find(item => item.id === config.finish).label}, ${thicknesses.find(item => item.id === config.thickness).label} card, ${glowColors.find(item => item.id === config.glow).label} glow`
}
