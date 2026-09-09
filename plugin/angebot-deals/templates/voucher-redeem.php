<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$deal_title = $voucher ? get_the_title((int) $voucher->deal_id) : '';
$can_redeem = is_user_logged_in() && current_user_can('angebot_redeem_voucher');
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e('Voucher', 'angebot-deals'); ?> — <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body class="angebot-voucher-page">
<main class="angebot-voucher-shell">
    <?php if (!$voucher) : ?>
        <h1><?php esc_html_e('Voucher not found', 'angebot-deals'); ?></h1>
    <?php else :
        $voucher = Angebot_Deals_Voucher::refresh_status($voucher);
        ?>
        <h1><?php echo esc_html($deal_title); ?></h1>
        <p class="angebot-voucher-code"><?php echo esc_html($voucher->code); ?></p>
        <p class="angebot-voucher-status status-<?php echo esc_attr($voucher->status); ?>">
            <?php echo esc_html(Angebot_Deals_Voucher::status_label($voucher->status)); ?>
        </p>
        <?php if ($voucher->expires_at) : ?>
            <p><?php printf(esc_html__('Valid until %s', 'angebot-deals'), esc_html(date_i18n('d/m/Y', strtotime($voucher->expires_at)))); ?></p>
        <?php endif; ?>
        <?php echo Angebot_Deals_QR_Code::render_img((string) $voucher->qr_token, 220); // phpcs:ignore ?>
        <?php if ($can_redeem && $voucher->status === Angebot_Deals_Voucher::STATUS_ACTIVE) : ?>
            <button type="button" class="angebot-btn angebot-btn--primary" id="angebot-redeem-from-qr" data-id="<?php echo esc_attr((string) $voucher->id); ?>">
                <?php esc_html_e('Mark as redeemed', 'angebot-deals'); ?>
            </button>
            <p id="angebot-redeem-msg" hidden></p>
            <script>
            document.getElementById('angebot-redeem-from-qr')?.addEventListener('click', async function () {
                const res = await fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'angebot_redeem_voucher',
                        nonce: '<?php echo esc_js(wp_create_nonce('angebot_merchant')); ?>',
                        voucher_id: this.dataset.id
                    })
                });
                const json = await res.json();
                const msg = document.getElementById('angebot-redeem-msg');
                msg.hidden = false;
                msg.textContent = json.data?.message || json.data?.message || (json.success ? 'OK' : 'Error');
                if (json.success) location.reload();
            });
            </script>
        <?php elseif (!$can_redeem) : ?>
            <p class="angebot-hint"><?php esc_html_e('Customer: show this screen to the merchant. Merchant: please log in to redeem.', 'angebot-deals'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
