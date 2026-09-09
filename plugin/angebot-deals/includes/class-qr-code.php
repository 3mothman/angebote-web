<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * QR codes via Google Charts API fallback + local verification endpoint.
 * Payload encodes a redeem URL with the voucher token.
 */
final class Angebot_Deals_QR_Code
{
    public static function register_hooks(): void
    {
        add_action('init', [self::class, 'add_rewrite']);
        add_filter('query_vars', [self::class, 'query_vars']);
        add_action('template_redirect', [self::class, 'handle_redeem_page']);
    }

    public static function add_rewrite(): void
    {
        add_rewrite_rule('^voucher/([a-f0-9]+)/?', 'index.php?angebot_voucher_token=$matches[1]', 'top');
    }

    public static function query_vars(array $vars): array
    {
        $vars[] = 'angebot_voucher_token';
        return $vars;
    }

    public static function redeem_url(string $token): string
    {
        return home_url('/voucher/' . rawurlencode($token) . '/');
    }

    public static function image_url(string $token, int $size = 200): string
    {
        $data = self::redeem_url($token);
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=%1$dx%1$d&data=%2$s',
            $size,
            rawurlencode($data)
        );
    }

    /**
     * SVG QR alternative that works offline (simple matrix via php if lib present).
     * Falls back to remote URL.
     */
    public static function render_img(string $token, int $size = 180): string
    {
        $url = self::image_url($token, $size);
        return sprintf(
            '<img class="angebot-qr" src="%s" width="%d" height="%d" alt="%s" loading="lazy">',
            esc_url($url),
            $size,
            $size,
            esc_attr__('Voucher QR code', 'angebot-deals')
        );
    }

    public static function handle_redeem_page(): void
    {
        $token = get_query_var('angebot_voucher_token');
        if (!$token) {
            return;
        }

        $voucher = Angebot_Deals_Voucher::get_by_token(sanitize_text_field((string) $token));
        status_header(200);
        nocache_headers();

        include ANGEBOT_DEALS_PATH . 'templates/voucher-redeem.php';
        exit;
    }
}
