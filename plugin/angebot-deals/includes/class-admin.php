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
            wp_die(esc_html__('Permission denied.', 'angebot-deals'));
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
            __('Settings', 'angebot-deals'),
            __('Settings', 'angebot-deals'),
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
            'default'           => 'london',
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
            <h1><?php esc_html_e('Angebot Deals — Settings', 'angebot-deals'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('angebot_deals'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="angebot_brand_name"><?php esc_html_e('Brand name', 'angebot-deals'); ?></label></th>
                        <td>
                            <input type="text" class="regular-text" id="angebot_brand_name" name="angebot_brand_name" value="<?php echo esc_attr((string) get_option('angebot_brand_name', 'Angebot')); ?>">
                            <p class="description"><?php esc_html_e('Your own brand name — do not use “Groupon”.', 'angebot-deals'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="angebot_default_location"><?php esc_html_e('Default city (slug)', 'angebot-deals'); ?></label></th>
                        <td>
                            <input type="text" class="regular-text" id="angebot_default_location" name="angebot_default_location" value="<?php echo esc_attr((string) get_option('angebot_default_location', 'london')); ?>">
                            <p class="description"><?php esc_html_e('e.g. london, manchester, birmingham', 'angebot-deals'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <hr>
            <h2><?php esc_html_e('Demo content', 'angebot-deals'); ?></h2>
            <?php if (isset($_GET['demo_seeded'])) : ?>
                <div class="notice notice-success"><p>
                    <?php printf(esc_html__('%d demo deals created.', 'angebot-deals'), absint($_GET['demo_seeded'])); ?>
                </p></div>
            <?php endif; ?>
            <p><?php esc_html_e('Creates sample deals in London, Manchester, Birmingham, Edinburgh and Bath so you can test design & checkout.', 'angebot-deals'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="angebot_seed_demo">
                <?php wp_nonce_field('angebot_seed_demo'); ?>
                <?php submit_button(__('Create demo deals', 'angebot-deals'), 'secondary', 'submit', false); ?>
            </form>
            <hr>
            <h2><?php esc_html_e('Payments (Stripe / PayPal)', 'angebot-deals'); ?></h2>
            <p><?php esc_html_e('In WooCommerce → Settings → Payments, enable the “WooCommerce Stripe Gateway” and/or “WooCommerce PayPal Payments” plugins. Disable test mode there once live keys are set.', 'angebot-deals'); ?></p>
        </div>
        <?php
    }

    public static function product_state(array $states, WP_Post $post): array
    {
        if ($post->post_type === 'product' && get_post_meta($post->ID, '_angebot_is_deal_product', true) === 'yes') {
            $states['angebot_deal'] = __('Deal product (hidden)', 'angebot-deals');
        }
        return $states;
    }
}
