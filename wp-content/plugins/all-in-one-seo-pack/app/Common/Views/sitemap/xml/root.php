<?php
/**
 * XML template for our root index page.
 *
 * @since 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 // phpcs:disable
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ( $entries as $entry ) {
	if ( ! is_array( $entry ) || ! array_key_exists( 'loc', $entry ) || ! $entry['loc'] ) {
		continue;
	}
	?>
	<sitemap>
		<loc><?php aioseo()->sitemap->output->escapeAndEcho( $entry['loc'] ); ?></loc><?php
	if ( array_key_exists( 'lastmod', $entry ) && $entry['lastmod'] ) {
			?>

		<lastmod><?php aioseo()->sitemap->output->escapeAndEcho( $entry['lastmod'] ); ?></lastmod><?php
		}
	?>

	</sitemap>
<?php } ?>
</sitemapindex>
