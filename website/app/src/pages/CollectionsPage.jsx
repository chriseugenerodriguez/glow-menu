import { useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { collectionBuilderUrl } from '../data/builderTemplates'
import { Tabs, TabsList, TabsTrigger } from '../components/ui/tabs'
import { Dialog, DialogContent, DialogTitle, DialogDescription } from '../components/ui/dialog'
import { Badge } from '../components/ui/badge'
import { ToggleGroup, ToggleGroupItem } from '../components/ui/toggle-group'
import { Input } from '../components/ui/input'
import { Search, ArrowUpRight, Eye, SlidersHorizontal, Check } from 'lucide-react'
import { Button } from '../components/ui/button'
import { collections } from '../data/siteData'

function CollectionsPage() {
  const [preview, setPreview] = useState(null)
  const [previewSide, setPreviewSide] = useState('front')
  const previewTrigger = useRef(null)
  const [query, setQuery] = useState('')
  const [style, setStyle] = useState('All styles')
  const styles = { 'All styles': null, 'Minimal': ['Minimal East', 'Tall Intentions'], 'After dark': ['Elegant Bar', 'Chill Brew', 'Random Order'], 'Occasions': ['The Ceremony', 'Special Occasion', 'Glow Party'] }
  const visibleCollections = collections.filter(item => (!styles[style] || styles[style].includes(item.name)) && `${item.name} ${item.description}`.toLowerCase().includes(query.toLowerCase().trim()))

  return (
    <>
      <section className="page-section collections-page">
        <div className="section-header"><p className="eyebrow">Featured collections</p><h1>Find your kind of glow.</h1><p>Eight collections, each with its own personality. Take a closer look, then make it yours.</p></div>
        <ToggleGroup type="single" value={style} onValueChange={value => { if (value) setStyle(value) }} className="collection-style-tabs" aria-label="Collection style">{Object.keys(styles).map(item => <ToggleGroupItem key={item} value={item}>{item}</ToggleGroupItem>)}</ToggleGroup><div className="collection-tools"><span role="status"><SlidersHorizontal size={15} /> {visibleCollections.length} {visibleCollections.length === 1 ? 'collection' : 'collections'} to make your own</span><label className="collection-search"><Search size={16} /><Input aria-label="Search collections" placeholder="Find your style..." value={query} onChange={event => setQuery(event.target.value)} /></label></div>{!visibleCollections.length && <div className="collection-empty"><h2>No matching collections.</h2><p>Try another style or explore the full collection.</p><Button variant="outline" onClick={() => { setQuery(''); setStyle('All styles') }}>Show all collections</Button></div>}<div className="collection-list">
          {visibleCollections.map(({ name, description, frontImage, backImage }) => (
            <article key={name} className="collection-feature">
              <div className="collection-gallery">
                {[['Front', frontImage], ['Back', backImage]].map(([side, image]) => <Link key={side} className="collection-preview" to={`${collectionBuilderUrl(name)}&side=${side.toLowerCase()}`}><img src={image} loading="lazy" alt={`${name} menu ${side.toLowerCase()}`} /><span>Edit {side}<ArrowUpRight size={12} aria-hidden="true" /></span></Link>)}
              </div>
              <div className="collection-copy"><span className="collection-number">{String(collections.findIndex(item => item.name === name) + 1).padStart(2, '0')}</span><h2>{name}</h2><p>{description}</p><div className="collection-actions"><Button className="collection-customize" asChild><Link to={collectionBuilderUrl(name)}>Customize menu <ArrowUpRight size={15} /></Link></Button><Button className="collection-quick-view" variant="outline" onClick={event => { previewTrigger.current = event.currentTarget; setPreviewSide('front'); setPreview({ name, description, frontImage, backImage, label: `${name} original menu` }) }}><Eye size={16} />Quick view</Button></div></div>
            </article>
          ))}
        </div>
      <div className="collection-custom-callout"><div><p className="eyebrow">Something only you could make</p><h2>Have a different idea?</h2><p>Start with a blank canvas and bring your own atmosphere.</p></div><Button asChild><Link to="/custom">Create from scratch <ArrowUpRight size={16} /></Link></Button></div></section>

      <Dialog open={Boolean(preview)} onOpenChange={open => { if (!open) setPreview(null) }}>
        <DialogContent className="quick-view-dialog" onCloseAutoFocus={event => { event.preventDefault(); previewTrigger.current?.focus() }}>
          {preview && <><div className="quick-view-art"><div className="quick-view-art-header"><Badge variant="outline">Original artwork</Badge><span>{previewSide === 'front' ? '01 / Front' : '02 / Back'}</span></div><div className="quick-view-image"><img key={previewSide} src={previewSide === 'front' ? preview.frontImage : preview.backImage} alt={`${preview.name} ${previewSide}`} /></div><Tabs value={previewSide} onValueChange={setPreviewSide}><TabsList aria-label="Preview side"><TabsTrigger value="front">Front</TabsTrigger><TabsTrigger value="back">Back</TabsTrigger></TabsList></Tabs></div><div className="quick-view-details"><p className="eyebrow">THE COLLECTION EDIT</p><DialogTitle>{preview.name}</DialogTitle><DialogDescription>{preview.description}</DialogDescription><div className="quick-view-features"><h3>Your starting point. Your signature.</h3>{['Edit the text and make it your own', 'Choose size, finish, and glow color', 'Design both sides of your menu'].map(text => <p key={text}><Check size={15} />{text}</p>)}</div><div className="quick-view-cta"><Button asChild><Link to={`${collectionBuilderUrl(preview.name)}&side=${previewSide}`}>Customize this collection <ArrowUpRight size={16} /></Link></Button><p>Opens an editable recreation in the menu studio.</p></div></div></>}
        </DialogContent>
      </Dialog>
    </>
  )
}

export default CollectionsPage
