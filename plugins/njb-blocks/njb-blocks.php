<?php
/**
 * Plugin Name:       Njb Blocks
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       njb-blocks
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_njb_blocks_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_njb_blocks_block_init' );

add_action('rest_api_init', function () {
    register_rest_route('njb/v1', '/listing', [
        'methods' => 'GET',
        'callback' => 'njb_listing_api',
    ]);
});

function njb_listing_api(WP_REST_Request $request) {
	$search = $request['s'] ?? '';
    $args = [
        'post_type' => 'business',
        'posts_per_page' => 10,
        's' => $search,
    ];
    if ( ! empty( $request['business_location_country'] ) ) {
        $args['tax_query'][] = [[
            'taxonomy' => 'business_location_country',
            'field' => 'slug',
            'terms' => sanitize_text_field( $request['business_location_country']),
        ]];
    }
	if ( ! empty( $request['business_location_state'] ) ) {
        $args['tax_query'][] = [[
            'taxonomy' => 'business_location_state',
            'field' => 'slug',
            'terms' => sanitize_text_field( $request['business_location_state']),
        ]];
    }
	if ( ! empty( $request['business_sector'] ) ) {
        $args['tax_query'][] = [[
            'taxonomy' => 'business_sector',
            'field' => 'slug',
            'terms' => sanitize_text_field( $request['business_sector']),
        ]];
    }

	  // Extiende la búsqueda a taxonomías si hay término de búsqueda
    if ( $search ) {
        add_filter('posts_search', function($search_sql, $wp_query) use ($search) {
            global $wpdb;
            if ( ! $search ) return $search_sql;

            // Taxonomías a buscar
            $taxonomies = ['business_sector', 'business_location_country', 'business_location_state'];
            $terms_sql = [];
            foreach ( $taxonomies as $tax ) {
                $terms_sql[] = $wpdb->prepare(
                    "ID IN (
                        SELECT object_id FROM {$wpdb->term_relationships}
                        WHERE term_taxonomy_id IN (
                            SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
                            WHERE taxonomy = %s AND term_id IN (
                                SELECT term_id FROM {$wpdb->terms}
                                WHERE name LIKE %s
                            )
                        )
                    )",
                    $tax, '%' . $wpdb->esc_like($search) . '%'
                );
            }
            // Une la búsqueda original con la de taxonomías
            $search_sql .= ' OR (' . implode(' OR ', $terms_sql) . ')';
            return $search_sql;
        }, 10, 2);
    }

    $query = new WP_Query($args);
    $posts = [];
    while ( $query->have_posts() ) {
        $query->the_post();
		if ( ! get_field('approved') ) continue;
        $address_group = get_field('address');
        $street_address = '';
        $address = [];
        $street_address1 = $address_group['street_address'];
        if ( ! empty ( $street_address1 ) ) {
            $street_address .= $street_address1;
        }
        $city = $address_group['city'];
        if ( ! empty ( $city ) ) {
            if( ! empty( $street_address ) ) {
                $street_address .= ' ';
            }
            $street_address .= $city;
        }
        $postal_code = $address_group['postal_code'];
        if ( ! empty( $postal_code ) ) {
            if( ! empty( $street_address ) ) {
                $street_address .= ' ';
            }
            $street_address .=  $postal_code;
        }
        $address[] = $street_address;
        $state         = get_the_terms( get_the_ID(), 'business_location_state' );
        $country       = get_the_terms( get_the_ID(), 'business_location_country' );
        // map state and country into string of names
        if ( ! empty ( $state ) && !is_wp_error( $state ) ) 
            $address[] = implode(', ', array_map(function($s){ return $s->name; }, $state ) );
        if ( ! empty ( $country ) && !is_wp_error( $country ) )
            $address[] = implode(', ', array_map(function($c){ return $c->name; }, $country ) );
		
        $posts[] = [
            'title' => get_the_title(),
            'website' => get_permalink(),
            'phone_number' => get_field('phone_number'),
            'email' => get_field('email'),
            'logo'  => wp_get_attachment_image( get_field('small_business_logoicon'), 'medium' ),
			'address' => $address ?? [],
            'sectors' => get_the_terms( get_the_ID(), 'business_sector' ),
			'country' => get_the_terms( get_the_ID(), 'business_location_country' ),
			'state'   => get_the_terms( get_the_ID(), 'business_location_state' ),
			'excerpt' => get_the_excerpt(),
			'content' => apply_filters('the_content', get_the_content()),
        ];
    }
    wp_reset_postdata();
    return $posts;
}
