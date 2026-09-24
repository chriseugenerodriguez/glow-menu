import { Breadcrumb, BreadcrumbList, BreadcrumbItem, BreadcrumbLink, BreadcrumbPage, BreadcrumbSeparator } from '../components/ui/breadcrumb'
import { Link } from 'react-router-dom'
import { contentPages } from '../data/contentPages'

export default function ContentPage({ page }) {
  return <article className="content-page">
    <Breadcrumb className="breadcrumbs"><BreadcrumbList><BreadcrumbItem><BreadcrumbLink asChild><Link to="/">Home</Link></BreadcrumbLink></BreadcrumbItem><BreadcrumbSeparator /><BreadcrumbItem><BreadcrumbPage>{page.label}</BreadcrumbPage></BreadcrumbItem></BreadcrumbList></Breadcrumb>
    <header className="content-hero"><div><p className="eyebrow">{page.eyebrow}</p><h1>{page.heading}</h1><p>{page.intro}</p><div className="content-actions"><Link className="primary-btn" to="/contact" state={{ project: `I'd like to discuss ${page.label.toLowerCase()}.` }}>Plan your menu &#8599;</Link><Link className="text-link" to="/collections">Browse collections &rarr;</Link></div></div><div className="content-hero-art"><img src={page.image} alt={page.imageAlt} /></div></header>
    <nav className="article-nav" aria-label="On this page"><span>In this guide</span>{page.sections.map(([heading], index) => <a href={`#section-${index + 1}`} key={heading}>{heading}</a>)}</nav><div className="content-body"><div className="content-sections">{page.sections.map(([heading, text], index) => <section id={`section-${index + 1}`} key={heading}><span className="article-section-number">0{index + 1}</span><h2>{heading}</h2><p>{text}</p></section>)}</div><aside className="project-checklist"><p className="eyebrow">Your project checklist</p><h2>Bring your ideas.<br />We'll talk details.</h2><ul>{page.checklist.map(item => <li key={item}>{item}</li>)}</ul><Link className="text-link" to="/pricing">Configure your menu &rarr;</Link></aside></div>
    <section className="related-pages"><p className="eyebrow">Keep exploring</p><h2>Find the right direction.</h2><div>{page.related.map(path => { const item = contentPages.find(other => other.path === path); return <Link key={path} to={path}><h3>{item.label}<span aria-hidden="true">&#8599;</span></h3><p>{item.description}</p></Link> })}</div></section>
  </article>
}
