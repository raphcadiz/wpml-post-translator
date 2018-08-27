<?php
/**
 * Plugin Name: WPML Post Translator
 * Plugin URI:  https://github.com/raphcadiz
 * Description: Translate post through Google Translator API
 * Version:     1.0
 * Author:      raphcadiz
 * Author URI:  https://github.com/raphcadiz
 * Text Domain: rcpt
 */

if (!class_exists('WPML_Post_Translator')):

    define( 'RCPT_PATH', dirname( __FILE__ ) );
    define( 'RCPT_PATH_INCLUDES', dirname( __FILE__ ) . '/includes' );
    define( 'RCPT_PATH_CLASS', dirname( __FILE__ ) . '/class' );
    define( 'RCPT_FOLDER', basename( RCPT_PATH ) );
    define( 'RCPT_URL', plugins_url() . '/' . RCPT_FOLDER );
    define( 'RCPT_URL_INCLUDES', RCPT_URL . '/includes' );
    define( 'RCPT_URL_CLASS', RCPT_URL . '/class' );
    define( 'RCPT_VERSION', 1.0 );

    register_activation_hook( __FILE__, 'rcpt_activation' );
    function rcpt_activation(){
        if ( ! class_exists('SitePress') ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die('Sorry, but this plugin requires SitePress WPML to be activated.');
        }
    }

    add_action( 'admin_init', 'rcpt_activate' );
    function rcpt_activate(){
        if ( ! class_exists('SitePress') ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
    }

    register_deactivation_hook( __FILE__, 'rcpt_translation_schedule' );
    function rcpt_deactivation() {
        wp_clear_scheduled_hook( 'rcpt_translation_schedule' );
    }

    /*
     * include necessary files
     */
    require_once('vendor/autoload.php');
    require_once(RCPT_PATH_CLASS . '/rcpt-main.class.php');
    require_once(RCPT_PATH_CLASS . '/rcpt-settings.class.php');
    require_once(RCPT_PATH_CLASS . '/rcpt-translation-processes.class.php');


    add_action( 'plugins_loaded', array( 'WPML_Post_Translator', 'get_instance' ) );

endif;