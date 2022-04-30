<?php
if (!function_exists('bolby_theme_custom_css'))
{
    function bolby_theme_custom_css()
    {

        // init colors
        $header_copyright_color = '';
        $header_bg_color = '';
        $header_border_color = '';
        $menu_color = '';
        $menu_hover_color = '';
        $menu_icon_color = '';
        $menu_icon_hover_color = '';
        $menu_active_color = '';
        $site_title_color = '';
        $hamburger_color = '';
        $body_color = '';
        $link_color = '';
        $link_hover_color = '';
        $default_color = '';
        $secondary_color = '';
        $third_color = '';
        $fourth_color = '';
        $dark_box_color = '';
        $meta_data_color = '';
        $headings_color = '';
        $blog_title_color = '';
        $footer_bg_color = '';
        $footer_copyright_color = '';
        $preloader_bg = '';

        // enqueue
        $dir = get_template_directory_uri();
        wp_enqueue_style('bolby-theme-color', $dir . '/css/custom_script.css', array() , '', 'all');

        // customizer options
        $header_copyright_color = esc_attr(get_theme_mod('header_copyright_color'));
        $header_bg_color = esc_attr(get_theme_mod('header_bg_color'));
        $header_border_color = esc_attr(get_theme_mod('header_border_color'));
        $menu_color = esc_attr(get_theme_mod('menu_color'));
        $menu_hover_color = esc_attr(get_theme_mod('menu_hover_color'));
        $menu_icon_color = esc_attr(get_theme_mod('menu_icon_color'));
        $menu_icon_hover_color = esc_attr(get_theme_mod('menu_icon_hover_color'));
        $menu_active_color = esc_attr(get_theme_mod('menu_active_color'));
        $site_title_color = esc_attr(get_theme_mod('site_title_color'));
        $hamburger_color = esc_attr(get_theme_mod('hamburger_color'));
        $body_color = esc_attr(get_theme_mod('body_color'));
        $link_color = esc_attr(get_theme_mod('link_color'));
        $link_hover_color = esc_attr(get_theme_mod('link_hover_color'));
        $default_color = esc_attr(get_theme_mod('default_color'));
        $secondary_color = esc_attr(get_theme_mod('secondary_color'));
        $third_color = esc_attr(get_theme_mod('third_color'));
        $fourth_color = esc_attr(get_theme_mod('fourth_color'));
        $dark_box_color = esc_attr(get_theme_mod('dark_box_color'));
        $meta_data_color = esc_attr(get_theme_mod('meta_data_color'));
        $headings_color = esc_attr(get_theme_mod('headings_color'));
        $blog_title_color = esc_attr(get_theme_mod('blog_title_color'));
        $footer_bg_color = esc_attr(get_theme_mod('footer_bg_color'));
        $footer_copyright_color = esc_attr(get_theme_mod('footer_copyright_color'));
        $preloader_bg = esc_attr(get_theme_mod('preloader_bg'));

        if ($header_copyright_color)
        {

            $header_copyright_color = "
            header.desktop-header-1 .copyright,
            header.desktop-header-2 .copyright {
              color: {$header_copyright_color};
            }
          ";

        }

        if ($header_bg_color)
        {

            $header_bg_color = "
              .desktop-header-3, 
              header.desktop-header-1, 
              header.mobile-header-1, 
              header.desktop-header-2, 
              header.mobile-header-2 {
                background: {$header_bg_color};
              }
            ";

        }

        if ($header_border_color)
        {

            $header_border_color = "
            header.desktop-header-1,
            header.desktop-header-2 {
                border-right-color: {$header_border_color};
              }
            ";

        }

        if ($menu_color)
        {

            $menu_color = "
            .nav-menu li .nav-link {
              color: {$menu_color};
            }
          ";

        }

        if ($menu_hover_color)
        {

            $menu_hover_color = "
            .nav-menu li .nav-link:hover,
            .nav-menu li.current-menu-item .nav-link:hover, 
            .nav-menu li.active .nav-link:hover,
            body.dark .nav-menu li .nav-link:hover,
            body.dark .nav-menu li.current-menu-item .nav-link:hover, 
            body.dark .nav-menu li.active .nav-link:hover {
              color: {$menu_hover_color};
            }
          ";

        }

        if ($menu_icon_color)
        {

            $menu_icon_color = "
            .desktop-header-1 .nav-menu i {
              color: {$menu_icon_color};
            }
          ";

        }

        if ($menu_icon_hover_color)
        {

            $menu_icon_hover_color = "
            .nav-menu i:hover {
              color: {$menu_icon_hover_color};
            }
          ";

        }

        if ($menu_active_color)
        {

            $menu_active_color = "
            .nav-menu li.current-menu-item .nav-link,
            .nav-menu li.active .nav-link,
            .nav-menu li.current-menu-item .nav-link i,
            .nav-menu li.active .nav-link i,
            .nav-menu li .nav-link.active {
              color: {$menu_active_color};
            }
          ";

        }

        if ($site_title_color)
        {

            $site_title_color = "
            header .logo-text {
              color: {$site_title_color};
            }
          ";

        }
        if ($hamburger_color)
        {

            $hamburger_color = "
            header.mobile-header-1 .menu-icon span,
            header.mobile-header-1 .menu-icon span:before,
            header.mobile-header-1 .menu-icon span:after,
            header.mobile-header-2 .menu-icon span,
            header.mobile-header-2 .menu-icon span:before,
            header.mobile-header-2 .menu-icon span:after,
            header.desktop-header-3 .menu-icon span,
            header.desktop-header-3 .menu-icon span:before,
            header.desktop-header-3 .menu-icon span:after {
              background: {$hamburger_color};
            }
          ";

        }

        if ($body_color)
        {

            $body_color = "
            body {
              background-color: {$body_color};
            }
          ";

        }

        if ($link_color)
        {

            $link_color = "
            a,
            .wp-block-calendar tfoot a {
              color: {$link_color};
            }
          ";

        }

        if ($link_hover_color)
        {

            $link_hover_color = "
            a:hover,
            .blog-standard .meta li a:hover,
            .wp-block-calendar tfoot a:hover,
            .blog-standard .title a:hover,
            .comment-author .fn:hover {
              color: {$link_hover_color};
            }
          ";

        }

        if ($default_color)
        {

            $default_color = "
            .btn-default, 
            button, 
            input[type=submit], 
            input[type=button],
            .widget .searchform input[type=submit],
            .comment-respond input[type=submit],
            .pagination > li > a.current, 
            .pagination > li > span.current,
            .tags a:hover,
            #return-to-top:hover,
            .wp-block-search button[type=submit],
            .page-links li,
            .page-links a li:hover,
            .post-password-form input[type=submit],
            .portfolio-item .details span.term,
            .blog-item .category,
            .slick-dots li.slick-active button:before,
            .woocommerce #respond input#submit:hover, 
            .woocommerce a.button:hover, 
            .woocommerce button.button:hover, 
            .woocommerce input.button:hover,
            .woocommerce nav.woocommerce-pagination ul li a:focus, 
            .woocommerce nav.woocommerce-pagination ul li a:hover, 
            .woocommerce nav.woocommerce-pagination ul li span.current {
              background: {$default_color};
            }

            .portfolio-filter li:hover,
            .portfolio-filter li.current,
            .blog-item .details h4.title a:hover,
            .blog-standard .title a:hover,
            .blog-standard .meta li a:hover,
            body.dark .portfolio-filter li.current,
            body.dark .blog-item .details h4.title a:hover,
            .timeline.edu .timeline-container::after,
            .timeline.exp .timeline-container::after,
            .comment-author .fn:hover,
            .bypostauthor .comment-footer-meta .by-post-author,
            .comment-respond .comment-notes a:focus,
            .comment-respond .comment-notes a:hover,
            .comment-respond .logged-in-as a:focus,
            .comment-respond .logged-in-as a:hover,
            .woocommerce ul.products li.product .price,
            .woocommerce div.product p.price,
            .woocommerce div.product .woocommerce-tabs ul.tabs li.active a,
            .woocommerce div.product .woocommerce-tabs ul.tabs li a:hover,
            .woocommerce-message::before,
            .woocommerce-error::before, .woocommerce-info:before,
            .timeline i {
                color: {$default_color};
            }

            .woocommerce a.remove {
                color: {$default_color} !important;
            }

            .comment-reply-link,
            .timeline span.line,
            .woocommerce #respond input#submit, 
            .woocommerce a.button, 
            .woocommerce button.button, 
            .woocommerce input.button,
            .woocommerce #respond input#submit.alt, 
            .woocommerce a.button.alt, 
            .woocommerce button.button.alt, 
            .woocommerce input.button.alt,
            .woocommerce #respond input#submit.alt:hover, 
            .woocommerce a.button.alt:hover, 
            .woocommerce button.button.alt:hover, 
            .woocommerce input.button.alt:hover,
            .woocommerce a.remove:hover,
            .select2-container--default .select2-results__option--highlighted[aria-selected], 
            .select2-container--default .select2-results__option--highlighted[data-selected],
            .woocommerce #respond input#submit.alt.disabled, 
            .woocommerce #respond input#submit.alt.disabled:hover, 
            .woocommerce #respond input#submit.alt:disabled, 
            .woocommerce #respond input#submit.alt:disabled:hover, 
            .woocommerce #respond input#submit.alt:disabled[disabled], 
            .woocommerce #respond input#submit.alt:disabled[disabled]:hover, 
            .woocommerce a.button.alt.disabled, 
            .woocommerce a.button.alt.disabled:hover, 
            .woocommerce a.button.alt:disabled, 
            .woocommerce a.button.alt:disabled:hover, 
            .woocommerce a.button.alt:disabled[disabled], 
            .woocommerce a.button.alt:disabled[disabled]:hover, 
            .woocommerce button.button.alt.disabled, 
            .woocommerce button.button.alt.disabled:hover, 
            .woocommerce button.button.alt:disabled, 
            .woocommerce button.button.alt:disabled:hover, 
            .woocommerce button.button.alt:disabled[disabled], 
            .woocommerce button.button.alt:disabled[disabled]:hover, 
            .woocommerce input.button.alt.disabled, 
            .woocommerce input.button.alt.disabled:hover, 
            .woocommerce input.button.alt:disabled, 
            .woocommerce input.button.alt:disabled:hover, 
            .woocommerce input.button.alt:disabled[disabled], 
            .woocommerce input.button.alt:disabled[disabled]:hover {
                background-color: {$default_color};
            }

            .tags a:hover,
            .page-links li,
            .page-links a li:hover:hover,
            .post-password-form input[type=submit] {
                border-color: {$default_color};
            }
            blockquote {
                border-left-color: {$default_color};
            }
            .woocommerce-message,
            .woocommerce-error, 
            .woocommerce-info {
                border-top-color: {$default_color};
            }
            ";

        }

        if ($secondary_color)
        {

            $secondary_color = "
            .portfolio-item .details .more-button {
              background: {$secondary_color};
            }
            .social-icons.light li a:hover {
                color: {$secondary_color};
            }
          ";

        }

        if ($third_color)
        {

            $third_color = "
            .portfolio-item .mask,
            .price-item .badge {
              background: {$third_color};
            }
          ";

        }

        if ($fourth_color)
        {

            $fourth_color = "
            .scroll-down.light .mouse .wheel,
            .woocommerce-account .woocommerce-MyAccount-navigation {
              background: {$fourth_color};
            }
            .progress-bar,
            .woocommerce span.onsale {
                background-color: {$fourth_color};
            }
            .scroll-down.light .mouse {
                border-color: {$fourth_color};
            }
            th, dt, strong,
            .comment-author .fn {
                color: {$fourth_color};
            }
          ";

        }

        if ($dark_box_color)
        {

            $dark_box_color = "
            body.dark .bg-white,
            body.dark .bg-dark {
              background-color: {$dark_box_color} !important;
            }
          ";

        }

        if ($meta_data_color)
        {

            $meta_data_color = "
            .blog-standard .meta {
              color: {$meta_data_color};
            }
          ";

        }

        if ($headings_color)
        {

            $headings_color = "
            h1, h2, h3, h4, h5, h6 {
              color: {$headings_color};
            }
          ";

        }

        if ($blog_title_color)
        {

            $blog_title_color = "
            .blog-standard .title a,
            .blog-item .details h4.title a {
              color: {$blog_title_color};
            }
          ";

        }

        if ($footer_bg_color)
        {

            $footer_bg_color = "
            footer.footer {
              background: {$footer_bg_color};
            }
          ";

        }

        if ($footer_copyright_color)
        {

            $footer_copyright_color = "
            footer.footer .copyright {
              color: {$footer_copyright_color};
            }
          ";

        }

        if ($preloader_bg)
        {

            $preloader_bg = "
            #preloader {
              background: {$preloader_bg};
            }
          ";

        }
        
        wp_add_inline_style('bolby-theme-color', $header_copyright_color);
        wp_add_inline_style('bolby-theme-color', $header_bg_color);
        wp_add_inline_style('bolby-theme-color', $header_border_color);
        wp_add_inline_style('bolby-theme-color', $menu_color);
        wp_add_inline_style('bolby-theme-color', $menu_hover_color);
        wp_add_inline_style('bolby-theme-color', $menu_icon_color);
        wp_add_inline_style('bolby-theme-color', $menu_icon_hover_color);
        wp_add_inline_style('bolby-theme-color', $menu_active_color);
        wp_add_inline_style('bolby-theme-color', $site_title_color);
        wp_add_inline_style('bolby-theme-color', $hamburger_color);
        wp_add_inline_style('bolby-theme-color', $body_color);
        wp_add_inline_style('bolby-theme-color', $link_color);
        wp_add_inline_style('bolby-theme-color', $link_hover_color);
        wp_add_inline_style('bolby-theme-color', $default_color);
        wp_add_inline_style('bolby-theme-color', $secondary_color);
        wp_add_inline_style('bolby-theme-color', $third_color);
        wp_add_inline_style('bolby-theme-color', $fourth_color);
        wp_add_inline_style('bolby-theme-color', $dark_box_color);
        wp_add_inline_style('bolby-theme-color', $meta_data_color);
        wp_add_inline_style('bolby-theme-color', $headings_color);
        wp_add_inline_style('bolby-theme-color', $blog_title_color);
        wp_add_inline_style('bolby-theme-color', $footer_bg_color);
        wp_add_inline_style('bolby-theme-color', $footer_copyright_color);
        wp_add_inline_style('bolby-theme-color', $preloader_bg);
    }
    add_action('wp_enqueue_scripts', 'bolby_theme_custom_css', PHP_INT_MAX);
}