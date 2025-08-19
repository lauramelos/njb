<?php

/**
 * Class NJB_business
 *
 * Handles the registration and management of the Business post type.
 *
 * @since 1.0.0
 * @subpackage NJB_Customizations
 * @author NJB_Customizations Team
 * @see https://developer.wordpress.org/reference/


 *
 * @package NJB_Customizations
 */
if ( ! defined( 'ABSPATH' ) )   { 
    exit; // Exit if accessed directly.
}
class NJB_business {

    /**
     * Class constructor.
     */
    public function __construct() {
        require_once plugin_dir_path( __FILE__ ) . 'post-types/business.php';
        add_action( 'init', array( __CLASS__, 'init' ) );
        add_filter( 'query_vars', array( __CLASS__ , 'custom_query_vars' ), 0 );
        add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'my_account_menu_items' ) );
        add_action( 'woocommerce_account_business_endpoint', array( __CLASS__, 'endpoint_content' ) );
        add_filter( 'acf/prepare_field/name=approved', array( __CLASS__, 'hide_approved' ) );
        add_action( 'wps_sfw_order_status_changed', array( __CLASS__, 'clear_transients' ) );
        add_action( 'acf/save_post', array( __CLASS__ , 'save_taxonomies' ), 20);

        add_filter( 'acf/update_value/name=business_name', array( __CLASS__ , 'update_title' ), 10, 4 );

    }


    /**
     * Initializes the Business class.
     */
    public static function init() {
        self::custom_endpoints();
    }

    /**
     * Registers the Business endpoint.
     */
    static public function custom_endpoints() {
        add_rewrite_endpoint( 'business', EP_ROOT | EP_PAGES );
    }

    /**
     * Registers the Business query vars.
     */
    static public function custom_query_vars( $vars ) {
        $vars[] = 'business';
        return $vars;
    }

    /**
     * Add business menu itmem to the WooCommerce account menu.
     * Rmove downlods tab.
     */
    static public function my_account_menu_items( $items ) {

        unset( $items['downloads'] ); // Remove Downloads tab

       if( ! NJB_business::wps_sfw_user_has_active_subscription() ) {
            return $items; // No active subscription, do not show the Business tab
        }

        $items = array (
            'dashboard'         => __('Dashboard', 'woocommerce'),
            'orders'            => __('Orders', 'woocommerce'),
            'edit-address'      => __('Addresses', 'woocommerce'),
            'business'          => __('Business', 'njb-customizations'),
            'payment-methods'   => __('Payment methods', 'woocommerce'),
            'edit-account'      => __('Account details', 'woocommerce'),
            'wps_subscriptions' => __('Subscriptions', 'woocommerce'),
            'customer-logout'   => __('Log out', 'woocommerce'),
        );  

        return $items;
    }

    /**
     * Content for the Business endpoint.
     *
     * This function displays the ACF form for the Business post type.
     * It also ensures that the user ID is set correctly in the form.
     *
     * @since 1.0.0
     */
    static public function endpoint_content() {

        // Enqueue necessary scripts for the address fields.
        wp_enqueue_script( 'wc-address-i18n' );

        $user_id = get_current_user_id();
        $post_id = self::get_or_create_user_cpt( $user_id );

        if ( function_exists('acf_form') ) {
            acf_form_head();
            acf_form( array(
                'post_id'            => $post_id,
                'post_title'         => false,
                'post_content'       => false,
                'submit_value'       => __( 'Submit', 'njb-customization' ),
                'html_submit_button' => '<input type="submit" class="wp-block-button wp-element-button" value="%s" />',
                'updated_message'    => __( 'Business saved',  'njb-customization' ),
            ));
        }
    }

    /**
     * Hides the 'approved' field on the frontend.
     */
    static public function hide_approved( $field ) {
        if ( is_admin() ) {
            return $field; // Only modify on the frontend
        }
      
        return false;
    }

    /**
     * Gets or creates a Business post for the current user.
     */
    public static function get_or_create_user_cpt( $user_id ) {
        $args = array(
            'post_type'      => 'business',
            'author'         => $user_id,
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        );

        $posts = get_posts( $args );

        if ( ! empty( $posts ) ) {
            return $posts[0];
        }

        $new_post_id = wp_insert_post( array(
            'post_type'   => 'business',
            'post_status' => 'draft',
            'post_author' => $user_id,
            'post_title'  => 'Draft business for ' . $user_id
        ));

        return $new_post_id;
    }

    /**
     * Checks if the user has an active subscription.
     *
     * @param int $user_id The user ID to check.
     * @return bool True if the user has an active subscription, false otherwise.
     */
    static public function wps_sfw_user_has_active_subscription( $user_id = null ) {
        if ( null == $user_id ) {
            $user_id = get_current_user_id();
        }
        if ( ! $user_id ) {
            return false; // No user logged in
        }
        // get value from transient
        $active_subscription = get_transient( 'wps_sfw_active_subscription_' . $user_id );
        if ( false !== $active_subscription ) {
            return $active_subscription;
        }
        $args = array(
                    'type'   => 'wps_subscriptions',
                    'return' => 'ids',
                    'number' => 20,
                    'meta_query' => array(
                        array(
                            'key'   => 'wps_customer_id',
                            'value' => $user_id,
                        ),
                    ),
                );
        $wps_subscriptions = wc_get_orders( $args );

        foreach ( $wps_subscriptions as $subscription_id ) {
            $status = wps_sfw_get_meta_data( $subscription_id, 'wps_subscription_status', true );
            if ( 'active' === $status ) {
                // Set transient for 12 hours
                set_transient( 'wps_sfw_active_subscription_' . $user_id, true, 12 * HOUR_IN_SECONDS );
                return true;
            }
        }
        // Set transient for 12 hours
        set_transient( 'wps_sfw_active_subscription_' . $user_id, false, 12 * HOUR_IN_SECONDS );
        return false;
    }

    /**
     * Clear transients when an order status changes.
     *
     * @param int $order_id The order ID.
     */
    public static function clear_transients( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        // Clear the transient for the user associated with the order
        $user_id = $order->get_user_id();
        if ( $user_id ) {
            delete_transient( 'wps_sfw_active_subscription_' . $user_id );
        }
    }

    /**
     * Save taxonomies when the Business post is saved.
     *
     * @param int $post_id The post ID.
     */
    public static function save_taxonomies( $post_id ) {
        // Check if the post type is 'business'
        if ( get_post_type( $post_id ) !== 'business' ) {
            return;
        }

        if ( empty($_POST['acf']) ) {
            return;
        }

        $all_countries = WC()->countries->get_allowed_countries();
        // Define the ACF field keys for the taxonomies
        $term_array = array(
            'business_location_country' => isset( $_POST['acf']['field_689dc076d6518']['field_689e068b52813'] ) ? $all_countries[ sanitize_text_field( $_POST['acf']['field_689dc076d6518']['field_689e068b52813'] ) ] : '',
            'business_location_state'   => isset( $_POST['acf']['field_689dc076d6518']['field_689e069352814'] ) ? sanitize_text_field( $_POST['acf']['field_689dc076d6518']['field_689e069352814'] ) : '',
            'business_sector'           => isset( $_POST['acf']['field_689dc057d6517'] ) ? sanitize_text_field( $_POST['acf']['field_689dc057d6517']) : '',
        );
        foreach ( $term_array as $taxonomy => $term_name ) {
            if ( ! empty( $term_name ) ) {
                // Get the term ID from the submitted value
                $term_id = self::get_term_id_by_name( $term_name, $taxonomy );
                if ( is_wp_error( $term_id ) || ! $term_id ) {
                    continue; // Skip if there was an error or term ID is not valid
                }
                // Set the term for the post
                wp_set_object_terms( (int) $post_id, (int) $term_id, $taxonomy, false );
            }
        }
    }

    /**
     * Get object term ID by name, or create it if it doesn't exist.
     */
    public static function get_term_id_by_name( $name, $taxonomy ) {
        $term = get_term_by( 'name', $name, $taxonomy );
        if ( ! $term ) {
            // If the term doesn't exist, create it
            $term = wp_insert_term( $name, $taxonomy );
            if ( is_wp_error( $term ) ) {
                return 0; // Return 0 if there was an error creating the term
            }
            return $term['term_id']; // Return the newly created term ID
        }
        return $term->term_id; // Return the term ID
    }

     /**
     * Get object term ID by name, or create it if it doesn't exist.
     */
    public static function update_title( $value, $post_id, $field, $original ) {
        if ( is_string( $value ) ) {
            // change post title to the value of the ACF field
            $post = get_post( $post_id );
            if ( $post && $post->post_type === 'business' && $post->post_title !== $value ) {
                $post->post_title = $value;
                wp_update_post( $post );
            }
        }
        return $value;
    }
}

$NJB_business = new NJB_business();