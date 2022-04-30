<?php
namespace AIOSEO\Plugin\Common\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extendable Block class helper
 *
 * @since 4.1.1
 */
class Blocks {
	/**
	 * Class constructor.
	 *
	 * @since 4.1.1
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );

		global $wp_version;
		if ( version_compare( $wp_version, '5.8', '<' ) ) {
			add_filter( 'block_categories', [ $this, 'blockCategories' ], 10 );

			return;
		}
		add_filter( 'block_categories_all', [ $this, 'blockCategories' ], 10 );
	}

	/**
	 * Initializes our blocks.
	 *
	 * @since 4.1.1
	 *
	 * @return void
	 */
	public function init() {
		if ( ! $this->isBlockEditorActive() ) {
			return;
		}

		$this->register();
	}

	/**
	 * Registers the block. This is a wrapper to be extended in the child class.
	 *
	 * @since 4.1.1
	 *
	 * @return void
	 */
	public function register() {}

	/**
	 * Adds a new AIOSEO block category.
	 *
	 * @since 4.1.1
	 *
	 * @param  array $categories Array of block categories.
	 * @return void
	 */
	public function blockCategories( $categories ) {
		$exists = wp_list_filter( $categories, [ 'slug' => 'aioseo' ] );
		if ( ! empty( $exists ) ) {
			return $categories;
		}

		return array_merge(
			$categories,
			[
				[
					'slug'  => 'aioseo',
					'title' => AIOSEO_PLUGIN_SHORT_NAME,
				]
			]
		);
	}

	/**
	 * Helper function to determine if we're rendering the block inside Gutenberg.
	 *
	 * @since 4.1.1
	 *
	 * @return bool In gutenberg.
	 */
	public function isGBEditor() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context']; // phpcs:ignore HM.Security.NonceVerification.Recommended
	}

	/**
	 * Helper function to determine if we can register blocks.
	 *
	 * @since 4.1.1
	 *
	 * @return bool Can register block.
	 */
	public function isBlockEditorActive() {
		return function_exists( 'register_block_type' );
	}
}