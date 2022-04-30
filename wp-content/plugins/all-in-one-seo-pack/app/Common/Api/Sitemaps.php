<?php
namespace AIOSEO\Plugin\Common\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;

/**
 * Route class for the API.
 *
 * @since 4.0.0
 */
class Sitemaps {
	/**
	 * Delete all static sitemap files.
	 *
	 * @since 4.0.0
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function deleteStaticFiles() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$files = list_files( get_home_path(), 1 );
		if ( ! count( $files ) ) {
			return;
		}

		$isGeneralSitemapStatic = aioseo()->options->sitemap->general->advancedSettings->enable &&
			in_array( 'staticSitemap', aioseo()->internalOptions->internal->deprecatedOptions, true ) &&
			! aioseo()->options->deprecated->sitemap->general->advancedSettings->dynamic;

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

		if ( ! count( $detectedFiles ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => 'No sitemap files found.'
			], 400 );
		}

		$fs = aioseo()->core->fs;
		if ( ! $fs->isWpfsValid() ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => 'No access to filesystem.'
			], 400 );
		}

		foreach ( $detectedFiles as $file ) {
			$fs->fs->delete( $file, false, 'f' );
		}

		Models\Notification::deleteNotificationByName( 'sitemap-static-files' );

		return new \WP_REST_Response( [
			'success'       => true,
			'notifications' => Models\Notification::getNotifications()
		], 200 );
	}

	/**
	 * Deactivates conflicting plugins.
	 *
	 * @since 4.0.0
	 *
	 * @param  \WP_REST_Request  $request The REST Request
	 * @return \WP_REST_Response          The response.
	 */
	public static function deactivateConflictingPlugins() {
		$error = esc_html__( 'Deactivation failed. Please check permissions and try again.', 'all-in-one-seo-pack' );
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => $error
			], 400 );
		}

		$plugins = array_merge(
			aioseo()->conflictingPlugins->getConflictingPlugins( 'seo' ),
			aioseo()->conflictingPlugins->getConflictingPlugins( 'sitemap' )
		);

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		foreach ( $plugins as $pluginPath ) {
			if ( is_plugin_active( $pluginPath ) ) {
				deactivate_plugins( $pluginPath );
			}
		}

		Models\Notification::deleteNotificationByName( 'conflicting-plugins' );

		return new \WP_REST_Response( [
			'success'       => true,
			'notifications' => Models\Notification::getNotifications()
		], 200 );
	}

	/**
	* Check whether the slug for the HTML sitemap is not in use.
	*
	* @since 4.1.3
	*
	* @param  \WP_REST_Request   $request The REST Request
	* @return \WP_REST_Response           The response.
	*/
	public static function validateHtmlSitemapSlug( $request ) {
		$body = $request->get_json_params();

		$pageUrl = ! empty( $body['pageUrl'] ) ? sanitize_text_field( $body['pageUrl'] ) : '';
		if ( empty( $pageUrl ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => 'No path was provided.'
			], 400 );
		}

		$pageUrl = wp_parse_url( $pageUrl );
		if ( empty( $pageUrl['path'] ) ) {
			return new \WP_REST_Response( [
				'success' => false,
				'message' => 'The given path is invalid.'
			], 400 );
		}

		$path   = trim( $pageUrl['path'], '/' );
		$exists = aioseo()->helpers->pathExists( $path );

		return new \WP_REST_Response( [
			'exists' => $exists
		], 200 );
	}
}