<?php
namespace AIOSEO\Plugin\Common\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * News Article graph class.
 *
 * @since 4.0.0
 */
class NewsArticle extends Article {
	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.0
	 *
	 * @return array The graph data.
	 */
	public function get() {
		$data = parent::get();
		if ( ! $data ) {
			return [];
		}

		$data['@type']    = 'NewsArticle';
		$data['@id']      = aioseo()->schema->context['url'] . '#newsarticle';
		// Translators: 1 - The date the article was published on.
		$data['dateline'] = sprintf( __( 'Published on %1$s.', 'all-in-one-seo-pack' ), get_the_date( 'F j, Y' ) );

		return $data;
	}
}