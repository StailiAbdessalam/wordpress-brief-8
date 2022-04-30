<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;

/*
 *  Elementor widget for Clients
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Clients extends Widget_Base {
	
	public function get_name() {
		return 'bolby-clients';
	}
	
	public function get_title() {
		return __( 'Clients', 'bolby' );
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
			'image',
			[
				'label' => __( 'Image', 'bolby' ),
				'type' => Controls_Manager::MEDIA
			]
		);
		
		$this->add_control(
			'clients',
			[
				'label' => __( 'Clients items', 'bolby' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		
		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings();
		if( $settings['clients'] ) { 
		?>
		<!-- clients wrapper -->
    	<div class="row">
    		<?php foreach ( $settings['clients'] as $item ) : if( $item['image']['url'] ) : ?>
    		<div class="col-md-3 col-6">
				<div class="client-item">
					<div class="inner">
						<img src="<?php echo esc_url($item['image']['url']); ?>" alt="" />
					</div>
				</div>
    		</div>
    		<?php endif; endforeach; ?>
    	</div>
		<?php
		}
	}

}
?>