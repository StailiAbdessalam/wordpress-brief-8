<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

/*
 *  Elementor widget for Testimonial
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Testimonial extends Widget_Base {
	
	public function get_name() {
		return 'bolby-testimonial';
	}
	
	public function get_title() {
		return __( 'Testimonial', 'bolby' );
	}
	
	public function get_icon() {
		return 'eicon-posts-carousel';
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
		
        $repeater = new Repeater();
		
		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'John Doe', 'bolby' ),
			]
		);
		
		$repeater->add_control(
			'position',
			[
				'label' => __( 'Position', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Product designer at Dribbble', 'bolby' ),
			]
		);
		
		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'bolby' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		
		$repeater->add_control(
			'text',
			[
				'label' => __( 'Text', 'bolby' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'I enjoy working with the theme and learn so much. You guys make the process fun and interesting. Good luck!', 'bolby' ),
			]
		);
		
		$this->add_control(
		    'testimonial', 
		    [
		        'label' => __( 'Testimonial', 'bolby' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
	        ]
	    );

		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		?>
		<div class="testimonials-wrapper">
		    <?php foreach($settings['testimonial'] as $item) : ?>		
				<div class="testimonial-item text-center mx-auto">
					<div class="thumb mb-3 mx-auto">
						<img src="<?php echo esc_url($item['image']['url']); ?>" alt="" />						
					</div>
					<h4 class="mt-3 mb-0"><?php echo esc_attr($item['title']); ?></h4>
					<span class="subtitle"><?php echo esc_attr($item['position']); ?></span>
					<div class="bg-white padding-30 shadow-dark rounded triangle-top position-relative mt-4">
						<p class="mb-0"><?php echo esc_attr($item['text']); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

}
?>