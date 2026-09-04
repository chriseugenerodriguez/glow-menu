import { useState } from 'react'
import emailjs from '@emailjs/browser'

const initialForm = { name: '', email: '', company: '', project: '' }

function ContactPage() {
  const [formData, setFormData] = useState(initialForm)
  const [status, setStatus] = useState({ type: 'idle', message: '' })
  const handleChange = ({ target }) => setFormData((current) => ({ ...current, [target.name]: target.value }))

  const handleSubmit = async (event) => {
    event.preventDefault()
    setStatus({ type: 'loading', message: 'Sending your inquiry...' })
    const serviceId = import.meta.env.VITE_EMAILJS_SERVICE_ID
    const templateId = import.meta.env.VITE_EMAILJS_TEMPLATE_ID
    const publicKey = import.meta.env.VITE_EMAILJS_PUBLIC_KEY

    if (!serviceId || !templateId || !publicKey) {
      const subject = encodeURIComponent(`Glow Menu inquiry from ${formData.name}`)
      const body = encodeURIComponent(`Name: ${formData.name}\nEmail: ${formData.email}\nCompany: ${formData.company || 'N/A'}\n\nProject details:\n${formData.project}`)
      window.location.href = `mailto:hello@glowmenu.co?subject=${subject}&body=${body}`
      setStatus({ type: 'success', message: 'Your email client opened so you can send the inquiry directly.' })
      return
    }

    try {
      await emailjs.send(serviceId, templateId, { from_name: formData.name, from_email: formData.email, company: formData.company, message: formData.project }, publicKey)
      setStatus({ type: 'success', message: 'Thanks! Your inquiry was sent successfully.' })
      setFormData(initialForm)
    } catch (error) {
      console.error('EmailJS failed:', error)
      setStatus({ type: 'error', message: 'The form could not send. Please email hello@glowmenu.co directly.' })
    }
  }

  return (
    <section className="page-section contact-page">
      <div className="contact-intro"><p className="eyebrow">Contact Glow Menu</p><h2>Let's design a menu experience your guests remember.</h2><p>Share your brand, timeline, and the mood you want your menu to create. We'll shape a polished concept for your hospitality space.</p><div className="contact-details"><a href="mailto:hello@glowmenu.co">hello@glowmenu.co</a><span>Custom menus / event pieces / hospitality print</span></div></div>
      <form className="contact-form" onSubmit={handleSubmit}>
        <div className="field-row"><label>Name<input type="text" name="name" value={formData.name} onChange={handleChange} placeholder="Your name" required /></label><label>Email<input type="email" name="email" value={formData.email} onChange={handleChange} placeholder="you@example.com" required /></label></div>
        <label>Company / venue<input type="text" name="company" value={formData.company} onChange={handleChange} placeholder="Restaurant, bar, or event brand" /></label>
        <label>Project details<textarea name="project" value={formData.project} onChange={handleChange} placeholder="Tell us about the menu, vibe, and launch timeline..." rows="6" required /></label>
        <button type="submit" className="primary-btn contact-submit" disabled={status.type === 'loading'}>{status.type === 'loading' ? 'Sending...' : 'Send inquiry'}</button>
        {status.type !== 'idle' && <div role="status" className={`form-status form-status-${status.type}`}>{status.message}</div>}
      </form>
    </section>
  )
}

export default ContactPage
