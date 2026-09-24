# Menu builder and pricing research

Reviewed September 23, 2026.

## Local evidence

- `website/WordPress/wp-content/themes/glowmenu/assets/scripts/init.js`, `pricing()`:
  quantity times (size + finish + glow). The old default size amount is 2.38;
  the other rates come from database-generated option attributes. This is not
  a current rate card and the old size-change handler does not update size cost.
- The same file's `designer()` wires Fancy Product Designer upload, text, layers,
  download, print, reset, save, and load controls into a side panel.
- `assets/styles/modules/_body.scss` defines five proportions, four glow colors,
  Semi Gloss and Laminated effects.
- `menus/base` supplies exact artwork filenames: 4x6, 5x5, 5x7, 4.25x11, 4.25x14.
- Plugins: Fancy Product Designer and WooCommerce Extra Product Options. Their
  product definitions and option prices are database content, not recovered here.

## Current choices

Per the user's request: Stock (interpreted as uncoated), Gloss, Laminated;
10/14/16 pt thickness; primary Red/Blue/Yellow plus original Green/Orange glow.
These are configurable concept choices, not a confirmed manufacturing catalog.
The builder uses original React/SVG code, not copied proprietary plugin code.
Uploaded images are fitted backgrounds; the text layers can be moved and edited.
SVG downloads are visual concepts, not CMYK/bleed-ready production files.
Drafts stay in localStorage on the current device. Inquiry forms carry text and
specifications, not uploaded image files. Files must be shared separately.

## Public pricing benchmarks

- [4OVER4 laminated menus](https://www.4over4.com/product/laminated-menus): advertised
  starting price $57.11 per set of 50, about $1.14 each. Starting configuration,
  not a quote for a selected Glow Menu size or stock.
- [Ace Printing menus](https://aceprintingservice.com/menus/): flat glossy menus
  $165/500 or $215/1,000. Lamination is separately quoted.
- [PrintPlace menus](https://www.printplace.com/products/menus): paper/cardstock
  choices include 14 pt and 16 pt. Configuration-specific pricing is dynamic.
- [PrintMagic laminated menus](https://www.printmagic.com/menu-printing/laminated-menus/):
  describes 80 lb cover with 3 mil gloss lamination. Paper weight and laminate
  thickness are different measurements and should not be treated as card points.

No matching public rate card was verified for colored glow printing in all five
sizes. Do not scale these unrelated benchmark totals into a fictional retail
price. Confirm size, stock, finish, glow production, quantity breaks, setup,
shipping, and tax before enabling binding prices.

## Open-source references reviewed

- [Fabric.js](https://github.com/fabricjs/fabric.js): MIT canvas/SVG editor foundation.
- [Fabric Canvas Editor](https://github.com/onerkiz/fabric-canvas-editor): React editor
  reference. No code or dependencies imported into this implementation.
- Polotno's SDK requires a license; it was not selected as an open-source dependency.
