<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e('Deal-Bewertungen', 'angebot-deals'); ?></h1>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Deal', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Autor', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Sterne', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Inhalt', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Status', 'angebot-deals'); ?></th>
                <th><?php esc_html_e('Aktionen', 'angebot-deals'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$reviews) : ?>
                <tr><td colspan="6"><?php esc_html_e('Keine Bewertungen.', 'angebot-deals'); ?></td></tr>
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
                                <?php esc_html_e('Freigeben', 'angebot-deals'); ?>
                            </a> |
                        <?php endif; ?>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=deal&page=angebot-reviews&trash=' . (int) $r->id . '&_wpnonce=' . $nonce)); ?>" style="color:#b32d2e">
                            <?php esc_html_e('Löschen', 'angebot-deals'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
