<?php
namespace AIOSEO\Plugin\Common\Sitemap;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determines which images are included in a post/term.
 *
 * @since 4.0.0
 */
class Image {
	/**
	 * The image scan action name.
	 *
	 * @since 4.0.13
	 *
	 * @var string
	 */
	private $imageScanAction = 'aioseo_image_sitemap_scan';

	/**
	 * Class constructor.
	 *
	 * @since 4.0.5
	 */
	public function __construct() {
		// Column may not have been created yet.
		if ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'image_scan_date' ) ) {
			return;
		}

		// NOTE: This needs to go above the is_admin check in order for it to run at all.
		add_action( $this->imageScanAction, [ $this, 'scanPosts' ] );

		// Don't schedule a scan if we are not in the admin.
		if ( ! is_admin() ) {
			return;
		}

		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// Don't schedule a scan if an importer or the V3 migration is running.
		// We'll do our scans there.
		if (
			aioseo()->importExport->isImportRunning() ||
			aioseo()->migration->isMigrationRunning()
		) {
			return;
		}
		// Action Scheduler hooks.
		add_filter( 'init', [ $this, 'scheduleScan' ], 3001 );
	}

	/**
	 * Schedules the image sitemap scan.
	 *
	 * @since 4.0.5
	 *
	 * @return void
	 */
	public function scheduleScan() {
		if (
			aioseo()->options->sitemap->general->enable &&
			( ! aioseo()->options->sitemap->general->advancedSettings->enable || ! aioseo()->options->sitemap->general->advancedSettings->excludeImages )
		) {
			aioseo()->helpers->scheduleSingleAction( $this->imageScanAction, 10 );
		}
	}

	/**
	 * Scans posts for images.
	 *
	 * @since 4.0.5
	 *
	 * @return void
	 */
	public function scanPosts() {
		if (
			! aioseo()->options->sitemap->general->enable ||
			( aioseo()->options->sitemap->general->advancedSettings->enable && aioseo()->options->sitemap->general->advancedSettings->excludeImages )
		) {
			return;
		}

		$postsPerScan = apply_filters( 'aioseo_image_sitemap_posts_per_scan', 10 );
		$postTypes    = implode( "', '", aioseo()->helpers->getPublicPostTypes( true ) );

		$posts = aioseo()->core->db
			->start( aioseo()->core->db->db->posts . ' as p', true )
			->select( '`p`.`ID`, `p`.`post_type`, `p`.`post_content`, `p`.`post_excerpt`, `p`.`post_modified_gmt`' )
			->leftJoin( 'aioseo_posts as ap', '`ap`.`post_id` = `p`.`ID`' )
			->whereRaw( '( `ap`.`id` IS NULL OR `p`.`post_modified_gmt` > `ap`.`image_scan_date` OR `ap`.`image_scan_date` IS NULL )' )
			->where( 'p.post_status', 'publish' )
			->whereRaw( "`p`.`post_type` IN ( '$postTypes' )" )
			->limit( $postsPerScan )
			->run()
			->result();

		if ( ! $posts ) {
			aioseo()->helpers->scheduleSingleAction( $this->imageScanAction, 15 * MINUTE_IN_SECONDS, [], true );

			return;
		}

		foreach ( $posts as $post ) {
			$this->scanPost( $post );
		}

		aioseo()->helpers->scheduleSingleAction( $this->imageScanAction, 30, [], true );
	}

	/**
	 * Returns the image entries for a given post.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Post|int $post The post object or ID.
	 * @return void
	 */
	public function scanPost( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! empty( $post->post_password ) ) {
			$this->updatePost( $post->ID );

			return;
		}

		if ( 'attachment' === $post->post_type ) {
			if ( ! wp_attachment_is( 'image', $post ) ) {
				$this->updatePost( $post->ID );

				return;
			}
			$image = $this->buildEntries( [ $post->ID ] );
			$this->updatePost( $post->ID, $image );

			return;
		}

		$images = $this->extract( $post );

		// Get the featured image.
		if ( has_post_thumbnail( $post ) ) {
			$images[] = get_the_post_thumbnail_url( $post );
		}

		$images = $this->removeImageDimensions( $images );

		if ( aioseo()->helpers->isWooCommerceActive() && 'product' === $post->post_type ) {
			$images = array_merge( $images, $this->getProductImages( $post ) );
		}

		$images = apply_filters( 'aioseo_sitemap_images', $images, $post );

		if ( ! $images ) {
			$this->updatePost( $post->ID );

			return;
		}

		// Limit to a 1,000 URLs, in accordance to Google's specifications.
		$images = array_slice( $images, 0, 1000 );
		$this->updatePost( $post->ID, $this->buildEntries( $images ) );
	}

	/**
	 * Returns the image entries for a given term.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Term $term The term object.
	 * @return array         The image entries.
	 */
	public function term( $term ) {
		if ( aioseo()->options->sitemap->general->advancedSettings->excludeImages ) {
			return [];
		}

		$id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( ! $id ) {
			return [];
		}

		return $this->buildEntries( [ $id ] );
	}

	/**
	 * Gets the gallery images for the given WooCommerce product.
	 *
	 * @since 4.1.2
	 *
	 * @param  \WP_Post $post The post object.
	 * @return array          The product gallery images.
	 */
	private function getProductImages( $post ) {
		$productImageIds = get_post_meta( $post->ID, '_product_image_gallery', true );
		if ( ! $productImageIds ) {
			return [];
		}

		$productImageIds = explode( ',', $productImageIds );

		return is_array( $productImageIds ) ? $productImageIds : [];
	}

	/**
	 * Builds the image entries.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $images The images, consisting of attachment IDs or external URLs.
	 * @return array         The image entries.
	 */
	private function buildEntries( $images ) {
		$entries = [];
		foreach ( $images as $image ) {
			$id = $this->getImageId( $image );
			if ( ! is_numeric( $id ) ) {
				$entries[] = [ 'image:loc' => aioseo()->sitemap->helpers->formatUrl( $id ) ];
				continue;
			}

			$entries[] = [
				'image:loc'     => aioseo()->sitemap->helpers->formatUrl( wp_get_attachment_url( $id ) ),
				'image:title'   => get_the_title( $id ),
				'image:caption' => wp_get_attachment_caption( $id )
			];
		}

		return $entries;
	}

	/**
	 * Returns the ID of the image if it's hosted on the site. Otherwise it returns the external URL.
	 *
	 * @since 4.1.3
	 *
	 * @param  int|string $image The attachment ID or URL.
	 * @return int|string        The attachment ID or URL.
	 */
	private function getImageId( $image ) {
		if ( is_numeric( $image ) ) {
			return $image;
		}

		$attachmentId = false;
		if ( aioseo()->helpers->isValidAttachment( $image ) ) {
			$attachmentId = aioseo()->helpers->attachmentUrlToPostId( $image );
		}

		return $attachmentId ? $attachmentId : $image;
	}

	/**
	 * Extracts all image URls from the post.
	 *
	 * @since 4.0.0
	 *
	 * @param  Object $post The post object.
	 * @return array        The image URLs.
	 */
	private function extract( $post ) {
		$urls        = [];
		$postContent = $post->post_content;

		// Get the galleries here instead of through doShortcodes to prevent buggy behaviour.
		// WordPress is supposed to only return the attached images but returns more if the shortcode has no valid attributes, so we need to grab them ourselves.
		$galleries = get_post_galleries( $post, false );
		foreach ( $galleries as $gallery ) {
			foreach ( $gallery['src'] as $imageUrl ) {
				$urls[] = $imageUrl;
			}
		}

		// Now, get rid of them so that we don't process the shortcodes again.
		$regex       = get_shortcode_regex( [ 'gallery' ] );
		$postContent = preg_replace( "/$regex/i", '', $postContent );

		// Get images from Divi if it's active.
		$urls = array_merge( $urls, $this->extractDiviImages( $postContent ) );

		// Now, get the remaining images from image tags in the post content.
		$postContent = aioseo()->helpers->doShortcodes( $postContent, true, $post->ID );
		$postContent = preg_replace( '/\s\s+/u', ' ', trim( $postContent ) ); // Trim both internal and external whitespace.

		preg_match_all( '#<img[^>]+src="([^">]+)"#', $postContent, $matches );
		foreach ( $matches[1] as $url ) {
			$urls[] = aioseo()->helpers->makeUrlAbsolute( $url );
		}

		return array_unique( $urls );
	}

	/**
	 * Extracts images from Divi shortcodes and returns them.
	 *
	 * @since 4.1.8
	 *
	 * @param  string $content The post content.
	 * @return array           The URLs.
	 */
	private function extractDiviImages( $content ) {
		if ( ! defined( 'ET_BUILDER_VERSION' ) ) {
			return [];
		}

		$urls  = [];
		$regex = get_shortcode_regex( [ 'et_pb_image', 'et_pb_gallery' ] );
		preg_match_all( "/$regex/i", $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $shortcode ) {
			$attributes = shortcode_parse_atts( $shortcode[3] );
			if ( ! empty( $attributes['src'] ) ) {
				$urls[] = $attributes['src'];
			}

			if ( ! empty( $attributes['gallery_ids'] ) ) {
				$attachmentIds = explode( ',', $attributes['gallery_ids'] );
				foreach ( $attachmentIds as $attachmentId ) {
					$urls[] = wp_get_attachment_url( $attachmentId );
				}
			}
		}

		return $urls;
	}

	/**
	 * Removes image dimensions from the slug.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $urls         The image URLs.
	 * @return array $preparedUrls The formatted image URLs.
	 */
	private function removeImageDimensions( $urls ) {
		$preparedUrls = [];
		foreach ( $urls as $url ) {
			$preparedUrls[] = aioseo()->helpers->removeImageDimensions( $url );
		}

		return array_filter( $preparedUrls );
	}

	/**
	 * Stores the image data for a given post in our DB table.
	 *
	 * @since 4.0.5
	 *
	 * @param  int   $postId The post ID.
	 * @param  array $images The images.
	 * @return void
	 */
	private function updatePost( $postId, $images = [] ) {
		$post                    = \AIOSEO\Plugin\Common\Models\Post::getPost( $postId );
		$meta                    = $post->exists() ? [] : aioseo()->migration->meta->getMigratedPostMeta( $postId );
		$meta['post_id']         = $postId;
		$meta['images']          = ! empty( $images ) ? $images : null;
		$meta['image_scan_date'] = gmdate( 'Y-m-d H:i:s' );

		$post->set( $meta );
		$post->save();
	}
}