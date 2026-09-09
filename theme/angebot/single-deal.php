<?php
declare(strict_types=1);

get_header();

while (have_posts()) :
    the_post();
    $deal_id = get_the_ID();
    $data = class_exists('Angebot_Deals_Deal_Meta')
        ? Angebot_Deals_Deal_Meta::get_all($deal_id)
        : [];
    $remaining = class_exists('Angebot_Deals_Deal_Meta')
        ? Angebot_Deals_Deal_Meta::remaining_quantity($deal_id)
        : 0;
    $product_id = (int) ($data['product_id'] ?? 0);
    $thumb = get_the_post_thumbnail_url($deal_id, 'large');
    $highlights = array_filter(array_map('trim', explode("\n", (string) ($data['highlights'] ?? ''))));
    $avg = class_exists('Angebot_Deals_Reviews') ? Angebot_Deals_Reviews::average_rating($deal_id) : 0;
    $rcount = class_exists('Angebot_Deals_Reviews') ? Angebot_Deals_Reviews::count($deal_id) : 0;
    ?>
    <article <?php post_class('deal-single'); ?>>
        <div class="container deal-single__layout">
            <div class="deal-single__main">
                <div class="deal-single__gallery">
                    <?php if ($thumb) : ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>
                    <?php if (!empty($data['discount_percent'])) : ?>
                        <span class="angebot-badge">-<?php echo esc_html((string) (int) $data['discount_percent']); ?>%</span>
                    <?php endif; ?>
                </div>

                <header class="deal-single__header">
                    <?php if (!empty($data['merchant_name'])) : ?>
                        <p class="deal-single__merchant"><?php echo esc_html((string) $data['merchant_name']); ?></p>
                    <?php endif; ?>
                    <h1><?php the_title(); ?></h1>
                    <?php if (!empty($data['location_label'])) : ?>
                        <p class="deal-single__location"><?php echo esc_html((string) $data['location_label']); ?></p>
                    <?php endif; ?>
                    <?php if ($avg > 0) : ?>
                        <p class="deal-single__rating">
                            <span class="angebot-stars"><?php echo esc_html(str_repeat('★', (int) round($avg))); ?></span>
                            <?php echo esc_html(number_format($avg, 1, ',', '.')); ?>
                            (<?php echo esc_html((string) $rcount); ?>)
                        </p>
                    <?php endif; ?>
                </header>

                <?php if ($highlights) : ?>
                    <ul class="deal-highlights">
                        <?php foreach ($highlights as $line) : ?>
                            <li><?php echo esc_html($line); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="deal-content entry-content">
                    <?php the_content(); ?>
                </div>

                <?php if (!empty($data['fine_print'])) : ?>
                    <details class="deal-fineprint">
                        <summary><?php esc_html_e('Terms & fine print', 'angebot'); ?></summary>
                        <div><?php echo nl2br(esc_html((string) $data['fine_print'])); ?></div>
                    </details>
                <?php endif; ?>

                <?php
                if (shortcode_exists('angebot_reviews')) {
                    echo do_shortcode('[angebot_reviews deal_id="' . (int) $deal_id . '"]');
                }
                ?>
            </div>

            <aside class="deal-single__buybox">
                <div class="buybox">
                    <div class="buybox__prices">
                        <span class="angebot-price-old"><?php echo esc_html(angebot_format_price((float) ($data['original_price'] ?? 0))); ?></span>
                        <span class="angebot-price-new"><?php echo esc_html(angebot_format_price((float) ($data['deal_price'] ?? 0))); ?></span>
                    </div>
                    <?php if (!empty($data['discount_percent'])) : ?>
                        <p class="buybox__save">
                            <?php printf(esc_html__('You save %d%%', 'angebot'), (int) $data['discount_percent']); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($data['voucher_expires'])) : ?>
                        <p class="angebot-countdown" data-expires="<?php echo esc_attr((string) $data['voucher_expires']); ?>T23:59:59">
                            <?php esc_html_e('Voucher valid — expires in', 'angebot'); ?>
                            <span class="angebot-countdown__time">—</span>
                        </p>
                    <?php endif; ?>

                    <?php if ($remaining < PHP_INT_MAX) : ?>
                        <p class="buybox__stock">
                            <?php printf(esc_html__('%d left', 'angebot'), (int) $remaining); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($remaining > 0 && $product_id) : ?>
                        <a class="angebot-btn angebot-btn--primary buybox__cta" href="<?php echo esc_url(angebot_deal_buy_url($deal_id)); ?>">
                            <?php esc_html_e('Buy now', 'angebot'); ?>
                        </a>
                    <?php else : ?>
                        <span class="angebot-btn angebot-btn--disabled buybox__cta"><?php esc_html_e('Sold out', 'angebot'); ?></span>
                    <?php endif; ?>

                    <p class="buybox__note"><?php esc_html_e('After purchase you will receive your voucher by email — including a QR code.', 'angebot'); ?></p>
                </div>
            </aside>
        </div>
    </article>
    <?php
endwhile;

get_footer();
