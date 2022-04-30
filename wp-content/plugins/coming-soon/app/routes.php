<?php
/**
 * Postback Routes
 */



/**
 * Admin Menu Routes
 */


add_action( 'admin_menu', 'seedprod_lite_create_menus' );

/**
 * Create menus for plugin.
 */
function seedprod_lite_create_menus() {
	// get notifications count
	$notification        = '';
	$n                   = new SeedProd_Notifications();
	$notifications_count = $n->get_count();

	if ( ! empty( $notifications_count ) ) {
		$notification = '<div class="seedprod-menu-notification-counter"><span>' . $notifications_count . '</span></div>';
	}

	add_menu_page(
		'SeedProd',
		'SeedProd' . $notification,
		apply_filters( 'seedprod_main_menu_capability', 'edit_others_posts' ),
		'seedprod_lite',
		'seedprod_lite_dashboard_page',
		'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI1IiBoZWlnaHQ9IjEzMiIgdmlld0JveD0iMCAwIDEyNSAxMzIiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0wIDBDMCAwIDIuOTE2NjQgMC4xOTc4OTQgNjIuODIxMiA4LjAyNjgzQzEyMi43MjYgMTUuODU1OCAxNDMuNDU5IDc2LjYwNjQgMTA2Ljc4MSAxMjkuNjI4QzExMi40NTQgODIuMjUyNyAxMDIuMDcgMzMuMTA2MiA2MC4zNjA1IDI3LjM2MDZDMTguNjUwNSAyMS42MTUxIDIyLjI4MzQgMjIuNDk1NCAyMi4yODM0IDIyLjQ5NTRDMjIuMjgzNCAyMi40OTU0IDIyLjk3NDUgMzIuOTI5OSAyNi44ODgzIDYwLjk3OTlDMzAuODAyMSA4OS4wMjk5IDUyLjcwMzUgMTAyLjc4NiA3MS44NzA0IDEwOS44NjhDNzEuODcwNCAxMDkuODY4IDcyLjk5NDUgNzcuMDQwMSA2Mi4zMDA3IDYyLjU5MDlDNTEuNjA2OSA0OC4xNDE4IDM4LjMwMjYgMzguNTQ2IDM4LjMwMjYgMzguNTQ2QzM4LjMwMjYgMzguNTQ2IDY5LjU2OCA0Mi4yOTYgODEuMzcyMiA2NC4xMDE5QzkzLjE3NjQgODUuOTA3OCA5Mi4wMjY1IDEzMiA5Mi4wMjY1IDEzMkw3OS4yOTI1IDEzMS4zNDFDNDUuMDI4NCAxMjcuMjI1IDEzLjAxNzIgMTA2LjU5MSA3LjU3NDIzIDYzLjNDMi4xMzEzIDIwLjAwODggMCAwIDAgMFoiIGZpbGw9ImJsYWNrIi8+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0wIDBDMCAwIDIuOTE2NjQgMC4xOTc4OTQgNjIuODIxMiA4LjAyNjgzQzEyMi43MjYgMTUuODU1OCAxNDMuNDU5IDc2LjYwNjQgMTA2Ljc4MSAxMjkuNjI4QzExMi40NTQgODIuMjUyNyAxMDIuMDcgMzMuMTA2MiA2MC4zNjA1IDI3LjM2MDZDMTguNjUwNSAyMS42MTUxIDIyLjI4MzQgMjIuNDk1NCAyMi4yODM0IDIyLjQ5NTRDMjIuMjgzNCAyMi40OTU0IDIyLjk3NDUgMzIuOTI5OSAyNi44ODgzIDYwLjk3OTlDMzAuODAyMSA4OS4wMjk5IDUyLjcwMzUgMTAyLjc4NiA3MS44NzA0IDEwOS44NjhDNzEuODcwNCAxMDkuODY4IDcyLjk5NDUgNzcuMDQwMSA2Mi4zMDA3IDYyLjU5MDlDNTEuNjA2OSA0OC4xNDE4IDM4LjMwMjYgMzguNTQ2IDM4LjMwMjYgMzguNTQ2QzM4LjMwMjYgMzguNTQ2IDY5LjU2OCA0Mi4yOTYgODEuMzcyMiA2NC4xMDE5QzkzLjE3NjQgODUuOTA3OCA5Mi4wMjY1IDEzMiA5Mi4wMjY1IDEzMkw3OS4yOTI1IDEzMS4zNDFDNDUuMDI4NCAxMjcuMjI1IDEzLjAxNzIgMTA2LjU5MSA3LjU3NDIzIDYzLjNDMi4xMzEzIDIwLjAwODggMCAwIDAgMFoiIGZpbGw9IndoaXRlIi8+PC9zdmc+',
		apply_filters( 'seedprod_top_level_menu_postion', 58 )
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Landing Pages', 'coming-soon' ),
		__( 'Landing Pages', 'coming-soon' ),
		apply_filters( 'seedprod_dashboard_menu_capability', 'edit_others_posts' ),
		'seedprod_lite',
		'seedprod_lite_dashboard_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Theme Builder', 'coming-soon' ),
		__( 'Theme Builder', 'coming-soon' ),
		apply_filters( 'seedprod_theme_templates_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_theme_templates',
		'seedprod_lite_theme_templates_page'
	);

	add_theme_page(
		__( 'Theme Builder', 'coming-soon' ),
		__( 'Theme Builder', 'coming-soon' ),
		apply_filters( 'seedprod_theme_templates_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_theme_templates',
		'seedprod_lite_theme_templates_page'
	);

	if ( 'pro' === SEEDPROD_BUILD ) {
		add_submenu_page(
			'seedprod_lite',
			__( 'Templates', 'coming-soon' ),
			__( 'Templates', 'coming-soon' ),
			apply_filters( 'seedprod_templates_menu_capability', 'edit_others_posts' ),
			'seedprod_lite_templates',
			'seedprod_lite_templates_page'
		);
	}

	add_submenu_page(
		'seedprod_lite',
		__( 'Subscribers', 'coming-soon' ),
		__( 'Subscribers', 'coming-soon' ),
		apply_filters( 'seedprod_subscribers_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_subscribers',
		'seedprod_lite_subscribers_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Settings', 'coming-soon' ),
		__( 'Settings', 'coming-soon' ),
		apply_filters( 'seedprod_settings_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_settings',
		'seedprod_lite_settings_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Growth Tools', 'coming-soon' ),
		__( 'Growth Tools', 'coming-soon' ),
		apply_filters( 'seedprod_growthtools_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_growth_tools',
		'seedprod_lite_growth_tools_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'About Us', 'coming-soon' ),
		__( 'About Us', 'coming-soon' ),
		apply_filters( 'seedprod_aboutus_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_about_us',
		'seedprod_lite_about_us_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Request a Feature', 'coming-soon' ),
		'<span id="sp-feature-request">' . __( 'Request a Feature', 'coming-soon' ) . '</span>',
		apply_filters( 'seedprod_featurerequest_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_featurerequest',
		'seedprod_lite_featurerequest_page'
	);

	if ( SEEDPROD_BUILD == 'lite' ) {
		add_submenu_page(
			'seedprod_lite',
			__( 'Get Pro', 'coming-soon' ),
			'<span id="sp-lite-admin-menu__upgrade" style="color:#ff845b">' . __( 'Get Pro', 'coming-soon' ) . '</span>',
			apply_filters( 'seedprod_gopro_menu_capability', 'edit_others_posts' ),
			'seedprod_lite_get_pro',
			'seedprod_lite_get_pro_page'
		);
	}

	add_submenu_page(
		'seedprod_lite',
		__( 'Templates', 'coming-soon' ),
		__( 'Templates', 'coming-soon' ),
		apply_filters( 'seedprod_templates_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_template',
		'seedprod_lite_template_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Builder', 'coming-soon' ),
		__( 'Builder', 'coming-soon' ),
		apply_filters( 'seedprod_builder_menu_capability', 'edit_others_posts' ),
		'seedprod_lite_builder',
		'seedprod_lite_builder_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Import/Export', 'coming-soon' ),
		__( 'Import/Export', 'coming-soon' ),
		apply_filters( 'seedprod_exportimport_menu_capability', 'edit_others_posts' ),
		'sp_pro_importexport',
		'seedprod_lite_importexport_page'
	);

	add_submenu_page(
		'seedprod_lite',
		__( 'Debug', 'coming-soon' ),
		__( 'Debug', 'coming-soon' ),
		apply_filters( 'seedprod_debug_menu_capability', 'edit_others_posts' ),
		'sp_pro_debug',
		'seedprod_lite_debug_page'
	);
}

add_action( 'admin_head', 'seedprod_lite_remove_menus' );

/**
 * Remove menus for plugin.
 */
function seedprod_lite_remove_menus() {
	remove_submenu_page( 'seedprod_lite', 'seedprod_lite_builder' );
	remove_submenu_page( 'seedprod_lite', 'seedprod_lite_template' );
	remove_submenu_page( 'seedprod_lite', 'sp_pro_importexport' );
	remove_submenu_page( 'seedprod_lite', 'sp_pro_debug' );
}

/**
 * Import/Export page.
 */
function seedprod_lite_importexport_page() {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/views/importexport.php';
}

/**
 * Debug page.
 */
function seedprod_lite_debug_page() {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/views/debug.php';
}

/**
 * Dashboard page.
 */
function seedprod_lite_dashboard_page() {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/views/dashboard.php';
}

/**
 * Builder page.
 */
function seedprod_lite_builder_page() {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/views/builder.php';
}

/**
 * Template page.
 */
function seedprod_lite_template_page() {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/views/builder.php';
}

// update selected page
add_action( 'admin_footer', 'seedprod_lite_update_selected_page_in_submenu' );

/**
 * Update menu for single page app.
 */
function seedprod_lite_update_selected_page_in_submenu() {
	?>
	<script>
	jQuery(document).ready(function($){
		if(location.search.indexOf('seedprod_') >= 0){
			// Theme Builder
			if(location.hash.indexOf('#/theme-templates') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>_theme_templates']" ).parent().addClass('current');
			}
			// Templates
			if(location.hash.indexOf('#/template') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>_templates']" ).parent().addClass('current');
			}
			// Subscribers
			if(location.hash.indexOf('#/subscribers') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>_subscribers']" ).parent().addClass('current');
			}
			// Settings
			if(location.hash.indexOf('#/settings') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>_settings']" ).parent().addClass('current');
			}
			// Growth Tools
			if(location.hash.indexOf('#/growth-tools') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>_growth_tools']" ).parent().addClass('current');
			}
			// About Us
			if(location.hash.indexOf('#/aboutus') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_BUILD ); ?>_about_us']" ).parent().addClass('current');
			}
		}
	});
	</script>
	<?php
}



/* Short circuit new request */

add_action( 'admin_init', 'seedprod_lite_new_lpage', 1 );


/* Redirect to SPA */

add_action( 'admin_init', 'seedprod_lite_redirect_to_site', 1 );

/**
 * Redirects for single page app.
 */
function seedprod_lite_redirect_to_site() {
	// settings page
	if ( isset( $_GET['page'] ) && 'seedprod_lite_settings' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_lite#/settings' );
		exit();
	}

	// subscribers
	if ( isset( $_GET['page'] ) && 'seedprod_lite_templates' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_lite_template&id=0&from=sidebar#/template' );
		exit();
	}

	// subscribers
	if ( isset( $_GET['page'] ) && 'seedprod_lite_subscribers' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_lite#/subscribers/0' );
		exit();
	}

	// theme templates
	if ( isset( $_GET['page'] ) && 'seedprod_lite_theme_templates' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_lite#/theme-templates' );
		exit();
	}

	// growth tools page
	if ( isset( $_GET['page'] ) && 'seedprod_lite_growth_tools' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_lite#/growth-tools' );
		exit();
	}

	//  about us page
	if ( isset( $_GET['page'] ) && 'seedprod_lite_about_us' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_lite#/aboutus' );
		exit();
	}

	// feature request page
	if ( isset( $_GET['page'] ) && 'seedprod_lite_featurerequest' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_redirect( 'https://www.seedprod.com/suggest-a-feature/?utm_source=wordpress&utm_medium=plugin-sidebar&utm_campaign=suggest-a-feature' );
		exit();
	}

	// getpro page
	if ( isset( $_GET['page'] ) && 'seedprod_lite_get_pro' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_redirect( seedprod_lite_upgrade_link( 'wp-sidebar-menu' ) );
		exit();
	}
}

/**
 * Preview Shortcode
 */
function seedprod_lite_render_shortcode() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		if ( ! empty( $_POST['shortcode'] ) ) {
			$shortcode = sanitize_text_field( wp_unslash( $_POST['shortcode'] ) );

			do_action( 'wp_print_footer_scripts' );
			do_action( 'wp_footer' );
			$content = do_shortcode( $shortcode );
			//$content = do_shortcode( $content );
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		exit();
	}
	exit;
}

if ( defined( 'DOING_AJAX' ) ) {

	add_action( 'wp_ajax_seedprod_lite_dismiss_settings_lite_cta', 'seedprod_lite_dismiss_settings_lite_cta' );

	add_action( 'wp_ajax_seedprod_lite_save_settings', 'seedprod_lite_save_settings' );
	add_action( 'wp_ajax_seedprod_lite_save_api_key', 'seedprod_lite_save_api_key' );

	add_action( 'wp_ajax_seedprod_lite_save_app_settings', 'seedprod_lite_save_app_settings' );


	add_action( 'wp_ajax_seedprod_lite_template_subscribe', 'seedprod_lite_template_subscribe' );
	add_action( 'wp_ajax_seedprod_lite_save_template', 'seedprod_lite_save_template' );
	add_action( 'wp_ajax_seedprod_lite_save_lpage', 'seedprod_lite_save_lpage' );
	add_action( 'wp_ajax_seedprod_lite_get_revisions', 'seedprod_lite_get_revisisons' );
	add_action( 'wp_ajax_seedprod_lite_get_utc_offset', 'seedprod_lite_get_utc_offset' );
	add_action( 'wp_ajax_seedprod_lite_get_namespaced_custom_css', 'seedprod_lite_get_namespaced_custom_css' );
	add_action( 'wp_ajax_seedprod_lite_get_stockimages', 'seedprod_lite_get_stockimages' );

	// Landing pages
	add_action( 'wp_ajax_seedprod_lite_slug_exists', 'seedprod_lite_slug_exists' );
	add_action( 'wp_ajax_seedprod_lite_lpage_datatable', 'seedprod_lite_lpage_datatable' );
	add_action( 'wp_ajax_seedprod_lite_duplicate_lpage', 'seedprod_lite_duplicate_lpage' );
	add_action( 'wp_ajax_seedprod_lite_get_lpage_list', 'seedprod_lite_get_lpage_list' );
	add_action( 'wp_ajax_seedprod_lite_archive_selected_lpages', 'seedprod_lite_archive_selected_lpages' );
	add_action( 'wp_ajax_seedprod_lite_unarchive_selected_lpages', 'seedprod_lite_unarchive_selected_lpages' );
	add_action( 'wp_ajax_seedprod_lite_delete_archived_lpages', 'seedprod_lite_delete_archived_lpages' );

	// Theme templates

	add_action( 'wp_ajax_seedprod_lite_update_subscriber_count', 'seedprod_lite_update_subscriber_count' );
	add_action( 'wp_ajax_seedprod_lite_subscribers_datatable', 'seedprod_lite_subscribers_datatable' );

	add_action( 'wp_ajax_seedprod_lite_get_plugins_list', 'seedprod_lite_get_plugins_list' );

	add_action( 'wp_ajax_seedprod_lite_install_addon', 'seedprod_lite_install_addon' );
	add_action( 'wp_ajax_seedprod_lite_activate_addon', 'seedprod_lite_activate_addon' );
	add_action( 'wp_ajax_seedprod_lite_deactivate_addon', 'seedprod_lite_deactivate_addon' );

	add_action( 'wp_ajax_seedprod_lite_install_addon', 'seedprod_lite_install_addon' );
	add_action( 'wp_ajax_seedprod_lite_deactivate_addon', 'seedprod_lite_deactivate_addon' );
	add_action( 'wp_ajax_seedprod_lite_activate_addon', 'seedprod_lite_activate_addon' );
	add_action( 'wp_ajax_seedprod_lite_plugin_nonce', 'seedprod_lite_plugin_nonce' );

	add_action( 'wp_ajax_nopriv_seedprod_lite_run_one_click_upgrade', 'seedprod_lite_run_one_click_upgrade' );
	add_action( 'wp_ajax_seedprod_lite_upgrade_license', 'seedprod_lite_upgrade_license' );

	add_action( 'wp_ajax_seedprod_lite_get_wpforms', 'seedprod_lite_get_wpforms' );
	add_action( 'wp_ajax_seedprod_lite_get_wpform', 'seedprod_lite_get_wpform' );
	add_action( 'wp_ajax_seedprod_lite_get_rafflepress', 'seedprod_lite_get_rafflepress' );
	add_action( 'wp_ajax_seedprod_lite_get_rafflepress_code', 'seedprod_lite_get_rafflepress_code' );

	add_action( 'wp_ajax_seedprod_lite_get_widget_wpforms', 'seedprod_lite_get_widget_wpforms' );
	add_action( 'wp_ajax_seedprod_lite_get_widget_wpresults', 'seedprod_lite_get_widget_wpresults' );


	add_action( 'wp_ajax_seedprod_lite_dismiss_upsell', 'seedprod_lite_dismiss_upsell' );

	// WooCommerce.
	add_action( 'wp_ajax_seedprod_lite_get_woocommerce_products', 'seedprod_lite_get_woocommerce_products' );
	add_action( 'wp_ajax_seedprod_lite_get_woocommerce_product_taxonomy', 'seedprod_lite_get_woocommerce_product_taxonomy' );
	add_action( 'wp_ajax_seedprod_lite_get_woocommerce_product_attributes', 'seedprod_lite_get_woocommerce_product_attributes' );
	add_action( 'wp_ajax_seedprod_lite_get_woocommerce_product_attribute_terms', 'seedprod_lite_get_woocommerce_product_attribute_terms' );




}






