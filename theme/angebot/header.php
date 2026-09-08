<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Zum Inhalt springen', 'angebot'); ?></a>

<header class="site-header">
    <div class="site-header__top">
        <div class="container site-header__top-inner">
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a class="site-logo-text" href="<?php echo esc_url(home_url('/')); ?>">
                        <span class="site-logo-mark" aria-hidden="true">A</span>
                        <span class="site-logo-name"><?php echo esc_html(angebot_brand_name()); ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <form class="site-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="angebot-search"><?php esc_html_e('Deals suchen', 'angebot'); ?></label>
                <input id="angebot-search" type="search" name="s" placeholder="<?php esc_attr_e('Wonach suchst du?', 'angebot'); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
                <input type="hidden" name="post_type" value="deal">
                <button type="submit" aria-label="<?php esc_attr_e('Suchen', 'angebot'); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </form>

            <div class="site-header__actions">
                <?php
                if (shortcode_exists('angebot_location_picker')) {
                    echo do_shortcode('[angebot_location_picker]');
                }
                ?>
                <?php if (function_exists('wc_get_page_permalink')) : ?>
                    <a class="header-link" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
                        <?php echo is_user_logged_in() ? esc_html__('Konto', 'angebot') : esc_html__('Login', 'angebot'); ?>
                    </a>
                    <a class="header-cart" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                        <?php esc_html_e('Warenkorb', 'angebot'); ?>
                        <span class="header-cart__count"><?php echo esc_html((string) angebot_cart_count()); ?></span>
                    </a>
                <?php endif; ?>
                <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e('Menü', 'angebot'); ?>">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>

    <div class="site-header__nav" id="primary-nav">
        <div class="container">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => 'nav',
                'container_class'=> 'primary-nav',
                'fallback_cb'    => static function (): void {
                    if (shortcode_exists('angebot_categories')) {
                        echo do_shortcode('[angebot_categories]');
                    }
                },
            ]);
            ?>
        </div>
    </div>

    <?php if (!is_front_page() && shortcode_exists('angebot_categories')) : ?>
        <div class="site-header__cats">
            <?php echo do_shortcode('[angebot_categories]'); ?>
        </div>
    <?php endif; ?>
</header>

<main id="main" class="site-main">
