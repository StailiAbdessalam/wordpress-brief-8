<?php /* Template Name: Page One Page */ get_header('onepage'); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<?php the_content(); ?>

    <?php endwhile;?>

	<?php endif; ?>

<?php get_footer(); ?>