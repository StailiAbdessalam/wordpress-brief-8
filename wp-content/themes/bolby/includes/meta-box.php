<?php

add_action( 'cmb2_admin_init', 'portfolio_metabox_register' );

function portfolio_metabox_register() {
	$prefix = 'bolby_';

	$portfolio_type = new_cmb2_box( array(
		'id'            => $prefix . 'type_metabox',
		'title'         => esc_attr__( 'Portfolio Type', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$portfolio_type->add_field( array(
		'name'    => esc_attr__('Portfolio type', 'bolby' ),
		'id'      => 'portfolio_type',
		'desc'	  => esc_attr__( 'Select your portfolio type and fill your selected fields. (Note: Make sure leave empty other field boxes)', 'bolby' ),
		'type'    => 'radio_inline',
		'options' => array(
			'image' => esc_attr__( 'Single image', 'bolby' ),
			'content' => esc_attr__( 'Content', 'bolby' ),
			'gallery'   => esc_attr__( 'Gallery', 'bolby' ),
			'video'     => esc_attr__( 'Video', 'bolby' ),
			'soundcloud'     => esc_attr__( 'Soundcloud', 'bolby' ),
			'link'     => esc_attr__( 'Link', 'bolby' ),
		),
		'default' => 'image',
	) );

	$single_type = new_cmb2_box( array(
		'id'            => $prefix . 'single_type_metabox',
		'title'         => esc_attr__( 'Single image', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$single_type->add_field( array(
		'name'    => esc_attr__('Image file', 'bolby' ),
		'desc'    => esc_attr__('Upload an image', 'bolby' ),
		'id'      => 'single_image',
		'type'    => 'file',
		'options' => array(
			'url' => false,
		),
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
				'image/svg'
			),
		),
		'text'    => array(
			'add_upload_file_text' => esc_attr__('Add File', 'bolby' ),
		),
		'preview_size' => 'large',
	) );

	$content_type = new_cmb2_box( array(
		'id'            => $prefix . 'content_type_metabox',
		'title'         => esc_attr__( 'Content', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$content_type->add_field( array(
		'name'    => esc_attr__('Content', 'bolby' ),
		'id'      => 'single_content',
		'type'    => 'wysiwyg',
		'options' => array(),
	) );

	$gallery_type = new_cmb2_box( array(
		'id'            => $prefix . 'gallery_type_metabox',
		'title'         => esc_attr__( 'Gallery', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$gallery_type->add_field( array(
		'name' => esc_attr__( 'Upload images', 'bolby' ),
		'id'   => 'single_gallery',
		'type' => 'file_list',
	) );

	$video_type = new_cmb2_box( array(
		'id'            => $prefix . 'video_type_metabox',
		'title'         => esc_attr__( 'Video', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$video_type->add_field( array(
		'name' => esc_attr__( 'Video URL', 'bolby' ),
		'desc' => esc_attr__( 'Enter a Youtube, Vimeo URL.', 'bolby' ),
		'id'   => 'single_video',
		'type' => 'oembed',
	) );

	$soundcloud_type = new_cmb2_box( array(
		'id'            => $prefix . 'soundcloud_type_metabox',
		'title'         => esc_attr__( 'Soundcloud', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$soundcloud_type->add_field( array(
		'name' => esc_attr__( 'Soundcloud URL', 'bolby' ),
		'desc' => esc_attr__( 'Enter a Soundcloud URL.', 'bolby' ),
		'id'   => 'single_soundcloud',
		'type' => 'oembed',
	) );

	$link_type = new_cmb2_box( array(
		'id'            => $prefix . 'link_type_metabox',
		'title'         => esc_attr__( 'Link', 'bolby' ),
		'object_types'  => array( 'portfolio' ),
	) );

	$link_type->add_field( array(
		'name' => esc_attr__( 'Enter URL', 'bolby' ),
		'id'   => 'single_link',
		'type' => 'text_url',
	) );
}