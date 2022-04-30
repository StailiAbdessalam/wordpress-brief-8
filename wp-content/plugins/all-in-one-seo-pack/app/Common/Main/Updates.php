<?php
namespace AIOSEO\Plugin\Common\Main;

use \AIOSEO\Plugin\Common\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Updater class.
 *
 * @since 4.0.0
 */
class Updates {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		add_action( 'init', [ $this, 'init' ], 1001 );
		add_action( 'init', [ $this, 'runUpdates' ], 1002 );
		add_action( 'init', [ $this, 'updateLatestVersion' ], 3000 );
	}

	/**
	 * Sets the latest active version if it is not set yet.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		if ( '0.0' !== aioseo()->internalOptions->internal->lastActiveVersion ) {
			return;
		}

		// It's possible the user may not have capabilities. Let's add them now.
		aioseo()->access->addCapabilities();

		$oldOptions = get_option( 'aioseop_options' );
		if ( empty( $oldOptions ) && ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			// Sets 30 second transient for welcome screen redirect on activation.
			aioseo()->core->cache->update( 'activation_redirect', true, 30 );
		}

		if ( ! empty( $oldOptions['last_active_version'] ) ) {
			aioseo()->internalOptions->internal->lastActiveVersion = $oldOptions['last_active_version'];
		}

		$this->addInitialCustomTablesForV4();
		add_action( 'wp_loaded', [ $this, 'setDefaultSocialImages' ], 1001 );
	}

	/**
	 * Runs our migrations.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function runUpdates() {
		// The dynamic options have not yet fully loaded, so let's refresh here to force that to happen.
		aioseo()->dynamicOptions->refresh(); // TODO: Check if we still need this since it already runs on 999 in the main AIOSEO file.

		$lastActiveVersion = aioseo()->internalOptions->internal->lastActiveVersion;
		if ( version_compare( $lastActiveVersion, '4.0.5', '<' ) ) {
			$this->addImageScanDateColumn();
		}

		if ( version_compare( $lastActiveVersion, '4.0.6', '<' ) ) {
			$this->disableTwitterUseOgDefault();
			$this->updateMaxImagePreviewDefault();
		}

		if ( ! aioseo()->pro && version_compare( $lastActiveVersion, '4.0.6', '=' ) && 'posts' !== get_option( 'show_on_front' ) ) {
			aioseo()->migration->helpers->redoMigration();
		}

		if ( version_compare( $lastActiveVersion, '4.0.13', '<' ) ) {
			$this->removeDuplicateRecords();
		}

		if ( version_compare( $lastActiveVersion, '4.0.17', '<' ) ) {
			$this->removeLocationColumn();
		}

		if ( version_compare( $lastActiveVersion, '4.1.2', '<' ) ) {
			$this->clearProductImages();
		}

		if ( version_compare( $lastActiveVersion, '4.1.3', '<' ) ) {
			$this->addNotificationsNewColumn();
			$this->noindexWooCommercePages();
			$this->accessControlNewCapabilities();
		}

		if ( version_compare( $lastActiveVersion, '4.1.3.3', '<' ) ) {
			$this->accessControlNewCapabilities();
		}

		if ( version_compare( $lastActiveVersion, '4.1.4.3', '<' ) ) {
			$this->migrateDynamicSettings();
		}

		if ( version_compare( $lastActiveVersion, '4.1.5', '<' ) ) {
			aioseo()->helpers->unscheduleAction( 'aioseo_cleanup_action_scheduler' );
			// Schedule routine to remove our old transients from the options table.
			aioseo()->helpers->scheduleSingleAction( aioseo()->core->cachePrune->getOptionCacheCleanAction(), MINUTE_IN_SECONDS );

			// Refresh with new Redirects capability.
			$this->accessControlNewCapabilities();

			// Regenerate the sitemap if using a static one to update the data for the new stylesheets.
			aioseo()->sitemap->regenerateStaticSitemap();

			$this->fixSchemaTypeDefault();
		}

		if ( version_compare( $lastActiveVersion, '4.1.6', '<' ) ) {
			// Clear the cache so addons get reset.
			aioseo()->core->cache->clear();

			// Remove the recurring scheduled action for notifications.
			aioseo()->helpers->unscheduleAction( 'aioseo_admin_notifications_update' );

			$this->migrateOgTwitterImageColumns();

			// Set the OG data to false for current installs.
			aioseo()->options->social->twitter->general->useOgData = false;
		}

		if ( version_compare( $lastActiveVersion, '4.1.8', '<' ) ) {
			$this->addLimitModifiedDateColumn();

			// Refresh with new Redirects Page capability.
			$this->accessControlNewCapabilities();
		}

		if ( version_compare( $lastActiveVersion, '4.1.9', '<' ) ) {
			$this->fixTaxonomyTags();
			$this->removeRevisionRecords();
		}

		if ( version_compare( $lastActiveVersion, '4.0.0', '>=' ) && version_compare( $lastActiveVersion, '4.2.0', '<' ) ) {
			$this->migrateDeprecatedRunShortcodesSetting();
		}

		do_action( 'aioseo_run_updates', $lastActiveVersion );
	}

	/**
	 * Retrieve the raw options from the database for migration.
	 *
	 * @since 4.1.4
	 *
	 * @return array An array of options.
	 */
	private function getRawOptions() {
		// Options from the DB.
		$commonOptions = json_decode( get_option( aioseo()->options->optionsName ), true );
		if ( empty( $commonOptions ) ) {
			$commonOptions = [];
		}

		return $commonOptions;
	}

	/**
	 * Updates the latest version after all migrations and updates have run.
	 *
	 * @since 4.0.3
	 *
	 * @return void
	 */
	public function updateLatestVersion() {
		if ( aioseo()->internalOptions->internal->lastActiveVersion === aioseo()->version ) {
			return;
		}

		aioseo()->internalOptions->internal->lastActiveVersion = aioseo()->version;

		// Bust the tableExists and columnExists cache.
		aioseo()->internalOptions->database->installedTables = '';

		// Bust the DB cache so we can make sure that everything is fresh.
		aioseo()->core->db->bustCache();
	}

	/**
	 * Adds our custom tables for V4.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function addInitialCustomTablesForV4() {
		$db             = aioseo()->core->db->db;
		$charsetCollate = '';

		if ( ! empty( $db->charset ) ) {
			$charsetCollate .= "DEFAULT CHARACTER SET {$db->charset}";
		}
		if ( ! empty( $db->collate ) ) {
			$charsetCollate .= " COLLATE {$db->collate}";
		}

		// Check for notifications table.
		if ( ! aioseo()->core->db->tableExists( 'aioseo_notifications' ) ) {
			$tableName = $db->prefix . 'aioseo_notifications';

			aioseo()->core->db->execute(
				"CREATE TABLE {$tableName} (
					id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					slug varchar(13) NOT NULL,
					title text NOT NULL,
					content longtext NOT NULL,
					type varchar(64) NOT NULL,
					level text NOT NULL,
					notification_id bigint(20) unsigned DEFAULT NULL,
					notification_name varchar(255) DEFAULT NULL,
					start datetime DEFAULT NULL,
					end datetime DEFAULT NULL,
					button1_label varchar(255) DEFAULT NULL,
					button1_action varchar(255) DEFAULT NULL,
					button2_label varchar(255) DEFAULT NULL,
					button2_action varchar(255) DEFAULT NULL,
					dismissed tinyint(1) NOT NULL DEFAULT 0,
					created datetime NOT NULL,
					updated datetime NOT NULL,
					PRIMARY KEY (id),
					UNIQUE KEY ndx_aioseo_notifications_slug (slug),
					KEY ndx_aioseo_notifications_dates (start, end),
					KEY ndx_aioseo_notifications_type (type),
					KEY ndx_aioseo_notifications_dismissed (dismissed)
				) {$charsetCollate};"
			);
		}

		if ( ! aioseo()->core->db->tableExists( 'aioseo_posts' ) ) {
			$tableName = $db->prefix . 'aioseo_posts';

			// Incorrect defaults are adjusted below through migrations.
			aioseo()->core->db->execute(
				"CREATE TABLE {$tableName} (
					id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					post_id bigint(20) unsigned NOT NULL,
					title text DEFAULT NULL,
					description text DEFAULT NULL,
					keywords mediumtext DEFAULT NULL,
					keyphrases longtext DEFAULT NULL,
					page_analysis longtext DEFAULT NULL,
					canonical_url text DEFAULT NULL,
					og_title text DEFAULT NULL,
					og_description text DEFAULT NULL,
					og_object_type varchar(64) DEFAULT 'default',
					og_image_type varchar(64) DEFAULT 'default',
					og_image_custom_url text DEFAULT NULL,
					og_image_custom_fields text DEFAULT NULL,
					og_custom_image_width int(11) DEFAULT NULL,
					og_custom_image_height int(11) DEFAULT NULL,
					og_video varchar(255) DEFAULT NULL,
					og_custom_url text DEFAULT NULL,
					og_article_section text DEFAULT NULL,
					og_article_tags text DEFAULT NULL,
					twitter_use_og tinyint(1) DEFAULT 1,
					twitter_card varchar(64) DEFAULT 'default',
					twitter_image_type varchar(64) DEFAULT 'default',
					twitter_image_custom_url text DEFAULT NULL,
					twitter_image_custom_fields text DEFAULT NULL,
					twitter_title text DEFAULT NULL,
					twitter_description text DEFAULT NULL,
					seo_score int(11) DEFAULT 0 NOT NULL,
					schema_type varchar(20) DEFAULT NULL,
					schema_type_options longtext DEFAULT NULL,
					pillar_content tinyint(1) DEFAULT NULL,
					robots_default tinyint(1) DEFAULT 1 NOT NULL,
					robots_noindex tinyint(1) DEFAULT 0 NOT NULL,
					robots_noarchive tinyint(1) DEFAULT 0 NOT NULL,
					robots_nosnippet tinyint(1) DEFAULT 0 NOT NULL,
					robots_nofollow tinyint(1) DEFAULT 0 NOT NULL,
					robots_noimageindex tinyint(1) DEFAULT 0 NOT NULL,
					robots_noodp tinyint(1) DEFAULT 0 NOT NULL,
					robots_notranslate tinyint(1) DEFAULT 0 NOT NULL,
					robots_max_snippet int(11) DEFAULT NULL,
					robots_max_videopreview int(11) DEFAULT NULL,
					robots_max_imagepreview varchar(20) DEFAULT 'none',
					tabs mediumtext DEFAULT NULL,
					images longtext DEFAULT NULL,
					priority tinytext DEFAULT NULL,
					frequency tinytext DEFAULT NULL,
					videos longtext DEFAULT NULL,
					video_thumbnail text DEFAULT NULL,
					video_scan_date datetime DEFAULT NULL,
					local_seo longtext DEFAULT NULL,
					created datetime NOT NULL,
					updated datetime NOT NULL,
					PRIMARY KEY (id),
					KEY ndx_aioseo_posts_post_id (post_id)
				) {$charsetCollate};"
			);
		}

		// Reset the cache for the installed tables.
		aioseo()->internalOptions->database->installedTables = '';
	}

	/**
	 * Sets the default social images.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function setDefaultSocialImages() {
		$siteLogo = aioseo()->helpers->getSiteLogoUrl();
		if ( $siteLogo && ! aioseo()->internalOptions->internal->migratedVersion ) {
			if ( ! aioseo()->options->social->facebook->general->defaultImagePosts ) {
				aioseo()->options->social->facebook->general->defaultImagePosts = $siteLogo;
			}
			if ( ! aioseo()->options->social->twitter->general->defaultImagePosts ) {
				aioseo()->options->social->twitter->general->defaultImagePosts = $siteLogo;
			}
		}
	}

	/**
	 * Adds the image scan date column to our posts table.
	 *
	 * @since 4.0.5
	 *
	 * @return void
	 */
	public function addImageScanDateColumn() {
		if ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'image_scan_date' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				ADD image_scan_date datetime DEFAULT NULL AFTER images"
			);

			// Reset the cache for the installed tables.
			aioseo()->internalOptions->database->installedTables = '';
		}
	}

	/**
	 * Modifes the default value of the twitter_use_og column.
	 *
	 * @since 4.0.6
	 *
	 * @return void
	 */
	public function disableTwitterUseOgDefault() {
		if ( aioseo()->core->db->tableExists( 'aioseo_posts' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				MODIFY twitter_use_og tinyint(1) DEFAULT 0"
			);
		}
	}

	/**
	 * Modifes the default value of the robots_max_imagepreview column.
	 *
	 * @since 4.0.6
	 *
	 * @return void
	 */
	public function updateMaxImagePreviewDefault() {
		if ( aioseo()->core->db->tableExists( 'aioseo_posts' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				MODIFY robots_max_imagepreview varchar(20) DEFAULT 'large'"
			);
		}
	}

	/**
	 * Deletes duplicate records in our custom tables.
	 *
	 * @since 4.0.13
	 *
	 * @return void
	 */
	public function removeDuplicateRecords() {
		$duplicates = aioseo()->core->db->start( 'aioseo_posts' )
			->select( 'post_id, min(id) as id' )
			->groupBy( 'post_id having count(post_id) > 1' )
			->orderBy( 'count(post_id) DESC' )
			->run()
			->result();

		if ( empty( $duplicates ) ) {
			return;
		}

		foreach ( $duplicates as $duplicate ) {
			$postId        = $duplicate->post_id;
			$firstRecordId = $duplicate->id;

			aioseo()->core->db->delete( 'aioseo_posts' )
				->whereRaw( "( id > $firstRecordId AND post_id = $postId )" )
				->run();
		}
	}

	/**
	 * Removes the location column.
	 *
	 * @since 4.0.17
	 *
	 * @return void
	 */
	public function removeLocationColumn() {
		if ( aioseo()->core->db->columnExists( 'aioseo_posts', 'location' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				DROP location"
			);
		}
	}

	/**
	 * Clears the image data for WooCommerce Products so that we scan them again and include product gallery images.
	 *
	 * @since 4.1.2
	 *
	 * @return void
	 */
	public function clearProductImages() {
		if ( ! aioseo()->helpers->isWooCommerceActive() ) {
			return;
		}

		aioseo()->core->db->update( 'aioseo_posts as ap' )
			->join( 'posts as p', 'ap.post_id = p.ID' )
			->where( 'p.post_type', 'product' )
			->set(
				[
					'images'          => null,
					'image_scan_date' => null
				]
			)
			->run();
	}

	/**
	 * Adds the new flag to the notifications table.
	 *
	 * @since 4.1.3
	 *
	 * @return void
	 */
	public function addNotificationsNewColumn() {
		if ( ! aioseo()->core->db->columnExists( 'aioseo_notifications', 'new' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_notifications';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				ADD new tinyint(1) NOT NULL DEFAULT 1 AFTER dismissed"
			);

			// Reset the cache for the installed tables.
			aioseo()->internalOptions->database->installedTables = '';

			aioseo()->core->db
				->update( 'aioseo_notifications' )
				->where( 'new', 1 )
				->set( 'new', 0 )
				->run();
		}
	}

	/**
	 * Noindexes the WooCommerce cart, checkout and account pages.
	 *
	 * @since 4.1.3
	 *
	 * @return void
	 */
	public function noindexWooCommercePages() {
		if ( ! aioseo()->helpers->isWooCommerceActive() ) {
			return;
		}

		$cartId     = (int) get_option( 'woocommerce_cart_page_id' );
		$checkoutId = (int) get_option( 'woocommerce_checkout_page_id' );
		$accountId  = (int) get_option( 'woocommerce_myaccount_page_id' );

		$cartPage     = Models\Post::getPost( $cartId );
		$checkoutPage = Models\Post::getPost( $checkoutId );
		$accountPage  = Models\Post::getPost( $accountId );

		$newMeta = [
			'robots_default' => false,
			'robots_noindex' => true
		];

		if ( $cartPage->exists() ) {
			$cartPage->set( $newMeta );
			$cartPage->save();
		}
		if ( $checkoutPage->exists() ) {
			$checkoutPage->set( $newMeta );
			$checkoutPage->save();
		}
		if ( $accountPage->exists() ) {
			$accountPage->set( $newMeta );
			$accountPage->save();
		}
	}

	/**
	 * Adds the new capabilities for all the roles.
	 *
	 * @since 4.1.3
	 *
	 * @return void
	 */
	public function accessControlNewCapabilities() {
		aioseo()->access->addCapabilities();
	}

	/**
	 * Migrate dynamic settings to a separate options structure.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	public function migrateDynamicSettings() {
		$rawOptions = $this->getRawOptions();
		$options    = aioseo()->dynamicOptions->noConflict();

		// Sitemap post type priorities/frequencies.
		if (
			! empty( $rawOptions['sitemap']['dynamic']['priority']['postTypes'] )
		) {
			foreach ( $rawOptions['sitemap']['dynamic']['priority']['postTypes'] as $postTypeName => $data ) {
				if ( $options->sitemap->priority->postTypes->has( $postTypeName ) ) {
					$options->sitemap->priority->postTypes->$postTypeName->priority  = $data['priority'];
					$options->sitemap->priority->postTypes->$postTypeName->frequency = $data['frequency'];
				}
			}
		}

		// Sitemap taxonomy priorities/frequencies.
		if (
			! empty( $rawOptions['sitemap']['dynamic']['priority']['taxonomies'] )
		) {
			foreach ( $rawOptions['sitemap']['dynamic']['priority']['taxonomies'] as $taxonomyName => $data ) {
				if ( $options->sitemap->priority->taxonomies->has( $taxonomyName ) ) {
					$options->sitemap->priority->taxonomies->$taxonomyName->priority  = $data['priority'];
					$options->sitemap->priority->taxonomies->$taxonomyName->frequency = $data['frequency'];
				}
			}
		}

		// Facebook post type object types.
		if (
			! empty( $rawOptions['social']['facebook']['general']['dynamic']['postTypes'] )
		) {
			foreach ( $rawOptions['social']['facebook']['general']['dynamic']['postTypes'] as $postTypeName => $data ) {
				if ( $options->social->facebook->general->postTypes->has( $postTypeName ) ) {
					$options->social->facebook->general->postTypes->$postTypeName->objectType = $data['objectType'];
				}
			}
		}

		// Search appearance post type data.
		if (
			! empty( $rawOptions['searchAppearance']['dynamic']['postTypes'] )
		) {
			foreach ( $rawOptions['searchAppearance']['dynamic']['postTypes'] as $postTypeName => $data ) {
				if ( $options->searchAppearance->postTypes->has( $postTypeName ) ) {
					$options->searchAppearance->postTypes->$postTypeName->show            = $data['show'];
					$options->searchAppearance->postTypes->$postTypeName->title           = $data['title'];
					$options->searchAppearance->postTypes->$postTypeName->metaDescription = $data['metaDescription'];
					$options->searchAppearance->postTypes->$postTypeName->schemaType      = $data['schemaType'];
					$options->searchAppearance->postTypes->$postTypeName->webPageType     = $data['webPageType'];
					$options->searchAppearance->postTypes->$postTypeName->articleType     = $data['articleType'];
					$options->searchAppearance->postTypes->$postTypeName->customFields    = $data['customFields'];

					// Advanced settings.
					$advanced = ! empty( $data['advanced']['robotsMeta'] ) ? $data['advanced']['robotsMeta'] : null;
					if ( ! empty( $advanced ) ) {
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->default         = $data['advanced']['robotsMeta']['default'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->noindex         = $data['advanced']['robotsMeta']['noindex'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->nofollow        = $data['advanced']['robotsMeta']['nofollow'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->noarchive       = $data['advanced']['robotsMeta']['noarchive'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->noimageindex    = $data['advanced']['robotsMeta']['noimageindex'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->notranslate     = $data['advanced']['robotsMeta']['notranslate'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->nosnippet       = $data['advanced']['robotsMeta']['nosnippet'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->noodp           = $data['advanced']['robotsMeta']['noodp'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->maxSnippet      = $data['advanced']['robotsMeta']['maxSnippet'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->maxVideoPreview = $data['advanced']['robotsMeta']['maxVideoPreview'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->robotsMeta->maxImagePreview = $data['advanced']['robotsMeta']['maxImagePreview'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->showDateInGooglePreview     = $data['advanced']['showDateInGooglePreview'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->showPostThumbnailInSearch   = $data['advanced']['showPostThumbnailInSearch'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->showMetaBox                 = $data['advanced']['showMetaBox'];
						$options->searchAppearance->postTypes->$postTypeName->advanced->bulkEditing                 = $data['advanced']['bulkEditing'];
					}

					if ( 'attachment' === $postTypeName ) {
						$options->searchAppearance->postTypes->$postTypeName->redirectAttachmentUrls = $data['redirectAttachmentUrls'];
					}
				}
			}
		}

		// Search appearance taxonomy data.
		if (
			! empty( $rawOptions['searchAppearance']['dynamic']['taxonomies'] )
		) {
			foreach ( $rawOptions['searchAppearance']['dynamic']['taxonomies'] as $taxonomyName => $data ) {
				if ( $options->searchAppearance->taxonomies->has( $taxonomyName ) ) {
					$options->searchAppearance->taxonomies->$taxonomyName->show            = $data['show'];
					$options->searchAppearance->taxonomies->$taxonomyName->title           = $data['title'];
					$options->searchAppearance->taxonomies->$taxonomyName->metaDescription = $data['metaDescription'];

					// Advanced settings.
					$advanced = ! empty( $data['advanced']['robotsMeta'] ) ? $data['advanced']['robotsMeta'] : null;
					if ( ! empty( $advanced ) ) {
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->default         = $data['advanced']['robotsMeta']['default'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->noindex         = $data['advanced']['robotsMeta']['noindex'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->nofollow        = $data['advanced']['robotsMeta']['nofollow'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->noarchive       = $data['advanced']['robotsMeta']['noarchive'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->noimageindex    = $data['advanced']['robotsMeta']['noimageindex'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->notranslate     = $data['advanced']['robotsMeta']['notranslate'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->nosnippet       = $data['advanced']['robotsMeta']['nosnippet'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->noodp           = $data['advanced']['robotsMeta']['noodp'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->maxSnippet      = $data['advanced']['robotsMeta']['maxSnippet'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->maxVideoPreview = $data['advanced']['robotsMeta']['maxVideoPreview'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->robotsMeta->maxImagePreview = $data['advanced']['robotsMeta']['maxImagePreview'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->showDateInGooglePreview     = $data['advanced']['showDateInGooglePreview'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->showPostThumbnailInSearch   = $data['advanced']['showPostThumbnailInSearch'];
						$options->searchAppearance->taxonomies->$taxonomyName->advanced->showMetaBox                 = $data['advanced']['showMetaBox'];
					}
				}
			}
		}
	}

	/**
	 * Fixes the default value for the post schema type.
	 *
	 * @since 4.1.5
	 *
	 * @return void
	 */
	private function fixSchemaTypeDefault() {
		if ( aioseo()->core->db->tableExists( 'aioseo_posts' ) && aioseo()->core->db->columnExists( 'aioseo_posts', 'schema_type' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				MODIFY schema_type varchar(20) DEFAULT 'default'"
			);
		}
	}

	/**
	 * Add in image with/height columns and image URL for caching.
	 *
	 * @since 4.1.6
	 *
	 * @return void
	 */
	protected function migrateOgTwitterImageColumns() {
		if ( aioseo()->core->db->tableExists( 'aioseo_posts' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';

			// OG Columns.
			if ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'og_image_url' ) ) {
				aioseo()->core->db->execute(
					"ALTER TABLE {$tableName} ADD og_image_url text DEFAULT NULL AFTER og_image_type"
				);
			}

			if ( aioseo()->core->db->columnExists( 'aioseo_posts', 'og_custom_image_height' ) ) {
				aioseo()->core->db->execute(
					"ALTER TABLE {$tableName} CHANGE COLUMN og_custom_image_height og_image_height int(11) DEFAULT NULL AFTER og_image_url"
				);
			} elseif ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'og_image_height' ) ) {
				aioseo()->core->db->execute(
					"ALTER TABLE {$tableName} ADD og_image_height int(11) DEFAULT NULL AFTER og_image_url"
				);
			}

			if ( aioseo()->core->db->columnExists( 'aioseo_posts', 'og_custom_image_width' ) ) {
				aioseo()->core->db->execute(
					"ALTER TABLE {$tableName} CHANGE COLUMN og_custom_image_width og_image_width int(11) DEFAULT NULL AFTER og_image_url"
				);
			} elseif ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'og_image_width' ) ) {
				aioseo()->core->db->execute(
					"ALTER TABLE {$tableName} ADD og_image_width int(11) DEFAULT NULL AFTER og_image_url"
				);
			}

			// Twitter image url columnn.
			if ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'twitter_image_url' ) ) {
				aioseo()->core->db->execute(
					"ALTER TABLE {$tableName} ADD twitter_image_url text DEFAULT NULL AFTER twitter_image_type"
				);
			}

			// Reset the cache for the installed tables.
			aioseo()->internalOptions->database->installedTables = '';
		}
	}

	/**
	 * Adds the limit modified date column to our posts table.
	 *
	 * @since 4.1.8
	 *
	 * @return void
	 */
	private function addLimitModifiedDateColumn() {
		if ( ! aioseo()->core->db->columnExists( 'aioseo_posts', 'limit_modified_date' ) ) {
			$tableName = aioseo()->core->db->db->prefix . 'aioseo_posts';
			aioseo()->core->db->execute(
				"ALTER TABLE {$tableName}
				ADD limit_modified_date tinyint(1) NOT NULL DEFAULT 0 AFTER local_seo"
			);

			// Reset the cache for the installed tables.
			aioseo()->internalOptions->database->installedTables = '';
		}
	}

	/**
	 * Fixes tags that should not be in the search appearance taxonomy options.
	 *
	 * @since 4.1.9
	 *
	 * @return void
	 */
	protected function fixTaxonomyTags() {
		$searchAppearanceTaxonomies = aioseo()->dynamicOptions->searchAppearance->taxonomies->all();

		$replaces = [
			'#breadcrumb_separator' => '#separator_sa',
			'#breadcrumb_'          => '#',
			'#blog_title'           => '#site_title'
		];

		foreach ( $searchAppearanceTaxonomies as $taxonomy => $searchAppearanceTaxonomy ) {
			aioseo()->dynamicOptions->searchAppearance->taxonomies->{$taxonomy}->title = str_replace(
				array_keys( $replaces ),
				array_values( $replaces ),
				$searchAppearanceTaxonomy['title']
			);

			aioseo()->dynamicOptions->searchAppearance->taxonomies->{$taxonomy}->metaDescription = str_replace(
				array_keys( $replaces ),
				array_values( $replaces ),
				$searchAppearanceTaxonomy['metaDescription']
			);
		}

	}

	/**
	 * Removes any AIOSEO Post records for revisions.
	 *
	 * @since 4.1.9
	 *
	 * @return void
	 */
	private function removeRevisionRecords() {
		$postsTableName       = aioseo()->db->prefix . 'posts';
		$aioseoPostsTableName = aioseo()->db->prefix . 'aioseo_posts';
		aioseo()->db->execute(
			"DELETE FROM `$aioseoPostsTableName`
			WHERE `post_id` IN (
				SELECT `ID`
				FROM `$postsTableName`
				WHERE `post_parent` != 0
				AND `post_type` = 'revision'
				AND `post_status` = 'inherit'
			)"
		);
	}

	/**
	 * Enables the new shortcodes parsing setting if it was already enabled before as a deprecated setting.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	private function migrateDeprecatedRunShortcodesSetting() {
		if (
			in_array( 'runShortcodesInDescription', aioseo()->internalOptions->deprecatedOptions, true ) &&
			! aioseo()->options->deprecated->searchAppearance->advanced->runShortcodesInDescription
		) {
			return;
		}

		aioseo()->options->searchAppearance->advanced->runShortcodes = true;
	}
}