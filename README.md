# Angebot — Lokale Deals-Plattform

WordPress-Plugin + Theme für eine Groupon-ähnliche Deals-Website mit **eigenem Branding** (Markenname, Logo, Farben). Layout und Funktionsweise orientieren sich an groupon.de — Markenrechte von Groupon werden bewusst nicht übernommen.

## Stack

- WordPress 6.4+
- WooCommerce 8+
- Custom Plugin `angebot-deals` (PHP 8, OOP)
- Custom Theme `angebot`
- Hosting: Hostinger (oder jedes WP-Hosting)

## Installation (Hostinger)

1. WordPress + WooCommerce installieren.
2. Ordner kopieren:
   - `plugin/angebot-deals` → `wp-content/plugins/angebot-deals`
   - `theme/angebot` → `wp-content/themes/angebot`
3. Plugin **Angebot Deals** aktivieren, Theme **Angebot** aktivieren.
4. Unter **Einstellungen → Permalinks** einmal speichern (Rewrite-Rules).
5. WooCommerce-Setup (Währung EUR, Standort DE) durchlaufen.
6. Zahlungen: **WooCommerce Stripe Gateway** und/oder **PayPal Payments** installieren, Live-Keys hinterlegen, Testmodus aus.
7. Optional: **Deals → Einstellungen → Demo-Deals anlegen**.
8. Rechtliche Seiten (`/impressum`, `/agb`, `/widerruf`, `/datenschutz`) öffnen und Platzhalter durch echte Firmendaten ersetzen — idealerweise anwaltlich prüfen lassen.
9. Eigenes Logo unter **Design → Customizer → Logo** hochladen. Markenname unter **Deals → Einstellungen**.

## Was das Plugin kann

| Feature | Status |
|--------|--------|
| CPT `deal` + Kategorien + Standorte | ✅ |
| Meta: Preise, Rabatt, Merchant, Kontingent, Ablauf | ✅ |
| Auto WooCommerce-Produkt (versteckt) | ✅ |
| Checkout über WooCommerce | ✅ |
| Gutschein-Tabelle + E-Mail inkl. QR | ✅ |
| Merchant-Rolle + Einlösen (Admin & Frontend) | ✅ |
| Filter (Kategorie, Stadt, Preis) + Standort-Cookie | ✅ |
| Bewertungen mit Moderation | ✅ |
| Rechtliche Seiten (Vorlagen DE) | ✅ |
| Demo-Deals | ✅ |

### Shortcodes

- `[deals_grid]` / `[angebot_deals]` — Deal-Grid (optional `show_filters="1"`)
- `[angebot_featured]` — Highlight-Deals
- `[angebot_filters]` — Filterleiste
- `[angebot_location_picker]` — Stadtauswahl
- `[angebot_categories]` — Kategorieleiste
- `[angebot_reviews]` — Bewertungen (Deal-Detail)
- `[angebot_merchant_portal]` — Einlösen für Anbieter

### Merchant-Workflow

1. Benutzer mit Rolle **Anbieter / Merchant** anlegen.
2. Beim Deal unter „Merchant-Benutzer“ zuweisen.
3. Anbieter sieht nur eigene Deals und kann unter **Gutscheine** oder Seite mit `[angebot_merchant_portal]` Codes/QR einlösen.
4. QR öffnet `/gutschein/{token}/` — eingeloggte Merchants können dort direkt freigeben.

## Branding (wichtig)

- **Nicht** „Groupon“ als Namen, Logo oder Domain nutzen.
- Farben sind bewusst **Navy + Coral** (nicht Groupon-Grün).
- Layout/UX (Header, Karten, Buy-Box, Footer-Städte) darf sich am Marktstandard orientieren.

## Empfohlene Live-Checkliste Deutschland

- [ ] Impressum, AGB, Widerruf, Datenschutz final
- [ ] Stripe/PayPal Live
- [ ] SSL (Hostinger)
- [ ] E-Mail-Zustellung testen (SMTP-Plugin empfohlen)
- [ ] Ersten Testkauf inkl. Gutschein-Mail und Einlösung
- [ ] Markenname + Logo final
- [ ] Steuer/USt-Einstellungen in WooCommerce

## Projektstruktur

```
angebot-web/
├── plugin/angebot-deals/     # Custom Plugin
└── theme/angebot/            # Frontend Theme
```

## Hinweise zur Weiterentwicklung

- Bewertungen starten im Status `pending` (Admin freigeben).
- QR-Bilder kommen von `api.qrserver.com` (E-Mail/Frontend). Für Offline/DSGVO-strikt: lokale QR-Lib ergänzen.
- n8n: Webhook an `woocommerce_order_status_completed` oder Custom-Action `angebot_deals_vouchers_created` anbinden.

Kein Rechtsberatung — Vorlagen ersetzen keine anwaltliche Prüfung.
