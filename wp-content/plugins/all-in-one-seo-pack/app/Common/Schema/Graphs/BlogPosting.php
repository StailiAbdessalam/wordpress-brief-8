<?php
namespace AIOSEO\Plugin\Common\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blog Posting graph class.
 *
 * @since 4.0.0
 */
class BlogPosting extends Article {
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

		$data['@type'] = 'BlogPosting';
		$data['@id']   = aioseo()->schema->context['url'] . '#blogposting';

		return $data;
	}
}