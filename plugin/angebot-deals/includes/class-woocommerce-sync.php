<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_WooCommerce_Sync
{
    public static function register_hooks(): void
    {
        add_action('save_post_' . Angebot_Deals_Deal_CPT::POST_TYPE, [self::class, 'sync_product'], 20, 2);
        add_action('before_delete_post', [self::class, 'delete_linked_product']);
        add_filter('woocommerce_is_purchasable', [self::class, 'filter_purchasable'], 10, 2);
        add_action('woocommerce_check_cart_items', [self::class, 'validate_cart_stock']);
        add_filter('woocommerce_add_to_cart_validation', [self::class, 'validate_add_to_cart'], 10, 3);
        add_filter('woocommerce_product_is_visible', [self::class, 'hide_from_catalog'], 10, 2);
    }

    public static function sync_product(int $post_id, WP_Post $post): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_status === 'auto-draft') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $data       = Angebot_Deals_Deal_Meta::get_all($post_id);
        $product_id = (int) $data['product_id'];
        $product    = $product_id ? wc_get_product($product_id) : false;

        if (!$product) {
            $product = new WC_Product_Simple();
            $product->set_catalog_visibility('hidden');
            $product->set_virtual(true);
            $product->set_sold_individually(false);
        }

        $product->set_name($post->post_title);
        $product->set_status($post->post_status === 'publish' ? 'publish' : 'draft');
        $product->set_regular_price((string) $data['original_price']);
        $product->set_sale_price((string) $data['deal_price']);
        $product->set_price((string) $data['deal_price']);
        $product->set_description($post->post_content);
        $product->set_short_description($post->post_excerpt);

        $qty = (int) $data['quantity'];
        $sold = (int) $data['sold_count'];
        if ($qty > 0) {
            $product->set_manage_stock(true);
            $product->set_stock_quantity(max(0, $qty - $sold));
            $product->set_stock_status(($qty - $sold) > 0 ? 'instock' : 'outofstock');
        } else {
            $product->set_manage_stock(false);
            $product->set_stock_status('instock');
        }

        $thumb = get_post_thumbnail_id($post_id);
        if ($thumb) {
            $product->set_image_id($thumb);
        }

        $product->update_meta_data('_angebot_deal_id', $post_id);
        $product->update_meta_data('_angebot_is_deal_product', 'yes');

        $new_id = $product->save();
        update_post_meta($post_id, '_angebot_product_id', $new_id);
    }

    public static function delete_linked_product(int $post_id): void
    {
        if (get_post_type($post_id) !== Angebot_Deals_Deal_CPT::POST_TYPE) {
            return;
        }

        $product_id = (int) Angebot_Deals_Deal_Meta::get($post_id, 'product_id', 0);
        if ($product_id) {
            wp_delete_post($product_id, true);
        }
    }

    public static function get_deal_id_from_product(int $product_id): int
    {
        return (int) get_post_meta($product_id, '_angebot_deal_id', true);
    }

    public static function hide_from_catalog(bool $visible, int $product_id): bool
    {
        if (get_post_meta($product_id, '_angebot_is_deal_product', true) === 'yes') {
            return false;
        }
        return $visible;
    }

    public static function filter_purchasable(bool $purchasable, WC_Product $product): bool
    {
        $deal_id = self::get_deal_id_from_product($product->get_id());
        if (!$deal_id) {
            return $purchasable;
        }

        if (get_post_status($deal_id) !== 'publish') {
            return false;
        }

        if (Angebot_Deals_Deal_Meta::remaining_quantity($deal_id) <= 0) {
            return false;
        }

        return $purchasable;
    }

    public static function validate_add_to_cart(bool $passed, int $product_id, int $quantity): bool
    {
        $deal_id = self::get_deal_id_from_product($product_id);
        if (!$deal_id) {
            return $passed;
        }

        $remaining = Angebot_Deals_Deal_Meta::remaining_quantity($deal_id);
        if ($quantity > $remaining) {
            wc_add_notice(
                sprintf(
                    /* translators: %d: remaining quantity */
                    __('Nur noch %d Stück dieses Deals verfügbar.', 'angebot-deals'),
                    $remaining
                ),
                'error'
            );
            return false;
        }

        return $passed;
    }

    public static function validate_cart_stock(): void
    {
        foreach (WC()->cart->get_cart() as $item) {
            $product_id = (int) $item['product_id'];
            $deal_id    = self::get_deal_id_from_product($product_id);
            if (!$deal_id) {
                continue;
            }

            $remaining = Angebot_Deals_Deal_Meta::remaining_quantity($deal_id);
            if ((int) $item['quantity'] > $remaining) {
                wc_add_notice(__('Ein Deal im Warenkorb ist nicht mehr in der gewünschten Menge verfügbar.', 'angebot-deals'), 'error');
            }
        }
    }
}
