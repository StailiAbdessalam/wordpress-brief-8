<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Advanced_Import_Theme_Template_Library_Base' ) ) {

	/**
	 * Base Class For CosmosWP for common functions
	 *
	 * @package Advanced Import
	 * @subpackage  Advanced Import Template Library
	 * @since 1.0.0
	 */
	class Advanced_Import_Theme_Template_Library_Base {

		/**
		 * Run Block
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function run() {

			if ( method_exists( $this, 'add_template_library' ) ) {
				add_filter( 'advanced_import_demo_lists', array( $this, 'add_template_library' ) );
			}
		}
	}
}
