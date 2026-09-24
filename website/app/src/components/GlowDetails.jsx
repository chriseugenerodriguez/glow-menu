import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from './ui/accordion'
import { useState } from 'react'
import { collectionBuilderUrl } from '../data/builderTemplates'
import { Link } from 'react-router-dom'

const moods = [
  { name: 'After hours', tag: 'Bars & lounges', title: 'Turn the lights down.', copy: 'Deep tones, a little contrast, and a menu that feels at home after dark.', image: '/collections/chill_brew_back.jpg', collection: 'Chill Brew', color: 'mint' },
  { name: 'The occasion', tag: 'Weddings & private dining', title: 'Make a moment of it.', copy: 'Quiet typography and warm details for a table worth gathering around.', image: '/collections/the_ceremony_front.png', collection: 'The Ceremony', color: 'amber' },
  { name: 'Late nights', tag: 'Events & nightlife', title: 'Bring a little energy.', copy: 'Bold color and expressive layouts for a night with its own personality.', image: '/collections/glow_party_front.png', collection: 'Glow Party', color: 'violet' },
]

export default function GlowDetails() {
  const [selected, setSelected] = useState(0)
  const mood = moods[selected]
  return <>
    <section className="mood-section" aria-labelledby="mood-heading">
      <div className="home-section-heading"><div><p className="eyebrow">A little atmosphere</p><h2 id="mood-heading">Find your after-dark personality.</h2></div><span className="section-note">Three moods. Your own take.</span></div>
      <div className="mood-tabs" role="group" aria-label="Choose a menu mood">{moods.map((item, index) => <button key={item.name} type="button" aria-pressed={selected === index} onClick={() => setSelected(index)}><span className={`mood-dot ${item.color}`} />{item.name}</button>)}</div>
      <div className={`mood-panel mood-${mood.color}`}>
        <div className="mood-copy" aria-live="polite"><p className="eyebrow">{mood.tag}</p><h3>{mood.title}</h3><p>{mood.copy}</p><Link className="text-link" to={collectionBuilderUrl(mood.collection)}>Customize this collection &#8599;</Link></div>
        <div className="mood-art"><div className="mood-orbit" aria-hidden="true" /><img src={mood.image} alt={`${mood.collection} menu artwork`} loading="lazy" /><span className="art-caption">{mood.collection}<span>Collection preview</span></span></div>
      </div>
    </section>
    <section className="glow-process" aria-labelledby="process-heading"><div><p className="eyebrow">From an idea to your table</p><h2 id="process-heading">Make it yours.<br />One detail at a time.</h2></div><ol>{[
      ['Find your starting point', 'Choose a collection or bring an idea of your own.'],
      ['Shape the details', 'Share your brand, menu content, and the atmosphere you have in mind.'],
      ['Plan the finished piece', 'Discuss artwork, finishes, and production with us before moving ahead.'],
    ].map(([title, copy], index) => <li key={title}><span className="step-badge">0{index + 1}</span><div><h3>{title}</h3><p>{copy}</p></div></li>)}</ol></section>
    <section className="glow-faq" aria-labelledby="faq-heading"><div><p className="eyebrow">Good to know</p><h2 id="faq-heading">A few bright answers.</h2><Link className="text-link" to="/contact">Ask us something &#8599;</Link></div><Accordion type="single" collapsible className="faq-list">{[
      ['Can I start with my own design?', 'Yes. Send us your existing artwork or brand direction through the project form so we can discuss how to adapt it.'],
      ['Can I customize a collection?', 'Choose a collection as a starting point, then tell us about your content, colors, and venue. We can discuss the details together.'],
      ['How is my menu priced?', 'Pricing depends on size, finish, card thickness, glow color, and quantity. Configure your options and request a production quote.'],
      ['How do I get started?', 'Browse the collections or open the custom builder, then send a project inquiry. Your text and selected specifications will be included in the form.'],
    ].map(([question, answer]) => <AccordionItem value={question} key={question}><AccordionTrigger>{question}</AccordionTrigger><AccordionContent><p>{answer}</p></AccordionContent></AccordionItem>)}</Accordion></section>
    <section className="glow-outro"><div><p className="eyebrow">Set the tone for your next chapter</p><h2>Good evenings start<br />with a little glow.</h2></div><Link className="primary-btn" to="/contact">Start your project &#8599;</Link></section>
  </>
}
