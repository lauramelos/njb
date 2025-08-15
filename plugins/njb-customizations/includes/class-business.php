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
        add_filter( 'acf/prepare_field/name=user_id', array( __CLASS__, 'set_user_id' ));
        add_filter( 'acf/prepare_field/name=approved', array( __CLASS__, 'hide_approved' ) );
        add_action( 'wps_sfw_order_status_changed', array( __CLASS__, 'clear_transients' ) );
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
     * Sets the user ID field to the current user's ID.
     *
     * @param array $field The ACF field array.
     * @return array Modified field array.
     */
    static public function set_user_id( $field ) {
        if ( is_admin() ) {
            return $field; // Only modify on the frontend
        }
        $field['class'] = 'hidden';
        $field['type'] = 'hidden';
        $field['default_value'] = get_current_user_id();
        return $field;
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
            'post_title'  => 'Datos de usuario ' . $user_id
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
}

$NJB_business = new NJB_business();