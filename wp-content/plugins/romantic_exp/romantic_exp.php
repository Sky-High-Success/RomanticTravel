<?php
/*
 * Plugin Name: Romantic Experience
 * Plugin URI: http://romantic_exp.me
 * Description: Core Romantic Experience App Plugin
 * Version: 0.5
 * Author: Jack Cheng, Boris
 */

// version constant
define ( "RD_VERSION", "0.1" );
define ( "PMPRO_NETWORK_MANAGE_SITES_SLUG", "manage-sites" );
/*
 * Includes
 */
define ( "RD_DIR", dirname ( __FILE__ ) );
// require_once (RD_DIR . "/includes/functions.php");

// misc functions used by the plugin
// require_once(SP_DIR . "/scheduled/crons.php"); //crons for expiring members, sending expiration emails, etc
// //Loading Classes
// require_once(SP_DIR . "/classes/class.SPClass.php"); //class for Class
// require_once(SP_DIR . "/classes/class.SPAssignment.php"); //class for Assignments
// require_once(SP_DIR . "/classes/class.SPSubmission.php"); //class for Assignments
// require_once(SP_DIR . "/classes/class.SPStudent.php"); //class for Student
// require_once(SP_DIR . "/classes/class.SPTeacher.php"); //class for Teacher
// require_once(SP_DIR . "/classes/class.SPSchool.php"); //class for School
// Loading Page Templates
// require_once(SP_DIR . "/pages/my_classes.php");
// require_once(SP_DIR . "/pages/edit_class.php");
// require_once(SP_DIR . "/pages/edit_assignment.php");

// setup the database for the Romantic Directory app
function rd_setupDB() {
	global $wpdb;
	
	// shortcuts for DB tables
	$wpdb->listing_meta = $wpdb->prefix . 'listing_meta';
	$wpdb->regions = $wpdb->prefix.'regions';
	$wpdb->region_suburb_relationships = $wpdb->prefix.'region_suburb_relationships';
	$wpdb->travel_enquiry_booking = $wpdb->prefix.'travel_enquiry_booking';
	
	$db_version = get_option ( 'rd_db_version', 0 );

	$new_db_version = '2.1';
	
	if ($db_version != $new_db_version) {
		
		$db_version = $new_db_version;
		update_option ( 'rd_db_version', $new_db_version );

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';		
		global $wpdb;
		
		$sqlQuery4 = "CREATE TABLE IF NOT EXISTS `" . $wpdb->travel_enquiry_booking . "`(
`id` int(11) NOT NULL AUTO_INCREMENT,
  `First Name` varchar(255) DEFAULT NULL,
  `Last Name` varchar(255) DEFAULT NULL,
  `City Depart` varchar(255) DEFAULT NULL,
  `Departure Date` date DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Package` varchar(255) DEFAULT NULL,
  `Promo Code` varchar(255) DEFAULT NULL,
  `Honeymoon` tinyint(1) DEFAULT NULL,
  `Flight` tinyint(1) DEFAULT NULL,
  `Message` varchar(255) DEFAULT NULL,
  `category` ENUM('enquiry','booking') NOT NULL DEFAULT 'enquiry',
  `Enquiry Date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`Departure Date`)
		
) ENGINE=InnoDB;";
		
		dbDelta ( $sqlQuery4 );

	}
}

register_activation_hook ( __FILE__, 'rd_setupDB' );
function rd_create_posttype() {
	
	// Register Travel Package taxonomy and CPT
	
	$taxonomy_labels = array(
		'name'              => _x( 'Package Taxonomies', 'taxonomy general name' ),
		'singular_name'     => _x( 'Package Taxonomy', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Package Taxonomy' ),
		'all_items'         => __( 'All Package Taxonomy' ),
		'parent_item'       => __( 'Parent Package Taxonomy' ),
		'parent_item_colon' => __( 'Parent Package Taxonomy:' ),
		'edit_item'         => __( 'Edit Package Taxonomy' ),
		'update_item'       => __( 'Update Package Taxonomy' ),
		'add_new_item'      => __( 'Add New Package Taxonomy' ),
		'new_item_name'     => __( 'New Package Taxonomy Name' ),
		'menu_name'         => __( 'Package Taxonomy' ),
	);
	
	register_taxonomy( 'package_taxonomy', 'travel_package',
					 array(
							'hierarchical' => true,
							'labels' => $taxonomy_labels,
							'show_ui'  => true,
							'show_admin_column' => true,
							'query_var'  => true,
							'rewrite' => array( 
							    'slug' => 'taxonomy',
							    //'hierarchical' => true
							),
					 ) 
	);
	
	// Travel Package CPT
	register_post_type ( 'travel_package',
	// CPT Options
			array (
					'labels' => array (
							'name' => __ ( 'Travel Packages' ),
							'singular_name' => __ ( 'Travel Package' ),
							'add_new_item' => __ ( 'Add New Travel Package' ),
							'edit_item' => __ ( 'Edit Travel Package' ),
							'new_item' => __ ( 'New Travel Package' ),
							'view_item' => __ ( 'View Travel Package' ),
							'search_items' => __ ( 'Search Travel Package' ),
							'not_found' => __ ( 'No Travel Packages found' ),
							'not_found_in_trash' => __ ( 'No Travel Packages found in Trash' )
					),
					'description' => "Romantic Travel package data type",
					'public' => true,
					'has_archive' => true,
					'rewrite' => array (
						'slug' => 'packages'
					),
					'taxonomies' => array (
						'package_taxonomy',
					),
					'capability_type' => 'page',
					'supports' => array('title')
	) );
	
	flush_rewrite_rules();
}
// Hooking up our function to plugin setup
add_action ( 'init', 'rd_create_posttype' );


// register_activation_hook ( __FILE__, 'rd_create_posttype' );
function rd_import_data() {
	$import_version = get_option ( 'rd_import_version', 0 );
	
	$new_import_version = '7.8';
	
	if ($import_version != $new_import_version) {
		

		$import_version = $new_import_version;
		update_option ( 'rd_import_version', $new_import_version );
		
		rd_import_data_execution ();
		
	}
}
function rd_import_data_execution() {
	global $wpdb;
	
	$listingdrafts = $wpdb->get_results ( "
			SELECT ID as `id`, `Business name` as `business_name`, `Contact Name` as `contact_name`, `Address` as `address`, `Suburb` as `suburb`, `State` as `state`, `Post code` as `post_code`, `Phone Number` as `phone_number`, `Email Address` as `email_address`, `Website Address` as `website_address`
			FROM beauty_salons where (id>1600) and (id<=2000)
			" );
	
	// retrieve each raw record from original romantic table
	foreach ( $listingdrafts as $obj ) {
		
		$listing_post = array (
				'post_title' => $obj->business_name,
				'post_content' => "",
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type' => 'listing',
				'post_category' => array (
						50 
				) 
		);
		// insert each listing as a post
		$post_id = wp_insert_post ( $listing_post );
		error_log ( $post_id );
		
		// insert listing meta for each listing post in separate table
		$wpdb->insert ( $wpdb->prefix . 'listing_meta', array (
				'post_id' => $post_id,
				'Street Address' => $obj->address,
				'Suburb' => $obj->suburb,
				'State' => $obj->state,
				'Post Code' => $obj->post_code,
				'Phone Number' => $obj->phone_number,
				//'Region' => $obj->region,
				'Email Address' => $obj->email_address,
				'Website Address' => $obj->website_address 
		), array (
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				//'%s',
				'%s',
				'%s' 
		) );
	}
}

add_action ( 'init', 'rd_import_data' );
// register_activation_hook ( __FILE__, 'rd_import_data' );
function rd_delete_data() {
	
	
	$args = array('post_type' => 'listing',);
	
	
	$query1 = new WP_Query( $args );
	
	// The Loop
	while ( $query1->have_posts() ) {
		$query1->the_post();
		
		global $post;
		error_log($post->ID);
		wp_delete_post ( $post->ID, true );
	}
	
	
}


//add_action ( 'init', 'rd_delete_data' );


