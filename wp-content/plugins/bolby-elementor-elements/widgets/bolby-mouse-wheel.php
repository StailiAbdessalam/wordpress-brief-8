<?php
namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*
 *  Elementor icons widget
 *  @since 1.0
 */ 
 
class Mouse_Wheel extends Widget_Base {
    
    public function get_name() {
        return 'bolby-mouse-wheel';
    }
    
    public function get_title() {
        return __( 'Mouse Wheel', 'bolby' );
    }
    
    public function get_icon() {
        return 'eicon-scroll';
    }
    
    public function get_categories() {
		return [ 'bolby-elements' ];
	}
	
	protected function _register_controls() {

		$this->start_controls_section(
		    'category_config',
		    [
		        'label' => __( 'Mouse Wheel', 'bolby' ),
		        'tab' => Controls_Manager::TAB_CONTENT,
		    ]
		);

		$this->add_control(
			'section_id',
			[
				'label' => __( 'Section ID', 'bolby' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'dark',
			[
				'label' => __( 'Color dark', 'bolby' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
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

	    $this->add_render_attribute( 'wrapper', 'class', 'bolby-mouse-wheel-wrapper' );

		?>
        <div class="scroll-down <?php if ( 'yes' === $settings['dark'] ) { echo 'light'; } ?>" <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <a href="<?php echo esc_url($settings['section_id']) ?>" class="mouse-wrapper">
                <span><?php echo esc_attr('Scroll Down', 'bolby'); ?></span>
                <span class="mouse">
                    <span class="wheel"></span>
                </span>
            </a>
        </div>

        <?php
	}

}

?>