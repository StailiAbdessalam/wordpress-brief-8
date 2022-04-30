=== Advanced Import : One Click Import for WordPress or Theme Demo Data ===

Contributors: addonspress, codersantosh, acmeit
Donate link: https://addonspress.com/
Tags: import, advanced import, demo import, theme import, widget import, customizer import
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 5.6.20
Stable tag: 1.3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced Import is a very flexible plugin which convenient user to import site data( posts, page, media and even widget and customizer option ).

== Description ==

Import Data or Demo Content which is exported by [Advanced Export](https://wordpress.org/plugins/advanced-export/)

Advanced Import is one of the best and powerful data importer plugin. It has several features which make more manageable and convenient to WordPress user to import their WordPress site data from another website.

It is designed specially for theme developer who wants to provide demo data to their customer but it can be used for migration purpose too.

While you use Demo Import features of themes, Advanced Import may fetches screenshots, images and templates(demo) JSON files from a respective theme site. This helps you to import your site with a single click. You must accept the terms and privacy of the themes you are using to use Demo Import ( Starter Sites Template Library ) Features.

View [Advanced Import](https://www.addonspress.com/wordpress-plugins/advanced-import/) Details and Documentation

Some listed features of Advanced Import are given below :

* Import demo starter sites very easily
* Import widget
* Import option
* Import media,
* Import pages,
* Import post
* Import custom post type
* Import actual media files

== Features for Theme Author ==

* Code/Plugin example available
* Support for the premium version or premium plugin of the free theme
* Install separate dependent plugin/s for each demo starter packages of the theme
* Categorized available demo import starter packages to type and categories
* Search filter keywords for demo starter packages
* Sufficient hooks to customize the plugin design and functionality
* Add demo URL
* Add pro URL
* Better experience for the user

== Dashboard Location ==

= Theme Demo Import Screen =

Dashboard -> Appearance -> Demo Import

= Zip File Import Screen =

Dashboard -> Tool -> Advanced Import

== Installation ==
There are two ways to install any Advanced Import Plugin:-
1. Upload zip file from Dashboard->Plugin->Add New "upload plugin".
2. Extract Advanced Import and placed it to the "/wp-content/plugins/" directory.
    - Activate the plugin through the "Plugins" menu in WordPress.


== Frequently Asked Questions ==

= Is Advanced Import is a free plugin? =

Yes, it is a free plugin.

= Why my demo zip file is not importing? =

Please make sure it is exported using the [Advanced Export](https://wordpress.org/plugins/advanced-export/) plugin

= I have activated the plugin. Where is the "Demo Import"? =

Login to your WordPress dashboard, go to Appearance -> Demo Import.
You can also find Import zip options on WordPress dashboard -> Tools -> Advanced Import.

= Do I need to check to include media while Export? =

If you are providing starter content- NO, but if you are migrating site - Yes. As long as the exported site is live on the internet it is not necessary but if you are migrating the site from local to live you should check it.
Technically, if you check to include media, the zip files content media files. Huge media files may cause other issues. Generally not recommended to check but you can check media as your requirements.

= I am a theme author, how can I use this plugin for my theme? =

First of all, you need to Export your theme demo data from your live demo site using [Advanced Export](https://wordpress.org/plugins/advanced-export/) plugin.
Export the zip file, it should contain 3 files content.json, options.json and widgets.json.
If you are submitting theme on WordPress dot org, you are not allowed to include Demo files ( XML, JSON, ZIP), You have to create a separate plugin for your theme/company. We would like to highly recommend to create a single plugin for your all themes. For other platforms, you may add code on your theme.
Code Example :
`
function prefix_demo_import_lists(){
   $demo_lists = array(
      'demo1' =>array(
         'title' => __( 'Title 1', 'text-domain' ),/*Title*/
         'is_pro' => false,/*Is Premium*/
         'type' => 'gutentor',/*Optional eg gutentor, elementor or other page builders or type*/
         'author' => __( 'Gutentor', 'text-domain' ),/*Author Name*/
         'keywords' => array( 'medical', 'multipurpose' ),/*Search keyword*/
         'categories' => array( 'medical','multipurpose' ),/*Categories*/
            'template_url' => array(
                'content' => 'full-url-path/content.json',/*Full URL Path to content.json*/
                'options' => 'full-url-path/master/options.json',/*Full URL Path to options.json*/
                'widgets' => 'full-url-path/widgets.json'/*Full URL Path to widgets.json*/
            ),
         'screenshot_url' => 'full-url-path/screenshot.png?ver=1.6',/*Full URL Path to demo screenshot image*/
         'demo_url' => 'https://www.demo.cosmoswp.com/',/*Full URL Path to Live Demo*/
         /* Recommended plugin for this demo */
         'plugins' => array(
            array(
               'name'      => __( 'Gutentor', 'text-domain' ),
               'slug'      => 'gutentor',
            ),
         )
      ),
        'demo2' =>array(
            'title' => __( 'Title 2', 'text-domain' ),/*Title*/
            'is_pro' => false,/*Is Premium*/
            'type' => 'gutentor',/*Optional eg gutentor, elementor or other page builders or type*/
            'author' => __( 'Gutentor', 'text-domain' ),/*Author Name*/
            'keywords' => array( 'about-block', 'about 3' ),/*Search keyword*/
            'categories' => array( 'contact','multipurpose','woocommerce' ),/*Categories*/
            'template_url' => array(
                'content' => 'full-url-path/content.json',/*Full URL Path to content.json*/
                'options' => 'full-url-path/master/options.json',/*Full URL Path to options.json*/
                'widgets' => 'full-url-path/widgets.json'/*Full URL Path to widgets.json*/
            ),
            'screenshot_url' => 'full-url-path/screenshot.png?ver=1.6',/*Full URL Path to demo screenshot image*/
            'demo_url' => 'https://www.demo.cosmoswp.com/',/*Full URL Path to Live Demo*/
            /* Recommended plugin for this demo */
            'plugins' => array(
                array(
                    'name'      => __( 'Gutentor', 'text-domain' ),
                    'slug'      => 'gutentor',
                ),
                array(
                    'name'      => __( 'Contact Form 7', 'text-domain' ),
                    'slug'      => 'contact-form-7',
                    'main_file' => 'wp-contact-form-7.php',/*the main plugin file of contact form 7 is different from plugin slug */
                ),
            )
        ),
        'demo3' =>array(
            'title' => __( 'Title 1', 'text-domain' ),/*Title*/
            'is_pro' => true,/*Is Premium : Support Premium Version*/
            'pro_url' => 'https://www.cosmoswp.com/pricing/',/*Premium version/Pricing Url*/
            'type' => 'gutentor',/*Optional eg gutentor, elementor or other page builders or type*/
            'author' => __( 'Gutentor', 'text-domain' ),/*Author Name*/
            'keywords' => array( 'woocommerce', 'shop' ),/*Search keyword*/
            'categories' => array( 'woocommerce','multipurpose' ),/*Categories*/
            'template_url' => array(/* Optional for premium theme, you can add your own logic by hook*/
                'content' => 'full-url-path/content.json',/*Full URL Path to content.json*/
                'options' => 'full-url-path/master/options.json',/*Full URL Path to options.json*/
                'widgets' => 'full-url-path/widgets.json'/*Full URL Path to widgets.json*/
            ),
            'screenshot_url' => 'full-url-path/screenshot.png?ver=1.6',/*Full URL Path to demo screenshot image*/
            'demo_url' => 'https://www.demo.cosmoswp.com/',/*Full URL Path to Live Demo*/
            /* Recommended plugin for this demo */
            'plugins' => array(
                array(
                    'name'      => __( 'Gutentor', 'text-domain' ),
                    'slug'      => 'gutentor',
                ),
            )
        ),
        /*and so on ............................*/
   );
   return $demo_lists;
}
add_filter('advanced_import_demo_lists','prefix_demo_import_lists');
`
= Do I need to assign "Front page", "Posts page" and "Menu Locations" after the importer is done or do they automatically assign? =

You don't need to assign them manually, they will be automatically assigned. Theme author also doesn't have to write additional code for it.

= I am a theme author and I would like to customize it for my theme, Can you list hooks available on Advanced Import plugin? =

Here are some important list of filter hooks:

- advanced_import_is_template_available
- advanced_import_template_import_button
- advanced_import_welcome_message
- advanced_import_demo_lists
- advanced_import_is_pro_active
- advanced_import_post_data
- advanced_import_replace_post_ids
- advanced_import_replace_term_ids
- advanced_import_new_options
- advanced_import_sidebars_widgets
- advanced_import_complete_message
- advanced_import_update_option_['option-name']
- advanced_import_update_value_['option-name']
- advanced_import_menu_hook_suffix

Here are some important list of action hooks:

- advanced_import_before_demo_import_screen
- advanced_import_after_demo_import_screen
- advanced_import_before_plugin_screen
- advanced_import_after_plugin_screen
- advanced_import_before_content_screen
- advanced_import_after_content_screen
- advanced_import_before_complete_screen
- advanced_import_after_complete_screen

= I would like to develop a companion/starter sites/toolset plugin for my theme/company, Do you have any starter plugin? =

We don't have any starter plugin but we have developed a plugin for [Acme Themes](https://www.acmethemes.com/). The plugin name is [Acme Demo Setup](https://wordpress.org/plugins/acme-demo-setup/), you can download and view the code of the plugin and develop one for yourself.

= Are there any themes using these plugin? =

Yes, many themes are using this plugin, for an example, you can look on [CosmosWP Theme](https://cosmoswp.com/)

== Screenshots ==

1. Import Main Screen
2. Import Start Popup Message
3. Import Complete Popup Message
4. Import Via File
5. The frontend of CosmosWP after Import
6. The frontend of Opus Blog after Import

== Changelog ==

= 1.3.6 - 2022-04-27 =
* Updated : Reset plugin via ajax
* Updated : Elementor imports in some cases

= 1.3.5 - 2022-02-04 =
* Added :  Plugin Errors Details
* Updated : Latest version test
* Fixed : Reset errors with some plugins

= 1.3.4 - 2022-01-04 =
* Added :  [Elementor global site settings](https://wordpress.org/support/topic/plugin-update-to-process-elementor-global-site-settings/)

= 1.3.3 - 2021-06-15 =
* Added : Plugin info user consent
* Added : 4 new hooks: advanced_import_update_option_['option-name'], advanced_import_update_value_['option-name'] , advanced_import_menu_hook_suffix and advanced_import_current_url
* Updated : Error handling and Error Message
* Updated : recommendedPlugins check if isset
* Fixed : Reset Message

= 1.3.2 - 2021-04-22 =
* Updated : Latest version test

= 1.3.1 - 2021-01-27 =
* Added : Filter list tab active design
* Updated : is_pro check more strictly

= 1.3.0 - 2021-01-26 =
* Added : All, Free and Pro Tab
* Updated : Tested up to 5.6 WordPress
* Updated : PHPCS
* Updated : Some minor design
* Updated : Minor changes
* Fixed : File Type Check

= 1.2.5 - 2020-11-11 =
* Updated : Hook attributes, control each demo import.
Now developer can provide user template kit separately. View Example : [CosmosWP Template kits](https://www.cosmoswp.com/template-kits/)
* Updated : PHPCS
* Updated : Some minor design
* Updated : Minor changes

= 1.2.4 - 2020-06-22 =
* Updated : API Url

= 1.2.3 - 2020-06-22 =
* Added : Shortcode support
* Added : Meta post id support
* Added : Gutentor import support
* Added : Elementor import support
* Added : Post type array order
* Added : Default Post and Page move to trash

= 1.2.2 - 2020-04-06 =
* Updated : Contributors
* Fixed : Some Design Issue

= 1.2.1 - 2020-03-16 =
* Fixed : Premium demo support

= 1.2.0 - 2020-03-12 =
* Added : Premium demo support
* Added : Readme documentation
* Added : Extra layer of security
* Updated : Example File

= 1.0.8 - 2020-02-19 =
* Fixed : Secure reset function

= 1.0.7 - 2020-01-05 =
* Fixed : Script loading when SCRIPT_DEBUG on

= 1.0.6 - 2019-12-02 =
* Fixed : Author check fixed for CosmosWP

= 1.0.5 - 2019-11-25 =
* Added : Supports for CosmosWP and Acme Themes
* Fixed : Isotope loading issue
* Fixed : Author info design issue

= 1.0.4 - 2019-11-10 =
* Added : WooCommerce pages import issues

= 1.0.3 - 2019-10-24 =
* Added : Some Hooks

= 1.0.2 - 2019-09-29 =
* Updated : Some Information
* Fixed : Multisite warning

= 1.0.1 - 2019-09-26 =
* Fixed : Tools => Advanced Import
* Fixed : Minor Changes

= 1.0.0 =
* Initial release.