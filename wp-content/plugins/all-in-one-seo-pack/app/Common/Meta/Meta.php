<?php
namespace AIOSEO\Plugin\Common\Meta;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;

/**
 * Instantiates the Meta classes.
 *
 * @since 4.0.0
 */
class Meta {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->metaData     = new MetaData();
		$this->title        = new Title();
		$this->description  = new Description();
		$this->keywords     = new Keywords();
		$this->amp          = new Amp();
		$this->links        = new Links();
		$this->robots       = new Robots();

		add_action( 'delete_post', [ $this, 'deletePostMeta' ], 1000, 2 );
	}

	/**
	 * When we delete the meta, we want to delete our post model.
	 *
	 * @since 4.0.1
	 *
	 * @param  integer $postId The ID of the post.
	 * @param  WP_Post $post   The post object.
	 * @return void
	 */
	public function deletePostMeta( $postId ) {
		$aioseoPost = Models\Post::getPost( $postId );
		if ( $aioseoPost->exists() ) {
			$aioseoPost->delete();
		}
	}
}