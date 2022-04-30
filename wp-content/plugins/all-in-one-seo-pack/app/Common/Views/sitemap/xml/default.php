<?php
/**
 * XML template for our sitemap index pages.
 *
 * @since 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable
?>
<urlset
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xhtml="http://www.w3.org/1999/xhtml"
<?php if ( ! $advanced || ! apply_filters( 'aioseo_sitemap_images', $excludeImages ) ): ?>
	xmlns:image="https://www.google.com/schemas/sitemap-image/1.1"
<?php endif; ?>
>
<?php foreach ( $entries as $entry ) {
	if ( ! is_array( $entry ) || ! array_key_exists( 'loc', $entry ) || ! $entry['loc'] ) {
		continue;
	}
	?>
	<url>
		<loc<?php echo ! empty( $entry['language'] ) ? ' language="' . $entry['language'] . '"' : ''; ?>><?php aioseo()->sitemap->output->escapeAndEcho( $entry['loc'] ); ?></loc><?php
	if ( array_key_exists( 'languages', $entry ) && count( $entry['languages'] ) ) {
			foreach ( $entry['languages'] as $language ) {
			?>

		<xhtml:link rel="alternate" hreflang="<?php echo esc_attr( $language['language'] ); ?>" href="<?php echo esc_url( $language['location'] ); ?>" /><?php
		}
	}
	if ( array_key_exists( 'lastmod', $entry ) && $entry['lastmod'] ) {
			?>

		<lastmod><?php aioseo()->sitemap->output->escapeAndEcho( $entry['lastmod'] ); ?></lastmod><?php
	}
	if ( array_key_exists( 'changefreq', $entry ) && $entry['changefreq'] ) {
			?>

		<changefreq><?php aioseo()->sitemap->output->escapeAndEcho( $entry['changefreq'] ); ?></changefreq><?php
	}
	if ( array_key_exists( 'priority', $entry ) && $entry['priority'] ) {
			?>

		<priority><?php aioseo()->sitemap->output->escapeAndEcho( $entry['priority'] ); ?></priority><?php
	}
	if ( array_key_exists( 'images', $entry ) && $entry['images'] ) {
			foreach ( $entry['images'] as $image ) {
				$image = (array) $image;
			?>

		<image:image>
			<image:loc><?php aioseo()->sitemap->output->escapeAndEcho( $image['image:loc'] ); ?></image:loc><?php
			if ( array_key_exists( 'image:caption', $image ) && $image['image:caption'] ) {
			?>

			<image:caption><?php aioseo()->sitemap->output->escapeAndEcho( $image['image:caption'] ); ?></image:caption><?php
			}
			if ( array_key_exists( 'image:title', $image ) && $image['image:title'] ) {
			?>

			<image:title><?php aioseo()->sitemap->output->escapeAndEcho( $image['image:title'] ); ?></image:title><?php
			}
			?>

		</image:image><?php
		}
	}
	?>

	</url>
<?php } ?>
</urlset>
