<?php
/* ================================================== */
/*    |                   Logo                   |    */
/* ================================================== */
if(!function_exists('bolby_logo_default')){
    function bolby_logo_default(){
    	$logo_default = get_theme_mod('logo_default');
		if($logo_default) {
		    echo '<a href="'; echo esc_url(home_url('/')); echo'" class="navbar-brand"><img src="'; echo esc_url( $logo_default ); echo '" alt="'. get_bloginfo( 'name' ) .'" /></a>';
		}
    }
}
?>
<?php
/* ================================================== */
/*    |                Header 1                  |    */
/* ================================================== */
if(!function_exists('bolby_theme_header_1')){
  function bolby_theme_header_1($menu_location, $menu_class){ ?>
	<!-- mobile header -->
	<header class="mobile-header-1">
		<div class="container">
			<!-- menu icon -->
			<div class="menu-icon mr-4">
				<button>
					<span></span>
				</button>
			</div>
			<!-- logo image -->
			<div class="site-logo">
				<?php
					if(get_theme_mod('logo_default')){
						bolby_logo_default();
					} else {
						echo '<a href="'; echo esc_url(home_url('/')); 
						echo '" class="logo-text">'; 
						echo get_bloginfo( 'name' ); 
						echo '</a>'; 
					}
				?>
			</div>
		</div>
	</header>
	<!-- desktop header -->
	<header class="desktop-header-1 d-flex align-items-start flex-column">
		
		<!-- logo image -->
		<div class="site-logo">
			<?php
				if(get_theme_mod('logo_default')){
					bolby_logo_default();
				} else {
					echo '<a href="'; echo esc_url(home_url('/')); 
					echo '" class="logo-text">'; 
					echo get_bloginfo( 'name' ); 
					echo '</a>'; 
				}
			?>
		</div>
		
		<!-- main menu -->
		<nav>
			<?php
                if ( has_nav_menu( ''. $menu_location .'' ) ) {
                    wp_nav_menu(
                        array( 
                            'theme_location'  => ''. $menu_location .'',
                            'depth' => 2,
                            'container' => false,
                            'menu_class' => 'nav-menu vertical-menu '. $menu_class .'',
                            'fallback_cb'     => 'bolby_Nav_Menu::fallback',
                            'walker' => new bolby_Nav_Menu(), 
                        ) 
                    );
                } else {
                    if ( ! is_admin() ) {
                        echo '<h6 class="mt-4"><a href="'. esc_url(admin_url( 'nav-menus.php' )) .'" class="add-menu-link">'; esc_attr_e('Add a menu', 'bolby'); echo '</a></h6>';
                    }
                }
            ?>
		</nav>
		
		<!-- site footer -->
		<div class="footer">
			<?php if ( is_active_sidebar( 'language-switcher-widget-area' ) ) : ?>
				<div class="language-switcher mb-3">
					<?php dynamic_sidebar( 'language-switcher-widget-area' ); ?>
				</div>
			<?php endif; ?>
			<div class="clearfix"></div>
			<!-- copyright text -->
			<span class="copyright">
				<?php
					$copyright = get_theme_mod('copyright');
					if( $copyright ) {
						echo esc_attr( $copyright );
					} else {
						echo esc_attr__('© 2020 Bolby Theme.','bolby');
					}
				?>
			</span>
		</div>

	</header>
<?php } } ?>
<?php
/* ================================================== */
/*    |                Header 2                  |    */
/* ================================================== */
if(!function_exists('bolby_theme_header_2')){
  function bolby_theme_header_2($menu_location, $menu_class){ ?>
	<!-- mobile header -->
	<header class="mobile-header-2">
		<div class="container">
			<!-- menu icon -->
			<div class="menu-icon mr-4">
				<button>
					<span></span>
				</button>
			</div>
			<!-- logo image -->
			<div class="site-logo">
				<?php
					if(get_theme_mod('logo_default')){
						bolby_logo_default();
					} else {
						echo '<a href="'; echo esc_url(home_url('/')); 
						echo '" class="logo-text">'; 
						echo get_bloginfo( 'name' ); 
						echo '</a>'; 
					}
				?>
			</div>
		</div>
	</header>

	<!-- desktop header -->
	<header class="desktop-header-2 d-flex align-items-start flex-column">
		
		<!-- logo image -->
		<div class="site-logo">
			<?php
				if(get_theme_mod('logo_default')){
					bolby_logo_default();
				} else {
					echo '<a href="'; echo esc_url(home_url('/')); 
					echo '" class="logo-text">'; 
					echo get_bloginfo( 'name' ); 
					echo '</a>'; 
				}
			?>
		</div>
		
		<!-- main menu -->
		<nav>
			<?php
                if ( has_nav_menu( ''. $menu_location .'' ) ) {
                    wp_nav_menu(
                        array( 
                            'theme_location'  => ''. $menu_location .'',
                            'depth' => 2,
                            'container' => false,
                            'menu_class' => 'nav-menu vertical-menu '. $menu_class .'',
                            'fallback_cb'     => 'bolby_Nav_Menu::fallback',
                            'walker' => new bolby_Nav_Menu(), 
                        ) 
                    );
                } else {
                    if ( ! is_admin() ) {
                        echo '<h6 class="mt-4"><a href="'. esc_url(admin_url( 'nav-menus.php' )) .'" class="add-menu-link">'; esc_attr_e('Add a menu', 'bolby'); echo '</a></h6>';
                    }
                }
            ?>
		</nav>
		
		<!-- site footer -->
		<div class="footer">
			<?php if ( is_active_sidebar( 'language-switcher-widget-area' ) ) : ?>
				<div class="language-switcher mb-3">
					<?php dynamic_sidebar( 'language-switcher-widget-area' ); ?>
				</div>
			<?php endif; ?>
			<div class="clearfix"></div>
			<!-- copyright text -->
			<span class="copyright">
				<?php
					$copyright = get_theme_mod('copyright');
					if( $copyright ) {
						echo esc_attr( $copyright );
					} else {
						echo esc_attr__('© 2020 Bolby Theme.','bolby');
					}
				?>
			</span>
		</div>

	</header>
<?php } } ?>
<?php
/* ================================================== */
/*    |                Header 3                  |    */
/* ================================================== */
if(!function_exists('bolby_theme_header_3')){
  function bolby_theme_header_3($menu_location, $menu_class){ ?>
	<header class="desktop-header-3 <?php if( get_theme_mod('sticky_header', false) == true ) {echo 'fixed-top';} ?>">
	<div class="container">

		<nav class="navbar navbar-expand-lg">

			<?php
				if(get_theme_mod('logo_default')){
					bolby_logo_default();
				} else {
					echo '<a href="'; echo esc_url(home_url('/')); 
					echo '" class="logo-text">'; 
					echo get_bloginfo( 'name' ); 
					echo '</a>'; 
				}
			?>
			<?php if ( is_active_sidebar( 'language-switcher-widget-area' ) ) : ?>
				<div class="language-switcher ml-auto mr-3 d-md-block d-sm-block d-lg-none">
					<?php dynamic_sidebar( 'language-switcher-widget-area' ); ?>
				</div>
			<?php endif; ?>
			<div class="menu-icon">
				<button aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation" data-target="#navbarNavDropdown" data-toggle="collapse" type="button">
					<span></span>
				</button>
			</div>
			<?php
				wp_nav_menu( array(
					'theme_location'  => ''. $menu_location .'',
					'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
					'container'       => 'div',
					'container_class' => 'collapse navbar-collapse',
					'container_id'    => 'navbarNavDropdown',
					'menu_class'      => 'nav-menu navbar-nav ml-auto '. $menu_class .'',
					'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
					'walker'          => new WP_Bootstrap_Navwalker(),
				) );
			?>
			<?php if ( is_active_sidebar( 'language-switcher-widget-area' ) ) : ?>
				<div class="language-switcher ml-5 d-none d-lg-block">
					<?php dynamic_sidebar( 'language-switcher-widget-area' ); ?>
				</div>
			<?php endif; ?>
		</nav>

	</div>
	</header>
<?php } } ?>
<?php
/* ================================================== */
/*    |       Single Post WP Link Pages          |    */
/* ================================================== */
if(!function_exists('bolby_theme_link_pages')){
  function bolby_theme_link_pages(){

        wp_link_pages( 'before=<ul class="page-links">&after=</ul>&link_before=<li class="page-link">&link_after=</li>' );

    } 
}
?>