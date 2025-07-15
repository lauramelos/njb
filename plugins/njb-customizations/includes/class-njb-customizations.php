<?php
class NJB_Customizations {
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
        add_action( 'wps_sfw_subscription_order', array( __CLASS__, 'add_custom_number_to_subscription'), 10, 2 );
        add_filter( 'wps_sfw_column_subscription_table', array( __CLASS__, 'add_custom_number_to_subscription_table' ), 10);
        add_filter( 'wps_sfw_add_case_column', array( __CLASS__, 'add_custom_number_value_to_subscription_table' ), 10, 3 );
        add_action('template_redirect', array( __CLASS__, 'skip_cart_page_redirection_to_checkout' ) );
        add_filter ('woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirection_to_checkout' ) ); 

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
                'label'         => 'WhatsApp Number',
                'location'      => 'contact',
                'required'      => true,
                'attributes'    => array(
                    'autocomplete'     => 'whatsapp_number',
                    'aria-describedby' => 'WhatsApp Number',
                    'aria-label'       => 'WhatsApp Number label',
                    'pattern' => '\+?[0-9\s\-\(\)]{7,15}', // A phone number pattern allowing 7 to 15 digits with optional +, spaces, dashes, and parentheses.					'title'            => 'Title to show on hover',
                    'data-custom'      => 'custom data',
                ),
            ),
        );
    }

    public static function add_group_options () {
        $checkboxes = array(
            'book-club' => 'Book Club',
            'business-start-up' => 'Business Start Up',
            'community-outreach'=> 'Community Outreach',
            'culinary-interests' => 'Culinary Interests',
            'fashion-lifestyle'=>'Fashion & Lifestyle',
            'fitness-exercise' => 'Fitness & Exercise',
            'travels-art-culture' => 'Travels, Art & Culture',
            'young-adults' => 'Young Adults',
        );
        foreach ( $checkboxes as $key => $value ) {
            woocommerce_register_additional_checkout_field(
                array(
                    'id'       => 'njb/' . $key,
                    'label'    => $value,
                    'optionalLabel' => $value,
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
        
        $subscription = wc_get_order( $new_order );
        $subscription->add_meta_data( 'custom_number', 'NJB00000001' );  // Todo: Replace with actual logic to generate a custom number 
        $subscription->add_order_note( 'Custom number added: NJB00000001' );
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
        //error_log(print_r( $item, true ));
        if ( 'custom_number' === $column_name ) {
            $subscription = wc_get_order( $item['subscription_id'] );
            $custom_number = $subscription->get_meta( 'custom_number');
            
            if ( ! empty( $custom_number ) ) {
                $return =  esc_html( $custom_number );
            } else {
                $return =  __( 'No custom number', 'njb-customizations' );
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

} 
 