import { useCallback, useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { collections, features } from '../data/siteData'
import './HomePage.css'
import { collectionBuilderUrl } from '../data/builderTemplates'
import { Layers, Sparkles, SlidersHorizontal, ArrowLeft, ArrowRight } from 'lucide-react'
import GlowDetails from '../components/GlowDetails'

const slides = [
  { label: 'Chill Brew', tone: 'navy', front: '/collections/chill_brew_front.jpg', back: '/collections/chill_brew_back.jpg', frontMode: 'native', backMode: 'striped' },
  { label: 'Minimal East', tone: 'charcoal', front: '/collections/me_f_glow.jpg', back: '/collections/me_b_glow.jpg', frontMode: 'knockout', backMode: 'light' },
  { label: 'Special Occasion', tone: 'green', front: '/collections/so_f_glow.png', back: '/collections/so_b_glow.png', frontMode: 'native', backMode: 'native' },
  { label: 'Glow Party', tone: 'red', front: '/collections/glow_party_front.png', back: '/collections/glow_party_back.png', frontMode: 'native', backMode: 'native' },
]


function MenuArtwork({ item }) {
  return <div className="carousel-artwork">
    <figure className={`carousel-menu carousel-menu-front menu-${item.frontMode}`}><img src={item.front} alt={`${item.label} featured menu artwork`} draggable="false" /></figure>
    <figure className={`carousel-menu carousel-menu-back menu-${item.backMode}`}>
      <img src={item.back} alt={`${item.label} companion menu artwork`} draggable="false" />
    </figure>
  </div>
}

function HomePage() {
  const [slide, setSlide] = useState(0)
  const [slideDirection, setSlideDirection] = useState(1)
  const [previousSlide, setPreviousSlide] = useState(null)
  const [paused, setPaused] = useState(false)
  const [rotationStopped, setRotationStopped] = useState(true)
  const [reducedMotion, setReducedMotion] = useState(false)
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
    if (paused || rotationStopped || reducedMotion) return undefined
    const timer = window.setInterval(() => {
      transitionTo((slide + 1) % slides.length, 1)
    }, 6000)
    return () => window.clearInterval(timer)
  }, [paused, rotationStopped, reducedMotion, slide, transitionTo])

  useEffect(() => {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)')
    const update = () => setReducedMotion(preference.matches)
    update()
    preference.addEventListener('change', update)
    return () => preference.removeEventListener('change', update)
  }, [])

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
    <div className="home-page">
      <section className={`hero hero-${current.tone}`} onMouseEnter={() => setPaused(true)} onMouseLeave={() => setPaused(false)}>
        <div className="hero-copy">
          <p className="eyebrow">Menus made to stand out</p><h1>Set the mood.<br /><span>Make it glow.</span></h1>
          <p className="subheading">Distinctive menus for restaurants, bars, and unforgettable evenings. Designed around you.</p>
          <div className="hero-actions"><Link className="primary-btn" to="/collections">Explore menus <span aria-hidden="true">&#8599;</span></Link><Link className="hero-secondary" to="/custom">Create your own &rarr;</Link></div>
          <div className="hero-controls"><div className="hero-arrow-controls"><button type="button" aria-label="Previous featured collection" onClick={() => moveSlide(-1)}><ArrowLeft size={15} /></button><button type="button" aria-label="Next featured collection" onClick={() => moveSlide(1)}><ArrowRight size={15} /></button></div><span className="hero-slide-label">{String(slide + 1).padStart(2, '0')} / {current.label}</span><div className="hero-dots" aria-label="Choose featured slide">{slides.map((item, index) => <button type="button" key={item.label} className={index === slide ? 'active' : ''} aria-label={`Show slide ${index + 1}: ${item.label}`} aria-current={index === slide ? 'true' : undefined} onClick={() => chooseSlide(index)} />)}</div>{!reducedMotion && <button className="rotation-toggle" type="button" onClick={() => setRotationStopped((stopped) => !stopped)} aria-label={rotationStopped ? 'Play slideshow' : 'Pause slideshow'}>{rotationStopped ? 'Play' : 'Pause'}</button>}</div>
        </div>
        {previousSlide !== null && <div className={`hero-panel hero-panel-outgoing ${slideDirection < 0 ? 'to-right' : 'to-left'}`} aria-hidden="true">
          <MenuArtwork item={slides[previousSlide]} />
        </div>}
        <div className={`hero-panel hero-panel-active ${previousSlide !== null ? (slideDirection < 0 ? 'from-left' : 'from-right') : ''}`} key={`panel-${slide}`} role="region" aria-roledescription="carousel" aria-label={`${current.label} featured menu. Slide ${slide + 1} of ${slides.length}.`} aria-live="polite" tabIndex="0" onKeyDown={(event) => { if (event.key === 'ArrowLeft') moveSlide(-1); if (event.key === 'ArrowRight') moveSlide(1) }} onFocus={() => setPaused(true)} onBlur={() => setPaused(false)} onPointerDown={startDrag} onPointerUp={finishDrag} onPointerCancel={(event) => { dragStart.current = null; if (event.pointerType !== 'mouse') setPaused(false) }}>
          <MenuArtwork item={current} />
        </div>
      </section>
      <div className="home-detail-strip"><span><Layers size={16} />Eight starting points. Endless possibilities.</span><span><SlidersHorizontal size={16} />Your size. Your finish. Your feel.</span><span><Sparkles size={16} />A glow that sets the mood.</span></div><section className="home-collections" aria-labelledby="collections-heading">
        <div className="home-section-heading"><div><p className="eyebrow">Find your style</p><h2 id="collections-heading">A menu for every mood.</h2></div><Link to="/collections" className="text-link">Explore all 8 collections &rarr;</Link></div>
        <div className="home-collection-grid">{collections.slice(0, 4).map(({ name, description, frontImage, backImage }) => <Link key={name} to={collectionBuilderUrl(name)} className="home-collection-card"><div className="home-collection-art"><img src={frontImage} alt={`${name} menu front`} loading="lazy" /><img src={backImage} alt={`${name} menu back`} loading="lazy" /></div><div className="home-collection-title"><h3>{name}</h3><span aria-hidden="true">&#8599;</span></div><p>{description}</p></Link>)}</div>
      </section>
      <section className="home-custom" aria-labelledby="custom-heading">
        <div className="home-custom-art"><img src="/collections/the_ceremony_front.png" alt="The Ceremony custom menu design" loading="lazy" /><img src="/collections/the_ceremony_back.png" alt="The Ceremony menu details" loading="lazy" /></div>
        <div className="home-custom-copy"><p className="eyebrow">Made for your space</p><h2 id="custom-heading">Your menu.<br />Your atmosphere.</h2><p>A neighborhood bar, an intimate dinner, a night to remember. Start with a collection or create something entirely your own.</p><Link className="primary-btn" to="/custom">Let's make it yours <span aria-hidden="true">&#8599;</span></Link><Link className="text-link" to="/pricing">Explore pricing &rarr;</Link></div>
      </section>
      <section className="venue-links" aria-labelledby="venue-heading"><div><p className="eyebrow">At home in your world</p><h2 id="venue-heading">Every table has a story.</h2></div><div className="venue-link-grid">{[['01', 'Restaurants', 'From the first course to the last impression.', '/restaurant-menus'], ['02', 'Bars & lounges', 'A signature menu for your signature serves.', '/bar-menus'], ['03', 'Events & occasions', 'Details that make a gathering feel personal.', '/event-menus']].map(([n, title, copy, to]) => <Link key={to} to={to}><span>{n}</span><h3>{title}<span aria-hidden="true">&#8599;</span></h3><p>{copy}</p></Link>)}</div></section><section className="home-services" aria-label="Our services">{features.map((feature, index) => <article key={feature.title}><span className="service-number">0{index + 1}</span><h3>{feature.title}</h3><p>{feature.text}</p></article>)}</section>
      <GlowDetails />
    </div>
  )
}

export default HomePage
