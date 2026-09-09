<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap angebot-admin-vouchers">
    <h1><?php esc_html_e('Redeem vouchers', 'angebot-deals'); ?></h1>

    <div class="angebot-admin-lookup card" style="max-width:640px;padding:16px;margin:16px 0">
        <h2><?php esc_html_e('Check code', 'angebot-deals'); ?></h2>
        <p>
            <input type="text" id="angebot-admin-code" class="regular-text" placeholder="XXXX-XXXX-XXXX">
            <button type="button" class="button button-primary" id="angebot-admin-lookup-btn"><?php esc_html_e('Check', 'angebot-deals'); ?></button>
            <button type="button" class="button" id="angebot-admin-redeem-btn" disabled><?php esc_html_e('Redeem', 'angebot-deals'); ?></button>
        </p>
        <div id="angebot-admin-lookup-result"></div>
    </div>

    <form method="get">
        <input type="hidden" name="page" value="angebot-vouchers">
        <p class="search-box">
            <label class="screen-reader-text" for="voucher-search"><?php esc_html_e('Search', 'angebot-deals'); ?></label>
            <input type="search" id="voucher-search" name="s" value="<?php echo esc_attr($search); ?>">
            <select name="status">
                <option value=""><?php esc_html_e('All statuses', 'angebot-deals'); ?></option>
                <?php foreach (['active', 'redeemed', 'expired', 'cancelled'] as $st) : ?>
                    <option value="<?php echo esc_attr($st); ?>" <?php selected($status, $st); ?>>
                        <?php echo esc_html(Angebot_Deals_Voucher::status_label($st)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php submit_button(__('Filter', 'angebot-deals'), '', '', false); ?>
        </p>
    </form>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Code', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Deal', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Customer', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Status', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Expires', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Order', 'angebot-deals'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$vouchers) : ?>
                <tr><td colspan="6"><?php esc_html_e('No vouchers.', 'angebot-deals'); ?></td></tr>
            <?php else : foreach ($vouchers as $v) : ?>
                <tr>
                    <td><code><?php echo esc_html($v->code); ?></code></td>
                    <td><?php echo esc_html(get_the_title((int) $v->deal_id)); ?></td>
                    <td><?php echo esc_html($v->customer_email); ?></td>
                    <td><?php echo esc_html(Angebot_Deals_Voucher::status_label($v->status)); ?></td>
                    <td><?php echo $v->expires_at ? esc_html(date_i18n('d.m.Y', strtotime($v->expires_at))) : '—'; ?></td>
                    <td>#<?php echo esc_html((string) $v->order_id); ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
