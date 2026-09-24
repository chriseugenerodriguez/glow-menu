import { Link } from 'react-router-dom'

function NotFoundPage() {
  return (
    <section className="not-found-page">
      <div className="missing-page-art" aria-hidden="true">404<span>OFF THE MENU</span></div><p className="eyebrow">404 / Page not found</p>
      <h1>This page is no longer on the menu.</h1>
      <p>Let's find you a fresh starting point. Explore a collection or create a menu of your own.</p>
      <div className="not-found-actions">
        <Link className="primary-btn" to="/">Return home</Link>
        <Link className="text-link" to="/collections">Browse menus &rarr;</Link>
      </div>
    </section>
  )
}

export default NotFoundPage
