<?php get_header(); ?>
<?php 
	
	while ( have_posts() ) : the_post();
	// Getting categories
	$categories = get_the_category();
	$separator = ' , ';
	$cat_output = '';
	if ( ! empty( $categories ) ) {
		foreach( $categories as $category ) {
		    $cat_output .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'bolby' ), $category->name ) ) . '">' . esc_attr( $category->name ) . '</a>' . $separator;
		}
	}
	// variables
	$blog_meta = get_theme_mod('blog_meta', true);
	$blog_date = get_theme_mod('blog_date', true);
	$blog_author = get_theme_mod('blog_author', true);
	$blog_category = get_theme_mod('blog_category', true);

	global $blog_meta;
?>

<div class="content">

<div class="row">

	<div class="col-md-<?php if( get_theme_mod('blog_single_sidebar', true) == true ) {echo '8';} else {echo '12';} ?>">

		<div class="blog-single blog-standard shadow-dark">

			<h1 class="my-0"><?php the_title(); ?></h1>
				<?php if(true == $blog_meta) : ?>
					<ul class="list-inline unstyled meta mb-0 mt-3">
						<?php if(true == $blog_date) : ?>
							<li class="list-inline-item"><?php the_time('d F Y'); ?></li>
						<?php endif; ?>
						<?php if(true == $blog_author) : ?>
							<li class="list-inline-item"><?php the_author_posts_link(); ?></li>
						<?php endif; ?>
						<?php if(true == $blog_category) : ?>
							<li class="list-inline-item"><?php echo trim( $cat_output, $separator ); ?></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>

				<!-- blog item -->
				<article id="post-<?php the_ID(); ?>" <?php post_class('is-single'); ?>>

					<?php if ( has_post_thumbnail() ) {
						echo '<div class="thumb-wrapper mt-4">';
							the_post_thumbnail('full');
						echo '</div>';
						} else { ?>
					<?php } ?>
					<div class="clearfix my-4">
						<?php the_content(); 
							bolby_theme_link_pages();
						?>
					</div>

					<footer class="clearfix">
						<div class="tags">
							<?php the_tags('', '', ''); ?>
						</div>
					</footer>
				</article>

				<?php 

					if(true == get_theme_mod('blog_comment', true)) {	
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif; 
					}
					
				?>

		</div>

	</div>

	<?php if( get_theme_mod('blog_single_sidebar', true) == true ) { ?>
            <div class="col-md-4">
                <?php get_sidebar(); ?>
            </div>
        <?php } ?>

</div>

</div>

<?php
	endwhile; // end of the loop. 
?>

<?php get_footer(); ?>