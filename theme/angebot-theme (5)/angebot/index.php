<?php
declare(strict_types=1);

get_header();
?>

<div class="container content-page">
    <?php if (have_posts()) : ?>
        <div class="angebot-deals-grid" data-columns="3">
            <?php while (have_posts()) : the_post(); ?>
                <?php
                if (get_post_type() === 'deal' && defined('ANGEBOT_DEALS_PATH')) {
                    include ANGEBOT_DEALS_PATH . 'templates/deal-card.php';
                } else {
                    ?>
                    <article <?php post_class('search-result'); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                    </article>
                    <?php
                }
                ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('Nothing found.', 'angebot'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
