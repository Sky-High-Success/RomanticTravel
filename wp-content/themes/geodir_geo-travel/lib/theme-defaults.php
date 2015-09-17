<?php

//* Geo Travel Theme Setting Defaults
add_filter( 'genesis_theme_settings_defaults', 'geotravel_theme_defaults' );
function geotravel_theme_defaults( $defaults ) {

	$defaults['blog_cat_num']              = 6;	
	$defaults['content_archive']           = 'full';
	$defaults['content_archive_limit']     = 0;
	$defaults['content_archive_thumbnail'] = 0;
	$defaults['posts_nav']                 = 'numeric';
	$defaults['site_layout']               = 'content-sidebar';

	return $defaults;

}

//* Geo Travel Theme Setup
add_action( 'after_switch_theme', 'geotravel_theme_setting_defaults' );
function geotravel_theme_setting_defaults() {

	_genesis_update_settings( array(
		'blog_cat_num'              => 6,	
		'content_archive'           => 'full',
		'content_archive_limit'     => 0,
		'content_archive_thumbnail' => 0,
		'posts_nav'                 => 'numeric',
		'site_layout'               => 'content-sidebar',
	) );

	update_option( 'posts_per_page', 6 );

}
