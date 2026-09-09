<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Merchant_Portal
{
    public static function register_hooks(): void
    {
        add_action('admin_menu', [self::class, 'admin_menu']);
        add_action('wp_ajax_angebot_redeem_voucher', [self::class, 'ajax_redeem']);
        add_action('wp_ajax_angebot_lookup_voucher', [self::class, 'ajax_lookup']);
        add_shortcode('angebot_merchant_portal', [self::class, 'shortcode']);
    }

    public static function admin_menu(): void
    {
        add_menu_page(
            __('Redeem vouchers', 'angebot-deals'),
            __('Vouchers', 'angebot-deals'),
            'angebot_redeem_voucher',
            'angebot-vouchers',
            [self::class, 'render_admin_page'],
            'dashicons-tickets-alt',
            6
        );
    }

    public static function render_admin_page(): void
    {
        if (!current_user_can('angebot_redeem_voucher')) {
            wp_die(esc_html__('Permission denied.', 'angebot-deals'));
        }

        $user_id  = get_current_user_id();
        $is_admin = current_user_can('manage_options');
        $search   = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $status   = isset($_GET['status']) ? sanitize_text_field(wp_unslash($_GET['status'])) : '';

        if ($is_admin) {
            global $wpdb;
            $table = Angebot_Deals_Voucher::table();
            $sql   = "SELECT * FROM {$table} WHERE 1=1";
            $params = [];
            if ($status) {
                $sql .= ' AND status = %s';
                $params[] = $status;
            }
            if ($search) {
                $sql .= ' AND (code LIKE %s OR customer_email LIKE %s)';
                $like = '%' . $wpdb->esc_like($search) . '%';
                $params[] = $like;
                $params[] = $like;
            }
            $sql .= ' ORDER BY created_at DESC LIMIT 100';
            $vouchers = $params ? $wpdb->get_results($wpdb->prepare($sql, $params)) : $wpdb->get_results($sql);
        } else {
            $vouchers = Angebot_Deals_Voucher::get_for_merchant($user_id, [
                'search' => $search,
                'status' => $status,
                'limit'  => 100,
            ]);
        }

        include ANGEBOT_DEALS_PATH . 'admin/views/voucher-redeem.php';
    }

    public static function shortcode(): string
    {
        if (!is_user_logged_in() || !current_user_can('angebot_redeem_voucher')) {
            return '<p>' . esc_html__('Please log in as a merchant to redeem vouchers.', 'angebot-deals') . '</p>';
        }

        ob_start();
        include ANGEBOT_DEALS_PATH . 'templates/merchant-portal.php';
        return (string) ob_get_clean();
    }

    public static function ajax_lookup(): void
    {
        check_ajax_referer('angebot_merchant', 'nonce');

        if (!current_user_can('angebot_redeem_voucher')) {
            wp_send_json_error(['message' => __('Permission denied.', 'angebot-deals')], 403);
        }

        $code  = isset($_POST['code']) ? sanitize_text_field(wp_unslash($_POST['code'])) : '';
        $token = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';

        $voucher = $token
            ? Angebot_Deals_Voucher::get_by_token($token)
            : Angebot_Deals_Voucher::get_by_code($code);

        if (!$voucher) {
            wp_send_json_error(['message' => __('Voucher not found.', 'angebot-deals')]);
        }

        $voucher = Angebot_Deals_Voucher::refresh_status($voucher);

        if (!current_user_can('manage_options') && (int) $voucher->merchant_user_id !== get_current_user_id()) {
            wp_send_json_error(['message' => __('This voucher does not belong to your deals.', 'angebot-deals')]);
        }

        wp_send_json_success([
            'id'            => (int) $voucher->id,
            'code'          => $voucher->code,
            'status'        => $voucher->status,
            'status_label'  => Angebot_Deals_Voucher::status_label($voucher->status),
            'deal_title'    => get_the_title((int) $voucher->deal_id),
            'customer'      => $voucher->customer_name ?: $voucher->customer_email,
            'expires_at'    => $voucher->expires_at ? date_i18n('d.m.Y', strtotime($voucher->expires_at)) : '',
            'can_redeem'    => $voucher->status === Angebot_Deals_Voucher::STATUS_ACTIVE,
        ]);
    }

    public static function ajax_redeem(): void
    {
        check_ajax_referer('angebot_merchant', 'nonce');

        if (!current_user_can('angebot_redeem_voucher')) {
            wp_send_json_error(['message' => __('Permission denied.', 'angebot-deals')], 403);
        }

        $id = absint($_POST['voucher_id'] ?? 0);
        $result = Angebot_Deals_Voucher::redeem($id, get_current_user_id());

        if (!$result['success']) {
            wp_send_json_error(['message' => $result['message']]);
        }

        wp_send_json_success(['message' => $result['message']]);
    }
}
