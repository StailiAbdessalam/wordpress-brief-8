<?php
namespace AIOSEO\Plugin\Common\Sitemap;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;

/**
 * Handles our sitemaps.
 *
 * @since 4.0.0
 */
class Sitemap {
	/**
	 * Holds all active addons and their classes.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	public $addons = [];

	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->content  = new Content();
		$this->root     = new Root();
		$this->query    = new Query();
		$this->file     = new File();
		$this->image    = new Image();
		$this->ping     = new Ping();
		$this->priority = new Priority();
		$this->output   = new Output();
		$this->helpers  = new Helpers();

		// Disables the built-in WP sitemap.
		if ( aioseo()->options->sitemap->general->enable ) {
			remove_action( 'init', 'wp_sitemaps_get_server' );
			add_filter( 'wp_sitemaps_enabled', '__return_false' );
			add_filter( 'pre_handle_404', [ $this, 'redirectOtherSitemaps' ] );
		}

		add_action( 'aioseo_static_sitemap_regeneration', [ $this, 'regenerateStaticSitemap' ] );
	}

	/**
	 * Adds our hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		// Watch for XSL requests.
		add_action( 'wp_loaded', [ $this, 'xsl' ] );

		// Add rewrite rules.
		$class = new \ReflectionClass( new Rewrite );
		add_action( 'wp_loaded', [ $class->getName(), 'updateRewriteRules' ] );

		// Remove trailing slash if the sitemap is requested.
		add_filter( 'redirect_canonical', [ $this, 'untrailUrl' ], 10, 2 );

		// Parse the request to see if the sitemap should be returned.
		// This doesn't run if a static file is requested.
		add_filter( 'query_vars', [ $this, 'addWhitelistParams' ] );
		add_action( 'template_redirect', [ $this, 'checkUrlParams' ], 10, 1 );

		// Check if static files need to be updated.
		add_action( 'wp_insert_post', [ $this, 'regenerateOnUpdate' ] );
		add_action( 'edited_term', [ $this, 'regenerateStaticSitemap' ] );

		add_action( 'admin_init', [ $this, 'detectStatic' ] );
	}

	/**
	 * Checks if static sitemap files prevent dynamic sitemap generation.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function detectStatic() {
		$isGeneralSitemapStatic = aioseo()->options->sitemap->general->advancedSettings->enable &&
			in_array( 'staticSitemap', aioseo()->internalOptions->internal->deprecatedOptions, true ) &&
			! aioseo()->options->deprecated->sitemap->general->advancedSettings->dynamic;

		if ( $isGeneralSitemapStatic ) {
			Models\Notification::deleteNotificationByName( 'sitemap-static-files' );

			return;
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$files = list_files( get_home_path(), 1 );
		if ( ! count( $files ) ) {
			return;
		}

		$detectedFiles = [];
		if ( ! $isGeneralSitemapStatic ) {
			foreach ( $files as $filename ) {
				if ( preg_match( '#.*sitemap.*#', $filename ) ) {
					// We don't want to delete the video sitemap here at all.
					$isVideoSitemap = preg_match( '#.*video.*#', $filename ) ? true : false;
					if ( ! $isVideoSitemap ) {
						$detectedFiles[] = $filename;
					}
				}
			}
		}

		$this->maybeShowStaticSitemapNotification( $detectedFiles );
	}

	/**
	 * If there are files, show a notice, otherwise delete it.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $detectedFiles An array of detected files.
	 * @return void
	 */
	protected function maybeShowStaticSitemapNotification( $detectedFiles ) {
		if ( ! count( $detectedFiles ) ) {
			Models\Notification::deleteNotificationByName( 'sitemap-static-files' );

			return;
		}

		$notification = Models\Notification::getNotificationByName( 'sitemap-static-files' );
		if ( $notification->notification_name ) {
			return;
		}

		Models\Notification::addNotification( [
			'slug'              => uniqid(),
			'notification_name' => 'sitemap-static-files',
			'title'             => __( 'Static sitemap files detected', 'all-in-one-seo-pack' ),
			'content'           => sprintf(
				// Translators: 1 - The plugin short name ("AIOSEO"), 2 - Same as previous.
				__( '%1$s has detected static sitemap files in the root folder of your WordPress installation.
				As long as these files are present, %2$s is not able to dynamically generate your sitemap.', 'all-in-one-seo-pack' ),
				AIOSEO_PLUGIN_SHORT_NAME,
				AIOSEO_PLUGIN_SHORT_NAME
			),
			'type'              => 'error',
			'level'             => [ 'all' ],
			'button1_label'     => __( 'Delete Static Files', 'all-in-one-seo-pack' ),
			'button1_action'    => 'http://action#sitemap/delete-static-files',
			'start'             => gmdate( 'Y-m-d H:i:s' )
		] );
	}

	/**
	 * Regenerates the static sitemap files when a post is updated.
	 *
	 * @since 4.0.0
	 *
	 * @param  integer $postId The post ID.
	 * @return void
	 */
	public function regenerateOnUpdate( $postId ) {
		if ( aioseo()->helpers->isValidPost( $postId ) ) {
			$this->scheduleRegeneration();
		}
	}

	/**
	 * Schedules an action to regenerate the static sitemap files.
	 *
	 * @since 4.0.5
	 *
	 * @return void
	 */
	public function scheduleRegeneration() {
		try {
			if (
				! aioseo()->options->deprecated->sitemap->general->advancedSettings->dynamic &&
				! as_next_scheduled_action( 'aioseo_static_sitemap_regeneration' )
			) {
				as_schedule_single_action( time() + 60, 'aioseo_static_sitemap_regeneration', [], 'aioseo' );
			}
		} catch ( \Exception $e ) {
			// Do nothing.
		}
	}

	/**
	 * Regenerates the static sitemap files.
	 *
	 * @since 4.0.5
	 *
	 * @return void
	 */
	public function regenerateStaticSitemap() {
		aioseo()->sitemap->file->generate();
	}

	/**
	 * Removes the trailing slash from the redirect URL.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $redirect The redirect URL.
	 * @param  string $request  The requested URL.
	 * @return string           Either the original requested URL for the sitemap or the redirect URL.
	 */
	public function untrailUrl( $redirect, $request ) {
		if ( preg_match( '#(.*sitemap[0-9]*?.xml|.*sitemap[0-9]*?.xml.gz|.*sitemap.rss)$#i', $request ) ) {
			return $request;
		}

		return $redirect;
	}

	/**
	 * Adds our sitemap params to the query vars whitelist.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $params The array of whitelisted query variable names.
	 * @return array $params The filtered array of whitelisted query variable names.
	 */
	public function addWhitelistParams( $params ) {
		$params[] = 'aiosp_sitemap_path';
		if ( aioseo()->options->sitemap->general->indexes && ! isset( $params['aiosp_sitemap_page'] ) ) {
			$params[] = 'aiosp_sitemap_page';
		}

		foreach ( $this->addons as $classes ) {
			if ( ! empty( $classes['sitemap'] ) ) {
				$params = $classes['sitemap']->addWhitelistParams( $params );
			}
		}

		return $params;
	}

	/**
	 * Checks whether one of our sitemaps is being requested.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function checkUrlParams() {
		global $wp_query;
		if ( ! empty( $wp_query->query_vars['aiosp_sitemap_path'] ) ) {
			$this->generate();
		}
	}

	/**
	 * Generates the requested sitemap.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function generate() {
		// This is a hack to prevent WordPress from running it's default stuff during our processing.
		global $wp_query;
		$wp_query->is_home = false;

		// This prevents the sitemap from including terms twice when WPML is active.
		if ( class_exists( 'SitePress' ) ) {
			global $sitepress_settings;
			// Before building the sitemap make sure links aren't translated.
			// The setting should not be updated in the DB.
			$sitepress_settings['auto_adjust_ids'] = 0;
		}

		// Sets context class properties.
		$this->determineContext();
		// If requested sitemap should be static and doesn't exist, then generate it.
		// We'll then serve it dynamically for the current request so that we don't serve a blank page.
		$this->doesFileExist();

		$options = aioseo()->options->noConflict();
		if ( ! $options->sitemap->{aioseo()->sitemap->type}->enable ) {
			$this->notFoundPage();

			return;
		}

		$entries = aioseo()->sitemap->content->get();
		$total   = aioseo()->sitemap->content->getTotal();
		if ( ! $entries ) {
			foreach ( $this->addons as $classes ) {
				if ( ! empty( $classes['content'] ) ) {
					$entries = $classes['content']->get();
					$total   = count( $entries );
					if ( method_exists( $classes['content'], 'getTotal' ) ) {
						$total = $classes['content']->getTotal();
					}

					if ( $entries ) {
						break;
					}
				}
			}
		}

		if ( 0 === $total && empty( $entries ) ) {
			status_header( 404 );
		}

		global $wp;
		$this->saveXslData(
			$wp->request,
			$entries,
			$total
		);

		$this->headers();
		aioseo()->sitemap->output->output( $entries );
		foreach ( $this->addons as $classes ) {
			if ( ! empty( $classes['output'] ) ) {
				$classes['output']->output( $entries );
			}
		}

		exit();
	}

	/**
	 * Save the data to use on the XSL.
	 *
	 * @since 4.1.5
	 *
	 * @param  string $fileName The sitemap file name.
	 * @param  array  $entries  The sitemap entries.
	 * @param  int    $total    The total sitemap entries count.
	 * @return void
	 */
	public function saveXslData( $fileName, $entries, $total ) {
		$counts     = [];
		$datetime   = [];
		$dateFormat = get_option( 'date_format' );
		$timeFormat = get_option( 'time_format' );

		foreach ( $entries as $index ) {
			$url = ! empty( $index['guid'] ) ? $index['guid'] : $index['loc'];

			if ( ! empty( $index['count'] ) && aioseo()->options->sitemap->general->linksPerIndex !== (int) $index['count'] ) {
				$counts[ $url ] = $index['count'];
			}

			if ( ! empty( $index['lastmod'] ) || ! empty( $index['publicationDate'] ) || ! empty( $index['pubDate'] ) ) {
				$date             = ! empty( $index['lastmod'] ) ? $index['lastmod'] : ( ! empty( $index['publicationDate'] ) ? $index['publicationDate'] : $index['pubDate'] );
				$isTimezone       = ! empty( $index['isTimezone'] ) && $index['isTimezone'];
				$datetime[ $url ] = [
					'date' => $isTimezone ? date_i18n( $dateFormat, strtotime( $date ) ) : get_date_from_gmt( $date, $dateFormat ),
					'time' => $isTimezone ? date_i18n( $timeFormat, strtotime( $date ) ) : get_date_from_gmt( $date, $timeFormat )
				];
			}
		}

		$data = [
			'counts'     => $counts,
			'datetime'   => $datetime,
			'pagination' => [
				'showing' => count( $entries ),
				'total'   => $total
			]
		];

		// Set a high expiration date so we still have the cache for static sitemaps.
		aioseo()->core->cache->update( 'aioseo_sitemap_' . $fileName, $data, MONTH_IN_SECONDS );
	}

	/**
	 * Determines the current sitemap context.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function determineContext() {
		global $wp_query;
		$this->type          = 'rss' === $wp_query->query_vars['aiosp_sitemap_path'] ? 'rss' : 'general';
		$this->filename      = $this->helpers->filename();
		$this->indexName     = $wp_query->query_vars['aiosp_sitemap_path'];
		$this->pageNumber    = ! empty( $wp_query->query_vars['aiosp_sitemap_page'] ) ? $wp_query->query_vars['aiosp_sitemap_page'] - 1 : 0;
		$this->indexes       = aioseo()->options->sitemap->general->indexes;
		$this->linksPerIndex = aioseo()->options->sitemap->{$this->type}->linksPerIndex;
		$this->offset        = $this->linksPerIndex * $this->pageNumber;
		// The sitemap isn't statically generated if we get here.
		$this->isStatic = false;

		foreach ( $this->addons as $classes ) {
			if ( ! empty( $classes['sitemap'] ) ) {
				$classes['sitemap']->determineContext();
			}
		}

		if ( $this->linksPerIndex > 50000 ) {
			$this->linksPerIndex = 50000;
		}
	}

	/**
	 * Checks if static file should be served and generates it if it doesn't exist.
	 *
	 * This essentially acts as a safety net in case a file doesn't exist yet or has been deleted.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function doesFileExist() {
		foreach ( $this->addons as $classes ) {
			if ( ! empty( $classes['sitemap'] ) ) {
				$classes['sitemap']->doesFileExist();
			}
		}

		if (
			'general' !== $this->type ||
			! aioseo()->options->sitemap->general->advancedSettings->enable ||
			! in_array( 'staticSitemap', aioseo()->internalOptions->internal->deprecatedOptions, true ) ||
			aioseo()->options->sitemap->general->advancedSettings->dynamic
		) {
			return;
		}

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		if ( ! aioseo()->core->fs->exists( get_home_path() . $_SERVER['REQUEST_URI'] ) ) {
			$this->scheduleRegeneration();
		}
	}

	/**
	 * Sets the HTTP headers for the sitemap.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function headers() {
		$charset = get_option( 'blog_charset' );
		header( "Content-Type: text/xml; charset=$charset", true );
		header( 'X-Robots-Tag: noindex, follow', true );
	}

	/**
	 * Redirects to a 404 Not Found page if the sitemap is disabled.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function notFoundPage() {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		include( get_404_template() );
		exit();
	}

	/**
	 * Returns the sitemap stylesheet.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function xsl() {
		$requestUrl  = ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		$requestPath = wp_parse_url( $requestUrl, PHP_URL_PATH );

		if ( ! empty( $requestPath ) && preg_match( '#(/default\.xsl)$#i', $requestPath ) ) {
			$this->headers();
			$charset       = get_option( 'blog_charset' );
			$sitemapUrl    = wp_get_referer();
			$sitemapPath   = wp_parse_url( $sitemapUrl, PHP_URL_PATH );
			$sitemapName   = strtoupper( pathinfo( $sitemapPath, PATHINFO_EXTENSION ) );

			// Get Sitemap info by URL.
			preg_match( '/\/(.*?)-?sitemap([0-9]*)\.xml/', $sitemapPath, $sitemapInfo );
			if ( ! empty( $sitemapInfo[1] ) ) {
				switch ( $sitemapInfo[1] ) {
					case 'addl':
						$sitemapName = __( 'Additional Pages', 'all-in-one-seo-pack' );
						break;
					case 'post-archive':
						$sitemapName = __( 'Post Archive', 'all-in-one-seo-pack' );
						break;
					default:
						if ( post_type_exists( $sitemapInfo[1] ) ) {
							$postTypeObject = get_post_type_object( $sitemapInfo[1] );
							$sitemapName    = $postTypeObject->labels->singular_name;
						}
						if ( taxonomy_exists( $sitemapInfo[1] ) ) {
							$taxonomyObject = get_taxonomy( $sitemapInfo[1] );
							$sitemapName    = $taxonomyObject->labels->singular_name;
						}
						break;
				}
			}

			$currentPage = ! empty( $sitemapInfo[2] ) ? (int) $sitemapInfo[2] : 1;

			// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			$linksPerIndex = aioseo()->options->sitemap->general->linksPerIndex;
			$advanced      = aioseo()->options->sitemap->general->advancedSettings->enable;
			$excludeImages = aioseo()->options->sitemap->general->advancedSettings->excludeImages;
			$sitemapParams = aioseo()->helpers->getParametersFromUrl( $sitemapUrl );
			$xslParams     = aioseo()->core->cache->get( 'aioseo_sitemap_' . trim( $sitemapPath, '/' ) );
			// phpcs:enable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

			// Translators: 1 - The sitemap name, 2 - The current page.
			$title = sprintf( __( '%1$s Sitemap %2$s', 'all-in-one-seo-pack' ), $sitemapName, $currentPage > 1 ? $currentPage : '' );
			$title = trim( $title );

			echo '<?xml version="1.0" encoding="' . esc_attr( $charset ) . '"?>';
			include_once( AIOSEO_DIR . '/app/Common/Views/sitemap/xsl/default.php' );
			exit;
		}

		foreach ( $this->addons as $classes ) {
			if ( ! empty( $classes['sitemap'] ) ) {
				$classes['sitemap']->xsl();
			}
		}
	}

	/**
	 * Redirects sitemaps from other WordPress Core/other plugins to ours.
	 *
	 * @since 4.1.5
	 *
	 * @param  bool $preempt Whether to short-circuit default header status handling.
	 * @return bool          Whether to short-circuit default header status handling.
	 */
	public function redirectOtherSitemaps( $preempt ) {
		$sitemapPatterns = [
			'general' => [
				'sitemap\.txt',
				'sitemaps\.xml',
				'sitemap-xml\.xml',
				'sitemap[0-9]+\.xml',
				'sitemap(|[-_\/])?index[0-9]*\.xml',
				'wp-sitemap\.xml',
			],
			'rss'     => [
				'rss[0-9]*\.xml',
			]
		];

		foreach ( aioseo()->addons->getLoadedAddons() as $addonName => $loadedAddon ) {
			if ( ! empty( $loadedAddon->helpers ) && method_exists( $loadedAddon->helpers, 'getOtherSitemapPatterns' ) ) {
				$sitemapPatterns[ $addonName ] = $loadedAddon->helpers->getOtherSitemapPatterns();
			}
		}

		$sitemapPatterns = apply_filters( 'aioseo_sitemap_redirect_filenames', $sitemapPatterns );

		foreach ( $sitemapPatterns as $type => $patterns ) {
			foreach ( $patterns as $pattern ) {
				if ( preg_match( "/^$pattern$/i", $GLOBALS['wp']->request ) ) {
					wp_safe_redirect( aioseo()->sitemap->helpers->getUrl( $type ) );
					exit();
				}
			}
		}

		return $preempt;
	}

	/**
	 * Registers an active sitemap addon and its classes.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function addAddon( $name, $classes ) {
		$this->addons[ $name ] = $classes;
	}
}