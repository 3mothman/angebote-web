<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void
    {
        load_plugin_textdomain('angebot-deals', false, dirname(ANGEBOT_DEALS_BASENAME) . '/languages');

        Angebot_Deals_Deal_CPT::register_hooks();
        Angebot_Deals_Deal_Meta::register_hooks();
        Angebot_Deals_WooCommerce_Sync::register_hooks();
        Angebot_Deals_Voucher::register_hooks();
        Angebot_Deals_Voucher_Email::register_hooks();
        Angebot_Deals_Merchant_Role::register_hooks();
        Angebot_Deals_Merchant_Portal::register_hooks();
        Angebot_Deals_Shortcodes::register_hooks();
        Angebot_Deals_Ajax_Filters::register_hooks();
        Angebot_Deals_Reviews::register_hooks();
        Angebot_Deals_Legal_Pages::register_hooks();
        Angebot_Deals_Assets::register_hooks();
        Angebot_Deals_Location::register_hooks();
        Angebot_Deals_Admin::register_hooks();
        Angebot_Deals_QR_Code::register_hooks();

        add_action('init', [$this, 'maybe_upgrade']);
    }

    public function maybe_upgrade(): void
    {
        $installed = get_option('angebot_deals_version');
        if ($installed === ANGEBOT_DEALS_VERSION) {
            return;
        }

        Angebot_Deals_Activator::create_voucher_table();
        Angebot_Deals_Activator::create_reviews_table();
        update_option('angebot_deals_version', ANGEBOT_DEALS_VERSION);
    }
}
