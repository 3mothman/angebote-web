<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Admin
{
    public static function register_hooks(): void
    {
        add_action('admin_menu', [self::class, 'settings_menu']);
        add_action('admin_init', [self::class, 'register_settings']);
        add_action('admin_post_angebot_seed_demo', [self::class, 'handle_seed_demo']);
        add_filter('display_post_states', [self::class, 'product_state'], 10, 2);
    }

    public static function handle_seed_demo(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Keine Berechtigung.', 'angebot-deals'));
        }
        check_admin_referer('angebot_seed_demo');
        $created = Angebot_Deals_Demo_Content::seed();
        wp_safe_redirect(add_query_arg([
            'page'         => 'angebot-settings',
            'post_type'    => 'deal',
            'demo_seeded'  => count($created),
        ], admin_url('edit.php')));
        exit;
    }

    public static function settings_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=deal',
            __('Einstellungen', 'angebot-deals'),
            __('Einstellungen', 'angebot-deals'),
            'manage_options',
            'angebot-settings',
            [self::class, 'render_settings']
        );
    }

    public static function register_settings(): void
    {
        register_setting('angebot_deals', 'angebot_default_location', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_title',
            'default'           => 'berlin',
        ]);
        register_setting('angebot_deals', 'angebot_brand_name', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'Angebot',
        ]);
    }

    public static function render_settings(): void
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Angebot Deals — Einstellungen', 'angebot-deals'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('angebot_deals'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="angebot_brand_name"><?php esc_html_e('Markenname', 'angebot-deals'); ?></label></th>
                        <td>
                            <input type="text" class="regular-text" id="angebot_brand_name" name="angebot_brand_name" value="<?php echo esc_attr((string) get_option('angebot_brand_name', 'Angebot')); ?>">
                            <p class="description"><?php esc_html_e('Eigener Name — nicht „Groupon“ verwenden.', 'angebot-deals'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="angebot_default_location"><?php esc_html_e('Standard-Standort (Slug)', 'angebot-deals'); ?></label></th>
                        <td>
                            <input type="text" class="regular-text" id="angebot_default_location" name="angebot_default_location" value="<?php echo esc_attr((string) get_option('angebot_default_location', 'berlin')); ?>">
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <hr>
            <h2><?php esc_html_e('Demo-Inhalte', 'angebot-deals'); ?></h2>
            <?php if (isset($_GET['demo_seeded'])) : ?>
                <div class="notice notice-success"><p>
                    <?php printf(esc_html__('%d Demo-Deals angelegt.', 'angebot-deals'), absint($_GET['demo_seeded'])); ?>
                </p></div>
            <?php endif; ?>
            <p><?php esc_html_e('Legt ein paar Beispiel-Deals an (Berlin, Halle, Leipzig, Merseburg), damit du Design & Checkout sofort testen kannst.', 'angebot-deals'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="angebot_seed_demo">
                <?php wp_nonce_field('angebot_seed_demo'); ?>
                <?php submit_button(__('Demo-Deals anlegen', 'angebot-deals'), 'secondary', 'submit', false); ?>
            </form>
            <hr>
            <h2><?php esc_html_e('Zahlungen (Stripe / PayPal)', 'angebot-deals'); ?></h2>
            <p><?php esc_html_e('Aktiviere in WooCommerce → Einstellungen → Zahlungen die Plugins „WooCommerce Stripe Gateway“ und/oder „WooCommerce PayPal Payments“. Testmodus dort deaktivieren, sobald Live-Keys hinterlegt sind.', 'angebot-deals'); ?></p>
        </div>
        <?php
    }

    public static function product_state(array $states, WP_Post $post): array
    {
        if ($post->post_type === 'product' && get_post_meta($post->ID, '_angebot_is_deal_product', true) === 'yes') {
            $states['angebot_deal'] = __('Deal-Produkt (versteckt)', 'angebot-deals');
        }
        return $states;
    }
}
