<?php

/* Register Styles */
function bolby_theme_styles()
{
	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', 'style');

	wp_enqueue_style('font-awesome-5', get_template_directory_uri() . '/css/all.min.css', 'style');

	wp_enqueue_style('simple-line-icons', get_template_directory_uri() . '/css/simple-line-icons.css', 'style');

	wp_enqueue_style('slick', get_template_directory_uri() . '/css/slick.css', 'style');
	
	wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.css', 'style');

	wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/css/magnific-popup.css', 'style');

	wp_enqueue_style('bolby-default-style', get_template_directory_uri() . '/css/style.css', 'style');

	wp_enqueue_style('bolby-style', get_template_directory_uri() . '/style.css', 'style');
	
}
add_action( 'wp_enqueue_scripts', 'bolby_theme_styles' );




/* Register Scripts */
function bolby_theme_scripts()
{

	wp_enqueue_style( 'bolby-fonts', bolby_theme_fonts_url(), array(), '1.0.0' );

	wp_enqueue_script( 'popper', get_template_directory_uri() . '/js/popper.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'isotope', get_template_directory_uri() . '/js/isotope.pkgd.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'infinite-scroll', get_template_directory_uri() . '/js/infinite-scroll.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'imagesloaded' );

	wp_enqueue_script( 'slick-slider', get_template_directory_uri() . '/js/slick.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'jquery-easing', get_template_directory_uri() . '/js/jquery.easing.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'waypoints', get_template_directory_uri() . '/js/jquery.waypoints.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'counterup', get_template_directory_uri() . '/js/jquery.counterup.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'morphext', get_template_directory_uri() . '/js/morphext.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'parallax', get_template_directory_uri() . '/js/parallax.min.js', array('jquery'),'',true );

	wp_enqueue_script( 'wow-js', get_template_directory_uri() . '/js/wow.min.js', array('jquery'),'',true );

	wp_enqueue_script('bolby-custom-js', get_template_directory_uri() . '/js/custom.js', array('jquery'), '', true);

}
add_action( 'wp_enqueue_scripts', 'bolby_theme_scripts' );

function bolby_theme_fonts_url() {
    $font_url = '';
    
    /*
    Translators: If there are characters in your language that are not supported
    by chosen font(s), translate this to 'off'. Do not translate into your own language.
     */
    if ( 'off' !== _x( 'on', 'Google font: on or off', 'bolby' ) ) {
        $font_url = add_query_arg( 'family', urldecode( 'Rubik:300,300i,400,400i,500,500i,700,700i,900,900i&display=swap&subset=cyrillic' ), "//fonts.googleapis.com/css" );
    }
    return esc_url_raw( $font_url );
}

function bolby_custom_wp_admin_style(){
    wp_enqueue_style('bolby-admin', get_template_directory_uri() . '/css/admin.css', 'style');
}
add_action('admin_enqueue_scripts', 'bolby_custom_wp_admin_style');