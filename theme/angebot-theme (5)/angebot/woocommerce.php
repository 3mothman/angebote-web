<?php
/**
 * WooCommerce wrapper — keeps shop/cart/checkout in theme chrome.
 */
defined('ABSPATH') || exit;
get_header();
?>
<div class="container content-page woocommerce-wrap">
    <?php woocommerce_content(); ?>
</div>
<?php
get_footer();
