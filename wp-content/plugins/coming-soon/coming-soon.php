<?php
/*
Plugin Name: Coming Soon Page, Maintenance Mode, Landing Pages & WordPress Website Builder by SeedProd
Plugin URI: https://www.seedprod.com
Description: The Easiest WordPress Drag & Drop Page Builder that allows you to build your webiste, create Landing Pages, Coming Soon Pages, Maintenance Mode Pages and more.
Version:  6.10.0
Author: SeedProd
Author URI: https://www.seedprod.com
TextDomain: coming-soon
Domain Path: /languages
License: GPLv2 or later
*/

/**
 * Default Constants
 */
define( 'SEEDPROD_BUILD', 'lite' );
define( 'SEEDPROD_SLUG', 'coming-soon/coming-soon.php' );
define( 'SEEDPROD_VERSION', '6.10.0' );
define( 'SEEDPROD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
// Example output: /Applications/MAMP/htdocs/wordpress/wp-content/plugins/seedprod/
define( 'SEEDPROD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// Example output: http://localhost:8888/wordpress/wp-content/plugins/seedprod/

if ( defined( 'SEEDPROD_LOCAL_JS' ) ) {
	define( 'SEEDPROD_API_URL', 'http://v4app.seedprod.test/v4/' );
	define( 'SEEDPROD_WEB_API_URL', 'http://v4app.seedprod.test/' );
	define( 'SEEDPROD_BACKGROUND_DOWNLOAD_API_URL', 'https://api.seedprod.com/v3/background_download' );

} else {
	define( 'SEEDPROD_API_URL', 'https://api.seedprod.com/v4/' );
	define( 'SEEDPROD_WEB_API_URL', 'https://app.seedprod.com/' );
	define( 'SEEDPROD_BACKGROUND_DOWNLOAD_API_URL', 'https://api.seedprod.com/v3/background_download' );
}



/**
 * Load Translation
 */
function seedprod_lite_load_textdomain() {
	load_plugin_textdomain( 'coming-soon', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'seedprod_lite_load_textdomain' );


/**
 * Upon activation of the plugin check php version, load defaults and show welcome screen.
 */
function seedprod_lite_activation() {
	seedprod_lite_check_for_free_version();

	update_option( 'seedprod_run_activation', true, '', false );

	// Load and Set Default Settings
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
	add_option( 'seedprod_settings', $seedprod_default_settings );

	// Set inital version
	$data = array(
		'installed_version' => SEEDPROD_VERSION,
		'installed_date'    => time(),
		'installed_pro'     => SEEDPROD_BUILD,
	);

	add_option( 'seedprod_over_time', $data );

	// Set a token
	add_option( 'seedprod_token', wp_generate_uuid4() );

	// Welcome Page Flag
	set_transient( '_seedprod_welcome_screen_activation_redirect', true, 30 );

	// set cron to fetch feed
	if ( ! wp_next_scheduled( 'seedprod_notifications' ) ) {
		wp_schedule_event( time(), 'daily', 'seedprod_notifications' );
	}

	// flush rewrite rules
	flush_rewrite_rules();

}

register_activation_hook( __FILE__, 'seedprod_lite_activation' );


/**
 * Deactivate Flush Rules
 */
function seedprod_lite_deactivate() {
	wp_clear_scheduled_hook( 'seedprod_notifications' );
}

register_deactivation_hook( __FILE__, 'seedprod_lite_deactivate' );



/**
 * Load Plugin
 */
require_once SEEDPROD_PLUGIN_PATH . 'app/bootstrap.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/routes.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/load_controller.php';

/**
 * Maybe Migrate
 */
add_action( 'upgrader_process_complete', 'seedprod_lite_check_for_free_version' );
add_action( 'init', 'seedprod_lite_check_for_free_version' );




