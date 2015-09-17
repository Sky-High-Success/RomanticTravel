<?php
/*
Plugin Name: Indeed Social Share & Locker Pro
Plugin URI: http://www.wpindeed.com/
Description: Share your content on Social Media Networks or Lock your content before the page is shared.
Version: 3.6
Author: indeed
Author URI: http://www.wpindeed.com
*/
define('ISM_DIR_PATH', plugin_dir_path(__FILE__));
define('ISM_DIR_URL', plugin_dir_url(__FILE__));
define('ISM_PROTOCOL', ism_site_protocol());
define('IMTST_FLAG_LIMIT', 100);

function ism_site_protocol() {
	if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
		return 'https://';
	}
	return 'http://';
}

require_once( ISM_DIR_PATH . 'includes/functions.php' );
add_action ( 'admin_menu', 'ism_menu', 81 );
function ism_menu() {
	add_menu_page ( 'Social Share&Locker', 'Social Share & Locker Pro', 'manage_options',
                     'ism_manage', 'ism_manage', ISM_DIR_URL . 'files/images/icon.png' );
}

add_action('wp_enqueue_scripts', 'ism_fe_head');
function ism_fe_head(){
    wp_enqueue_style ( 'ism_font-awesome', ISM_DIR_URL . 'files/css/font-awesome.css' );
    wp_enqueue_style ( 'ism_style', ISM_DIR_URL . 'files/css/style-front_end.css' );
    wp_enqueue_style ( 'ism_templates', ISM_DIR_URL . 'files/css/templates.css' );
	wp_enqueue_style ( 'ism_lockers', ISM_DIR_URL . 'lockers/lockers.css' );
	//include scripts in header
	wp_enqueue_script ( 'jquery');
	wp_enqueue_script ( 'ism_jquery_ui', ISM_DIR_URL . 'files/js/jquery-ui.min.js', array(), null );
    wp_enqueue_script ( 'ism_json2', ISM_DIR_URL . 'files/js/json2.js', array(), null );
    wp_enqueue_script ( 'ism_jstorage', ISM_DIR_URL . 'files/js/jstorage.js', array(), null );
    wp_enqueue_script ( 'ism_front_end_h', ISM_DIR_URL . 'files/js/front_end_h.js', array(), null );
    wp_enqueue_script( 'ism_plusone', 'https://apis.google.com/js/plusone.js', array(), null );
    //register scripts
    wp_register_script ( 'ism_twitter', 'https://platform.twitter.com/widgets.js', array(), null );
    wp_register_script( 'ism_front_end_f', ISM_DIR_URL . 'files/js/front_end_f.js', array(), null  );
    wp_register_script( 'ism_linkedinjs', 'http://platform.linkedin.com/in.js', array(), null ); 

    //additional templates
    ism_enqueue_additional_templates();
}
add_action("admin_enqueue_scripts", 'ism_be_head');
function ism_be_head(){
    if(!isset($_REQUEST['page']) || $_REQUEST['page']!='ism_manage')return;
    wp_enqueue_style ( 'ism_style', ISM_DIR_URL . 'files/css/style-back_end.css' );
    wp_enqueue_style ( 'ism_colorpicker_css', ISM_DIR_URL . 'files/css/colorpicker.css' );
	wp_enqueue_script ( 'jquery' );
    wp_enqueue_style ( 'ism_font-awesome', ISM_DIR_URL . 'files/css/font-awesome.css' );
    wp_enqueue_style ( 'ism_templates', ISM_DIR_URL . 'files/css/templates.css' );
    wp_enqueue_style ( 'ism_front_end', ISM_DIR_URL . 'files/css/style-front_end.css' );
    wp_enqueue_style ( 'ism_lockers', ISM_DIR_URL . 'lockers/lockers.css' );
    
    if( function_exists( 'wp_enqueue_media' ) ){
        wp_enqueue_media();
        wp_enqueue_script ( 'ism_open_media_3_5', ISM_DIR_URL . 'files/js/open_media_3_5.js', array(), null );
    }else{
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script ( 'ism_open_media_3_4', ISM_DIR_URL . 'files/js/open_media_3_4.js', array(), null );
    }
    wp_enqueue_script ( 'ism_colorpicker_js', ISM_DIR_URL . 'files/js/colorpicker.js', array(), null );
    wp_enqueue_script ( 'ism_js_functions', ISM_DIR_URL . 'files/js/functions.js', array(), null );   

    if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='statistics'){
    	#statistic page
    	wp_enqueue_script ( 'ism_jquery_flot', ISM_DIR_URL . 'files/js/jquery.flot.js', array(), null );
    	wp_enqueue_script ( 'ism_jquery_flot_time', ISM_DIR_URL . 'files/js/jquery.flot.time.js', array(), null );
    	wp_enqueue_style ( 'ism_jquery-ui.min.css', ISM_DIR_URL . 'files/css/jquery-ui.min.css' );//for date picker
    	wp_enqueue_script ( 'ism_date_picker', ISM_DIR_URL . 'files/js/date_picker-jquery-ui.min.js', array(), null );//for date picker    	
    }
    
    //additional templates
    ism_enqueue_additional_templates();
}
function ism_manage(){
    require( ISM_DIR_PATH . 'includes/ism_manage.php' );
}
////////SHORTCODE
add_shortcode( 'indeed-social-media', 'ism_shortcode' );
function ism_shortcode($attr){
    $html = "";
    $js = "";
    $css = "";
    $print_outside = false;
    $shortcode_meta = ism_remove_arr_prefix($attr, 'sm_');//remove sm from each shortcode attr array key
    $meta_arr = $shortcode_meta;
    
    //Mobile
    if(isset($meta_arr['disable_mobile']) && $meta_arr['disable_mobile']==1 && ism_is_mobile() ) return '';
    
    require( ISM_DIR_PATH . 'includes/ism_view.php' );
    //for isi:
    if(isset($attr['isi_type_return']) && $attr['isi_type_return']==true){
    	$return_arr['html'] = $html;
    	$return_arr['css'] = $css;
    	return $return_arr;
    }
    //default:
    return $js . $css . $html;
}

///website display
add_action('wp_footer', 'ism_filter');
function ism_filter(){
  global $wp_query;
  $postID = $wp_query->post->ID;
  @$disable = get_post_meta($postID, 'ism_disable_wd', TRUE);
  if($disable==1) return;
  $html = "";
  $js = "";
  $css = "";
  $print_outside = false;
  $website_display = true;
  $meta_arr = ism_return_arr_val('wd');
  $meta_arr = ism_remove_arr_prefix($meta_arr, 'wd_');
  
  $meta_arr['disable_mobile'] = 1;
  
  //Mobile
  if(isset($meta_arr['disable_mobile']) && $meta_arr['disable_mobile']==1 && ism_is_mobile() ) return;
  
  if( ism_if_display($meta_arr['display_where']) ){
        require( ISM_DIR_PATH . 'includes/ism_view.php' );
        echo $js . $css . $html;
  }
}

function ism_insert_top_div($content){
    return '<div id="indeed_top_ism" class="indeed_top_ism"></div><div id="fb-root"></div>' . $content;
}
add_filter( 'the_content', 'ism_insert_top_div', 12 );

///inside display - before title
function ism_before_content_check( $content ) {
  global $wp_query;
  global $post;
  $postID = $wp_query->post->ID;
  @$disable = get_post_meta($postID, 'ism_disable_id', TRUE);
  if($disable==1) return $content;
  $html = "";
  $js = "";
  $css = "";
  $arr = ism_return_arr_val('id');
  $meta_arr = ism_remove_arr_prefix($arr, 'id_');
  
  //Mobile
  if(isset($meta_arr['disable_mobile']) && $meta_arr['disable_mobile']==1 && ism_is_mobile() ) return $content;
  
  $print_outside = false;
  if( ism_if_display($meta_arr['display_where']) ){
      require( ISM_DIR_PATH . 'includes/ism_view.php' );
  }
  switch($meta_arr['position']){
        case 'both':  //before & after
            $content = $js . $css . $html . $content . $html;
        break;
        case 'before': //before
            $content = $js . $css . $html . $content;
        break;
        case 'after':
            $content .= $js . $css . $html;
        break;
        default:
        break;
  }
  if($print_outside===true){
        //custom position
        global $ism_string_return;
        $ism_string_return = $js . $css . $html;
        add_action('wp_footer', 'ism_print_content_outside');
  }
  if( $post->post_type=='bp_members' || $post->post_type=='bp_activity' || $post->post_type=='bp_group' ) echo $content;
  else return $content;
}
add_filter( 'the_content', 'ism_before_content_check', 10 );

add_filter('get_the_excerpt', 'ism_cancel_inside_display', 5); 
function ism_cancel_inside_display($content) {
	remove_filter('the_content', 'ism_before_content_check');
	return $content;
}

function ism_print_content_outside(){
    global $ism_string_return;
    if(isset($ism_string_return) && $ism_string_return!='')
    echo $ism_string_return;
    unset($ism_string_return);
}
function ism_insert_bottom_div($content){
    return $content . "<div id='indeed_bottom_ism' class='indeed_bottom_ism'></div>";
}
add_filter( 'the_content', 'ism_insert_bottom_div', 11 );

////////SHORTCODE
add_shortcode( 'indeed-social-locker', 'ism_locker_shortcode' );
function ism_locker_shortcode($attr, $content=null, $vc_set = false){
	//REGISTERED USER 
	if(isset($attr['not_registered_u']) && $attr['not_registered_u']==1 && is_user_logged_in() == 1) return $content;

	$attr['locker_rand'] = rand( 1,5000 );
    $attr['content_id'] = "indeed_locker_content_" . $attr['locker_rand'];
    $attr['locker_div_id'] = "indeed_locker_" . $attr['locker_rand'];
    if(!isset($attr['ism_overlock']) || $attr['ism_overlock']=='' ) $attr['ism_overlock'] = 'default';
    	
	//$loker_type = 3; //default
	$ism = ism_shortcode($attr);
	include_once ISM_DIR_PATH . 'lockers/content.php';
	if(!isset($attr['locker_template']) || $attr['locker_template']=='') $attr['locker_template'] = 1;
	$content_box = GetLockerContent($attr['locker_template'], $ism, $attr);
    
	///URL
    if(isset($attr['ism_url_type']) && $attr['ism_url_type']=='permalink') $url = get_permalink();
    else $url = ISM_PROTOCOL.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

    $return_str = "";
    
    //TIMEOUT
    if(isset($attr['enable_timeout_lk']) && $attr['enable_timeout_lk']==1 && isset($attr['sm_timeout_locker'])) $return_str .= ism_add_timeout($attr['content_id'], $attr['locker_div_id'], $attr['sm_timeout_locker']);
    if(isset($attr['reset_locker']) && $attr['reset_locker']==1 && isset($attr['locker_reset_after']) && isset($attr['locker_reset_type'])) $return_str .= ism_return_reset_after($attr['locker_reset_after'], $attr['locker_reset_type'], $url, $return_str);  
    
    if($vc_set===true){
    /////////// VISUAL COMPOSER
        $lock_msg = "<div style='text-align: center;'>".htmlspecialchars_decode($attr['sm_d_text'])."</div>";
        $return_str .= "<div id='".$attr['locker_div_id']."' style='display: none;'>" . $content_box . "</div>";
        $return_str .= "<div class='ism-before-row' data-ism_overlock='".$attr['ism_overlock']."' data-ism_url='".$url."' data-vc_set='1' data-lockerId='".$attr['locker_div_id']."' data-id='".$attr['content_id']."' style='display: none;'></div>";
    }else{ 
        $return_str .= "<div id='".$attr['content_id']."' style='display: none;'>" . do_shortcode($content) . "</div>";
        $return_str .= "<div id='".$attr['locker_div_id']."' style='display: none;' >" . $content_box . "</div>";
        $return_str .= "<div class='ism-before-row' data-ism_overlock='".$attr['ism_overlock']."' data-ism_url='".$url."' data-vc_set='0' data-lockerId='".$attr['locker_div_id']."' data-id='".$attr['content_id']."' style='display: none;'></div>";
    }
    return $return_str;
}

/////////////AJAX
add_action( 'wp_ajax_ism_a_return_counts', 'ism_a_return_counts' );
add_action('wp_ajax_nopriv_ism_a_return_counts', 'ism_a_return_counts');
function ism_a_return_counts() {
    $arr = array();
    $num = 0;
    switch($_REQUEST['sm_type']){
        //facebook
        case 'facebook':
        	$url = "http://graph.facebook.com/?id=".$_REQUEST['dir_url'];
        	$data = ism_get_data_from_url( $url );
            @$result = json_decode($data);
            if(isset($result->shares)) $num = (int)$result->shares;
        break;
        case 'twitter':
        	$url = "http://cdn.api.twitter.com/1/urls/count.json?url=".$_REQUEST['dir_url']."&callback=?";
			$data = ism_get_data_from_url( $url );
            @$result = json_decode($data);
            if(isset($result->count)) $num = (int)$result->count;
        break;
        case 'google':
        	$url = "https://plusone.google.com/u/0/_/+1/fastbutton?url=".$_REQUEST['dir_url']."&count=true";
        	$data = ism_get_data_from_url( $url );
        	if (preg_match("/window\.__SSR\s=\s\{c:\s([0-9]+)\.0/", $data, $matches)) $num = (int)$matches[1];
        break;
        case 'linkedin':
        	$url = "http://www.linkedin.com/countserv/count/share?format=jsonp&url=".$_REQUEST['dir_url'];
            $data = ism_get_data_from_url( $url );
            if(strpos($data, 'IN.Tags.Share.handleCount(')!==FALSE){
                $data = str_replace('IN.Tags.Share.handleCount(', '', $data);
                $data = str_replace(');', '', $data);
            }
            @$result = json_decode($data);
            if(isset($result->count)) $num = (int)$result->count;
        break;
        case 'pinterest':
        	$url = "http://api.pinterest.com/v1/urls/count.json?url=".$_REQUEST['dir_url'];
            $data = ism_get_data_from_url( $url );
          	@$data = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $data);
          	@$result = json_decode($data);
          	if (isset($result->count) && is_int($result->count)) $num = (int)$result->count;
        break;
        case 'stumbleupon':
        	$url = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=".$_REQUEST['dir_url'];
            $data = ism_get_data_from_url( $url );
        	@$result = json_decode($data);
            if (isset($result->result->views)) $num = (int)$result->result->views;
        break;
        case 'vk':
        	$url = 'http://vk.com/share.php?act=count&url='.$_REQUEST['dir_url'];
            $data = ism_get_data_from_url( $url );
        	if (preg_match( '/^VK\.Share\.count\(\d, (\d+)\);$/i', $data, $matches ))  $num = (int)$matches[1];
        break;
		case 'reddit':
			$url = 'http://www.reddit.com/api/info.json?url='.$_REQUEST['dir_url'];
			@$data = ism_get_data_from_url( $url );
			@$result = json_decode($data);
            if (isset($result->data->children[0]->data->score)) $num = (int)$result->data->children[0]->data->score;
		break;
		case 'print':
			$data = get_option('ism_sm_internal_counts_share');
			if($data!==FALSE){
				$arr = json_decode($data, TRUE);
				if(!isset($arr[$_REQUEST['dir_url']]['print'])) $num = 0;
				else $num = $arr[$_REQUEST['dir_url']]['print'];
			}else $num = 0;
		break;
		case 'email':
			$data = get_option('ism_sm_internal_counts_share');
			if($data!==FALSE){
				$arr = json_decode($data, TRUE);
				if(!isset($arr[$_REQUEST['dir_url']]['email'])) $num = 0;
				else $num = $arr[$_REQUEST['dir_url']]['email'];
			}else $num = 0;
		break;
    }
    $num = $num + ism_test_special_counts($_REQUEST['sm_type'], $_REQUEST['dir_url']);
    if(ism_return_min_count_sm($_REQUEST['sm_type'])!==FALSE){
    	if($num>=ism_return_min_count_sm($_REQUEST['sm_type'])) echo $num;
    	else echo '';
    }else echo $num;
    die();
}

add_action( 'wp_ajax_ism_admin_items_preview', 'ism_admin_items_preview' );
add_action('wp_ajax_nopriv_ism_admin_items_preview', 'ism_admin_items_preview');
function ism_admin_items_preview() {
    $str = '';
    $ism_list = ism_return_general_labels_sm();
    $items = array( array(
                            'type' => 'facebook',
                            'label' => '',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'twitter',
                            'label' => '',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'google',
                            'label' => '',
                            'icon' => true,
                            'count' => false
                         ),
                    array(
                            'type' => 'pinterest',
                            'label' => '',
                            'icon' => true,
                            'count' => false
                         ),
                    array(
                            'type' => 'linkedin',
                            'label' => '',
                            'icon' => true,
                            'count' => false
                         ),
                    array(
                            'type' => 'digg',
                            'label' => $ism_list['digg'],//'DiggDigg',
                            'icon' => true,
                            'count' => false
                         ),
                    array(
                            'type' => 'stumbleupon',
                            'label' => $ism_list['stumbleupon'],//'Stumbleupon',
                            'icon' => true,
                            'count' => true
                         ),
                    array(
                            'type' => 'tumblr',
                            'label' => 'Tumblr',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'vk',
                            'label' => $ism_list['vk'],//'VKontakte',
                            'icon' => true,
                            'count' => true,
                         ),
                    array(
                            'type' => 'reddit',
                            'label' => $ism_list['reddit'],//'Reddit',
                            'icon' => true,
                            'count' => true,
                         ),
                    array(
                            'type' => 'delicious',
                            'label' => $ism_list['delicious'],//'Delicious',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'weibo',
                            'label' => $ism_list['weibo'],//'Weibo',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'xing',
                            'label' => $ism_list['xing'],//'Xing',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'print',
                            'label' => $ism_list['print'],//'PrintFriendly',
                            'icon' => true,
                            'count' => true,
                         ),					 
                    array(
                            'type' => 'email',
                            'label' => $ism_list['email'],//'email',
                            'icon' => true,
                            'count' => false,
                         ),
                  );
    if($_REQUEST['template']=='ism_template_9'){
    $items = array( array(
                            'type' => 'facebook',
                            'label' => $ism_list['facebook'],//'Facebook',
                            'icon' => true,
                            'count' => true,
                         ),
                    array(
                            'type' => 'twitter',
                            'label' => $ism_list['twitter'],//'Twitter',
                            'icon' => true,
                            'count' => true,
                         ),
                    array(
                            'type' => 'google',
                            'label' => $ism_list['google'],//'Google',
                            'icon' => true,
                            'count' => true
                         ),
                    array(
                            'type' => 'pinterest',
                            'label' => $ism_list['pinterest'],//'Pinterest',
                            'icon' => true,
                            'count' => true
                         ),
                    array(
                            'type' => 'linkedin',
                            'label' => $ism_list['linkedin'],//'Linkedin',
                            'icon' => true,
                            'count' => true
                         ),
                    array(
                            'type' => 'digg',
                            'label' => $ism_list['digg'],//'DiggDigg',
                            'icon' => true,
                            'count' => false
                         ),
                    array(
                            'type' => 'stumbleupon',
                            'label' => $ism_list['stumbleupon'],//'Stumbleupon',
                            'icon' => true,
                            'count' => true
                         ),
                    array(
                            'type' => 'tumblr',
                            'label' => $ism_list['tumblr'],//'Tumblr',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'vk',
                            'label' => $ism_list['vk'],//'VKontakte',
                            'icon' => true,
                            'count' => true,
                         ),
                    array(
                            'type' => 'reddit',
                            'label' => $ism_list['reddit'],//'Reddit',
                            'icon' => true,
                            'count' => true,
                         ),
                    array(
                            'type' => 'delicious',
                            'label' => $ism_list['delicious'],//'Delicious',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'weibo',
                            'label' => $ism_list['weibo'],//'Weibo',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'xing',
                            'label' => $ism_list['xing'],//'Xing',
                            'icon' => true,
                            'count' => false,
                         ),
                    array(
                            'type' => 'print',
                            'label' => $ism_list['print'],//'PrintFriendly',
                            'icon' => true,
                            'count' => true,
                         ),	
                    array(
                            'type' => 'email',
                            'label' => $ism_list['email'],//'email',
                            'icon' => true,
                            'count' => false,
                         ),
                  );
        $align = 'vertical';
    }
    else $align = 'horizontal';
    $str .= '<div class="ism_wrap '.$_REQUEST['template'].'" >';
    foreach($items as $arr){
        $str .= ism_preview_items_be( $arr, $align );
    }
    $str .= '</div>';
    echo $str;
    die();
}
///CUSTOM METABOX
add_action( 'add_meta_boxes', 'ism_custom_field_posts' );
function ism_custom_field_posts(){
    add_meta_box('ism_disable',
                 'Social Share & Locker Settings',
                 'ism_return_meta_box',
                 'post',
                 'side',
                 'high');
}
add_action( 'add_meta_boxes', 'ism_custom_field_page' );
function ism_custom_field_page(){
    add_meta_box('ism_disable',
                 'Social Share & Locker Settings',
                 'ism_return_meta_box',
                 'page',
                 'side',
                 'high');
}
add_action('save_post', 'ism_save_post_de');
function ism_save_post_de($post_id){
    $disable_wd = get_post_meta($post_id, 'ism_disable_wd', TRUE);
    if(isset($_REQUEST['ism_disable_wd'])){
		if(isset($disable_wd)) update_post_meta($post_id, 'ism_disable_wd', $_REQUEST['ism_disable_wd']);
    	else add_post_meta($post_id, 'ism_disable_wd', $_REQUEST['ism_disable_wd'], TRUE);
	}
	if(isset($_REQUEST['ism_disable_id'])){
	$disable_id = get_post_meta($post_id, 'ism_disable_id', TRUE);
    if(isset($disable_id)) update_post_meta($post_id, 'ism_disable_id', $_REQUEST['ism_disable_id']);
    else add_post_meta($post_id, 'ism_disable_id', $_REQUEST['ism_disable_id'], TRUE);
	}
}
function ism_return_meta_box($post){
    $disable_wd = esc_html(get_post_meta($post->ID, 'ism_disable_wd', true));
    $disable_id = esc_html(get_post_meta($post->ID, 'ism_disable_id', true));
    ?>
    <script>
        function check_and_h(target, hidden){
        	if (jQuery(target).is(":checked")) jQuery(hidden).val(1);
        	else jQuery(hidden).val(0);
        }
    </script>
    <table class="ism-it-table">
		<tr>
            <td>
                <?php
                    if($disable_wd==1)$checked = 'checked="checked|"';
                    else $checked = '';
                ?>
                <input type="checkbox"  <?php echo $checked;?> onClick="check_and_h(this, '#ism_disable_wd')" />
                <input type="hidden" value="<?php echo $disable_wd;?>" id="ism_disable_wd" name="ism_disable_wd"/>
            </td>
            <td class="it-label">Disable Website Display</td>
        </tr>
		<tr>
            <td>
                <?php
                    if($disable_id==1)$checked = 'checked="checked|"';
                    else $checked = '';
                ?>
                <input type="checkbox"  <?php echo $checked;?> onClick="check_and_h(this, '#ism_disable_id')" />
                <input type="hidden" value="<?php echo $disable_id;?>" id="ism_disable_id" name="ism_disable_id"/>
            </td>
            <td class="it-label">Disable Inside Display</td>
        </tr>
	</table>
	<div class="clear"></div>
<?php
}

//send email popup
add_action( 'wp_ajax_ism_send_email_ajax_popup', 'ism_send_email_ajax_popup' );
add_action('wp_ajax_nopriv_ism_send_email_ajax_popup', 'ism_send_email_ajax_popup');
function ism_send_email_ajax_popup() {
    require ISM_DIR_PATH . "includes/send_email_popup.php";
}
add_action( 'wp_ajax_ism_sendEmail', 'ism_sendEmail' );
add_action('wp_ajax_nopriv_ism_sendEmail', 'ism_sendEmail');
function ism_sendEmail() {
	/*****************SEND EMAIL****************/
   if( isset($_REQUEST['capcha_key']) && $_REQUEST['capcha_key']!='' ){
        if(ism_capcha_a( $_REQUEST['capcha_key'] )!=$_REQUEST['capcha']){
            echo 2;
            die();
        }
   }
    $email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    $subject = $_REQUEST['subject'];
    $message = stripslashes( $_REQUEST['message'] );
    if( $_REQUEST['name']!='' && $_REQUEST['from']!='' ){
        if (!preg_match($email_regex, $_REQUEST['from'])){
            echo 0;
            die();
        }
        $headers = 'From: '.$_REQUEST['name'].' <'.$_REQUEST['from'].'>' . "\r\n";
    }
    else{
        echo 0;
        die();
    }
    if($_REQUEST['sentTo']!=''){
        $to = $_REQUEST['sentTo'];
        if(strpos($to, ',')!==false){
            //multiple adrr
            $email_arr = explode(',', $to);
            foreach($email_arr as $email){
                if (!preg_match($email_regex, $email)){
                    echo 0;
                    die();
                }
            }
        }else{
                if (!preg_match($email_regex, $to)){
                    echo 0;
                    die();
                }
        }
    }
    else{
        echo 0;
        die();
    }
    if(get_option('email_send_copy')!='') $to .= ',' . get_option('email_send_copy');

    $sent = wp_mail($to, $subject, $message, $headers ); //wp_mail($to, $subject, $message, $headers, $attachments )
    if($sent){
    	$data = get_option('ism_sm_internal_counts_share');
    	if($data!==FALSE){
    		$arr = json_decode($data, TRUE);
    		if(!isset($arr[$_REQUEST['the_url']]['email'])) $arr[$_REQUEST['the_url']]['email'] = 1;
    		else $arr[$_REQUEST['the_url']]['email'] = (int)$arr[$_REQUEST['the_url']]['email'] + 1;
    	}else $arr[$_REQUEST['the_url']]['email'] = 1;
    	$new_data = json_encode($arr);
    	if(get_option('ism_sm_internal_counts_share')!==FALSE){
    		update_option('ism_sm_internal_counts_share', $new_data);
    	}else{
    		add_option('ism_sm_internal_counts_share', $new_data);
    	}
        echo 1;
    }
    else echo 0;
   die();
}
//printfriendly counter
add_action( 'wp_ajax_ism_print_friendly', 'ism_print_friendly' );
add_action('wp_ajax_nopriv_ism_print_friendly', 'ism_print_friendly');
function ism_print_friendly() {
	$data = get_option('ism_sm_internal_counts_share');
	if($data!==FALSE){
		$arr = json_decode($data, TRUE);
		if(!isset($arr[$_REQUEST['the_url']]['print'])) $arr[$_REQUEST['the_url']]['print'] = 1;
		else $arr[$_REQUEST['the_url']]['print'] = (int)$arr[$_REQUEST['the_url']]['print'] + 1;
	}else $arr[$_REQUEST['the_url']]['print'] = 1;	
	$new_data = json_encode($arr);
	if(get_option('ism_sm_internal_counts_share')!==FALSE){
		update_option('ism_sm_internal_counts_share', $new_data);
	}else{
		add_option('ism_sm_internal_counts_share', $new_data);
	}
}

//pinterest popup
add_action( 'wp_ajax_ism_pinterest_popup', 'ism_pinterest_popup' );
add_action('wp_ajax_nopriv_ism_pinterest_popup', 'ism_pinterest_popup');
function ism_pinterest_popup() {
	echo ism_return_pinterest_popup();
	die();
}

//DELETE SPECIAL COUNTS
add_action( 'wp_ajax_ism_delete_special_count', 'ism_delete_special_count' );
add_action('wp_ajax_nopriv_ism_delete_special_count', 'ism_delete_special_count');
function ism_delete_special_count(){
	if($_REQUEST['the_url']=='all'){
		$arr = get_option('ism_special_count_all');
		$arr[$_REQUEST['the_type']] = '';
		update_option('ism_special_count_all', $arr);
	}else{
		$arr = get_option('ism_special_count_'.$_REQUEST['the_type']);
		$arr[$_REQUEST['the_url']] = '';
		update_option('ism_special_count_'.$_REQUEST['the_type'], $arr);
	}echo 1;
	die();
}

/**************************************** MINIM START COUNTS ************************************/
//UPDATE
add_action( 'wp_ajax_ism_update_min_count', 'ism_update_min_count' );
add_action('wp_ajax_nopriv_ism_update_min_count', 'ism_update_min_count');
function ism_update_min_count(){
	if(isset($_REQUEST['sm']) && isset($_REQUEST['count'])){
		if(get_option('ism_min_count')===FALSE){
			//add this new option
			$arr[$_REQUEST['sm']] = $_REQUEST['count'];
			add_option('ism_min_count', $arr);
		}else{
			//update
			$arr = get_option('ism_min_count');
			$arr[$_REQUEST['sm']] = $_REQUEST['count'];
			update_option('ism_min_count', $arr);
		}
		echo 1;
	}else echo 0;
	die();
}
//RETURN MIN COUNT START
add_action( 'wp_ajax_ism_return_min_count_table', 'ism_return_min_count_table' );
add_action('wp_ajax_nopriv_ism_return_min_count_table', 'ism_return_min_count_table'); 
function ism_return_min_count_table(){
	$ism_list = ism_return_general_labels_sm();
	@$arr = get_option('ism_min_count');
	if($arr!==FALSE && count($arr)>0){
		$str = '';
		foreach($arr as $k=>$v){
			if($v!=''){
				$str .= "<tr id='ism_count_min_sm-{$k}'>
							<td>{$ism_list[$k]}</td>
							<td>{$v}</td>
							<td>
							<i class='icon-trash' title='Delete' onClick='ism_deleteMinCount(\"{$k}\", \"#ism_count_min_sm-{$k}\");'></i>
							</td>
						</tr>";	
			}
		}
		echo $str;
	}else echo 0;
	die();
}
//DELETE MIN COUNT START
add_action( 'wp_ajax_ism_delete_min_count', 'ism_delete_min_count' );
add_action('wp_ajax_nopriv_ism_delete_min_count', 'ism_delete_min_count');
function ism_delete_min_count(){
	if(isset($_REQUEST['sm'])){
		$arr = get_option('ism_min_count');
		$arr[$_REQUEST['sm']] = '';
		update_option('ism_min_count', $arr);
		echo 1;
	}else echo 0;
	die();
}

/********************************************* PREVIEW LOCKER *********************************************/
add_action( 'wp_ajax_ism_preview_shortcode', 'ism_preview_shortcode' );
add_action('wp_ajax_nopriv_ism_preview_shortcode', 'ism_preview_shortcode');
function ism_preview_shortcode(){
	$attr = shortcode_parse_atts(stripslashes($_REQUEST['shortcode']));
	$return_str = "";
	include ISM_DIR_PATH . 'includes/shortcode_preview.php';
	echo $return_str;
	die();	
}


/******************************************** VISUAL COMPOSER ***************************************/
add_action( 'init', 'ism_check_vc' );
function ism_check_vc(){
    if(function_exists('vc_map')){
        require ISM_DIR_PATH . 'includes/ism_vc_functions.php';
        require ISM_DIR_PATH . 'includes/ism_vc_map.php';

        ////////////////style & script for page
        add_action("admin_enqueue_scripts", 'ism_vc_admin_header');
        function ism_vc_admin_header(){
            wp_enqueue_style ( 'ism_font-awesome', ISM_DIR_URL . 'files/css/font-awesome.css' );
            wp_enqueue_style ( 'ism_back_end_vc', ISM_DIR_URL . 'files/css/style-back_end.css' );
            wp_enqueue_style ( 'ism_front_end_vc', ISM_DIR_URL . 'files/css/style-front_end.css' );
            wp_enqueue_style ( 'ism_templates', ISM_DIR_URL . 'files/css/templates.css' );

            wp_enqueue_script( 'ism_js_functions_vc', ISM_DIR_URL . 'files/js/functions.js', array(), null );
            //additional templates
            ism_enqueue_additional_templates();
        }
    }
}

if (!function_exists('vc_theme_before_vc_row')){
	function vc_theme_before_vc_row($atts, $content = null) {
		if(isset($atts['sm_list']) && $atts['sm_list']!='' && array_key_exists('sm_d_text', $atts) ){
			$arr_keys = array('sm_list', 'template', 'list_align', 'display_counts', 'display_full_name', 'sm_lock_bk', 'sm_lock_padding', 'sm_d_text', 'locker_template', 'sm_timeout_locker', 'enable_timeout_lk', 'not_registered_u', 'checkbox_reset_locker', 'locker_reset_after', 'locker_reset_type', 'ism_overlock');
			foreach($arr_keys as $v){
				if(isset($atts[$v])) $arr[$v] = $atts[$v];	
			}
			if(isset($arr) && count($arr)>0){
				$str = ism_locker_shortcode( $arr, $content, true);
			}
			else $str = $content;
			return $str;
		}
	}
}


/****************************************** SHARE COUNTS FROM DB ****************************************/
add_action( 'wp_ajax_ism_get_sm_db_share_counts_return_list', 'ism_get_sm_db_share_counts_return_list' );
add_action('wp_ajax_nopriv_ism_get_sm_db_share_counts_return_list', 'ism_get_sm_db_share_counts_return_list');
function ism_get_sm_db_share_counts_return_list(){
	$url = $_REQUEST['the_url'];
	$sm_list = $_REQUEST['sm_list'];
	$data = get_option('ism_sm_internal_counts_share');
	
	if($data!==FALSE){
		/**************************** IF OPTION "ism_sm_internal_counts_share" EXISTS ************************/
		$arr = json_decode($data, TRUE);//if return an associative array from json
		if(isset($arr[$url]) && count($arr[$url])!=''){
			//IF URL KEY EXISTS
			foreach($sm_list as $sm){
				if( isset( $arr[$url][$sm] ) ){
					if( isset($arr[$url][$sm.'-flag']) && $arr[$url][$sm.'-flag']>0 && $arr[$url][$sm.'-flag']<IMTST_FLAG_LIMIT ){
						//update 
						//$num = (int)ism_update_sm_db_share_counts($url, $sm);
						$current = $arr[$url][$sm];
						$current_from_server = ism_get_share_counts_from_server($url, $sm);
							if($current!=$current_from_server){
								//update sm counts
								$num = (int)ism_update_sm_db_share_counts($url, $sm);
							}else{
								//increment flag 
								ism_increment_flag($url, $sm);
								$num = $current;
							}
					}else{
						$num = (int)$arr[$url][$sm];
					}
				}else{
					$num = ism_update_sm_db_share_counts($url, $sm);
				}
				//the count
				$new_arr[$sm] = $num;
			}			
		}else{
			//CREATE NEW ARRAY INTO "ism_sm_internal_counts_share"
			foreach($sm_list as $sm){
				$arr[$url][$sm] = ism_get_share_counts_from_server($url, $sm);
				$arr[$url][$sm.'-flag'] = 0;
			}
			$data2 = json_encode($arr);
			update_option('ism_sm_internal_counts_share', $data2);
			$new_arr = $arr[$url];
		}
	}else{
		/**************************** FIRST TIME INITIALIZE the "ism_sm_internal_counts_share" OPTION ************************/
		foreach($sm_list as $sm){
			$new_arr[$sm] = ism_get_share_counts_from_server($url, $sm);
			$new_arr[$sm.'-flag'] = 0;
		}
		$new_arr2[$url] = $new_arr;
		$data2 = json_encode($new_arr2);
		add_option('ism_sm_internal_counts_share', $data2);
	}
	
	//MIN COUNTS AND INITIAL COUNTS
	foreach($new_arr as $key=>$value){
		$new_arr[$key] = $value + ism_test_special_counts( $key, $url );
		if(ism_return_min_count_sm($key)!==FALSE){
			if($new_arr[$key]<(int)ism_return_min_count_sm($key)) $new_arr[$key] = 'not_show';
		}
	}
	
	//RETURNING THE VALUES
	echo json_encode($new_arr);
	die();
}

function ism_update_sm_db_share_counts($url, $sm){
	$data = get_option('ism_sm_internal_counts_share');
	if($data!==FALSE){
		$arr = json_decode($data, TRUE);
	}
	$arr[$url][$sm] = ism_get_share_counts_from_server($url, $sm);
	$arr[$url][$sm.'-flag'] = 0;
	$data = json_encode($arr);
	if(get_option('ism_sm_internal_counts_share')===FALSE){
		//add option
		add_option('ism_sm_internal_counts_share', $data);
	}else{
		//update
		update_option('ism_sm_internal_counts_share', $data);
	}
	return $arr[$url][$sm];
}

function ism_increment_flag($url, $sm){
	$data = get_option('ism_sm_internal_counts_share');
	if($data!==FALSE){
		$arr = json_decode($data, TRUE);
		if(isset($arr[$url][$sm.'-flag'])){
			$arr[$url][$sm.'-flag'] = $arr[$url][$sm.'-flag'] + 1;
		}
		$data = json_encode($arr);
		update_option('ism_sm_internal_counts_share', $data);
	}	
}

function ism_get_share_counts_from_server($the_url, $sm){
	$num = 0;
	switch($sm){
		case 'facebook':
			$url = "http://graph.facebook.com/?id=".$the_url;
			$data = ism_get_data_from_url( $url );
			@$result = json_decode($data);
			if(isset($result->shares)) $num = (int)$result->shares;
			break;
		case 'twitter':
			$url = "http://cdn.api.twitter.com/1/urls/count.json?url=".$the_url."&callback=?";
			$data = ism_get_data_from_url( $url );
			@$result = json_decode($data);
			if(isset($result->count)) $num = (int)$result->count;
			break;
		case 'google':
			$url = "https://plusone.google.com/u/0/_/+1/fastbutton?url=".$the_url."&count=true";
			$data = ism_get_data_from_url( $url );
			if (preg_match("/window\.__SSR\s=\s\{c:\s([0-9]+)\.0/", $data, $matches)) $num = (int)$matches[1];
			break;
		case 'linkedin':
			$url = "http://www.linkedin.com/countserv/count/share?format=jsonp&url=".$the_url;
			$data = ism_get_data_from_url( $url );
			if(strpos($data, 'IN.Tags.Share.handleCount(')!==FALSE){
				$data = str_replace('IN.Tags.Share.handleCount(', '', $data);
				$data = str_replace(');', '', $data);
			}
			@$result = json_decode($data);
			if(isset($result->count)) $num = (int)$result->count;
			break;
		case 'pinterest':
			$url = "http://api.pinterest.com/v1/urls/count.json?url=".$the_url;
			$data = ism_get_data_from_url( $url );
			@$data = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $data);
			@$result = json_decode($data);
			if (isset($result->count) && is_int($result->count)) $num = (int)$result->count;
			break;
		case 'stumbleupon':
			$url = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=".$the_url;
			$data = ism_get_data_from_url( $url );
			@$result = json_decode($data);
			if (isset($result->result->views)) $num = (int)$result->result->views;
			break;
		case 'vk':
			$url = 'http://vk.com/share.php?act=count&url='.$the_url;
			$data = ism_get_data_from_url( $url );
			if (preg_match( '/^VK\.Share\.count\(\d, (\d+)\);$/i', $data, $matches ))  $num = (int)$matches[1];
			break;
		case 'reddit':
			$url = 'http://www.reddit.com/api/info.json?url='.$the_url;
			@$data = ism_get_data_from_url( $url );
			@$result = json_decode($data);
			if (isset($result->data->children[0]->data->score)) $num = (int)$result->data->children[0]->data->score;
			break;
		case 'print':
			$data = get_option('ism_sm_internal_counts_share');
			if($data!==FALSE){
				$arr = json_decode($data, TRUE);
				if(!isset($arr[$the_url][$sm]) || $arr[$the_url][$sm]=='') $num = 0;
				else $num = $arr[$the_url][$sm];
			}else $num = 0;
			break;
		case 'email':
			$data = get_option('ism_sm_internal_counts_share');
			if($data!==FALSE){
				$arr = json_decode($data, TRUE);
				if(!isset($arr[$the_url][$sm]) || $arr[$the_url][$sm]=='') $num = 0;
				else $num = $arr[$the_url][$sm];
			}else $num = 0;
			break;
	}
	return $num;
}


add_action( 'wp_ajax_ism_update_db_share_count_share_bttn_action', 'ism_update_db_share_count_share_bttn_action' );
add_action('wp_ajax_nopriv_ism_update_db_share_count_share_bttn_action', 'ism_update_db_share_count_share_bttn_action');
function ism_update_db_share_count_share_bttn_action(){
	$sm = $_REQUEST['sm'];
	$url = $_REQUEST['the_url'];
	$data = get_option('ism_sm_internal_counts_share');
	
	if($data!==FALSE){
		$arr = json_decode($data, TRUE);
	}
	if($sm!='email' && $sm!='print'){
		$arr[ $url ][ $sm.'-flag' ] = 1;//flag can be 1 or 0, when 1 it will update the value of social media share counts on next refresh		
	}
	$new_data = json_encode($arr);
	update_option('ism_sm_internal_counts_share', $new_data);
	die();
}

/******************** TWITTER META HEAD TAGS ****************************/
add_action('wp_head', 'ism_twitter_meta_tags');
function ism_twitter_meta_tags(){
	$enable = get_option('ism_twitter_share_img');
	if($enable==1){
		global $post;
		//FEATURE IMAGE
		@$feature_img = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
		$str = '<meta name="twitter:card" content="photo" />
<meta name="twitter:image:src" id="twitter_meta_img" content="'.$feature_img.'">
<meta name="twitter:url" content="'.get_site_url().'" />
		';
		echo $str;
	}
}

/********************** MOBILE DISPLAY ************************/
//add_action('wp_footer', 'ism_mobile_display');
function ism_mobile_display(){
	if(!ism_is_mobile()) return;

	$html = "";
	$js = "";
	$css = "";
	$print_outside = false;
	$meta_arr = ism_return_arr_val('md');
	$meta_arr = ism_remove_arr_prefix($meta_arr, 'md_');
	$meta_arr['is_mobile'] = true;
	//if NO items return
	if(!isset($meta_arr['list']) || $meta_arr['list']=='')return; 

	if( ism_if_display($meta_arr['display_where']) ){
		require( ISM_DIR_PATH . 'includes/ism_view.php' );
		echo $js . $css . $html;
	}
}


#share count database with date
add_action('init', 'ism_create_share_table');
function ism_create_share_table(){
	#check if table WP_ISM_SHARE exists, if not create it
	global $wpdb;
	$table_name = $wpdb->prefix . "ism_share_counts";
	if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $table_name . " (
		id int(9) NOT NULL AUTO_INCREMENT,
		sm varchar(10) DEFAULT NULL,
		url varchar(200) DEFAULT NULL,
		ism_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`)
		);";
		dbDelta ( $sql );
	}
}
add_action( 'wp_ajax_ism_update_share_db_wd', 'ism_update_share_db_wd' );
add_action('wp_ajax_nopriv_ism_update_share_db_wd', 'ism_update_share_db_wd');
function ism_update_share_db_wd(){
	if(isset($_REQUEST['sm']) && isset($_REQUEST['the_url'])){
		global $wpdb;
		$wpdb->query("INSERT INTO ".$wpdb->prefix."ism_share_counts VALUES (NULL, '".$_REQUEST['sm']."', '".$_REQUEST['the_url']."', NULL);");
	}
	die();
}

#Clear statistics
add_action( 'wp_ajax_ism_delete_statistic_data', 'ism_delete_statistic_data' );
add_action('wp_ajax_nopriv_ism_delete_statistic_data', 'ism_delete_statistic_data');
function ism_delete_statistic_data(){
	if(isset($_REQUEST['older_than'])){
		switch($_REQUEST['older_than']){
			case 'day':
				$date = date('Y-m-d H:i:s', time()-(24 * 60 * 60));
			break;
			case 'week':
				$date = date('Y-m-d H:i:s', time()-(7 * 24 * 60 * 60));
			break;
			default:
				#month
				$date = date('Y-m-d H:i:s', time()-(30 * 24 * 60 * 60));
			break;			
		}
		global $wpdb;
		$q = $wpdb->query( "DELETE FROM {$wpdb->prefix}ism_share_counts WHERE ism_date<='{$date}';" );
		echo $date;
	}
	die();
}
?>