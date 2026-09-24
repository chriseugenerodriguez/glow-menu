import { useEffect, useState } from 'react'
import { Check } from 'lucide-react'
import { Button } from './ui/button'
import { Input } from './ui/input'
import { Slider } from './ui/slider'
import { Label } from './ui/label'
import { sizes, finishes, thicknesses, glowColors, normalizeConfig } from '../data/menuOptions'
export default function MenuOptions({ config, onChange, section }) {
  const [quantityText, setQuantityText] = useState(String(config.quantity))
  useEffect(() => setQuantityText(String(config.quantity)), [config.quantity])
  const set = (key, value) => onChange(normalizeConfig({ ...config, [key]: value }))
  const commitQuantity = () => {
    const quantity = Number(quantityText)
    if (Number.isInteger(quantity) && quantity >= 1 && quantity <= 500) set('quantity', quantity)
    else setQuantityText(String(config.quantity))
  }
  return <div className="menu-options">
    {(!section || section === 'format') && <fieldset><legend>01 / Menu size</legend><div className="size-choices">{sizes.map(item => <Button type="button" key={item.id} aria-pressed={config.size === item.id} onClick={() => set('size', item.id)}><Check className="option-check" size={13} aria-hidden="true" /><span className="size-outline" style={{ aspectRatio: `${item.width}/${item.height}` }} /><span>{item.label}<small>{item.width} &times; {item.height} in</small></span></Button>)}</div></fieldset>}
    {[['finish', '02 / Finish', finishes], ['thickness', '03 / Card thickness', thicknesses]].filter(() => !section || section === 'material').map(([key, title, items]) => <fieldset key={key}><legend>{title}</legend><div className="finish-choices">{items.map(item => <Button type="button" key={item.id} aria-pressed={config[key] === item.id} onClick={() => set(key, item.id)}><span className={`material-sample sample-${item.id}`} aria-hidden="true" /><Check className="option-check" size={13} aria-hidden="true" />{item.label}<small>{item.detail}</small></Button>)}</div></fieldset>)}
    {(!section || section === 'glow') && <fieldset><legend>04 / Glow color</legend><div className="glow-choices">{glowColors.map(item => <Button type="button" key={item.id} aria-pressed={config.glow === item.id} onClick={() => set('glow', item.id)}><span style={{ background: item.hex, boxShadow: `0 0 14px ${item.hex}60` }} />{item.label}</Button>)}</div></fieldset>}
    {(!section || section === 'glow') && <div className="quantity-picker"><div className="quantity-picker-heading"><Label htmlFor={`menu-quantity-${section || 'builder'}`}>Quantity</Label><output aria-live="polite" aria-atomic="true">{config.quantity.toLocaleString()}<span> menus</span></output></div><Slider aria-label="Menu quantity slider" min={1} max={500} step={1} value={[config.quantity]} onValueChange={([quantity]) => set('quantity', quantity)} /><div className="quantity-range"><span>1 menu</span><span>500 menus</span></div><Label className="quantity-exact" htmlFor={`menu-quantity-${section || 'builder'}`}>Enter an exact amount<Input id={`menu-quantity-${section || 'builder'}`} aria-label="Menu quantity" type="number" min="1" max="500" step="1" value={quantityText} onChange={event => setQuantityText(event.target.value)} onBlur={commitQuantity} onKeyDown={event => { if (event.key === 'Enter') { event.preventDefault(); commitQuantity(); event.currentTarget.blur() } }} /></Label></div>}
  </div>
}
