<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Legal_Pages
{
    public static function register_hooks(): void
    {
        add_action('admin_init', [self::class, 'maybe_create_pages']);
    }

    public static function maybe_create_pages(): void
    {
        if (get_option('angebot_legal_pages_created')) {
            return;
        }

        if (!current_user_can('manage_options') && !defined('WP_CLI')) {
            // On activation, current_user_can may work; seed via activator flag check.
        }

        $pages = [
            'impressum' => [
                'title'   => 'Impressum',
                'content' => self::impressum_content(),
            ],
            'agb' => [
                'title'   => 'AGB',
                'content' => self::agb_content(),
            ],
            'widerruf' => [
                'title'   => 'Widerrufsbelehrung',
                'content' => self::widerruf_content(),
            ],
            'datenschutz' => [
                'title'   => 'Datenschutz',
                'content' => self::datenschutz_content(),
            ],
        ];

        $ids = [];
        foreach ($pages as $key => $page) {
            $existing = get_page_by_path($key);
            if ($existing) {
                $ids[$key] = $existing->ID;
                continue;
            }

            $ids[$key] = wp_insert_post([
                'post_title'   => $page['title'],
                'post_name'    => $key,
                'post_content' => $page['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ]);
        }

        update_option('angebot_legal_page_ids', $ids);
        update_option('angebot_legal_pages_created', 1);
    }

    private static function impressum_content(): string
    {
        return <<<HTML
<!-- wp:paragraph -->
<p><strong>Angaben gemäß § 5 TMG</strong></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>[Firmenname]<br>[Straße Hausnummer]<br>[PLZ Ort]</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Vertreten durch:</strong><br>[Vorname Nachname]</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Kontakt:</strong><br>Telefon: [Telefon]<br>E-Mail: [E-Mail]</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Umsatzsteuer-ID:</strong><br>Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz: [USt-IdNr.]</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Bitte ersetze die Platzhalter durch deine echten Firmendaten, bevor du live gehst.</em></p>
<!-- /wp:paragraph -->
HTML;
    }

    private static function agb_content(): string
    {
        return <<<HTML
<!-- wp:heading -->
<h2>Allgemeine Geschäftsbedingungen</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>§ 1 Geltungsbereich — Diese AGB gelten für alle Bestellungen von Gutscheinen/Deals über diese Website.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>§ 2 Vertragsschluss — Mit Abschluss der Bestellung kommt ein Kaufvertrag über den Gutschein zustande. Die Leistung des lokalen Anbieters erfolgt nach Einlösung.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>§ 3 Gutscheine — Jeder Gutschein ist einmalig, personenbezogen übertragbar nach Maßgabe der Deal-Bedingungen und nur bis zum angegebenen Ablaufdatum gültig.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>§ 4 Preise & Zahlung — Alle Preise verstehen sich in Euro inkl. gesetzlicher MwSt., sofern ausgewiesen. Zahlung erfolgt über die angebotenen WooCommerce-Zahlungsarten.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>§ 5 Haftung — Wir vermitteln den Deal; die Durchführung der Leistung obliegt dem jeweiligen Anbieter. Details stehen in der Deal-Beschreibung.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Lasse diese AGB vor dem Live-Gang von einem Anwalt prüfen.</em></p>
<!-- /wp:paragraph -->
HTML;
    }

    private static function widerruf_content(): string
    {
        return <<<HTML
<!-- wp:heading -->
<h2>Widerrufsbelehrung</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Verbraucher haben ein gesetzliches Widerrufsrecht. Bei Gutscheinen für Freizeitleistungen mit festem Termin kann das Widerrufsrecht unter Umständen ausgeschlossen sein (§ 312g Abs. 2 BGB).</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Widerrufsfrist:</strong> 14 Tage ab Vertragsschluss, sofern kein gesetzlicher Ausschluss greift.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Folgen des Widerrufs:</strong> Wir erstatten alle Zahlungen unverzüglich, spätestens binnen 14 Tagen.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Passe den Text an dein Geschäftsmodell an und lass ihn rechtlich prüfen. Dies ist eine Vorlage, keine Rechtsberatung.</em></p>
<!-- /wp:paragraph -->
HTML;
    }

    private static function datenschutz_content(): string
    {
        return <<<HTML
<!-- wp:heading -->
<h2>Datenschutzerklärung</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Wir verarbeiten personenbezogene Daten (Name, E-Mail, Bestelldaten, Zahlungsdaten über den Zahlungsdienstleister) zur Vertragserfüllung und Kundenkommunikation.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>Rechtsgrundlage: Art. 6 Abs. 1 lit. b DSGVO (Vertrag) sowie lit. f (berechtigtes Interesse an IT-Sicherheit).</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>Du hast Rechte auf Auskunft, Berichtigung, Löschung, Einschränkung und Datenübertragbarkeit sowie Beschwerde bei einer Aufsichtsbehörde.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Ergänze Hosting, Cookies, Analytics und Zahlungsanbieter (Stripe/PayPal) vor dem Live-Gang.</em></p>
<!-- /wp:paragraph -->
HTML;
    }
}
