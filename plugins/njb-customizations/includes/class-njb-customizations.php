<?php
class NJB_Customizations {
    public function __construct() {
        // Initialization code here
       add_filter( 'excerpt_more', array( $this, 'custom_excerpt_more' ) );
       add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ) );
       add_action( 'woocommerce_init', array( __CLASS__, 'add_whatsapp_number' ) );
    }

    public function activate() {
        // Code to run on plugin activation
    }


    public function deactivate() {
        // Code to run on plugin deactivation
    }

    public function run() {
        require_once plugin_dir_path( __FILE__ ) . 'post-types/donations.php';
        // Code to run the plugin
        add_action('init', array( $this, 'custom_functionality' ));
       
    }

    /**
     * Custom functionality for the plugin.
     */
    public function custom_functionality() {
        // Custom functionality for the plugin
      
    }
    public function custom_excerpt_more( $more ) {
        return '...';
    }
   
    public function custom_excerpt_length( $more ) {
        return 15;
    }


    public static  function add_whatsapp_number () {
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
}
