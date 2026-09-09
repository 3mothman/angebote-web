<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="angebot-reviews" id="angebot-reviews" data-deal="<?php echo esc_attr((string) $deal_id); ?>">
    <header class="angebot-reviews__header">
        <h2><?php esc_html_e('Reviews', 'angebot-deals'); ?></h2>
        <?php if ($avg > 0) : ?>
            <p class="angebot-reviews__avg">
                <strong><?php echo esc_html(number_format($avg, 1, ',', '.')); ?></strong> / 5
                <span>(<?php echo esc_html((string) count($reviews)); ?>)</span>
            </p>
        <?php endif; ?>
    </header>

    <div class="angebot-reviews__list">
        <?php if (!$reviews) : ?>
            <p><?php esc_html_e('No reviews yet — be the first!', 'angebot-deals'); ?></p>
        <?php else : foreach ($reviews as $review) : ?>
            <article class="angebot-review">
                <div class="angebot-review__meta">
                    <strong><?php echo esc_html($review->author_name); ?></strong>
                    <span class="angebot-stars" aria-label="<?php echo esc_attr((string) $review->rating); ?> stars">
                        <?php echo esc_html(str_repeat('★', (int) $review->rating)); ?>
                    </span>
                    <time datetime="<?php echo esc_attr($review->created_at); ?>">
                        <?php echo esc_html(date_i18n('d.m.Y', strtotime($review->created_at))); ?>
                    </time>
                </div>
                <?php if ($review->title) : ?>
                    <h3><?php echo esc_html($review->title); ?></h3>
                <?php endif; ?>
                <p><?php echo esc_html($review->content); ?></p>
            </article>
        <?php endforeach; endif; ?>
    </div>

    <form class="angebot-review-form" id="angebot-review-form">
        <h3><?php esc_html_e('Write a review', 'angebot-deals'); ?></h3>
        <input type="hidden" name="deal_id" value="<?php echo esc_attr((string) $deal_id); ?>">
        <label>
            <span><?php esc_html_e('Stars', 'angebot-deals'); ?></span>
            <select name="rating" required>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </label>
        <label>
            <span><?php esc_html_e('Name', 'angebot-deals'); ?></span>
            <input type="text" name="author_name" required value="<?php echo esc_attr(wp_get_current_user()->display_name ?? ''); ?>">
        </label>
        <label>
            <span><?php esc_html_e('Email', 'angebot-deals'); ?></span>
            <input type="email" name="author_email" value="<?php echo esc_attr(wp_get_current_user()->user_email ?? ''); ?>">
        </label>
        <label>
            <span><?php esc_html_e('Title', 'angebot-deals'); ?></span>
            <input type="text" name="title">
        </label>
        <label>
            <span><?php esc_html_e('Your experience', 'angebot-deals'); ?></span>
            <textarea name="content" rows="4" required></textarea>
        </label>
        <button type="submit" class="angebot-btn angebot-btn--primary"><?php esc_html_e('Submit', 'angebot-deals'); ?></button>
        <p class="angebot-review-form__msg" hidden></p>
    </form>
</section>
