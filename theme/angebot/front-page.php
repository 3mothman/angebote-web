<?php
declare(strict_types=1);

get_header();
?>

<section class="hero">
    <div class="hero__atmosphere" aria-hidden="true"></div>
    <div class="container hero__content">
        <p class="hero__brand"><?php echo esc_html(angebot_brand_name()); ?></p>
        <h1 class="hero__title"><?php esc_html_e('Deals in deiner Stadt', 'angebot'); ?></h1>
        <p class="hero__lead"><?php esc_html_e('Spare bei Essen, Wellness, Freizeit und mehr — lokale Angebote mit echtem Rabatt.', 'angebot'); ?></p>
        <div class="hero__cta">
            <a class="angebot-btn angebot-btn--primary" href="<?php echo esc_url(get_post_type_archive_link('deal')); ?>">
                <?php esc_html_e('Deals entdecken', 'angebot'); ?>
            </a>
            <?php if (shortcode_exists('angebot_location_picker')) : ?>
                <div class="hero__location"><?php echo do_shortcode('[angebot_location_picker]'); ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (shortcode_exists('angebot_categories')) : ?>
    <div class="home-cats container">
        <?php echo do_shortcode('[angebot_categories]'); ?>
    </div>
<?php endif; ?>

<section class="home-section container">
    <header class="section-header">
        <h2><?php esc_html_e('Highlights', 'angebot'); ?></h2>
        <p><?php esc_html_e('Ausgewählte Angebote mit besonders starkem Rabatt.', 'angebot'); ?></p>
    </header>
    <?php echo do_shortcode('[angebot_featured limit="4"]'); ?>
</section>

<section class="home-section container">
    <header class="section-header">
        <h2><?php printf(esc_html__('Beliebt in %s', 'angebot'), esc_html(class_exists('Angebot_Deals_Location') ? Angebot_Deals_Location::current_label() : '')); ?></h2>
        <p><?php esc_html_e('Aktuelle Deals in deiner ausgewählten Stadt.', 'angebot'); ?></p>
    </header>
    <?php echo do_shortcode('[deals_grid limit="9" columns="3" show_filters="1"]'); ?>
</section>

<section class="home-howto">
    <div class="container">
        <header class="section-header section-header--center">
            <h2><?php esc_html_e('So funktioniert\'s', 'angebot'); ?></h2>
        </header>
        <ol class="howto-steps">
            <li>
                <strong><?php esc_html_e('Deal wählen', 'angebot'); ?></strong>
                <span><?php esc_html_e('Stadt & Kategorie filtern, Angebot aussuchen.', 'angebot'); ?></span>
            </li>
            <li>
                <strong><?php esc_html_e('Sicher bezahlen', 'angebot'); ?></strong>
                <span><?php esc_html_e('Checkout über WooCommerce — Stripe oder PayPal.', 'angebot'); ?></span>
            </li>
            <li>
                <strong><?php esc_html_e('Einlösen', 'angebot'); ?></strong>
                <span><?php esc_html_e('QR-Code oder Textcode beim Anbieter vorzeigen.', 'angebot'); ?></span>
            </li>
        </ol>
    </div>
</section>

<?php
get_footer();
