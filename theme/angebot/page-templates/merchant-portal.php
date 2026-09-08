<?php
declare(strict_types=1);
/**
 * Template Name: Merchant Portal
 */
get_header();
?>
<div class="container content-page">
    <h1><?php the_title(); ?></h1>
    <?php echo do_shortcode('[angebot_merchant_portal]'); ?>
</div>
<?php
get_footer();
