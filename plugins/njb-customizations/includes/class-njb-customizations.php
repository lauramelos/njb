<?php
class NJB_Customizations {
    public function __construct() {
        // Initialization code here
       add_filter( 'excerpt_more', array( $this, 'custom_excerpt_more' ) );
       add_filter( 'excerpt_length',array( $this, 'custom_excerpt_length' ) );

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
}