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
        require_once plugin_dir_path( __FILE__ ) . 'acf.php';
        add_filter( 'excerpt_more', array( __CLASS__, 'custom_excerpt_more' ) );
        add_filter( 'excerpt_length', array( __CLASS__, 'custom_excerpt_length' ) );
        add_action( 'woocommerce_init', array( __CLASS__, 'add_whatsapp_number' ) );
        add_action( 'woocommerce_init', array( __CLASS__, 'add_group_options' ) );
        add_action( 'acf/init', array( __CLASS__, 'set_acf_settings' ) );
        add_filter( 'render_block_core/query', array( __CLASS__, 'query_carousel_block' ), 10, 2 );
        add_action( 'tribe_events_single_event_after_the_content', array( __CLASS__, 'event_add_external_link' ), 10, 2 );
        add_action( 'user_register',  array( __CLASS__, 'create_user_number') );
        add_action( 'wps_sfw_subscription_order', array( __CLASS__, 'add_custom_number_to_subscription'), 10, 2 );
        add_filter( 'wps_sfw_column_subscription_table', array( __CLASS__, 'add_custom_number_to_subscription_table' ), 10);
        add_filter( 'wps_sfw_add_case_column', array( __CLASS__, 'add_custom_number_value_to_subscription_table' ), 10, 3 );
        add_action( 'template_redirect', array( __CLASS__, 'skip_cart_page_redirection_to_checkout' ) );
        add_filter( 'woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirection_to_checkout' ) ); 
        add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'remove_cart_item_before_add_to_cart' ), 5 );
        $emails = WC_Emails::instance();
        remove_action( 'woocommerce_email_customer_details', array( $emails, 'additional_checkout_fields' ), 30, 3 );
        add_action( 'woocommerce_email_customer_details', array( __CLASS__, 'additional_checkout_fields' ), 30, 3 );
        add_filter( 'wc_stripe_force_save_source', array( __CLASS__, 'wps_sfw_wc_stripe_force_save_source_callback_old' ), 20 );

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
    public static function add_whatsapp_number () {
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

    public static function add_group_options () {
        foreach ( self::$group_options as $key => $value ) {
            woocommerce_register_additional_checkout_field(
                array(
                    'id'       => $key,
                    'label'    => $value['label'],
                    'optionalLabel' => $value['label'],
                    'location' => 'contact',
                    'type'     => 'checkbox',
                )
            );
        }
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

    /**
     * Redirect the cart page to the checkout page.
     *
     * @return void
     */
    public static  function skip_cart_page_redirection_to_checkout() {
        // Check if WooCommerce is active
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        if( is_cart() ) {
            wp_redirect( wc_get_checkout_url() );
        }
    }

    public static function add_to_cart_redirection_to_checkout( ) {
        return wc_get_checkout_url();
    }

    public static function remove_cart_item_before_add_to_cart( $passed ) {
        if( ! WC()->cart->is_empty() )
           WC()->cart->empty_cart();
        return $passed;
    }

    /**
     * Add additional checkout fields to the WooCommerce emails.
     *
     * @param WC_Order $order The order object.
     * @param bool $sent_to_admin Whether the email is sent to the admin.
     * @param bool $plain_text Whether the email is plain text.
     */
    public static function additional_checkout_fields( $order, $sent_to_admin, $plain_text ) {
        // Get the WhatsApp number from the order meta 
        $checkout_fields = Package::container()->get( CheckoutFields::class );
        $all_fields = $checkout_fields->get_all_fields_from_object( $order, 'other' );

        $whatsapp_number =  $all_fields[ 'njb/whatsapp_number' ];
        if ( ! empty( $whatsapp_number ) ) {
            echo '<p><strong>' . __( 'WhatsApp Number:', 'njb-customizations' ) . '</strong> ' . esc_html( $whatsapp_number ) . '</p>';
        }

        // Get the custom number from the order meta
        $custom_number = $order->get_meta( 'custom_number' );
        if ( ! empty( $custom_number ) ) {
            echo '<p><strong>' . __( 'Custom Number:', 'njb-customizations' ) . '</strong> ' . esc_html( $custom_number ) . '<br />';
            echo '<strong>' . __( 'Note: ', 'njb-customizations') . '</strong>' . __( 'This NJB ID will be required for registration at future NJB events.', 'njb-customizations' ) . '</p>';
        }

        // Get the group options from the order meta
        ?>
        <p>
            <strong><?php esc_html_e( 'Selected Groups: ', 'njb-customizations' ); ?></strong> 
            <?php
                foreach ( self::$group_options as $key => $value ) {
                    $group_value = $all_fields[ $key ];
                    if ( ! empty( $group_value ) ) {
                        echo esc_html( $value['label'] ) . ': <a href="https://chat.whatsapp.com/' . esc_html( $value['whatsapp'] ) . '" target="_blank">Join Whatsapp Group</a><br />';
                    }
                }
            ?>
        </p>
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
 