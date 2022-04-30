<?php
namespace AIOSEO\Plugin\Common\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;

/**
 * Registers notifications for removed filters.
 */
class Filter {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_action( 'shutdown', [ $this, 'flagDeprecated' ] );
	}

	/**
	 * Registers a notification for removed filters that have hooks registered.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function flagDeprecated() {
		$oldFilters = [
			'aioseop_title_format',
			'aioseop_canonical_url',
			'aioseop_description',
			'aioseop_keywords',
			'aioseop_title',
			'aioseop_home_page_title',
			'aioseop_title_page',
			'aioseop_attachment_title',
			'aioseop_title_single',
			'aioseop_archive_title',
			'aiosp_generate_descriptions_from_content',
			'aioseop_canonical_url_pagination',
			'aioseop_pre_tax_types_setting',
			'aiosp_disable',
			'aioseop_wp_head_priority',
			'aioseop_amp_schema',
			'aioseop_amp_description',
			'aioseop_amp_description_full',
			'aioseop_amp_description_attributes',
			'aioseop_description_full',
			'aioseop_description_attributes',
			'aioseop_keywords_attributes',
			'aioseop_prev_link',
			'aioseop_next_link',
			'aioseop_disable_schema',
			'aioseop_description_override',
			'aioseop_canonical_protocol',
			'aioseo_custom_menu_order',
			'manage_aiosp',
			'aioseop_add_post_metabox',
			'aiosp_bad_robots_botlist',
			'aiosp_bad_robots_badbotlist',
			'aiosp_bad_robots_badreferlist',
			'aiosp_bad_robots_allow_bot',
			'aioseop_export_settings_exporter_post_types',
			'aioseop_export_settings_exporter_choices',
			'aioseop_thumbnail_size',
			'aioseop_attachment_size',
			'aioseop_post_metabox_context',
			'aioseop_post_metabox_piority',
			'aioseop_helper_set_help_text',
			'aioseop_module_list',
			'aioseop_get_options',
			'aioseop_localize_script_data',
			'aioseop_home_url',
			'aioseo_include_images_in_sitemap',
			'aioseop_format_date',
			'aioseop_update_check_time',
			'aioseop_admin_flyout_menu',
			'aioseop_disable_twitter_plugin_card',
			'aioseop_robots_meta',
			'aioseop_register_schema_objects',
			'aioseop_schema_layout',
			'aioseop_module_info',
			'aioseop_export_settings',
			'aioseop_opengraph_display',
			'aioseop_opengraph_placeholder',
			'aiosp_opengraph_default_image_type',
			'aiosp_opengraph_default_image',
			'aiosp_opengraph_meta',
			'aioseop_enable_amp_social_meta',
			'aioseop_sitemap_admin_enqueue_selectize',
			'aioseop_gtm_container_id',
			'aioseop_disable_google_tag_manager',
			'aioseop_image_attribute_columns',
			'aioseop_image_seo_title',
			'aioseop_image_seo_alt_tag',
			'aiosp_sitemap_extra',
			'aioseo_news_sitemap_post_types',
			'aisop_woocommerce_hide_hidden_products',
			'aioseop_pro_options',
			'aioseo_video_sitemap_default_thumbnail',
			'aiosp_sitemap_extra_sitemaps',
			'aioseop_robotstxt_sitemap_url',
			'aioseop_sitemap_add_post_types_taxonomy_terms_args',
			'aiosp_sitemap_filename',
			'aiosp_sitemap_custom_*',
			'aiosp_sitemap_data',
			'aioseo_sitemap_ping_urls',
			'aioseop_sitemap_index_filenames',
			'aioseop_sitemap_xsl_url',
			'aiosp_sitemap_rss_latest_limit',
			'aiosp_sitemap_xml_namespace',
			'aiosp_sitemap_addl_pages_only',
			'aiosp_addl_pages',
			'aiosp_sitemap_include_post_types_archives',
			'aioseo_sitemap_author',
			'aiosp_sitemap_prio_item_filter',
			'aioseo_include_images_in_wp_gallery',
			'aioseop_gallery_shortcodes',
			'aioseop_clean_url',
			'aioseop_allowed_image_extensions',
			'aioseop_images_allowed_from_hosts',
			'aioseop_sitemap_exclude_tax_terms',
			'aiosp_sitemap_show_taxonomy',
			'aiosp_sitemap_term_counts',
			'aiosp_sitemap_post_counts',
			'aiosp_sitemap_modify_post_params',
			'aioseop_sitemap_include_password_posts',
			'aiosp_sitemap_post_filter',
			'aiosp_sitemap_post_query',
			'aioseo_news_sitemap_enabled',
			'aiosp_video_sitemap_remove_taxonomy',
			'aioseop_video_sitemap_scan_posts_limit',
			'aiosp_video_sitemap_count_videos_only',
			'aioseop_pro_supported_video_links',
			'aioseop_video_shortcodes',
			'aiosp_video_sitemap_thumbnail',
			'aiosp_video_sitemap_add_videos',
			'aioseop_noindex_rss',
			'aioseop_ga_enable_autotrack',
			'aiosp_google_autotrack',
			'aiosp_google_analytics',
			'aioseop_ga_extra_options',
			'aioseop_pro_gtm_enabled',
			'aioseop_ga_attributes',
			'aioseo_social_meta_tags'
		];

		$inUse = [];
		foreach ( $oldFilters as $filter ) {
			if ( has_filter( $filter ) ) {
				$inUse[] = $filter;
			}
		}

		if ( empty( $inUse ) ) {
			// We've updated our notification so let's remove the old one if it exists.
			$notification = Models\Notification::getNotificationByName( 'deprecated-filters' );
			if ( $notification->exists() ) {
				Models\Notification::deleteNotificationByName( 'deprecated-filters' );
			}

			$notification = Models\Notification::getNotificationByName( 'deprecated-filters-v2' );
			if ( ! $notification->exists() ) {
				return;
			}

			Models\Notification::deleteNotificationByName( 'deprecated-filters-v2' );

			return;
		}

		aioseo()->notices->deprecatedFilters( $inUse );
	}
}