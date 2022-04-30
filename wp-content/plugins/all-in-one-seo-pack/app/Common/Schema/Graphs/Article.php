<?php
namespace AIOSEO\Plugin\Common\Schema\Graphs;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Article graph class.
 *
 * @since 4.0.0
 */
class Article extends Graph {
	/**
	 * Returns the graph data.
	 *
	 * @since 4.0.0
	 *
	 * @return array The graph data.
	 */
	public function get() {
		if ( ! isset( aioseo()->schema->context['object'] ) || ! aioseo()->schema->context['object'] ) {
			return [];
		}

		// Get all terms that the post is assigned to.
		$post           = aioseo()->schema->context['object'];
		$postTaxonomies = get_post_taxonomies( $post );
		$postTerms      = [];
		foreach ( $postTaxonomies as $taxonomy ) {
			$terms = get_the_terms( $post, $taxonomy );
			if ( $terms ) {
				$postTerms = array_merge( $postTerms, wp_list_pluck( $terms, 'name' ) );
			}
		}

		$data = [
			'@type'            => 'Article',
			'@id'              => aioseo()->schema->context['url'] . '#article',
			'name'             => aioseo()->schema->context['name'],
			'description'      => aioseo()->schema->context['description'],
			'inLanguage'       => aioseo()->helpers->currentLanguageCodeBCP47(),
			'headline'         => $post->post_title,
			'author'           => [ '@id' => get_author_posts_url( $post->post_author ) . '#author' ],
			'publisher'        => [ '@id' => trailingslashit( home_url() ) . '#' . aioseo()->options->searchAppearance->global->schema->siteRepresents ],
			'datePublished'    => mysql2date( DATE_W3C, $post->post_date_gmt, false ),
			'dateModified'     => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
			'commentCount'     => get_comment_count( $post->ID )['approved'],
			'articleSection'   => implode( ', ', $postTerms ),
			'mainEntityOfPage' => [ '@id' => aioseo()->schema->context['url'] . '#webpage' ],
			'isPartOf'         => [ '@id' => aioseo()->schema->context['url'] . '#webpage' ],
		];

		$pageNumber = aioseo()->helpers->getPageNumber();
		if ( 1 < $pageNumber ) {
			$data['pagination'] = $pageNumber;
		}

		$image = $this->postImage( $post );
		if ( ! empty( $image ) ) {
			$data['image'] = $image;
		}

		return $data;
	}

	/**
	 * Returns the graph data for the post image.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Post $post The post object.
	 * @return array         The image graph data.
	 */
	private function postImage( $post ) {
		if ( has_post_thumbnail( $post ) ) {
			return $this->image( get_post_thumbnail_id(), 'articleImage' );
		}

		preg_match_all( '#<img[^>]+src="([^">]+)"#', $post->post_content, $matches );
		if ( isset( $matches[1] ) && isset( $matches[1][0] ) ) {
			$url     = aioseo()->helpers->removeImageDimensions( $matches[1][0] );
			$imageId = aioseo()->helpers->attachmentUrlToPostId( $url );
			if ( $imageId ) {
				return $this->image( $imageId, 'articleImage' );
			} else {
				return $this->image( $url, 'articleImage' );
			}
		}

		if ( 'organization' === aioseo()->options->searchAppearance->global->schema->siteRepresents ) {
			$logo = ( new Organization() )->logo();
			if ( ! empty( $logo ) ) {
				$logo['@id'] = trailingslashit( home_url() ) . '#articleImage';

				return $logo;
			}
		} else {
			$avatar = $this->avatar( $post->post_author, 'articleImage' );
			if ( $avatar ) {
				return $avatar;
			}
		}

		$imageId = aioseo()->helpers->getSiteLogoId();
		if ( $imageId ) {
			return $this->image( $imageId, 'articleImage' );
		}

		return [];
	}
}