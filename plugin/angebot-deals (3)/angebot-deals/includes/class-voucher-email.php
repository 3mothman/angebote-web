<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Voucher_Email
{
    public static function register_hooks(): void
    {
        add_action('angebot_deals_vouchers_created', [self::class, 'send'], 10, 2);
        add_action('woocommerce_email_order_details', [self::class, 'append_to_order_email'], 20, 4);
    }

    public static function send(array $vouchers, WC_Order $order): void
    {
        if (!$vouchers) {
            return;
        }

        $to      = $order->get_billing_email();
        $subject = sprintf(
            /* translators: %s: site name */
            __('[%s] Your vouchers', 'angebot-deals'),
            wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)
        );

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $body    = self::build_html($vouchers, $order);

        wp_mail($to, $subject, $body, $headers);
    }

    public static function build_html(array $vouchers, WC_Order $order): string
    {
        $site = esc_html(get_bloginfo('name'));
        $name = esc_html($order->get_billing_first_name());

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head><meta charset="UTF-8"><title><?php echo $site; ?></title></head>
        <body style="font-family:Arial,sans-serif;background:#f5f6f8;padding:24px;color:#1a1a1a">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden">
                <tr>
                    <td style="background:#0f3d5e;color:#fff;padding:20px 24px;font-size:22px;font-weight:700">
                        <?php echo $site; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px">
                        <p><?php printf(esc_html__('Hi %s,', 'angebot-deals'), $name); ?></p>
                        <p><?php esc_html_e('Thank you for your purchase! Here are your vouchers:', 'angebot-deals'); ?></p>
                        <?php foreach ($vouchers as $voucher) :
                            $deal_title = get_the_title((int) $voucher->deal_id);
                            $qr_url     = Angebot_Deals_QR_Code::image_url((string) $voucher->qr_token);
                            ?>
                            <div style="border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:16px 0">
                                <h3 style="margin:0 0 8px"><?php echo esc_html($deal_title); ?></h3>
                                <p style="margin:0 0 8px;font-size:20px;letter-spacing:2px;font-weight:700"><?php echo esc_html($voucher->code); ?></p>
                                <?php if ($voucher->expires_at) : ?>
                                    <p style="margin:0 0 12px;color:#666;font-size:13px">
                                        <?php printf(esc_html__('Valid until: %s', 'angebot-deals'), esc_html(date_i18n('d.m.Y', strtotime($voucher->expires_at)))); ?>
                                    </p>
                                <?php endif; ?>
                                <img src="<?php echo esc_url($qr_url); ?>" alt="QR" width="160" height="160" style="display:block">
                                <p style="margin:8px 0 0;font-size:12px;color:#888">
                                    <?php esc_html_e('Show this QR code or the text code to the merchant.', 'angebot-deals'); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                        <p style="font-size:13px;color:#666">
                            <?php printf(esc_html__('Order number: #%s', 'angebot-deals'), esc_html((string) $order->get_order_number())); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <?php
        return (string) ob_get_clean();
    }

    public static function append_to_order_email(WC_Order $order, bool $sent_to_admin, bool $plain_text, $email): void
    {
        if ($sent_to_admin) {
            return;
        }

        $vouchers = Angebot_Deals_Voucher::get_for_order($order->get_id());
        if (!$vouchers) {
            return;
        }

        if ($plain_text) {
            echo "\n" . __('Your vouchers:', 'angebot-deals') . "\n";
            foreach ($vouchers as $v) {
                echo '- ' . $v->code . ' (' . get_the_title((int) $v->deal_id) . ")\n";
            }
            return;
        }

        echo '<h2>' . esc_html__('Your vouchers', 'angebot-deals') . '</h2>';
        echo '<ul>';
        foreach ($vouchers as $v) {
            printf(
                '<li><strong>%s</strong> — %s</li>',
                esc_html($v->code),
                esc_html(get_the_title((int) $v->deal_id))
            );
        }
        echo '</ul>';
    }
}
