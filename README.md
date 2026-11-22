# Maison Verrane — luxury WooCommerce store (demo)

A real WordPress + WooCommerce site: custom theme, demo payment plugin, demo content importer.
All brand names, products, prices and images are **original and fictional**. Payments are **demo / test mode only**.

```
maison-verrane-package/
├─ wp-content/
│  ├─ themes/maison-verrane/        Custom theme (PHP, CSS, JS, demo images)
│  └─ plugins/maison-demo-gateway/  Demo payment gateway (test mode, no card data)
├─ docker-compose.yml               Optional local test stack
├─ scripts/setup.sh                 One-command local install (Docker + WP-CLI)
└─ docs/                            Install, QA checklist, production checklist
```

## Requirements
WordPress 6.4+, PHP 7.4+ (8.1+ recommended), MySQL 5.7+/MariaDB 10.4+, HTTPS in production, WooCommerce 8+.

## Option A: local test in 3 commands (Docker)
```
docker compose up -d
sh scripts/setup.sh
```
Open http://localhost:8080 (admin: `/wp-admin`, user `admin`, password printed in `scripts/setup.sh`; local only).

## Option B: install on hosting (cPanel / managed WordPress)
1. **Database.** Create a MySQL database and user (cPanel → MySQL Databases), or:
   ```sql
   CREATE DATABASE maison CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
   CREATE USER 'maison'@'localhost' IDENTIFIED BY 'USE-A-LONG-RANDOM-PASSWORD';
   GRANT ALL PRIVILEGES ON maison.* TO 'maison'@'localhost';
   FLUSH PRIVILEGES;
   ```
2. **WordPress.** Install WordPress (download from wordpress.org, run the 5-minute installer, enter the DB details above). No SQL dump is shipped: WordPress and WooCommerce create their own tables, and the importer in step 5 adds the demo content.
3. **Upload.** Copy `wp-content/themes/maison-verrane` and `wp-content/plugins/maison-demo-gateway` to the same paths on the server (or upload each folder zipped via Appearance → Themes / Plugins → Add New).
4. **Activate.** Plugins → install and activate **WooCommerce** (complete or skip its setup wizard), activate **Maison Demo Gateway**, then Appearance → Themes → activate **Maison Verrane**.
5. **Import demo content.** Appearance → **Maison demo setup** → Import demo content.
   This creates 4 categories, 12 products, pages (Home, About us, Contact us, Categories, Wishlist, Privacy policy), menus, a shipping zone, the `WELCOME10` coupon, enables reviews and permalinks `/%postname%/`, and enables the demo gateway.
6. Open the site. Done.

## Editing content (all from the dashboard)
| What | Where |
|---|---|
| Products, prices, images, stock, sale prices | Products → All Products |
| Categories and category images | Products → Categories |
| Hero slides (3), promo banners (2), announcement bar, contact details, footer text | Appearance → Customize |
| Logo | Appearance → Customize → Site Identity |
| Menus | Appearance → Menus |
| About, Contact, Privacy and other pages | Pages |
| Coupons | Marketing → Coupons |
| Shipping, tax, payments | WooCommerce → Settings |
| Orders, customers, reviews | WooCommerce → Orders / Customers; Products → Reviews |

## Payments
* **Maison Demo Gateway** (included) shows a "TEST MODE" box at checkout and lets you simulate approved, declined or pending outcomes. It collects **no card details**, contacts **no bank** and moves **no money**. Orders created with it carry `[DEMO]` notes, and the thank-you page and emails are labelled as demo.
* **For a realistic hosted-field test**, install the official *WooCommerce Stripe Payment Gateway* (or PayPal Payments), enable its **test mode**, and use the provider's published test cards. Card data is entered into the provider's hosted fields and never touches this site.
* **Before going live:** disable the demo gateway (WooCommerce → Settings → Payments), enable a certified gateway with live keys, and serve the site over HTTPS. Never paste payment credentials into theme files; keep API keys in the gateway plugin's settings or in `wp-config.php` constants.

## Features implemented
Wishlist (guests via localStorage, logged-in users in user meta, merged on login) · category / price / on-sale filters + sorting · product search (products only) · cart and fragments-powered live count · coupon support · customer accounts and orders · reviews and ratings · responsive gallery with zoom, lightbox and slider · SEO meta, Open Graph, Organization JSON-LD (WooCommerce adds Product schema; steps aside for Yoast / Rank Math) · security headers, honeypot + nonce contact form, generic login errors, XML-RPC off.

## Design notes
Deep bottle green, mist grey and brass; Bodoni Moda (display) with Manrope (text). Sentence-case UI, no decorative caps. The one animated moment is the headline reveal on the home hero; slider motion respects `prefers-reduced-motion` and can be paused.

## Honest limitations
* This package was **written and statically checked, but not executed inside a live WordPress install** in the environment where it was prepared (no PHP/network there). Run `docs/QA-CHECKLIST.md` once on your staging site before showing the client; it takes about 15 minutes.
* Product photos are original **illustrations** generated for the demo. Replace them with real photography (4:5 ratio, min 1000×1250 px) in Products → each product → Product image.
* Google Fonts are loaded from Google's CDN. For GDPR-strict sites, self-host the two families and edit `inc/setup.php`.
* The privacy policy is a template, not legal advice. Have it reviewed before launch.
* The cart and checkout pages use the classic WooCommerce shortcodes (set by the importer) so the demo gateway and theme styling work reliably.

## License
Theme and plugin: GPL-2.0-or-later. Demo images: created for this project, CC0.

## GitHub Pages preview

The repository root also contains `index.html`, a static brand preview served by GitHub Pages. GitHub Pages does not run PHP, WordPress, or WooCommerce; consequently, browsing products from a live catalog, accounts, cart, checkout, payments, and WordPress forms are not active on this preview. The complete original WordPress theme, demo plugin, assets, scripts, and documentation are preserved under `wp-content/`, `scripts/`, and `docs/` for installation on PHP-enabled WordPress hosting. See `docs/PRODUCTION-CHECKLIST.md` before any production deployment.
