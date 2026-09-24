# Glow Menu React Site

The inquiry-led replacement for the legacy Glow Menu WordPress site, built with
React 19, React Router, and Vite.

## Local development

Run these commands from the repository root or from `website/app`:

```bash
npm install
npm run dev
```

Public routes:

- `/` - homepage and menu carousel
- `/collections` - eight menu collections with full-size previews
- `/pricing` - size, finish, thickness, and glow configurator
- `/custom` - interactive front/back menu designer with local drafts and SVG concepts
- `/contact` - project inquiry form
- all unknown paths - branded not-found page

## Environment

Copy `.env.example` to `.env.local`. EmailJS values make the inquiry form submit
directly; without them, the form opens a pre-addressed email draft.
`VITE_GA_MEASUREMENT_ID` is optional and analytics stays unloaded when empty.

## Verification

The build prerenders all ten public routes with page-specific titles,
descriptions, canonical URLs, social metadata, and WebPage structured data.
Route metadata and the generated sitemap share `src/data/contentPages.js`.
New service pages cover custom design, restaurants, bars, and events; the
menu design guide is linked from the header and footer.

```bash
npm run lint
npm run build
npm run preview
```

This site intentionally uses an inquiry flow. It does not recreate the old
WooCommerce checkout, customer accounts, or Fancy Product Designer. Review
`MIGRATION_AUDIT.md` before changing DNS or retiring WordPress. Firebase Hosting
deployment instructions are in `DEPLOYMENT.md`.

Builder recovery and public pricing benchmarks are documented in `BUILDER_RESEARCH.md`. Current production prices require a quote; standard-print benchmark prices do not include glow treatment.
