<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
/**
 *
 * @link              https://addonspress.com/
 * @since             1.0.0
 * @package           Advanced_Import
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Import
 * Plugin URI:        https://addonspress.com/item/advanced-import
 * Description:       Easily import demo data starter site packages or Migrate your site data
 * Version:           1.3.6
 * Author:            AddonsPress
 * Author URI:        https://addonspress.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-import
 * Domain Path:       /languages
 */

/*Define Constants for this plugin*/
define( 'ADVANCED_IMPORT_VERSION', '1.3.6' );
define( 'ADVANCED_IMPORT_PLUGIN_NAME', 'advanced-import' );
define( 'ADVANCED_IMPORT_PATH', plugin_dir_path( __FILE__ ) );
define( 'ADVANCED_IMPORT_URL', plugin_dir_url( __FILE__ ) );
define( 'ADVANCED_IMPORT_SCRIPT_PREFIX', ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '.min' );

$upload_dir                   = wp_upload_dir();
$advanced_import_temp         = $upload_dir['basedir'] . '/advanced-import-temp/';
$advanced_import_temp_zip     = $upload_dir['basedir'] . '/advanced-import-temp-zip/';
$advanced_import_temp_uploads = $advanced_import_temp . '/uploads/';

define( 'ADVANCED_IMPORT_TEMP', $advanced_import_temp );
define( 'ADVANCED_IMPORT_TEMP_ZIP', $advanced_import_temp_zip );
define( 'ADVANCED_IMPORT_TEMP_UPLOADS', $advanced_import_temp_uploads );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-import-activator.php
 */
function activate_advanced_import() {
	require_once ADVANCED_IMPORT_PATH . 'includes/class-advanced-import-activator.php';
	Advanced_Import_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-advanced-import-deactivator.php
 */
function deactivate_advanced_import() {
	require_once ADVANCED_IMPORT_PATH . 'includes/class-advanced-import-deactivator.php';
	Advanced_Import_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_advanced_import' );
register_deactivation_hook( __FILE__, 'deactivate_advanced_import' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ADVANCED_IMPORT_PATH . 'includes/class-advanced-import.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function advanced_import() {
	return Advanced_Import::instance();
}
advanced_import();
