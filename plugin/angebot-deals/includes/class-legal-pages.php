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
            'company-info' => [
                'title'   => 'Company Information',
                'content' => self::impressum_content(),
            ],
            'terms' => [
                'title'   => 'Terms & Conditions',
                'content' => self::agb_content(),
            ],
            'cancellation' => [
                'title'   => 'Cancellation Policy',
                'content' => self::widerruf_content(),
            ],
            'privacy' => [
                'title'   => 'Privacy Policy',
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
<p><strong>Company information</strong></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>[Company Name]<br>[Street Address]<br>[City, Postcode]<br>United Kingdom</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Company number:</strong> [Companies House number]<br><strong>VAT number:</strong> [GB VAT number if registered]</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Contact:</strong><br>Phone: [Phone]<br>Email: [Email]</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Replace these placeholders with your real UK company details before going live.</em></p>
<!-- /wp:paragraph -->
HTML;
    }

    private static function agb_content(): string
    {
        return <<<HTML
<!-- wp:heading -->
<h2>Terms &amp; Conditions</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>1. Scope — These terms apply to all purchases of vouchers/deals through this website for customers in the United Kingdom.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>2. Contract — Completing an order creates a purchase contract for the voucher. The local merchant provides the service upon redemption.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>3. Vouchers — Each voucher is single-use, transferable as stated in the deal terms, and valid only until the stated expiry date.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>4. Prices &amp; payment — All prices are in British pounds (GBP) including VAT where applicable. Payment is processed via the available WooCommerce payment methods.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>5. Liability — We broker the deal; fulfilment of the service is the responsibility of the respective merchant. Details are in the deal description.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Have these terms reviewed by a UK solicitor before going live.</em></p>
<!-- /wp:paragraph -->
HTML;
    }

    private static function widerruf_content(): string
    {
        return <<<HTML
<!-- wp:heading -->
<h2>Cancellation Policy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Your rights may include those under the Consumer Contracts Regulations 2013 and the Consumer Rights Act 2015. For leisure services with a specific date or period of performance, the right to cancel may not apply once the service has begun or as otherwise permitted by law.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Cooling-off period:</strong> Where a 14-day cooling-off period applies, it runs from the day the contract is concluded, unless a statutory exception applies.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><strong>Refunds:</strong> Where you are entitled to cancel, we will refund payments without undue delay, and no later than 14 days after we are informed of the cancellation.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Adapt this text to your business model and have it reviewed by a UK solicitor. This is a template, not legal advice.</em></p>
<!-- /wp:paragraph -->
HTML;
    }

    private static function datenschutz_content(): string
    {
        return <<<HTML
<!-- wp:heading -->
<h2>Privacy Policy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>We process personal data (name, email, order data, payment data via the payment provider) to fulfil contracts and communicate with customers, in line with UK GDPR and the Data Protection Act 2018.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>Legal bases include contract performance and legitimate interests (such as IT security), where applicable.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>You have rights of access, rectification, erasure, restriction, and data portability, and the right to complain to the Information Commissioner’s Office (ICO).</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><em>Add hosting, cookies, analytics, and payment providers (Stripe/PayPal) before going live.</em></p>
<!-- /wp:paragraph -->
HTML;
    }
}
