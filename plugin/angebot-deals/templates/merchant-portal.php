<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="angebot-merchant-portal">
    <h2><?php esc_html_e('Gutschein prüfen & einlösen', 'angebot-deals'); ?></h2>
    <form id="angebot-merchant-lookup" class="angebot-merchant-lookup">
        <label>
            <span><?php esc_html_e('Gutschein-Code', 'angebot-deals'); ?></span>
            <input type="text" name="code" placeholder="XXXX-XXXX-XXXX" required autocomplete="off">
        </label>
        <button type="submit" class="angebot-btn angebot-btn--primary"><?php esc_html_e('Prüfen', 'angebot-deals'); ?></button>
    </form>
    <div id="angebot-merchant-result" class="angebot-merchant-result" hidden></div>
</div>
