# Angebot — Local Deals Platform

WordPress plugin + theme for a Groupon-style deals site with **its own branding** (brand name, logo, colors). Layout and UX follow common local-deals patterns (e.g. groupon.de-style flows) without copying Groupon’s trademarks.

## Stack

- WordPress 6.4+
- WooCommerce 8+
- Custom plugin `angebot-deals` (PHP 8, OOP)
- Custom theme `angebot`
- Hosting: Hostinger (or any WordPress host)

## Installation (Hostinger)

1. Install WordPress + WooCommerce.
2. Copy folders:
   - `plugin/angebot-deals` → `wp-content/plugins/angebot-deals`
   - `theme/angebot` → `wp-content/themes/angebot`
3. Activate the **Angebot Deals** plugin and the **Angebot** theme.
4. Open **Settings → Permalinks** and save once (flush rewrite rules).
5. Complete WooCommerce setup (**currency GBP £**, store address in the UK).
6. Set **Settings → General → Site Language** to **English (UK)** — required so WordPress menus/admin are fully English (not German).
7. Payments: install **WooCommerce Stripe Gateway** and/or **PayPal Payments**, add live keys, turn off test mode when going live.
8. Optional: **Deals → Settings → Create demo deals** (London, Manchester, Birmingham, Edinburgh, Bath).
9. Open legal pages (`/company-info`, `/terms`, `/cancellation`, `/privacy`) and replace placeholders with real UK company details — ideally have a solicitor review them.
10. Upload your logo under **Appearance → Customizer → Logo**. Set the brand name under **Deals → Settings**.

On first load the plugin migrates old German cities/categories to English UK cities, creates an English **Main Menu** (Home, All Deals, categories), and prefers `en_GB` locale.

Default city is **London**. Seeded UK cities include London, Manchester, Birmingham, Leeds, Glasgow, Liverpool, Bristol, Sheffield, Edinburgh, Cardiff, Belfast, Newcastle, Nottingham, Southampton, Leicester, Brighton, Oxford, Cambridge, York, and Bath.

## Plugin features

| Feature | Status |
|--------|--------|
| CPT `deal` + categories + locations | ✅ |
| Meta: prices, discount, merchant, stock, expiry | ✅ |
| Auto WooCommerce product (hidden) | ✅ |
| Checkout via WooCommerce | ✅ |
| Voucher table + email with QR | ✅ |
| Merchant role + redeem (admin & frontend) | ✅ |
| Filters (category, city, price) + location cookie | ✅ |
| Reviews with moderation | ✅ |
| Legal pages (English templates) | ✅ |
| Demo deals | ✅ |

### Shortcodes

- `[deals_grid]` / `[angebot_deals]` — deal grid (optional `show_filters="1"`)
- `[angebot_featured]` — featured deals
- `[angebot_filters]` — filter bar
- `[angebot_location_picker]` — city picker
- `[angebot_categories]` — category bar
- `[angebot_reviews]` — reviews (deal detail)
- `[angebot_merchant_portal]` — redeem UI for merchants

### Merchant workflow

1. Create a user with the **Merchant** role.
2. Assign them on the deal under “Merchant user”.
3. Merchants only see their own deals and can redeem codes/QR under **Vouchers** or a page with `[angebot_merchant_portal]`.
4. QR opens `/voucher/{token}/` — logged-in merchants can approve redemption there.

## Branding (important)

- Do **not** use “Groupon” as a name, logo, or domain.
- Colors are intentionally **navy + coral** (not Groupon green).
- Layout/UX (header, cards, buy box, footer cities) may follow marketplace conventions.

## Recommended live checklist

- [ ] Imprint, terms, cancellation, and privacy finalized
- [ ] Stripe/PayPal live
- [ ] SSL (Hostinger)
- [ ] Test email delivery (SMTP plugin recommended)
- [ ] First test purchase including voucher email and redemption
- [ ] Brand name + logo finalized
- [ ] Tax/VAT settings in WooCommerce

## Project structure

```
angebot-web/
├── plugin/angebot-deals/     # Custom plugin
└── theme/angebot/            # Frontend theme
```

## Notes for further development

- Reviews start as `pending` (admin must approve).
- QR images come from `api.qrserver.com` (email/frontend). For offline / stricter privacy: add a local QR library.
- n8n: hook a webhook to `woocommerce_order_status_completed` or the custom action `angebot_deals_vouchers_created`.

Not legal advice — templates do not replace a lawyer’s review.
