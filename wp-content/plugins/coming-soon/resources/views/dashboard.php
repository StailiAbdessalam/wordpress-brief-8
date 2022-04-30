<?php

$seedprod_api_token = get_option( 'seedprod_api_token' );
$license_key        = get_option( 'seedprod_api_key' );

$sp_current_user = wp_get_current_user();
$name            = '';
if ( ! empty( $sp_current_user->user_firstname ) ) {
	$name = $sp_current_user->user_firstname . ',';
}
$email = $sp_current_user->user_email;

$timezones = seedprod_lite_get_timezones();

// Pers
$per                   = array();
$active_license        = false;
$template_dev_mode     = false;
$theme_dev_mode        = false;
$is_woocommerce_active = false;



$license_name = '';


// Get counts for pages and subscribers

global $wpdb;
$tablename  = $wpdb->prefix . 'postmeta';
$sql        = "SELECT count(*) FROM $tablename WHERE meta_key = '_seedprod_page'";
$page_count = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
if ( empty( $page_count ) ) {
	$page_count = 0;
}


$subscriber_count = get_option( 'seedprod_subscriber_count' );
if ( empty( $subscriber_count ) ) {
	$subscriber_count = 1;
}


// Get notifications
$notifications = new SeedProd_Notifications();
$notifications = $notifications->get();


$seedprod_settings = get_option( 'seedprod_settings' );
if ( ! empty( $seedprod_settings ) ) {
	$seedprod_settings = json_decode( stripslashes( $seedprod_settings ) );
} else {
	// fail safe incase settings go missing
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
	update_option( 'seedprod_settings', $seedprod_default_settings );
	$seedprod_settings = json_decode( $seedprod_default_settings );
}
if ( empty( $seedprod_settings ) || 'false' === $seedprod_settings ) {
	// fail safe incase settings go missing
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
	update_option( 'seedprod_settings', $seedprod_default_settings );
	$seedprod_settings = json_decode( $seedprod_default_settings );
}
$seedprod_api_key = get_option( 'seedprod_api_key' );
if ( false === $seedprod_api_key ) {
	$seedprod_api_key = '';
}

$seedprod_app_settings = get_option( 'seedprod_app_settings' );
if ( ! empty( $seedprod_app_settings ) ) {
	$seedprod_app_settings = json_decode( stripslashes( $seedprod_app_settings ) );
} else {
	// fail safe incase settings go missing
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
	update_option( 'seedprod_app_settings', $seedprod_app_default_settings );
	$seedprod_app_settings = json_decode( $seedprod_app_default_settings );
}

$seedprod_upgrade_link = seedprod_lite_upgrade_link( '' );

$lmsg = get_option( 'seedprod_api_message' );
if ( empty( $lmsg ) ) {
	$lmsg = '';
}

$lclass = 'alert-danger';
if ( seedprod_lite_cu() ) {
	$lclass = 'alert-success';
}

// get special page ids and uuids
$csp_id    = get_option( 'seedprod_coming_soon_page_id' );
$mmp_id    = get_option( 'seedprod_maintenance_mode_page_id' );
$p404_id   = get_option( 'seedprod_404_page_id' );
$loginp_id = get_option( 'seedprod_login_page_id' );

$csp_uuid                = get_post_meta( $csp_id, '_seedprod_page_uuid', true );
$mmp_uuid                = get_post_meta( $mmp_id, '_seedprod_page_uuid', true );
$p404_uuid               = get_post_meta( $p404_id, '_seedprod_page_uuid', true );
$loginp_uuid             = get_post_meta( $loginp_id, '_seedprod_page_uuid', true );
$seedprod_csp4_migrated  = get_option( 'seedprod_csp4_migrated' );
$seedprod_csp4_imported  = get_option( 'seedprod_csp4_imported' );
$seedprod_cspv5_migrated = get_option( 'seedprod_cspv5_migrated' );
$seedprod_cspv5_imported = get_option( 'seedprod_cspv5_imported' );
$seedprod_site_token     = get_option( 'seedprod_token' );

// one time flush permalinks
if ( empty( get_option( 'seedprod_onetime_flush_rewrite' ) ) ) {
	flush_rewrite_rules();
	update_option( 'seedprod_onetime_flush_rewrite', true );
}

$csp_preview_url = '';
if ( ! empty( $csp_id ) ) {
	$csp_preview_url = get_preview_post_link( $csp_id );
	//$csp_preview_url = home_url(). "/?post_type=seedprod&page_id=".$csp_id."&preview_nonce=".wp_create_nonce('post_preview_' . $csp_id);
	//$csp_preview_url = home_url(). '/?post_type=seedprod&p='.$csp_id.'&preview=true';
}
$mmp_preview_url = '';
if ( ! empty( $mmp_id ) ) {
	$mmp_preview_url = get_preview_post_link( $mmp_id );
	//$mmp_preview_url = home_url(). "/?post_type=seedprod&page_id=".$mmp_id."&preview_nonce=".wp_create_nonce('post_preview_' . $mmp_id);
	//$mmp_preview_url= home_url(). '/?post_type=seedprod&p='.$mmp_id.'&preview=true';
}
$p404_preview_url = '';
if ( ! empty( $p404_id ) ) {
	$p404_preview_url = get_preview_post_link( $p404_id );
	//$p404_preview_url = home_url(). "/?post_type=seedprod&page_id=".$p404_id."&preview_nonce=".wp_create_nonce('post_preview_' . $p404_id);
	//$p404_preview_url= home_url(). '/?post_type=seedprod&p='.$p404_id.'&preview=true';
}

$loginp_preview_url = '';
if ( ! empty( $loginp_id ) ) {
	$loginp_preview_url = get_preview_post_link( $loginp_id );
	//$loginp_preview_url = home_url(). "/?post_type=seedprod&page_id=".$loginp_id."&preview_nonce=".wp_create_nonce('post_preview_' . $loginp_id);
	//$loginp_preview_url= home_url(). '/?post_type=seedprod&p='.$loginp_id.'&preview=true';
}



$show_topbar_cta    = true;
$dismiss_topbar_cta = get_option( 'seedprod_dismiss_upsell_1' );
if ( $dismiss_topbar_cta ) {
	$show_topbar_cta = false;
}

$show_inline_cta    = true;
$dismiss_inline_cta = get_option( 'seedprod_dismiss_upsell_3' );
if ( $dismiss_inline_cta ) {
	$show_inline_cta = false;
}

$conditions             = array();
$theme_preview_mode     = false;
$seedprod_theme_enabled = false;
?>

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet"> <?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>
<div id="seedprod-vue-app"></div>
<script>
var seedprod_remote_api = "<?php echo esc_url( SEEDPROD_API_URL ); ?>";

<?php $seedprod_nonce = wp_create_nonce( 'seedprod_nonce' ); ?>
var seedprod_nonce = <?php echo wp_json_encode( $seedprod_nonce ); ?>;


<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_run_one_click_upgrade', 'seedprod_lite_run_one_click_upgrade' ) ); ?>
var seedprod_run_one_click_upgrade_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_upgrade_license', 'seedprod_lite_upgrade_license' ) ); ?>
var seedprod_upgrade_license_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_dismiss_upsell', 'seedprod_lite_dismiss_upsell' ) ); ?>
var seedprod_dismiss_upsell = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_archive_selected_lpages', 'seedprod_lite_archive_selected_lpages' ) ); ?>
var seedprod_archive_selected_lpages = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_unarchive_selected_lpages', 'seedprod_lite_unarchive_selected_lpages' ) ); ?>
var seedprod_unarchive_selected_lpages = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_delete_archived_lpages', 'seedprod_lite_delete_archived_lpages' ) ); ?>
var seedprod_delete_archived_lpages = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_duplicate_lpage', 'seedprod_lite_duplicate_lpage' ) ); ?>
var seedprod_duplicate_lpage_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_archive_selected_themetemplates', 'seedprod_lite_archive_selected_themetemplates' ) ); ?>
var seedprod_archive_selected_themetemplates = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_unarchive_selected_themetemplates', 'seedprod_lite_unarchive_selected_themetemplates' ) ); ?>
var seedprod_unarchive_selected_themetemplates = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_delete_archived_themetemplates', 'seedprod_lite_delete_archived_themetemplates' ) ); ?>
var seedprod_delete_archived_themetemplates = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_duplicate_themetemplate', 'seedprod_lite_duplicate_themetemplate' ) ); ?>
var seedprod_duplicate_themetemplate_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_temp_save_theme_template', 'seedprod_lite_temp_save_theme_template' ) ); ?>
var seedprod_temp_save_theme_template_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_update_theme_template_conditions', 'seedprod_lite_update_theme_template_conditions' ) ); ?>
var seedprod_update_theme_template_conditions_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_update_theme_template_post_status', 'seedprod_lite_update_theme_template_post_status' ) ); ?>
var seedprod_update_theme_template_post_status_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_update_theme_template_preview_mode', 'seedprod_lite_update_theme_template_preview_mode' ) ); ?>
var seedprod_update_theme_template_preview_mode_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_update_seedprod_theme_enabled', 'seedprod_lite_update_seedprod_theme_enabled' ) ); ?>
var seedprod_update_seedprod_theme_enabled_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_plugins_list', 'seedprod_lite_get_plugins_list' ) ); ?>
var seedprod_get_plugins_list_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_install_addon', 'seedprod_lite_install_addon' ) ); ?>
var seedprod_get_install_addon_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_activate_addon', 'seedprod_lite_activate_addon' ) ); ?>
var seedprod_activate_addon_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_deactivate_addon', 'seedprod_lite_deactivate_addon' ) ); ?>
var seedprod_deactivate_addon_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_notification_dismiss', 'seedprod_lite_notification_dismiss' ) ); ?>
var seedprod_notification_dismiss = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_deactivate_api_key', 'seedprod_lite_deactivate_api_key' ) ); ?>
var seedprod_api_key_deactivate_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_plugin_nonce', 'seedprod_lite_plugin_nonce' ) ); ?>
var seedprod_plugin_nonce_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_update_subscriber_count', 'seedprod_lite_update_subscriber_count' ) ); ?>
var seedprod_update_subscriber_count = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_delete_subscribers', 'seedprod_lite_delete_subscribers' ) ); ?>
var seedprod_delete_subscribers_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_export_subscribers', 'seedprod_lite_export_subscribers' ) ); ?>
var seedprod_export_subscribers_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_export_theme', 'seedprod_lite_export_theme' ) ); ?>
var seedprod_export_theme_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_import_theme_request', 'seedprod_lite_import_theme_request' ) ); ?>
var seedprod_import_theme_request_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_save_app_settings', 'seedprod_lite_save_app_settings' ) ); ?>
var seedprod_save_app_settings_ajax_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_create_blog_and_home_for_theme', 'seedprod_lite_create_blog_and_home_for_theme' ) ); ?>
var seedprod_create_blog_and_home_for_theme_ajax_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php
$seedprod_unsupported_feature = get_option( 'seedprod_unsupported_feature' );
if ( ! empty( $seedprod_unsupported_feature ) ) {
	$seedprod_unsupported_feature = implode( ',', $seedprod_unsupported_feature );
}

// get static home page status
$show_on_front  = get_option( 'show_on_front' );
$page_for_posts = get_option( 'page_for_posts' );
$page_on_front  = get_option( 'page_on_front' );
?>

var seedprod_data_admin =
	<?php
	echo wp_json_encode(
		array(
			'show_on_front'                => $show_on_front,
			'page_for_posts'               => $page_for_posts,
			'page_on_front'                => $page_on_front,
			'is_woocommerce_active'        => $is_woocommerce_active,
			'site_token'                   => $seedprod_site_token,
			'theme_preview_mode'           => $theme_preview_mode,
			'seedprod_theme_enabled'       => $seedprod_theme_enabled,
			'conditions'                   => $conditions,
			'show_inline_cta'              => $show_inline_cta,
			'show_topbar_cta'              => $show_topbar_cta,
			'seedprod_unsupported_feature' => $seedprod_unsupported_feature,
			'seedprod_csp4_migrated'       => $seedprod_csp4_migrated,
			'seedprod_csp4_imported'       => $seedprod_csp4_imported,
			'seedprod_cspv5_migrated'      => $seedprod_cspv5_migrated,
			'seedprod_cspv5_imported'      => $seedprod_cspv5_imported,
			'page_count'                   => $page_count,
			'subscriber_count'             => $subscriber_count,
			'notifications'                => $notifications,
			'csp_id'                       => $csp_id,
			'csp_uuid'                     => $csp_uuid,
			'csp_preview_url'              => $csp_preview_url,
			'mmp_id'                       => $mmp_id,
			'mmp_uuid'                     => $mmp_uuid,
			'mmp_preview_url'              => $mmp_preview_url,
			'p404_id'                      => $p404_id,
			'p404_uuid'                    => $p404_uuid,
			'p404_preview_url'             => $p404_preview_url,
			'loginp_id'                    => $loginp_id,
			'loginp_uuid'                  => $loginp_uuid,
			'loginp_preview_url'           => $loginp_preview_url,
			'api_token'                    => $seedprod_api_token,
			'license_key'                  => $license_key,
			'license_name'                 => $license_name,
			'per'                          => $per,
			'active_license'               => $active_license,
			'page_path'                    => 'seedprod_lite',
			'plugin_path'                  => SEEDPROD_PLUGIN_URL,
			'home_url'                     => home_url(),
			'upgrade_link'                 => $seedprod_upgrade_link,
			'timezones'                    => $timezones,
			'api_key'                      => $seedprod_api_key,
			'name'                         => $name,
			'email'                        => $email,
			'lmsg'                         => $lmsg,
			'lclass'                       => $lclass,
			'settings'                     => $seedprod_settings,
			'app_settings'                 => $seedprod_app_settings,
			'template_dev_mode'            => $template_dev_mode,
			'theme_dev_mode'               => $theme_dev_mode,
			'dismiss_settings_lite_cta'    => get_option( 'seedprod_dismiss_settings_lite_cta' ),
		)
	);
	?>
			;



</script>
