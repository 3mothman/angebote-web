<?php
declare(strict_types=1);

get_header();
?>

<div class="container content-page">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="entry-content"><?php the_content(); ?></div>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();
