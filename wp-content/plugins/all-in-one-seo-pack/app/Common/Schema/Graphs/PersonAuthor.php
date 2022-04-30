<?php
namespace AIOSEO\Plugin\Common\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Person Author graph class.
 *
 * This a secondary Person graph for post authors and BuddyPress profile pages.
 *
 * @since 4.0.0
 */
class PersonAuthor extends Person {
	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.0
	 *
	 * @return array $data The graph data.
	 */
	public function get() {
		$post = aioseo()->helpers->getPost();
		if ( ! $post ) {
			return [];
		}

		$userId = $post->post_author;
		if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
			$userId = intval( wp_get_current_user()->ID );
		}

		if ( ! $userId ) {
			return [];
		}

		$authorUrl = get_author_posts_url( $post->post_author );

		$data = [
			'@type' => 'Person',
			'@id'   => $authorUrl . '#author',
			'url'   => $authorUrl,
			'name'  => get_the_author_meta( 'display_name', $userId )
		];

		$avatar = $this->avatar( $userId, 'authorImage' );
		if ( $avatar ) {
			$data['image'] = $avatar;
		}

		$socialUrls = $this->socialUrls( $userId );
		if ( $socialUrls ) {
			$data['sameAs'] = $socialUrls;
		}

		if ( is_author() ) {
			$data['mainEntityOfPage'] = [
				'#id' => aioseo()->schema->context['url'] . '#profilepage'
			];
		}

		return $data;
	}
}