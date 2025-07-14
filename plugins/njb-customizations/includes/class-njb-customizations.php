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
        add_action( 'acf/init', array( __CLASS__, 'set_acf_settings' ) );
        add_filter( 'render_block_core/query', array( __CLASS__, 'query_carousel_block' ), 10, 2 );
        add_action( 'tribe_events_single_event_after_the_content', array( __CLASS__, 'event_add_external_link' ), 10, 2 );
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
                'id'            => 'nbj/whatsapp_number',
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
}
