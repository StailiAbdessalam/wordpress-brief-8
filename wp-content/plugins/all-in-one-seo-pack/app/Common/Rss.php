<?php
namespace AIOSEO\Plugin\Common;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds content before or after posts in the RSS feed.
 *
 * @since 4.0.0
 */
class Rss {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_filter( 'the_content_feed', [ $this, 'addRssContent' ] );
		add_filter( 'the_excerpt_rss', [ $this, 'addRssContentExcerpt' ] );
	}

	/**
	 * Adds content before or after the RSS excerpt.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $content The post excerpt.
	 * @return void
	 */
	public function addRssContentExcerpt( $content ) {
		return $this->addRssContent( $content, 'excerpt' );
	}

	/**
	 * Adds content before or after the RSS post.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $content The post content.
	 * @param  string $type    Type of feed.
	 * @return string          The post content with prepended/appended content.
	 */
	public function addRssContent( $content, $type = 'complete' ) {
		$content = trim( $content );
		if ( empty( $content ) ) {
			return '';
		}

		if ( is_feed() ) {
			global $wp_query;
			$isHome = is_home();
			if ( $isHome ) {
				// If this feed is for the static blog page, we must temporarily set "is_home" to false.
				// Otherwise any getPost() calls will return the blog page object for every post in the feed.
				$wp_query->is_home = false;
			}

			$before = aioseo()->tags->replaceTags( aioseo()->options->rssContent->before, get_the_ID() );
			$after  = aioseo()->tags->replaceTags( aioseo()->options->rssContent->after, get_the_ID() );

			if ( $before || $after ) {
				if ( 'excerpt' === $type ) {
					$content = wpautop( $content );
				}
				$content = aioseo()->helpers->decodeHtmlEntities( $before ) . $content . aioseo()->helpers->decodeHtmlEntities( $after );
			}

			// Set back to the original value.
			$wp_query->is_home = $isHome;
		}

		return $content;
	}
}