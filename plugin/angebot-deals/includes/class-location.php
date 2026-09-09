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
        return (string) get_option('angebot_default_location', 'london');
    }

    public static function current_label(): string
    {
        $slug = self::current();
        $term = get_term_by('slug', $slug, Angebot_Deals_Deal_CPT::TAX_LOCATION);
        return $term && !is_wp_error($term) ? $term->name : ucwords(str_replace('-', ' ', $slug));
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
        // Migration is owned by Angebot_Deals_Setup (uk-en-v2). Keep a light seed for fresh installs.
        if (get_option('angebot_locations_seeded')) {
            return;
        }
        self::seed_uk_cities_and_categories(false);
        update_option('angebot_locations_seeded', Angebot_Deals_Setup::SEED_VERSION);
        update_option('angebot_default_location', 'london');
    }

    public static function seed_uk_cities_and_categories(bool $force_categories = false): void
    {
        $cities = [
            'London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow', 'Liverpool',
            'Bristol', 'Sheffield', 'Edinburgh', 'Cardiff', 'Belfast', 'Newcastle',
            'Nottingham', 'Southampton', 'Leicester', 'Brighton', 'Oxford', 'Cambridge',
            'York', 'Bath',
        ];

        foreach ($cities as $city) {
            if (!term_exists($city, Angebot_Deals_Deal_CPT::TAX_LOCATION)) {
                wp_insert_term($city, Angebot_Deals_Deal_CPT::TAX_LOCATION);
            }
        }

        $categories = [
            'Food & Drink', 'Beauty & Wellness', 'Leisure', 'Travel',
            'Activities', 'Services', 'Shopping', 'Events',
        ];

        foreach ($categories as $cat) {
            if ($force_categories || !term_exists($cat, Angebot_Deals_Deal_CPT::TAX_CATEGORY)) {
                if (!term_exists($cat, Angebot_Deals_Deal_CPT::TAX_CATEGORY)) {
                    wp_insert_term($cat, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
                }
            }
        }
    }
}
