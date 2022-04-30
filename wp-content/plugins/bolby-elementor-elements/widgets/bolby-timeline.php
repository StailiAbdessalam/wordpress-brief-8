<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

/*
 *  Elementor widget for Timeline
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Timeline extends Widget_Base {
	
	public function get_name() {
		return 'bolby-timeline';
	}
	
	public function get_title() {
		return __( 'Timeline', 'bolby' );
	}
	
	public function get_icon() {
		return 'eicon-time-line';
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
			'year',
			[
				'label' => __( 'Year', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '2018-2015', 'bolby' ),
			]
		);
		
		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Academic Degree', 'bolby' ),
			]
		);

		$repeater->add_control(
			'content',
			[
				'label' => __( 'Content', 'bolby' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Lorem ipsum dolor sit amet consectetuer adipiscing elit aenean commodo ligula eget dolor aenean massa.', 'bolby' ),
			]
		);
		
		$this->add_control(
		    'timeline',
		    [
		        'label' => __( 'Timeline', 'bolby' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
	        ]
		);
		
		$this->add_control(
			'icon_style',
			[
				'label' => __( 'Icon style', 'bolby' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'edu',
				'options' => [
					'edu'  => __( 'Education', 'bolby' ),
					'exp' => __( 'Experience', 'bolby' ),
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		?>
		<div class="timeline <?php echo esc_attr($settings['icon_style']); ?> bg-white rounded shadow-dark padding-30 overflow-hidden">

			<?php foreach($settings['timeline'] as $time) : ?>
			<div class="timeline-container wow fadeInUp">
				<div class="content">
					<?php if( $time['year'] ) { ?>
						<span class="time"><?php echo esc_attr($time['year']); ?></span>
					<?php } ?>

					<?php if( $time['title'] ) { ?>
						<h3 class="title"><?php echo esc_attr($time['title']); ?></h3>
					<?php } ?>
					
					<?php if( $time['content'] ) { ?>
						<p><?php echo esc_attr($time['content']); ?></p>
					<?php } ?>
				</div>
			</div>
			<?php endforeach; ?>

			<!-- main line -->
			<span class="line"></span>

		</div>
		<?php
	}

}
?>