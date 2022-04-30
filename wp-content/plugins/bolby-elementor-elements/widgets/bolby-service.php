<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/*
 *  Elementor widget for Service Item
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Service extends Widget_Base {
	
	public function get_name() {
		return 'bolby-service';
	}
	
	public function get_title() {
		return __( 'Service Item', 'bolby' );
	}
	
	public function get_icon() {
		return 'eicon-gallery-grid';
	}
	
	public function get_categories() {
		return ['bolby-elements'];
	}
	
	protected function _register_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => __( 'Content', 'bolby' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'bolby' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'UI/UX design', 'bolby' ),
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'bolby' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem ipsum dolor sit amet consectetuer adipiscing elit aenean commodo ligula eget.', 'bolby' ),
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label' => __( 'Background color', 'bolby' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#6C6CE5'
			]
		);

		$this->add_control(
			'shadow_style',
			[
				'label' => __( 'Shadow style', 'bolby' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'blue',
				'options' => [
					'blue'  => __( 'Blue', 'bolby' ),
					'yellow' => __( 'Yellow', 'bolby' ),
					'pink' => __( 'Pink', 'bolby' ),
					'dark' => __( 'Dark', 'bolby' ),
				],
			]
		);

		$this->add_control(
			'text_style',
			[
				'label' => __( 'Text style', 'bolby' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light'  => __( 'Light', 'bolby' ),
					'dark' => __( 'Dark', 'bolby' ),
				],
			]
		);
		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'bolby' ),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		?>
		<div class="service-box rounded data-background padding-30 text-center text-<?php echo esc_html($settings['text_style']); ?> shadow-<?php echo esc_html($settings['shadow_style']); ?>" data-color="<?php echo esc_html($settings['bg_color']); ?>">
			<?php 
				if($settings['link']['url'] != ''){
					echo '<a href="'.$settings['link']['url'].'" target="__'.($settings['link']['is_external']? 'blank' : 'self').'">';
				}
				if($settings['image']['url']){
					echo '<img src="' . $settings['image']['url'] . '">'; 
				}
			?>
			<h3 class="mb-3 mt-0"><?php echo esc_attr($settings['title']); ?></h3>
			<p class="mb-0"><?php echo esc_attr($settings['text']); ?></p>
			<?php 
			if($settings['link']['url'] != ''){
					echo '</a>';
				}
			?>
		</div>
		<?php
	}

}
?>