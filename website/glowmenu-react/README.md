# Glow Menu – Modern React Front-End

A premium, multi-page React + Vite site for Glow Menu—custom menu design for hospitality brands.

> **No WordPress. No Firebase. Just fast, modern React.**

## 🚀 Features

- **Multi-page routing** with React Router
- **Premium hospitality branding** with curated styling
- **Real menu mockup gallery** showcasing all collection styles
- **Fully functional contact form** with optional email service integration
- **Responsive design** optimized for all devices
- **Fast builds** (<150ms with Vite)
- **Production-ready** deployment config for Vercel

## 📦 Quick Start

```bash
npm install
npm run dev
```

Open [http://localhost:5173](http://localhost:5173) in your browser.

## 🏗️ Project Structure

```
src/
├── pages/                    # Route pages
│   ├── HomePage.jsx
│   ├── CollectionsPage.jsx  # Full menu style gallery
│   ├── PricingPage.jsx
│   └── ContactPage.jsx      # Inquiry form
├── components/
│   └── Layout.jsx           # Shared header, nav, footer
├── data/
│   └── siteData.js          # All content (copy, collections, pricing)
├── App.jsx                  # Router config
├── App.css                  # Premium styling
└── main.jsx                 # Entry point

public/collections/         # Menu mockup images
```

## 🎨 Pages

- **Home** (`/`) – Hero, features, collection preview, pricing teaser, CTA
- **Collections** (`/collections`) – Full gallery of 6 menu styles with real mockup images
- **Pricing** (`/pricing`) – Pricing tiers and plan details
- **Contact** (`/contact`) – Inquiry form with email fallback

## 🛠️ Build & Deploy

```bash
npm run build    # Production build → dist/
npm run preview  # Test production locally
```

**Deploy to Vercel in 2 minutes:** See [DEPLOYMENT.md](DEPLOYMENT.md)

## ✏️ Customization

All content lives in `src/data/siteData.js`:
- Copy and messaging
- Pricing tiers
- Collection styles (with gallery images)
- Statistics and features

Edit CSS in `src/App.css` (all premium styling is centralized and well-commented).

## 📮 Contact Form

The form works in **two modes**:

1. **With EmailJS** – Direct email submission (requires API keys)
2. **Fallback** – Opens user's email client with pre-filled template (no setup needed)

See [DEPLOYMENT.md](DEPLOYMENT.md) for email setup.

## 📱 Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile-first responsive design
- Works on all screen sizes

## 🚀 Production Checklist

- [ ] Update site copy in `siteData.js`
- [ ] Replace mock collection images (or keep real Glow Menu mockups)
- [ ] Configure contact form (EmailJS or keep mailto fallback)
- [ ] Test all pages at `/`, `/collections`, `/pricing`, `/contact`
- [ ] Deploy to Vercel
- [ ] Set up custom domain
- [ ] Monitor form submissions

---

**Built with React + Vite. Hosted on Vercel. Zero backend required.**

