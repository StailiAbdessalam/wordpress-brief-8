<?php
namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*
 *  Elementor icons widget
 *  @since 1.0
 */ 
 
class Posts extends Widget_Base {
    
    public function get_name() {
        return 'bolby-posts';
    }
    
    public function get_title() {
        return __( 'Blog Posts', 'bolby' );
    }
    
    public function get_icon() {
        return 'eicon-posts-grid';
    }
    
    public function get_categories() {
		return [ 'bolby-elements' ];
	}
	
	protected function _register_controls() {

		$this->start_controls_section(
		    'content',
		    [
		        'label' => __( 'Blog Posts', 'bolby' )
		    ]
		);

		$this->add_control(
			'category',
			[
				'label' => __( 'Show category label', 'bolby' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'bolby' ),
				'label_off' => __( 'Hide', 'bolby' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'date',
			[
				'label' => __( 'Show date', 'bolby' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'bolby' ),
				'label_off' => __( 'Hide', 'bolby' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'author',
			[
				'label' => __( 'Show author', 'bolby' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'bolby' ),
				'label_off' => __( 'Hide', 'bolby' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		
		$this->end_controls_section();
		
	}
	
	protected function render() {
	    $settings = $this->get_settings();

		?>
		<div class="row blog-wrapper">

		<?php
			$args = array( 'post_type' => 'post', 'posts_per_page' => 3 );
			$query = new \WP_Query( $args );
			if ($query->have_posts()) : 
			while ($query->have_posts()) : $query->the_post(); 

		?>
				
			<div class="col-md-4">
				<div class="blog-item rounded bg-white shadow-dark wow fadeIn">
					<div class="thumb">
						<?php
							if ( 'yes' === $settings['category'] ) {
								$categories = get_the_category();
								if ( ! empty( $categories ) ) {
									echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '"><span class="category">' . esc_attr( $categories[0]->name ) . '</span></a>';
								}
							}
							if ( has_post_thumbnail() ) {
								echo '<a href="'; the_permalink(); echo '">'; the_post_thumbnail(array( 530, 345)); echo '</a>';
							}
						?>
					</div>
					<div class="details">
						<h4 class="my-0 title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<ul class="list-inline meta mb-0 mt-2">
							<?php if ( 'yes' === $settings['date'] ) : ?>
								<li class="list-inline-item"><?php the_time('d F Y'); ?></li>
							<?php endif; ?>
							<?php if ( 'yes' === $settings['author'] ) : ?>
								<li class="list-inline-item"><?php the_author_posts_link(); ?></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>

		<?php endwhile;?>

		</div>

		<?php wp_reset_postdata(); ?>

		<?php else : ?>

		<p><?php esc_attr_e('No more posts.', 'bolby' ); ?></p>

		<?php endif; ?>


        <?php
	}

}

?>