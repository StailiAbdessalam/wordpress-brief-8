<?php
function prefix_demo_import_lists() {
	$demo_lists = array(
		'demo1' => array(
			'title'          => __( 'Title 1', 'text-domain' ), /*Title*/
			'is_pro'         => false, /*Is Premium*/
			'type'           => 'gutentor', /*Optional eg gutentor, elementor or other page builders or type*/
			'author'         => __( 'Gutentor', 'text-domain' ), /*Author Name*/
			'keywords'       => array( 'medical', 'multipurpose' ), /*Search keyword*/
			'categories'     => array( 'medical', 'multipurpose' ), /*Categories*/
			'template_url'   => array(
				'content' => 'full-url-path/content.json', /*Full URL Path to content.json*/
				'options' => 'full-url-path/master/options.json', /*Full URL Path to options.json*/
				'widgets' => 'full-url-path/widgets.json', /*Full URL Path to widgets.json*/
			),
			'screenshot_url' => 'full-url-path/screenshot.png?ver=1.6', /*Full URL Path to demo screenshot image*/
			'demo_url'       => 'https://www.demo.cosmoswp.com/', /*
		Full URL Path to Live Demo*/
			/* Recommended plugin for this demo */
			'plugins'        => array(
				array(
					'name' => __( 'Gutentor', 'text-domain' ),
					'slug' => 'gutentor',
				),
			),
		),
		'demo2' => array(
			'title'          => __( 'Title 2', 'text-domain' ), /*Title*/
			'is_pro'         => false, /*Is Premium*/
			'type'           => 'gutentor', /*Optional eg gutentor, elementor or other page builders or type*/
			'author'         => __( 'Gutentor', 'text-domain' ), /*Author Name*/
			'keywords'       => array( 'about-block', 'about 3' ), /*Search keyword*/
			'categories'     => array( 'contact', 'multipurpose', 'woocommerce' ), /*Categories*/
			'template_url'   => array(
				'content' => 'full-url-path/content.json', /*Full URL Path to content.json*/
				'options' => 'full-url-path/master/options.json', /*Full URL Path to options.json*/
				'widgets' => 'full-url-path/widgets.json', /*Full URL Path to widgets.json*/
			),
			'screenshot_url' => 'full-url-path/screenshot.png?ver=1.6', /*Full URL Path to demo screenshot image*/
			'demo_url'       => 'https://www.demo.cosmoswp.com/', /*
		Full URL Path to Live Demo*/
			/* Recommended plugin for this demo */
			'plugins'        => array(
				array(
					'name' => __( 'Gutentor', 'text-domain' ),
					'slug' => 'gutentor',
				),
				array(
					'name'      => __( 'Contact Form 7', 'text-domain' ),
					'slug'      => 'contact-form-7',
					'main_file' => 'wp-contact-form-7.php', /*the main plugin file of contact form 7 is different from plugin slug */
				),
			),
		),
		'demo3' => array(
			'title'          => __( 'Title 1', 'text-domain' ), /*Title*/
			'is_pro'         => true, /*Is Premium : Support Premium Version*/
			'pro_url'        => 'https://www.cosmoswp.com/pricing/', /*Premium version/Pricing Url*/
			'type'           => 'gutentor', /*Optional eg gutentor, elementor or other page builders or type*/
			'author'         => __( 'Gutentor', 'text-domain' ), /*Author Name*/
			'keywords'       => array( 'woocommerce', 'shop' ), /*Search keyword*/
			'categories'     => array( 'woocommerce', 'multipurpose' ), /*Categories*/
			'template_url'   => array(/* Optional for premium theme, you can add your own logic by hook*/
				'content' => 'full-url-path/content.json', /*Full URL Path to content.json*/
				'options' => 'full-url-path/master/options.json', /*Full URL Path to options.json*/
				'widgets' => 'full-url-path/widgets.json', /*Full URL Path to widgets.json*/
			),
			'screenshot_url' => 'full-url-path/screenshot.png?ver=1.6', /*Full URL Path to demo screenshot image*/
			'demo_url'       => 'https://www.demo.cosmoswp.com/', /*
		Full URL Path to Live Demo*/
			/* Recommended plugin for this demo */
			'plugins'        => array(
				array(
					'name' => __( 'Gutentor', 'text-domain' ),
					'slug' => 'gutentor',
				),
			),
		),
		/*and so on ............................*/
	);
	return $demo_lists;
}
add_filter( 'advanced_import_demo_lists', 'prefix_demo_import_lists' );
