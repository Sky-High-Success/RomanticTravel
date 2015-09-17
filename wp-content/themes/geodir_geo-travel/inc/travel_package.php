<?php

// Customize column
add_action( 'manage_travel_package_posts_custom_column' , 'custom_travel_package_column', 10, 2 );

function custom_travel_package_column( $column, $post_id ) {
	switch ( $column ) {

		case 'package_excerpt' :
			echo "";
			break;

		case 'package_pricing' :
			echo get_post_meta($post_id, 'package_pricing', true);
			break;

	}
}

function set_travel_package_columns($defaults) {
	unset($defaults["ratings"]);
	
	$defaults["title"] = 'Package Name';

    $defaults['package_pricing'] = 'Package Pricing';
    return $defaults;
}

add_filter( 'manage_travel_package_posts_columns', 'set_travel_package_columns', 10);

// change default title filter
function change_default_title( $title ){
	$screen = get_current_screen();

	if  ( $screen->post_type == 'travel_package' ) {
		return 'Enter Travel Package Name';
	}else{
		return $title;
	}
}

add_filter( 'enter_title_here', 'change_default_title' );


// Package Pricing box
add_action( 'add_meta_boxes', 'list_package_pricing' );
function list_package_pricing() {
		
	add_meta_box(
	'list_package1_pricing',
	__( 'Package Pricing', 'myplugin_textdomain' ),
	'list_package_pricing_content',
	'travel_package',
	'normal',
	'high'
			);
	
}

function list_package_pricing_content( $post ) {
	
	wp_nonce_field( 'travel_package_meta_box1', 'travel_package_meta_box_nonce1' );
	$list_package_pricing = get_post_meta($post->ID, 'package_pricing', true);

	echo '<label for="list_package_pricing"></label>';
	echo '<input type="text" id="list_package_pricing" name="list_package_pricing" size="100" placeholder="Enter package pricing here" value="';
	echo $list_package_pricing;
	echo '">';

}

add_action( 'save_post', 'list_package_pricing_save' );
function list_package_pricing_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce1'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce1'], 'travel_package_meta_box1' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_pricing"])){
		$list_package_pricing = $_POST['list_package_pricing'];
		update_post_meta($post_id, 'package_pricing', $list_package_pricing);
	}
}

// Package Discount box
add_action( 'add_meta_boxes', 'list_package_discount' );
function list_package_discount() {

	add_meta_box(
	'list_package2_discount',
	__( 'Package Discount', 'myplugin_textdomain' ),
	'list_package_discount_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_discount_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box21', 'travel_package_meta_box_nonce21' );
	$list_package_discount = get_post_meta($post->ID, 'package_discount', true);

	echo '<label for="list_package_discount"></label>';
	echo '<input type="text" id="list_package_discount" name="list_package_discount" size="100" placeholder="Enter package discount in percentage here" value="';
	echo $list_package_discount;
	echo '">';

}

add_action( 'save_post', 'list_package_discount_save' );
function list_package_discount_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce21'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce21'], 'travel_package_meta_box21' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_discount"])){
		$list_package_discount = $_POST['list_package_discount'];
		update_post_meta($post_id, 'package_discount', $list_package_discount);
	}
}

// Package Excerpt box
add_action( 'add_meta_boxes', 'list_package_excerpt' );
function list_package_excerpt() {
	add_meta_box(
	'list_package2_excerpt',
	__( 'Package Excerpt', 'myplugin_textdomain' ),
	'list_package_excerpt_content',
	'travel_package',
	'normal',
	'high'
			);
	
}

function list_package_excerpt_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box2', 'travel_package_meta_box_nonce2' );
	$list_package_excerpt = get_post_meta($post->ID, 'package_excerpt', true);

	echo '<label for="list_package_excerpt"></label>';
	echo '<textarea id="list_package_excerpt" name="list_package_excerpt" rows="10" cols="100" placeholder="Enter package excerpt here" >';
	echo $list_package_excerpt;
	echo '</textarea>';

}

add_action( 'save_post', 'list_package_excerpt_save' );
function list_package_excerpt_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce2'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce2'], 'travel_package_meta_box2' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_excerpt"])){
		$list_package_excerpt = $_POST['list_package_excerpt'];
		update_post_meta($post_id, 'package_excerpt', $list_package_excerpt);
	}
}

// Package Cover Photo URL box
add_action( 'add_meta_boxes', 'list_package_cover_photo_url' );
function list_package_cover_photo_url() {

	add_meta_box(
	'list_package3_cover_photo_url',
	__( 'Package Cover Photo URL', 'myplugin_textdomain' ),
	'list_package_cover_photo_url_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_cover_photo_url_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box3', 'travel_package_meta_box_nonce3' );
	$list_package_cover_photo_url = get_post_meta($post->ID, 'package_cover_photo_url', true);

	echo '<label for="list_package_cover_photo_url"></label>';
	echo '<input type="text" id="list_package_cover_photo_url" name="list_package_cover_photo_url" size="100" placeholder="Enter package cover photo url here" value="';
	echo $list_package_cover_photo_url;
	echo '">';

}

add_action( 'save_post', 'list_package_cover_photo_url_save' );
function list_package_cover_photo_url_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce3'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce3'], 'travel_package_meta_box3' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_cover_photo_url"])){
		$list_package_cover_photo_url = $_POST['list_package_cover_photo_url'];
		update_post_meta($post_id, 'package_cover_photo_url', $list_package_cover_photo_url);
	}
}

// Package Layer Slider ID box
add_action( 'add_meta_boxes', 'list_package_layer_slider_id' );
function list_package_layer_slider_id() {

	add_meta_box(
	'list_package4_layer_slider_id',
	__( 'Package Layer Slider ID', 'myplugin_textdomain' ),
	'list_package_layer_slider_id_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_layer_slider_id_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box4', 'travel_package_meta_box_nonce4' );
	$list_package_layer_slider_id = get_post_meta($post->ID, 'package_layer_slider_id', true);

	echo '<label for="list_package_layer_slider_id"></label>';
	echo '<input type="text" id="list_package_layer_slider_id" name="list_package_layer_slider_id" size="100" placeholder="Enter Package_Layer Slider ID here" value="';
	echo $list_package_layer_slider_id;
	echo '">';

}

add_action( 'save_post', 'list_package_layer_slider_id_save' );
function list_package_layer_slider_id_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce4'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce4'], 'travel_package_meta_box4' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_layer_slider_id"])){
		$list_package_layer_slider_id = $_POST['list_package_layer_slider_id'];
		update_post_meta($post_id, 'package_layer_slider_id', $list_package_layer_slider_id);
	}
}

// Package Detail Include box
add_action( 'add_meta_boxes', 'list_package_detail_include' );
function list_package_detail_include() {
	add_meta_box(
	'list_package5_detail_include',
	__( 'Package Detail Include', 'myplugin_textdomain' ),
	'list_package_detail_include_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_detail_include_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box5', 'travel_package_meta_box_nonce5' );
	$list_package_detail_include = get_post_meta($post->ID, 'package_detail_include', true);

	echo '<label for="list_package_detail_include"></label>';
	echo '<textarea id="list_package_detail_include" name="list_package_detail_include" rows="10" cols="100" placeholder="Enter Package Include Detail here" >';
	echo $list_package_detail_include;
	echo '</textarea>';

}

add_action( 'save_post', 'list_package_detail_include_save' );
function list_package_detail_include_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce5'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce5'], 'travel_package_meta_box5' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_detail_include"])){
		$list_package_detail_include = $_POST['list_package_detail_include'];
		update_post_meta($post_id, 'package_detail_include', $list_package_detail_include);
	}
}


// Package Detail Validity box
add_action( 'add_meta_boxes', 'list_package_detail_validity' );
function list_package_detail_validity() {
	add_meta_box(
	'list_package6_detail_validity',
	__( 'Package Detail Validity', 'myplugin_textdomain' ),
	'list_package_detail_validity_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_detail_validity_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box6', 'travel_package_meta_box_nonce6' );
	$list_package_detail_validity = get_post_meta($post->ID, 'package_detail_validity', true);

	echo '<label for="list_package_detail_validity"></label>';
	echo '<textarea id="list_package_detail_validity" name="list_package_detail_validity" rows="10" cols="100" placeholder="Enter Package Validity Detail here" >';
	echo $list_package_detail_validity;
	echo '</textarea>';

}

add_action( 'save_post', 'list_package_detail_validity_save' );
function list_package_detail_validity_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce6'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce6'], 'travel_package_meta_box6' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_detail_validity"])){
		$list_package_detail_validity = $_POST['list_package_detail_validity'];
		update_post_meta($post_id, 'package_detail_validity', $list_package_detail_validity);
	}
}
