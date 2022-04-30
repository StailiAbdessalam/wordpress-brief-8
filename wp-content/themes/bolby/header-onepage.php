<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <?php wp_head(); ?>
    
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php if( true == get_theme_mod('preloader', true) ) : ?>
<!-- Preloader -->
<div id="preloader">
	<div class="outer">
		<!-- Google Chrome -->
		<div class="infinityChrome">
			<div></div>
			<div></div>
			<div></div>
		</div>

		<!-- Safari and others -->
		<div class="infinity">
			<div>
				<span></span>
			</div>
			<div>
				<span></span>
			</div>
			<div>
				<span></span>
			</div>
		</div>
		<!-- Stuff -->
		<svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="goo-outer">
			<defs>
				<filter id="goo">
					<feGaussianBlur in="SourceGraphic" stdDeviation="6" result="blur" />
					<feColorMatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo" />
					<feBlend in="SourceGraphic" in2="goo" />
				</filter>
			</defs>
		</svg>
	</div>
</div>
<?php endif; ?>

<?php
	if( get_theme_mod('header_layout') == 'header_1') {
		bolby_theme_header_1('onepage-menu', 'scrollspy'); 
	} elseif( get_theme_mod('header_layout') == 'header_2') {
		bolby_theme_header_2('onepage-menu', 'scrollspy');
	} else {
		bolby_theme_header_3('onepage-menu', 'scrollspy');
	}
?>

<!-- main layout -->
<main class="<?php if( get_theme_mod('header_layout') == 'header_1') {
		echo 'content-1';
	} elseif( get_theme_mod('header_layout') == 'header_2') {
		echo 'content-2';
	} else {
		echo 'content-3';
	} ?>">

    <div class="container">