<?php

namespace BolbyBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/*
 *  Elementor widget for Portfolio
 *  @since 1.0
 */ 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!function_exists('class_names'))   {
	function class_names() {
		$portfolio_type = get_post_meta( get_the_ID(), 'portfolio_type', true );

		if( $portfolio_type == 'image' ) {
			echo 'image';
		} elseif ( $portfolio_type == 'content' ) {
			echo 'work-content';
		} elseif ( $portfolio_type == 'gallery' ) {
			echo 'gallery-link';
		} elseif ( $portfolio_type == 'video' ) {
			echo 'work-video';
		} elseif ( $portfolio_type == 'soundcloud' ) {
			echo 'work-video';
		} else {
			echo 'empty';
		}
	}
}

if (!function_exists('portfolio_urls'))   {
	function portfolio_urls() {
		$portfolio_type = get_post_meta( get_the_ID(), 'portfolio_type', true );
		$single_image = wp_get_attachment_image_url( get_post_meta( get_the_ID(), 'single_image_id', 1 ), 'full' );
		$single_video = get_post_meta( get_the_ID(), 'single_video', 1 );
		$single_soundcloud = get_post_meta( get_the_ID(), 'single_soundcloud', 1 );
		$single_link = get_post_meta( get_the_ID(), 'single_link', 1 );

		if( $portfolio_type == 'image' ) {
			echo $single_image;
		} elseif( $portfolio_type == 'video' ) {
			echo $single_video;
		} elseif( $portfolio_type == 'soundcloud' ) {
			echo $single_soundcloud;
		} elseif( $portfolio_type == 'content' ) {
			echo '#content-' . get_the_ID();
		} elseif( $portfolio_type == 'gallery' ) {
			echo '#gallery-' . get_the_ID();
		} elseif( $portfolio_type == 'link' ) {
			echo esc_url($single_link);
		} else {
			echo 'empty';
		}
	}
}

if (!function_exists('bolby_gallery_output_file_list'))   {
	function bolby_gallery_output_file_list( $file_list_meta_key, $img_size = 'medium' ) {

		$files = get_post_meta( get_the_ID(), $file_list_meta_key, 1 );

		foreach ( (array) $files as $attachment_id => $attachment_url ) {
			echo '<a href="';
			echo wp_get_attachment_image_url( $attachment_id, $img_size );
			echo '"></a>';
		}
	}
}

class Portfolio extends Widget_Base {
	
	public function get_name() {
		return 'bolby-portfolio';
	}
	
	public function get_title() {
		return __( 'Portfolio', 'bolby' );
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
			'count',
			[
				'label' => __( 'Post count', 'bolby' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'default' => 6,
			]
		);

		$this->add_control(
			'filter',
			[
				'label' => __( 'Show filter', 'bolby' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'bolby' ),
				'label_off' => __( 'Hide', 'bolby' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'load_more',
			[
				'label' => __( 'Load more', 'bolby' ),
				'type' => Controls_Manager::SWITCHER,
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
		global $paged;
        if ( is_front_page()){
			$paged = (get_query_var('page')) ? get_query_var('page') : 1;
		} else {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		}
	    
	    $args = array(
            'post_type' => 'portfolio',
            'posts_per_page' => $settings['count'],
            'ignore_sticky_posts' => true,
            'paged' => $paged,
        );

	    $cat_array = '';
	    $cat_mobile_array = '';
		$terms = get_terms(array('taxonomy'=>'portfolio_categories'));
		
		foreach($terms as $term) {
		    $cat_array .= '<li class="list-inline-item" data-filter=".'. $term->slug .'">'. $term->name .'</li>';
		    $cat_mobile_array .= '<option value=".'. $term->slug .'" data-filter=".'. $term->slug .'">'. $term->name .'</option>';
		}
		
		$portfolio_filter = '
			<ul class="portfolio-filter list-inline wow fadeInUp">
                <li class="current list-inline-item" data-filter="*">'. esc_attr__("Everything", "bolby") .'</li>
                '. $cat_array .'
			</ul>
            <div class="pf-filter-wrapper">
				<select class="portfolio-filter-mobile">
					<option value="*">'. esc_attr__("All Projects", "bolby") .'</option>
					'. $cat_mobile_array .'
				</select>
			</div>
        ';

		if ( 'yes' === $settings['filter'] ) {
			echo $portfolio_filter;
		} 

		?>

		<div class="row portfolio-wrapper">
		<?php
        $posts_query = new \WP_Query($args);
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();

        	$term_names = '';
        	$term_slugs = '';
			$terms = wp_get_post_terms(get_the_ID(), 'portfolio_categories');
	        foreach ($terms as $term){
	            $term_name = '<em>'.$term->name.'</em>';
	            $term_slug = $term->slug;
	            $term_slugs .= "$term_slug ";
				$term_names .= "$term_name";
			}

			$single_content = wpautop( get_post_meta( get_the_ID(), 'single_content', true ) );

			$allowed_html_terms = array(
				'em' => array()
			);

		?>
			<div class="col-md-4 col-sm-6 grid-item <?php echo esc_html($term_slugs); ?>">
				<a href="<?php portfolio_urls(); ?>" class="<?php class_names(); ?>" <?php if(get_post_meta( get_the_ID(), 'portfolio_type', true ) == 'link') {echo 'target="_blank"';} ?>>
					<div class="portfolio-item rounded shadow-dark">
						<div class="details">
							<span class="term"><?php echo wp_kses($term_names, $allowed_html_terms); ?></span>
							<h4 class="title"><?php the_title(); ?></h4>
							<span class="more-button">
								<?php
									$portfolio_type = get_post_meta( get_the_ID(), 'portfolio_type', true );

									if( $portfolio_type == 'image' ) {
										echo '<i class="icon-magnifier-add"></i>';
									} elseif ( $portfolio_type == 'content' ) {
										echo '<i class="icon-options"></i>';
									} elseif ( $portfolio_type == 'gallery' ) {
										echo '<i class="icon-picture"></i>';
									} elseif ( $portfolio_type == 'video' ) {
										echo '<i class="icon-camrecorder"></i>';
									} elseif ( $portfolio_type == 'soundcloud' ) {
										echo '<i class="icon-music-tone-alt"></i>';
									} elseif ( $portfolio_type == 'link' ) {
										echo '<i class="icon-link"></i>';
									}
								?>
							</span>
						</div>
						<div class="thumb">
							<?php echo get_the_post_thumbnail( get_the_ID(), array( 560, 455) ); ?>
							<div class="mask"></div>
						</div>
					</div>
				</a>
				<?php
					$portfolio_type = get_post_meta( get_the_ID(), 'portfolio_type', true ); 
					if( $portfolio_type == 'content' ) : 
				?>
					<div id="content-<?php echo get_the_ID() ?>" class="white-popup zoom-anim-dialog mfp-hide">
						<?php echo get_the_post_thumbnail( get_the_ID(), 'full' ); ?>
						<h2><?php the_title(); ?></h2>
						<?php echo $single_content; ?>
					</div>
				<?php endif; ?>
				<?php
					$portfolio_type = get_post_meta( get_the_ID(), 'portfolio_type', true );
					if( $portfolio_type == 'gallery' ) : 
				?>
					<div id="gallery-<?php echo get_the_ID() ?>" class="gallery mfp-hide">
						<?php bolby_gallery_output_file_list( 'single_gallery', 'full' ); ?>
					</div>
				<?php endif; ?>
			</div>

		<?php } ?>
			
		</div>

		<?php

		wp_reset_postdata();

	    $pagination = '';

	    if ( 'yes' === $settings['load_more'] ) {
		    $pagination .= ''. bolby_portfolio_pagination($posts_query). '';
		}

	    echo $pagination;
	}
	

}
?>