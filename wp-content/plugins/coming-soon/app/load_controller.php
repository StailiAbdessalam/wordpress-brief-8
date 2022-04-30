<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
// must load first
require_once SEEDPROD_PLUGIN_PATH . 'app/functions-utils.php';

require_once SEEDPROD_PLUGIN_PATH . 'app/cpt.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/admin-bar-menu.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/class-seedprod-notifications.php';

// helper functions
require_once SEEDPROD_PLUGIN_PATH . 'app/functions-wpforms.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/functions-rafflepress.php';

require_once SEEDPROD_PLUGIN_PATH . 'app/render-lp.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/render-csp-mm.php';

require_once SEEDPROD_PLUGIN_PATH . 'app/nestednavmenu.php';

require_once SEEDPROD_PLUGIN_PATH . 'app/backwards/backwards_compatibility.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/subscriber.php';
add_action( 'plugins_loaded', array( 'SeedProd_Lite_Render', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'SeedProd_Notifications', 'get_instance' ) );

if ( is_admin() ) {
	// Admin Only
	require_once SEEDPROD_PLUGIN_PATH . 'app/settings.php';
	require_once SEEDPROD_PLUGIN_PATH . 'app/lpage.php';
	require_once SEEDPROD_PLUGIN_PATH . 'app/edit_with_seedprod.php';
	require_once SEEDPROD_PLUGIN_PATH . 'app/functions-addons.php';
	if ( SEEDPROD_BUILD == 'lite' ) {
		require_once SEEDPROD_PLUGIN_PATH . 'app/class-seedprod-review.php';
	}
}

// Load on Public and Admin
require_once SEEDPROD_PLUGIN_PATH . 'app/license.php';
require_once SEEDPROD_PLUGIN_PATH . 'app/includes/upgrade.php';


