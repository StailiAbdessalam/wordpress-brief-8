<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/*
 *  Elementor widget for Price Item
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Price extends Widget_Base {
	
	public function get_name() {
		return 'bolby-price';
	}
	
	public function get_title() {
		return __( 'Price Item', 'bolby' );
	}
	
	public function get_icon() {
		return 'eicon-price-table';
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
				'default' => __( 'Basic', 'bolby' ),
			]
		);
		
		$this->add_control(
			'price',
			[
				'label' => __( 'Price', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '9', 'bolby' ),
			]
		);

		$this->add_control(
			'currency',
			[
				'label' => __( 'Currency', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '$', 'bolby' ),
			]
		);
		
		$this->add_control(
			'period',
			[
				'label' => __( 'Period', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Month', 'bolby' ),
			]
		);

		$this->add_control(
			'content_value',
			[
				'label' => __( 'Content', 'bolby' ),
				'type' => Controls_Manager::WYSIWYG,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Get Started', 'bolby' ),
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => __( 'Button Link', 'bolby' ),
				'type' => Controls_Manager::URL,
			]
		);
		
		$this->add_control(
			'recommended',
			[
				'label' => __( 'Recommended?', 'bolby' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'bolby' ),
				'label_off' => __( 'No', 'bolby' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		$target = $settings['button_link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['button_link']['nofollow'] ? ' rel="nofollow"' : '';
		$allowed_html = array(
			'a' => array(
			'href' => array(),
			'title' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'ul' => array(),
			'li' => array(),
			'p' => array(),
		);
		?>
		<div class="price-item bg-white rounded shadow-dark text-center <?php if ( 'yes' === $settings['recommended'] ) { echo 'best'; } ?>">
			<?php if ( 'yes' === $settings['recommended'] ) { ?>
				<span class="badge"><?php echo esc_attr('Recommended', 'bolby'); ?></span>
			<?php } ?>
			<img src="<?php echo esc_attr($settings['image']['url']) ?>" />
			<h2 class="plan"><?php echo esc_attr($settings['title']); ?></h2>
			<?php echo wp_kses($settings['content_value'], $allowed_html) ?>
			<h3 class="price"><em><?php echo esc_attr($settings['currency']); ?></em><?php echo esc_attr($settings['price']); ?><span><?php echo esc_attr($settings['period']) ?></span></h3>
			<a href="<?php echo esc_url($settings['button_link']['url']); ?>" class="btn btn-default" <?php echo $target; echo $nofollow ?>><?php echo esc_attr($settings['button_text']); ?></a>
		</div>
		<?php
	}

}
?>