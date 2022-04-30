<?php /* Template Name: Page Blog */ get_header(); ?>

<div class="spacer" data-height="70"></div>

<h1 class="mt-0 mb-5 archive-header"><?php the_title(); ?></h1>

<div class="row">

	<div class="col-md-<?php if( get_theme_mod('blog_sidebar', true) == true ) {echo '8';} else {echo '12';} ?>">

		<?php
			// variables
			$blog_meta = get_theme_mod('blog_meta', true);
			$blog_date = get_theme_mod('blog_date', true);
			$blog_author = get_theme_mod('blog_author', true);
			$blog_category = get_theme_mod('blog_category', true);
			$blog_except = get_theme_mod('except');

		?>

		<?php
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';
			$args = array( 'post_type' => 'post', 'paged' => $paged );
			$the_query = new WP_Query( $args );
			if ($the_query->have_posts()) : 
			while ($the_query->have_posts()) : $the_query->the_post(); 

		?>

			<!-- blog item -->
			<div id="post-<?php the_ID(); ?>" <?php post_class('blog-standard bg-white padding-30 rounded shadow-dark'); ?>>

				<?php if ( has_post_thumbnail() ) {
					echo '<div class="thumb mb-4">';
						echo '<a href="'; the_permalink(); echo '">'; the_post_thumbnail('large'); echo '</a>';
					echo '</div>';
				} ?>

				<h3 class="mt-0 title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

				<?php if(true == $blog_meta) : ?>
					<ul class="list-inline unstyled meta mb-0 mt-3">
						<?php if(true == $blog_date) : ?>
							<li class="list-inline-item"><?php the_time('d F Y'); ?></li>
						<?php endif; ?>
						<?php if(true == $blog_author) : ?>
							<li class="list-inline-item"><?php the_author_posts_link(); ?></li>
						<?php endif; ?>
						<?php if(true == $blog_category) {
							$categories = get_the_category();
						
							if ( ! empty( $categories ) ) {
								echo '<li class="list-inline-item"><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
							} 
						} ?>
					</ul>
				<?php endif; ?>

				<p class="mt-3 mb-0"><?php
				$content = get_the_content();
				if($blog_except) {
					$trimmed_content = wp_trim_words( $content, $blog_except );
					echo esc_attr($trimmed_content);
				} else {
					$trimmed_content_default = wp_trim_words( $content, 30 );
					echo esc_attr($trimmed_content_default);
				} ?></p>

				<a href="<?php the_permalink(); ?>" class="btn btn-default btn-animation mt-3"><?php esc_attr_e('Read more', 'bolby'); ?></a>
				
			</div>

		<?php endwhile;?>

		<?php wp_reset_postdata(); ?>

		<?php else : ?>

		<p><?php esc_attr_e('No more posts.', 'bolby' ); ?></p>

		<?php endif; ?>

		<?php bolby_theme_pagination_static(); ?>

	</div>

	<?php if( get_theme_mod('blog_sidebar', true) == true ) { ?>
		<div class="col-md-4">
			<?php get_sidebar(); ?>
		</div>
	<?php } ?>

</div>

<div class="spacer" data-height="70"></div>

<?php get_footer(); ?>