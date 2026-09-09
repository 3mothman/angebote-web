<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Assets
{
    public static function register_hooks(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_public']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_admin']);
    }

    public static function enqueue_public(): void
    {
        wp_enqueue_style(
            'angebot-deals',
            ANGEBOT_DEALS_URL . 'public/css/deals.css',
            [],
            ANGEBOT_DEALS_VERSION
        );

        wp_enqueue_script(
            'angebot-deals',
            ANGEBOT_DEALS_URL . 'public/js/deals.js',
            [],
            ANGEBOT_DEALS_VERSION,
            true
        );

        wp_localize_script('angebot-deals', 'AngebotDeals', [
            'ajaxUrl'  => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('angebot_public'),
            'merchantNonce' => is_user_logged_in() ? wp_create_nonce('angebot_merchant') : '',
            'i18n'     => [
                'loading' => __('Loading…', 'angebot-deals'),
                'error'   => __('Something went wrong.', 'angebot-deals'),
            ],
        ]);
    }

    public static function enqueue_admin(string $hook): void
    {
        if (!in_array($hook, ['toplevel_page_angebot-vouchers', 'deal_page_angebot-reviews'], true)
            && strpos($hook, 'angebot') === false
        ) {
            // Still load on merchant portal pages.
        }

        wp_enqueue_style(
            'angebot-deals-admin',
            ANGEBOT_DEALS_URL . 'public/css/admin.css',
            [],
            ANGEBOT_DEALS_VERSION
        );

        wp_enqueue_script(
            'angebot-deals-admin',
            ANGEBOT_DEALS_URL . 'public/js/admin.js',
            [],
            ANGEBOT_DEALS_VERSION,
            true
        );

        wp_localize_script('angebot-deals-admin', 'AngebotDealsAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('angebot_merchant'),
        ]);
    }
}
