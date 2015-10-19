<?php
/**
 * This file adds the Home Page to the Geo Travel Theme if GeoDirectory default is not being used.
 *
 * @author WP Geo Themes
 * @package Geo Travel
 * @subpackage Customizations
 */

//* Enqueue scripts
add_action( 'wp_enqueue_scripts', 'geotravel_front_page_enqueue_scripts' );
function geotravel_front_page_enqueue_scripts() {
	
	//* Load scripts only if custom background is being used
	if ( ! get_option( 'geotravel-home-image' ) )
		return;

	//* Enqueue Backstretch scripts
	wp_enqueue_script( 'geotravel-backstretch', get_bloginfo( 'stylesheet_directory' ) . '/js/backstretch.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_script( 'geotravel-backstretch-set', get_bloginfo( 'stylesheet_directory' ).'/js/backstretch-set.js' , array( 'jquery', 'geotravel-backstretch' ), '1.0.0' );

	// Template
	wp_enqueue_style ( 'bootstrap3-style', get_stylesheet_directory_uri () . '/css/bootstrap3.min.css', array (), '1.0.0' );
	
	wp_enqueue_style ( 'theme-custom-style', get_stylesheet_directory_uri () . '/css/theme.css', array (
	'bootstrap3-style'
			), '1.0.0' );
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script ( 'bootstrap3-js', get_stylesheet_directory_uri () . '/js/bootstrap3.min.js', array (
	'jquery'
			), '2014-07-18', true );
	
	wp_localize_script( 'geotravel-backstretch-set', 'BackStretchImg', array( 'src' => str_replace( 'http:', '', get_option( 'geotravel-home-image' ) ) ) );
	
	//* Add Geo Travel body class
	add_filter( 'body_class', 'geotravel_body_class' );

}

add_action('genesis_meta','genesis_meta_content');

add_action('genesis_after_footer','genesis_after_footer_modal');

function genesis_meta_content() { ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<?php	
}

function geotravel_body_class( $classes ) {

		$classes[] = 'geotravel-home';
		return $classes;
		
}

//* Add widget support for homepage. If no widgets active, display the default loop.
add_action( 'genesis_meta', 'geotravel_home_genesis_meta' );
function geotravel_home_genesis_meta() {

	if ( is_active_sidebar( 'home-featured-left' ) || is_active_sidebar( 'home-featured-right' ) || is_active_sidebar( 'home-top' ) || is_active_sidebar( 'home-middle' ) || is_active_sidebar( 'home-bottom' ) ) {

		//* Force full-width-content layout setting
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
		
		//* Remove breadcrumbs
		remove_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs' );

		//* Remove the default Genesis loop
		remove_action( 'genesis_loop', 'genesis_do_loop' );
		
		//* Add home featured widget areas
		add_action( 'genesis_after_header', 'geotravel_featured_widgets' );
		
		//* Add home widget areas
		add_action( 'genesis_loop', 'geotravel_home_widgets' );

	}
}


function geotravel_featured_widgets() {
	
	echo '<div class="home-featured full-width"><div class="wrap">';

		genesis_widget_area( 'home-featured-left', array(
			'before' => '<div class="home-featured-left widget-area">',
			'after' => '</div>',
		) );
		
		genesis_widget_area( 'home-featured-right', array(
			'before' => '<div class="home-featured-right widget-area">',
			'after' => '</div>',
		) );
	
	echo '</div></div>';

}

function geotravel_home_widgets() {

	genesis_widget_area( 'home-top', array(
		'before' => '<div class="home-top full-width widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );

	genesis_widget_area( 'home-middle', array(
		'before' => '<div class="home-middle full-width widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );
	
	genesis_widget_area( 'home-bottom', array(
		'before' => '<div class="home-bottom full-width widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );

}

function genesis_internet_after_footer() {
	?>
<script type="text/javascript">
    jQuery(document).ready(function($){

//     	var $tabPane = $('.tab-pane'),
// 	        tabsHeight = $('.nav-tabs').height();
	    
// 	    $tabPane.css({
// 	      height: tabsHeight
// 	    });

     if($("#detect_lg").is(":visible")){
    	 $('#landing_modal').modal('show');

     }	

    });


</script>



<?php
}

genesis();
