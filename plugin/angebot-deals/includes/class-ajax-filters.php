<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Ajax_Filters
{
    public static function register_hooks(): void
    {
        add_action('wp_ajax_angebot_filter_deals', [self::class, 'filter']);
        add_action('wp_ajax_nopriv_angebot_filter_deals', [self::class, 'filter']);
    }

    public static function filter(): void
    {
        check_ajax_referer('angebot_public', 'nonce');

        $atts = [
            'category' => isset($_POST['category']) ? sanitize_title(wp_unslash($_POST['category'])) : '',
            'location' => isset($_POST['location']) ? sanitize_title(wp_unslash($_POST['location'])) : '',
            'limit'    => absint($_POST['limit'] ?? 24),
            'orderby'  => sanitize_key($_POST['orderby'] ?? 'date'),
            'columns'  => sanitize_text_field(wp_unslash($_POST['columns'] ?? '3')),
        ];

        if (!empty($_POST['min_price'])) {
            $_GET['min_price'] = (float) $_POST['min_price'];
        }
        if (!empty($_POST['max_price'])) {
            $_GET['max_price'] = (float) $_POST['max_price'];
        }
        if (!empty($_POST['order'])) {
            $_GET['order'] = sanitize_text_field(wp_unslash($_POST['order']));
        }

        $html = Angebot_Deals_Shortcodes::deals_grid(array_merge($atts, ['show_filters' => '0']));
        wp_send_json_success(['html' => $html]);
    }
}
