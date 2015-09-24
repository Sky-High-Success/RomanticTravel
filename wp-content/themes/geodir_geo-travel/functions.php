<?php
/**
 * classiads functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin' );

function load_custom_wp_admin() {

	wp_enqueue_style( 'wp_admin_css', get_stylesheet_directory_uri() . '/admin_css.css', array(), '1' );
	wp_enqueue_script( 'wp_admin_script', get_stylesheet_directory_uri() . '/admin_script.js', array( 'jquery' ), '2014-07-18', true );

}

function simple_authentication($result) {

	$client_id = $_POST['client_id'];

	$client_secret = $_POST['client_secret'];

	if($client_id == 'VPLJ1vXj9kyRhizubrsMrYAMhj6Vns' && $client_secret == 'VihH2jcZCljbcAuM5FdyrEx2rOlxJI'){
		return true;
	}else{
		return false;
	}

}



// for website ID tracking
add_action( 'wp_ajax_id_tracking', 'id_tracking_callback' );
add_action( 'wp_ajax_nopriv_id_tracking', 'id_tracking_callback' );

function id_tracking_callback(){

	global $wpdb;


	$id_tracking_table = 'id_tracking';

	$name = esc_attr(strip_tags($_GET['name']));
	
	if(empty($name)){
		echo "Invalid";
	}else{
		
		$row = $wpdb->query(
				"UPDATE $id_tracking_table
				SET generated_id = generated_id + 1
				WHERE name='$name'"
		);
		
		$result = $wpdb->get_row ( "SELECT name, generated_id FROM $id_tracking_table
										  where name='$name'");
		
		echo $result->generated_id;
		
	}

	die();
}

function get_all_packages_meta(){
	global $wpdb;

	$meta_table = $wpdb->prefix.'postmeta';
	
	$post_table = $wpdb->prefix.'posts';
		
	$package_meta_obj_arr = $wpdb->get_results ( "
			SELECT post.ID as `id`, post.post_name as `post_name`, post.post_title as `post_title`, meta.meta_value as `validity_date`
			FROM $post_table as post inner join $meta_table as meta where post.ID = meta.post_id AND post.post_type = 'travel_package' AND meta.meta_key like 'package_detail_validity'
			" );
	
	foreach($package_meta_obj_arr as $package_meta_obj){
		$date_str = $package_meta_obj->validity_date;
		
		preg_match_all('%From[:]*\s*(?P<from>[-\w]+)\s*To[:]*\s*(?P<to>[-\w]+)%', $date_str, $matches);
		
		$from_arr = $matches['from'];
		$to_arr = $matches['to'];
		

		foreach($from_arr as $from){
			$package_meta_obj->from = $from_arr; 
		}
		
		
		foreach($to_arr as $to){
			$package_meta_obj->to = $to_arr;
		}
		
		unset($package_meta_obj->validity_date);
	}
	
	return $package_meta_obj_arr;
}

// exclude plugins from auto update
function exclude_plugins_from_auto_update( $update, $item ) {
		
	return ( ! in_array( $item->slug, array(
			'indeed-social-media', 'scarcity-samurai') ) 
		   );
}
add_filter( 'auto_update_plugin', 'exclude_plugins_from_auto_update', 10, 2 );

// Disable User wp-admin access
add_action('admin_init', 'no_more_dashboard');

function no_more_dashboard() {
	if (!current_user_can('manage_options') && !(defined('DOING_AJAX') && DOING_AJAX)) {
		wp_redirect(home_url()); exit;
	}
}

// Category pagination fix
function remove_page_from_query_string($query_string)
{
	if ($query_string['name'] == 'page' && isset($query_string['page'])) {
		unset($query_string['name']);
		// 'page' in the query_string looks like '/2', so i'm spliting it out
		list($delim, $page_index) = split('/', $query_string['page']);
		$query_string['paged'] = $page_index;
	}
	return $query_string;
}
// I will kill you if you remove this. I died two days for this line
add_filter('request', 'remove_page_from_query_string');

// following are code adapted from Custom Post Type Category Pagination Fix by jdantzer
function fix_category_pagination($qs){
	if(isset($qs['category_name']) && isset($qs['paged'])){
		$qs['post_type'] = get_post_types($args = array(
				'public'   => true,
				'_builtin' => false
		));
		array_push($qs['post_type'],'post');
	}
	return $qs;
}
add_filter('request', 'fix_category_pagination');

function stop_heartbeat() {
	wp_deregister_script('heartbeat');
}

//add_action( 'init', 'stop_heartbeat', 1 );

add_action( 'after_setup_theme', 'classiads_theme_setup' );


function classiads_theme_setup() {

    add_theme_support( 'woocommerce' );

    add_theme_support( 'custom-background' );

    
   
    
    /**
     * Adds post custom meta.
     */
    
    require get_stylesheet_directory() . '/inc/travel_package.php';

    // Add Featured Post Prie Plans
    require get_stylesheet_directory() . '/inc/plans.php';

    // Add Page Meta Fields
    require get_stylesheet_directory() . '/inc/page_meta.php';

    // Add Shortcodes
    require get_stylesheet_directory() . '/inc/shortcode.lib.php';

    // Add Colors
    require get_stylesheet_directory() . '/inc/colors.php';


    // Add Colors
    require get_stylesheet_directory() . '/inc/widgets/recent_posts_widget.php';
	
    require get_stylesheet_directory() . '/inc/widgets/twitter_widget.php';
	
    require get_stylesheet_directory() . '/inc/widgets/categories.php';
	
	require get_stylesheet_directory() . '/inc/twitter-function.php';


    /**
     * Sets up the content width value based on the theme's design.
     * @see classiads_content_width() for template-specific adjustments.
     */
    if ( ! isset( $content_width ) )
        $content_width = 1060;

    /**
     * classiads only works in WordPress 3.6 or later.
     */
    if ( version_compare( $GLOBALS['wp_version'], '3.6-alpha', '<' ) )
        require get_stylesheet_directory() . '/inc/back-compat.php';

    /**
     * Sets up theme defaults and registers the various WordPress features that
     * classiads supports.
     *
     * @uses load_theme_textdomain() For translation/localization support.
     * @uses add_editor_style() To add Visual Editor stylesheets.
     * @uses add_theme_support() To add support for automatic feed links, post
     * formats, and post thumbnails.
     * @uses register_nav_menu() To add support for a navigation menu.
     * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
     *
     * @since classiads 1.2.2
     *
     * @return void
    */

	/*
	 * Makes classiads available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on classiads, use a find and
	 * replace to change 'classiads' to the name of your theme in all
	 * template files.
	 */
	load_theme_textdomain( 'agrg', get_stylesheet_directory() . '/languages' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', 'fonts/genericons.css', classiads_fonts_url() ) );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Switches default core markup for search form, comment form, and comments
	// to output valid HTML5.
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Navigation Menu', 'agrg' ) );

	/*
	 * This theme uses a custom image size for featured images, displayed on
	 * "standard" posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 440, 266, true );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );

    // Disable Disqus commehts on woocommerce product //
    add_filter( 'woocommerce_product_tabs', 'disqus_override_tabs', 98);

    // Custom admin scripts
    add_action('admin_enqueue_scripts', 'wpcads_custom_admin_scripts' );

    // Author add new contact details
    add_filter('user_contactmethods','wpcrown_author_new_contact',10,1);

    
    
    // Load scripts and styles
    add_action( 'wp_enqueue_scripts', 'classiads_scripts_styles' );

    // Add custom metas
    add_action( 'add_meta_boxes', 'wpcrown_add_posts_metaboxes' );

    // Save custom posts
    add_action('save_post', 'wpcrown_save_post_meta', 1, 2); // save the custom fields

    // Category new fields (the form)
    add_filter('add_category_form', 'wpcrown_my_category_fields');
    add_filter('edit_category_form', 'wpcrown_my_category_fields');

    // Update category fields
    add_action( 'edited_category', 'wpcrown_update_my_category_fields', 10, 2 );  
    add_action( 'create_category', 'wpcrown_update_my_category_fields', 10, 2 );

    //Include the TGM_Plugin_Activation class.
    require_once dirname( __FILE__ ) . '/inc/class-tgm-plugin-activation.php';
    add_action( 'tgmpa_register', 'TW_register_required_plugins' );

    // Google analitycs code
    //add_action( 'wp_enqueue_scripts', 'wpcrown_google_analityc_code' );

    // Enqueue main font
    add_action( 'wp_enqueue_scripts', 'wpcrown_main_font' );

    // Enqueue main font
    add_action( 'wp_enqueue_scripts', 'wpcrown_second_font_armata' );

    // Track views
    add_action( 'wp_head', 'wpb_track_post_views');

    // Theme page titles
    add_filter( 'wp_title', 'classiads_wp_title', 10, 2 );
	
	 // Footer Menu
	add_action( 'init', 'wpcrown_register_my_menu' );

    // classiads sidebars spot
    add_action( 'widgets_init', 'classiads_widgets_init' );

    // classiads body class
    add_filter( 'body_class', 'classiads_body_class' );

    // classiads content width
    add_action( 'template_redirect', 'classiads_content_width' );

    // classiads customize register
    add_action( 'customize_register', 'classiads_customize_register' );

    // classiads customize preview
    add_action( 'customize_preview_init', 'classiads_customize_preview_js' );

    /* Add theme support for automatic feed links. */   
    add_theme_support( 'automatic-feed-links' );

}


// Disable Disqus commehts on woocommerce product //
function disqus_override_tabs($tabs){
    if ( has_filter( 'comments_template', 'dsq_comments_template' ) ){
        remove_filter( 'comments_template', 'dsq_comments_template' );
        add_filter('comments_template', 'dsq_comments_template',90);//higher priority, so the filter is called after woocommerce filter
    }
    return $tabs;
}

/*
 *  Setup main navigation menu
 */
function wpcrown_register_my_menu() {
    // register menu
    register_nav_menus (
        array(
        'primary' =>__('Main menu'),
        'secondary' =>__('Footer menu'),
        )
    );
}


// Custom admin scripts
function wpcads_custom_admin_scripts() {
	wp_enqueue_media();
}


// Author add new contact details
function wpcrown_author_new_contact( $contactmethods ) {

	// Add telefone
	$contactmethods['phone'] = 'Phone';
	// add address
	$contactmethods['address'] = 'Address';
 
	return $contactmethods;
	
}


// Add user price plan info
function wpcrown_save_extra_profile_fields( $user_id ) {
	update_user_meta( $user_id, 'price_plan' );
	add_user_meta( $user_id, 'price_plan_id' );
}






// Insert attachments front end
function wpcads_insert_attachment($file_handler,$post_id,$setthumb='false') {

  // check to make sure its a successful upload
  if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
  require_once(ABSPATH . "wp-admin" . '/includes/media.php');

  $attach_id = media_handle_upload( $file_handler, $post_id );

  if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
  return $attach_id;
}





/*--------------------------------------*/
/*          Custom Post Meta           */
/*--------------------------------------*/
// Add the Post Meta Boxes
function wpcrown_add_posts_metaboxes() {
	//add_meta_box('wpcrown_featured_post', 'Featured Post', 'wpcrown_featured_post', 'post', 'side', 'default');
}

// Show The Post On Slider Option
function wpcrown_featured_post() {
	global $post;
	
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	// Get the location data if its already been entered
	$featured_post = get_post_meta($post->ID, 'featured_post', true);
	
	// Echo out the field
	echo '<span class="text overall" style="margin-right: 20px;">Check to have this as featured post:</span>';
	
	$checked = get_post_meta($post->ID, 'featured_post', true) == '1' ? "checked" : "";
	
	echo '<input type="checkbox" name="featured_post" id="featured_post" value="1" '. $checked .'/>';

}

// Save the Metabox Data
function wpcrown_save_post_meta($post_id, $post) {
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( isset( $_POST['eventmeta_noncename'] ) ? $_POST['eventmeta_noncename'] : '', plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	
	$events_meta['featured_post'] = $_POST['featured_post'];
	
	$chk = ( isset( $_POST['featured_post'] ) && $_POST['featured_post'] ) ? '1' : '2';
	update_post_meta( $post_id, 'featured_post', $chk );
	
	// Add values of $events_meta as custom fields
	foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
		if( $post->post_type == 'post' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}

}
/* End */




/**
 * Returns the Google font stylesheet URL, if available.
 *
 * The use of Source Sans Pro and Bitter by default is localized. For languages
 * that use characters not supported by the font, the font can be disabled.
 *
 * @since classiads 1.2.2
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function classiads_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Source Sans Pro, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$source_sans_pro = _x( 'on', 'Source Sans Pro font: on or off', 'classiads' );

	/* Translators: If there are characters in your language that are not
	 * supported by Bitter, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$bitter = _x( 'on', 'Bitter font: on or off', 'classiads' );

	if ( 'off' !== $source_sans_pro || 'off' !== $bitter ) {
		$font_families = array();

		if ( 'off' !== $source_sans_pro )
			$font_families[] = 'Source Sans Pro:300,400,700,300italic,400italic,700italic';

		if ( 'off' !== $bitter )
			$font_families[] = 'Bitter:400,700';

		$query_args = array(
			'family' => urlencode( implode( '%7C', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}

//////////////////////////////////////////////////////////////////
//// function to display extra info on category admin
//////////////////////////////////////////////////////////////////
// the option name
define('MY_CATEGORY_FIELDS', 'my_category_fields_option');

// your fields (the form)
function wpcrown_my_category_fields($tag) {
    $tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
	$category_icon_code = isset( $tag_extra_fields[$tag->term_id]['category_icon_code'] ) ? esc_attr( $tag_extra_fields[$tag->term_id]['category_icon_code'] ) : '';
    $category_icon_color = isset( $tag_extra_fields[$tag->term_id]['category_icon_color'] ) ? esc_attr( $tag_extra_fields[$tag->term_id]['category_icon_color'] ) : '';
    $your_image_url = isset( $tag_extra_fields[$tag->term_id]['your_image_url'] ) ? esc_attr( $tag_extra_fields[$tag->term_id]['your_image_url'] ) : '';
    ?>

<div class="form-field">	
<table class="form-table">
        <tr class="form-field">
        	<th scope="row" valign="top"><label for="category-page-slider">Icon Code</label></th>
        	<td>

				<input id="category_icon_code" type="text" size="36" name="category_icon_code" value="<?php $category_icon = stripslashes($category_icon_code); echo esc_attr($category_icon); ?>" />
                <p class="description">AwesomeFont code: <a href="http://fontawesome.io/icons/" target="_blank">fontawesome.io/icons</a></p>

			</td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="category-page-slider">Icon Background Color</label></th>
            <td>

                <link rel="stylesheet" media="screen" href="<?php echo get_stylesheet_directory_uri() ?>/inc/color-picker/css/colorpicker.css" />
                <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/inc/color-picker/js/colorpicker.js"></script>
                <script type="text/javascript">
                jQuery.noConflict();
                jQuery(document).ready(function(){
                    jQuery('#colorpickerHolder').ColorPicker({color: '<?php echo $category_icon_color; ?>', flat: true, onChange: function (hsb, hex, rgb) { jQuery('#category_icon_color').val('#' + hex); }});
                });
                </script>

                <p id="colorpickerHolder"></p>

                <input id="category_icon_color" type="text" size="36" name="category_icon_color" value="<?php echo $category_icon_color; ?>" style="margin-top: 20px; max-width: 90px; visibility: hidden;" />

            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="category-page-slider">Map Pin</label></th>
            <td>
            <?php 

            if(!empty($your_image_url)) {

                echo '<div style="width: 100%; float: left;"><img id="your_image_url_img" src="'. $your_image_url .'" style="float: left; margin-bottom: 20px;" /> </div>';
                echo '<input id="your_image_url" type="text" size="36" name="your_image_url" style="max-width: 200px; float: left; margin-top: 10px; display: none;" value="'.$your_image_url.'" />';
                echo '<input id="your_image_url_button_remove" class="button" type="button" style="max-width: 140px; float: left; margin-top: 10px;" value="Remove" /> </br>';
                echo '<input id="your_image_url_button" class="button" type="button" style="max-width: 140px; float: left; margin-top: 10px; display: none;" value="Upload Image" /> </br>'; 

            } else {

                echo '<div style="width: 100%; float: left;"><img id="your_image_url_img" src="'. $your_image_url .'" style="float: left; margin-bottom: 20px;" /> </div>';
                echo '<input id="your_image_url" type="text" size="36" name="your_image_url" style="max-width: 200px; float: left; margin-top: 10px; display: none;" value="'.$your_image_url.'" />';
                echo '<input id="your_image_url_button_remove" class="button" type="button" style="max-width: 140px; float: left; margin-top: 10px; display: none;" value="Remove" /> </br>';
                echo '<input id="your_image_url_button" class="button" type="button" style="max-width: 140px; float: left; margin-top: 10px;" value="Upload Image" /> </br>';

            }

            ?>
            </td>

            <script>
            var image_custom_uploader;
            jQuery('#your_image_url_button').click(function(e) {
                e.preventDefault();

                //If the uploader object has already been created, reopen the dialog
                if (image_custom_uploader) {
                    image_custom_uploader.open();
                    return;
                }

                //Extend the wp.media object
                image_custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });

                //When a file is selected, grab the URL and set it as the text field's value
                image_custom_uploader.on('select', function() {
                    attachment = image_custom_uploader.state().get('selection').first().toJSON();
                    var url = '';
                    url = attachment['url'];
                    jQuery('#your_image_url').val(url);
                    jQuery( "img#your_image_url_img" ).attr({
                        src: url
                    });
                    jQuery("#your_image_url_button").css("display", "none");
                    jQuery("#your_image_url_button_remove").css("display", "block");
                });

                //Open the uploader dialog
                image_custom_uploader.open();
             });

             jQuery('#your_image_url_button_remove').click(function(e) {
                jQuery('#your_image_url').val('');
                jQuery( "img#your_image_url_img" ).attr({
                    src: ''
                });
                jQuery("#your_image_url_button").css("display", "block");
                jQuery("#your_image_url_button_remove").css("display", "none");
             });
            </script>
        </tr>
</table>
</div>

    <?php
}


// when the form gets submitted, and the category gets updated (in your case the option will get updated with the values of your custom fields above
function wpcrown_update_my_category_fields($term_id) {
  if($_POST['taxonomy'] == 'category'):
    $tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
    $tag_extra_fields[$term_id]['your_image_url'] = strip_tags($_POST['your_image_url']);
    $tag_extra_fields[$term_id]['category_icon_code'] = $_POST['category_icon_code'];
    $tag_extra_fields[$term_id]['category_icon_color'] = $_POST['category_icon_color'];
    update_option(MY_CATEGORY_FIELDS, $tag_extra_fields);
  endif;
}


// when a category is removed
add_filter('deleted_term_taxonomy', 'wpcrown_remove_my_category_fields');
function wpcrown_remove_my_category_fields($term_id) {
  if($_POST['taxonomy'] == 'category'):
    $tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
    unset($tag_extra_fields[$term_id]);
    update_option(MY_CATEGORY_FIELDS, $tag_extra_fields);
  endif;
}




/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function TW_register_required_plugins() {
 
    /**
     * Array of plugin arrays. Required keys are name, slug and required.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

    	
        // Layer SLider
        array(
            'name' => 'LayerSlider WP',
            'slug' => 'LayerSlider',
            'source' => get_stylesheet_directory() . '/inc/plugins/layersliderwp-5.0.2.installable.zip',
            'required' => false,
            'version' => '5.0.2',
            'force_activation' => true,
            'force_deactivation' => true
        )

        // Ratings
//         array(
//             'name' => 'WP-PostRatings',
//             'slug' => 'wp-postratings',
//             'source' => get_stylesheet_directory() . '/inc/plugins/wp-postratings.zip',
//             'required' => false,
//             'version' => '1.77',
//             'force_activation' => true,
//             'force_deactivation' => true
//         ),        
 
    );
 
    // Change this to your theme text domain, used for internationalising strings
    $theme_text_domain = 'wpcrown';
 
    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'domain'            => 'wpcrown',           // Text domain - likely want to be the same as your theme.
        'default_path'      => '',                           // Default absolute path to pre-packaged plugins
        'parent_menu_slug'  => 'themes.php',         // Default parent menu slug
        'parent_url_slug'   => 'themes.php',         // Default parent URL slug
        'menu'              => 'install-required-plugins',   // Menu slug
        'has_notices'       => true,                         // Show admin notices or not
        'is_automatic'      => false,            // Automatically activate plugins after installation or not
        'message'           => '',               // Message to output right before the plugins table
        'strings'           => array(
            'page_title'                                => __( 'Install Required Plugins', 'wpcrown' ),
            'menu_title'                                => __( 'Install Plugins', 'wpcrown' ),
            'installing'                                => __( 'Installing Plugin: %s', 'wpcrown' ), // %1$s = plugin name
            'oops'                                      => __( 'Something went wrong with the plugin API.', 'wpcrown' ),
            'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
            'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
            'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
            'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
            'return'                                    => __( 'Return to Required Plugins Installer', 'wpcrown' ),
            'plugin_activated'                          => __( 'Plugin activated successfully.', 'wpcrown' ),
            'complete'                                  => __( 'All plugins installed and activated successfully. %s', 'wpcrown' ) // %1$s = dashboard link
        )
    );
 
    tgmpa( $plugins, $config );
 
}



/**
* Google analytic code
*/
function wpcrown_google_analityc_code() { ?>

	<script type="text/javascript">

	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php global $redux_demo; $google_id = $redux_demo['google_id']; echo $google_id; ?>']);
	_gaq.push(['_setDomainName', 'none']);
	_gaq.push(['_setAllowLinker', true]);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

</script>
	
<?php }



/**
 * Enqueues scripts and styles for front end.
 *
 * @since classiads 1.2.2
 *
 * @return void
 */
function classiads_scripts_styles() {
	
  
  // Load bootstrapValidator
  wp_enqueue_script( 'jqueryValidator', get_stylesheet_directory_uri() . '/js/jquery.validate.min.js', array('jquery'), '2014-07-18', true );
  
 // wp_enqueue_style( 'plan', get_stylesheet_directory_uri() . '/plan.css', array(), '1' );
  
  
  
}

add_image_size( 'premium-post-image', 370, 240, true );
add_image_size( 'single-post-image', 300, 300, true );
add_image_size( 'single-cat-image', 255, 218, true );
add_image_size( '270x220', 270, 220, true );
function wpcrown_main_font() {
    $protocol = is_ssl() ? 'https' : 'http';
    //wp_enqueue_style( 'mytheme-roboto', "$protocol://fonts.googleapis.com/css?family=Roboto:400,400italic,500,300,300italic,500italic,700,700italic" );
}



function wpcrown_second_font_armata() {
    $protocol = is_ssl() ? 'https' : 'http';
    //wp_enqueue_style( 'mytheme-armata', "$protocol://fonts.googleapis.com/css?family=Armata" );
}


// Post views
function wpb_set_post_views($postID) {
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
//To keep the count accurate, lets get rid of prefetching


function wpb_track_post_views ($post_id) {
    if ( !is_single() ) return;
    if ( empty ( $post_id) ) {
        global $post;
        $post_id = $post->ID;    
    }
    wpb_set_post_views($post_id);
}


function wpb_get_post_views($postID){
    $count_key = 'wpb_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}




/**
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since classiads 1.2.2
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function classiads_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'agrg' ), max( $paged, $page ) );

	return $title;
}


/**
 * Registers two widget areas.
 *
 * @since classiads 1.2.2
 *
 * @return void
 */
function classiads_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer Widget ', 'agrg' ),
		'id'            => 'footer-one',
		'description'   => __( 'Appears in the footer section of the site.', 'agrg' ),
		'before_widget' => '<div class="span3 first">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h4 class="block-title">',
		'after_title'   => '</h4><div class="block-content clearfix">',
	) );

    register_sidebar( array(
        'name'          => __( 'Forum Widget Area', 'agrg' ),
        'id'            => 'forum',
        'description'   => __( 'Appears on posts and pages in the sidebar.', 'agrg' ),
        'before_widget' => '<div class="cat-widget"><div class="cat-widget-content">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<div class="cat-widget-title"><h4>',
        'after_title'   => '</h4></div>',
    ) );

    

   

	register_sidebar( array(
		'name'          => __( 'Pages Widget Area', 'agrg' ),
		'id'            => 'pages',
		'description'   => __( 'Appears on posts and pages in the sidebar.', 'agrg' ),
		'before_widget' => '<div class="cat-widget"><div class="cat-widget-content">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<div class="cat-widget-title"><h3>',
		'after_title'   => '</h3><div class="h3-seprator-sidebar"></div></div>',
	) );

    

    
}

if ( ! function_exists( 'classiads_paging_nav' ) ) :
/**
 * Displays navigation to next/previous set of posts when applicable.
 *
 * @since classiads 1.2.2
 *
 * @return void
 */
function classiads_paging_nav() {
	global $wp_query;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 )
		return;
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'agrg' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'agrg' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'agrg' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'classiads_post_nav' ) ) :
/**
 * Displays navigation to next/previous post when applicable.
*
* @since classiads 1.2.2
*
* @return void
*/
function classiads_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'agrg' ); ?></h1>
		<div class="nav-links">

			<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'agrg' ) ); ?>
			<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'agrg' ) ); ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'classiads_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own classiads_entry_meta() to override in a child theme.
 *
 * @since classiads 1.2.2
 *
 * @return void
 */
function classiads_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'agrg' ) . '</span>';

	if ( ! has_post_format( 'link' ) && 'post' == get_post_type() )
		classiads_entry_date();

	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'agrg' ) );
	if ( $categories_list ) {
		echo '<span class="categories-links">' . $categories_list . '</span>';
	}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'agrg' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'agrg' ), get_the_author() ) ),
			get_the_author()
		);
	}
}
endif;

if ( ! function_exists( 'classiads_entry_date' ) ) :
/**
 * Prints HTML with date information for current post.
 *
 * Create your own classiads_entry_date() to override in a child theme.
 *
 * @since classiads 1.2.2
 *
 * @param boolean $echo Whether to echo the date. Default true.
 * @return string The HTML-formatted post date.
 */
function classiads_entry_date( $echo = true ) {
	if ( has_post_format( array( 'chat', 'status' ) ) )
		$format_prefix = _x( '%1$s on %2$s', '1: post format name. 2: date', 'agrg' );
	else
		$format_prefix = '%2$s';

	$date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
		esc_url( get_permalink() ),
		esc_attr( sprintf( __( 'Permalink to %s', 'agrg' ), the_title_attribute( 'echo=0' ) ) ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
	);

	if ( $echo )
		echo $date;

	return $date;
}
endif;

if ( ! function_exists( 'classiads_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 *
 * @since classiads 1.2.2
 *
 * @return void
 */
function classiads_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'classiads_attachment_size', array( 724, 724 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

/**
 * Returns the URL from the post.
 *
 * @uses get_url_in_content() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since classiads 1.2.2
 *
 * @return string The Link format URL.
 */
function classiads_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}

/**
 * Extends the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Active widgets in the sidebar to change the layout and spacing.
 * 3. When avatars are disabled in discussion settings.
 *
 * @since classiads 1.2.2
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function classiads_body_class( $classes ) {
	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_active_sidebar( 'sidebar-2' ) && ! is_attachment() && ! is_404() )
		$classes[] = 'sidebar';

	if ( ! get_option( 'show_avatars' ) )
		$classes[] = 'no-avatars';

	return $classes;
}


/**
 * Adjusts content_width value for video post formats and attachment templates.
 *
 * @since classiads 1.2.2
 *
 * @return void
 */
function classiads_content_width() {
	global $content_width;

	if ( is_attachment() )
		$content_width = 724;
	elseif ( has_post_format( 'audio' ) )
		$content_width = 484;
}

/**
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since classiads 1.2.2
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 * @return void
 */
function classiads_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}


/**
 * Binds JavaScript handlers to make Customizer preview reload changes
 * asynchronously.
 *
 * @since classiads 1.2.2
 */
function classiads_customize_preview_js() {
	wp_enqueue_script( 'classiads-customizer', get_stylesheet_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130226', true );
}

// Add Redux Framework
if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/ReduxFramework/ReduxCore/framework.php' ) ) {
	require_once( dirname( __FILE__ ) . '/ReduxFramework/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/ReduxFramework/sample/sample-config.php' ) ) {
	require_once( dirname( __FILE__ ) . '/ReduxFramework/sample/sample-config.php' );
}

/*---------------------------------------------------
register categories custom fields page
----------------------------------------------------*/
function theme_settings_init(){
  register_setting( 'theme_settings', 'theme_settings' );
  wp_enqueue_style("panel_style", get_stylesheet_directory_uri()."/css/categories-custom-fields.css", false, "1.0", "all");
  wp_enqueue_script("panel_script", get_stylesheet_directory_uri()."/js/categories-custom-fields-script.js", false, "1.0");
}
 
/*---------------------------------------------------
add categories custom fields page to menu
----------------------------------------------------*/
function add_settings_page() {
  add_menu_page(  'Categories Custom Fields' , 'Categories Custom Fields' , 'manage_options', 'settings', 'theme_settings_page');
}
 
//
add_action('template_redirect', 'add_scripts');
 
function add_scripts() {
    if (is_singular()) {
      add_thickbox(); 
    }
}


add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  show_admin_bar(false);
	}
}


//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'Geo Travel Theme', 'geotravel' ) );
define( 'CHILD_THEME_URL', 'http://wpgeothemes.com/theme/geo-travel/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

//* Add Homepage Image upload to WordPress Theme Customizer
require_once( get_stylesheet_directory() . '/lib/customize.php' );

//* Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'geotravel_google_fonts' );
function geotravel_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700', array(), CHILD_THEME_VERSION );

}

//* Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'geotravel_scripts' );
function geotravel_scripts() {

	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'geotravel-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
		
}

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'caption', ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'header-selector' => '.site-title a',
	'header-text'     => false,
	'height'          => 100,
	'width'           => 320,
) );

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'subnav',
	'site-inner',
	'footer-widgets',
	'footer',
) );

/*//* Reposition the simple share buttons
add_action( 'genesis_header', 'bg_simple_share', 11 );
function bg_simple_share() {

	if( 'post' === get_post_type() ) {
		genesis_share_icon_output( 'genesis_header', array(  'twitter', 'facebook', 'googlePlus' ) );
	}

}
*/
//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

//* Reduce the secondary navigation menu to one level depth
add_filter( 'wp_nav_menu_args', 'geotravel_secondary_menu_args' );
function geotravel_secondary_menu_args( $args ){

	if( 'secondary' != $args['theme_location'] )
	return $args;

	$args['depth'] = 1;
	return $args;

}

//* Hook after post widget after the entry content
add_action( 'genesis_after_entry', 'geotravel_after_entry', 5 );
function geotravel_after_entry() {

	if ( is_singular( 'post' ) )
		genesis_widget_area( 'after-entry', array(
			'before' => '<div class="after-entry widget-area">',
			'after'  => '</div>',
		) );

}

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'geotravel_remove_comment_form_allowed_tags' );
function geotravel_remove_comment_form_allowed_tags( $defaults ) {
	
	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Add My Custom Footer Links
add_filter('genesis_footer_creds_text', 'custom_footer_creds_text');
function custom_footer_creds_text($creds) {
 $creds = '[footer_copyright] ' . get_bloginfo('name') . ' &bull; <a href="http://wpgeothemes.com/theme/geo-travel/">Geo Travel Theme</a> by <a href="http://wpgeothemes.com/">WP Geo Themes</a>';
 return  $creds;
}

//* Add Discliamer to Footer
add_action( 'genesis_footer', 'geotravel_disclaimer' );
	function geotravel_disclaimer() {
		genesis_widget_area( 'disclaimer', array(
			'before' => '<div class="disclaimer widget-area">',
			'after'  => '</div>',
		) );
}

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-featured-left',
	'name'        => __( 'Home Featured Left', 'geotravel' ),
	'description' => __( 'This is the home featured left widget area.', 'geotravel' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-featured-right',
	'name'        => __( 'Home Featured Right', 'geotravel' ),
	'description' => __( 'This is the home featured right widget area.', 'geotravel' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-top',
	'name'        => __( 'Home Top', 'geotravel' ),
	'description' => __( 'This is the home top widget area.', 'geotravel' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle',
	'name'        => __( 'Home Middle', 'geotravel' ),
	'description' => __( 'This is the home middle widget area.', 'geotravel' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-bottom',
	'name'        => __( 'Home Bottom', 'geotravel' ),
	'description' => __( 'This is the home bottom widget area.', 'geotravel' ),
) );
genesis_register_sidebar( array(
	'id'          => 'after-entry',
	'name'        => __( 'After Entry', 'geotravel' ),
	'description' => __( 'This is the after entry widget area.', 'geotravel' ),
) );
genesis_register_sidebar( array(
	'id'          => 'disclaimer',
	'name'        => __( 'Disclaimer', 'geotravel' ),
	'description' => __( 'This is the disclaimer section of the footer.', 'geotravel' ),
) );
