# Deployment Guide

## Firebase Hosting

The included `firebase.json` publishes `dist`, serves React Router paths through
`index.html`, sets asset cache headers, and redirects the principal legacy
commerce URLs.

Create `.env.local` from `.env.example` and configure these variables before the
production build:

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

The repository is connected to the existing `glowmenu-1efe4` Firebase project.
Deploy from this directory:

```bash
firebase deploy --only hosting
```

For CI, build before running `firebase deploy --only hosting` and provide the
project ID through Firebase CLI or CI configuration.

After deployment, verify all routes and an unknown URL, direct route refreshes,
inquiry delivery, carousel controls and touch drag, redirects, canonical URLs,
the sitemap, and any configured analytics events.

## WordPress retirement

Keep WordPress available until its database-backed URLs and business records are
exported. React does not contain customer accounts, orders, product configuration
data, blog articles, or comments. Create a production URL inventory and add
specific redirects for valuable product and article URLs before switching DNS.
