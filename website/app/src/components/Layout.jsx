import { NavLink, Outlet, useLocation } from 'react-router-dom'
import './Layout.css'
import { contentPages } from '../data/contentPages'
import { Sheet, SheetContent, SheetHeader, SheetFooter, SheetTitle, SheetDescription, SheetTrigger, SheetClose } from './ui/sheet'
import { ArrowUpRight, Compass, Layers3, Sparkles } from 'lucide-react'

function Layout() {
  const { pathname } = useLocation()

  return (
    <div className="page-shell dark-theme">
      <a className="skip-link" href="#main-content">Skip to content</a>
      <header className={`topbar ${pathname === '/' ? 'home-topbar' : ''}`}>
        <Sheet>
          <SheetTrigger asChild><button className="menu-toggle" type="button" aria-label="Open navigation"><span /><span /><span /></button></SheetTrigger>
          <SheetContent side="right" className="mobile-nav-sheet">
            <SheetHeader className="mobile-nav-heading"><img src="/glow-menu-logo.svg" alt="" /><SheetTitle>Find your next starting point.</SheetTitle><SheetDescription>Make the menu yours, one detail at a time.</SheetDescription></SheetHeader>
            <nav className="mobile-nav-links" aria-label="Mobile navigation">
              {[['/collections', 'Menus & collections', Layers3], ['/custom', 'Custom menu studio', Sparkles], ['/pricing', 'Sizes & pricing', Compass], ['/menu-design-guide', 'Design inspiration', ArrowUpRight]].map(([to, label, Icon]) => <SheetClose asChild key={to}><NavLink to={to}><Icon size={18} /><span>{label}</span><ArrowUpRight className="mobile-nav-arrow" size={15} /></NavLink></SheetClose>)}
            </nav>
            <SheetFooter className="mobile-nav-footer"><p>Have a project in mind?</p><SheetClose asChild><NavLink className="mobile-nav-cta" to="/contact">Let&apos;s talk <ArrowUpRight size={16} /></NavLink></SheetClose><a href="mailto:hello@glowmenu.co">hello@glowmenu.co</a></SheetFooter>
          </SheetContent>
        </Sheet>

        <nav id="main-navigation" className="nav" aria-label="Main navigation">
          <NavLink to="/collections">Menus</NavLink>
          <NavLink to="/custom">Custom builder</NavLink>
          <NavLink to="/pricing">Pricing</NavLink>
          <NavLink to="/menu-design-guide">Design guide</NavLink>
        <NavLink className="mobile-contact-link" to="/contact">Start a project &rarr;</NavLink></nav>

        <NavLink to="/" className="brand" aria-label="Glow Menu home">
          <img src="/glow-menu-logo.svg" alt="Glow Menu" draggable="false" />
        </NavLink>

        <div className="header-actions">
          <NavLink className="nav-cta" to="/contact">Start a project</NavLink>
        </div>
      </header>

      <main id="main-content" tabIndex={-1}>
        <Outlet />
      </main>

      <footer className="footer site-footer">
        <div className="footer-intro"><NavLink to="/" aria-label="Glow Menu home"><img src="/glow-menu-logo.svg" alt="Glow Menu" /></NavLink><h2>Set the mood.<br />Make it glow.</h2><p>Distinctive menus for spaces with personality.</p><a href="mailto:hello@glowmenu.co">hello@glowmenu.co</a></div>
        <nav className="footer-link-group" aria-label="Explore"><h3>Explore</h3><NavLink to="/collections">Menu collections</NavLink><NavLink to="/custom">Custom builder</NavLink><NavLink to="/custom-menu-design">Custom design guide</NavLink><NavLink to="/pricing">Pricing & quotes</NavLink><NavLink to="/contact">Start a project</NavLink></nav>
        <nav className="footer-link-group" aria-label="Menus for your space"><h3>Your space</h3>{contentPages.filter(page => ['/restaurant-menus', '/bar-menus', '/event-menus'].includes(page.path)).map(page => <NavLink key={page.path} to={page.path}>{page.label}</NavLink>)}</nav>
        <nav className="footer-link-group" aria-label="Resources"><h3>Good to know</h3><NavLink to="/menu-design-guide">Menu design guide</NavLink><NavLink to="/contact">Contact us</NavLink><NavLink to="/pricing">Plan your budget</NavLink></nav>
        <div className="footer-bottom"><span>&copy; {new Date().getFullYear()} Glow Menu</span><span>Designed around your atmosphere.</span></div>
      </footer>
    </div>
  )
}

export default Layout
