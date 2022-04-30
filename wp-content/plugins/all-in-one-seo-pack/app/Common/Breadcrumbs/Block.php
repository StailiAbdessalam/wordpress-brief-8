<?php
namespace AIOSEO\Plugin\Common\Breadcrumbs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Breadcrumb Block.
 *
 * @since 4.1.1
 */
class Block extends \AIOSEO\Plugin\Common\Utils\Blocks {
	/**
	 * Class constructor.
	 *
	 * @since 4.1.1
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Registers the block.
	 *
	 * @since 4.1.1
	 *
	 * @return void
	 */
	public function register() {
		register_block_type(
			'aioseo/breadcrumbs', [
				'render_callback' => [ $this, 'render' ]
			]
		);
	}

	/**
	 * Renders the block.
	 *
	 * @since 4.1.1
	 *
	 * @param  array  $blockAttributes The block attributes.
	 * @return string                  The output from the output buffering.
	 */
	public function render( $blockAttributes ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$postId = ! empty( $_GET['post_id'] ) ? wp_unslash( $_GET['post_id'] ) : false; // phpcs:ignore HM.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( $this->isGBEditor() && ! empty( $postId ) ) {
			return aioseo()->breadcrumbs->frontend->sideDisplay( false, 'post' === get_post_type( $postId ) ? 'post' : 'single', get_post( $postId ) );
		}

		return aioseo()->breadcrumbs->frontend->display( false );
	}
}