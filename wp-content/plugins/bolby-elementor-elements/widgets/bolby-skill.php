<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/*
 *  Elementor widget for Skill Info
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skill extends Widget_Base {
	
	public function get_name() {
		return 'bolby-skill';
	}
	
	public function get_title() {
		return __( 'Skill Info', 'bolby' );
	}
	
	public function get_icon() {
		return 'eicon-skill-bar';
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
			'title',
			[
				'label' => __( 'Title', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'WordPress', 'bolby' ),
				'placeholder' => __( 'Write your skill title.', 'bolby' )
			]
		);
		
		$this->add_control(
			'value',
			[
				'label' => __( 'Value', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '85', 'bolby' ),
			]
		);
		
		$this->add_control(
			'symbol',
			[
				'label' => __( 'Symbol', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '%', 'bolby' ),
			]
		);

		$this->add_control(
			'bar_color',
			[
				'label' => __( 'Progress bar color', 'bolby' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_1,
				],
				'default' => '#FFD15C'
			]
		);

		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		?>
		<!-- skill item -->
		<div class="skill-item">
			<?php if( $settings['title'] || $settings['value'] ) { ?>
			<div class="skill-info clearfix">
				<?php if( $settings['title'] ) { ?>
				<h4 class="float-left mb-3 mt-0"><?php echo esc_attr($settings['title']); ?></h4>
				<?php } ?>
				<?php if( $settings['value'] ) { ?>
				<span class="float-right"><?php echo esc_attr($settings['value']) . esc_attr($settings['symbol']); ?></span>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if( $settings['value'] ) { ?>
			<div class="progress">
				<div class="progress-bar data-background" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr($settings['value']); ?>" data-color="<?php echo esc_html($settings['bar_color']); ?>">
				</div>
			</div>
			<?php } ?>
		</div>
		<?php
	}
}
?>