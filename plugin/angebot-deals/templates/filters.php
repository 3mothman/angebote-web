<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$current_cat = isset($_GET['deal_category']) ? sanitize_title(wp_unslash($_GET['deal_category'])) : '';
$current_loc = isset($_GET['deal_location']) ? sanitize_title(wp_unslash($_GET['deal_location'])) : Angebot_Deals_Location::current();
$min = isset($_GET['min_price']) ? (float) $_GET['min_price'] : '';
$max = isset($_GET['max_price']) ? (float) $_GET['max_price'] : '';
?>
<form class="angebot-filters" id="angebot-filters" action="" method="get">
    <div class="angebot-filters__row">
        <label>
            <span><?php esc_html_e('Kategorie', 'angebot-deals'); ?></span>
            <select name="deal_category" data-filter="category">
                <option value=""><?php esc_html_e('Alle Kategorien', 'angebot-deals'); ?></option>
                <?php if (!is_wp_error($categories)) : foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($current_cat, $cat->slug); ?>>
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </label>
        <label>
            <span><?php esc_html_e('Standort', 'angebot-deals'); ?></span>
            <select name="deal_location" data-filter="location">
                <option value="all"><?php esc_html_e('Alle Städte', 'angebot-deals'); ?></option>
                <?php if (!is_wp_error($locations)) : foreach ($locations as $loc) : ?>
                    <option value="<?php echo esc_attr($loc->slug); ?>" <?php selected($current_loc, $loc->slug); ?>>
                        <?php echo esc_html($loc->name); ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </label>
        <label>
            <span><?php esc_html_e('Preis von', 'angebot-deals'); ?></span>
            <input type="number" name="min_price" min="0" step="1" value="<?php echo esc_attr((string) $min); ?>" data-filter="min_price" placeholder="0">
        </label>
        <label>
            <span><?php esc_html_e('Preis bis', 'angebot-deals'); ?></span>
            <input type="number" name="max_price" min="0" step="1" value="<?php echo esc_attr((string) $max); ?>" data-filter="max_price" placeholder="999">
        </label>
        <label>
            <span><?php esc_html_e('Sortierung', 'angebot-deals'); ?></span>
            <select name="orderby" data-filter="orderby">
                <option value="date"><?php esc_html_e('Neueste', 'angebot-deals'); ?></option>
                <option value="price"><?php esc_html_e('Preis', 'angebot-deals'); ?></option>
            </select>
        </label>
        <button type="submit" class="angebot-btn angebot-btn--primary"><?php esc_html_e('Filtern', 'angebot-deals'); ?></button>
    </div>
</form>
