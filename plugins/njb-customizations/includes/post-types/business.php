<?php

/**
 * Registers the `Business` post type.
 */


class NJB_Business_Post_Type {

    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type_and_taxonomies' ] );
        add_filter( 'post_updated_messages', [ __CLASS__, 'updated_messages' ] );
        add_filter( 'bulk_post_updated_messages', [ __CLASS__, 'bulk_updated_messages' ], 10, 2 );
        add_filter( 'enter_title_here', [ __CLASS__, 'replace_title_placeholder' ] );
    }

    public static function register_post_type_and_taxonomies() {
        register_post_type(
            'business',
            [
                'labels'                => [
                    'name'                  => __( 'Business', 'njb-customizations' ),
                    'singular_name'         => __( 'Business', 'njb-customizations' ),
                    'all_items'             => __( 'All Business', 'njb-customizations' ),
                    'archives'              => __( 'Business Archives', 'njb-customizations' ),
                    'attributes'            => __( 'Business Attributes', 'njb-customizations' ),
                    'insert_into_item'      => __( 'Insert into Business', 'njb-customizations' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Business', 'njb-customizations' ),
                    'featured_image'        => _x( 'Featured Image', 'Business', 'njb-customizations' ),
                    'set_featured_image'    => _x( 'Set featured image', 'Business', 'njb-customizations' ),
                    'remove_featured_image' => _x( 'Remove featured image', 'Business', 'njb-customizations' ),
                    'use_featured_image'    => _x( 'Use as featured image', 'Business', 'njb-customizations' ),
                    'filter_items_list'     => __( 'Filter Business list', 'njb-customizations' ),
                    'items_list_navigation' => __( 'Business list navigation', 'njb-customizations' ),
                    'items_list'            => __( 'Business list', 'njb-customizations' ),
                    'new_item'              => __( 'New Business', 'njb-customizations' ),
                    'add_new'               => __( 'Add New', 'njb-customizations' ),
                    'add_new_item'          => __( 'Add New Business', 'njb-customizations' ),
                    'edit_item'             => __( 'Edit Business', 'njb-customizations' ),
                    'view_item'             => __( 'View Business', 'njb-customizations' ),
                    'view_items'            => __( 'View Business', 'njb-customizations' ),
                    'search_items'          => __( 'Search Business', 'njb-customizations' ),
                    'not_found'             => __( 'No Business found', 'njb-customizations' ),
                    'not_found_in_trash'    => __( 'No Business found in trash', 'njb-customizations' ),
                    'parent_item_colon'     => __( 'Parent Busines:', 'njb-customizations' ),
                    'menu_name'             => __( 'Business', 'njb-customizations' ),
                ],
                'public'                => true,
                'hierarchical'          => false,
                'show_ui'               => true,
                'show_in_nav_menus'     => true,
                'supports'              => [ 'title', 'editor', 'thumbnail', 'author' ],
                'has_archive'           => true,
                'rewrite'               => true,
                'query_var'             => true,
                'menu_position'         => null,
                'menu_icon'             => 'dashicons-admin-post',
                'show_in_rest'          => true,
                'rest_base'             => 'business',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
            ]
        );

        register_taxonomy(
            'business_sector',
            'business',
            [
                'label'        => __( 'Sector/Industry', 'njb-customizations' ),
                'public'       => true,
                'hierarchical' => true,
                'show_ui'      => true,
                'show_in_rest' => true,
                'rewrite'      => [ 'slug' => 'business-sector' ],
            ]
        );

        register_taxonomy(
            'business_location_country',
            'business',
            [
                'label'        => __( 'Location Country', 'njb-customizations' ),
                'public'       => true,
                'hierarchical' => true,
                'show_ui'      => true,
                'show_in_rest' => true,
                'rewrite'      => [ 'slug' => 'business-location-country' ],
            ]
        );
		
		register_taxonomy(
            'business_location_state',
            'business',
            [
                'label'        => __( 'Location State', 'njb-customizations' ),
                'public'       => true,
                'hierarchical' => true,
                'show_ui'      => true,
                'show_in_rest' => true,
                'rewrite'      => [ 'slug' => 'business-location-state' ],
            ]
        );
    }

    public static function updated_messages( $messages ) {
        global $post;

        $permalink = get_permalink( $post );

        $messages['Business'] = [
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf( __( 'Business updated. <a target="_blank" href="%s">View Busines</a>', 'njb-customizations' ), esc_url( $permalink ) ),
            2  => __( 'Custom field updated.', 'njb-customizations' ),
            3  => __( 'Custom field deleted.', 'njb-customizations' ),
            4  => __( 'Business updated.', 'njb-customizations' ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Business restored to revision from %s', 'njb-customizations' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf( __( 'Business published. <a href="%s">View Busines</a>', 'njb-customizations' ), esc_url( $permalink ) ),
            7  => __( 'Business saved.', 'njb-customizations' ),
            8  => sprintf( __( 'Business submitted. <a target="_blank" href="%s">Preview Busines</a>', 'njb-customizations' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
            9  => sprintf( __( 'Business scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Busines</a>', 'njb-customizations' ), date_i18n( __( 'M j, Y @ G:i', 'njb-customizations' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
            10 => sprintf( __( 'Business draft updated. <a target="_blank" href="%s">Preview Busines</a>', 'njb-customizations' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
        ];

        return $messages;
    }

    public static function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
        $bulk_messages['Business'] = [
            'updated'   => _n( '%s Business updated.', '%s Business updated.', $bulk_counts['updated'], 'njb-customizations' ),
            'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Business not updated, somebody is editing it.', 'njb-customizations' ) :
                            _n( '%s Business not updated, somebody is editing it.', '%s Business not updated, somebody is editing them.', $bulk_counts['locked'], 'njb-customizations' ),
            'deleted'   => _n( '%s Business permanently deleted.', '%s Business permanently deleted.', $bulk_counts['deleted'], 'njb-customizations' ),
            'trashed'   => _n( '%s Business moved to the Trash.', '%s Business moved to the Trash.', $bulk_counts['trashed'], 'njb-customizations' ),
            'untrashed' => _n( '%s Business restored from the Trash.', '%s Business restored from the Trash.', $bulk_counts['untrashed'], 'njb-customizations' ),
        ];

        return $bulk_messages;
    }

    public static function replace_title_placeholder( $title ) {
        $screen = get_current_screen();
        if ( 'Business' == $screen->post_type ) {
            $title = 'Name of the Business';
        }
        return $title;
    }
}

NJB_Business_Post_Type::init();