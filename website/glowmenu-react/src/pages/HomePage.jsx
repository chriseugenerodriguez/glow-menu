import { useCallback, useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { collections, features } from '../data/siteData'

const slides = [
  { label: 'Chill Brew', tone: 'navy', front: '/collections/cb_b_glow.jpg', back: '/collections/random_order_front.jpg', frontMode: 'native', backMode: 'light' },
  { label: 'Minimal East', tone: 'charcoal', front: '/collections/me_f_glow.jpg', back: '/collections/me_b_glow.jpg', frontMode: 'knockout', backMode: 'light' },
  { label: 'Special Occasion', tone: 'green', front: '/collections/so_f_glow.png', back: '/collections/so_b_glow.png', frontMode: 'native', backMode: 'native' },
  { label: 'Glow Party', tone: 'red', front: '/collections/glow_party_front.png', back: '/collections/glow_party_back.png', frontMode: 'native', backMode: 'native' },
]

const clientLogos = Array.from({ length: 8 }, (_, index) => `/clients/logo${index + 1}.jpg`)

function HomePage() {
  const [slide, setSlide] = useState(0)
  const [slideDirection, setSlideDirection] = useState(1)
  const [previousSlide, setPreviousSlide] = useState(null)
  const [paused, setPaused] = useState(false)
  const dragStart = useRef(null)
  const transitionTimer = useRef(null)

  const transitionTo = useCallback((nextSlide, direction) => {
    if (nextSlide === slide) return
    window.clearTimeout(transitionTimer.current)
    setPreviousSlide(slide)
    setSlideDirection(direction)
    setSlide(nextSlide)
    transitionTimer.current = window.setTimeout(() => setPreviousSlide(null), 720)
  }, [slide])

  useEffect(() => {
    if (paused) return undefined
    const timer = window.setInterval(() => {
      transitionTo((slide + 1) % slides.length, 1)
    }, 6000)
    return () => window.clearInterval(timer)
  }, [paused, slide, transitionTo])

  useEffect(() => () => window.clearTimeout(transitionTimer.current), [])

  const current = slides[slide]
  const moveSlide = (direction) => {
    transitionTo((slide + direction + slides.length) % slides.length, direction)
  }
  const chooseSlide = (index) => {
    if (index === slide) return
    transitionTo(index, index > slide ? 1 : -1)
  }
  const startDrag = (event) => {
    dragStart.current = event.clientX
    event.currentTarget.setPointerCapture?.(event.pointerId)
    if (event.pointerType !== 'mouse') setPaused(true)
  }
  const finishDrag = (event) => {
    if (dragStart.current === null) return
    const distance = event.clientX - dragStart.current
    if (Math.abs(distance) > 45) moveSlide(distance < 0 ? 1 : -1)
    dragStart.current = null
    if (event.currentTarget.hasPointerCapture?.(event.pointerId)) event.currentTarget.releasePointerCapture(event.pointerId)
    if (event.pointerType !== 'mouse') setPaused(false)
  }

  return (
    <>
      <section className={`hero hero-${current.tone}`} onMouseEnter={() => setPaused(true)} onMouseLeave={() => setPaused(false)}>
        <div className="hero-copy">
          <h1>Glow in the<br />dark menus</h1><span className="hero-rule" aria-hidden="true" />
          <p className="subheading">Create one of a kind menus for your business.</p>
          <div className="hero-actions"><Link className="primary-btn" to="/collections">Get started</Link></div>
          <div className="hero-dots" aria-label="Choose featured slide">{slides.map((item, index) => <button type="button" key={item.label} className={index === slide ? 'active' : ''} aria-label={`Show slide ${index + 1}: ${item.label}`} aria-current={index === slide ? 'true' : undefined} onClick={() => chooseSlide(index)} />)}</div>
        </div>
        {previousSlide !== null && <div className={`hero-panel hero-panel-outgoing ${slideDirection < 0 ? 'to-right' : 'to-left'}`} aria-hidden="true">
          <div className="carousel-artwork">
            <figure className={`carousel-menu carousel-menu-front menu-${slides[previousSlide].frontMode}`}><img src={slides[previousSlide].front} alt="" draggable="false" /></figure>
            <figure className={`carousel-menu carousel-menu-back menu-${slides[previousSlide].backMode}`}><img src={slides[previousSlide].back} alt="" draggable="false" /></figure>
          </div>
        </div>}
        <div className={`hero-panel hero-panel-active ${previousSlide !== null ? (slideDirection < 0 ? 'from-left' : 'from-right') : ''}`} key={`panel-${slide}`} role="region" aria-roledescription="carousel" aria-label={`${current.label} featured menu. Slide ${slide + 1} of ${slides.length}.`} aria-live="polite" tabIndex="0" onKeyDown={(event) => { if (event.key === 'ArrowLeft') moveSlide(-1); if (event.key === 'ArrowRight') moveSlide(1) }} onFocus={() => setPaused(true)} onBlur={() => setPaused(false)} onPointerDown={startDrag} onPointerUp={finishDrag} onPointerCancel={(event) => { dragStart.current = null; if (event.pointerType !== 'mouse') setPaused(false) }}>
          <div className="carousel-artwork" aria-label={`${current.label} menu collection`}>
            <figure className={`carousel-menu carousel-menu-front menu-${current.frontMode}`}><img src={current.front} alt={`${current.label} menu front`} draggable="false" /><figcaption>{current.label} / Front</figcaption></figure>
            <figure className={`carousel-menu carousel-menu-back menu-${current.backMode}`}><img src={current.back} alt={`${current.label} menu back`} draggable="false" /><figcaption>{current.label} / Back</figcaption></figure>
          </div>
        </div>
      </section>
      <section className="client-strip" aria-label="Glow Menu clients">
        <div className="client-track">
          {[0, 1].map((group) => <div className="client-group" aria-hidden={group === 1 ? 'true' : undefined} key={group}>{clientLogos.map((logo, index) => <img src={logo} alt={group === 0 ? `Client logo ${index + 1}` : ''} key={`${group}-${logo}`} loading="lazy" />)}</div>)}
        </div>
      </section>
      <section className="section-block"><div className="section-header"><p className="eyebrow">What we design</p><h2>Everything your guest experience touches.</h2></div><div className="feature-grid">{features.map((feature) => <article className="feature-card" key={feature.title}><span className="feature-icon" aria-hidden="true">+</span><h3>{feature.title}</h3><p>{feature.text}</p></article>)}</div></section>
      <section className="design-story">
        <div className="design-story-copy"><p className="eyebrow">Designed around your service</p><h2>Create a menu that belongs in the room.</h2><span className="story-rule" aria-hidden="true" /><p>From the proportion of the page to the way it glows after dark, each detail is shaped around your brand, your menu, and how guests move through the experience.</p><Link className="text-link" to="/contact">Start a custom project &rarr;</Link></div>
        <div className="design-story-art"><img className="story-menu story-menu-back" src="/collections/tall_intentions_back.jpg" alt="Tall Intentions menu back" /><img className="story-menu story-menu-front" src="/collections/glow_party_front.png" alt="Glow Party menu front" /></div>
      </section>
      <section className="section-block showcase"><div className="section-header two-col"><div><p className="eyebrow">Featured collections</p><h2>Modern menu styles for hospitality brands.</h2></div><Link to="/collections" className="text-link">See all collections &rarr;</Link></div><div className="collection-grid">{collections.map(({ name, frontImage, backImage }) => <Link key={name} to="/collections" className="collection-card"><span className="collection-card-art"><img src={frontImage} alt={`${name} menu front`} /><img src={backImage} alt={`${name} menu back`} /></span><strong>{name}</strong></Link>)}</div></section>
      <section className="section-block pricing-panel"><div className="section-header"><p className="eyebrow">Simple, flexible pricing</p><h2>Choose a starting point, then make it yours.</h2></div><div className="pricing-box"><div><span className="price-tag">Starting at</span><strong>$49</strong></div><p>Custom menu design, premium finishes, and branded hospitality assets.</p><Link to="/pricing" className="primary-btn">Get started</Link></div></section>
      <section className="cta-banner"><div><p className="eyebrow">Let's build your next guest experience</p><h2>Ready for a menu system that feels as premium as your brand?</h2></div><Link to="/contact" className="primary-btn">Start a project</Link></section>
    </>
  )
}

export default HomePage
