<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Location
{
    public const COOKIE = 'angebot_location';

    public static function register_hooks(): void
    {
        add_action('wp_ajax_angebot_set_location', [self::class, 'ajax_set']);
        add_action('wp_ajax_nopriv_angebot_set_location', [self::class, 'ajax_set']);
        add_action('init', [self::class, 'seed_default_locations'], 20);
    }

    public static function current(): string
    {
        if (!empty($_COOKIE[self::COOKIE])) {
            return sanitize_title(wp_unslash($_COOKIE[self::COOKIE]));
        }
        return (string) get_option('angebot_default_location', 'berlin');
    }

    public static function current_label(): string
    {
        $slug = self::current();
        $term = get_term_by('slug', $slug, Angebot_Deals_Deal_CPT::TAX_LOCATION);
        return $term && !is_wp_error($term) ? $term->name : ucfirst($slug);
    }

    public static function ajax_set(): void
    {
        check_ajax_referer('angebot_public', 'nonce');
        $slug = isset($_POST['location']) ? sanitize_title(wp_unslash($_POST['location'])) : '';
        if ($slug === '') {
            wp_send_json_error(['message' => 'empty']);
        }

        setcookie(self::COOKIE, $slug, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
        $_COOKIE[self::COOKIE] = $slug;

        wp_send_json_success(['location' => $slug, 'label' => self::current_label()]);
    }

    public static function seed_default_locations(): void
    {
        if (get_option('angebot_locations_seeded')) {
            return;
        }

        $cities = [
            'Berlin', 'Hamburg', 'München', 'Köln', 'Frankfurt', 'Stuttgart',
            'Düsseldorf', 'Leipzig', 'Dortmund', 'Essen', 'Bremen', 'Dresden',
            'Hannover', 'Nürnberg', 'Halle', 'Merseburg', 'Magdeburg',
        ];

        foreach ($cities as $city) {
            if (!term_exists($city, Angebot_Deals_Deal_CPT::TAX_LOCATION)) {
                wp_insert_term($city, Angebot_Deals_Deal_CPT::TAX_LOCATION);
            }
        }

        $categories = [
            'Essen & Trinken', 'Beauty & Wellness', 'Freizeit', 'Reisen',
            'Aktivitäten', 'Services', 'Shopping', 'Events',
        ];

        foreach ($categories as $cat) {
            if (!term_exists($cat, Angebot_Deals_Deal_CPT::TAX_CATEGORY)) {
                wp_insert_term($cat, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
            }
        }

        update_option('angebot_locations_seeded', 1);
        update_option('angebot_default_location', 'berlin');
    }
}
