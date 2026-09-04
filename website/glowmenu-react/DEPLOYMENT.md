# Deployment Guide

## Vercel

Import the repository in Vercel and set the root directory to
`website/glowmenu-react`. The included `vercel.json` builds `dist`, serves React
Router paths through `index.html`, and redirects principal legacy commerce URLs.

Configure these project environment variables before the production build:

```text
VITE_EMAILJS_SERVICE_ID=
VITE_EMAILJS_TEMPLATE_ID=
VITE_EMAILJS_PUBLIC_KEY=
VITE_GA_MEASUREMENT_ID=
```

The EmailJS values are required for direct form delivery. Analytics is optional.
Never commit production values to Git.

## Pre-deployment checks

```bash
npm ci
npm run lint
npm run build
```

After deployment, verify all routes and an unknown URL, direct route refreshes,
inquiry delivery, carousel controls and touch drag, redirects, canonical URLs,
the sitemap, and any configured analytics events.

## WordPress retirement

Keep WordPress available until its database-backed URLs and business records are
exported. React does not contain customer accounts, orders, product configuration
data, blog articles, or comments. Create a production URL inventory and add
specific redirects for valuable product and article URLs before switching DNS.
