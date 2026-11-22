# QA checklist (about 15 minutes)

Tick each item on staging. Enable `WP_DEBUG` and `WP_DEBUG_LOG` first and confirm `wp-content/debug.log` stays empty.

**Browse**
- [ ] Home: hero rotates, pause button works, arrows and dots work, 4 categories, 2 product rows, 2 promos
- [ ] Shop: 12 products, 4 columns desktop / 3 tablet / 2 phone
- [ ] Categories page and each category archive load
- [ ] Filters: category chips, each price chip, "On sale" (shows 3 products), sort dropdown; clicking an active chip clears it
- [ ] Search "watch" returns watches; search "zzz" shows "no products found"
- [ ] Product page: gallery zoom/lightbox, attributes tab, reviews tab, related products

**Wishlist**
- [ ] Heart on a card toggles, header count updates, Wishlist page lists the item, removing it empties the page
- [ ] Logged in: list persists after logout/login on another browser

**Cart and checkout**
- [ ] Add to cart from card and product page; header count updates without reload
- [ ] Cart: change quantity, remove, apply `WELCOME10` (10% off), shipping shows "Insured courier $15" under $250 and free over $250
- [ ] Checkout as guest with **Approved payment**: order received page shows demo notice; order is Processing with `[DEMO]` note; stock reduced
- [ ] Checkout with **Declined**: error notice, order Failed, cart intact
- [ ] Checkout with **Pending**: order On hold

**Accounts**
- [ ] Register, log in, My Account → Orders shows the order, edit address, edit account, log out

**Content and forms**
- [ ] About, Contact (submit form: success notice and email arrives), Privacy policy
- [ ] Edit a hero headline in Customizer and see it on Home

**Responsive and accessibility**
- [ ] 375 px, 768 px, 1280 px: no horizontal scroll; mobile menu opens and closes (Esc)
- [ ] Keyboard only: skip link, menu, filters, add to cart, checkout reachable with visible focus
- [ ] Lighthouse (mobile): Accessibility ≥ 95, SEO ≥ 95 (Performance depends on host; enable caching)
