import { useRef, useState } from 'react'
import { Link, useLocation, useSearchParams } from 'react-router-dom'
import { makeDesign, getTemplate } from '../data/builderTemplates'
import { Button } from '../components/ui/button'
import { Badge } from '../components/ui/badge'
import { Alert, AlertDescription } from '../components/ui/alert'
import { Tabs, TabsList, TabsTrigger, TabsContent } from '../components/ui/tabs'
import { Slider } from '../components/ui/slider'
import { Switch } from '../components/ui/switch'
import { Input } from '../components/ui/input'
import { Textarea } from '../components/ui/textarea'
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '../components/ui/tooltip'
import { ToggleGroup, ToggleGroupItem } from '../components/ui/toggle-group'
import { Undo2, RotateCcw, Plus, Copy, CircleCheck, CircleAlert, AlignLeft, AlignCenter, AlignRight, Bold, Italic, Layers, SlidersHorizontal, Type, ImagePlus, ChevronUp, ChevronDown, Info, DollarSign } from 'lucide-react'
import MenuOptions from '../components/MenuOptions'
import { sizes, finishes, thicknesses, glowColors, normalizeConfig, describeConfig } from '../data/menuOptions'
export default function BuilderPage() {
  const { state } = useLocation()
  const [params] = useSearchParams()
  const template = getTemplate(params.get('collection'))
  const initialDesign = makeDesign(template?.name)
  const [config, setConfig] = useState(() => normalizeConfig(state?.config || { size: template?.size }))
  const [design, setDesign] = useState(initialDesign)
  const [history, setHistory] = useState([])
  const [side, setSide] = useState(params.get('side') === 'back' ? 'back' : 'front')
  const [selected, setSelected] = useState(template ? initialDesign[params.get('side') === 'back' ? 'back' : 'front'][0]?.id : null)
  const [tab, setTab] = useState('text')
  const [lightsOff, setLightsOff] = useState(true)
  const [status, setStatus] = useState('')
  const svgRef = useRef(null)
  const drag = useRef(null)
  const textEditSnapshot = useRef(null)
  const sliderSnapshot = useRef(null)
  const size = sizes.find(item => item.id === config.size)
  const glow = glowColors.find(item => item.id === config.glow)
  const height = 400 * size.height / size.width
  const layer = design[side].find(item => item.id === selected)
  const artworkPosition = design[`${side}ArtworkPosition`] || { x: 0, y: 0 }
  const selectedLabel = selected === 'artwork' ? `${side} artwork` : selected === 'background' ? 'Background' : layer?.text.split('\n')[0] || ''
  const layerCountFor = sideName => design[sideName].length + (design[`${sideName}Image`] ? 1 : 0) + 1
  const selectedLayerPosition = selected === 'background' ? layerCountFor(side) : selected === 'artwork' ? design[side].length + 1 : layer ? design[side].length - design[side].findIndex(item => item.id === selected) : 0
  const textBounds = item => {
    const lines = item.text.split('\n')
    const width = Math.max(...lines.map(line => line.length), 1) * item.size * .62 + 16
    const x = item.align === 'start' ? item.x * 4 - 4 : item.align === 'end' ? item.x * 4 - width + 4 : item.x * 4 - width / 2
    return { x, y: item.y / 100 * height - item.size - 7, width, height: Math.max(1, lines.length) * item.size * 1.5 + 8 }
  }
  const reorderLayer = (id, offset) => {
    const layers = [...design[side]]
    const index = layers.findIndex(item => item.id === id)
    const target = index + offset
    if (target < 0 || target >= layers.length) return
    ;[layers[index], layers[target]] = [layers[target], layers[index]]
    commit({ ...design, [side]: layers })
  }
  const commit = next => { setHistory(items => [...items.slice(-29), design]); setDesign(next) }
  const changeLayer = patch => commit({ ...design, [side]: design[side].map(item => item.id === selected ? { ...item, ...patch } : item) })
  const undo = () => { if (history.length) { setDesign(history[history.length - 1]); setHistory(items => items.slice(0, -1)) } }
  const addText = (preset = 'text') => { const id = `text-${Date.now()}`; commit({ ...design, [side]: [...design[side], { id, text: preset === 'heading' ? 'YOUR MENU' : preset === 'item' ? 'Signature serve     14' : 'New text', x: 50, y: preset === 'heading' ? 20 : 50, size: preset === 'heading' ? 32 : 18, glow: preset !== 'item' }] }); setSelected(id); setTab('text') }
  const chooseArtwork = useOriginal => {
    const imageKey = `${side}Image`
    const source = template?.[imageKey] || ''
    const starter = side === 'front'
      ? [{ id: 'venue', text: 'YOUR VENUE', x: 50, y: 17, size: 16 }, { id: 'title', text: template?.name.toUpperCase() || 'YOUR MENU', x: 50, y: 38, size: 34, glow: true }, { id: 'subtitle', text: 'A MENU FOR GOOD EVENINGS', x: 50, y: 65, size: 11 }, { id: 'address', text: 'Your address · Your city', x: 50, y: 83, size: 11 }]
      : [{ id: 'menu-title', text: 'THE MENU', x: 50, y: 14, size: 26, glow: true }, { id: 'section', text: 'HOUSE FAVORITES', x: 50, y: 27, size: 13 }, { id: 'items', text: 'Signature cocktail     14\nSeasonal spritz     12\nHouse classic     16\nZero-proof cooler     10', x: 50, y: 43, size: 15 }, { id: 'second-section', text: 'SOMETHING MORE', x: 50, y: 69, size: 13, glow: true }, { id: 'more-items', text: 'A little something     8\nThe perfect finish     12', x: 50, y: 80, size: 14 }]
    commit({ ...design, [imageKey]: useOriginal ? source : '', [`${side}ArtworkPosition`]: { x: 0, y: 0 }, [side]: useOriginal ? [] : starter })
    setSelected(useOriginal ? 'artwork' : starter[0].id)
    setStatus(useOriginal ? 'Original artwork selected. Add editable text layers over it.' : 'Editable recreation selected. Every text layer can be changed.')
  }
  const upload = event => {
    const file = event.target.files?.[0]
    if (!file) return
    if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type) || file.size > 4 * 1024 * 1024) { setStatus('Choose a PNG, JPEG, or WebP under 4 MB.'); return }
    const target = side
    const reader = new FileReader()
    reader.onload = () => { setHistory(items => [...items.slice(-29), design]); setDesign(current => ({ ...current, [`${target}Image`]: reader.result, [`${target}ArtworkPosition`]: { x: 0, y: 0 } })); setSelected('artwork'); setStatus('Artwork added.') }
    reader.readAsDataURL(file); event.target.value = ''
  }
  const move = event => {
    const activeDrag = drag.current
    if (!activeDrag) return
    const bounds = svgRef.current.getBoundingClientRect()
    const point = { x: (event.clientX - bounds.left) / bounds.width * 400, y: (event.clientY - bounds.top) / bounds.height * height }
    if (activeDrag.type === 'artwork') {
      const dx = (point.x - activeDrag.pointer.x) * 100 / 400
      const dy = (point.y - activeDrag.pointer.y) * 100 / height
      if (!activeDrag.moved && Math.abs(dx) < .4 && Math.abs(dy) < .4) return
      if (!activeDrag.moved) { setHistory(items => [...items.slice(-29), activeDrag.snapshot]); activeDrag.moved = true }
      setDesign(current => ({ ...current, [`${side}ArtworkPosition`]: { x: Math.max(-85, Math.min(85, activeDrag.origin.x + dx)), y: Math.max(-85, Math.min(85, activeDrag.origin.y + dy)) } }))
      return
    }
    const dx = (point.x - activeDrag.pointer.x) / 4
    const dy = (point.y - activeDrag.pointer.y) / height * 100
    if (!activeDrag.moved && Math.abs(dx) < .4 && Math.abs(dy) < .4) return
    if (!activeDrag.moved) { setHistory(items => [...items.slice(-29), activeDrag.snapshot]); activeDrag.moved = true }
    const x = Math.max(5, Math.min(95, activeDrag.origin.x + dx))
    const y = Math.max(5, Math.min(95, activeDrag.origin.y + dy))
    setDesign(current => ({ ...current, [side]: current[side].map(item => item.id === activeDrag.id ? { ...item, x, y } : item) }))
  }
  const nudgeSelected = event => {
    const directions = { ArrowLeft: [-1, 0], ArrowRight: [1, 0], ArrowUp: [0, -1], ArrowDown: [0, 1] }
    const direction = directions[event.key]
    if (!direction || !selected) return
    event.preventDefault()
    if (selected === 'artwork' && design[`${side}Image`]) {
      const position = design[`${side}ArtworkPosition`] || { x: 0, y: 0 }
      setHistory(items => [...items.slice(-29), design])
      setDesign(current => ({ ...current, [`${side}ArtworkPosition`]: { x: Math.max(-85, Math.min(85, position.x + direction[0])), y: Math.max(-85, Math.min(85, position.y + direction[1])) } }))
      return
    }
    if (layer) {
      setHistory(items => [...items.slice(-29), design])
      setDesign(current => ({ ...current, [side]: current[side].map(item => item.id === selected ? { ...item, x: Math.max(5, Math.min(95, item.x + direction[0])), y: Math.max(5, Math.min(95, item.y + direction[1])) } : item) }))
    }
  }
  const changeArtworkPosition = (axis, value) => {
    setSelected('artwork')
    setDesign(current => ({ ...current, [`${side}ArtworkPosition`]: { ...(current[`${side}ArtworkPosition`] || { x: 0, y: 0 }), [axis]: value } }))
  }
  const finishArtworkPosition = () => {
    if (sliderSnapshot.current) {
      setHistory(items => [...items.slice(-29), sliderSnapshot.current])
      sliderSnapshot.current = null
    }
  }
  const artworkControls = design[`${side}Image`] && <div className="artwork-position-controls">
    <div className="artwork-position-heading"><strong>Artwork position</strong><Button type="button" variant="outline" disabled={!artworkPosition.x && !artworkPosition.y} onClick={() => { commit({ ...design, [`${side}ArtworkPosition`]: { x: 0, y: 0 } }); setSelected('artwork') }}>Center artwork</Button></div>
    <p>Drag the image on the menu or fine-tune its position here.</p>
    <label>Horizontal <output>{Math.round(artworkPosition.x)}%</output><Slider aria-label="Artwork horizontal position" min={-85} max={85} value={[artworkPosition.x]} onPointerDown={() => { sliderSnapshot.current = design }} onFocus={() => { sliderSnapshot.current ||= design }} onValueChange={([value]) => changeArtworkPosition('x', value)} onValueCommit={finishArtworkPosition} /></label>
    <label>Vertical <output>{Math.round(artworkPosition.y)}%</output><Slider aria-label="Artwork vertical position" min={-85} max={85} value={[artworkPosition.y]} onPointerDown={() => { sliderSnapshot.current = design }} onFocus={() => { sliderSnapshot.current ||= design }} onValueChange={([value]) => changeArtworkPosition('y', value)} onValueCommit={finishArtworkPosition} /></label>
  </div>
  const project = `${template ? template.name + ' collection' : 'Custom blank design'}: ${describeConfig(config)}.\nFront:\n${design.front.map(item => item.text).join('\n')}
Back:\n${design.back.map(item => item.text).join('\n')}
${template && (design.frontImage === template.frontImage || design.backImage === template.backImage) ? 'Starts from the selected collection artwork.' : design.frontImage || design.backImage ? 'Includes uploaded artwork; files will be shared separately.' : ''}`
  return <section className="menu-builder studio-v2">
    <header className="designer-heading"><div className="builder-heading-main"><p className="eyebrow">Your menu studio</p><h1>{template ? template.name : 'Make it yours.'}</h1><div className="builder-context"><Badge variant="secondary">{template ? 'Editable collection recreation' : 'Design from scratch'}</Badge></div></div><Button variant="outline" asChild><Link to="/collections">Browse collections &rarr;</Link></Button></header>
    <div className="designer-toolbar"><div><Button type="button" onClick={undo} disabled={!history.length}><Undo2 size={14} />Undo</Button><Button type="button" onClick={() => { commit(initialDesign); setSelected(null); setStatus('Design reset. Undo restores the previous design.') }}><RotateCcw size={14} />Reset</Button></div><div className="designer-toolbar-actions"><TooltipProvider><Tooltip><TooltipTrigger asChild><Button type="button" variant="outline" size="icon-sm" className="toolbar-info-button" aria-label="View configuration"><Info size={15} /></Button></TooltipTrigger><TooltipContent className="builder-tooltip" side="bottom" align="end" sideOffset={10} collisionPadding={12}><div className="builder-tooltip-title"><Info size={14} /><strong>Your configuration</strong></div><dl className="builder-tooltip-specs"><div><dt>Quantity</dt><dd>{config.quantity} menus</dd></div><div><dt>Size</dt><dd>{size.width} × {size.height} in</dd></div><div><dt>Finish</dt><dd>{finishes.find(item => item.id === config.finish)?.label}</dd></div><div><dt>Card</dt><dd>{thicknesses.find(item => item.id === config.thickness)?.label}</dd></div><div><dt>Glow</dt><dd><i style={{ '--swatch': glow.hex }} />{glow.label}</dd></div></dl><p className="builder-tooltip-note">Final pricing is confirmed by quote.</p></TooltipContent></Tooltip><Tooltip><TooltipTrigger asChild><Button asChild type="button" size="icon-sm" className="toolbar-quote-button"><Link to="/contact" state={{ project }} aria-label="Request design quote"><DollarSign size={16} /></Link></Button></TooltipTrigger><TooltipContent className="builder-tooltip" side="bottom" align="end" sideOffset={10} collisionPadding={12}><div className="builder-tooltip-title"><DollarSign size={14} /><strong>Request a design quote</strong></div><p>We’ll include your current menu specifications with your inquiry.</p><p className="builder-tooltip-note">Final pricing is confirmed after review.</p></TooltipContent></Tooltip></TooltipProvider></div></div>
    <div className="designer-workspace"><aside className="designer-controls"><Tabs value={tab} onValueChange={setTab}><TabsList className="designer-tabs" aria-label="Editor panels"><TabsTrigger value="layers"><Layers size={14} />Layers</TabsTrigger><TabsTrigger value="specs"><SlidersHorizontal size={14} />Options</TabsTrigger><TabsTrigger value="text"><Type size={14} />Text</TabsTrigger><TabsTrigger value="art"><ImagePlus size={14} />Artwork</TabsTrigger></TabsList>
      <TabsContent value="specs"><MenuOptions config={config} onChange={setConfig} /></TabsContent>
      <TabsContent value="text"><div className={`text-controls ${layer ? 'is-editing' : ''}`}><div className="panel-intro"><h2>Text layers</h2></div><Button type="button" onClick={() => addText()}>+ Add text layer</Button><div className="text-presets" role="group" aria-label="Text starting points"><Button variant="outline" onClick={() => addText('heading')}>Add heading</Button><Button variant="outline" onClick={() => addText('item')}>Add menu item</Button></div>{layer && <><label>Text content<Textarea value={layer.text} onFocus={() => { textEditSnapshot.current = design }} onChange={event => setDesign(current => ({ ...current, [side]: current[side].map(item => item.id === selected ? { ...item, text: event.target.value } : item) }))} onBlur={() => { if (textEditSnapshot.current) { setHistory(items => [...items.slice(-29), textEditSnapshot.current]); textEditSnapshot.current = null } }} rows={4} maxLength={600} /></label><div className="type-format-controls"><ToggleGroup type="single" value={layer.align || 'middle'} onValueChange={align => { if (align) changeLayer({ align }) }} aria-label="Text alignment"><ToggleGroupItem value="start" aria-label="Align left"><AlignLeft size={16} /></ToggleGroupItem><ToggleGroupItem value="middle" aria-label="Align center"><AlignCenter size={16} /></ToggleGroupItem><ToggleGroupItem value="end" aria-label="Align right"><AlignRight size={16} /></ToggleGroupItem></ToggleGroup><ToggleGroup type="multiple" value={[...(layer.bold ? ['bold'] : []), ...(layer.italic ? ['italic'] : [])]} onValueChange={values => changeLayer({ bold: values.includes('bold'), italic: values.includes('italic') })} aria-label="Text style"><ToggleGroupItem value="bold" aria-label="Bold text"><Bold size={16} /></ToggleGroupItem><ToggleGroupItem value="italic" aria-label="Italic text"><Italic size={16} /></ToggleGroupItem></ToggleGroup></div><label>Type size <output>{layer.size} px</output><Slider aria-label="Type size" min={8} max={56} value={[layer.size]} onPointerDown={() => { sliderSnapshot.current = design }} onValueChange={([size]) => setDesign(current => ({ ...current, [side]: current[side].map(item => item.id === selected ? { ...item, size } : item) }))} onValueCommit={() => { if (sliderSnapshot.current) { setHistory(items => [...items.slice(-29), sliderSnapshot.current]); sliderSnapshot.current = null } }} /></label><label>Horizontal position <output>{Math.round(layer.x)}%</output><Slider aria-label="Horizontal position" min={5} max={95} value={[layer.x]} onPointerDown={() => { sliderSnapshot.current = design }} onValueChange={([x]) => setDesign(current => ({ ...current, [side]: current[side].map(item => item.id === selected ? { ...item, x } : item) }))} onValueCommit={() => { if (sliderSnapshot.current) { setHistory(items => [...items.slice(-29), sliderSnapshot.current]); sliderSnapshot.current = null } }} /></label><label>Vertical position <output>{Math.round(layer.y)}%</output><Slider aria-label="Vertical position" min={5} max={95} value={[layer.y]} onPointerDown={() => { sliderSnapshot.current = design }} onValueChange={([y]) => setDesign(current => ({ ...current, [side]: current[side].map(item => item.id === selected ? { ...item, y } : item) }))} onValueCommit={() => { if (sliderSnapshot.current) { setHistory(items => [...items.slice(-29), sliderSnapshot.current]); sliderSnapshot.current = null } }} /></label><label className="editor-check"><Switch aria-label="Add glow to this text" checked={layer.glow} onCheckedChange={glow => changeLayer({ glow })} />Add glow to this text</label><Button type="button" onClick={() => { const id = `text-${Date.now()}`; commit({ ...design, [side]: [...design[side], { ...layer, id, y: Math.min(95, layer.y + 6) }] }); setSelected(id) }}><Copy size={14} />Duplicate layer</Button><Button type="button" onClick={() => { commit({ ...design, [side]: design[side].filter(item => item.id !== selected) }); setSelected(null) }}>Remove layer</Button></>}</div></TabsContent>
      <TabsContent value="art"><div className="text-controls"><div className="panel-intro"><h2>Artwork &amp; color</h2></div><div className="canvas-colors">{[['background', 'Background'], ['ink', 'Text'], ['accent', 'Accent']].map(([key, label]) => <label key={key}>{label}<Input type="color" aria-label={`${label} color`} value={design[key] || '#101b19'} onChange={event => commit({ ...design, [key]: event.target.value })} /></label>)}</div><div className="font-choices" role="group" aria-label="Font style"><Button aria-pressed={design.font !== 'serif'} onClick={() => commit({ ...design, font: 'sans' })}>Modern sans</Button><Button aria-pressed={design.font === 'serif'} onClick={() => commit({ ...design, font: 'serif' })}>Classic serif</Button></div><h2>Artwork for {side}</h2>{template && <div className="artwork-choice-grid"><button type="button" className={`artwork-choice ${design[`${side}Image`] === template[`${side}Image`] ? 'is-selected' : ''}`} onClick={() => chooseArtwork(true)}><img src={template[`${side}Image`]} alt={`${template.name} original ${side} artwork`} /><span><strong>Use original artwork</strong><small>Keep the complete menu image</small></span></button><button type="button" className={`artwork-choice artwork-choice-recreate ${!design[`${side}Image`] ? 'is-selected' : ''}`} onClick={() => chooseArtwork(false)}><span className="artwork-type-icon">Tt</span><span><strong>Recreate with editable text</strong><small>Build this side from editable text layers</small></span></button></div>}{template && <p className="editor-hint">Original artwork is one image. Use the editable version to change text.</p>}<label className="artwork-upload"><ImagePlus size={24} /><strong>Add your artwork</strong><span>Choose a file for the {side} of your menu</span><Input aria-label="Upload image" type="file" accept="image/png,image/jpeg,image/webp" onChange={upload} /></label><small>PNG, JPEG, WebP · up to 4 MB</small>{design[`${side}Image`] && <div className="uploaded-art-preview"><img src={design[`${side}Image`]} alt={`Current ${side} menu artwork`} /><div><strong>{template && design[`${side}Image`] === template[`${side}Image`] ? `${template.name} artwork` : 'Artwork loaded'}</strong><span>Fills the {side} menu background</span></div><Button type="button" variant="outline" onClick={() => { commit({ ...design, [`${side}Image`]: '', [`${side}ArtworkPosition`]: { x: 0, y: 0 } }); setSelected(current => current === 'artwork' ? null : current) }}>Remove</Button></div>}{artworkControls}</div></TabsContent>
    <TabsContent value="layers"><div className="studio-layers">
      <div className="layers-heading"><p className="eyebrow"><Layers size={14} /> Layers <span>/ {side}</span></p><span>{layerCountFor(side)} layers</span></div>
      <p className="layers-hint">Frontmost at top.</p>
      <div className="layer-list">
        {[...design[side]].reverse().map((item, index) => <div className="text-layer-row" key={item.id}>
          <Button type="button" aria-pressed={selected === item.id} onClick={() => setSelected(item.id)}><span>T</span><span>{item.text.split('\n')[0] || 'Empty text'}<small>Text layer / {index + 1}</small></span>{item.glow && <small>Glow</small>}</Button>
          <div><Button type="button" variant="ghost" aria-label={`Bring ${item.text.split('\n')[0]} forward`} disabled={index === 0} onClick={() => reorderLayer(item.id, 1)}><ChevronUp size={14} /></Button><Button type="button" variant="ghost" aria-label={`Send ${item.text.split('\n')[0]} backward`} disabled={index === design[side].length - 1} onClick={() => reorderLayer(item.id, -1)}><ChevronDown size={14} /></Button></div>
        </div>)}
        {design[`${side}Image`] && <Button type="button" className="art-layer-row" aria-pressed={selected === 'artwork'} onClick={() => setSelected('artwork')}><img src={design[`${side}Image`]} alt="" /><span>{template && design[`${side}Image`] === template[`${side}Image`] ? `${template.name} artwork` : 'Uploaded artwork'}<small>Artwork / {side} / behind text</small></span></Button>}
        <Button type="button" className="art-layer-row background-layer-row" aria-pressed={selected === 'background'} onClick={() => setSelected('background')}><span className="background-layer-icon">BG</span><span>Canvas background<small>Color and finish</small></span></Button>
      </div>
      {selectedLabel && <div className="layer-selection-summary"><span className="layer-selection-kicker">LAYER {selectedLayerPosition} OF {layerCountFor(side)}</span><strong>{selectedLabel}</strong><Button type="button" variant="outline" onClick={() => setTab(selected === 'artwork' || selected === 'background' ? 'art' : 'text')}>Edit {selected === 'background' ? 'colors' : selected === 'artwork' ? 'artwork' : 'text'}</Button></div>}
      <Button type="button" className="add-layer-button" onClick={() => addText()}><Plus size={15} /> Add text layer</Button>
    </div></TabsContent></Tabs></aside><div className={`designer-stage ${lightsOff ? 'lights-off' : 'lights-on'}`}><div className="stage-toolbar"><ToggleGroup type="single" value={side} onValueChange={item => { if (item) { setSide(item); setSelected(null) } }} aria-label="Menu side">{['front', 'back'].map(item => <ToggleGroupItem key={item} value={item}>{item}<span className="side-layer-count">{layerCountFor(item)}</span></ToggleGroupItem>)}</ToggleGroup><Switch className="glow-preview-switch" checked={lightsOff} onCheckedChange={setLightsOff} aria-label="Toggle glow preview" /></div>
      <div className="canvas-area">{!design[side].length && !design[`${side}Image`] && <div className="canvas-empty"><Plus size={24} /><strong>Start your menu</strong><Button variant="outline" type="button" onClick={() => addText()}>Add text</Button></div>}<div className={`canvas-finish finish-${config.finish} thickness-${config.thickness}`}><svg ref={svgRef} xmlns="http://www.w3.org/2000/svg" viewBox={`0 0 400 ${height}`} className="design-canvas" style={{ aspectRatio: `400/${height}`, '--preview-glow': lightsOff ? glow.hex : 'transparent' }} aria-label={`${side} menu design preview`} tabIndex={0} onKeyDown={nudgeSelected} onPointerMove={move} onPointerUp={() => { drag.current = null }} onPointerCancel={() => { drag.current = null }}>
        <defs><filter id="text-glow"><feGaussianBlur stdDeviation="2" result="blur" /><feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge></filter></defs><rect width="400" height={height} fill={design.background || '#101b19'} onPointerDown={() => { setSelected('background'); setTab('art'); svgRef.current.focus() }} />
        {design.decoration === 'frame' && <rect x="24" y="24" width="352" height={height - 48} fill="none" stroke={design.accent} strokeWidth="2" />}
        {design.decoration === 'stripe' && <rect x="350" y="0" width="26" height={height} fill={design.accent} opacity=".5" />}
        {design.decoration === 'rules' && <><line x1="50" x2="350" y1={height * .1} y2={height * .1} stroke={design.accent} /><line x1="50" x2="350" y1={height * .9} y2={height * .9} stroke={design.accent} /></>}
        {design[`${side}Image`] && <image href={design[`${side}Image`]} x={(design[`${side}ArtworkPosition`]?.x || 0) * 4} y={(design[`${side}ArtworkPosition`]?.y || 0) / 100 * height} width="400" height={height} preserveAspectRatio="xMidYMid meet" role="button" tabIndex="0" aria-label={`Select and move ${side} artwork layer`} onClick={() => { setSelected('artwork'); setTab('art') }} onKeyDown={event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); setSelected('artwork'); setTab('art') } }} onPointerDown={event => { event.stopPropagation(); event.preventDefault(); setSelected('artwork'); setTab('art'); const position = design[`${side}ArtworkPosition`] || { x: 0, y: 0 }; drag.current = { type: 'artwork', pointer: { x: (event.clientX - svgRef.current.getBoundingClientRect().left) / svgRef.current.getBoundingClientRect().width * 400, y: (event.clientY - svgRef.current.getBoundingClientRect().top) / svgRef.current.getBoundingClientRect().height * height }, origin: position, snapshot: design, moved: false }; svgRef.current.focus(); svgRef.current.setPointerCapture(event.pointerId) }} style={{ cursor: selected === 'artwork' ? 'grabbing' : 'grab' }} />}{selected === 'artwork' && design[`${side}Image`] && <g data-selection="true" transform={`translate(${(design[`${side}ArtworkPosition`]?.x || 0) * 4} ${(design[`${side}ArtworkPosition`]?.y || 0) / 100 * height})`} pointerEvents="none"><rect x="3" y="3" width="394" height={height - 6} fill="none" stroke="#c5f78b" strokeWidth="3" strokeDasharray="8 5" /><circle cx="5" cy="5" r="5" fill="#c5f78b" stroke="#101810" strokeWidth="2" /><circle cx="395" cy="5" r="5" fill="#c5f78b" stroke="#101810" strokeWidth="2" /><circle cx="5" cy={height - 5} r="5" fill="#c5f78b" stroke="#101810" strokeWidth="2" /><circle cx="395" cy={height - 5} r="5" fill="#c5f78b" stroke="#101810" strokeWidth="2" /></g>}
        {design[side].map(item => <g key={item.id} onPointerDown={event => { event.preventDefault(); setSelected(item.id); setTab('text'); const bounds = svgRef.current.getBoundingClientRect(); drag.current = { type: 'text', id: item.id, origin: { x: item.x, y: item.y }, pointer: { x: (event.clientX - bounds.left) / bounds.width * 400, y: (event.clientY - bounds.top) / bounds.height * height }, snapshot: design, moved: false }; svgRef.current.focus(); svgRef.current.setPointerCapture(event.pointerId) }} style={{ cursor: 'move' }}><text x={item.x * 4} y={item.y / 100 * height} textAnchor={item.align || 'middle'} fontWeight={item.bold ? 700 : 400} fontStyle={item.italic ? 'italic' : 'normal'} fontFamily={design.font === 'serif' ? 'Georgia, serif' : 'Arial, sans-serif'} fontSize={item.size} fill={item.glow ? (lightsOff ? glow.hex : design.accent || glow.hex) : design.ink || '#f0efe3'} filter={item.glow && lightsOff ? 'url(#text-glow)' : undefined}>{item.text.split('\n').map((line, index) => <tspan key={index} x={item.x * 4} dy={index ? item.size * 1.5 : 0}>{line || ' '}</tspan>)}</text>{selected === item.id && <g data-selection="true" pointerEvents="none"><rect x={textBounds(item).x} y={textBounds(item).y} width={textBounds(item).width} height={textBounds(item).height} fill="#c5f78b0b" stroke="#c5f78b" strokeWidth="1.5" strokeDasharray="5 3" rx="4" /><circle cx={textBounds(item).x} cy={textBounds(item).y} r="4" fill="#c5f78b" stroke="#0b1109" strokeWidth="1.5" /><circle cx={textBounds(item).x + textBounds(item).width} cy={textBounds(item).y} r="4" fill="#c5f78b" stroke="#0b1109" strokeWidth="1.5" /><circle cx={textBounds(item).x} cy={textBounds(item).y + textBounds(item).height} r="4" fill="#c5f78b" stroke="#0b1109" strokeWidth="1.5" /><circle cx={textBounds(item).x + textBounds(item).width} cy={textBounds(item).y + textBounds(item).height} r="4" fill="#c5f78b" stroke="#0b1109" strokeWidth="1.5" /></g>}</g>)}
        {selected === 'background' && <rect data-selection="true" x="3" y="3" width="394" height={height - 6} fill="none" stroke="#c5f78b" strokeWidth="3" strokeDasharray="8 5" pointerEvents="none" />}
      </svg></div></div><p className="stage-caption"><span className="preview-indicator" aria-hidden="true" />{size.width} &times; {size.height} in &middot; {side} &middot; Preview only</p>
    </div></div>{status && <Alert className={`studio-status ${status.startsWith('Choose') ? 'is-error' : ''}`} role="status">{status.includes('added') ? <CircleCheck size={16} /> : <CircleAlert size={16} />}<AlertDescription>{status}</AlertDescription></Alert>}
  </section>
}
