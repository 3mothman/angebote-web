<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Reviews
{
    public static function register_hooks(): void
    {
        add_action('wp_ajax_angebot_submit_review', [self::class, 'ajax_submit']);
        add_action('wp_ajax_nopriv_angebot_submit_review', [self::class, 'ajax_submit']);
        add_shortcode('angebot_reviews', [self::class, 'shortcode']);
        add_action('admin_menu', [self::class, 'admin_menu']);
    }

    public static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'angebot_reviews';
    }

    public static function shortcode($atts): string
    {
        $atts = shortcode_atts(['deal_id' => '0'], $atts, 'angebot_reviews');
        $deal_id = absint($atts['deal_id']) ?: get_the_ID();
        if (!$deal_id) {
            return '';
        }

        $reviews = self::get_approved($deal_id);
        $avg     = self::average_rating($deal_id);

        ob_start();
        include ANGEBOT_DEALS_PATH . 'templates/reviews.php';
        return (string) ob_get_clean();
    }

    public static function get_approved(int $deal_id, int $limit = 50): array
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            'SELECT * FROM ' . self::table() . ' WHERE deal_id = %d AND status = %s ORDER BY created_at DESC LIMIT %d',
            $deal_id,
            'approved',
            $limit
        )) ?: [];
    }

    public static function average_rating(int $deal_id): float
    {
        global $wpdb;
        $avg = $wpdb->get_var($wpdb->prepare(
            'SELECT AVG(rating) FROM ' . self::table() . ' WHERE deal_id = %d AND status = %s',
            $deal_id,
            'approved'
        ));
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public static function count(int $deal_id): int
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare(
            'SELECT COUNT(*) FROM ' . self::table() . ' WHERE deal_id = %d AND status = %s',
            $deal_id,
            'approved'
        ));
    }

    public static function ajax_submit(): void
    {
        check_ajax_referer('angebot_public', 'nonce');

        $deal_id = absint($_POST['deal_id'] ?? 0);
        $rating  = absint($_POST['rating'] ?? 0);
        $title   = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
        $content = sanitize_textarea_field(wp_unslash($_POST['content'] ?? ''));
        $name    = sanitize_text_field(wp_unslash($_POST['author_name'] ?? ''));
        $email   = sanitize_email(wp_unslash($_POST['author_email'] ?? ''));

        if (!$deal_id || get_post_type($deal_id) !== Angebot_Deals_Deal_CPT::POST_TYPE) {
            wp_send_json_error(['message' => __('Ungültiger Deal.', 'angebot-deals')]);
        }

        if ($rating < 1 || $rating > 5 || $content === '' || $name === '') {
            wp_send_json_error(['message' => __('Bitte Bewertung, Name und Text ausfüllen.', 'angebot-deals')]);
        }

        global $wpdb;
        $wpdb->insert(
            self::table(),
            [
                'deal_id'      => $deal_id,
                'user_id'      => get_current_user_id(),
                'author_name'  => $name,
                'author_email' => $email,
                'rating'       => $rating,
                'title'        => $title,
                'content'      => $content,
                'status'       => current_user_can('manage_options') ? 'approved' : 'pending',
                'created_at'   => current_time('mysql'),
            ],
            ['%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s']
        );

        wp_send_json_success([
            'message' => __('Danke! Deine Bewertung wird geprüft.', 'angebot-deals'),
        ]);
    }

    public static function admin_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=deal',
            __('Bewertungen', 'angebot-deals'),
            __('Bewertungen', 'angebot-deals'),
            'manage_options',
            'angebot-reviews',
            [self::class, 'render_admin']
        );
    }

    public static function render_admin(): void
    {
        global $wpdb;
        $table = self::table();

        if (isset($_GET['approve'], $_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'angebot_review')) {
            $wpdb->update($table, ['status' => 'approved'], ['id' => absint($_GET['approve'])], ['%s'], ['%d']);
            echo '<div class="notice notice-success"><p>' . esc_html__('Freigegeben.', 'angebot-deals') . '</p></div>';
        }

        if (isset($_GET['trash'], $_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'angebot_review')) {
            $wpdb->delete($table, ['id' => absint($_GET['trash'])], ['%d']);
            echo '<div class="notice notice-success"><p>' . esc_html__('Gelöscht.', 'angebot-deals') . '</p></div>';
        }

        $reviews = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 100");
        include ANGEBOT_DEALS_PATH . 'admin/views/reviews.php';
    }
}
