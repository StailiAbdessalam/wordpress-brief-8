<?php

// PORTFOLIO POST TYPE
add_action( 'init', 'bolby_portfolio_posttype' );
function bolby_portfolio_posttype() {
  register_post_type( 'portfolio',
    array(
      'labels' => array(

        'name' => __( 'Portfolio' , 'bolby' ),
        'singular_name' => __( 'Portfolio' , 'bolby' ),
        'menu_name'           => __( 'Portfolio' , 'bolby' ),
            'parent_item_colon'   => __( 'Parent Portfolio' , 'bolby' ),
            'all_items'           => __( 'All Portfolio' , 'bolby' ),
            'view_item'           => __( 'View Portfolio' , 'bolby' ),
            'add_new_item'        => __( 'Add Portfolio' , 'bolby' ),
            'add_new'             => __( 'Add New Portfolio' , 'bolby' ),
            'edit_item'           => __( 'Edit Portfolio' , 'bolby' ),
            'update_item'         => __( 'Update Portfolio' , 'bolby' ),
            'search_items'        => __( 'Search Portfolio' , 'bolby' ),
            'not_found'           => __( 'Not Found' , 'bolby' ),
            'not_found_in_trash'  => __( 'Not Found in Trash' , 'bolby' ),

      ),

      'public' => true,
      'menu_position'       => 5,
      'has_archive' => true,
      'rewrite' => array('slug' => 'portfolio'),
      'supports'           => array( 'title', 'thumbnail' ),
      'menu_icon'   => 'dashicons-schedule'

    )
  );
}

add_action( 'init', 'create_categories_hierarchical_taxonomy', 0 );

function create_categories_hierarchical_taxonomy() {

// Add new taxonomy, make it hierarchical like categories

  $labels = array(
    'name' => _x( 'Category', 'taxonomy general name' , 'bolby' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name' , 'bolby' ),
    'search_items' =>  __( 'Search Category' , 'bolby' ),
    'all_items' => __( 'All Category' , 'bolby' ),
    'parent_item' => __( 'Parent Category' , 'bolby' ),
    'parent_item_colon' => __( 'Parent Category:' , 'bolby' ),
    'edit_item' => __( 'Edit Category' , 'bolby' ), 
    'update_item' => __( 'Update Category' , 'bolby' ),
    'add_new_item' => __( 'Add Category' , 'bolby' ),
    'new_item_name' => __( 'Category Name' , 'bolby' ),
    'menu_name' => __( 'Category' , 'bolby' ),
  );    

// Register the taxonomy

  register_taxonomy('portfolio_categories',array('portfolio'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'category' ),
  ));

}
?>