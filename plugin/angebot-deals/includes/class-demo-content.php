<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Optional demo deals — WP-CLI: wp eval-file wp-content/plugins/angebot-deals/includes/class-demo-content.php
 * Or trigger via Admin → Deals → Einstellungen (button).
 */
final class Angebot_Deals_Demo_Content
{
    public static function seed(): array
    {
        if (!taxonomy_exists(Angebot_Deals_Deal_CPT::TAX_LOCATION)) {
            Angebot_Deals_Deal_CPT::register();
        }
        Angebot_Deals_Location::seed_default_locations();

        $samples = [
            [
                'title'    => '3-Gänge-Menü für 2 Personen',
                'merchant' => 'Bistro Merseburg',
                'location' => 'merseburg',
                'category' => 'essen-trinken',
                'original' => 79.00,
                'deal'     => 39.00,
                'qty'      => 50,
                'featured' => 1,
                'excerpt'  => 'Candle-Light-Dinner inkl. Begrüßungsgetränk.',
                'content'  => '<p>Genießt ein 3-Gänge-Menü nach Wahl der Saisonkarte. Ideal für Paare und besondere Anlässe.</p>',
            ],
            [
                'title'    => '60 Min. Classic Massage',
                'merchant' => 'Spa Halle',
                'location' => 'halle',
                'category' => 'beauty-wellness',
                'original' => 65.00,
                'deal'     => 29.90,
                'qty'      => 40,
                'featured' => 1,
                'excerpt'  => 'Entspannung vom Nacken bis zu den Füßen.',
                'content'  => '<p>Professionelle Ganzkörpermassage mit hochwertigen Ölen. Terminvereinbarung nach Kauf.</p>',
            ],
            [
                'title'    => 'Indoor-Trampolin 2 Std.',
                'merchant' => 'Jump Arena Leipzig',
                'location' => 'leipzig',
                'category' => 'freizeit',
                'original' => 28.00,
                'deal'     => 14.00,
                'qty'      => 100,
                'featured' => 0,
                'excerpt'  => 'Hüpfen, Softplay & Foam Pit inklusive.',
                'content'  => '<p>Zwei Stunden Freizeitspaß für Kinder und Erwachsene. Sockenpflicht.</p>',
            ],
            [
                'title'    => 'Stadtführung Berlin-Mitte',
                'merchant' => 'Stadtführer Berlin',
                'location' => 'berlin',
                'category' => 'aktivitaten',
                'original' => 25.00,
                'deal'     => 12.50,
                'qty'      => 80,
                'featured' => 1,
                'excerpt'  => '2 Stunden Highlights rund um den Alex.',
                'content'  => '<p>Kleine Gruppen, deutschsprachig. Treffpunkt wird nach Kauf mitgeteilt.</p>',
            ],
        ];

        $created = [];
        foreach ($samples as $sample) {
            $existing = get_posts([
                'post_type'      => Angebot_Deals_Deal_CPT::POST_TYPE,
                'title'          => $sample['title'],
                'posts_per_page' => 1,
                'post_status'    => 'any',
                'fields'         => 'ids',
            ]);
            if ($existing) {
                continue;
            }

            $id = wp_insert_post([
                'post_type'    => Angebot_Deals_Deal_CPT::POST_TYPE,
                'post_status'  => 'publish',
                'post_title'   => $sample['title'],
                'post_content' => $sample['content'],
                'post_excerpt' => $sample['excerpt'],
            ]);

            if (is_wp_error($id) || !$id) {
                continue;
            }

            $discount = (int) round((($sample['original'] - $sample['deal']) / $sample['original']) * 100);
            update_post_meta($id, '_angebot_original_price', $sample['original']);
            update_post_meta($id, '_angebot_deal_price', $sample['deal']);
            update_post_meta($id, '_angebot_discount_percent', $discount);
            update_post_meta($id, '_angebot_merchant_name', $sample['merchant']);
            update_post_meta($id, '_angebot_location_label', ucfirst($sample['location']));
            update_post_meta($id, '_angebot_quantity', $sample['qty']);
            update_post_meta($id, '_angebot_sold_count', 0);
            update_post_meta($id, '_angebot_is_featured', $sample['featured']);
            update_post_meta($id, '_angebot_voucher_expires', gmdate('Y-m-d', strtotime('+90 days')));
            update_post_meta($id, '_angebot_highlights', "Sofort per E-Mail\nVor Ort einlösbar\nQR-Code inklusive");
            update_post_meta($id, '_angebot_fine_print', "Nicht mit anderen Rabatten kombinierbar. Terminvereinbarung erforderlich. Keine Barauszahlung.");

            $loc = get_term_by('slug', $sample['location'], Angebot_Deals_Deal_CPT::TAX_LOCATION);
            if ($loc) {
                wp_set_object_terms($id, [(int) $loc->term_id], Angebot_Deals_Deal_CPT::TAX_LOCATION);
            }

            $cat = get_term_by('slug', $sample['category'], Angebot_Deals_Deal_CPT::TAX_CATEGORY);
            if (!$cat) {
                // Fallback: first matching name fragment.
                $terms = get_terms(['taxonomy' => Angebot_Deals_Deal_CPT::TAX_CATEGORY, 'hide_empty' => false]);
                if (!is_wp_error($terms) && $terms) {
                    wp_set_object_terms($id, [(int) $terms[0]->term_id], Angebot_Deals_Deal_CPT::TAX_CATEGORY);
                }
            } else {
                wp_set_object_terms($id, [(int) $cat->term_id], Angebot_Deals_Deal_CPT::TAX_CATEGORY);
            }

            Angebot_Deals_WooCommerce_Sync::sync_product($id, get_post($id));
            $created[] = $id;
        }

        return $created;
    }
}
