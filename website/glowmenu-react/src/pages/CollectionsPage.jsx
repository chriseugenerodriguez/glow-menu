import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { collections } from '../data/siteData'

function CollectionsPage() {
  const [preview, setPreview] = useState(null)

  useEffect(() => {
    if (!preview) return undefined
    const closeOnEscape = (event) => event.key === 'Escape' && setPreview(null)
    document.body.classList.add('modal-open')
    window.addEventListener('keydown', closeOnEscape)
    return () => {
      document.body.classList.remove('modal-open')
      window.removeEventListener('keydown', closeOnEscape)
    }
  }, [preview])

  return (
    <>
      <section className="page-section collections-page">
        <div className="section-header"><p className="eyebrow">Featured collections</p><h1>Menu concepts built for premium hospitality brands.</h1><p>Select either side of a menu to inspect the artwork.</p></div>
        <div className="collection-list">
          {collections.map(({ name, description, frontImage, backImage }, index) => (
            <article key={name} className="collection-feature">
              <div className="collection-gallery">
                {[['Front', frontImage], ['Back', backImage]].map(([side, image]) => <button type="button" key={side} className="collection-preview" onClick={() => setPreview({ image, label: `${name} menu ${side.toLowerCase()}` })}><img src={image} alt={`${name} menu ${side.toLowerCase()}`} /><span>{side}</span></button>)}
              </div>
              <div className="collection-copy"><span className="collection-number">{String(index + 1).padStart(2, '0')}</span><h2>{name}</h2><p>{description}</p><Link to="/contact" className="text-link">Request this style &rarr;</Link></div>
            </article>
          ))}
        </div>
      </section>

      {preview && <div className="image-modal" role="dialog" aria-modal="true" aria-label={preview.label} onClick={() => setPreview(null)}><button type="button" className="modal-close" aria-label="Close preview" onClick={() => setPreview(null)}>&times;</button><img src={preview.image} alt={preview.label} onClick={(event) => event.stopPropagation()} /></div>}
    </>
  )
}

export default CollectionsPage
