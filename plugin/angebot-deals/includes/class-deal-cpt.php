<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Angebot_Deals_Deal_CPT
{
    public const POST_TYPE = 'deal';
    public const TAX_CATEGORY = 'deal_category';
    public const TAX_LOCATION = 'deal_location';

    public static function register_hooks(): void
    {
        add_action('init', [self::class, 'register']);
    }

    public static function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name'               => __('Deals', 'angebot-deals'),
                'singular_name'      => __('Deal', 'angebot-deals'),
                'add_new'            => __('Neuer Deal', 'angebot-deals'),
                'add_new_item'       => __('Neuen Deal hinzufügen', 'angebot-deals'),
                'edit_item'          => __('Deal bearbeiten', 'angebot-deals'),
                'new_item'           => __('Neuer Deal', 'angebot-deals'),
                'view_item'          => __('Deal ansehen', 'angebot-deals'),
                'search_items'       => __('Deals suchen', 'angebot-deals'),
                'not_found'          => __('Keine Deals gefunden', 'angebot-deals'),
                'not_found_in_trash' => __('Keine Deals im Papierkorb', 'angebot-deals'),
                'menu_name'          => __('Deals', 'angebot-deals'),
            ],
            'public'             => true,
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'deals', 'with_front' => false],
            'menu_icon'          => 'dashicons-tag',
            'menu_position'      => 5,
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'author'],
            'show_in_rest'       => true,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'publicly_queryable' => true,
        ]);

        register_taxonomy(self::TAX_CATEGORY, self::POST_TYPE, [
            'labels' => [
                'name'          => __('Deal-Kategorien', 'angebot-deals'),
                'singular_name' => __('Deal-Kategorie', 'angebot-deals'),
                'search_items'  => __('Kategorien suchen', 'angebot-deals'),
                'all_items'     => __('Alle Kategorien', 'angebot-deals'),
                'edit_item'     => __('Kategorie bearbeiten', 'angebot-deals'),
                'add_new_item'  => __('Neue Kategorie', 'angebot-deals'),
            ],
            'public'            => true,
            'hierarchical'      => true,
            'rewrite'           => ['slug' => 'kategorie'],
            'show_admin_column' => true,
            'show_in_rest'      => true,
        ]);

        register_taxonomy(self::TAX_LOCATION, self::POST_TYPE, [
            'labels' => [
                'name'          => __('Standorte', 'angebot-deals'),
                'singular_name' => __('Standort', 'angebot-deals'),
                'search_items'  => __('Standorte suchen', 'angebot-deals'),
                'all_items'     => __('Alle Standorte', 'angebot-deals'),
                'edit_item'     => __('Standort bearbeiten', 'angebot-deals'),
                'add_new_item'  => __('Neuer Standort', 'angebot-deals'),
            ],
            'public'            => true,
            'hierarchical'      => true,
            'rewrite'           => ['slug' => 'stadt'],
            'show_admin_column' => true,
            'show_in_rest'      => true,
        ]);
    }
}
