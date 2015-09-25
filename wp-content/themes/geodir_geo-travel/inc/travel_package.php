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

// Package Excerpt box
add_action( 'add_meta_boxes', 'list_package_excerpt' );
function list_package_excerpt() {
	add_meta_box(
	'list_package2_excerpt',
	__( 'Package Description', 'myplugin_textdomain' ),
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
	echo '<textarea id="list_package_excerpt" name="list_package_excerpt" rows="10" cols="100" placeholder="Enter package description here" >';
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



// Package Other Travel Dates box
add_action( 'add_meta_boxes', 'list_package_other_travel_dates' );
function list_package_other_travel_dates() {
	add_meta_box(
	'list_package8_other_travel_dates',
	__( 'For Other Travel Dates', 'myplugin_textdomain' ),
	'list_package_other_travel_dates_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_other_travel_dates_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box8', 'travel_package_meta_box_nonce8' );
	
	$list_package_other_travel_dates_honeymoon = get_post_meta($post->ID, 'list_package_other_travel_dates_honeymoon', true);
	$list_package_other_travel_dates_holiday = get_post_meta($post->ID, 'list_package_other_travel_dates_holiday', true);

	echo '<div class="travel_admin_checkbox" ><label class="travel_admin_label" for="list_package_notes_added_value">Honeymoon: </label>';
	echo '<input type="text" id="list_package_other_travel_dates_honeymoon" name="list_package_other_travel_dates_honeymoon" size="50" placeholder="Enter URL of Honeymoon Travel Button" value="';
	echo $list_package_other_travel_dates_honeymoon;
	echo '"></div>';
	
	echo '<div class="travel_admin_checkbox" ><label class="travel_admin_label" for="list_package_notes_added_value">Holiday: </label>';
	echo '<input type="text" id="list_package_other_travel_dates_holiday" name="list_package_other_travel_dates_holiday" size="50" placeholder="Enter URL of Holiday Travel Button" value="';
	echo $list_package_other_travel_dates_holiday;
	echo '"></div>';
	

}

add_action( 'save_post', 'list_package_other_travel_dates_save' );
function list_package_other_travel_dates_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce8'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce8'], 'travel_package_meta_box8' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_other_travel_dates_honeymoon"])){
		$list_package_other_travel_dates_honeymoon = $_POST['list_package_other_travel_dates_honeymoon'];
		update_post_meta($post_id, 'list_package_other_travel_dates_honeymoon', $list_package_other_travel_dates_honeymoon);
	}
	
	if(isset($_POST["list_package_other_travel_dates_holiday"])){
		$list_package_other_travel_dates_holiday = $_POST['list_package_other_travel_dates_holiday'];
		update_post_meta($post_id, 'list_package_other_travel_dates_holiday', $list_package_other_travel_dates_holiday);
	}
}

// Package Other Travel Dates Romantic Travel Only box
add_action( 'add_meta_boxes', 'list_package_other_travel_dates_romantic_only' );
function list_package_other_travel_dates_romantic_only() {
	add_meta_box(
	'list_package9_other_travel_dates_romantic_only',
	__( 'For Other Travel Dates (RomanticTravel Only)', 'myplugin_textdomain' ),
	'list_package_other_travel_dates_romantic_only_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_other_travel_dates_romantic_only_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box9', 'travel_package_meta_box_nonce9' );

	$list_package_other_travel_dates_honeymoon = get_post_meta($post->ID, 'list_package_other_travel_dates_romantic_only_honeymoon', true);
	$list_package_other_travel_dates_holiday = get_post_meta($post->ID, 'list_package_other_travel_dates_romantic_only_holiday', true);

	echo '<div class="travel_admin_checkbox" ><label class="travel_admin_label" for="list_package_notes_added_value">Honeymoon: </label>';
	echo '<input type="text" id="list_package_other_travel_dates_romantic_only_honeymoon" name="list_package_other_travel_dates_romantic_only_honeymoon" size="50" placeholder="Enter URL of Honeymoon Travel Button (Romantic Only)" value="';
	echo $list_package_other_travel_dates_honeymoon;
	echo '"></div>';

	echo '<div class="travel_admin_checkbox" ><label class="travel_admin_label" for="list_package_notes_added_value">Holiday: </label>';
	echo '<input type="text" id="list_package_other_travel_dates_romantic_only_holiday" name="list_package_other_travel_dates_romantic_only_holiday" size="50" placeholder="Enter URL of Holiday Travel Button (Romantic Only)" value="';
	echo $list_package_other_travel_dates_holiday;
	echo '"></div>';


}

add_action( 'save_post', 'list_package_other_travel_dates_romantic_only_save' );
function list_package_other_travel_dates_romantic_only_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce9'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce9'], 'travel_package_meta_box9' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($_POST["list_package_other_travel_dates_romantic_only_honeymoon"])){
		$list_package_other_travel_dates_honeymoon = $_POST['list_package_other_travel_dates_romantic_only_honeymoon'];
		update_post_meta($post_id, 'list_package_other_travel_dates_romantic_only_honeymoon', $list_package_other_travel_dates_honeymoon);
	}

	if(isset($_POST["list_package_other_travel_dates_romantic_only_holiday"])){
		$list_package_other_travel_dates_holiday = $_POST['list_package_other_travel_dates_romantic_only_holiday'];
		update_post_meta($post_id, 'list_package_other_travel_dates_romantic_only_holiday', $list_package_other_travel_dates_holiday);
	}
}



// Package Exclusive Deal box
add_action( 'add_meta_boxes', 'list_package_notes' );
function list_package_notes() {

	add_meta_box(
	'list_package7_notes',
	__( 'Package Promotion', 'myplugin_textdomain' ),
	'list_package_notes_content',
	'travel_package',
	'normal',
	'high'
			);

}

function list_package_notes_content( $post ) {

	wp_nonce_field( 'travel_package_meta_box7', 'travel_package_meta_box_nonce7' );
	$list_package_notes = get_post_meta($post->ID, 'list_package_notes', true);
	
	$list_package_notes_exclusive_sale = get_post_meta($post->ID, 'list_package_notes_exclusive_sale', true);
	$list_package_notes_added_value = get_post_meta($post->ID, 'list_package_notes_added_value', true);
	$list_package_notes_value_inclusion_1 = get_post_meta($post->ID, 'list_package_notes_value_inclusion_1', true);
	$list_package_notes_value_inclusion_2 = get_post_meta($post->ID, 'list_package_notes_value_inclusion_2', true);
	
	echo '<div class="travel_admin_checkbox" ><input type="checkbox" class="travel_package_promotion_checkbox" name="package_notes" value="exclusive_sale" ';
	
	if($list_package_notes == "exclusive_sale"){
		echo " checked ";
	}
	
	echo '/> Exclusive Sale</div>';
	echo '<label class="travel_admin_label" for="list_package_notes_exclusive_sale">Pecentage Discount: </label>';
	echo '<input type="text" id="list_package_notes_exclusive_sale" name="list_package_notes_exclusive_sale" size="50" placeholder="Enter package discount in percentage here" value="';
	echo $list_package_notes_exclusive_sale;
	echo '">%';
	
	echo '<div class="travel_admin_checkbox" ><input type="checkbox" class="travel_package_promotion_checkbox" name="package_notes" value="added_value"';
	
	if($list_package_notes == "added_value"){
		echo " checked ";
	}
	echo '/> Added Value</div>';
	
	echo '<label class="travel_admin_label" for="list_package_notes_added_value">Value: </label>';
	echo '$<input type="text" id="list_package_notes_added_value" name="list_package_notes_added_value" size="50" placeholder="Enter added value in AUD" value="';
	echo $list_package_notes_added_value;
	echo '">';
	
	echo '<div class="travel_admin_inclusion_label" > <label>Inclusions: </label> </div>';
	echo '<div class="travel_admin_inclusion_label"><label class="travel_admin_label" for="list_package_notes_value_inclusion_1">1: </label>';
	echo '<input type="text" id="list_package_notes_value_inclusion_1" name="list_package_notes_value_inclusion_1" size="100" placeholder="Enter added value inclusion 1" value="';
	echo $list_package_notes_value_inclusion_1;
	echo '"></div>';
	echo '<div class="travel_admin_inclusion_label"><label class="travel_admin_label" for="list_package_notes_value_inclusion_2">2: </label>';
	echo '<input type="text" id="list_package_notes_value_inclusion_2" name="list_package_notes_value_inclusion_2" size="100" placeholder="Enter added value inclusion 2" value="';
	echo $list_package_notes_value_inclusion_2;
	echo '"></div>';
	
	

}

add_action( 'save_post', 'list_package_notes_save' );
function list_package_notes_save( $post_id ) {

	if ( ! isset( $_POST['travel_package_meta_box_nonce7'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['travel_package_meta_box_nonce7'], 'travel_package_meta_box7' ) ) {
		return;
	}
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	if(!empty($_POST['package_notes']) && !is_array($_POST['package_notes'])) {
		
		update_post_meta($post_id, 'list_package_notes', $_POST['package_notes']);
	}else{
		update_post_meta($post_id, 'list_package_notes', "");
	}
	
	

	if(isset($_POST["list_package_notes_exclusive_sale"])){
		$list_package_notes_exclusive_sale = $_POST['list_package_notes_exclusive_sale'];
		update_post_meta($post_id, 'list_package_notes_exclusive_sale', $list_package_notes_exclusive_sale);
	}
	
	if(isset($_POST["list_package_notes_added_value"])){
		$list_package_notes_added_value = $_POST['list_package_notes_added_value'];
		update_post_meta($post_id, 'list_package_notes_added_value', $list_package_notes_added_value);
	}
	
	if(isset($_POST["list_package_notes_value_inclusion_1"])){
		$list_package_notes_value_inclusion_1 = $_POST['list_package_notes_value_inclusion_1'];
		update_post_meta($post_id, 'list_package_notes_value_inclusion_1', $list_package_notes_value_inclusion_1);
	}
	
	if(isset($_POST["list_package_notes_value_inclusion_2"])){
		$list_package_notes_value_inclusion_2 = $_POST['list_package_notes_value_inclusion_2'];
		update_post_meta($post_id, 'list_package_notes_value_inclusion_2', $list_package_notes_value_inclusion_2);
	}
	
}

