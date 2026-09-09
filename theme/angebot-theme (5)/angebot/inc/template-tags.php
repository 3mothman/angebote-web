<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function angebot_deal_buy_url(int $deal_id): string
{
    if (!class_exists('Angebot_Deals_Deal_Meta')) {
        return get_permalink($deal_id) ?: '#';
    }
    $product_id = (int) Angebot_Deals_Deal_Meta::get($deal_id, 'product_id', 0);
    if (!$product_id || !function_exists('wc_get_cart_url')) {
        return get_permalink($deal_id) ?: '#';
    }
    return add_query_arg('add-to-cart', $product_id, wc_get_cart_url());
}

function angebot_format_price(float $price): string
{
    return number_format($price, 2, ',', '.') . ' €';
}
