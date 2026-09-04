import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'

const formats = [
  { id: 'classic', label: 'Classic', price: 42, detail: 'Single illuminated menu' },
  { id: 'fold', label: 'Book fold', price: 58, detail: 'Two-panel presentation' },
  { id: 'suite', label: 'Menu suite', price: 76, detail: 'Menu with matching insert' },
]

const finishes = [
  { id: 'glow', label: 'Glow treatment', price: 8 },
  { id: 'proof', label: 'Printed proof', price: 25 },
  { id: 'rush', label: 'Rush production', price: 65 },
]

function PricingPage() {
  const [quantity, setQuantity] = useState(10)
  const [format, setFormat] = useState(formats[0])
  const [selectedFinishes, setSelectedFinishes] = useState(['glow'])
  const estimate = useMemo(() => {
    const extras = finishes.filter((finish) => selectedFinishes.includes(finish.id)).reduce((total, finish) => total + finish.price, 0)
    return quantity * (format.price + extras)
  }, [format, quantity, selectedFinishes])

  const toggleFinish = (id) => setSelectedFinishes((current) => current.includes(id) ? current.filter((item) => item !== id) : [...current, id])

  return (
    <section className="pricing-page">
      <header className="pricing-hero"><p className="eyebrow">Simple, flexible pricing</p><h1>Build your menu estimate.</h1><p>Choose a format, quantity, and finish. Final production pricing is confirmed after artwork review.</p></header>
      <div className="estimate-bar"><span>Estimated project cost</span><strong>${estimate.toLocaleString()}</strong><Link className="primary-btn" to="/contact">Get started</Link></div>
      <div className="pricing-builder">
        <section className="builder-section"><div className="builder-heading"><span>01</span><div><h2>Quantity</h2><p>How many finished menus do you need?</p></div></div><label className="quantity-control"><button type="button" onClick={() => setQuantity((value) => Math.max(1, value - 1))} aria-label="Decrease quantity">-</button><input type="number" min="1" max="500" value={quantity} onChange={(event) => setQuantity(Math.max(1, Number(event.target.value)))} /><button type="button" onClick={() => setQuantity((value) => value + 1)} aria-label="Increase quantity">+</button></label></section>
        <section className="builder-section"><div className="builder-heading"><span>02</span><div><h2>Format</h2><p>Select the structure that suits your service.</p></div></div><div className="option-grid">{formats.map((option) => <button type="button" className={format.id === option.id ? 'option-button selected' : 'option-button'} key={option.id} onClick={() => setFormat(option)}><strong>{option.label}</strong><span>{option.detail}</span><small>${option.price} each</small></button>)}</div></section>
        <section className="builder-section"><div className="builder-heading"><span>03</span><div><h2>Finishes</h2><p>Add production services to your menu.</p></div></div><div className="finish-list">{finishes.map((finish) => <label key={finish.id}><input type="checkbox" checked={selectedFinishes.includes(finish.id)} onChange={() => toggleFinish(finish.id)} /><span>{finish.label}</span><small>+${finish.price}</small></label>)}</div></section>
      </div>
    </section>
  )
}

export default PricingPage
