<?php if ( ! defined( 'ABSPATH' ) ) exit;

class RCPT_Settings {

    public function __construct() {
        add_action('admin_menu', array( $this, 'admin_menus'), 10 );
        add_action('admin_init', array( $this, 'register_settings' ));
        add_filter('cron_schedules', array( $this, 'rcpt_cron_schedules' ) );
        add_action('init', array($this, 'schedule_translation'));
    }

    public function register_settings() {
        register_setting( 'rcpt_settings', 'rcpt_settings', '' );
    }

    public function admin_menus() {
        add_menu_page ( 'Post Translator' , 'Post Translator Settings' , 'manage_options' , 'rcpt-settings' , array( $this , 'rcpt_settings_page' ), 'dashicons-translation');
    }

    public function rcpt_settings_page() {
        $rcpt_settings = get_option('rcpt_settings');
        include_once(RCPT_PATH_INCLUDES . '/settings.php');
    }

    public function rcpt_cron_schedules($schedules){
        $rcpt_settings = get_option('rcpt_settings');
        $translate_time = isset($rcpt_settings['translate_time']) ? $rcpt_settings['translate_time'] : '';
        $translate_time = intval($translate_time);

	    if(!isset($schedules["rcptsched"])){
	        $schedules["rcptsched"] = array(
 	            'interval' => $translate_time*60,
	            'display' => __('Once every '.$translate_time.' minutes'));
	    }
	    return $schedules;
	}

    public function schedule_translation() {
        $rcpt_settings = get_option('rcpt_settings');
        $cron_translate = isset($rcpt_settings['cron_translate']) ? $rcpt_settings['cron_translate'] : '';

        if ($cron_translate) {
            if ( ! wp_next_scheduled( 'rcpt_translation_schedule' ) ) {
                wp_schedule_event( current_time( 'timestamp', true ), 'rcptsched', 'rcpt_translation_schedule' );
            }
        } else {
			if ( wp_next_scheduled( 'rcpt_translation_schedule' ) ) {
				wp_clear_scheduled_hook( 'rcpt_translation_schedule' );
			}
		}
    }
    

}

new RCPT_Settings;