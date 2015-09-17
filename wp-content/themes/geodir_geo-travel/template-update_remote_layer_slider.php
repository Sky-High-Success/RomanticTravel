<?php
/**
 * Template name: UPDATE Remote Layer Slider
 * 
 * The template for displaying Package Taxonomy pages.
 *
 * Used to display archive-type pages for posts with a post format.
 * If you'd like to further customize these Post Format views, you may create a
 * new template file for each specific one.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>
<?php

	if(!simple_authentication(null)){
		exit;
	}
	
	$slider_id = $_POST["slider_id"];
	$slider_name = $_POST["slider_name"];
	$slider_data = stripslashes($_POST["slider_data"]);
	
	global $wpdb;
	$table_name = $wpdb->prefix . constant("LS_DB_TABLE");
	
	$wpdb->query("INSERT INTO $table_name (id, name, data, date_c, date_m) VALUES ('$slider_id', '$slider_name', '{}', '".time()."', '".time()."')
			ON DUPLICATE KEY UPDATE name = '$slider_name', data = '$slider_data', date_m = '".time()."'");
	
	echo "success";
	exit;
?>

