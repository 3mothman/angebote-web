<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * One-time English/UK content migration: cities, categories, menus, locale.
 */
final class Angebot_Deals_Setup
{
    public const SEED_VERSION = 'uk-en-v3';

    public static function register_hooks(): void
    {
        add_action('init', [self::class, 'maybe_migrate'], 25);
        add_action('after_switch_theme', [self::class, 'ensure_english_menus']);
        add_action('admin_init', [self::class, 'ensure_english_menus']);
        add_action('admin_notices', [self::class, 'locale_notice']);
    }

    public static function locale_notice(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $lang = get_locale();
        if (strpos($lang, 'en_') === 0) {
            return;
        }
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('Angebot Deals is built for English (UK). Set Settings → General → Site Language to “English (UK)” so menus and WordPress admin stay fully English.', 'angebot-deals');
        echo ' <a href="' . esc_url(admin_url('options-general.php')) . '">' . esc_html__('Open settings', 'angebot-deals') . '</a>';
        echo '</p></div>';
    }

    public static function maybe_migrate(): void
    {
        if (get_option('angebot_locations_seeded') === self::SEED_VERSION) {
            return;
        }

        if (!taxonomy_exists(Angebot_Deals_Deal_CPT::TAX_LOCATION)) {
            return;
        }

        self::remove_german_locations();
        self::migrate_categories_to_english();
        Angebot_Deals_Location::seed_uk_cities_and_categories(true);
        delete_option('angebot_english_menus_v2');
        delete_option('angebot_english_menus_v3');
        self::ensure_english_menus();
        self::prefer_english_locale();

        update_option('angebot_locations_seeded', self::SEED_VERSION);
        update_option('angebot_default_location', 'london');
    }

    public static function remove_german_locations(): void
    {
        $german = [
            'Berlin', 'Hamburg', 'München', 'Munchen', 'Köln', 'Koln', 'Frankfurt',
            'Stuttgart', 'Düsseldorf', 'Dusseldorf', 'Leipzig', 'Dortmund', 'Essen',
            'Bremen', 'Dresden', 'Hannover', 'Nürnberg', 'Nurnberg', 'Halle',
            'Merseburg', 'Magdeburg',
        ];

        foreach ($german as $name) {
            $term = get_term_by('name', $name, Angebot_Deals_Deal_CPT::TAX_LOCATION);
            if (!$term) {
                $term = get_term_by('slug', sanitize_title($name), Angebot_Deals_Deal_CPT::TAX_LOCATION);
            }
            if ($term && !is_wp_error($term)) {
                wp_delete_term((int) $term->term_id, Angebot_Deals_Deal_CPT::TAX_LOCATION);
            }
        }
    }

    public static function migrate_categories_to_english(): void
    {
        $map = [
            'Essen & Trinken' => 'Food & Drink',
            'Essen und Trinken' => 'Food & Drink',
            'Beauty & Wellness' => 'Beauty & Wellness',
            'Freizeit' => 'Leisure',
            'Reisen' => 'Travel',
            'Aktivitäten' => 'Activities',
            'Aktivitaten' => 'Activities',
            'Services' => 'Services',
            'Shopping' => 'Shopping',
            'Events' => 'Events',
        ];

        foreach ($map as $german => $english) {
            $old = get_term_by('name', $german, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
            if (!$old) {
                continue;
            }

            $new = get_term_by('name', $english, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
            if (!$new) {
                $created = wp_insert_term($english, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
                if (!is_wp_error($created)) {
                    $new = get_term((int) $created['term_id'], Angebot_Deals_Deal_CPT::TAX_CATEGORY);
                }
            }

            if ($new && !is_wp_error($new)) {
                $object_ids = get_objects_in_term((int) $old->term_id, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
                if (!is_wp_error($object_ids) && $object_ids) {
                    foreach ($object_ids as $object_id) {
                        wp_set_object_terms((int) $object_id, [(int) $new->term_id], Angebot_Deals_Deal_CPT::TAX_CATEGORY, true);
                    }
                }
                if ((int) $old->term_id !== (int) $new->term_id) {
                    wp_delete_term((int) $old->term_id, Angebot_Deals_Deal_CPT::TAX_CATEGORY);
                }
            }
        }
    }

    public static function prefer_english_locale(): void
    {
        // Site language: British English (does not override user admin locale if set per-user).
        if (get_option('WPLANG') === '' || get_option('WPLANG') === 'de_DE' || get_option('WPLANG') === 'de_DE_formal') {
            update_option('WPLANG', 'en_GB');
        }
    }

    public static function ensure_english_menus(): void
    {
        if (get_option('angebot_english_menus_v3')) {
            $locations = get_theme_mod('nav_menu_locations');
            if (is_array($locations) && !empty($locations['primary'])) {
                return;
            }
        }

        if (!function_exists('wp_create_nav_menu')) {
            require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
        }

        $primary_id = self::get_or_create_menu('Main Menu');
        $footer_id  = self::get_or_create_menu('Footer Menu');

        // Primary: Home + All Deals only — categories are shown once in the category bar.
        self::clear_menu_items($primary_id);
        self::add_menu_link($primary_id, 'Home', home_url('/'));
        self::add_menu_link($primary_id, 'All Deals', get_post_type_archive_link(Angebot_Deals_Deal_CPT::POST_TYPE) ?: home_url('/deals/'));

        self::clear_menu_items($footer_id);
        self::add_menu_link($footer_id, 'All Deals', get_post_type_archive_link(Angebot_Deals_Deal_CPT::POST_TYPE) ?: home_url('/deals/'));
        self::add_menu_link($footer_id, 'How it works', home_url('/#how-it-works'));
        foreach (['company-info' => 'Company Information', 'terms' => 'Terms & Conditions', 'cancellation' => 'Cancellation Policy', 'privacy' => 'Privacy Policy'] as $slug => $label) {
            $page = get_page_by_path($slug);
            if ($page) {
                self::add_menu_link($footer_id, $label, get_permalink($page));
            }
        }

        $locations = (array) get_theme_mod('nav_menu_locations');
        $locations['primary'] = $primary_id;
        $locations['footer']  = $footer_id;
        set_theme_mod('nav_menu_locations', $locations);

        update_option('angebot_english_menus_v3', 1);
    }

    private static function get_or_create_menu(string $name): int
    {
        $existing = wp_get_nav_menu_object($name);
        if ($existing) {
            return (int) $existing->term_id;
        }
        $id = wp_create_nav_menu($name);
        return is_wp_error($id) ? 0 : (int) $id;
    }

    private static function clear_menu_items(int $menu_id): void
    {
        if (!$menu_id) {
            return;
        }
        $items = wp_get_nav_menu_items($menu_id);
        if (!$items) {
            return;
        }
        foreach ($items as $item) {
            wp_delete_post((int) $item->ID, true);
        }
    }

    private static function add_menu_link(int $menu_id, string $title, string $url): void
    {
        if (!$menu_id || $url === '') {
            return;
        }
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'  => $title,
            'menu-item-url'    => $url,
            'menu-item-status' => 'publish',
            'menu-item-type'   => 'custom',
        ]);
    }

    private static function add_menu_term(int $menu_id, WP_Term $term): void
    {
        if (!$menu_id) {
            return;
        }
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'     => $term->name,
            'menu-item-object'    => $term->taxonomy,
            'menu-item-object-id' => $term->term_id,
            'menu-item-type'      => 'taxonomy',
            'menu-item-status'    => 'publish',
        ]);
    }
}
