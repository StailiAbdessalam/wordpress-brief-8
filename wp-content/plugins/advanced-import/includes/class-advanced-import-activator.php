<?php

/**
 * Fired during plugin activation
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Advanced_Import
 * @subpackage Advanced_Import/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Advanced_Import
 * @subpackage Advanced_Import/includes
 * @author     Addons Press <addonspress.com>
 */
class Advanced_Import_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		advanced_import_add_installed_time();
	}

}
