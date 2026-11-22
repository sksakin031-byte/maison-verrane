#!/bin/sh
# Installs WordPress + WooCommerce into the local Docker stack and imports the demo.
# Run after: docker compose up -d   (wait ~20 s for the database)
set -e
WP="docker compose run --rm wpcli wp --path=/var/www/html"
$WP core install --url="http://localhost:8080" --title="Maison Verrane" \
  --admin_user="admin" --admin_password="ChangeMe-Local-123!" --admin_email="admin@example.test" --skip-email
$WP plugin install woocommerce --activate
$WP theme activate maison-verrane
$WP plugin activate maison-demo-gateway
$WP eval 'mv_run_demo_import();'
$WP rewrite flush --hard
echo "Done. Open http://localhost:8080  (admin: http://localhost:8080/wp-admin, user: admin)"
echo "Change the admin password immediately if this is anything other than a local test."
