<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Advanced_Import
 * @subpackage Advanced_Import/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Advanced_Import
 * @subpackage Advanced_Import/includes
 * @author     Addons Press <addonspress.com>
 */
class Advanced_Import {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Advanced_Import_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The admin class object of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object Advanced_Import_Admin    $admin
	 */
	public $admin;

	/**
	 * The language object of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object Advanced_Import_i18n    $plugin_i18n
	 */
	public $plugin_i18n;

	/**
	 * Main Advanced_Import Instance
	 *
	 * Insures that only one instance of Advanced_Import exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @uses Advanced_Import::setup_globals() Setup the globals needed
	 * @uses Advanced_Import::load_dependencies() Include the required files
	 * @uses Advanced_Import::set_locale() Setup language
	 * @uses Advanced_Import::define_admin_hooks() Setup admin hooks and actions
	 * @uses Advanced_Import::run() run
	 * @return object
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been ran previously
		if ( null === $instance ) {
			$instance = new Advanced_Import();

			$instance->setup_globals();
			$instance->load_dependencies();
			$instance->set_locale();
			$instance->define_admin_hooks();
			$instance->run();
		}

		// Always return the instance
		return $instance;
	}

	/**
	 * Empty construct
	 *
	 * @since    1.0.0
	 */
	public function __construct() { }
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Advanced_Import_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function setup_globals() {

		$this->version     = defined( 'ADVANCED_IMPORT_VERSION' ) ? ADVANCED_IMPORT_VERSION : '1.0.0';
		$this->plugin_name = ADVANCED_IMPORT_PLUGIN_NAME;

		// The array of actions and filters registered with this plugins.
		$this->actions = array();
		$this->filters = array();

		// Misc
		$this->domain = 'advanced-import';      // Unique identifier for retrieving translated strings
		$this->errors = new WP_Error(); // errors
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Advanced_Import_Loader. Orchestrates the hooks of the plugin.
	 * - Advanced_Import_i18n. Defines internationalization functionality.
	 * - Advanced_Import_Admin. Defines all hooks for the admin area.
	 * - Advanced_Import_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ADVANCED_IMPORT_PATH . 'includes/class-advanced-import-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ADVANCED_IMPORT_PATH . 'includes/class-advanced-import-i18n.php';

		/**
		 * The class responsible for defining common functions
		 * of the plugin.
		 */
		require_once ADVANCED_IMPORT_PATH . 'includes/functions-advanced-import.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ADVANCED_IMPORT_PATH . 'admin/class-advanced-import-tracking.php';
		require_once ADVANCED_IMPORT_PATH . 'admin/class-advanced-import-admin.php';
		require_once ADVANCED_IMPORT_PATH . 'admin/class-elementor-import.php';

		/**
		 * The class responsible for WordPress rset
		 */
		require_once ADVANCED_IMPORT_PATH . 'admin/class-reset.php';

		/*Theme Specific Setting*/
		require_once ADVANCED_IMPORT_PATH . 'includes/class-theme-template-library-base.php';

		require_once ADVANCED_IMPORT_PATH . 'includes/theme-template-library/cosmoswp.php'; /*cosmoswp*/
		require_once ADVANCED_IMPORT_PATH . 'includes/theme-template-library/acmethemes.php'; /*acmethemes*/

		$this->loader = new Advanced_Import_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Advanced_Import_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$this->plugin_i18n = new Advanced_Import_i18n();

		$this->loader->add_action( 'plugins_loaded', $this->plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->admin = advanced_import_admin();

		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );

		/*add mime types*/
		$this->loader->add_action( 'mime_types', $this->admin, 'mime_types' );

		/*add menu*/
		$this->loader->add_action( 'admin_menu', $this->admin, 'import_menu' );
		$this->loader->add_action( 'current_screen', $this->admin, 'help_tabs' );

		/*ajax process*/
		$this->loader->add_action( 'wp_ajax_advanced_import_ajax_setup', $this->admin, 'upload_zip' );
		$this->loader->add_action( 'wp_ajax_demo_download_and_unzip', $this->admin, 'demo_download_and_unzip' );
		$this->loader->add_action( 'wp_ajax_plugin_screen', $this->admin, 'plugin_screen' );
		$this->loader->add_action( 'wp_ajax_install_plugin', $this->admin, 'install_plugin' );
		$this->loader->add_action( 'wp_ajax_content_screen', $this->admin, 'content_screen' );
		$this->loader->add_action( 'wp_ajax_import_content', $this->admin, 'import_content' );
		$this->loader->add_action( 'wp_ajax_complete_screen', $this->admin, 'complete_screen' );

		/*Reset Process*/
		$this->loader->add_action( 'wp_loaded', advanced_import_reset_wordpress(), 'hide_reset_notice', -1 );
		$this->loader->add_action( 'admin_init', advanced_import_reset_wordpress(), 'reset_wizard_actions', -1 );
		$this->loader->add_action( 'admin_notices', advanced_import_reset_wordpress(), 'reset_wizard_notice', -1 );
		$this->loader->add_action( 'wp_ajax_advanced_import_before_reset', advanced_import_reset_wordpress(), 'before_reset' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Advanced_Import_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
