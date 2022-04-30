<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Advanced Import Admin class
 *
 * Collection of admin hooks
 *
 * @package Advanced_Import
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Import
 * @subpackage Advanced_Import/admin
 * @author     Addons Press <addonspress.com>
 */
class Advanced_Import_Admin {

	/**
	 * The Name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The Current theme name
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $theme_name    The Current theme name.
	 */
	public $theme_name = '';

	/**
	 * Current step
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $step    Current step.
	 */
	protected $step = '';

	/**
	 * Array of steps
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $steps    Array of steps.
	 */
	protected $steps = array();

	/**
	 * Demo lists
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $demo_lists    Array of demo lists.
	 */
	protected $demo_lists = array();

	/**
	 * Demo lists
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $demo_lists    Array of demo lists.
	 */
	protected $is_pro_active = false;

	/**
	 * Array of delayed post for late process
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $delay_posts    Array of delayed post for late process.
	 */
	private $delay_posts = array();

	/**
	 * Store logs and errors
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $logs    Store logs and errors.
	 */
	public $logs = array();

	/**
	 * Store errors
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $errors    Store errors.
	 */
	public $errors = array();

	/**
	 * Current added Menu hook_suffix
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $logs    Store logs and errors.
	 */
	public $hook_suffix;

	/**
	 * Slug of the import page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $logs    Store logs and errors.
	 */
	private $current_template_type;

	/**
	 * Slug of the import page
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $logs    Store logs and errors.
	 */
	private $current_template_url;

	/**
	 * Total requests
	 *
	 * @since    1.3.3
	 * @access   public
	 * @var      int    $total_request    Store total request for progress bar.
	 */
	private $total_request;
	private $current_request = 0;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {}

	/**
	 * Main Advanced_Import_Admin Instance
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @return object $instance Advanced_Import_Admin Instance
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication.
		static $instance = null;

		// Only run these methods if they haven't been ran previously.
		if ( null === $instance ) {
			$instance              = new Advanced_Import_Admin();
			$instance->plugin_name = ADVANCED_IMPORT_PLUGIN_NAME;
			$instance->version     = ADVANCED_IMPORT_VERSION;

			/*page slug using theme name */
			$instance->theme_name = sanitize_key( get_option( 'template' ) );

		}

		// Always return the instance.
		return $instance;
	}

	/**
	 * Check if template is available to import
	 *
	 * @since    1.0.8
	 * @param      array $item    current array of demo list.
	 * @return boolean
	 */
	public function is_template_available( $item ) {
		$is_available = false;

		/*if pro active everything is available*/
		if ( $this->is_pro_active ) {
			$is_available = true;
		} elseif ( ! isset( $item['is_pro'] ) ) {/*if is_pro not set the $item is available*/
			$is_available = true;/*template available since */
		} elseif ( isset( $item['is_pro'] ) && ! $item['is_pro'] ) {/*if is_pro not set but it is false, it will be free and avialable*/
			$is_available = true;
		}

		return (bool) apply_filters( 'advanced_import_is_template_available', $is_available, $item );
	}

	/**
	 * Check if template is available to import
	 *
	 * @since    1.0.8
	 * @param      array $item    current array of demo list.
	 * @return boolean
	 */
	public function is_pro( $item ) {
		$is_pro = false;
		if ( isset( $item['is_pro'] ) && $item['is_pro'] ) {
			$is_pro = true;
		}

		return (bool) apply_filters( 'advanced_import_is_pro', $is_pro, $item );
	}

	/**
	 * Return Template Button
	 *
	 * @since    1.0.8
	 * @param  array $item    current array of demo list.
	 * @return string
	 */
	public function template_button( $item ) {
		/*Start buffering*/
		ob_start();

		if ( $this->is_template_available( $item ) ) {
			$plugins = isset( $item['plugins'] ) && is_array( $item['plugins'] ) ? ' data-plugins="' . esc_attr( wp_json_encode( $item['plugins'] ) ) . '"' : '';
			?>
			<a class="button ai-demo-import ai-item-import is-button is-default is-primary is-large button-primary" href="#" aria-label="<?php esc_attr_e( 'Import', 'advanced-import' ); ?>" <?php echo $plugins; ?>>
				<span class="dashicons dashicons-download"></span><?php esc_html_e( 'Import', 'advanced-import' ); ?>
			</a>
			<?php
		} else {
			?>
			<a class="button is-button is-default is-primary is-large button-primary"
			   href="<?php echo esc_url( isset( $item['pro_url'] ) ? $item['pro_url'] : '#' ); ?>"
			   target="_blank"
			   aria-label="<?php esc_attr_e( 'View Pro', 'advanced-import' ); ?>">
				<span class="dashicons dashicons-awards"></span><?php esc_html_e( 'View Pro', 'advanced-import' ); ?>
			</a>
			<?php
		}

		/*removes the buffer (without printing it), and returns its content.*/
		$render_button = ob_get_clean();

		$render_button = apply_filters( 'advanced_import_template_import_button', $render_button, $item );
		return $render_button;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.8
	 * @param  string $hook_suffix    current hook.
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {
		if ( ! is_array( $this->hook_suffix ) || ! in_array( $hook_suffix, $this->hook_suffix ) ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, ADVANCED_IMPORT_URL . 'assets/css/advanced-import-admin' . ADVANCED_IMPORT_SCRIPT_PREFIX . '.css', array( 'wp-admin', 'dashicons' ), $this->version, 'all' );
		wp_enqueue_media();
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.8
	 * @param  string $hook_suffix    current hook.
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( ! is_array( $this->hook_suffix ) || ! in_array( $hook_suffix, $this->hook_suffix ) ) {
			return;
		}

		// Isotope Js.
		wp_enqueue_script(
			'isotope', // Handle.
			ADVANCED_IMPORT_URL . '/assets/library/isotope/isotope.pkgd' . ADVANCED_IMPORT_SCRIPT_PREFIX . '.js',
			array( 'jquery' ), // Dependencies, defined above.
			'3.0.6', // Version: File modification time.
			true // Enqueue the script in the footer.
		);

		// sweetalert2 Js.
		wp_enqueue_script(
			'sweetalert2', // Handle.
			ADVANCED_IMPORT_URL . '/assets/library/sweetalert2/sweetalert2.all' . ADVANCED_IMPORT_SCRIPT_PREFIX . '.js',
			array( 'jquery' ), // Dependencies, defined above.
			'3.0.6', // Version: File modification time.
			true // Enqueue the script in the footer.
		);

		wp_enqueue_script( $this->plugin_name, ADVANCED_IMPORT_URL . 'assets/js/advanced-import-admin' . ADVANCED_IMPORT_SCRIPT_PREFIX . '.js', array( 'jquery', 'masonry' ), $this->version, true );
		wp_localize_script(
			$this->plugin_name,
			'advanced_import_object',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'wpnonce' => wp_create_nonce( 'advanced_import_nonce' ),
				'text'    => array(
					'failed'        => esc_html__( 'Failed', 'advanced-import' ),
					'error'         => esc_html__( 'Error', 'advanced-import' ),
					'skip'          => esc_html__( 'Skipping', 'advanced-import' ),
					'confirmImport' => array(
						'title'             => esc_html__( 'Advanced Import! Just a step away', 'advanced-import' ),
						'html'              => sprintf(
						/* translators: 1: message 1, 2: message 2., 3: message 3., 4: message 4. */
							__( 'Importing demo data is the easiest way to setup your theme. It will allow you to quickly edit everything instead of creating content from scratch. Also, read following points before importing the demo: %1$s %2$s %3$s %4$s', 'advanced-import' ),
							'<ol><li class="warning">' . __( 'It is highly recommended to import demo on fresh WordPress installation to exactly replicate the theme demo. If no important data on your site, you can reset it from Reset Wizard at the top', 'advanced-import' ) . '</li>',
							'<li>' . __( 'No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified.', 'advanced-import' ) . '</li>',
							'<li>' . __( 'It will install the plugins required for demo and activate them. Also posts, pages, images, widgets, & other data will get imported.', 'advanced-import' ) . '</li>',
							'<li class="ai-plugin-info">' . __( 'The demo will install following plugin/s:', 'advanced-import' ) . 'ai_replace_plugins' . '</li>' .
							'<li>' . __( 'Please click on the Import button and wait, it will take some time to import the data.', 'advanced-import' ) . '</li></ol>'
						),
						'confirmButtonText' => esc_html__( 'Yes, Import Demo!', 'advanced-import' ),
						'cancelButtonText'  => esc_html__( 'Cancel', 'advanced-import' ),
						'no_plugins'        => esc_html__( 'No plugins will be installed.', 'advanced-import' ),
					),
					'confirmReset'  => array(
						'title'             => esc_html__( 'Are you sure?', 'advanced-import' ),
						'text'              => __( "You won't be able to revert this!", 'advanced-import' ),
						'confirmButtonText' => esc_html__( 'Yes, Reset', 'advanced-import' ),
						'cancelButtonText'  => esc_html__( 'Cancel', 'advanced-import' ),
						'resetting'         => esc_html__( 'Resetting! Starting WordPress in Default Mode.', 'advanced-import' ),
					),
					'resetSuccess'  => array(
						'title'             => esc_html__( 'Reset Successful', 'advanced-import' ),
						'confirmButtonText' => esc_html__( 'Ok', 'advanced-import' ),
					),
					'failedImport'  => array(
						'code'        => __( 'Error Code:', 'advanced-import' ),
						'text'        => __( 'Contact theme author or try again', 'advanced-import' ),
						'pluginError' => __( 'Your WordPress could not install the plugin correctly. You have to install the following plugin manually:', 'advanced-import' ),
						'plugin'      => __( 'Plugin:', 'advanced-import' ),
						'slug'        => __( 'Slug:', 'advanced-import' ),
					),
					'successImport' => array(
						'confirmButtonText' => esc_html__( 'Visit My Site', 'advanced-import' ),
						'cancelButtonText'  => esc_html__( 'Okay', 'advanced-import' ),
					),
				),
			)
		);
	}

	/**
	 * Adding new mime types.
	 *
	 * @access public
	 * @since    1.0.0
	 * @param  array $mimes    existing mime types.
	 * @return array
	 */
	public function mime_types( $mimes = array() ) {
		$add_mimes = array(
			'json' => 'application/json',
		);

		return array_merge( $mimes, $add_mimes );
	}

	/**
	 * Determine if the user already has theme content installed.
	 *
	 * @access public
	 */
	public function is_possible_upgrade() {
		return false;
	}

	/**
	 * Add admin menus
	 *
	 * @access public
	 */
	public function import_menu() {
		$this->hook_suffix[] = add_theme_page( esc_html__( 'Demo Import ', 'advanced-import' ), esc_html__( 'Demo Import' ), 'manage_options', 'advanced-import', array( $this, 'demo_import_screen' ) );
		$this->hook_suffix[] = add_management_page( esc_html__( 'Advanced Import', 'advanced-import' ), esc_html__( 'Advanced Import', 'advanced-import' ), 'manage_options', 'advanced-import-tool', array( $this, 'demo_import_screen' ) );
		$this->hook_suffix   = apply_filters( 'advanced_import_menu_hook_suffix', $this->hook_suffix );
	}

	/**
	 * Show the setup
	 *
	 * @access public
	 * @return void
	 */
	public function demo_import_screen() {
		do_action( 'advanced_import_before_demo_import_screen' );

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		echo '<div class="ai-body">';

		$this->get_header();

		echo '<div class="ai-content">';
		echo '<div class="ai-content-blocker hidden">';
		echo '<div class="ai-notification-title"><p>' . esc_html__( 'Processing... Please do not refresh this page or do not go to other url!', 'advanced-import' ) . '</p></div>';
		echo '<div id="ai-demo-popup"></div>';
		echo '</div>';
		$this->init_demo_import();
		echo '</div>';
		echo '</div>';/*ai-body*/
		do_action( 'advanced_import_after_demo_import_screen' );

	}

	/**
	 * Get header of setup
	 *
	 * @access public
	 * @return void
	 */
	public function get_header() {
		global $pagenow;

		$welcome_msg = "<div class='ai-header'>";
		/* translators: 1: current theme */
		$welcome_msg .= '<h1>' . sprintf( esc_html__( 'Welcome to the Advanced Import for %s.', 'advanced-import' ), wp_get_theme() ) . '</h1>';
		if ( $pagenow != 'tools.php' ) {
			/* translators: 1: current theme */
			$welcome_msg .= ' <p>' . sprintf( esc_html__( 'Thank you for choosing the %s theme. This quick demo import setup will help you configure your new website like theme demo. It will install the required WordPress plugins, default content and tell you a little about Help &amp; Support options. It should only take less than 5 minutes.', 'advanced-import' ), wp_get_theme() ) . '</p>';
		}
		$welcome_msg .= '</div>';
		echo advanced_import_allowed_html( apply_filters( 'advanced_import_welcome_message', $welcome_msg ) );

		if ( get_theme_mod( 'advanced_import_setup_complete', false ) && $pagenow != 'tools.php' ) {
			?>
			<p><?php esc_html_e( 'It looks like you have already run the demo import for this theme.', 'advanced-import' ); ?></p>
			<?php
		}
	}

	/**
	 * Handle the demo content upload and called to process
	 * Ajax callback
	 *
	 * @return void
	 */
	public function upload_zip() {
		if ( isset( $_FILES['ai-upload-zip-archive']['name'] ) && ! empty( $_FILES['ai-upload-zip-archive']['name'] ) ) {
			/*check for security*/
			if ( ! current_user_can( 'upload_files' ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
					)
				);
			}
			check_admin_referer( 'advanced-import' );

			/*file process*/
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global  $wp_filesystem;
			$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
			$upload_zip_archive = $_FILES['ai-upload-zip-archive'];
			$unzipfile          = unzip_file( $upload_zip_archive['tmp_name'], ADVANCED_IMPORT_TEMP );
			if ( is_wp_error( $unzipfile ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Error on unzipping, Please try again', 'advanced-import' ),
					)
				);
			}
			/*(check) Zip should contain json file and uploads folder only*/
			$dirlist = $wp_filesystem->dirlist( ADVANCED_IMPORT_TEMP );
			foreach ( (array) $dirlist as $filename => $fileinfo ) {
				if ( $fileinfo['type'] == 'd' ) {
					if ( $filename == 'uploads' ) {
						continue;
					} else {
						$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Invalid Zip File', 'advanced-import' ),
							)
						);
					}
				} else {
					$filetype = wp_check_filetype( $filename, $this->mime_types() );
					if ( empty( $filetype['ext'] ) || $filetype['ext'] != 'json' ) {
						$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Invalid Zip File', 'advanced-import' ),
							)
						);
					}
				}
			}
			wp_send_json_success(
				array(
					'message' => esc_html__( 'Success', 'advanced-import' ),
				)
			);
		}
		wp_send_json_error(
			array(
				'message' => esc_html__( 'No Zip File Found', 'advanced-import' ),
			)
		);
	}

	/**
	 * Download Zip file/ Move it to temp import folder
	 * Ajax callback
	 *
	 * @return mixed
	 */
	public function demo_download_and_unzip() {

		/*check for security*/
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
				)
			);
		}
		check_admin_referer( 'advanced-import' );

		/*get file and what should do*/
		$demo_file      = is_array( $_POST['demo_file'] ) ? (array) $_POST['demo_file'] : sanitize_text_field( $_POST['demo_file'] );
		$demo_file_type = sanitize_text_field( $_POST['demo_file_type'] );

		/*from file*/
		if ( $demo_file_type == 'file' ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global  $wp_filesystem;
			$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
			$unzipfile = unzip_file( $demo_file, ADVANCED_IMPORT_TEMP );
			if ( is_wp_error( $unzipfile ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Error on unzipping, Please try again', 'advanced-import' ),
					)
				);
			}
			/*(check) Zip should contain json file and uploads folder only*/
			$dirlist = $wp_filesystem->dirlist( ADVANCED_IMPORT_TEMP );
			foreach ( (array) $dirlist as $filename => $fileinfo ) {
				if ( $fileinfo['type'] == 'd' ) {
					if ( $filename == 'uploads' ) {
						continue;
					} else {
						$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Invalid Zip File', 'advanced-import' ),
							)
						);
					}
				} else {
					$filetype = wp_check_filetype( $filename, $this->mime_types() );
					if ( empty( $filetype['ext'] ) || $filetype['ext'] != 'json' ) {
						$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Invalid Zip File', 'advanced-import' ),
							)
						);
					}
				}
			}
			wp_send_json_success(
				array(
					'message' => esc_html__( 'Success', 'advanced-import' ),
				)
			);
		} elseif ( $demo_file_type == 'url' ) {

			/*finally fetch the file from remote*/
			$response = wp_remote_get( $demo_file );

			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
				global $wp_filesystem;
				$file_permission = 0777;
				if ( ! file_exists( ADVANCED_IMPORT_TEMP_ZIP ) ) {
					$wp_filesystem->mkdir( ADVANCED_IMPORT_TEMP_ZIP, $file_permission, true );
				}
				$temp_import_zip = trailingslashit( ADVANCED_IMPORT_TEMP_ZIP ) . 'temp-import.zip';
				$wp_filesystem->put_contents( $temp_import_zip, $response['body'], 0777 );

			} else {
				/*required to download file failed.*/
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__( 'Remote server did not respond, Contact to Theme Author', 'advanced-import' ),
					)
				);
			}

			$file_size = filesize( $temp_import_zip );

			/*if file size is 0*/
			if ( 0 == $file_size ) {
				$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP_ZIP, true );
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__( 'Zero size file downloaded, Contact to Theme Author', 'advanced-import' ),
					)
				);
			}

			/*if file is too large*/
			if ( ! empty( $max_size ) && $file_size > $max_size ) {
				$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP_ZIP, true );
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => sprintf( esc_html__( 'Remote file is too large, limit is %s', 'advanced-import' ), size_format( $max_size ) ),
					)
				);
			}

			/*if we are here then unzip file*/
			$unzipfile = unzip_file( $temp_import_zip, ADVANCED_IMPORT_TEMP );
			if ( is_wp_error( $unzipfile ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__( 'Error on unzipping, Please try again', 'advanced-import' ),
					)
				);
			}
			$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP_ZIP, true );

			/*(check) Zip should contain json file and uploads folder only*/
			$dirlist = $wp_filesystem->dirlist( ADVANCED_IMPORT_TEMP );
			foreach ( (array) $dirlist as $filename => $fileinfo ) {
				if ( $fileinfo['type'] == 'd' ) {
					if ( $filename == 'uploads' ) {
						continue;
					} else {
						$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Invalid Zip File', 'advanced-import' ),
							)
						);
					}
				} else {
					$filetype = wp_check_filetype( $filename, $this->mime_types() );
					if ( empty( $filetype['ext'] ) || $filetype['ext'] != 'json' ) {
						$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );
						wp_send_json_error(
							array(
								'message' => esc_html__( 'Invalid Zip File', 'advanced-import' ),
							)
						);
					}
				}
			}
			wp_send_json_success(
				array(
					'message' => esc_html__( 'Success', 'advanced-import' ),
				)
			);

		} else {
			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'File not allowed', 'advanced-import' ),
				)
			);
		}
	}

	/**
	 * List Demo List
	 *
	 * @return void
	 */
	public function demo_list( $demo_lists, $total_demo ) {
		?>
		<div class="ai-filter-header">
			<div class="ai-filter-tabs">
				<ul class="ai-types ai-filter-group" data-filter-group="secondary">
					<li class="ai-filter-btn-active ai-filter-btn ai-type-filter" data-filter="*">
						<?php esc_html_e( 'All', 'advanced-import' ); ?>
						<span class="ai-count"></span>
					</li>
					<?php
					$types        = array_column( $demo_lists, 'type' );
					$unique_types = array_unique( $types );
					foreach ( $unique_types as $cat_index => $single_type ) {
						?>
						<li class="ai-filter-btn ai-type-filter" data-filter=".<?php echo strtolower( esc_attr( $single_type ) ); ?>">
							<?php echo ucfirst( esc_html( $single_type ) ); ?>
							<span class="ai-count"></span>

						</li>
						<?php
					}
					?>
				</ul>
				<div class="ai-search-control">
					<input id="ai-filter-search-input" class="ai-search-filter" type="text" placeholder="<?php esc_attr_e( 'Search...', 'advanced-import' ); ?>">
				</div>
				<ul class="ai-form-type">
					<li class="ai-form-file-import">
						<?php esc_html_e( 'Upload zip', 'advanced-import' ); ?>
					</li>
				</ul>
			</div>
		</div>
		<div class="ai-filter-content" id="ai-filter-content">
			<div class="ai-actions ai-sidebar">
				<div class="ai-import-available-categories">
					<h3><?php esc_html_e( 'Categories', 'advanced-import' ); ?></h3>
					<div class="ai-import-fp-wrap">
						<ul class="ai-import-fp-lists ai-filter-group" data-filter-group="pricing">
							<li class="ai-fp-filter ai-filter-btn ai-filter-btn-active" data-filter="*">All</li>
							<li class="ai-fp-filter ai-filter-btn" data-filter=".ai-fp-filter-free">Free</li>
							<li class="ai-fp-filter ai-filter-btn" data-filter=".ai-fp-filter-pro">Pro</li>
						</ul>
					</div>
					<ul class="ai-import-available-categories-lists ai-filter-group" data-filter-group="primary">
						<li class="ai-filter-btn-active ai-filter-btn" data-filter="*">
							<?php esc_html_e( 'All Categories', 'advanced-import' ); ?>
							<span class="ai-count"></span>
						</li>
						<?php
						$categories        = array_column( $demo_lists, 'categories' );
						$unique_categories = array();
						if ( is_array( $categories ) && ! empty( $categories ) ) {
							foreach ( $categories as $demo_index => $demo_cats ) {
								foreach ( $demo_cats as $cat_index => $single_cat ) {
									if ( in_array( $single_cat, $unique_categories ) ) {
										continue;
									}
									$unique_categories[] = $single_cat;
									?>
									<li class="ai-filter-btn" data-filter=".<?php echo strtolower( esc_attr( $single_cat ) ); ?>">
										<?php echo ucfirst( esc_html( $single_cat ) ); ?>
										<span class="ai-count"></span>
									</li>
									<?php
								}
							}
						}
						?>
					</ul>
				</div>

			</div>
			<div class="ai-filter-content-wrapper">
				<?php
				foreach ( $demo_lists as $key => $demo_list ) {

					/*Check for required fields*/
					if ( ! isset( $demo_list['title'] ) || ! isset( $demo_list['screenshot_url'] ) || ! isset( $demo_list['demo_url'] ) ) {
						continue;
					}

					$template_url = isset( $demo_list['template_url'] ) ? $demo_list['template_url'] : '';
					if ( is_array( $template_url ) ) {
						$data_template      = 'data-template_url="' . esc_attr( wp_json_encode( $template_url ) ) . '"';
						$data_template_type = 'data-template_type="array"';
					} elseif ( $template_url ) {
						$data_template = 'data-template_url="' . esc_attr( $template_url ) . '"';
						if ( is_file( $template_url ) && filesize( $template_url ) > 0 ) {
							$data_template_type = 'data-template_type="file"';
						} else {
							$data_template_type = 'data-template_type="url"';
						}
					} else {
						$data_template      = 'data-template_url="' . esc_attr( wp_json_encode( $template_url ) ) . '"';
						$data_template_type = 'data-template_type="array"';
					}
					?>
					<div aria-label="<?php echo esc_attr( $demo_list['title'] ); ?>"
						 class="ai-item
					<?php
						 echo isset( $demo_list['categories'] ) ? esc_attr( implode( ' ', $demo_list['categories'] ) ) : '';
						 echo isset( $demo_list['type'] ) ? ' ' . esc_attr( $demo_list['type'] ) : '';
						 echo $this->is_pro( $demo_list ) ? ' ai-fp-filter-pro' : ' ai-fp-filter-free';
						 echo $this->is_template_available( $demo_list ) ? '' : ' ai-pro-item'
					?>
					"
						<?php echo $this->is_template_available( $demo_list ) ? $data_template . ' ' . $data_template_type : ''; ?>
					>
						<?php
						wp_nonce_field( 'advanced-import' );
						?>
						<div class="ai-item-preview">
							<div class="ai-item-screenshot">
								<img src="<?php echo esc_url( $demo_list['screenshot_url'] ); ?>">

							</div>
							<h4 class="ai-author-info"><?php esc_html_e( 'Author: ', 'advanced-import' ); ?><?php echo esc_html( isset( $demo_list['author'] ) ? $demo_list['author'] : wp_get_theme()->get( 'Author' ) ); ?></h4>
							<div class="ai-details"><?php esc_html_e( 'Details', 'advanced-import' ); ?></div>
							<?php
							if ( $this->is_pro( $demo_list ) ) {
								?>
								<span class="ai-premium-label"><?php esc_html_e( 'Premium', 'advanced-import' ); ?></span>
								<?php
							}
							?>
						</div>
						<div class="ai-item-footer">
							<div class="ai-item-footer_meta">
								<h3 class="theme-name"><?php echo esc_html( $demo_list['title'] ); ?></h3>
								<div class="ai-item-footer-actions">
									<a class="button ai-item-demo-link" href="<?php echo esc_url( $demo_list['demo_url'] ); ?>" target="_blank">
										<span class="dashicons dashicons-visibility"></span><?php esc_html_e( 'Preview', 'advanced-import' ); ?>
									</a>
									<?php
									echo $this->template_button( $demo_list );
									?>
								</div>
								<?php
								$keywords = isset( $demo_list['keywords'] ) ? $demo_list['keywords'] : array();
								if ( ! empty( $keywords ) ) {
									echo '<ul class="ai-keywords hidden">';
									foreach ( $keywords as $cat_index => $single_keywords ) {
										?>
										<li class="<?php echo strtolower( esc_attr( $single_keywords ) ); ?>"><?php echo ucfirst( esc_html( $single_keywords ) ); ?></li>
										<?php
									}
									echo '</ul>';
								}
								?>

							</div>

						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * List Demo Form
	 *
	 * @return void
	 */

	public function demo_import_form( $total_demo = 0 ) {
		?>
		<div class="ai-form <?php echo $total_demo > 0 ? 'hidden' : ''; ?>">
			<form action="" method="post" enctype="multipart/form-data" id="ai-upload-zip-form">
				<h3 class="media-title"><?php esc_html_e( 'Upload a zip file containing demo content', 'advanced-import' ); ?> </h3>
				<div class="input-file"><input type="file" name="ai-upload-zip-archive" id="ai-upload-zip-archive" size="50" /></div>
				<p>
					<?php
					wp_nonce_field( 'advanced-import' );
					printf( __( 'Maximum upload file size: %s', 'advanced-import' ), size_format( wp_max_upload_size() ) );
					?>
				</p>
				<div id='ai-empty-file' class="error hidden">
					<p>
						<?php esc_html_e( 'Select File and Try Again!', 'advanced-import' ); ?>
					</p>
				</div>
				<p class="ai-form-import-actions step">
					<button class="button-primary button button-large button-upload-demo" type="submit">
						<?php esc_html_e( 'Upload Demo Zip', 'advanced-import' ); ?>
					</button>
					<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>" class="button button-large">
						<?php esc_html_e( 'Not right now', 'advanced-import' ); ?>
					</a>
				</p>
				<div id='ai-ajax-install-result'></div>
			</form>
		</div>
		<?php
	}

	/**
	 * 1st step of demo import view
	 * Upload Zip file
	 * Demo List
	 */
	public function init_demo_import() {

		global $pagenow;
		$total_demo = 0;
		if ( $pagenow != 'tools.php' ) {
			$this->demo_lists    = apply_filters( 'advanced_import_demo_lists', array() );
			$this->is_pro_active = apply_filters( 'advanced_import_is_pro_active', $this->is_pro_active );
			$demo_lists          = $this->demo_lists;

			$total_demo = is_array( $demo_lists ) ? count( $demo_lists ) : 0;
			if ( $total_demo >= 1 ) {
				$this->demo_list( $demo_lists, $total_demo );
			}
		}

		$this->demo_import_form( $total_demo );
	}

	/**
	 * 2nd step Plugin Installation step View
	 * return void || boolean
	 */
	public function plugin_screen() {

		/*check for security*/
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
				)
			);
		}

		check_admin_referer( 'advanced-import' );

		/*for safety: delete_transient();*/
		$this->reset_transient();

		do_action( 'advanced_import_before_plugin_screen' );
		?>
		<div class="ai-notification-title">
			<p><?php esc_html_e( 'Your website needs a few essential plugins. We are installing them...', 'advanced-import' ); ?></p>
		</div>
		<ul class="ai-plugins-wrap hidden">
			<?php
			$recommended_plugins = isset( $_POST['recommendedPlugins'] ) ? (array) $_POST['recommendedPlugins'] : array();
			if ( count( $recommended_plugins ) ) {
				foreach ( $recommended_plugins as $index => $recommended_plugin ) {
					?>
					<li data-slug="<?php echo esc_attr( $recommended_plugin['slug'] ); ?>" data-main_file ="<?php echo esc_attr( isset( $recommended_plugin['main_file'] ) ? $recommended_plugin['main_file'] : $recommended_plugin['slug'] . '.php' ); ?>">
						<?php echo esc_html( $recommended_plugin['name'] ); ?>
					</li>
					<?php
				}
			} else {
				?>
				<li id="ai-no-recommended-plugins"><?php esc_html_e( 'No Recommended Plugins', 'advanced-import' ); ?></li>
				<?php
			}
			?>
		</ul>
		<?php
		do_action( 'advanced_import_after_plugin_screen' );
		exit;
	}

	/**
	 * 3rd steps helper functions
	 * Get json from json file
	 *
	 * @param string $file file url.
	 * @return mixed
	 */
	public function get_json_data_from_file( $file ) {

		if ( get_transient( $file ) ) {
			return get_transient( $file );
		}

		if ( $this->current_template_type == 'array' ) {
			$type = strtok( $file, '.' );
			if ( isset( $this->current_template_url[ $type ] ) ) {
				$request  = wp_remote_get( $this->current_template_url[ $type ] );
				$response = wp_remote_retrieve_body( $request );
				$result   = json_decode( $response, true );
				set_transient( $file, $result, 1000 );
				return $result;
			}
			return array();
		}

		if ( is_file( ADVANCED_IMPORT_TEMP . basename( $file ) ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;
			$file_name = ADVANCED_IMPORT_TEMP . basename( $file );
			if ( file_exists( $file_name ) ) {
				$result = json_decode( $wp_filesystem->get_contents( $file_name ), true );
				set_transient( $file, $result, 1000 );
				return $result;
			}
		}
		return array();
	}

	public function get_main_content_json() {
		return $this->get_json_data_from_file( 'content.json' );
	}
	public function get_widgets_json() {
		return $this->get_json_data_from_file( 'widgets.json' );
	}

	public function get_theme_options_json() {
		return $this->get_json_data_from_file( 'options.json' );
	}

	/*
	 * return array
	 */
	private function advanced_import_setup_content_steps() {

		$total_content = 0;

		$content = array();

		/*check if there is files*/
		$content_data = $this->get_main_content_json();
		foreach ( $content_data as $post_type => $post_data ) {
			if ( count( $post_data ) ) {
				$total_content        += count( $post_data );
				$first                 = current( $post_data );
				$post_type_title       = ! empty( $first['type_title'] ) ? $first['type_title'] : ucwords( $post_type ) . 's';
				$content[ $post_type ] = array(
					'title'            => $post_type_title,
					'description'      => sprintf( esc_html__( 'This will create default %s as seen in the demo.', 'advanced-import' ), $post_type_title ),
					'pending'          => esc_html__( 'Pending.', 'advanced-import' ),
					'installing'       => esc_html__( 'Installing.', 'advanced-import' ),
					'success'          => esc_html__( 'Success.', 'advanced-import' ),
					'install_callback' => array( $this, 'import_content_post_type_data' ),
					'checked'          => $this->is_possible_upgrade() ? 0 : 1,
					// dont check if already have content installed.
				);
			}
		}
		/*
		array adjustment
		TODO : Remove it after adjustment on Advanced Import
		*/
		/*Put post 3nd last*/
		$post = isset( $content['post'] ) ? $content['post'] : array();
		if ( $post ) {
			unset( $content['post'] );
			$content['post'] = $post;
		}
		/*Put page 2nd last*/
		$page = isset( $content['page'] ) ? $content['page'] : array();
		if ( $page ) {
			unset( $content['page'] );
			$content['page'] = $page;

		}
		/*Put nav last*/
		$nav = isset( $content['nav_menu_item'] ) ? $content['nav_menu_item'] : array();
		if ( $nav ) {
			unset( $content['nav_menu_item'] );
			$content['nav_menu_item'] = $nav;
		}
		/*check if there is files*/
		$widget_data = $this->get_widgets_json();
		if ( ! empty( $widget_data ) ) {
			$total_content += 1;

			$content['widgets'] = array(
				'title'            => esc_html__( 'Widgets', 'advanced-import' ),
				'description'      => esc_html__( 'Insert default sidebar widgets as seen in the demo.', 'advanced-import' ),
				'pending'          => esc_html__( 'Pending.', 'advanced-import' ),
				'installing'       => esc_html__( 'Installing Default Widgets.', 'advanced-import' ),
				'success'          => esc_html__( 'Success.', 'advanced-import' ),
				'install_callback' => array( $this, 'import_content_widgets_data' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				// dont check if already have content installed.
			);
		}
		$options_data = $this->get_theme_options_json();
		if ( ! empty( $options_data ) ) {
			$total_content      += 1;
			$content['settings'] = array(
				'title'            => esc_html__( 'Settings', 'advanced-import' ),
				'description'      => esc_html__( 'Configure default settings.', 'advanced-import' ),
				'pending'          => esc_html__( 'Pending.', 'advanced-import' ),
				'installing'       => esc_html__( 'Installing Default Settings.', 'advanced-import' ),
				'success'          => esc_html__( 'Success.', 'advanced-import' ),
				'install_callback' => array( $this, 'import_menu_and_options' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				// dont check if already have content installed.
			);
		}
		$content = apply_filters( $this->theme_name . '_theme_view_setup_step_content', $content );

		$this->total_request = $total_content;
		return $content;

	}

	/**
	 * 3rd Step Step for content, widget, setting import
	 * Page setup
	 */
	public function content_screen() {

		/*check for security*/
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
				)
			);
		}
		check_admin_referer( 'advanced-import' );

		if ( isset( $_POST['template_url'] ) ) {
			$this->current_template_url  = is_array( $_POST['template_url'] ) ? (array) $_POST['template_url'] : sanitize_text_field( $_POST['template_url'] );
			$this->current_template_type = sanitize_text_field( $_POST['template_type'] );
		}

		do_action( 'advanced_import_before_content_screen' );

		?>
		<div class="ai-notification-title">
			<p>
				<?php
				esc_html_e( 'It\'s time to insert some demo content for your new WordPress Site. Once inserted, this content can be managed from the WordPress admin dashboard. ' )
				?>
			</p>
		</div>

		<table class="ai-pages hidden">
			<thead>
			<tr>
				<th class="check"></th>
				<th class="item"><?php esc_html_e( 'Item', 'advanced-import' ); ?></th>
				<th class="description"><?php esc_html_e( 'Description', 'advanced-import' ); ?></th>
				<th class="status"><?php esc_html_e( 'Status', 'advanced-import' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			$setup_content_steps = $this->advanced_import_setup_content_steps();
			foreach ( $setup_content_steps as $slug => $default ) {
				?>
				<tr class="ai-available-content" data-content="<?php echo esc_attr( $slug ); ?>">
					<td>
						<input type="checkbox" name="import_content[<?php echo esc_attr( $slug ); ?>]" class="ai-available-content" id="import_content_<?php echo esc_attr( $slug ); ?>" value="1" <?php echo ( ! isset( $default['checked'] ) || $default['checked'] ) ? ' checked' : ''; ?>>
					</td>
					<td>
						<label for="import_content_<?php echo esc_attr( $slug ); ?>">
							<?php echo esc_html( $default['title'] ); ?>
						</label>
					</td>
					<td class="description">
						<?php echo esc_html( $default['description'] ); ?>
					</td>
					<td class="status">
						<span>
							<?php echo esc_html( $default['pending'] ); ?>
						</span>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
		do_action( 'advanced_import_after_content_screen' );

		exit;
	}

	/*
	* import ajax content
	* return JSON
	*/
	public function import_content() {

		/*check for security*/
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
				)
			);
		}
		if ( isset( $_POST['template_url'] ) ) {
			$this->current_template_url  = is_array( $_POST['template_url'] ) ? (array) $_POST['template_url'] : sanitize_text_field( $_POST['template_url'] );
			$this->current_template_type = sanitize_text_field( $_POST['template_type'] );
		}

		/*Move to Trash default page and post*/
		$sample_page      = get_page_by_title( 'Sample Page', OBJECT, 'page' );
		$hello_world_post = get_page_by_title( 'Hello world!', OBJECT, 'post' );
		if ( is_object( $sample_page ) ) {
			wp_trash_post( $sample_page->ID );
		}
		if ( is_object( $hello_world_post ) ) {
			wp_trash_post( $hello_world_post->ID );
		}

		$content_slug = isset( $_POST['content'] ) ? sanitize_title( $_POST['content'] ) : '';

		$content = $this->advanced_import_setup_content_steps();

		/*check for security again*/
		if ( ! check_ajax_referer( 'advanced_import_nonce', 'wpnonce' ) || empty( $content_slug ) || ! isset( $content[ $content_slug ] ) ) {
			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'No content Found', 'advanced-import' ),
				)
			);
		}

		$json         = false;
		$this_content = $content[ $content_slug ];

		if ( isset( $_POST['proceed'] ) ) {

			/*install the content*/
			$this->log( esc_html__( 'Starting import for ', 'advanced-import' ) . $content_slug );

			/*init delayed posts from transient.*/
			$this->delay_posts = get_transient( 'delayed_posts' );
			if ( ! is_array( $this->delay_posts ) ) {
				$this->delay_posts = array();
			}

			if ( ! empty( $this_content['install_callback'] ) ) {
				/*
				 install_callback includes following functions
				 * import_content_post_type_data
				 * import_content_widgets_data
				 * import_menu_and_options
				 * */
				if ( $result = call_user_func( $this_content['install_callback'] ) ) {

					$this->log( esc_html__( 'Finish writing ', 'advanced-import' ) . count( $this->delay_posts, COUNT_RECURSIVE ) . esc_html__( ' delayed posts to transient ', 'advanced-import' ) );
					set_transient( 'delayed_posts', $this->delay_posts, 60 * 60 * 24 );

					/*
					if there is retry, retry it
					or finish it*/
					if ( is_array( $result ) && isset( $result['retry'] ) ) {
						/*we split the stuff up again.*/
						$json = array(
							'url'           => admin_url( 'admin-ajax.php' ),
							'action'        => 'import_content',
							'proceed'       => 'true',
							'retry'         => time(),
							'retry_count'   => $result['retry_count'],
							'content'       => $content_slug,
							'_wpnonce'      => wp_create_nonce( 'advanced_import_nonce' ),
							'message'       => $this_content['installing'],
							'logs'          => $this->logs,
							'errors'        => $this->errors,
							'template_url'  => $this->current_template_url,
							'template_type' => $this->current_template_type,

						);
					} else {
						$json = array(
							'done'    => 1,
							'message' => $this_content['success'],
							'debug'   => $result,
							'logs'    => $this->logs,
							'errors'  => $this->errors,
						);
					}
				}
			}
		} else {

			$json = array(
				'url'           => admin_url( 'admin-ajax.php' ),
				'action'        => 'import_content',
				'proceed'       => 'true',
				'content'       => $content_slug,
				'_wpnonce'      => wp_create_nonce( 'advanced_import_nonce' ),
				'message'       => $this_content['installing'],
				'logs'          => $this->logs,
				'errors'        => $this->errors,
				'template_url'  => $this->current_template_url,
				'template_type' => $this->current_template_type,
			);
		}

		if ( $json ) {
			$json['hash'] = md5( serialize( $json ) ); /*used for checking if duplicates happen, move to next plugin*/
			wp_send_json( $json );
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Error', 'advanced-import' ),
					'logs'    => $this->logs,
					'errors'  => $this->errors,
				)
			);
		}
		exit;
	}

	/*
	 callback function to importing post type
	 * all post type is imported from here
	 * return mix
	 * */
	private function import_content_post_type_data() {
		$post_type = ! empty( $_POST['content'] ) ? sanitize_text_field( $_POST['content'] ) : false;
		$all_data  = $this->get_main_content_json();
		if ( ! $post_type || ! isset( $all_data[ $post_type ] ) ) {
			return false;
		}

		/*Import 10 posts at a time*/
		$limit = 10 + ( isset( $_REQUEST['retry_count'] ) ? (int) $_REQUEST['retry_count'] : 0 );

		$limit = apply_filters( 'advanced_import_limit_at_time', $limit );
		$x     = 0;
		foreach ( $all_data[ $post_type ] as $post_data ) {

			$this->process_import_single_post( $post_type, $post_data );

			if ( $x ++ > $limit ) {
				return array(
					'retry'       => 1,
					'retry_count' => $limit,
				);
			}
		}

		/*processed delayed posts*/
		$this->process_delayed_posts();

		/*process child posts*/
		$this->processpost_orphans();

		return true;

	}

	/*
	set and get transient imported_term_ids
	return mix*/
	public function imported_term_id( $original_term_id, $new_term_id = false ) {
		$terms = get_transient( 'imported_term_ids' );
		if ( ! is_array( $terms ) ) {
			$terms = array();
		}
		if ( $new_term_id ) {
			if ( ! isset( $terms[ $original_term_id ] ) ) {
				$this->log( esc_html__( 'Insert old TERM ID ', 'advanced-import' ) . $original_term_id . esc_html__( ' as new TERM ID: ', 'advanced-import' ) . $new_term_id );
			} elseif ( $terms[ $original_term_id ] != $new_term_id ) {
				$this->error( 'Replacement OLD TERM ID ' . $original_term_id . ' overwritten by new TERM ID: ' . $new_term_id );
			}
			$terms[ $original_term_id ] = $new_term_id;
			set_transient( 'imported_term_ids', $terms, 60 * 60 * 24 );
		} elseif ( $original_term_id && isset( $terms[ $original_term_id ] ) ) {
			return $terms[ $original_term_id ];
		}

		return false;
	}

	/*
	set and get imported_post_ids
	return mix*/
	public function imported_post_id( $original_id = false, $new_id = false ) {
		if ( is_array( $original_id ) || is_object( $original_id ) ) {
			return false;
		}
		$post_ids = get_transient( 'imported_post_ids' );
		if ( ! is_array( $post_ids ) ) {
			$post_ids = array();
		}
		if ( $new_id ) {
			if ( ! isset( $post_ids[ $original_id ] ) ) {
				$this->log( esc_html__( 'Insert old ID ', 'advanced-import' ) . $original_id . esc_html__( ' as new ID: ', 'advanced-import' ) . $new_id );
			} elseif ( $post_ids[ $original_id ] != $new_id ) {
				$this->error( esc_html__( 'Replacement OLD ID ', 'advanced-import' ) . $original_id . ' overwritten by new ID: ' . $new_id );
			}
			$post_ids[ $original_id ] = $new_id;
			set_transient( 'imported_post_ids', $post_ids, 60 * 60 * 24 );
		} elseif ( $original_id && isset( $post_ids[ $original_id ] ) ) {
			return $post_ids[ $original_id ];
		} elseif ( $original_id === false ) {
			return $post_ids;
		}
		return false;
	}

	/*
	set and get post_orphans/post parent
	if parent is not already imported the child will be orphan
	return mix*/
	private function post_orphans( $original_id = false, $missing_parent_id = false ) {
		$post_ids = get_transient( 'post_orphans' );
		if ( ! is_array( $post_ids ) ) {
			$post_ids = array();
		}
		if ( $missing_parent_id ) {
			$post_ids[ $original_id ] = $missing_parent_id;
			set_transient( 'post_orphans', $post_ids, 60 * 60 * 24 );
		} elseif ( $original_id && isset( $post_ids[ $original_id ] ) ) {
			return $post_ids[ $original_id ];
		} elseif ( $original_id === false ) {
			return $post_ids;
		}
		return false;
	}


	/*set delayed post for later process*/
	private function delay_post_process( $post_type, $post_data ) {
		if ( ! isset( $this->delay_posts[ $post_type ] ) ) {
			$this->delay_posts[ $post_type ] = array();
		}
		$this->delay_posts[ $post_type ][ $post_data['post_id'] ] = $post_data;
	}


	/*
	Important Function
	Import single Post/Content
	*/
	private function process_import_single_post( $post_type, $post_data, $delayed = 0 ) {

		$this->current_request = $this->current_request + 1;

		$this->log( esc_html__( 'Processing ', 'advanced-import' ) . $post_type . ' ' . $post_data['post_id'] );
		$original_post_data = $post_data;

		/*if there is not post type return false*/
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		/*if it is aready imported return*/
		if ( $this->imported_post_id( $post_data['post_id'] ) ) {
			return true; /*already done*/
		}

		/*set post_name id for empty post name/title*/
		if ( empty( $post_data['post_title'] ) && empty( $post_data['post_name'] ) ) {
			$post_data['post_name'] = $post_data['post_id'];
		}

		/*set post_type on $post_data*/
		$post_data['post_type'] = $post_type;

		/*post_orphans/post parent management */
		$post_parent = isset( $post_data['post_parent'] ) ? absint( $post_data['post_parent'] ) : false;
		if ( $post_parent ) {
			/*if we already know the parent, map it to the new local imported ID*/
			if ( $this->imported_post_id( $post_parent ) ) {
				$post_data['post_parent'] = $this->imported_post_id( $post_parent );
			} else {
				/*if there is not parent imported, child will be orphans*/
				$this->post_orphans( absint( $post_data['post_id'] ), $post_parent );
				$post_data['post_parent'] = 0;
			}
		}

		/*check if already exists by post_name*/
		if ( empty( $post_data['post_title'] ) && ! empty( $post_data['post_name'] ) ) {
			global $wpdb;
			$sql   = "
					SELECT ID, post_name, post_parent, post_type
					FROM $wpdb->posts
					WHERE post_name = %s
					AND post_type = %s
				";
			$pages = $wpdb->get_results(
				$wpdb->prepare(
					$sql,
					array(
						$post_data['post_name'],
						$post_type,
					)
				),
				OBJECT_K
			);

			$foundid = 0;
			foreach ( (array) $pages as $page ) {
				if ( $page->post_name == $post_data['post_name'] && empty( $page->post_title ) ) {
					$foundid = $page->ID;
				}
			}

			/*if we have found id by post_name, imported_post_id and return*/
			if ( $foundid ) {
				$this->imported_post_id( $post_data['post_id'], $foundid );
				return true;
			}
		}

		/*
		check if already exists by post_name and post_title*/
		/*don't use post_exists because it will dupe up on media with same name but different slug*/
		if ( ! empty( $post_data['post_title'] ) && ! empty( $post_data['post_name'] ) ) {
			global $wpdb;
			$sql   = "
                        SELECT ID, post_name, post_parent, post_type
                        FROM $wpdb->posts
                        WHERE post_name = %s
                        AND post_title = %s
                        AND post_type = %s
					";
			$pages = $wpdb->get_results(
				$wpdb->prepare(
					$sql,
					array(
						$post_data['post_name'],
						$post_data['post_title'],
						$post_type,
					)
				),
				OBJECT_K
			);

			$foundid = 0;
			foreach ( (array) $pages as $page ) {
				if ( $page->post_name == $post_data['post_name'] ) {
					$foundid = $page->ID;
				}
			}

			/*if we have found id by post_name and post_title, imported_post_id and return*/
			$elementor_library_id = '';
			if ( $foundid && 'elementor_library' != $post_type ) {
				$this->imported_post_id( $post_data['post_id'], $foundid );
				return true;
			}
			if ( $foundid && 'elementor_library' == $post_type ) {
				$elementor_library_id = $foundid;
			}
		}

		/*
		 * todo it may not required
		 * backwards compat with old import format.*/
		if ( isset( $post_data['meta'] ) ) {
			foreach ( $post_data['meta'] as $key => $meta ) {
				if ( is_array( $meta ) && count( $meta ) == 1 ) {
					$single_meta = current( $meta );
					if ( ! is_array( $single_meta ) ) {
						$post_data['meta'][ $key ] = $single_meta;
					}
				}
			}
		}

		/*finally process*/
		switch ( $post_type ) {

			/*case attachment*/
			case 'attachment':
				/*import media via url*/
				if ( isset( $post_data['guid'] ) && ! empty( $post_data['guid'] ) ) {

					/*check if this has already been imported.*/
					$old_guid = $post_data['guid'];
					if ( $this->imported_post_id( $old_guid ) ) {
						return true; /*already done*/
					}

					// ignore post parent, we haven't imported those yet.
					// $file_data = wp_remote_get($post_data['guid']);
					$remote_url = $post_data['guid'];

					$post_data['upload_date'] = date( 'Y/m', strtotime( $post_data['post_date_gmt'] ) );

					if ( isset( $post_data['meta'] ) ) {
						foreach ( $post_data['meta'] as $key => $meta ) {
							if ( $key == '_wp_attached_file' ) {
								foreach ( (array) $meta as $meta_val ) {
									if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta_val, $matches ) ) {
										$post_data['upload_date'] = $matches[0];
									}
								}
							}
						}
					}

					/*upload the file*/
					$upload = $this->import_image_and_file( $remote_url, $post_data );

					/*if error on upload*/
					if ( ! is_array( $upload ) || is_wp_error( $upload ) ) {
						/*todo: error*/
						return false;
					}

					/*check file type, if file type not found return false*/
					if ( $info = wp_check_filetype( $upload['file'] ) ) {
						$post_data['post_mime_type'] = $info['type'];
					} else {
						return false;
					}

					/*set guid file url*/
					$post_data['guid'] = $upload['url'];

					/*
					 * insert attachment
					 *https://developer.wordpress.org/reference/functions/wp_insert_attachment/
					 * */
					$attach_id = wp_insert_attachment( $post_data, $upload['file'] );
					if ( $attach_id ) {

						/*update meta*/
						if ( ! empty( $post_data['meta'] ) ) {
							foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
								if ( $meta_key != '_wp_attached_file' && ! empty( $meta_val ) ) {
									update_post_meta( $attach_id, $meta_key, $meta_val );
								}
							}
						}
						/* Update metadata for an attachment.*/
						wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );

						/*remap resized image URLs, works by stripping the extension and remapping the URL stub.*/
						if ( preg_match( '!^image/!', $info['type'] ) ) {
							$parts = pathinfo( $remote_url );
							$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

							$parts_new = pathinfo( $upload['url'] );
							$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

							$this->imported_post_id( $parts['dirname'] . '/' . $name, $parts_new['dirname'] . '/' . $name_new );
						}
						$this->imported_post_id( $post_data['post_id'], $attach_id );
					}
				}
				break;

			case 'elementor_library':
				if ( empty( $elementor_library_id ) ) {
					break;
				}
				if ( ! empty( $post_data['meta'] ) && is_array( $post_data['meta'] ) ) {

					/*fix for double json encoded stuff*/
					foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
						if ( is_string( $meta_val ) && strlen( $meta_val ) && $meta_val[0] == '[' ) {
							$test_json = @json_decode( $meta_val, true );
							if ( is_array( $test_json ) ) {
								$post_data['meta'][ $meta_key ] = $test_json;
							}
						}
					}

					array_walk_recursive(
						$post_data['meta'],
						array(
							advanced_import_elementor(),
							'elementor_id_import',
						)
					);
				}
				/*do further filter if you need*/
				$post_data['post_id'] = apply_filters( 'advanced_import_post_data', $post_data );
				$post_data['ID']      = $elementor_library_id;

				/*finally insert post data*/
				$post_id = wp_update_post( $post_data, true );
				if ( ! is_wp_error( $post_id ) ) {

					/*set id on imported_post_id*/
					$this->imported_post_id( $post_data['post_id'], $post_id );

					/*add/update post meta*/
					if ( ! empty( $post_data['meta'] ) ) {
						foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
							/*if the post has a featured image, take note of this in case of remap*/
							if ( '_thumbnail_id' == $meta_key ) {
								/*find this inserted id and use that instead.*/
								$inserted_id = $this->imported_post_id( intval( $meta_val ) );
								if ( $inserted_id ) {
									$meta_val = $inserted_id;
								}
							}
							/*update meta*/
							update_post_meta( $post_id, $meta_key, $meta_val );
						}
					}
				}
				//not needed
				//update_option('elementor_active_kit', $post_id);
				if (
					defined( 'ELEMENTOR_VERSION' )
					||
					defined( 'ELEMENTOR_PRO_VERSION' )
				) {
					\Elementor\Plugin::$instance->files_manager->clear_cache();
				}
				break;

			default:
				/*Process Post Meta*/
				if ( ! empty( $post_data['meta'] ) && is_array( $post_data['meta'] ) ) {

					/*fix for double json encoded stuff*/
					foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
						if ( is_string( $meta_val ) && strlen( $meta_val ) && $meta_val[0] == '[' ) {
							$test_json = @json_decode( $meta_val, true );
							if ( is_array( $test_json ) ) {
								$post_data['meta'][ $meta_key ] = $test_json;
							}
						}
					}

					//                  array_walk_recursive( $post_data['meta'], array( advanced_import_elementor(), 'elementor_id_import' ) );

					/*todo gutenberg and page builders*/

					/*
					replace menu data
					work out what we're replacing. a tax, page, term etc..*/
					if ( isset( $post_data['meta']['_menu_item_menu_item_parent'] ) && 0 != $post_data['meta']['_menu_item_menu_item_parent'] ) {
						$new_parent_id = $this->imported_post_id( $post_data['meta']['_menu_item_menu_item_parent'] );
						if ( ! $new_parent_id ) {
							if ( $delayed ) {
								/*already delayed, unable to find this meta value, skip inserting it*/
								$this->error( esc_html__( 'Unable to find replacement. Continue anyway.... content will most likely break..', 'advanced-import' ) );
							} else {
								/*not found , delay it*/
								$this->error( esc_html__( 'Unable to find replacement. Delaying....', 'advanced-import' ) );
								$this->delay_post_process( $post_type, $original_post_data );
								return false;
							}
						}
						$post_data['meta']['_menu_item_menu_item_parent'] = $new_parent_id;
					}

					/*if _menu_item_type*/
					if ( isset( $post_data['meta']['_menu_item_type'] ) ) {

						switch ( $post_data['meta']['_menu_item_type'] ) {
							case 'post_type':
								if ( ! empty( $post_data['meta']['_menu_item_object_id'] ) ) {
									$new_parent_id = $this->imported_post_id( $post_data['meta']['_menu_item_object_id'] );
									if ( ! $new_parent_id ) {
										if ( $delayed ) {
											/*already delayed, unable to find this meta value, skip inserting it*/
											$this->error( esc_html__( 'Unable to find replacement. Continue anyway.... content will most likely break..', 'advanced-import' ) );
										} else {
											/*not found , delay it*/
											$this->error( esc_html__( 'Unable to find replacement. Delaying....', 'advanced-import' ) );
											$this->delay_post_process( $post_type, $original_post_data );
											return false;
										}
									}
									$post_data['meta']['_menu_item_object_id'] = $new_parent_id;
								}
								break;

							case 'taxonomy':
								if ( ! empty( $post_data['meta']['_menu_item_object_id'] ) ) {
									$new_parent_id = $this->imported_term_id( $post_data['meta']['_menu_item_object_id'] );
									if ( ! $new_parent_id ) {
										if ( $delayed ) {
											/*already delayed, unable to find this meta value, skip inserting it*/
											$this->error( esc_html__( 'Unable to find replacement. Continue anyway.... content will most likely break..', 'advanced-import' ) );
										} else {
											/*not found , delay it*/
											$this->error( esc_html__( 'Unable to find replacement. Delaying....', 'advanced-import' ) );
											$this->delay_post_process( $post_type, $original_post_data );
											return false;
										}
									}
									$post_data['meta']['_menu_item_object_id'] = $new_parent_id;
								}
								break;
						}
					}
				}

				/*
				post content parser
				for shortcode post id replacement*/
				$post_data['post_content'] = $this->parse_shortcode_meta_content( $post_data['post_content'] );

				$replace_tax_id_keys = array(
					'taxonomies',
				);
				foreach ( $replace_tax_id_keys as $replace_key ) {
					if ( preg_match_all( '# ' . $replace_key . '="(\d+)"#', $post_data['post_content'], $matches ) ) {
						foreach ( $matches[0] as $match_id => $string ) {
							$new_id = $this->imported_term_id( $matches[1][ $match_id ] );
							if ( $new_id ) {
								$post_data['post_content'] = str_replace( $string, ' ' . $replace_key . '="' . $new_id . '"', $post_data['post_content'] );
							} else {
								$this->error( esc_html__( 'Unable to find TAXONOMY replacement for ', 'advanced-import' ) . $replace_key . '="' . $matches[1][ $match_id ] . esc_html__( 'in content.', 'advanced-import' ) );
								if ( $delayed ) {
									/*already delayed, unable to find this meta value, skip inserting it*/
									$this->error( esc_html__( 'Unable to find replacement. Continue anyway.... content will most likely break..', 'advanced-import' ) );
								} else {
									/*not found , delay it*/
									$this->delay_post_process( $post_type, $original_post_data );
									return false;
								}
							}
						}
					}
				}

				/*do further filter if you need*/
				$post_data = apply_filters( 'advanced_import_post_data', $post_data );

				/*finally insert post data*/
				$post_id = wp_insert_post( $post_data, true );
				if ( ! is_wp_error( $post_id ) ) {

					/*set id on imported_post_id*/
					$this->imported_post_id( $post_data['post_id'], $post_id );

					/*add/update post meta*/
					if ( ! empty( $post_data['meta'] ) ) {
						foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
							/*if the post has a featured image, take note of this in case of remap*/
							if ( '_thumbnail_id' == $meta_key ) {
								/*find this inserted id and use that instead.*/
								$inserted_id = $this->imported_post_id( intval( $meta_val ) );
								if ( $inserted_id ) {
									$meta_val = $inserted_id;
								}
							} elseif ( '_elementor_data' == $meta_key ) {
								advanced_import_elementor()->elementor_data_posts( $post_id, $meta_val );
							}
							/*update meta*/
							update_post_meta( $post_id, $meta_key, $meta_val );
						}
					}

					if ( ! empty( $post_data['terms'] ) ) {
						$terms_to_set = array();
						foreach ( $post_data['terms'] as $term_slug => $terms ) {
							foreach ( $terms as $term ) {
								$taxonomy = $term['taxonomy'];
								if ( taxonomy_exists( $taxonomy ) ) {
									$term_exists = term_exists( $term['slug'], $taxonomy );
									$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
									if ( ! $term_id ) {
										if ( ! empty( $term['parent'] ) ) {
											/*see if we have imported this yet?*/
											$term['parent'] = $this->imported_term_id( $term['parent'] );
										}
										$term_id_tax_id = wp_insert_term( $term['name'], $taxonomy, $term );
										if ( ! is_wp_error( $term_id_tax_id ) ) {
											$term_id = $term_id_tax_id['term_id'];
										} else {
											// todo - error
											continue;
										}
									}
									/*set term_id on imported_term_id*/
									$this->imported_term_id( $term['term_id'], $term_id );

									/*add the term meta.*/
									if ( $term_id && ! empty( $term['meta'] ) && is_array( $term['meta'] ) ) {

										$replace_post_ids = apply_filters(
											'advanced_import_replace_post_ids',
											array(
												'image_id',
												'thumbnail_id',
												'author_picture',
											)
										);
										foreach ( $term['meta'] as $meta_key => $meta_val ) {
											// we have to replace certain meta_key/meta_val
											// e.g. thumbnail id from woocommerce product categories.

											if ( in_array( $meta_key, $replace_post_ids ) ) {

												if ( $new_meta_val = $this->imported_post_id( $meta_val ) ) {
													/*use this new id.*/
													$meta_val = $new_meta_val;
												}
											}
											update_term_meta( $term_id, $meta_key, $meta_val );
										}
									}
									$terms_to_set[ $taxonomy ][] = intval( $term_id );
								}
							}
						}
						foreach ( $terms_to_set as $tax => $ids ) {
							wp_set_post_terms( $post_id, $ids, $tax );
						}

						if ( ( isset( $post_data['meta']['_elementor_data'] ) && ! empty( $post_data['meta']['_elementor_data'] ) ) ||
							( isset( $post_data['meta']['_elementor_css'] ) && ! ! empty( $post_data['meta']['_elementor_css'] ) )
						) {
							advanced_import_elementor()->elementor_post( $post_id );
						}

						/*Gutentor*/
						$post    = get_post( $post_id );
						$content = $post->post_content;
						if ( preg_match_all( '/data-gpid="(.*?)\" /', $content, $matches ) ) {
							foreach ( $matches[0] as $match_id => $string ) {
								$content = str_replace( $matches[0][ $match_id ], 'data-gpid="' . $post_id . '" ', $content );
							}
						}
						$post->post_content = $content;
						wp_update_post( $post );
					}
				}
				break;
		}

		return true;
	}

	/*Shortcode/Meta/Post Ids fixed start*/

	/*
	 * since 1.2.3
	 *  return the difference in length between two strings
	 * */
	public function strlen_diff( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}


	/*
	* since 1.2.3
	* helper function to parse url, shortcode, post ids form provided content
	 * * currently uses on meta and post content
	 * */
	public function parse_shortcode_meta_content( $content ) {
		/*we have to format the post content. rewriting images and gallery stuff*/
		$replace = $this->imported_post_id();

		/*filters urls for replace*/
		$urls_replace = array();
		foreach ( $replace as $key => $val ) {
			if ( $key && $val && ! is_numeric( $key ) && ! is_numeric( $val ) ) {
				$urls_replace[ $key ] = $val;
			}
		}
		/*replace image/file urls*/
		if ( $urls_replace ) {
			uksort( $urls_replace, array( &$this, 'strlen_diff' ) );
			foreach ( $urls_replace as $from_url => $to_url ) {
				$content = str_replace( $from_url, $to_url, $content );
			}
		}

		/*gallery fixed*/
		if ( preg_match_all( '#\[gallery[^\]]*\]#', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match( '#ids="([^"]+)"#', $string, $ids_matches ) ) {
					$ids = explode( ',', $ids_matches[1] );
					foreach ( $ids as $key => $val ) {
						$new_id = $val ? $this->imported_post_id( $val ) : false;
						if ( ! $new_id ) {
							unset( $ids[ $key ] );
						} else {
							$ids[ $key ] = $new_id;
						}
					}
					$new_ids = implode( ',', $ids );
					$content = str_replace( $ids_matches[0], 'ids="' . $new_ids . '"', $content );
				}
			}
		}

		/*contact form 7 id fixes.*/
		if ( preg_match_all( '#\[contact-form-7[^\]]*\]#', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match( '#id="(\d+)"#', $string, $id_match ) ) {
					$new_id = $this->imported_post_id( $id_match[1] );
					if ( $new_id ) {
						$content = str_replace( $id_match[0], 'id="' . $new_id . '"', $content );
					} else {
						/*no imported ID found. remove this entry.*/
						$content = str_replace( $matches[0], '(insert contact form here)', $content );
					}
				}
			}
		}

		/*Gutentor*/
		if ( preg_match_all( '/\"pTaxTerm"(.*?)\]/', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match_all( '/\"value":(.*?)\}/', $string, $matches1 ) ) {
					foreach ( $matches1[0] as $match_id1 => $string1 ) {
						$new_id  = $this->imported_term_id( $matches1[1][ $match_id1 ] );
						$content = str_replace( $string1, '"value":' . $new_id . '}', $content );
					}
				}
			}
		}
		if ( preg_match_all( '/\"e14TaxTerm"(.*?)\]/', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match_all( '/\"value":(.*?)\}/', $string, $matches1 ) ) {
					foreach ( $matches1[0] as $match_id1 => $string1 ) {
						$new_id  = $this->imported_term_id( $matches1[1][ $match_id1 ] );
						$content = str_replace( $string1, '"value":' . $new_id . '}', $content );
					}
				}
			}
		}
		if ( preg_match_all( '/data-gpid="(.*?)\" /', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				$new_id  = $this->imported_post_id( $matches[1][ $match_id ] );
				$content = str_replace( $matches[0][ $match_id ], 'data-gpid="' . $new_id . '" ', $content );
			}
		}
		if ( preg_match_all( '/\"p4PostId"(.*?)\,/', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				$new_id  = $this->imported_post_id( $matches[1][ $match_id ] );
				$content = str_replace( $matches[0][ $match_id ], '"p4PostId":' . $new_id . ',', $content );
			}
		}
		return $content;
	}
	/*Shortcode/Meta/Post Ids fixed end*/

	/*update parent page id for child page*/
	private function processpost_orphans() {

		/*get post orphans to find it parent*/
		$orphans = $this->post_orphans();
		foreach ( $orphans as $original_post_id => $original_post_parent_id ) {
			if ( $original_post_parent_id ) {
				if ( $this->imported_post_id( $original_post_id ) && $this->imported_post_id( $original_post_parent_id ) ) {
					$post_data                = array();
					$post_data['ID']          = $this->imported_post_id( $original_post_id );
					$post_data['post_parent'] = $this->imported_post_id( $original_post_parent_id );
					wp_update_post( $post_data );
					$this->post_orphans( $original_post_id, 0 ); /*ignore future*/
				}
			}
		}
	}

	/*
	Process delayed post
	*/
	private function process_delayed_posts( $last_delay = false ) {

		$this->log( esc_html__( 'Processing ', 'advanced-import' ) . count( $this->delay_posts, COUNT_RECURSIVE ) . esc_html__( 'delayed posts', 'advanced-import' ) );
		for ( $x = 1; $x < 4; $x ++ ) {
			foreach ( $this->delay_posts as $delayed_post_type => $delayed_post_data_s ) {
				foreach ( $delayed_post_data_s as $delayed_post_id => $delayed_post_data ) {

					/*already processed*/
					if ( $this->imported_post_id( $delayed_post_data['post_id'] ) ) {
						$this->log( $x . esc_html__( '- Successfully processed ', 'advanced-import' ) . $delayed_post_type . esc_html__( ' ID ', 'advanced-import' ) . $delayed_post_data['post_id'] . esc_html__( ' previously.', 'advanced-import' ) );

						/*already processed, remove it from delay_posts*/
						unset( $this->delay_posts[ $delayed_post_type ][ $delayed_post_id ] );
						$this->log( esc_html__( ' ( ', 'advanced-import' ) . count( $this->delay_posts, COUNT_RECURSIVE ) . esc_html__( ' delayed posts remain ) ', 'advanced-import' ) );
					}
					/*Process it*/
					elseif ( $this->process_import_single_post( $delayed_post_type, $delayed_post_data, $last_delay ) ) {
						$this->log( $x . esc_html__( ' - Successfully found delayed replacement for ', 'advanced-import' ) . $delayed_post_type . esc_html__( ' ID ', 'advanced-import' ) . $delayed_post_data['post_id'] );

						/*successfully processed, remove it from delay_posts*/
						unset( $this->delay_posts[ $delayed_post_type ][ $delayed_post_id ] );
						$this->log( esc_html__( ' ( ', 'advanced-import' ) . count( $this->delay_posts, COUNT_RECURSIVE ) . esc_html__( ' delayed posts remain ) ', 'advanced-import' ) );
					} else {
						$this->log( $x . esc_html__( ' - Not found delayed replacement for ', 'advanced-import' ) . $delayed_post_type . esc_html__( ' ID ', 'advanced-import' ) . $delayed_post_data['post_id'] );
					}
				}
			}
		}
	}

	/*Get file from url , download it and add to local*/
	private function import_image_and_file( $url, $post ) {

		/*extract the file name and extension from the url*/
		$file_name  = basename( $url );
		$local_file = ADVANCED_IMPORT_TEMP_UPLOADS . $file_name;
		$upload     = false;

		/*
		if file is already on local, return file information
		It means media is on local, while exporting media*/
		if ( is_file( $local_file ) && filesize( $local_file ) > 0 ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;
			$file_data = $wp_filesystem->get_contents( $local_file );
			$upload    = wp_upload_bits( $file_name, 0, $file_data, $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}
		}

		/*if there is no file on local or error on local file need to fetch it*/
		if ( ! $upload || $upload['error'] ) {

			/*get placeholder file in the upload dir with a unique, sanitized filename*/
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}

			$max_size = (int) apply_filters( 'import_attachment_size_limit', 0 );

			/*finally fetch the file from remote*/
			$response = wp_remote_get( $url );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				$headers = $response['headers'];
				WP_Filesystem();
				global $wp_filesystem;
				$wp_filesystem->put_contents( $upload['file'], $response['body'] );
			} else {
				/*required to download file failed.*/
				wp_delete_file( $upload['file'] );
				return new WP_Error( 'import_file_error', esc_html__( 'Remote server did not respond', 'advanced-import' ) );
			}

			$file_size = filesize( $upload['file'] );

			/*check for size*/
			if ( isset( $headers['content-length'] ) && $file_size != $headers['content-length'] ) {
				wp_delete_file( $upload['file'] );
				return new WP_Error( 'import_file_error', esc_html__( 'Remote file is incorrect size', 'advanced-import' ) );
			}

			/*if file size is 0*/
			if ( 0 == $file_size ) {
				wp_delete_file( $upload['file'] );
				return new WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded', 'advanced-import' ) );
			}

			/*if file is too large*/
			if ( ! empty( $max_size ) && $file_size > $max_size ) {
				wp_delete_file( $upload['file'] );
				return new WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote file is too large, limit is %s', 'advanced-import' ), size_format( $max_size ) ) );
			}
		}

		/*keep track of the old and new urls so we can substitute them later*/
		$this->imported_post_id( $url, $upload['url'] );
		$this->imported_post_id( $post['guid'], $upload['url'] );

		/*keep track of the destination if the remote url is redirected somewhere else*/
		if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
			$this->imported_post_id( $headers['x-final-location'], $upload['url'] );
		}
		return $upload;
	}

	/*
	Replace necessary ID by Local imported ID
	*/
	private function replace_old_id_to_new( $option_value, $index_key = false ) {

		/*Post IDS*/
		$replace_post_ids = apply_filters(
			'advanced_import_replace_post_ids',
			array(
				'page_id',
				'post_id',
				'image_id',
				'selectpage',
				'page_on_front',
				'page_for_posts',
				'first_page_id',
				'second_page_id',
				/*woocommerce pages*/
				'woocommerce_shop_page_id',
				'woocommerce_cart_page_id',
				'woocommerce_checkout_page_id',
				'woocommerce_pay_page_id',
				'woocommerce_thanks_page_id',
				'woocommerce_myaccount_page_id',
				'woocommerce_edit_address_page_id',
				'woocommerce_view_order_page_id',
				'woocommerce_terms_page_id',
				/*gutentor*/
				'wp_block_id',
			)
		);

		/*Terms IDS*/
		$replace_term_ids = apply_filters(
			'advanced_import_replace_term_ids',
			array(
				'cat_id',
				'nav_menu',
				'online-shop-feature-product-cat',
				'online_shop_featured_cats',
				'online_shop_wc_product_cat',
				'online_shop_wc_product_tag',
			)
		);

		/*replace terms in keys*/

		if ( is_array( $option_value ) ) {
			foreach ( $option_value as $key => $replace_old_value ) {

				if ( is_array( $replace_old_value ) && ! is_null( $replace_old_value ) ) {
					$option_value[ $key ] = $this->replace_old_id_to_new( $replace_old_value );
				} elseif ( $this->isJson( $replace_old_value ) && is_string( $replace_old_value ) && ! is_null( $replace_old_value ) ) {
					$value_array = json_decode( $replace_old_value, true );
					if ( is_array( $value_array ) ) {
						$option_value[ $key ] = wp_json_encode( $this->replace_old_id_to_new( $value_array ) );
					} else {
						if ( in_array( $key, $replace_post_ids ) && $key !== 0 ) {
							$new_id = $this->imported_post_id( $replace_old_value );
							if ( $new_id ) {
								$option_value[ $key ] = $new_id;
							}
						} elseif ( in_array( $key, $replace_term_ids ) && $key !== 0 ) {
							$new_id = $this->imported_term_id( $replace_old_value );
							if ( $new_id ) {
								$option_value[ $key ] = $new_id;
							}
						} else {
							$option_value[ $key ] = $replace_old_value;
						}
					}
				} else {

					if ( in_array( $key, $replace_post_ids ) && $key !== 0 ) {

						$new_id = $this->imported_post_id( $replace_old_value );
						if ( ! $new_id ) {
							/**/
						} else {
							$option_value[ $key ] = $new_id;
						}
					} elseif ( in_array( $key, $replace_term_ids ) && $key !== 0 ) {
						$new_id = $this->imported_term_id( $replace_old_value );
						if ( $new_id ) {
							$option_value[ $key ] = $new_id;
						}
					} else {
						$option_value[ $key ] = $replace_old_value;
					}
				}
			}
		} elseif ( is_numeric( $option_value ) && $index_key ) {

			if ( in_array( $index_key, $replace_post_ids ) && $index_key !== 0 ) {

				$new_id = $this->imported_post_id( $option_value );
				if ( ! $new_id ) {
					/**/
				} else {
					$option_value = $new_id;
				}
			} elseif ( in_array( $index_key, $replace_term_ids ) && $index_key !== 0 ) {
				$new_id = $this->imported_term_id( $option_value );
				if ( $new_id ) {
					$option_value = $new_id;
				}
			}
		}

		return $option_value;
	}

	/*
	 * Callback function to importing widgets data
	 * all widgets data is imported from here
	 * return mix
	 * */
	private function import_content_widgets_data() {
		$this->current_request = $this->current_request + 1;

		$import_widget_data      = $this->get_widgets_json();
		$import_widget_positions = $import_widget_data['widget_positions'];
		$import_widget_options   = $import_widget_data['widget_options'];

		/* get sidebars_widgets */
		$widget_positions = get_option( 'sidebars_widgets' );
		if ( ! is_array( $widget_positions ) ) {
			$widget_positions = array();
		}

		foreach ( $import_widget_options as $widget_name => $widget_options ) {

			/*replace $widget_options elements with updated imported entries.*/
			foreach ( $widget_options as $widget_option_id => $widget_option ) {
				$widget_options[ $widget_option_id ] = $this->replace_old_id_to_new( $widget_option, $widget_option_id );
			}
			$existing_options = get_option( 'widget_' . $widget_name, array() );
			if ( ! is_array( $existing_options ) ) {
				$existing_options = array();
			}
			$new_options = $widget_options + $existing_options;

			$new_options = apply_filters( 'advanced_import_new_options', $new_options );

			advanced_import_update_option( 'widget_' . $widget_name, $new_options );
		}

		$sidebars_widgets = array_merge( $widget_positions, $import_widget_positions );
		$sidebars_widgets = apply_filters( 'advanced_import_sidebars_widgets', $sidebars_widgets, $this );
		advanced_import_update_option( 'sidebars_widgets', $sidebars_widgets );

		return true;

	}

	/*check if string is json*/
	function isJson( $string ) {
		$test_json = @json_decode( $string, true );
		if ( is_array( $test_json ) ) {
			return true;
		}
		return false;
	}

	/*
	 callback function to importing menus and options data
	 * all menus and import data is imported from here
	 * return mix
	 * */
	public function import_menu_and_options() {
		$this->current_request = $this->current_request + 1;

		/*final wrap up of delayed posts.*/
		$this->process_delayed_posts( true );

		/*Elementor posts*/
		advanced_import_elementor()->process_elementor_posts();

		/*it includes options and menu data*/
		$theme_options = $this->get_theme_options_json();

		/*options data*/
		$custom_options = $theme_options['options'];

		/*menu data*/
		$menu_ids = $theme_options['menu'];

		/*we also want to update the widget area manager options.*/
		if ( is_array( $custom_options ) ) {
			foreach ( $custom_options as $option => $value ) {
				/*replace old entries with updated imported entries.*/
				$value = $this->replace_old_id_to_new( $value, $option );

				/*we have to update widget page numbers with imported page numbers.*/
				if (
					preg_match( '#(wam__position_)(\d+)_#', $option, $matches ) ||
					preg_match( '#(wam__area_)(\d+)_#', $option, $matches )
				) {
					$new_page_id = $this->imported_post_id( $matches[2] );
					if ( $new_page_id ) {
						// we have a new page id for this one. import the new setting value.
						$option = str_replace( $matches[1] . $matches[2] . '_', $matches[1] . $new_page_id . '_', $option );
					}
				} elseif ( $value && ! empty( $value['custom_logo'] ) ) {
					$new_logo_id = $this->imported_post_id( $value['custom_logo'] );
					if ( $new_logo_id ) {
						$value['custom_logo'] = $new_logo_id;
					}
				}
				/** For Gutentor */
				elseif ( strpos( $option, 'gutentor-cat' ) !== false ) {
					$cat_id = substr( $option, strrpos( $option, '-' ) + 1 );
					if ( 'child' !== $cat_id ) {
						$new_cat_id = $this->imported_term_id( $cat_id );
						$option     = str_replace( $cat_id, $new_cat_id, $option );
					}
				}
				advanced_import_update_option( $option, $value );
			}
		}

		/*
		Options completed
		Menu Start*/
		$save = array();
		foreach ( $menu_ids as $menu_id => $term_id ) {
			$new_term_id = $this->imported_term_id( $term_id );
			if ( $new_term_id ) {
				$save[ $menu_id ] = $new_term_id;
			}
		}

		if ( $save ) {
			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $save ) );
		}

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		advanced_import_update_option( 'rewrite_rules', false );
		$wp_rewrite->flush_rules( true );

		return true;
	}

	public function log( $message ) {
		$this->logs[] = $message;
	}


	public function error( $message ) {
		$this->logs[] = esc_html__( 'ERROR!!!! ', 'advanced-import' ) . $message;
	}

	public function reset_transient() {
		delete_transient( 'content.json' );
		delete_transient( 'widgets.json' );
		delete_transient( 'options.json' );
		delete_transient( 'delayed_posts' );
		delete_transient( 'imported_term_ids' );
		delete_transient( 'imported_post_ids' );
		delete_transient( 'post_orphans' );
		delete_transient( 'adi_elementor_data_posts' );
	}

	/*
		Callback function to completed
	 * Show Completed Message
	 * */
	public function complete_screen() {

		/*check for security*/
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
				)
			);
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;
		$wp_filesystem->rmdir( ADVANCED_IMPORT_TEMP, true );

		set_theme_mod( 'advanced_import_setup_complete', time() );

		/*delete_transient();*/
		$this->reset_transient();

		$message  = '<div class="ai-notification-title">';
		$message .= '<p>' . esc_html__( 'Your Website is Ready!', 'advanced-import' ) . '</p>';
		$message .= '<p class="ai-actions-buttons">' . sprintf( esc_html__( ' %1$sVisit your Site%2$s ', 'advanced-import' ), '<a target="_blank" href="' . esc_url( home_url( '/' ) ) . '">', '</a>' ) . '</p>';
		$message .= '<p>' . sprintf( esc_html__( 'Congratulations! All Data is imported successfully. From %1$s WordPress dashboard%2$s you can make changes and modify any of the default content to suit your needs.', 'advanced-import' ), '<a href="' . esc_url( admin_url() ) . '">', '</a>' ) . '</p>';
		$message .= '</div>';

		apply_filters( 'advanced_import_complete_message', $message );

		do_action( 'advanced_import_before_complete_screen' );
		echo $message;
		do_action( 'advanced_import_after_complete_screen' );
		exit;
	}


	/*
	 callback function for wp_ajax_install_plugin
	* Install plugin
	* */
	function install_plugin() {

		/*check for security*/
		if ( ! current_user_can( 'install_plugins' ) ) {
			$status['errorMessage'] = __( 'Sorry, you are not allowed to install plugins on this site.', 'advanced-import' );
			wp_send_json_error( $status );
		}

		if ( empty( $_POST['plugin'] ) || empty( $_POST['slug'] ) ) {
			wp_send_json_error(
				array(
					'slug'         => '',
					'errorCode'    => 'no_plugin_specified',
					'errorMessage' => __( 'No plugin specified.', 'advanced-import' ),
				)
			);
		}

		$slug   = sanitize_key( wp_unslash( $_POST['slug'] ) );
		$plugin = plugin_basename( sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) );

		if ( is_plugin_active_for_network( $plugin ) || is_plugin_active( $plugin ) ) {
			// Plugin is activated
			wp_send_json_success();

		}
		$status = array(
			'install' => 'plugin',
			'slug'    => sanitize_key( wp_unslash( $_POST['slug'] ) ),
		);

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		// Looks like a plugin is installed, but not active.
		if ( file_exists( WP_PLUGIN_DIR . '/' . $slug ) ) {
			$plugin_data          = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$status['plugin']     = $plugin;
			$status['pluginName'] = $plugin_data['Name'];

			if ( current_user_can( 'activate_plugin', $plugin ) && is_plugin_inactive( $plugin ) ) {
				$result = activate_plugin( $plugin );

				if ( is_wp_error( $result ) ) {
					$status['errorCode']    = $result->get_error_code();
					$status['errorMessage'] = $result->get_error_message();
					wp_send_json_error( $status );
				}

				wp_send_json_success( $status );
			}
		}

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => sanitize_key( wp_unslash( $_POST['slug'] ) ),
				'fields' => array(
					'sections' => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			$status['errorMessage'] = $api->get_error_message();
			wp_send_json_error( $status );
		}

		$status['pluginName'] = $api->name;

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$status['debug'] = $skin->get_upgrade_messages();
		}

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
			wp_send_json_error( $status );
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();
			wp_send_json_error( $status );
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$status['errorMessage'] = $skin->get_error_messages();
			wp_send_json_error( $status );
		} elseif ( is_null( $result ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'advanced-import' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			wp_send_json_error( $status );
		}

		$install_status = install_plugin_install_status( $api );

		if ( current_user_can( 'activate_plugin', $install_status['file'] ) && is_plugin_inactive( $install_status['file'] ) ) {
			$result = activate_plugin( $install_status['file'] );

			if ( is_wp_error( $result ) ) {
				$status['errorCode']    = $result->get_error_code();
				$status['errorMessage'] = $result->get_error_message();
				wp_send_json_error( $status );
			}
		}

		wp_send_json_success( $status );
	}

	/*
	 callback function to current_screen
	 * Add help Text
	 * @param $screen object screen
	 * */
	public function help_tabs( $screen ) {
		if ( ! is_array( $this->hook_suffix ) || ! in_array( $screen->base, $this->hook_suffix ) ) {
			return;
		}
		$current_url = advanced_import_current_url();

		$screen->add_help_tab(
			array(
				'id'      => 'ai_help_tab_info',
				'title'   => __( 'Information', 'advanced-import' ),
				'content' =>
					'<h2>' . __( 'Information', 'advanced-import' ) . '</h2>' .
					'<p>' . sprintf(
						__( 'Export you content via, <a href="%s" target="_blank">Advanced Export</a>. You can import export content, widget, customizer and media files too.', 'advanced-import' ),
						'https://wordpress.org/plugins/advanced-export/'
					) . '</p>' .
					'<p>' . sprintf(
						__( 'The zip file exported via <a href="%1$s" target="_blank">Advanced Export</a>. can be imported from this plugin <a href="%2$s">Advanced Import</a>.', 'advanced-import' ),
						'https://wordpress.org/support/plugin/advanced-export',
						'https://wordpress.org/support/plugin/advanced-import'
					) . '</p>' .
					'<p><a href="' . 'https://wordpress.org/support/plugin/advanced-import' . '" class="button button-primary" target="_blank">' . __( 'Community forum', 'advanced-import' ) . '</a> <a href="' . 'https://www.addonspress.com/' . '" class="button" target="_blank">' . __( 'Author', 'advanced-import' ) . '</a></p>',
			)
		);

		$reset_url = wp_nonce_url(
			add_query_arg( 'ai_reset_wordpress', 'true', $current_url ),
			'ai_reset_wordpress',
			'ai_reset_wordpress_nonce'
		);
		$screen->add_help_tab(
			array(
				'id'      => 'ai_help_tab_reset',
				'title'   => __( 'Reset wizard', 'advanced-import' ),
				'content' =>
					'<h2>' . __( '<strong>WordPress Reset</strong>', 'advanced-import' ) . '</h2>' .
					'<p>' . __( 'If no important data on your site. You can reset the WordPress back to default again!', 'advanced-import' ) . '</p>' .
					'<p class="submit">' . wp_nonce_field( 'advanced-import-reset', 'advanced-import-reset', true, false ) . '<a href="' . esc_url( $reset_url ) . '" class="button button-primary ai-wp-reset">' . __( 'Run the Reset Wizard', 'advanced-import' ) . '</a></p>',
			)
		);

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'More information:', 'advanced-import' ) . '</strong></p>' .
			'<p><a href="' . 'https://wordpress.org/plugins/advanced-export/' . '" target="_blank">' . __( 'Advanced Export', 'advanced-import' ) . '</a></p>' .
			'<p><a href="' . 'https://wordpress.org/plugins/advanced-import/' . '" target="_blank">' . __( 'Advanced Import', 'advanced-import' ) . '</a></p>'
		);

		global $advanced_import_tracking;
		ob_start();
		$advanced_import_tracking->admin_notice( true );
		$has_admin_notice = ob_get_contents();
		ob_end_clean();
		if ( $has_admin_notice ) {
			$has_admin_notice = '<h2>' . __( '<strong>Allow Track</strong>', 'advanced-import' ) . '</h2>' . $has_admin_notice;
			$screen->add_help_tab(
				array(
					'id'      => 'ai_help_tab_track',
					'title'   => __( 'Allow Track', 'advanced-import' ),
					'content' => $has_admin_notice,
				)
			);
		}

	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function advanced_import_admin() {
	return Advanced_Import_Admin::instance();
}
