<?php
/**
 * XML template for the RSS Sitemap.
 *
 * @since 4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 // phpcs:disable
?>

<rss version="2.0">
	<channel><?php
	if( ! $isYandexBot ) {
		?>

		<title><?php aioseo()->sitemap->output->escapeAndEcho( $title, false ) ?></title>
		<link><?php aioseo()->sitemap->output->escapeAndEcho( $link ); ?></link>
		<?php if ( $description ) {
		?><description><?php aioseo()->sitemap->output->escapeAndEcho( $description ); ?></description>
		<?php }
		?><?php if ( isset( $entries[0] ) && ( array_key_exists( 'pubDate', $entries[0] ) || ! $entries[0]['pubDate'] ) ) {
		?><lastBuildDate><?php aioseo()->sitemap->output->escapeAndEcho( $entries[0]['pubDate'] ); ?></lastBuildDate>
		<?php }
		?><docs>https://validator.w3.org/feed/docs/rss2.html</docs>
		<ttl>0</ttl>

<?php }
foreach ( $entries as $entry ) {
		if ( ! is_array( $entry ) || ! array_key_exists( 'guid', $entry ) || ! $entry['guid'] ) {
			continue;
			}?>
		<item>
			<guid><?php aioseo()->sitemap->output->escapeAndEcho( $entry['guid'] ); ?></guid>
			<link><?php aioseo()->sitemap->output->escapeAndEcho( $entry['guid'] ); ?></link><?php
			if ( array_key_exists( 'title', $entry ) && $entry['title'] ) {
				?>

			<title><?php aioseo()->sitemap->output->escapeAndEcho( $entry['title'], false ); ?></title><?php
			}
			if ( array_key_exists( 'pubDate', $entry ) && $entry['pubDate'] ) {
				?>

			<pubDate><?php aioseo()->sitemap->output->escapeAndEcho( $entry['pubDate'] ); ?></pubDate><?php
			}
			?>

		</item>
			<?php } ?>
	</channel>
</rss>
