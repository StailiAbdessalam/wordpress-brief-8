<?php

/**
 * Reset WordPress
 *
 * @link       https://addonspress.com/
 * @since      1.0.0
 *
 * @package    Advanced_Import
 * @subpackage Advanced_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 * Reset WordPress
 *
 * @package    Advanced_Import
 * @subpackage Advanced_Import/admin
 * @author     Addons Press <addonspress.com>
 */
class Advanced_Import_Reset_WordPress {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {}

	/**
	 * Main Advanced_Import_Reset_WordPress Instance
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @return object $instance Advanced_Import_Reset_WordPress Instance
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been ran previously
		if ( null === $instance ) {
			$instance = new Advanced_Import_Reset_WordPress();

		}

		// Always return the instance
		return $instance;
	}

	/**
	 * Check if user can reset
	 */
	private function can_reset() {
		if ( ! empty( $_GET['ai_reset_wordpress'] ) && ! empty( $_GET['ai_reset_wordpress_nonce'] ) ) {
			/*Security*/
			if ( ! wp_verify_nonce( wp_unslash( $_GET['ai_reset_wordpress_nonce'] ), 'ai_reset_wordpress' ) ) { // WPCS: input var ok, sanitization ok.
				return false;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Attempt to deactivate the plugins which gives errors while reseting.
	 * We may add other plugins after testing/reported
	 */
	private function deactivate_plugins() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			return;
		}

		if ( is_plugin_active( 'elementor/elementor.php' ) ) {
			deactivate_plugins( 'elementor/elementor.php' );
		}

	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_reset_notice() {
		if ( isset( $_GET['advanced-import-hide-notice'] ) && isset( $_GET['_advanced_import_notice_nonce'] ) ) {
			/*Security*/
			if ( ! wp_verify_nonce( $_GET['_advanced_import_notice_nonce'], 'advanced_import_hide_notice_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'advanced-import' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'advanced-import' ) );
			}

			$hide_notice = sanitize_text_field( $_GET['advanced-import-hide-notice'] );

			if ( ! empty( $hide_notice ) && 'reset_notice' == $hide_notice ) {
				advanced_import_update_option( 'advanced_import_reset_notice', 1 );
			}
		}
	}

	/**
	 * Reset actions when a reset button is clicked.
	 */
	public function reset_wizard_actions() {
		global $wpdb, $current_user;

		if ( ! empty( $_GET['ai_reset_wordpress'] ) && ! empty( $_GET['ai_reset_wordpress_nonce'] ) && $this->can_reset() ) {
			/*Security*/
			if ( ! wp_verify_nonce( wp_unslash( $_GET['ai_reset_wordpress_nonce'] ), 'ai_reset_wordpress' ) ) { // WPCS: input var ok, sanitization ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'advanced-import' ) );
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'No permission to reset WordPress', 'advanced-import' ) );
			}

			require_once ABSPATH . '/wp-admin/includes/upgrade.php';

			$template    = get_option( 'template' );
			$blogname    = get_option( 'blogname' );
			$admin_email = get_option( 'admin_email' );
			$blog_public = get_option( 'blog_public' );

			$current_url = advanced_import_current_url();

			if ( 'admin' != $current_user->user_login ) {
				$user = get_user_by( 'login', 'admin' );
			}

			if ( empty( $user->user_level ) || $user->user_level < 10 ) {
				$user = $current_user;
			}

			// Drop tables.
			$drop_tables = $wpdb->get_col( sprintf( "SHOW TABLES LIKE '%s%%'", str_replace( '_', '\_', $wpdb->prefix ) ) );
			foreach ( $drop_tables as $table ) {
				$wpdb->query( "DROP TABLE IF EXISTS $table" );
			}

			// Installs the site.
			$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );

			// Updates the user password with a old one.
			$wpdb->update(
				$wpdb->users,
				array(
					'user_pass'           => $user->user_pass,
					'user_activation_key' => '',
				),
				array( 'ID' => $result['user_id'] )
			);

			// Set up the Password change nag.
			$default_password_nag = get_user_option( 'default_password_nag', $result['user_id'] );
			if ( $default_password_nag ) {
				update_user_option( $result['user_id'], 'default_password_nag', false, true );
			}

			// Switch current theme.
			$current_theme = wp_get_theme( $template );
			if ( $current_theme->exists() ) {
				switch_theme( $template );
			}

			// Activate required plugins.
			$required_plugins = (array) apply_filters( 'advanced_import_' . $template . '_required_plugins', array() );
			if ( is_array( $required_plugins ) ) {
				if ( ! in_array( plugin_basename( ADVANCED_IMPORT_PATH . '/advanced-import.php' ), $required_plugins ) ) {
					$required_plugins = array_merge( $required_plugins, array( ADVANCED_IMPORT_PATH . '/advanced-import.php' ) );
				}
				activate_plugins( $required_plugins, '', is_network_admin(), true );
			}

			// Update the cookies.
			wp_clear_auth_cookie();
			wp_set_auth_cookie( $result['user_id'] );

			// Redirect to demo importer page to display reset success notice.
			wp_safe_redirect( $current_url . '&reset=true&from=ai-reset-wp' );
			exit();
		}
	}

	/**
	 * Reset wizard notice.
	 */
	public function reset_wizard_notice() {

		$screen = get_current_screen();
		if ( ! in_array( $screen->base, advanced_import_admin()->hook_suffix ) ) {
			return;
		}
		$current_url = advanced_import_current_url();
		$reset_url   = wp_nonce_url(
			add_query_arg( 'ai_reset_wordpress', 'true', $current_url ),
			'ai_reset_wordpress',
			'ai_reset_wordpress_nonce'
		);

		$demo_notice_dismiss = get_option( 'advanced_import_reset_notice' );

		// Output reset wizard notice.
		if ( ! $demo_notice_dismiss ) {
			?>
			<div id="message" class="updated ai-import-message">
				<p><?php _e( '<strong>WordPress Reset</strong> &#8211; If no important data on your site. You can reset the WordPress back to default again!', 'advanced-import' ); ?></p>
				<p class="submit"><?php wp_nonce_field( 'advanced-import-reset', 'advanced-import-reset' ); ?><a href="<?php echo esc_url( $reset_url ); ?>" class="button button-primary ai-wp-reset"><?php esc_html_e( 'Run the Reset Wizard', 'advanced-import' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'advanced-import-hide-notice', 'reset_notice', $current_url ), 'advanced_import_hide_notice_nonce', '_advanced_import_notice_nonce' ) ); ?>"><?php esc_attr_e( 'Hide this notice', 'advanced-import' ); ?></a></p>
			</div>
			<?php
		} elseif ( isset( $_GET['reset'] ) && 'true' === $_GET['reset'] ) {
			$user = get_user_by( 'id', 1 );
			?>
			<div id="message" class="notice notice-info is-dismissible">
				<p><?php printf( __( 'WordPress has been reset back to defaults. The user <strong>"%1$s"</strong> was recreated with its previous password.', 'advanced-import' ), $user->user_login ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Before Reset Ajax callback
	 */
	public function before_reset() {
		/*check for security*/
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, you are not allowed to install demo on this site.', 'advanced-import' ),
				)
			);
		}
		check_admin_referer( 'advanced-import-reset' );

		/*Deactivate troubleshoot plugins before reset*/
		$this->deactivate_plugins();

		do_action( 'advanced_import_before_reset' );
		wp_send_json_success(
			array(
				'message' => esc_html__( 'Success', 'advanced-import' ),
			)
		);
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
function advanced_import_reset_wordpress() {
	return Advanced_Import_Reset_WordPress::instance();
}
