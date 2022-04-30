<?php
namespace AIOSEO\Plugin\Common\Sitemap;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles our sitemap rewrite rules.
 *
 * @since 4.0.0
 */
class Rewrite {
	/**
	 * Returns our sitemap rewrite rules.
	 *
	 * @since 4.0.0
	 *
	 * @return array $rules The compiled array of rewrite rules, keyed by their regex pattern.
	 */
	private static function getRewriteRules() {
		$rules = [];

		foreach ( aioseo()->sitemap->addons as $classes ) {
			if ( ! empty( $classes['rewrite'] ) ) {
				$rules += $classes['rewrite']->get();
			}
		}

		if ( aioseo()->options->sitemap->general->enable ) {
			$filename       = aioseo()->sitemap->helpers->filename( 'general' );
			$indexesEnabled = aioseo()->options->sitemap->general->indexes();
			if ( $filename && 'sitemap' !== $filename ) {
				// Check if the user has a custom filename from the v3 migration.
				$rules += [
					"$filename.xml" => 'index.php?aiosp_sitemap_path=root',
				];
				if ( $indexesEnabled ) {
					$rules += [
						"(.+)-$filename.xml"      => 'index.php?aiosp_sitemap_path=$matches[1]',
						"(.+)-$filename(\d+).xml" => 'index.php?aiosp_sitemap_path=$matches[1]&aiosp_sitemap_page=$matches[2]'
					];
				}
			}

			$rules += [
				'sitemap.xml' => 'index.php?aiosp_sitemap_path=root',
			];
			if ( $indexesEnabled ) {
				$rules += [
					'(.+)-sitemap.xml'      => 'index.php?aiosp_sitemap_path=$matches[1]',
					'(.+)-sitemap(\d+).xml' => 'index.php?aiosp_sitemap_path=$matches[1]&aiosp_sitemap_page=$matches[2]'
				];
			}
		}

		if ( aioseo()->options->sitemap->rss->enable ) {
			$rules += [
				'sitemap.rss'        => 'index.php?aiosp_sitemap_path=rss',
				'sitemap.latest.rss' => 'index.php?aiosp_sitemap_path=rss'
			];
		}

		return $rules;
	}

	/**
	 * Updates the rewrite rules.
	 *
	 * @since 4.0.0
	 *
	 * @param  boolean $forceUpdate Force an update of the rewrite rules.
	 * @return void
	 */
	public static function updateRewriteRules( $forceUpdate = false ) {
		$existingRules = (array) get_option( 'rewrite_rules', [] );
		if ( empty( $existingRules ) ) {
			// We need to flush the rules after the Search Appearance options in Yoast SEO are updated; otherwise the regular rewrite rules are missing.
			return flush_rewrite_rules( false );
		}

		// Don't update rewrite rules unless a sitemap is requested or it is forced.
		if (
			! $forceUpdate &&
			(
				is_admin() ||
				! isset( $_SERVER['REQUEST_URI'] ) ||
				// Don't escape the subject here - this will break the pattern.
				! preg_match( '/(\.xml|\.xml\.gz|\.rss)$/', wp_unslash( $_SERVER['REQUEST_URI'] ), $matches ) // phpcs:ignore HM.Security.ValidatedSanitizedInput.InputNotSanitized
			)
		) {
			return;
		}

		// Remove all our existing rules before we update the option.
		$existingRules = self::removeRewriteRules( $existingRules );

		// Push our own sitemap rewrite rules to the top of the list.
		$newRules = self::getRewriteRules() + $existingRules;
		update_option( 'rewrite_rules', $newRules );
	}

	/**
	 * Removes our sitemap rewrite rules.
	 *
	 * @since 4.0.12
	 *
	 * @param  array   $rules  The rewrite rules.
	 * @param  boolean $update Whether the rewrite rules should be updated.
	 * @return array   $rules  The rewrite rules without ours.
	 */
	public static function removeRewriteRules( $rules = [], $update = false ) {
		$rules = ! empty( $rules ) ? $rules : get_option( 'rewrite_rules' );
		if ( empty( $rules ) ) {
			return;
		}

		// Remove all our existing rules before we update the option.
		foreach ( $rules as $k => $v ) {
			if ( preg_match( '/.*aiosp_.*/', aioseo()->helpers->escapeRegex( $v ) ) ) {
				unset( $rules[ $k ] );
			}
		}

		if ( $update ) {
			update_option( 'rewrite_rules', $rules );
		}

		return $rules;
	}
}