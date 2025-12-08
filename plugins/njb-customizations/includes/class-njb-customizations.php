<?php

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;

class NJB_Customizations {
    static $group_options = array(
        'njb/book-club'           => array( 'label' => 'Book Club',             'whatsapp' => 'LTr2ryN0Lf2BN5ARpA99qf' ),
        'njb/business-start-up'   => array( 'label' => 'Business Start Up',     'whatsapp' => 'EVK3OxGl9BMK2Wb5zyGMAi' ),
        'njb/community-outreach'  => array( 'label' =>'Community Outreach',     'whatsapp' => 'Fc793VxTBehLNwvjkfqFsq' ),
        'njb/culinary-interests'  => array( 'label' =>'Culinary Interests',     'whatsapp' => 'Gaf9QlV6nZ3Is4BqcrXR7h' ),
        'njb/fashion-lifestyle'   => array( 'label' =>'Fashion & Lifestyle',    'whatsapp' => 'LofawNEG3SVE8HvHqGvc9S' ),
        'njb/fitness-exercise'    => array( 'label' =>'Fitness & Exercise',     'whatsapp' => 'IWtXszLXxY42Yk7r5Wu7Go' ),
        'njb/travels-art-culture' => array( 'label' =>'Travels, Art & Culture', 'whatsapp' => 'JmP1LklPDlG0iQHJhHjyI4' ),
        'njb/young-adults'        => array( 'label' =>'Young Adults',           'whatsapp' => 'B4mmTfM8eigLmEYnccWf6E' ),
    );

    /**
     * Constructor for the NJB_Customizations class.
     */
    public function __construct() {
        // Initialization code here
      }

    /**
     * Run the plugin.
     */
    public function run() {
        require_once plugin_dir_path( __FILE__ ) . 'post-types/donations.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-business.php';
        require_once plugin_dir_path( __FILE__ ) . 'acf.php';
        add_filter( 'excerpt_more', array( __CLASS__, 'custom_excerpt_more' ) );
        add_filter( 'excerpt_length', array( __CLASS__, 'custom_excerpt_length' ) );
        add_action( 'woocommerce_init', array( __CLASS__, 'add_whatsapp_number' ) );
        add_action( 'woocommerce_init', array( __CLASS__, 'add_group_options' ) );
        add_action( 'woocommerce_init', array( __CLASS__, 'add_birth_date' ) );
        add_action( 'acf/init', array( __CLASS__, 'set_acf_settings' ) );
        add_filter( 'render_block_core/query', array( __CLASS__, 'query_carousel_block' ), 10, 2 );
        add_action( 'tribe_events_single_event_after_the_content', array( __CLASS__, 'event_add_external_link' ), 10, 2 );
        add_action( 'user_register',  array( __CLASS__, 'create_user_number') );
        add_action( 'wps_sfw_subscription_order', array( __CLASS__, 'add_custom_number_to_subscription'), 10, 2 );
        add_filter( 'wps_sfw_column_subscription_table', array( __CLASS__, 'add_custom_number_to_subscription_table' ), 10);
        add_filter( 'wps_sfw_add_case_column', array( __CLASS__, 'add_custom_number_value_to_subscription_table' ), 10, 3 );
        add_filter( 'woocommerce_add_to_cart_handler', array( __CLASS__, 'add_to_cart_subscription_handler' ), 10, 2 );
        add_action( 'woocommerce_add_to_cart_handler_wps_swf_subscription_handler', array( __CLASS__, 'redirect_subscription_to_checkout') );
        add_filter( 'wps_sfw_add_to_cart_validation', array( __CLASS__, 'remove_subscription_from_cart'), 10, 3 );
        $emails = WC_Emails::instance();
        remove_action( 'woocommerce_email_customer_details', array( $emails, 'additional_checkout_fields' ), 30, 3 );
        add_action( 'woocommerce_email_customer_details', array( __CLASS__, 'additional_checkout_fields' ), 30, 3 );

        // Remove WooCommerce Blocks default additional fields rendering on order details page
        add_action( 'init', array( __CLASS__, 'remove_wc_blocks_order_fields_hook' ), 20 );

        // Add our custom additional fields rendering
        add_action( 'woocommerce_order_details_after_customer_details', array( __CLASS__, 'display_additional_fields_on_order_page' ), 10, 1 );
        add_filter( 'wc_stripe_force_save_source', array( __CLASS__, 'wps_sfw_wc_stripe_force_save_source_callback_old' ), 20 );
        // Clear subscription product IDs cache when a product is saved or deleted
        add_action( 'save_post_product', array( __CLASS__, 'clear_subscription_product_ids_cache' ) );
        add_action( 'delete_post', array( __CLASS__, 'clear_subscription_product_ids_cache' ) );
    }

    /**
     * Custom excerpt more text.
     *
     * @param string $more The default excerpt more text.
     * @return string The custom excerpt more text.
     */
    public static function custom_excerpt_more( $more ) {
        return '...';
    }
   
    /**
     * Custom excerpt length.
     *
     * @param int $more The default excerpt length.
     * @return int The custom excerpt length.
     */
    public static function custom_excerpt_length( $more ) {
        return 15;
    }

    /**
     * Add a WhatsApp number field to the WooCommerce checkout.
     *
     * @return void
     */
    public static function add_whatsapp_number() {
        woocommerce_register_additional_checkout_field(
            array(
                'id'            => 'njb/whatsapp_number',
                'type'          => 'text',
                'label'         => 'Phone Number (Optional WhatsApp)',
                'location'      => 'contact',
                'required'      => true,
                'attributes'    => array(
                    'autocomplete'     => 'whatsapp_number',
                    'aria-describedby' => 'Phone Number (Optional WhatsApp) ',
                    'aria-label'       => 'Phone Number label',
                    'pattern' => '\+?[0-9\s\-\(\)]{7,15}', // A phone number pattern allowing 7 to 15 digits with optional +, spaces, dashes, and parentheses.					'title'            => 'Title to show on hover',
                    'data-custom'      => 'custom data',
                ),
            ),
        );
    }

    public static function add_group_options() {
        // Get all subscription product IDs dynamically
        $subscription_product_ids = self::get_subscription_product_ids();
        foreach ( self::$group_options as $key => $value ) {
            woocommerce_register_additional_checkout_field(
                array(
                    'id'       => $key,
                    'label'    => $value['label'],
                    'optionalLabel' => $value['label'],
                    'location' => 'contact',
                    'type'     => 'checkbox',
                    'class'    => 'njb-group-option',
                    'hidden' => [
                        'cart' => [
                            'properties' => [
                                'items' => [
                                    'not' => [
                                        'contains' => [
                                            'enum' => $subscription_product_ids // Subscription products IDs (dynamically loaded)
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                )
            );
        }
    }

    public static function add_birth_date() {
        woocommerce_register_additional_checkout_field(
            array(
                'id'            => 'njb/birth_date',
                'type'          => 'text',
                'label'         => 'Birth Date',
                'location'      => 'contact',
                'required'      => true,
                'attributes'    => array(
                    'autocomplete'     => 'birth_date',
                    'aria-describedby' => 'Birth Date',
                    'aria-label'       => 'Birth Date label',
                ),
            ),
        );

        add_action(
        'woocommerce_validate_additional_field',
            function ( WP_Error $errors, $field_key, $field_value ) {
            if ( 'njb/birth_date' === $field_key ) {
                    //Check if value is date and less than 30 years old
                    $birth_date = DateTime::createFromFormat( 'Y-m-d', $field_value );
                    // Check if cart contains the Young Adults Membership
                    $isYoungAdultMembership = false;
                    foreach ( WC()->cart->get_cart() as $cart_item ) {
                        if ( $cart_item['product_id'] === 1397 ) { // Replace with the actual product ID for Young Adults Membership
                            $isYoungAdultMembership = true;
                            break;
                        }
                    }
                    if ( $isYoungAdultMembership &&$birth_date < new DateTime( '-30 years' ) ) {
                        $errors->add( 'invalid_age', 'You must be less than 30 years old to subscribe to the Young Adults Membership.<br /><a href="/cart">Return to cart</a> and remove the item to add the Regular membership.' );
                    }
                }
                return $errors;
            }, 10, 3
        );
    }

    /**
     * Set ACF settings to enable shortcodes.
     *
     * @return void
     */
    public static function set_acf_settings() {
        acf_update_setting( 'enable_shortcode', true );
    }

    /**
     * Replace last instance of search from a given string
     *
     * @param string $search String to search for.
     * @param string $replace String to replace with.
     * @param string $subject Subject.
     * @return string
     */
    public static function str_replace_last( $search, $replace, $subject ) {
        if ( ( $pos = strrpos( $subject, $search ) ) !== false ) { // phpcs:ignore
            $search_length = strlen( $search );
            $subject       = substr_replace( $subject, $replace, $pos, $search_length );
        }
        return $subject;
    }


    /**
     * Add Splide markup to query carousel
     *
     * @param string $block_content Block content.
     * @param array  $block Block object.
     * @return string
     */
    public static function query_carousel_block( $block_content, $block ) {
        $is_carousel = false !== strpos( $block['attrs']['className'] ?? '', 'is-style-carousel' );
        if ( $is_carousel ) {
            $block_content = preg_replace( '/is\-style\-carousel/', 'is-style-carousel splide', $block_content, 1 );
            $block_content = preg_replace( '/wp\-block\-post\-template/', 'wp-block-post-template splide__list', $block_content, 1 );
            $block_content = preg_replace( '/\<ul/', '<div class="splide__track"><ul', $block_content, 1 );
            $block_content = self::str_replace_last( '</ul>', '</ul></div>', $block_content );
            $block_content = preg_replace( '/wp\-block\-post\s/', 'wp-block-post splide__slide ', $block_content );
        }

        return $block_content;
    }

    /**
     * Add an external link to the event if the event has ended and an external link is set.
     *
     * @return void
     */
    public static function event_add_external_link(){
        global $post;
        $event = $post->ID;
        if ( ! tribe_is_event( $event ) ){
            return false;
        }
        $event = tribe_events_get_event( $event );

        if (  time() < strtotime( $event->end_date ) || empty( get_field('external_link') ) ) {
            return false;
        }
        
        ?>
        <div class="wp-block-buttons is-content-justification-center is-layout-flex  wp-block-buttons-is-layout-flex">
            <div class="wp-block-button">
                <a class="wp-block-button__link wp-element-button" href="<?php echo get_field('external_link') ?>">Register to Event</a>
            </div>
        </div>
        <?php
    }

    /**
     *  Create a user number when a new user is registered.
     */
    public static function create_user_number( $user_id ) {
        // Check if the user ID is valid
        if ( ! $user_id || ! is_numeric( $user_id ) ) {
            return;
        }

        // Generate a custom number for the user
        $custom_number = 'NJB' . str_pad( $user_id, 8, '0', STR_PAD_LEFT );

        // Update the user meta with the custom number
        update_user_meta( $user_id, 'custom_number', $custom_number );

    }

    /**
     * Add a custom number to the subscription order.
     *
     * @param WC_Order $new_order The new order object.
     * @param int      $order_id  The order ID.
     */
    public static function add_custom_number_to_subscription( $new_order, $order_id ) {
        // Check if the order is a subscription order
        if ( ! wps_sfw_order_has_subscription( $new_order ) ) {
            error_log( 'Order ID ' . $order_id . ' is not a subscription order.' );
            return;
        }

        $subscription = wc_get_order( $order_id );
        if( ! $subscription || ! is_a( $subscription , 'WC_Order') ) {
            error_log( 'Subscription not found for order ID ' . $order_id );
            return;
        }
        // get user ID from the subscription
        $user_id = $subscription->get_customer_id();
        // Check if the user ID is valid
        if ( ! $user_id || ! is_numeric( $user_id ) ) {
            error_log( 'Invalid user ID for order ID ' . $order_id );
            return;
        }
        // Generate a custom number for the user
        $custom_number = get_user_meta( $user_id, 'custom_number', true );
        if ( empty( $custom_number ) ) {
            // If the custom number is not set, create it
            self::create_user_number( $user_id );
            $custom_number = get_user_meta( $user_id, 'custom_number', true );
        }

        $subscription->add_meta_data( 'custom_number', $custom_number );
        $subscription->add_order_note( 'Custom number added: ' . $custom_number );
        $subscription->save();
        return;
    }

    /**
     * Add a custom number column to the subscription table.
     *
     * @param array $columns The existing columns.
     * @return array The modified columns.
     */
    public static function add_custom_number_to_subscription_table( $columns ) {
        // Check if the order is a subscription order
        return array_merge( $columns, array(
            'custom_number' => __( 'Custom Number', 'njb-customizations' ),
        ) );
    }

    /**
     * Add the custom number value to the subscription table.
     *
     * @param string $return The current value.
     * @param string $column_name The column name.
     * @param array $item The item data.
     * @return string The modified value.
     */
    public static function add_custom_number_value_to_subscription_table( $return, $column_name, $item ){
        if ( 'custom_number' === $column_name ) {
            $subscription = wc_get_order( $item['parent_order_id'] );
            $custom_number = $subscription->get_meta( 'custom_number');
            
            if ( ! empty( $custom_number ) ) {
                $return =  esc_html( $custom_number );
            } else {
                //create custom number
                $user_id = $subscription->get_customer_id();
                if ( ! $user_id || ! is_numeric( $user_id ) ) {
                    error_log( 'Invalid user ID for subscription ID ' . $item['parent_order_id'] );
                    return $return;
                }
                self::create_user_number( $user_id );
                $custom_number = get_user_meta( $user_id, 'custom_number', true );

                $subscription->add_meta_data( 'custom_number', $custom_number );
                $subscription->add_order_note( 'Custom number added: ' . $custom_number );
                $subscription->save();

                if ( ! empty( $custom_number ) ) {
                    $return = esc_html( $custom_number );
                } else {
                    $return = __( 'No custom number found', 'njb-customizations' );
                }
            }
        }
        return $return;
    }

    public static function add_to_cart_subscription_handler ( $handler, $product_id ) {
        // Check if the product is a subscription product
        if ( wps_sfw_check_product_is_subscription( $product_id ) ) {
            // Redirect to checkout page
            return 'wps_swf_subscription_handler';
        }
        return $handler;
    }

    public static function redirect_subscription_to_checkout( $url ) {
        $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( wp_unslash( $_REQUEST['add-to-cart'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

        $quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			$was_added_to_cart =  true;
		}

        // If we added the product to the cart we can now optionally do a redirect.
        $url = apply_filters( 'woocommerce_add_to_cart_redirect', $url, $adding_to_cart );
        wp_safe_redirect( wc_get_checkout_url() );
        exit;
			
    }

    public static function remove_subscription_from_cart( $passed, $product_id, $quantity ) {
        // Remove the subscription product from the cart
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            // Check if the cart item is the subscription product
            if ( wps_sfw_check_product_is_subscription( $cart_item['data'] ) ) {
                error_log('Removing subscription product from cart: ' . $cart_item['product_id'] );
                WC()->cart->remove_cart_item( $cart_item_key );
                $passed = true;
                break;
            }
        }
        return $passed;
    }

    /**
     * Remove WooCommerce Blocks default order fields hook.
     * This prevents duplicate rendering of additional checkout fields.
     */
    public static function remove_wc_blocks_order_fields_hook() {
        // Check if WooCommerce Blocks CheckoutFieldsFrontend class exists
        if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFieldsFrontend' ) ) {
            return;
        }

        // Get the CheckoutFieldsFrontend instance from the container
        try {
            $checkout_fields_frontend = Package::container()->get( \Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFieldsFrontend::class );

            // Remove the default hook
            remove_action( 'woocommerce_order_details_after_customer_details', array( $checkout_fields_frontend, 'render_order_other_fields' ), 10 );
        } catch ( Exception $e ) {
            // If we can't get the instance, log the error
            error_log( 'NJB Customizations: Could not remove WooCommerce Blocks order fields hook - ' . $e->getMessage() );
        }
    }


    /**
     * Get all subscription product IDs.
     *
     * @param bool $force_refresh Force refresh the cache.
     * @return array Array of subscription product IDs.
     */
    public static function get_subscription_product_ids( $force_refresh = false ) {
        $transient_key = 'njb_subscription_product_ids';

        // Try to get cached value
        if ( ! $force_refresh ) {
            $cached_ids = get_transient( $transient_key );
            if ( false !== $cached_ids ) {
                return $cached_ids;
            }
        }

        $subscription_ids = array();

        if ( ! function_exists( 'wps_sfw_check_product_is_subscription' ) ) {
            return $subscription_ids;
        }

        // Query all products
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        );

        $product_ids = get_posts( $args );

        foreach ( $product_ids as $product_id ) {
            $wps_subscription_product = wps_sfw_get_meta_data( $product_id, '_wps_sfw_product', true );
			if ( 'yes' === $wps_subscription_product ) {
                $subscription_ids[] = $product_id;
            }
        }

        // Cache for 12 hours (43200 seconds)
        set_transient( $transient_key, $subscription_ids, 12 * HOUR_IN_SECONDS );

        return $subscription_ids;
    }

    /**
     * Clear the subscription product IDs cache.
     *
     * @param int $post_id The post ID.
     */
    public static function clear_subscription_product_ids_cache( $post_id = 0 ) {
        // If a post_id is provided, check if it's a product
        if ( $post_id && 'product' !== get_post_type( $post_id ) ) {
            return;
        }

        delete_transient( 'njb_subscription_product_ids' );
    }

    /**
     * Check if an order has subscription products.
     *
     * @param WC_Order $order The order object.
     * @return bool True if order has subscription products, false otherwise.
     */
    public static function order_has_subscription_products( $order ) {
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( $product && function_exists( 'wps_sfw_check_product_is_subscription' ) && wps_sfw_check_product_is_subscription( $product ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add additional checkout fields to the WooCommerce emails.
     *
     * @param WC_Order $order The order object.
     * @param bool $sent_to_admin Whether the email is sent to the admin.
     * @param bool $plain_text Whether the email is plain text.
     */
    public static function additional_checkout_fields( $order, $sent_to_admin, $plain_text ) {
        // Solo mostrar si la orden tiene productos de suscripción
        if ( ! self::order_has_subscription_products( $order ) ) {
            return;
        }

        // Get checkout fields
        $checkout_fields = Package::container()->get( CheckoutFields::class );
        $all_fields = $checkout_fields->get_all_fields_from_object( $order, 'other' );

        // Get WhatsApp number and Custom Number
        $whatsapp_number = ! empty( $all_fields['njb/whatsapp_number'] ) ? $all_fields['njb/whatsapp_number'] : '';
        $custom_number = $order->get_meta( 'custom_number' );

        ?>
        <?php if ( ! empty( $whatsapp_number ) ) : ?>
            <p>
                <strong><?php esc_html_e( 'WhatsApp Number:', 'njb-customizations' ); ?></strong> <?php echo esc_html( $whatsapp_number ); ?>
            </p>
        <?php endif; ?>

        <?php if ( ! empty( $custom_number ) ) : ?>
            <p>
                <strong><?php esc_html_e( 'Custom Number:', 'njb-customizations' ); ?></strong> <?php echo esc_html( $custom_number ); ?><br />
                <strong><?php esc_html_e( 'Note:', 'njb-customizations' ); ?></strong> <?php esc_html_e( 'This NJB ID will be required for registration at future NJB events.', 'njb-customizations' ); ?>
            </p>
        <?php endif; ?>

        <p>
            <strong><?php esc_html_e( 'Main NJB Group', 'njb-customizations' ); ?></strong> <a href="https://chat.whatsapp.com/Gb5oNEguy8N1zRXIStjbh0" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Join Main Whatsapp Group', 'njb-customizations' ); ?></a>
        </p>

        <?php
        $has_groups = false;

        // Check if any groups are selected
        foreach ( self::$group_options as $key => $value ) {
            if ( ! empty( $all_fields[ $key ] ) ) {
                $has_groups = true;
                break;
            }
        }

        if ( $has_groups ) :
        ?>
            <p>
                <strong><?php esc_html_e( 'Selected Additional Groups', 'njb-customizations' ); ?></strong><br />
                <?php
                foreach ( self::$group_options as $key => $value ) {
                    $group_value = $all_fields[ $key ];
                    if ( ! empty( $group_value ) ) {
                        echo esc_html( $value['label'] ) . ': <a href="https://chat.whatsapp.com/' . esc_html( $value['whatsapp'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Join Whatsapp Group', 'njb-customizations' ) . '</a><br />';
                    }
                }
                ?>
            </p>
        <?php endif; ?>

        <p><?php esc_html_e( 'Check your email address for your login information and unique NJB ID.', 'njb-customizations' ); ?></p>

        <p><strong><?php esc_html_e( '📌 Business Listing for Members', 'njb-customizations' ); ?></strong></p>

        <p><?php esc_html_e( 'As an NJB member, you are eligible to list your business in our official NJB Business Directory. This gives your brand visibility within the NJB community and allows other members to easily discover and support your services.', 'njb-customizations' ); ?></p>

        <p><?php echo sprintf( __( 'You can submit your business detail here: %s', 'njb-customizations' ), '<a href="' . wc_get_account_endpoint_url( 'business' ) . '">Business Area</a>' ); ?></p>
        <?php
    }

    /**
     * Display additional fields on the order received page.
     *
     * @param WC_Order $order The order object.
     */
    public static function display_additional_fields_on_order_page( $order ) {
        // Solo mostrar si la orden tiene productos de suscripción
        if ( ! self::order_has_subscription_products( $order ) ) {
            return;
        }

        // Get checkout fields
        $checkout_fields = Package::container()->get( CheckoutFields::class );
        $all_fields = $checkout_fields->get_all_fields_from_object( $order, 'other' );

        // Get WhatsApp number and Custom Number
        $whatsapp_number = ! empty( $all_fields['njb/whatsapp_number'] ) ? $all_fields['njb/whatsapp_number'] : '';
        $custom_number = $order->get_meta( 'custom_number' );

        ?>
        <div class="njb-additional-fields alignwide wp-block-woocommerce-order-confirmation-billing-wrapper">
            <div class="njb-whatsapp-groups wc-block-order-confirmation-billing-address alignwide">
                <?php if ( ! empty( $whatsapp_number ) ) : ?>
                    <p>
                        <strong><?php esc_html_e( 'WhatsApp Number:', 'njb-customizations' ); ?></strong> <?php echo esc_html( $whatsapp_number ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( ! empty( $custom_number ) ) : ?>
                    <p>
                        <strong><?php esc_html_e( 'Custom Number:', 'njb-customizations' ); ?></strong> <?php echo esc_html( $custom_number ); ?><br />
                        <strong><?php esc_html_e( 'Note:', 'njb-customizations' ); ?></strong> <?php esc_html_e( 'This NJB ID will be required for registration at future NJB events.', 'njb-customizations' ); ?>
                    </p>
                <?php endif; ?>

                <p>
                    <strong><?php esc_html_e( 'Main NJB Group', 'njb-customizations' ); ?></strong> <a href="https://chat.whatsapp.com/Gb5oNEguy8N1zRXIStjbh0" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Join Main Whatsapp Group', 'njb-customizations' ); ?></a>
                </p>

                <?php
                $has_groups = false;

                // Check if any groups are selected
                foreach ( self::$group_options as $key => $value ) {
                    if ( ! empty( $all_fields[ $key ] ) ) {
                        $has_groups = true;
                        break;
                    }
                }

                if ( $has_groups ) :
                ?>
                    <p>
                        <strong><?php esc_html_e( 'Selected Additional Groups', 'njb-customizations' ); ?></strong><br />
                        <?php
                        foreach ( self::$group_options as $key => $value ) {
                            $group_value = $all_fields[ $key ];
                            if ( ! empty( $group_value ) ) {
                                echo esc_html( $value['label'] ) . ': <a href="https://chat.whatsapp.com/' . esc_html( $value['whatsapp'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Join Whatsapp Group', 'njb-customizations' ) . '</a><br />';
                            }
                        }
                        ?>
                    </p>
                <?php endif; ?>

                <p><?php esc_html_e( 'Check your email address for your login information and unique NJB ID.', 'njb-customizations' ); ?></p>

                <p><strong><?php esc_html_e( '📌 Business Listing for Members', 'njb-customizations' ); ?></strong></p>

                <p><?php esc_html_e( 'As an NJB member, you are eligible to list your business in our official NJB Business Directory. This gives your brand visibility within the NJB community and allows other members to easily discover and support your services.', 'njb-customizations' ); ?></p>

                <p><?php echo sprintf( __( 'You can submit your business detail here: %s', 'njb-customizations' ), '<a href="' . wc_get_account_endpoint_url( 'business' ) . '">Business Area</a>' ); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Force stripe to Save payment information to my account for future purchases.
     *
     * @param bool $force_save_source Should we force save payment source.
     */
    public static function wps_sfw_wc_stripe_force_save_source_callback_old( $force_save_source ) {
        if ( wps_sfw_is_cart_has_subscription_product() ) {
            return false;
        }
        return $force_save_source;
    }
} 
 