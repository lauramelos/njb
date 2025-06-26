<?php

/**
 * Registers the `donations` post type.
 */
function donations_init() {
	register_post_type(
		'donations',
		[
			'labels'                => [
				'name'                  => __( 'Donations', 'njb-customizations' ),
				'singular_name'         => __( 'Donation', 'njb-customizations' ),
				'all_items'             => __( 'All Donations', 'njb-customizations' ),
				'archives'              => __( 'Donation Archives', 'njb-customizations' ),
				'attributes'            => __( 'Donation Attributes', 'njb-customizations' ),
				'insert_into_item'      => __( 'Insert into Donation', 'njb-customizations' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Donation', 'njb-customizations' ),
				'featured_image'        => _x( 'Featured Image', 'donations', 'njb-customizations' ),
				'set_featured_image'    => _x( 'Set featured image', 'donations', 'njb-customizations' ),
				'remove_featured_image' => _x( 'Remove featured image', 'donations', 'njb-customizations' ),
				'use_featured_image'    => _x( 'Use as featured image', 'donations', 'njb-customizations' ),
				'filter_items_list'     => __( 'Filter Donations list', 'njb-customizations' ),
				'items_list_navigation' => __( 'Donations list navigation', 'njb-customizations' ),
				'items_list'            => __( 'Donations list', 'njb-customizations' ),
				'new_item'              => __( 'New Donation', 'njb-customizations' ),
				'add_new'               => __( 'Add New', 'njb-customizations' ),
				'add_new_item'          => __( 'Add New Donation', 'njb-customizations' ),
				'edit_item'             => __( 'Edit Donation', 'njb-customizations' ),
				'view_item'             => __( 'View Donation', 'njb-customizations' ),
				'view_items'            => __( 'View Donations', 'njb-customizations' ),
				'search_items'          => __( 'Search Donations', 'njb-customizations' ),
				'not_found'             => __( 'No Donations found', 'njb-customizations' ),
				'not_found_in_trash'    => __( 'No Donations found in trash', 'njb-customizations' ),
				'parent_item_colon'     => __( 'Parent Donation:', 'njb-customizations' ),
				'menu_name'             => __( 'Donations', 'njb-customizations' ),
			],
			'public'                => true,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => [ 'title', 'editor', 'thumbnail' ],
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-admin-post',
			'show_in_rest'          => true,
			'rest_base'             => 'donations',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		]
	);

}

add_action( 'init', 'donations_init' );

/**
 * Sets the post updated messages for the `donations` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `donations` post type.
 */
function donations_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['donations'] = [
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Donation updated. <a target="_blank" href="%s">View Donation</a>', 'njb-customizations' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'njb-customizations' ),
		3  => __( 'Custom field deleted.', 'njb-customizations' ),
		4  => __( 'Donation updated.', 'njb-customizations' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Donation restored to revision from %s', 'njb-customizations' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Donation published. <a href="%s">View Donation</a>', 'njb-customizations' ), esc_url( $permalink ) ),
		7  => __( 'Donation saved.', 'njb-customizations' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Donation submitted. <a target="_blank" href="%s">Preview Donation</a>', 'njb-customizations' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Donation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Donation</a>', 'njb-customizations' ), date_i18n( __( 'M j, Y @ G:i', 'njb-customizations' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Donation draft updated. <a target="_blank" href="%s">Preview Donation</a>', 'njb-customizations' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	];

	return $messages;
}

add_filter( 'post_updated_messages', 'donations_updated_messages' );

/**
 * Sets the bulk post updated messages for the `donations` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `donations` post type.
 */
function donations_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages['donations'] = [
		/* translators: %s: Number of Donations. */
		'updated'   => _n( '%s Donation updated.', '%s Donations updated.', $bulk_counts['updated'], 'njb-customizations' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Donation not updated, somebody is editing it.', 'njb-customizations' ) :
						/* translators: %s: Number of Donations. */
						_n( '%s Donation not updated, somebody is editing it.', '%s Donations not updated, somebody is editing them.', $bulk_counts['locked'], 'njb-customizations' ),
		/* translators: %s: Number of Donations. */
		'deleted'   => _n( '%s Donation permanently deleted.', '%s Donations permanently deleted.', $bulk_counts['deleted'], 'njb-customizations' ),
		/* translators: %s: Number of Donations. */
		'trashed'   => _n( '%s Donation moved to the Trash.', '%s Donations moved to the Trash.', $bulk_counts['trashed'], 'njb-customizations' ),
		/* translators: %s: Number of Donations. */
		'untrashed' => _n( '%s Donation restored from the Trash.', '%s Donations restored from the Trash.', $bulk_counts['untrashed'], 'njb-customizations' ),
	];

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', 'donations_bulk_updated_messages', 10, 2 );

function replace_title_placeholder( $title ){
	$screen = get_current_screen();
	if  ( 'donations' == $screen->post_type ) {
		 $title = 'Name of organization donated to';
	}
	return $title;
}
add_filter( 'enter_title_here', 'replace_title_placeholder' );
