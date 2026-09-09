<?php
declare(strict_types=1);

get_header();
?>

<div class="container archive-deals">
    <header class="archive-header">
        <h1>
            <?php
            if (is_tax('deal_category')) {
                single_term_title();
            } elseif (is_tax('deal_location')) {
                printf(esc_html__('Deals in %s', 'angebot'), single_term_title('', false));
            } else {
                esc_html_e('All Deals', 'angebot');
            }
            ?>
        </h1>
        <?php the_archive_description('<div class="archive-desc">', '</div>'); ?>
    </header>

    <?php
    if (shortcode_exists('angebot_filters')) {
        echo do_shortcode('[angebot_filters]');
    }
    ?>

    <div class="angebot-deals-grid" data-columns="3" id="angebot-deals-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php
                if (defined('ANGEBOT_DEALS_PATH')) {
                    include ANGEBOT_DEALS_PATH . 'templates/deal-card.php';
                }
                ?>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="angebot-empty"><?php esc_html_e('No deals found.', 'angebot'); ?></p>
        <?php endif; ?>
    </div>

    <div class="pagination">
        <?php the_posts_pagination(['mid_size' => 2]); ?>
    </div>
</div>

<?php
get_footer();
