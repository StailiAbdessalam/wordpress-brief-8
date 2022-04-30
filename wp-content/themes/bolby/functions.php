<?php
/* ================================================== */
/*    |               Include                    |    */
/* ================================================== */
require_once  get_parent_theme_file_path().'/includes/theme-css.php';
require_once  get_parent_theme_file_path().'/includes/enqueue.php';
require_once  get_parent_theme_file_path().'/includes/theme-functions.php';
require_once  get_parent_theme_file_path().'/includes/meta-box.php';
require_once  get_parent_theme_file_path().'/includes/bolby-class-wp-bootstrap-navwalker.php';
// kirki
require_once  get_parent_theme_file_path().'/includes/plugin-activation/class-tgm-plugin-activation.php';
if (class_exists( 'Kirki' ) && file_exists(get_template_directory() . '/includes/customizer.php')) { 
  require_once  get_parent_theme_file_path().'/includes/customizer.php';
} 


/* ================================================== */
/*    |         TGMPA Plugin Activation          |    */
/* ================================================== */
add_action( 'tgmpa_register', 'bolby_theme_register_required_plugins' );

function bolby_theme_register_required_plugins() {
  $plugins = array(

    array(
      'name'      => esc_attr('One Click Demo Import', 'bolby'),
      'slug'      => 'one-click-demo-import',
      'required'  => true,
    ),

    array(
      'name'      => esc_attr('Elementor Page Builder', 'bolby'),
      'slug'      => 'elementor',
      'required'  => true,
    ),

    array(
      'name'      => esc_attr('CMB2', 'bolby'),
      'slug'      => 'cmb2',
      'required'  => true,
    ),

    array(
      'name'      => esc_attr('Kirki', 'bolby'),
      'slug'      => 'kirki',
      'required'  => true,
    ),

    array(
      'name'               => esc_attr('Bolby Custom Post Type', 'bolby'), // The plugin name.
      'slug'               => 'bolby-custom-posttype', // The plugin slug (typically the folder name).
      'source'             => get_template_directory() . '/includes/plugin-activation/bolby-custom-posttype.zip',
      'required'           => true, // If false, the plugin is only 'recommended' instead of required.
    ),

    array(
      'name'               => esc_attr('Bolby Elementor Elements', 'bolby'), // The plugin name.
      'slug'               => 'bolby-elementor-elements', // The plugin slug (typically the folder name).
      'source'             => get_template_directory() . '/includes/plugin-activation/bolby-elementor-elements.zip',
      'version'           => '1.1',
      'required'           => true, // If false, the plugin is only 'recommended' instead of required.
    ),

    array(
      'name'      => esc_attr('Contact Form 7', 'bolby'),
      'slug'      => 'contact-form-7',
      'required'  => true,
    ),

    array(
      'name'      => esc_attr('SVG Support', 'bolby'),
      'slug'      => 'svg-support',
      'required'  => true,
    ),

    array(
      'name'      => esc_attr('WooCommerce', 'bolby'),
      'slug'      => 'woocommerce',
      'required'  => false,
    ),

  );

  $config = array(
    'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
    'default_path' => '',                      // Default absolute path to bundled plugins.
    'menu'         => 'tgmpa-install-plugins', // Menu slug.
    'parent_slug'  => 'themes.php',            // Parent menu slug.
    'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
    'has_notices'  => true,                    // Show admin notices or not.
    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
    'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
    'is_automatic' => false,                   // Automatically activate plugins after installation or not.
    'message'      => '',                      // Message to output right before the plugins table.
  );

  tgmpa( $plugins, $config );
}





/* ================================================== */
/*    |         One Click Demo Importer          |    */
/* ================================================== */
function bolby_import_demos() {
  return array(
      array(
          'import_file_name'           => esc_attr('Demo 1 combined', 'bolby'),
          'categories'                 => array( 'Demo 1' ),
          'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-1.xml',
          'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
          'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-1.dat',
          'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_1.png',
          'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
          'preview_url'                => 'http://pxltheme.com/wp/bolby',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 1 dark', 'bolby'),
        'categories'                 => array( 'Demo 1' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-2.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-2.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_2.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-2',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 1 light', 'bolby'),
        'categories'                 => array( 'Demo 1' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-3.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-3.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_3.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-3',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 2 combined', 'bolby'),
        'categories'                 => array( 'Demo 2' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-4.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-4.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_4.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-4',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 2 dark', 'bolby'),
        'categories'                 => array( 'Demo 2' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-5.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-5.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_5.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-5',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 2 light', 'bolby'),
        'categories'                 => array( 'Demo 2' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-6.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-6.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_6.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-6',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 3 combined', 'bolby'),
        'categories'                 => array( 'Demo 3' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-7.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-7.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_7.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-7',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 3 dark', 'bolby'),
        'categories'                 => array( 'Demo 3' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-8.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-8.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_8.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-8',
      ),
      array(
        'import_file_name'           => esc_attr('Demo 3 light', 'bolby'),
        'categories'                 => array( 'Demo 3' ),
        'import_file_url'            => 'http://www.pxltheme.com/ocdi/bolby/demo-content-9.xml',
        'import_widget_file_url'     => 'http://www.pxltheme.com/ocdi/bolby/widgets.json',
        'import_customizer_file_url' => 'http://www.pxltheme.com/ocdi/bolby/customizer-9.dat',
        'import_preview_image_url'   => 'http://www.pxltheme.com/ocdi/bolby/demo_9.png',
        'import_notice'              => esc_attr( 'If the automatic demo importer does not works, please switch to manual importer from the right of top and import files from the demo-data folder in your theme files.', 'bolby' ),
        'preview_url'                => 'http://pxltheme.com/wp/bolby/demo-9',
      ),
  );
}
add_filter( 'pt-ocdi/import_files', 'bolby_import_demos' );







/* ================================================== */
/*    |         Register Nav Menus               |    */
/* ================================================== */
add_action( 'after_setup_theme', 'bolby_theme_menu_setup' );
if ( ! function_exists( 'bolby_theme_menu_setup' ) ):
function bolby_theme_menu_setup() {  
    register_nav_menu('primary-menu', esc_attr( 'Primary Menu', 'bolby' ));
    register_nav_menu('onepage-menu', esc_attr( 'One Page Menu', 'bolby' ));
} endif;





/* ================================================== */
/*    |           Register Sidebar               |    */
/* ================================================== */
function bolby_widgets_init() {
  register_sidebar( array(
    'name'          => esc_attr('Right Sidebar', 'bolby'),
    'id'            => 'primary-sidebar',
    'description'   => esc_attr('Main Right Sidebar', 'bolby'),
    'before_widget' => '<div class="widget bg-white rounded shadow-dark">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="widget-header">',
    'after_title'   => '</h3>',
  ) );
  register_sidebar( array(
    'name'          => esc_attr('Language Switcher Widget Area', 'bolby'),
    'id'            => 'language-switcher-widget-area',
    'before_widget' => '<div>',
    'after_widget'  => '</div>',
  ) );
}
add_action( 'widgets_init', 'bolby_widgets_init' );




/* ================================================== */
/*    |       Styling Default Search Form        |    */
/* ================================================== */
function bolby_theme_search_form( $form ) { 
  $form = '<form class="searchform" role="search" method="get" id="search-form" action="' . esc_url(home_url( '/' )) . '" >
 <label class="screen-reader-text" for="s"></label>
  <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="Search ..." />
  <input type="submit" id="searchsubmit" value="'. esc_attr__('Search', 'bolby') .'" />
  </form>';
  return $form;
}

add_filter( 'get_search_form', 'bolby_theme_search_form' );





/* ================================================== */
/*    |               Menu Walkers               |    */
/* ================================================== */
class Bolby_Nav_Menu extends Walker_Nav_Menu {
  function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"submenu\">\n";
  }
}






/* ================================================== */
/*    |                Dark Mode                 |    */
/* ================================================== */
function bolby_theme_dark_mode($classes) {
  if(true == get_theme_mod('dark_mode', false)){
    $classes[] = 'dark';
  }
    return $classes;
}

add_filter('body_class', 'bolby_theme_dark_mode');







/* ================================================== */
/*    |         Theme Features                   |    */
/* ================================================== */
if ( ! function_exists('bolby_theme_features') ) {
// Register Theme Features
function bolby_theme_features()  {

  // Add theme support for Post Thumbnails
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 300, 300, true );
  // Add theme support for Automatic Feed Links
  add_theme_support( 'automatic-feed-links' );
  // Add theme support for Title Tag
  add_theme_support( "title-tag" );
  // Add theme support for WooCommerce
  add_theme_support( 'woocommerce' );
  add_theme_support( 'wc-product-gallery-zoom' );
  add_theme_support( 'wc-product-gallery-lightbox' );
  add_theme_support( 'wc-product-gallery-slider' );
  /* Post Thumbnail Sizes */
  add_image_size( 'bolby-thumb', 365, 260, true );
  add_image_size( 'bolby-portfolio-single', 1200, 9999, false );
  add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
  );
  // Add theme support for Gutenberg
  add_theme_support( 'wp-block-styles' );
  // Add support for editor styles.
  add_theme_support( 'editor-styles' );
  // Add support for editor styles.
  add_theme_support( 'editor-styles' );
  
  // Enqueue for Custom Editor Styles
  add_editor_style( 'css/editor-style.css' );
  // Enqueue fonts in the editor.
  add_editor_style( bolby_theme_fonts_url() );
}
add_action( 'after_setup_theme', 'bolby_theme_features' );

}







/**
 * Change number or products per row to 3
 */
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}








// Set content width value based on the theme's design
if ( ! isset( $content_width ) )
  $content_width = 1140;







/* ================================================== */
/*    |             Post Paginations             |    */
/* ================================================== */
function bolby_theme_pagination() {
  global $wp_query;
  $big = 12345678;
  $page_format = paginate_links( array(
      'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
      'format' => '?paged=%#%',
      'current' => max( 1, get_query_var('paged') ),
      'total' => $wp_query->max_num_pages,
      'type'  => 'array',
      'prev_next' => false,
  ) );
  if( is_array($page_format) ) {
    $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
    echo '<nav class="pagination-outer"><ul class="list-inline pagination unstyled">';
    foreach ( $page_format as $page ) {
      echo "<li class='page-item list-inline-item'>$page</li>";
    }
      echo '</ul></nav>';
  }
}

function bolby_theme_pagination_static() {
  global $the_query;
  $big = 12345678;
  $page_format = paginate_links( array(
      'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
      'format' => '?paged=%#%',
      'current' => max( 1, get_query_var('paged') ),
      'total' => $the_query->max_num_pages,
      'type'  => 'array',
      'prev_next' => false,
  ) );
  if( is_array($page_format) ) {
    $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
    echo '<nav class="pagination-outer"><ul class="list-inline pagination unstyled">';
    foreach ( $page_format as $page ) {
      echo "<li class='page-item list-inline-item'>$page</li>";
    }
      echo '</ul></nav>';
  }
}

function bolby_portfolio_pagination($query) {
  $big = 12345678;
  $page_format = paginate_links( array(
      'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
      'format' => '?paged=%#%',
      'current' => max( 1, get_query_var('paged') ),
      'total' => $query->max_num_pages,
      'type'  => 'array',
      'prev_next' => false,
  ) );
  if( is_array($page_format) ) {
    $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
    echo '<ul class="portfolio-pagination list-inline d-none">';
    foreach ( $page_format as $page ) {
      echo "<li class='list-inline-item'>$page</li>";
    }
      echo '</ul>';
    echo '<div class="load-more text-center mt-4">
      <a href="javascript:" class="btn btn-default"><i class="fas fa-spinner"></i>'. esc_attr__("Load more", "bolby") .'</a>
    </div>';
  }
}






/* ================================================== */
/*    |         Comments Reply                   |    */
/* ================================================== */
function bolby_theme_enqueue_comments_reply() {
  if( get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'comment_form_before', 'bolby_theme_enqueue_comments_reply' );







/* ================================================== */
/*    |        Elementor Widget Category         |    */
/* ================================================== */
function bolby_elementor_widget_categories( $elements_manager ) {

  $elements_manager->add_category(
    'bolby-elements',
    [
      'title' => esc_attr( 'Bolby Elements', 'bolby' ),
      'icon' => 'fa fa-plug',
    ]
  );

}
add_action( 'elementor/elements/categories_registered', 'bolby_elementor_widget_categories' );