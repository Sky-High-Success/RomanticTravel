<?php
/**
 * Template name: GET Remote Travel Package
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
	
	$post_name = $_POST["request_post_name"];
	$post_type = $_POST["request_post_type"];
	
	if(!empty($post_name)){
		
		global $wpdb;
		
		$post_table = $wpdb->prefix.'posts';
		
		$meta_table = $wpdb->prefix.'postmeta';
				
		$id = $wpdb->get_var("SELECT ID FROM $post_table WHERE post_name = '".$post_name."' and post_type = '".$post_type."'");
		if($id){
			
			$meta = $wpdb->get_results( "SELECT * FROM $meta_table WHERE post_id = $id", ARRAY_A );
			
			$result = array("valid" => "1", "post_id" => $id, "meta" => $meta);
		}else{
			$result = array("valid" => "1", "post_id" => "0");
		}
	}	
	else{
		$result = array("valid" => "0", "post_id" => "0");
	}
	
	
	echo json_encode($result);
	exit;
?>
