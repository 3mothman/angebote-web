<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Deactivator
{
    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}
