# Original design references

## Current direction

The storefront now uses a dark charcoal theme with lime accents, soft glows,
and a logo-only header. `src/DarkTheme.css` defines the shared theme;
`src/components/GlowDetails.jsx` contains the mood selector, process, FAQ,
and closing project invitation.

The selected direction is a clean storefront: compact contained hero, complete
menu previews, a four-collection grid, and a shorter custom-design section.
The original theme remains a source for fonts and branding rather than a layout
to reproduce. Homepage styles live in `src/pages/HomePage.css` and shared header
styles in `src/components/Layout.css`. Slideshow rotation starts paused.

The homepage screenshots are in `website/blog/glowmenu-beta-phase.png` and
`glowmenu-beta-closed.jpg`. The latter is the same design with a beta notice.

The original theme is `website/WordPress/wp-content/themes/glowmenu`.
Paths below are relative to that theme:

- `assets/styles/modules/_body.scss`, `.home #theme-wrapper .slider`: Questrial
  heading, 75px / 1.077, #414141; Arimo excerpt, 22px / 1.5, #656565; 15% left
  inset; 40px by 5px heading rule; Montserrat rounded CTA.
- `assets/styles/modules/_header.scss`: Arimo 13px bold uppercase navigation,
  40px gaps, 1px tracking; logo at 50px by 60px.
- `_body.scss`, `.design`: Arimo bold 37px headings and overlapping menu imagery.
- `inc/logo.php`: original vector logo, reused with the theme's dark fill.
- `header.php`: confirms Google Fonts families Arimo, Questrial, Lato, Montserrat.
  Arimo and Questrial are now hosted locally in `public/fonts`.
- `index.php`: renders `the_content()`. Full homepage markup is database content
  and is not present in this template. No SQL dump was found in this WordPress folder.

The React site adapts the original desktop composition for smaller screens and
keeps its current inquiry flow. Original cart/account controls depend on
WooCommerce and are not reproduced as decorative, nonfunctional buttons.
