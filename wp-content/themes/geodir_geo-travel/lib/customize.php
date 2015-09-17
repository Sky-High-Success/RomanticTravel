<?php

/**
 * Customize Background Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
 
add_action( 'customize_register', 'geotravel_customizer' );
function geotravel_customizer(){

	global $wp_customize;

	$wp_customize->add_section( 'geotravel-image', array(
		'title'    		=> __( 'Home Background', 'geotravel' ),
		'description' 	=> __( '<p>Personalize your home page by uploading an image.</p><p> The image used on the demo is <strong>1600 x 870 pixels</strong>.</p>', 'geotravel' ),
		'priority' 		=> 35,
	) );
	
	$wp_customize->add_setting( 'geotravel-home-image', array(
		'default'  => '',
		'type'     => 'option',
	) );
	 
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'home-image',
			array(
				'label' => __( 'Home Image Upload', 'geotravel' ),
				'section'  => 'geotravel-image',
				'settings' => 'geotravel-home-image',
			)
		)
	);
	
}
