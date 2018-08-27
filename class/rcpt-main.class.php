<?php if ( ! defined( 'ABSPATH' ) ) exit;

class WPML_Post_Translator{
    
    private static $instance;

    public static function get_instance()
    {
        if( null == self::$instance ) {
            self::$instance = new WPML_Post_Translator();
        }

        return self::$instance;
    }

    function __construct() {
        add_action('admin_enqueue_scripts', array( $this, 'admin_scripts' ));
        add_action('wp_enqueue_scripts', array($this, 'public_scripts'));
    }

    public function admin_scripts($hook) {
        wp_enqueue_style('rcpt-style', RCPT_URL .'/assets/css/admin-styles.css');
        wp_enqueue_script('rcpt-script', RCPT_URL .'/assets/js/scripts.js', 'jquery', NULL, TRUE);
    }

    public function public_scripts() {
        // add public scripts
    }
}