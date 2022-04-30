<?php
namespace BolbyBuilder;

use BolbyBuilder\Widgets\Button;
use BolbyBuilder\Widgets\Clients;
use BolbyBuilder\Widgets\Contact;
use BolbyBuilder\Widgets\Icon_Counterup;
use BolbyBuilder\Widgets\Portfolio;
use BolbyBuilder\Widgets\Posts;
use BolbyBuilder\Widgets\Price;
use BolbyBuilder\Widgets\Service;
use BolbyBuilder\Widgets\Skill;
use BolbyBuilder\Widgets\Testimonial;
use BolbyBuilder\Widgets\Timeline;
use BolbyBuilder\Widgets\Title;
use BolbyBuilder\Widgets\Shapes;
use BolbyBuilder\Widgets\Mouse_Wheel;
use BolbyBuilder\Widgets\Social_Icons;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'enqueue_bolby_elementor_icons' ] );
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		require __DIR__ . '/widgets/bolby-button.php';
		require __DIR__ . '/widgets/bolby-clients.php';
		require __DIR__ . '/widgets/bolby-contact.php';
		require __DIR__ . '/widgets/bolby-icon-counterup.php';
		require __DIR__ . '/widgets/bolby-portfolio.php';
		require __DIR__ . '/widgets/bolby-posts.php';
		require __DIR__ . '/widgets/bolby-price.php';
		require __DIR__ . '/widgets/bolby-service.php';
		require __DIR__ . '/widgets/bolby-skill.php';
		require __DIR__ . '/widgets/bolby-testimonial.php';
		require __DIR__ . '/widgets/bolby-timeline.php';
		require __DIR__ . '/widgets/bolby-title.php';
		require __DIR__ . '/widgets/bolby-shapes.php';
		require __DIR__ . '/widgets/bolby-mouse-wheel.php';
		require __DIR__ . '/widgets/bolby-social-icons.php';
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Button() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Clients() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Contact() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Icon_Counterup() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Portfolio() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Posts() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Price() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Service() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Skill() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Testimonial() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Timeline() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Title() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Shapes() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Mouse_Wheel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Social_Icons() );
	}
	
	public function init_controls() {
	}
	
	public function register_controls() {
	}


	public function enqueue_bolby_elementor_icons() {

		wp_register_style( 'bolby-simple-line-icons', plugins_url( 'css/simple-line-icons.css', __FILE__ ) );
		wp_enqueue_style( 'bolby-simple-line-icons' );
		
	}
}

new Plugin();