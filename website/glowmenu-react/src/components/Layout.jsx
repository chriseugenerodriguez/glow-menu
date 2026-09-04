import { useState } from 'react'
import { NavLink, Outlet, useLocation } from 'react-router-dom'

function Layout() {
  const [menuOpen, setMenuOpen] = useState(false)
  const { pathname } = useLocation()

  return (
    <div className="page-shell">
      <header className={`topbar ${pathname === '/' ? 'home-topbar' : ''}`}>
        <button className="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded={menuOpen} onClick={() => setMenuOpen((open) => !open)}>
          <span /><span /><span />
        </button>

        <nav className={`nav ${menuOpen ? 'nav-open' : ''}`} aria-label="Main navigation">
          <NavLink to="/collections" onClick={() => setMenuOpen(false)}>Menus</NavLink>
          <NavLink to="/contact" onClick={() => setMenuOpen(false)}>Custom</NavLink>
          <NavLink to="/pricing" onClick={() => setMenuOpen(false)}>Pricing</NavLink>
        </nav>

        <NavLink to="/" className="brand" aria-label="Glow Menu home">
          <img src="/glow-menu-logo.png" alt="Glow Menu" draggable="false" />
        </NavLink>

        <div className="header-actions">
          <span className="cart-label">Bag &nbsp; $0.00</span>
          <img className="cart-preview" src="/collections/cb_b_glow.jpg" alt="Selected menu preview" />
          <NavLink className="nav-cta" to="/contact">My account</NavLink>
        </div>
      </header>

      <main>
        <Outlet />
      </main>

      <footer className="footer">
        <img src="/glow-menu-logo.png" alt="Glow Menu" />
        <span>Hospitality design for memorable guest experiences. &copy; {new Date().getFullYear()}</span>
      </footer>
    </div>
  )
}

export default Layout
