<?php
namespace AIOSEO\Plugin\Common\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that holds our dashboard widget.
 *
 * @since 4.0.0
 */
class Dashboard {
	/**
	 * Class Constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', [ $this, 'addDashboardWidgets' ] );
	}

	/**
	 * Registers our dashboard widgets.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public function addDashboardWidgets() {
		if ( ! $this->canShowWidgets() ) {
			return;
		}

		// Add the SEO Setup widget.
		if (
			apply_filters( 'aioseo_show_seo_setup', true ) &&
			( aioseo()->access->isAdmin() || aioseo()->access->hasCapability( 'aioseo_setup_wizard' ) ) &&
			! aioseo()->standalone->setupWizard->isCompleted()
		) {
			wp_add_dashboard_widget(
				'aioseo-seo-setup',
				// Translators: 1 - The plugin short name ("AIOSEO").
				sprintf( esc_html__( '%s Setup', 'all-in-one-seo-pack' ), AIOSEO_PLUGIN_SHORT_NAME ),
				[
					$this,
					'outputSeoSetup',
				]
			);
		}

		// Add the Overview widget.
		if (
			apply_filters( 'aioseo_show_seo_overview', true ) &&
			( aioseo()->access->isAdmin() || aioseo()->access->hasCapability( 'aioseo_page_analysis' ) )
		) {
			wp_add_dashboard_widget(
				'aioseo-overview',
				// Translators: 1 - The plugin short name ("AIOSEO").
				sprintf( esc_html__( '%s Overview', 'all-in-one-seo-pack' ), AIOSEO_PLUGIN_SHORT_NAME ),
				[
					$this,
					'outputSeoOverview',
				]
			);
		}
	}

	/**
	 * Whether or not to show the widget.
	 *
	 * @since 4.0.0
	 *
	 * @return boolean True if yes, false otherwise.
	 */
	protected function canShowWidgets() {
		return true;
	}

	/**
	 * Output the SEO Setup widget.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public function outputSeoSetup() {
		$this->output( 'aioseo-seo-setup-app' );
	}

	/**
	 * Output the SEO Overview widget.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public function outputSeoOverview() {
		$this->output( 'aioseo-overview-app' );
	}

	/**
	 * Output the widget wrapper for the Vue App.
	 *
	 * @since 4.2.0
	 *
	 * @param  string $appId The App ID to print out.
	 * @return void
	 */
	private function output( $appId ) {
		// Enqueue the scripts for the widget.
		$this->enqueue();

		// Opening tag.
		echo '<div id="' . esc_attr( $appId ) . '">';

		// Loader element.
		require( AIOSEO_DIR . '/app/Common/Views/parts/loader.php' );

		// Closing tag.
		echo '</div>';
	}

	/**
	 * Enqueue the scripts and styles.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	private function enqueue() {
		aioseo()->core->assets->load( 'src/vue/standalone/dashboard-widgets/main.js', [], aioseo()->helpers->getVueData( 'dashboard' ) );
	}

	/**
	 * Get the RSS posts.
	 *
	 * @since 4.2.0
	 *
	 * @return array The RSS posts.
	 */
	public function getAioseoRssFeed() {
		include_once( ABSPATH . WPINC . '/feed.php' );

		$rssItems = aioseo()->core->cache->get( 'rss_feed' );
		if ( null === $rssItems ) {
			$rss = fetch_feed( 'https://aioseo.com/feed/' );
			if ( is_wp_error( $rss ) ) {
				esc_html_e( 'Temporarily unable to load feed.', 'all-in-one-seo-pack' );

				return;
			}

			$rssItems = $rss->get_items( 0, 3 ); // Show three items.
			$cached   = [];
			foreach ( $rssItems as $item ) {
				$cached[] = [
					'url'     => $item->get_permalink(),
					'title'   => aioseo()->helpers->decodeHtmlEntities( $item->get_title() ),
					'date'    => $item->get_date( get_option( 'date_format' ) ),
					'content' => substr( wp_strip_all_tags( $item->get_content() ), 0, 128 ) . '...',
				];
			}
			$rssItems = $cached;

			aioseo()->core->cache->update( 'rss_feed', $cached, 12 * HOUR_IN_SECONDS );
		}

		return $rssItems;
	}
}