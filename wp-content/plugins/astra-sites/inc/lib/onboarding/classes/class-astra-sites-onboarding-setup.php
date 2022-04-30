<?php
/**
 * Ai site setup
 *
 * @since 3.0.0-beta.1
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sites_Onboarding_Setup' ) ) :

	/**
	 * AI Site Setup
	 */
	class Astra_Sites_Onboarding_Setup {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 3.0.0-beta.1
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 3.0.0-beta.1
		 */
		public function __construct() {
			add_action( 'wp_ajax_astra_sites_set_site_data', array( $this, 'set_site_data' ) );
			add_action( 'wp_ajax_report_error', array( $this, 'report_error' ) );
			add_action( 'st_before_sending_error_report', array( $this, 'delete_transient_for_import_process' ) );
			add_action( 'st_before_sending_error_report', array( $this, 'temporary_cache_errors' ), 10, 1 );
		}

		/**
		 * Delete transient for import process.
		 *
		 * @since 3.1.4
		 * @return void
		 */
		public function temporary_cache_errors( $posted_data ) {
			update_option( 'astra_sites_cached_import_error', $posted_data, 'no' );
		}

		/**
		 * Delete transient for import process.
		 *
		 * @since 3.1.4
		 * @return void
		 */
		public function delete_transient_for_import_process() {
			delete_transient( 'astra_sites_import_started' );
		}

		/**
		 * Report Error.
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function report_error() {
			$api_url = add_query_arg( [], trailingslashit( Astra_Sites::get_instance()->get_api_domain() ) . 'wp-json/starter-templates/v2/import-error/' );

			if ( ! astra_sites_is_valid_url( $api_url ) ) {
				wp_send_json_error(
					array(
						'message' => sprintf( __( 'Invalid Request URL - %s', 'astra-sites' ), $api_url ),
						'code'    => 'Error',
					)
				);
			}

			$post_id = ( isset( $_POST['id'] ) ) ? intval( $_POST['id'] ) : 0;
			$user_agent_string = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

			if ( 0 === $post_id ) {
				wp_send_json_error(
					array(
						'message' => sprintf( __( 'Invalid Post ID - %d', 'astra-sites' ), $post_id ),
						'code'    => 'Error',
					)
				);
			}

			$api_args = array(
				'timeout'   => 3,
				'blocking'  => true,
				'body'      => array(
					'url'    => esc_url( site_url() ),
					'err'   => stripslashes( $_POST['error'] ),
					'id'	=> $_POST['id'],
					'logfile' => $this->get_log_file_path(),
					'version' => ASTRA_SITES_VER,
					'abspath' => ABSPATH,
					'user_agent' => $user_agent_string,
					'server' => array(
						'php_version' => $this->get_php_version(),
						'php_post_max_size' => ini_get( 'post_max_size' ),
						'php_max_execution_time' => ini_get( 'max_execution_time' ),
						'max_input_time' => ini_get( 'max_input_time' ),
						'php_memory_limit' => ini_get( 'memory_limit' ),
						'php_max_input_vars' => ini_get( 'max_input_vars' ), // phpcs:ignore:PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound
					),
				),
			);

			do_action( 'st_before_sending_error_report', $api_args['body'] );

			$request = wp_remote_post( $api_url, $api_args );

			do_action( 'st_after_sending_error_report', $api_args['body'], $request );

			if ( is_wp_error( $request ) ) {
				wp_send_json_error( $request );
			}

			$code = (int) wp_remote_retrieve_response_code( $request );
			$data = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( 200 === $code ) {
				wp_send_json_success( $data );
			}

			wp_send_json_error( $data );
		}

		/**
		 * Get full path of the created log file.
		 *
		 * @return string File Path.
		 * @since 3.0.25
		 */
		public function get_log_file_path() {
			$log_file = get_option( 'astra_sites_recent_import_log_file', false );
			if ( ! empty( $log_file ) && isset( $log_file ) ) {
				return str_replace( ABSPATH , esc_url( site_url() ) . '/' , $log_file );
			}
			
			return "";
		}

		/**
		 * Get installed PHP version.
		 *
		 * @return float PHP version.
		 * @since 3.0.16
		 */
		public function get_php_version() {
			if ( defined( 'PHP_MAJOR_VERSION' ) && defined( 'PHP_MINOR_VERSION' ) && defined( 'PHP_RELEASE_VERSION' ) ) { // phpcs:ignore
				return PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION;
			}

			return phpversion();
		}

		/**
		 * Set site related data.
		 *
		 * @since 3.0.0-beta.1
		 * @return void
		 */
		public function set_site_data() {

			check_ajax_referer( 'astra-sites-set-ai-site-data', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => __( 'You are not authorized to perform this action.', 'astra-sites' ),
					)
				);
			}

			$param = isset( $_POST['param'] ) ? sanitize_text_field( $_POST['param'] ) : '';

			if ( empty( $param ) ) {
				wp_send_json_error();
			}

			switch ( $param ) {

				case 'site-title':
						$business_name = isset( $_POST['business-name'] ) ? sanitize_text_field( stripslashes( $_POST['business-name'] ) ) : '';
					if ( ! empty( $business_name ) ) {
						update_option( 'blogname', $business_name );
					}

					break;

				case 'site-logo' === $param && function_exists( 'astra_get_option' ):
						$logo_id = isset( $_POST['logo'] ) ? sanitize_text_field( $_POST['logo'] ) : '';
						$width_index = 'ast-header-responsive-logo-width';
						set_theme_mod( 'custom_logo', $logo_id );

					if ( ! empty( $logo_id ) ) {
						// Disable site title when logo is set.
						astra_update_option( 'display-site-title', false );
					}

						// Set logo width.
						$logo_width = isset( $_POST['logo-width'] ) ? sanitize_text_field( $_POST['logo-width'] ) : '';
						$option = astra_get_option( $width_index );

					if ( isset( $option['desktop'] ) ) {
						$option['desktop'] = $logo_width;
					}
						astra_update_option( $width_index, $option );

						// Check if transparent header is used in the demo.
						$transparent_header = astra_get_option( 'transparent-header-logo', false );
						$inherit_desk_logo = astra_get_option( 'different-transparent-logo', false );

					if ( '' !== $transparent_header && $inherit_desk_logo ) {
						astra_update_option( 'transparent-header-logo', wp_get_attachment_url( $logo_id ) );
						$width_index = 'transparent-header-logo-width';
						$option = astra_get_option( $width_index );

						if ( isset( $option['desktop'] ) ) {
							$option['desktop'] = $logo_width;
						}
						astra_update_option( $width_index, $option );
					}

					break;

				case 'site-colors' === $param && function_exists( 'astra_get_option' ) && method_exists( 'Astra_Global_Palette', 'get_default_color_palette' ):
						$palette = isset( $_POST['palette'] ) ? (array) json_decode( stripslashes( $_POST['palette'] ) ) : array();
						$colors = isset( $palette['colors'] ) ? (array) $palette['colors'] : array();
					if ( ! empty( $colors ) ) {
						$global_palette = astra_get_option( 'global-color-palette' );
						$color_palettes = get_option( 'astra-color-palettes', Astra_Global_Palette::get_default_color_palette() );

						foreach ( $colors as $key => $color ) {
							$global_palette['palette'][ $key ] = $color;
							$color_palettes['palettes']['palette_1'][ $key ] = $color;
						}

						update_option( 'astra-color-palettes', $color_palettes );
						astra_update_option( 'global-color-palette', $global_palette );
					}
					break;

				case 'site-typography' === $param && function_exists( 'astra_get_option' ):
						$typography = isset( $_POST['typography'] ) ? (array) json_decode( stripslashes( $_POST['typography'] ) ) : '';

						$font_size_body = isset( $typography['font-size-body'] ) ? (array) $typography['font-size-body'] : '';
						if( ! empty( $font_size_body ) && is_array( $font_size_body ) ) {
							astra_update_option( 'font-size-body', $font_size_body );
						}

						if ( ! empty( $typography['body-font-family'] ) ) {
							astra_update_option( 'body-font-family', $typography['body-font-family'] );
						}

						if ( ! empty( $typography['body-font-variant'] ) ) {
							astra_update_option( 'body-font-variant', $typography['body-font-variant'] );
						}

						if ( ! empty( $typography['body-font-weight'] ) ) {
							astra_update_option( 'body-font-weight', $typography['body-font-weight'] );
						}

						if ( ! empty( $typography['body-line-height'] ) ) {
							astra_update_option( 'body-line-height', $typography['body-line-height'] );
						}

						if ( ! empty( $typography['headings-font-family'] ) ) {
							astra_update_option( 'headings-font-family', $typography['headings-font-family'] );
						}

						if ( ! empty( $typography['headings-font-weight'] ) ) {
							astra_update_option( 'headings-font-weight', $typography['headings-font-weight'] );
						}

						if ( ! empty( $typography['headings-line-height'] ) ) {
							astra_update_option( 'headings-line-height', $typography['headings-line-height'] );
						}

						if ( ! empty( $typography['headings-font-variant'] ) ) {
							astra_update_option( 'headings-font-variant', $typography['headings-font-variant'] );
						}

					break;
			}

			wp_send_json_success();
		}
	}

	Astra_Sites_Onboarding_Setup::get_instance();

endif;
