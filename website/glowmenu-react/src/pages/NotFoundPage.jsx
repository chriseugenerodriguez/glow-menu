import { Link } from 'react-router-dom'

function NotFoundPage() {
  return (
    <section className="not-found-page">
      <p className="eyebrow">404 / Page not found</p>
      <h1>This page is no longer on the menu.</h1>
      <p>The address may have changed during our move from WordPress.</p>
      <div className="not-found-actions">
        <Link className="primary-btn" to="/">Return home</Link>
        <Link className="text-link" to="/collections">Browse menus &rarr;</Link>
      </div>
    </section>
  )
}

export default NotFoundPage
