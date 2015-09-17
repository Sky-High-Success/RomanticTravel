<?php
	$attr['locker_rand'] = rand( 1,5000 );
	$attr['content_id'] = "indeed_locker_content_" . $attr['locker_rand'];
	$attr['locker_div_id'] = "indeed_locker_" . $attr['locker_rand'];
	$shortcode_meta = ism_remove_arr_prefix($attr, 'sm_');//remove sm from each shortcode attr array key
	$meta_arr = $shortcode_meta;

	$url = '';
    $post_title = '';
    $description = '';
    $feat_img = "";

    $rand = rand(1,5000);
    $indeed_wrap_id = 'indeed_sm_wrap_' . $rand;
    $before_wrap_id = 'indeed_before_wrapD_' . $rand;
    if(!isset($attr['locker_div_id'])) $attr['locker_div_id'] = 0;
    else $attr['locker_div_id'] = '#' . $attr['locker_div_id'];
    if(!isset($attr['content_id'])) $attr['content_id'] = 0;
    else $attr['content_id'] = '#' . $attr['content_id'];
	if(isset($ismitems_arr)) unset($ismitems_arr);
	
	//labels
	$ism_list = ism_return_general_labels_sm(true);
    
    $html = "";
    $css = "";

    /*******************CSS***************/
    $aditional_css = '';
    $css .= "
                #$indeed_wrap_id{
            ";
    //position
    if(isset($meta_arr['floating'])){
        $css .= "position: ";
        if($meta_arr['floating']=='no') $css .= "absolute;";
        else $css .= "fixed;";
    }
    if(isset($meta_arr['top_bottom'])){
        //top or bottom
            if(isset($meta_arr['top_bottom_type'])) $type = $meta_arr['top_bottom_type'];
            else $type = 'px';
        $css .= "{$meta_arr['top_bottom']} : {$meta_arr['top_bottom_value']}$type;";
        //left or right
             if(isset($meta_arr['left_right_type'])) $type = $meta_arr['left_right_type'];
             else $type = 'px';
        $css .= "{$meta_arr['left_right']} : {$meta_arr['left_right_value']}$type;";
    }
    if(isset($meta_arr['position']) && $meta_arr['position']=='custom'){
        $css .= "display: none;";
        $css .= "position: absolute;";
        $print_outside = true;
    }
    $css .= "}";
    $css .= "#$indeed_wrap_id .ism_item{
                display: ";
    if($meta_arr['list_align']=='vertical'){
        ////VERTICAL ALIGN
        $css .= "block;";
        if((isset($meta_arr['position']) && $meta_arr['position']=='custom') || (isset($website_display) && $website_display==true) ){
            $margin_arr = array(
                                    'ism_template_0' => '4px 0px;',
                                    'ism_template_1' => '4px 0px;',
                                    'ism_template_2' => '4px 0px;',
                                    'ism_template_3' => '4px 0px;',
                                    'ism_template_4' => '7px 0px;',
                                    'ism_template_5' => '',
                                    'ism_template_6' => '7px 0px;',
                                    'ism_template_7' => '4px 0px;',
                                    'ism_template_8' => '4px 0px;',
                                    'ism_template_9' => '',
                                    'ism_template_10' => '3px 0px;',
                               );
            if(isset($margin_arr[$meta_arr['template']]) && $margin_arr[$meta_arr['template']]!='') $css .= 'margin: ' . $margin_arr[$meta_arr['template']] . ' !important;';
        }
    }else{
        ////HORIZONTAL ALIGN
        $css .= "inline-block;";
        if((isset($meta_arr['position']) && $meta_arr['position']=='custom') || (isset($website_display) && $website_display==true) ){
            $margin_arr = array(
                                    'ism_template_0' => '0px 4px;',
                                    'ism_template_1' => '0px 4px;',
                                    'ism_template_2' => '0px 4px;',
                                    'ism_template_3' => '0px 4px;',
                                    'ism_template_4' => '0px 7px;',
                                    'ism_template_5' => '',
                                    'ism_template_6' => '0px 7px;',
                                    'ism_template_7' => '0px 4px;',
                                    'ism_template_8' => '0px 4px;',
                                    'ism_template_9' => '',
                                    'ism_template_10' => '0px 3px;',
                               );
            if(isset($margin_arr[$meta_arr['template']]) && $margin_arr[$meta_arr['template']]!='') $css .= 'margin: ' . $margin_arr[$meta_arr['template']] . ' !important;';
        }
    }
            //CUSTOM TOP TEMPLATE 5
            if(isset($meta_arr['top_bottom']) && $meta_arr['top_bottom']=='top' && $meta_arr['template']=='ism_template_5'){
                $css .= '
                        	-webkit-box-shadow: inset 0px 6px 0px 0px rgba(0,0,0,0.2);
                        	-moz-box-shadow: inset 0px 6px 0px 0px rgba(0,0,0,0.2);
                        	box-shadow: inset 0px 6px 0px 0px rgba(0,0,0,0.2);
                        ';
                $aditional_css = '#'.$indeed_wrap_id.' .ism_item:hover{
                                        top:initial !important;
										bottom: -1px !important;		
                                  }';
            }
            //CUSTOM RIGHT FOR TEMPLATE 9
            if(isset($meta_arr['left_right']) && $meta_arr['left_right']=='right' && $meta_arr['template']=='ism_template_9'){
                $css .= '
                        	-webkit-box-shadow: inset -8px 0px 5px 0px rgba(0,0,0,0.2);
                        	-moz-box-shadow: inset -8px 0px 5px 0px rgba(0,0,0,0.2);
                        	box-shadow: inset -8px 0px 5px 0px rgba(0,0,0,0.2);
                        	border-top-left-radius:5px;
                        	border-bottom-left-radius:5px;
                        	margin-right:-5px;
                        ';
                $aditional_css = '#'.$indeed_wrap_id.' .ism_item:hover{
									   left: initial !important;	
                                       right: 5px !important;
                                    }';
            }
    $css .= "}"; //end of .ism_item style
    $css .= $aditional_css;

    /******************HTML****************************/
    $inline_style = '';
    if(isset($attr['locker_div_id']) && $attr['locker_div_id']!='') $inline_style = 'text-align: center;display: block;';
    $html .= '<div class="ism_wrap '.$meta_arr['template'].'" id="'.$indeed_wrap_id.'" style="'.$inline_style.'" >';

    //facebook
    if(strpos($meta_arr['list'], 'fb')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'javascript:void(0)';
        $args['sm_type'] = 'fb';
        $args['sm_class'] = 'facebook';
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['facebook'];//'Facebook';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'facebook';
        }
        $html .= ism_return_item($args);
    }

    //twitter
    if(strpos($meta_arr['list'], 'tw')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        @$twitter_name = get_option('twitter_name');
        $args['link'] = 'javascript:void(0)';
        if( isset($twitter_name) && $twitter_name!='' ) $args['link'] .= ' %40'.$twitter_name;
        $args['onClick'] = '';
        $args['sm_type'] = 'tw';
        $args['sm_class'] = 'twitter';
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['twitter'];//'Twitter';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'twitter';
        }
        $html .= ism_return_item($args);
    }

    //google +1button
    if(strpos($meta_arr['list'], 'go1')!==FALSE){
    	$html .= '
			    	<style>
				    	.ismgplusbutton{
				    	position:relative;
					    }
					    #ism_custom_gplus_'.$attr['locker_rand'].'{
						    margin:0 auto;
						    position:absolute;
						    width: 100%;
						    height: 100%;
						    opacity:0;
						    text-align:center;
						    top:0px;
					    }
				    </style>
    			';
    	$html .= '
    	<a href="#" class="ism_link">
    	<div class="ism_item ism_box_google" >
    	<i class="fa-ism fa-google-ism"></i>';
    	if($meta_arr['display_full_name']=='true') $html .= '<span class="ism_share_label" >'.$ism_list['google_plus'].'</span>';
    	if($meta_arr['display_counts']){
    		$ismitems_arr[] = 'google';
    		if(ism_return_min_count_sm('google')) $html .= '<span class="ism_share_counts google_share_count" ></span>';
    		else $html .= '<span class="ism_share_counts google_share_count" >0</span>';
    	}
    	$html .= '
    	<div class="ismgplusbutton">
	    	<div id="ism_custom_gplus_'.$attr['locker_rand'].'" >
		    	<div class="g-plusone" data-callback="gpRemoveLocker_'.$attr['locker_rand'].'"  data-locker="bla" data-annotation="none" data-recommendations="false" data-href="'.$url.'"></div>
		    	<div class="g-plusone" data-callback="gpRemoveLocker_'.$attr['locker_rand'].'"  data-locker="bla" data-annotation="none" data-recommendations="false" data-href="'.$url.'"></div>
	    	</div>
    	</div>
    	</div>
    	</a>
    	';
    }

    //linkedin
    if(strpos($meta_arr['list'], 'li')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();      
		$args['link'] = 'javascript:void(0)';
        $args['sm_type'] = 'li';
        $args['sm_class'] = 'linkedin';
        $args['custom_height'] = 450;
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['linkedin'];
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'linkedin';
        }
        $html .= ism_return_item($args);
    }

    $html .= "</div>";

    include_once ISM_DIR_PATH . 'lockers/content.php';
    $content_box = GetLockerContent($attr['locker_template'], $html, $attr);
    $return_str .= "<style>" . $css . "</style>";
    $return_str .= "<div id='".$attr['content_id']."' style='display: none;'> TEST </div>";
    $return_str .= "<div id='".$attr['locker_div_id']."' style='display: block;' >" . $content_box . "</div>";
    $return_str .= "<div class='ism-before-row' data-ism_url='".$url."' data-vc_set='0' data-lockerId='".$attr['locker_div_id']."' data-id='".$attr['content_id']."' style='display: none;'></div>";
    
?>