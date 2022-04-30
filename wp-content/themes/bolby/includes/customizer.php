<?php

// Config
Kirki::add_config( 'bolby_settings', array(
  'capability'    => 'edit_theme_options',
  'option_type'   => 'theme_mod',
) );

// Sections
Kirki::add_section( 'header_logo', array(
    'title'          => esc_attr__( 'Logo', 'bolby' ),
    'panel'          => 'header',
) );

Kirki::add_section( 'header_layout', array(
    'title'          => esc_attr__( 'Layout', 'bolby' ),
    'panel'          => 'header',
) );

Kirki::add_section( 'header_style', array(
    'title'          => esc_attr__( 'Style', 'bolby' ),
    'panel'          => 'header',
) );

Kirki::add_section( 'site_color', array(
    'title'          => esc_attr__( 'Color', 'bolby' ),
    'priority'       => 150,
) );

Kirki::add_section( 'typography', array(
    'title'          => esc_attr__( 'Typography', 'bolby' ),
    'priority'       => 160,
) );

Kirki::add_section( 'footer', array(
    'title'          => esc_attr__( 'Footer', 'bolby' ),
    'priority'       => 175,
) );

Kirki::add_section( 'miscellaneous', array(
    'title'          => esc_attr__( 'Miscellaneous', 'bolby' ),
    'priority'       => 170,
) );

Kirki::add_section( 'blog_archive', array(
    'title'          => esc_attr__( 'Blog Posts', 'bolby' ),
    'panel'          => 'blog',
) );

Kirki::add_section( 'blog_single', array(
    'title'          => esc_attr__( 'Single Post', 'bolby' ),
    'panel'          => 'blog',
) );

// Panels
Kirki::add_panel( 'header', array(
    'priority'    => 130,
    'title'       => esc_attr__( 'Header', 'bolby' ),
) );

Kirki::add_panel( 'blog', array(
    'priority'    => 145,
    'title'       => esc_attr__( 'Blog', 'bolby' ),
) );

// Header
Kirki::add_field( 'bolby_settings', array(
    'type'        => 'image',
    'settings'    => 'logo_default',
    'label'       => esc_attr__( 'Logo', 'bolby' ),
    'description' => esc_attr__( 'Upload your image file', 'bolby' ),
    'section'     => 'header_logo',
) );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'radio-image',
	'settings'    => 'header_layout',
	'label'       => esc_html__( 'Header layout', 'bolby' ),
	'section'     => 'header_layout',
	'default'     => 'header_3',
	'priority'    => 10,
	'choices'     => [
		'header_1'   => get_template_directory_uri() . '/images/header_1.jpg',
		'header_2' => get_template_directory_uri() . '/images/header_2.jpg',
		'header_3'  => get_template_directory_uri() . '/images/header_3.jpg',
	],
] );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'sticky_header',
    'label'       => esc_attr__( 'Sticky header', 'bolby' ),
    'description' => esc_attr__('This option works with only header layout 3.', 'bolby'),
    'section'     => 'header_layout',
    'default'     => '0',
) );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'site_title_color',
	'label'       => esc_attr__( 'Site title color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFF',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'header_bg_color',
	'label'       => esc_attr__( 'Background color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#353353',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'header_border_color',
    'label'       => esc_attr__( 'Border right color', 'bolby' ),
    'description'       => esc_attr__( 'For the header 1 and header 2', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#494865',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'menu_color',
	'label'       => esc_attr__( 'Menu color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFFFFF',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'menu_hover_color',
	'label'       => esc_attr__( 'Menu hover color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFD15C',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'menu_active_color',
	'label'       => esc_attr__( 'Menu active color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFD15C',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'menu_icon_color',
	'label'       => esc_attr__( 'Menu icon color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFD15C',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'menu_icon_hover_color',
	'label'       => esc_attr__( 'Menu icon hover color', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFD15C',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'hamburger_color',
    'label'       => esc_attr__( 'Hamburger menu icon color', 'bolby' ),
    'description' => esc_attr__( 'Responsive menu click icon', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#FFF',
] );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'color',
	'settings'    => 'header_copyright_color',
    'label'       => esc_attr__( 'Copyright text color', 'bolby' ),
    'description' => esc_attr__( 'Text editable in Footer section of customizer', 'bolby' ),
	'section'     => 'header_style',
	'default'     => '#9C9AB3',
] );

// Blog
Kirki::add_field( 'bolby_settings', array(
    'type'        => 'switch',
    'settings'    => 'blog_meta',
    'label'       => esc_attr__( 'Meta data', 'bolby' ),
    'section'     => 'blog_archive',
    'default'     => '1',
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'bolby' ),
        'off' => esc_attr__( 'Disable', 'bolby' ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'blog_date',
    'label'       => esc_attr__( 'Date', 'bolby' ),
    'section'     => 'blog_archive',
    'default'     => '1',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'blog_author',
    'label'       => esc_attr__( 'Author', 'bolby' ),
    'section'     => 'blog_archive',
    'default'     => '1',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'blog_category',
    'label'       => esc_attr__( 'Category', 'bolby' ),
    'section'     => 'blog_archive',
    'default'     => '1',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'     => 'text',
    'settings' => 'except',
    'label'    => esc_attr__( 'Except length (words)', 'bolby' ),
    'section'  => 'blog_archive',
    'default'  => esc_attr__( '30', 'bolby' ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'blog_sidebar',
    'label'       => esc_attr__( 'Sidebar', 'bolby' ),
    'section'     => 'blog_archive',
    'default'     => '1',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'blog_single_sidebar',
    'label'       => esc_attr__( 'Sidebar', 'bolby' ),
    'section'     => 'blog_single',
    'default'     => '1',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'toggle',
    'settings'    => 'blog_comment',
    'label'       => esc_attr__( 'Comment', 'bolby' ),
    'section'     => 'blog_single',
    'default'     => '1',
) );

// Color
Kirki::add_field( 'bolby_settings', [
	'type'        => 'custom',
	'settings'    => 'section_title_1',
	'section'     => 'site_color',
	'default'     => '<div class="customizer-section-title">' . esc_html__( 'General', 'bolby' ) . '</div>'
] );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'body_color',
    'label'       => esc_attr__( 'Body background color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#F9F9FF',
) );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'toggle',
	'settings'    => 'dark_mode',
	'label'       => esc_html__( 'Dark mode', 'bolby' ),
	'section'     => 'site_color',
	'default'     => 'off',
	'choices'     => [
		'on'  => esc_html__( 'Enable', 'bolby' ),
		'off' => esc_html__( 'Disable', 'bolby' ),
	],
] );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'dark_box_color',
    'label'       => esc_attr__( 'Dark boxes color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#302f4e',
) );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'custom',
	'settings'    => 'section_title_2',
	'section'     => 'site_color',
	'default'     => '<div class="customizer-section-title">' . esc_html__( 'Typography', 'bolby' ) . '</div>'
] );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'link_color',
    'label'       => esc_attr__( 'Link color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#FF4C60',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'link_hover_color',
    'label'       => esc_attr__( 'Link hover color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#353353',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'headings_color',
    'label'       => esc_attr__( 'Headings color', 'bolby' ),
    'description' => esc_attr__('H1-H6 tags', 'bolby'),
    'section'     => 'site_color',
    'default'     => '#353353',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'meta_data_color',
    'label'       => esc_attr__( 'Meta data color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#8B88B1',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'blog_title_color',
    'label'       => esc_attr__( 'Blog title color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#353353',
) );

Kirki::add_field( 'bolby_settings', [
	'type'        => 'custom',
	'settings'    => 'section_title_3',
	'section'     => 'site_color',
	'default'     => '<div class="customizer-section-title">' . esc_html__( 'Default colors', 'bolby' ) . '</div>'
] );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'default_color',
    'label'       => esc_attr__( 'Default color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#FF4C60',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'secondary_color',
    'label'       => esc_attr__( 'Secondary color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#FFD15C',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'third_color',
    'label'       => esc_attr__( 'Third color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#6C6CE5',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'fourth_color',
    'label'       => esc_attr__( 'Fourth color', 'bolby' ),
    'section'     => 'site_color',
    'default'     => '#353353',
) );

// Typography
Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_body',
    'label'       => esc_attr__( 'Body', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'body',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_h1',
    'label'       => esc_attr__( 'Heading 1', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'h1',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_h2',
    'label'       => esc_attr__( 'Heading 2', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'h2',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_h3',
    'label'       => esc_attr__( 'Heading 3', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'h3',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_h4',
    'label'       => esc_attr__( 'Heading 4', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'h4',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_h5',
    'label'       => esc_attr__( 'Heading 5', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'h5',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_h6',
    'label'       => esc_attr__( 'Heading 6', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => 'h6',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_navmenu',
    'label'       => esc_attr__( 'Navigation menu', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => '.nav-menu li .nav-link',
        ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'typography',
    'settings'    => 'typo_button',
    'label'       => esc_attr__( 'Button', 'bolby' ),
    'section'     => 'typography',
    'default'     => array(
        'font-family'    => '',
        'variant'        => '',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'color'          => '',
        'text-transform' => '',
        'text-align'     => '',
    ),
    'output'      => array(
        array(
            'element' => '.btn-default, 
            button, 
            input[type=submit], 
            input[type=button],
            .widget .searchform input[type=submit],
            .comment-respond input[type=submit]',
        ),
    ),
) );

// Footer
Kirki::add_field( 'bolby_settings', array(
    'type'        => 'switch',
    'settings'    => 'enable_footer',
    'label'       => esc_attr__( 'Footer', 'bolby' ),
    'section'     => 'footer',
    'default'     => '1',
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'bolby' ),
        'off' => esc_attr__( 'Disable', 'bolby' ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'footer_bg_color',
    'label'       => esc_attr__( 'Background color', 'bolby' ),
    'section'     => 'footer',
    'default'     => '#353353',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'footer_copyright_color',
    'label'       => esc_attr__( 'Copyright text color', 'bolby' ),
    'section'     => 'footer',
    'default'     => '#9C9AB3',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'     => 'text',
    'settings' => 'copyright',
    'label'    => esc_attr__( 'Copyright text', 'bolby' ),
    'section'  => 'footer',
    'default'  => esc_attr__( '© 2020 Bolby Theme.', 'bolby' ),
) );

// Miscellaneous
Kirki::add_field( 'bolby_settings', array(
    'type'        => 'switch',
    'settings'    => 'preloader',
    'label'       => esc_attr__( 'Preloader', 'bolby' ),
    'section'     => 'miscellaneous',
    'default'     => '1',
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'bolby' ),
        'off' => esc_attr__( 'Disable', 'bolby' ),
    ),
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'color',
    'settings'    => 'preloader_bg',
    'label'       => esc_attr__( 'Preloader background color', 'bolby' ),
    'section'     => 'miscellaneous',
    'default'     => '#353353',
) );

Kirki::add_field( 'bolby_settings', array(
    'type'        => 'switch',
    'settings'    => 'go_top',
    'label'       => esc_attr__( 'Go to top', 'bolby' ),
    'section'     => 'miscellaneous',
    'default'     => '1',
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'bolby' ),
        'off' => esc_attr__( 'Disable', 'bolby' ),
    ),
) );