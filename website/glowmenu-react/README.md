# Glow Menu React Site

The inquiry-led replacement for the legacy Glow Menu WordPress site, built with
React 19, React Router, and Vite.

## Local development

```bash
npm install
npm run dev
```

Public routes:

- `/` - homepage and menu carousel
- `/collections` - eight menu collections with full-size previews
- `/pricing` - interactive project estimator
- `/contact` - project inquiry form
- all unknown paths - branded not-found page

## Environment

Copy `.env.example` to `.env.local`. EmailJS values make the inquiry form submit
directly; without them, the form opens a pre-addressed email draft.
`VITE_GA_MEASUREMENT_ID` is optional and analytics stays unloaded when empty.

## Verification

```bash
npm run lint
npm run build
npm run preview
```

This site intentionally uses an inquiry flow. It does not recreate the old
WooCommerce checkout, customer accounts, or Fancy Product Designer. Review
`MIGRATION_AUDIT.md` before changing DNS or retiring WordPress. Firebase Hosting
deployment instructions are in `DEPLOYMENT.md`.
