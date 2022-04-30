<?php
namespace AIOSEO\Plugin\Lite\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Admin as CommonAdmin;

/**
 * Abstract class that Pro and Lite both extend.
 *
 * @since 4.0.0
 */
class Admin extends CommonAdmin\Admin {
	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		if ( ! wp_doing_ajax() && ! wp_doing_cron() ) {
			parent::__construct();
		}

		$this->connect = new Connect();
	}

	/**
	 * Actually adds the menu items to the admin bar.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	protected function addAdminBarMenuItems() {
		// Add an upsell to Pro.
		if ( current_user_can( $this->getPageRequiredCapability( '' ) ) ) {
			$this->adminBarMenuItems['aioseo-pro-upgrade'] = [
				'parent' => 'aioseo-main',
				'title'  => '<span class="aioseo-menu-highlight">' . __( 'Upgrade to Pro', 'all-in-one-seo-pack' ) . '</span>',
				'id'     => 'aioseo-pro-upgrade',
				'href'   => apply_filters( 'aioseo_upgrade_link', aioseo()->helpers->utmUrl( AIOSEO_MARKETING_URL . 'lite-upgrade/', 'admin-bar', null, false ) ),
				'meta'   => [ 'target' => '_blank' ],
			];
		}

		parent::addAdminBarMenuItems();
	}

	/**
	 * Add the menu inside of WordPress.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function addMenu() {
		parent::addMenu();

		$capability = $this->getPageRequiredCapability( '' );

		// We use the global submenu, because we are adding an external link here.
		if ( current_user_can( $capability ) ) {
			global $submenu;
			$submenu[ $this->pageSlug ][] = [
				'<span class="aioseo-menu-highlight">' . esc_html__( 'Upgrade to Pro', 'all-in-one-seo-pack' ) . '</span>',
				$capability,
				apply_filters( 'aioseo_upgrade_link', aioseo()->helpers->utmUrl( AIOSEO_MARKETING_URL . 'lite-upgrade/', 'admin-menu', null, false ) )
			];
		}
	}
}