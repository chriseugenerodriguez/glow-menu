# WordPress to React Migration Audit

Audited September 4, 2026 against `website/WordPress/wp-content/themes/glowmenu`,
the WordPress plugin inventory, uploads, and the current React application.

## Executive summary

The React application is a complete static marketing-site foundation, but it is
not yet a feature-equivalent migration of the WordPress site. It currently has
four routes: Home, Collections, Pricing, and Contact. The WordPress installation
also contains a WooCommerce storefront and product customizer, customer
accounts, cart and checkout, blog content, search/filtering, newsletter capture,
and third-party tracking/support integrations.

The migration must explicitly choose between rebuilding those services,
integrating hosted replacements, or retiring them. They should not disappear by
accident when DNS is switched.

## Migration gaps

### Launch blockers

- **Commerce flow:** WordPress supplies product archives, product details,
  quantity selection, cart, checkout, Stripe payments, order history, shipping,
  taxes/fees, coupons, wishlists, referrals, and tracking. React has no commerce
  backend or matching routes.
- **Menu customizer:** WordPress uses Fancy Product Designer plus custom theme
  behavior to edit a menu, save progress, resume a cart design, and calculate
  product options. The React collection cards only link to the contact form.
- **Accounts:** WordPress includes login, registration, password reset, social
  login, saved progress, and a WooCommerce account dashboard. React intentionally
  replaces this with a clearly labeled project-inquiry action.
- **Real content and pricing:** WordPress page bodies and product/catalog data
  live in its database, which is not present in this filesystem snapshot. React
  copy, statistics, tier names, and `$49/$129/$249` prices cannot be verified
  against the theme files and should be treated as provisional.
- **Production contact delivery:** React requires EmailJS environment values.
  Without them, submission opens the visitor's mail client rather than reliably
  delivering the inquiry.

### Important parity gaps

- **Blog:** WordPress has an index, paginated archives, category/tag/author/search
  views, rich single posts, gallery/image/video formats, comments, related posts,
  author details, and an article newsletter CTA. React has no blog routes or
  migrated articles.
- **Legacy print sizes:** Several legacy size images, including 8.5x11, 8.5x14,
  5.5x11, and 5.5x14, were not copied. The eight named menu collections are now
  exposed in React.
- **Catalog discovery:** The WordPress shop has design search, filters, sorting,
  result counts, pagination/show-all behavior, and category/tag archives. React
  presents a fixed list only.
- **Newsletter:** The WordPress footer/article experience uses Mailchimp forms.
  React has no newsletter subscription path.
- **Support and CRM:** The old theme initializes Slaask live chat and Agile CRM
  page tracking. React includes neither. Confirm whether these retired services
  should be replaced rather than copying obsolete keys.
- **Analytics:** React loads analytics only when `VITE_GA_MEASUREMENT_ID` is set.
- **Error handling:** React now provides a dedicated branded not-found view.
- **Legacy URL redirects:** WordPress URLs such as `/menus`, product/category
  URLs, `/blog`, article slugs, `/cart`, `/checkout`, and `/my-account` have no
  redirect map. This risks broken inbound links and lost search equity.

### Quality and content cleanup

- React uses only a simplified favicon/manifest set; verify final brand icons and
  social share artwork on iOS, Android, and pinned tabs.
- Most copied collection files are not byte-identical to the upload originals.
  Confirm that they were intentionally processed and retain sufficient print and
  high-density display quality.
- The React status/deployment documents were rewritten to match the current
  React 19 inquiry-led implementation and its actual production requirements.
- SEO metadata, sitemap URLs, canonical production domain, Open Graph imagery,
  consent/privacy behavior, and accessibility need a production-domain review.
- The migration has no automated component, route, form, or browser tests.

## What is already migrated

- Responsive shared header, navigation, footer, and mobile menu.
- Homepage hero carousel with autoplay, keyboard, pointer, and touch interaction.
- Full-width client-logo marquee.
- Marketing sections for process/features, collection previews, pricing, and CTA.
- Collections gallery with six visible collection styles and real menu artwork.
- Interactive pricing estimate UI.
- Contact page with EmailJS integration and mail-client fallback.
- Basic structured data, robots file, sitemap, manifest, Firebase Hosting SPA
  rewrites, cache headers, and static production build configuration.

## Repository audit

The uncommitted workspace should not be committed wholesale.

- `website/WordPress` is a separate, clean Git repository containing a complete
  WordPress installation and roughly 55,000 files within the broader `website`
  tree.
- Root-level PSDs and design folders are source artwork, not React runtime files.
- Root tracked deletions are old WordPress/plugin artifacts and are unrelated to
  the React migration commit; review them separately before restoring or deleting.
- Never commit live `wp-config.php`, SFTP configuration, logs, caches, uploads
  containing customer data, plugin dependencies, or `node_modules` into the React
  deployment repository.

## Recommended implementation order

1. Decide the target business flow: full ecommerce/customizer, hosted checkout,
   or inquiry-only. This determines most remaining architecture.
2. Export the WordPress database/content and create a verified inventory of URLs,
   products, prices, posts, taxonomies, users, orders, forms, and redirects.
3. Fix misleading/incomplete public paths: account CTA, 404, analytics, production
   contact delivery, catalog omissions, canonical URLs, and redirect rules.
4. Migrate blog/content and newsletter only if they remain part of the new site.
5. Add browser tests for navigation, carousel/touch behavior, contact delivery,
   responsive layouts, and whichever purchase flow is selected.
6. Run visual comparison at desktop and mobile sizes, accessibility checks,
   performance measurement, form testing, and redirect validation before launch.
