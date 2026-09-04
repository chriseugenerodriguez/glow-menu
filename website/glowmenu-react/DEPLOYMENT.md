# Glow Menu – Deployment & Setup Guide

This is a modern React + Vite site replacing the legacy WordPress backend. It's fully front-end with optional email service integration.

## Quick Start (Local Development)

```bash
cd website/glowmenu-react
npm install
npm run dev
```

Open http://localhost:5173/ in your browser.

## Production Build

```bash
npm run build
npm run preview
```

Build output goes to `dist/`. This is production-ready.

## Deployment to Vercel

Vercel is the recommended hosting platform for this app—it's built for Vite and React apps.

### Step 1: Push to GitHub
```bash
git init
git add .
git commit -m "Initial commit: Glow Menu React site"
git remote add origin https://github.com/your-username/glow-menu-react.git
git push -u origin main
```

### Step 2: Connect to Vercel
1. Go to [vercel.com](https://vercel.com)
2. Sign in or create a free account
3. Click **"Add New..."** → **"Project"**
4. Select your GitHub repo
5. Vercel auto-detects Vite; click **"Deploy"**

Your site will be live at `your-project.vercel.app`

### Step 3: Configure Environment Variables (Optional)

If you want the contact form to send emails directly (without falling back to mailto):

1. Get EmailJS API keys from [emailjs.com](https://emailjs.com)
   - Sign up for free
   - Create a Service (e.g., "Gmail")
   - Create an Email Template
   - Copy your Service ID, Template ID, and Public Key

2. In Vercel dashboard:
   - Go to **Settings** → **Environment Variables**
   - Add three new variables:
     - `VITE_EMAILJS_SERVICE_ID` = your service id
     - `VITE_EMAILJS_TEMPLATE_ID` = your template id
     - `VITE_EMAILJS_PUBLIC_KEY` = your public key
   - Redeploy: Click **Deployments** → the latest → **Redeploy**

3. Test the contact form at `your-site.com/contact`

## Local Email Setup (Optional)

If you want to test EmailJS locally:

1. Copy `.env.example` to `.env.local`:
   ```bash
   cp .env.example .env.local
   ```

2. Fill in your EmailJS keys:
   ```
   VITE_EMAILJS_SERVICE_ID=your_id
   VITE_EMAILJS_TEMPLATE_ID=your_id
   VITE_EMAILJS_PUBLIC_KEY=your_key
   ```

3. Restart dev server: `npm run dev`

## SEO & Analytics Setup

The site includes comprehensive SEO features out of the box:

### What's Included
- ✓ Dynamic meta tags (title, description, keywords)
- ✓ Open Graph tags for social media sharing
- ✓ Twitter Card support
- ✓ Structured data (JSON-LD schema markup)
- ✓ Sitemap.xml for search engines
- ✓ Robots.txt for crawler directives
- ✓ Dynamic page titles on navigation

### Setup Google Analytics

1. Create a Google Analytics 4 account at [analytics.google.com](https://analytics.google.com)
2. Create a property for your domain
3. Copy your **Measurement ID** (starts with `G-`)
4. Update `index.html`:
   ```html
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-YOUR_ID"></script>
   <script>
     gtag('config', 'G-YOUR_ID');
   </script>
   ```
5. Deploy to Vercel
6. Wait 24 hours for data to start appearing

### Custom Domain SEO

Once you add a custom domain (e.g., `glowmenu.co`):
1. Update all URLs in `index.html` from `https://glowmenu.co/` to your actual domain
2. Update `vercel.json` and `DEPLOYMENT.md` references
3. Submit sitemap to Google Search Console
4. Monitor search performance in Google Search Console

### Monitoring Performance

- **Google Search Console** – Monitor clicks, impressions, CTR
- **Google Analytics** – Track visitor behavior, conversions
- **Vercel Analytics** – Monitor site performance and Core Web Vitals
- **Contact form submissions** – Check for inquiries via EmailJS or email inbox

## Fallback Behavior

If EmailJS keys are missing, the contact form automatically falls back to a `mailto:` link. This means:
- No email service needed for a working contact flow
- User's email client opens with a pre-filled template
- Fully functional without any API setup

## Project Structure

```
website/glowmenu-react/
├── src/
│   ├── pages/           # Route pages (Home, Collections, Pricing, Contact)
│   ├── components/      # Shared Layout and UI components
│   ├── data/            # Content data (siteData.js)
│   ├── App.jsx          # Router config
│   ├── App.css          # Premium styling
│   └── main.jsx         # Entry point
├── public/
│   └── collections/     # Menu mockup images
├── dist/                # Production build (after npm run build)
├── vercel.json          # Vercel deployment config
├── .env.example         # Environment template
└── package.json
```

## Key Features

✓ **No Firebase** – Fully front-end, zero backend dependencies  
✓ **No WordPress** – Pure React + modern tooling  
✓ **Responsive Design** – Mobile-first, works on all devices  
✓ **Fast Build** – Vite builds in <150ms  
✓ **SEO-Ready** – Clean HTML, proper meta tags, structured data  
✓ **Email Integration** – Optional EmailJS or mailto fallback  
✓ **Collections Gallery** – Real menu mockup images  
✓ **Contact Form** – Fully functional inquiry flow  

## Customization

### Update Site Copy
Edit `src/data/siteData.js` – all text, pricing, and collection data lives here.

### Change Colors & Typography
Edit `src/App.css` – all premium styling is centralized and well-commented.

### Add New Pages
1. Create a new file in `src/pages/MyPage.jsx`
2. Add a route in `src/App.jsx`
3. Link from `src/components/Layout.jsx`

### Update Menu Images
Drop new images into `public/collections/` and update collection entries in `siteData.js`.

## Support

- **Local issues?** Run `npm install` again, then `npm run dev`
- **Build fails?** Check Node version (v16+) with `node --version`
- **Contact form not sending?** Verify EmailJS keys are correct, or rely on mailto fallback
- **Need to update?** All content is in `src/data/siteData.js` – edit and redeploy

---

**This is your production-ready Glow Menu front-end.** Ship it with confidence. 🚀
