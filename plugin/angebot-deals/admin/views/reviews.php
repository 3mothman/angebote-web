<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e('Deal reviews', 'angebot-deals'); ?></h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Deal', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Author', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Stars', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Content', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Status', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Actions', 'angebot-deals'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$reviews) : ?>
                <tr><td colspan="6"><?php esc_html_e('No reviews.', 'angebot-deals'); ?></td></tr>
            <?php else : foreach ($reviews as $r) :
                $nonce = wp_create_nonce('angebot_review');
                ?>
                <tr>
                    <td><?php echo esc_html(get_the_title((int) $r->deal_id)); ?></td>
                    <td><?php echo esc_html($r->author_name); ?></td>
                    <td><?php echo esc_html((string) $r->rating); ?></td>
                    <td><?php echo esc_html(wp_trim_words($r->content, 20)); ?></td>
                    <td><?php echo esc_html($r->status); ?></td>
                    <td>
                        <?php if ($r->status !== 'approved') : ?>
                            <a href="<?php echo esc_url(admin_url('edit.php?post_type=deal&page=angebot-reviews&approve=' . (int) $r->id . '&_wpnonce=' . $nonce)); ?>">
                                <?php esc_html_e('Approve', 'angebot-deals'); ?>
                            </a> |
                        <?php endif; ?>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=deal&page=angebot-reviews&trash=' . (int) $r->id . '&_wpnonce=' . $nonce)); ?>" style="color:#b32d2e">
                            <?php esc_html_e('Delete', 'angebot-deals'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
