# Before going live
1. Replace demo images and copy; set real prices, tax and shipping (WooCommerce → Settings).
2. Disable **Maison Demo Gateway**; enable a certified gateway with live keys; force HTTPS.
3. Delete the `admin` test user, use unique admin credentials, enable 2FA (e.g. a security plugin).
4. Set `define('DISALLOW_FILE_EDIT', true);` in `wp-config.php`; keep WordPress, WooCommerce and plugins updated.
5. Add a caching plugin or host-level cache and a CDN; convert uploaded images to WebP (WordPress 6.x supports it).
6. Install an SMTP plugin so order and contact emails are delivered reliably.
7. Review the Privacy policy, Terms and cookie consent with a qualified professional.
8. Submit `/sitemap.xml` (WordPress core) to Google Search Console; set up daily backups.
