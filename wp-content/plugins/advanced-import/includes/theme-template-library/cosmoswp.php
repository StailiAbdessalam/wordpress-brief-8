<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Advanced_Import_Theme_CosmosWP' ) ) {

	/**
	 * Functions related to About Block
	 *
	 * @package Advanced Import
	 * @subpackage Advanced_Import_Theme_CosmosWP
	 * @since 1.0.5
	 */

	class Advanced_Import_Theme_CosmosWP extends Advanced_Import_Theme_Template_Library_Base {

		/**
		 * Theme name
		 * This class is crated for cosmoswp theme
		 *
		 * @since 1.0.5
		 * @access   private
		 */
		private $theme = 'cosmoswp';

		/**
		 * Theme author
		 * This class is crated for cosmoswp theme
		 * Double check for author
		 *
		 * @since 1.0.5
		 * @access   private
		 */
		private $theme_author = 'cosmoswp';

		/**
		 * Gets an instance of this object.
		 * Prevents duplicate instances which avoid artefacts and improves performance.
		 *
		 * @static
		 * @access public
		 * @since 1.0.5
		 * @return object
		 */
		public static function get_instance() {

			// Store the instance locally to avoid private static replication
			static $instance = null;

			// Only run these methods if they haven't been ran previously
			if ( null === $instance ) {
				$instance = new self();
			}

			// Always return the instance
			return $instance;

		}

		/**
		 * Load block library
		 * Used for blog template loading
		 *
		 * @since 1.0.5
		 * @package    CosmosWP
		 * @author     CosmosWP <info@cosmoswp.com>
		 *
		 * @param$current_demo_list array
		 * @return array
		 */
		public function add_template_library( $current_demo_list ) {
			/*common check*/
			if ( strtolower( advanced_import_get_current_theme_author() ) != $this->theme_author ) {
				return $current_demo_list;
			}
			if ( advanced_import_get_current_theme_slug() != $this->theme ) {
				return $current_demo_list;
			}

			/*
			For cosmoswp only.
			check if cosmoswp template library function exist*/
			if ( function_exists( 'run_cosmoswp_template_library' ) ) {
				return $current_demo_list;
			}

			/*finally fetch template library data from live*/
			$templates_list = array();

			$url       = 'https://www.demo.cosmoswp.com/wp-json/cosmoswp-demo-api/v1/fetch_templates/';
			$body_args = array(
				/*API version*/
				'api_version' => wp_get_theme()['Version'],
				/*lang*/
				'site_lang'   => get_bloginfo( 'language' ),
			);
			$raw_json  = wp_safe_remote_get(
				$url,
				array(
					'timeout' => 100,
					'body'    => $body_args,
				)
			);

			if ( ! is_wp_error( $raw_json ) ) {
				$demo_server = json_decode( wp_remote_retrieve_body( $raw_json ), true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					if ( is_array( $demo_server ) ) {
						$templates_list = $demo_server;
					}
				}
			}

			return array_merge( $current_demo_list, $templates_list );
		}
	}
}
Advanced_Import_Theme_CosmosWP::get_instance()->run();
