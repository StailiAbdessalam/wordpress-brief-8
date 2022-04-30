<?php get_header(); ?>

    <div class="spacer" data-height="70"></div>

    <h1 class="mt-0 mb-5 archive-header"><?php single_cat_title(); ?></h1>

    <div class="row">

        <?php get_template_part( 'loop' ); ?>

        <?php if( get_theme_mod('blog_sidebar', true) == true ) { ?>
            <div class="col-md-4">
                <?php get_sidebar(); ?>
            </div>
        <?php } ?>

    </div>

    <div class="spacer" data-height="70"></div>

<?php get_footer(); ?>