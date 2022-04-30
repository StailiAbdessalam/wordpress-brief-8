<?php get_header(); ?>

	<div class="spacer" data-height="70"></div>

	<div class="row">

	<div class="col-md-8">
		
	<section class="padding-30 shadow-dark bg-white rounded">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>  

		<h1 class="mt-0 mb-4"><?php the_title(); ?></h1>

			<?php the_content(); ?>

			<?php bolby_theme_link_pages(); ?>

			<?php endwhile;?>

			<?php else : ?>

			<p><?php esc_attr_e( 'No entry founds.', 'bolby' ); ?></p>   

			<?php endif; ?>
			
		<?php 

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif; 
			
		?>

	</section>

	</div>

	<div class="col-md-4">
		<?php get_sidebar(); ?>
	</div>

	</div>

	<div class="spacer" data-height="70"></div>

<?php get_footer(); ?>