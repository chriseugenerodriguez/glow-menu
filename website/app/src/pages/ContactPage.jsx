import { useRef, useState } from 'react'
import { useLocation } from 'react-router-dom'
import { Input } from '../components/ui/input'
import { Textarea } from '../components/ui/textarea'
import { Button } from '../components/ui/button'
import { ArrowUpRight, Check, CircleAlert, CircleCheck, LoaderCircle, Sparkles } from 'lucide-react'
import { Label } from '../components/ui/label'
import { Alert, AlertDescription, AlertTitle } from '../components/ui/alert'
import { Badge } from '../components/ui/badge'
import { ToggleGroup, ToggleGroupItem } from '../components/ui/toggle-group'
import { saveInquiry } from '../lib/personal-data.js'

const initialForm = { name: '', email: '', company: '', project: '' }

function ContactPage() {
  const { state } = useLocation()
  const request = useRef(null)
  const [formData, setFormData] = useState(() => ({ ...initialForm, project: state?.project || '' }))
  const [status, setStatus] = useState({ type: 'idle', message: '' })
  const [projectType, setProjectType] = useState('')
  const handleChange = ({ target }) => setFormData((current) => ({ ...current, [target.name]: target.value }))

  const handleSubmit = async (event) => {
    event.preventDefault()
    setStatus({ type: 'loading', message: 'Saving your demo inquiry...' })
    const payload = {...formData, projectType}
    const fingerprint = JSON.stringify(payload)
    if (request.current?.fingerprint !== fingerprint) request.current = {fingerprint, id: crypto.randomUUID()}
    try {
      await saveInquiry({...payload, requestId: request.current.id})
      setStatus({ type: 'success', message: 'Your demo inquiry was saved. No email or order was sent.' })
      request.current = null
      setFormData(initialForm)
    } catch (error) {
      console.warn('Demo inquiry could not be saved')
      setStatus({ type: 'error', message: error.message || 'Your demo inquiry could not be saved. Please retry.' })
    }
  }

  return (
    <section className="page-section contact-page">
      <div className="contact-intro"><p className="eyebrow">Contact Glow Menu</p><h1>Let's make<br />something yours.</h1><p>Share your brand, timeline, and the mood you want your menu to create. We'll shape a polished concept for your hospitality space.</p><div className="contact-details"><a href="mailto:hello@glowmenu.co">hello@glowmenu.co</a><span>Custom menus / event pieces / hospitality print</span></div><div className="contact-next"><p className="eyebrow">What happens next</p>{['We review your idea and specifications', 'We discuss design and production details', 'You review the artwork before production'].map(text => <p key={text}><Check size={15} />{text}</p>)}</div></div>
      <form className="contact-form" onSubmit={handleSubmit}>
        <div className="contact-form-heading"><Badge variant="outline"><Sparkles size={13} /> PROJECT INQUIRY</Badge><h2>Tell us about your project</h2><p>A few details are all we need to get started.</p></div>
        <div className="field-row"><Label htmlFor="contact-name">Name<Input id="contact-name" type="text" autoComplete="name" name="name" value={formData.name} onChange={handleChange} placeholder="Your name" required /></Label><Label htmlFor="contact-email">Email<Input id="contact-email" type="email" autoComplete="email" name="email" value={formData.email} onChange={handleChange} placeholder="you@example.com" required /></Label></div>
        <fieldset className="project-type-field"><legend>What are you creating? <span>Optional</span></legend><ToggleGroup type="single" value={projectType} onValueChange={setProjectType} className="project-type-options" aria-label="Project type">{['Restaurant', 'Bar or lounge', 'Event'].map(type => <ToggleGroupItem key={type} value={type}>{type}</ToggleGroupItem>)}</ToggleGroup></fieldset><Label htmlFor="contact-company"><span>Company / venue <span className="field-optional">Optional</span></span><Input id="contact-company" type="text" autoComplete="organization" name="company" value={formData.company} onChange={handleChange} placeholder="Restaurant, bar, or event brand" /></Label>
        <Label htmlFor="contact-project">Project details<Textarea id="contact-project" name="project" value={formData.project} onChange={handleChange} placeholder="Tell us about the menu, vibe, and launch timeline..." rows="6" maxLength={1200} required /></Label><div className="message-meta"><span>A rough idea is a perfect place to start.</span><output>{formData.project.length} / 1,200</output></div>
        <Button type="submit" className="primary-btn contact-submit" disabled={status.type === 'loading'}>{status.type === 'loading' ? <><LoaderCircle className="sending-spinner" size={16} />Saving...</> : <>Save demo inquiry<ArrowUpRight size={16} /></>}</Button>
        {status.type !== 'idle' && <Alert role="status" variant={status.type === 'error' ? 'destructive' : 'default'} className={`form-status form-status-${status.type}`}><span className="form-status-icon">{status.type === 'error' ? <CircleAlert size={18} /> : <CircleCheck size={18} />}</span><AlertTitle>{status.type === 'error' ? 'We could not send that yet' : status.type === 'success' ? 'Demo inquiry saved' : 'Saving your inquiry'}</AlertTitle><AlertDescription>{status.message}</AlertDescription></Alert>}
      </form>
    </section>
  )
}

export default ContactPage
