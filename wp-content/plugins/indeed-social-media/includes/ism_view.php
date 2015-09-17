<?php
    global $post;

	$url = ism_return_current_url(); //url or permalink
    $post_title = ism_return_post_title(); // the title
	$description = ism_return_post_description(); // post description
    $feat_img = ism_return_feat_image($meta_arr); //feature image

    $rand = rand(1,5000);
    $indeed_wrap_id = 'indeed_sm_wrap_' . $rand;
    $before_wrap_id = 'indeed_before_wrapD_' . $rand;
    if(!isset($attr['locker_div_id'])) $attr['locker_div_id'] = 0;
    else $attr['locker_div_id'] = '#' . $attr['locker_div_id'];
    if(!isset($attr['content_id'])) $attr['content_id'] = 0;
    else $attr['content_id'] = '#' . $attr['content_id'];
	if(isset($ismitems_arr)) unset($ismitems_arr);
	
	//labels
	global $ism_list;
	if(!isset($ism_list) || $ism_list==false){
		$ism_list = ism_return_general_labels_sm(true);
	}
    
    $html = '';//string to print on front end
    $html_arr = array();//html_arr will contain all social media html items
    
    
    /************************JS****************/
    if(!defined('ISM_BASE_PATH_JS')){
    	wp_localize_script( 'ism_front_end_f', 'ism_base_path', get_site_url() );// base url for ajax calls
    	define('ISM_BASE_PATH_JS', 1);//include variable just one time
    }
    /************************JS****************/
    
    
    /****************************** MOBILE CSS ***********************************/
    $mobile_css = '';

    if(isset($meta_arr['is_mobile']) && $meta_arr['is_mobile']==true){ //available only in ism_mobile_display()
    	$mobile_css .= '#'.$indeed_wrap_id.'{';//open #$indeed_wrap_id

    	if(!isset($meta_arr['mobile_special_template']) || $meta_arr['mobile_special_template']==''){
    		# ZOOM & OPACITY, not available in mobile special template
    		if(isset($meta_arr['opacity']) && $meta_arr['opacity']!='') $mobile_css .= 'opacity: '.$meta_arr['opacity'].';';
    		if(isset($meta_arr['zoom']) && $meta_arr['zoom']!='') $mobile_css .= 'zoom: '.$meta_arr['zoom'].';';
    	}
    	
    	if(isset($meta_arr['mobile_special_template']) && $meta_arr['mobile_special_template']!=''){
    		//RE-write the template variable
    		$meta_arr['template'] = $meta_arr['mobile_special_template'];
    		if($meta_arr['mobile_special_template'] == 'ism_template_mob_1' || $meta_arr['mobile_special_template'] == 'ism_template_mob_2'){
    			$mobile_css .= 'left: 0px;';
    			$mobile_css .= 'width: 100%;';    			
    		}
    		//mobile predefined position
    		$mobile_css .= 'bottom: 0px;';
    		$mobile_css .= 'position: fixed;';
    		$mobile_css .= 'text-align: center;';
    		unset($meta_arr['top_bottom']); //unset the top bottom options
    		$meta_arr['list_align'] = 'horizontal';//for mobile predefined position only horizontal align it's ok
    	}elseif(isset($meta_arr['predefined_position']) && $meta_arr['predefined_position']!='' && isset($meta_arr['custom_position']) && $meta_arr['custom_position']==0){
    		//mobile predefined position
    		$mobile_css .= $meta_arr['predefined_position'].': 0px;';
    		$mobile_css .= 'position: fixed;';
    		$mobile_css .= 'text-align: center;';
    		$mobile_css .= 'width: 100%;';//
    		if(isset($meta_arr['behind_bk']) && $meta_arr['behind_bk']==1){
    			$mobile_css .= 'background: rgba(255,255,255, 0.7);';
    			$mobile_css .= 'padding: 7px 0px;';
    		}
    		unset($meta_arr['top_bottom']); //unset the top bottom options
    		$meta_arr['list_align'] = 'horizontal';//for mobile predefined position only horizontal align it's ok  		
    	}
    	$mobile_css .= '}';//close #$indeed_wrap_id
    	
    	if(isset($meta_arr['mobile_special_template']) && $meta_arr['mobile_special_template'] == 'ism_template_mob_1'){
    		$mobile_css .= '#'.$indeed_wrap_id.' .ism_item{';//open	#$indeed_wrap_id .ism_item
    		$sm_num_count = count(explode(",",$meta_arr['list']));
    		if($sm_num_count>4){
    			$mobile_css .= "width: 25%;";
    		}else{
    			$sm_item_width = 100/$sm_num_count;
    			$mobile_css .= "width: ".$sm_item_width."%;";
    		}
    		$mobile_css .= '}';//close	#$indeed_wrap_id .ism_item
    	}    	
    }

    /****************************** END OF MOBILE CSS ***********************************/
    
    
    /************************************ MAIN CSS *********************************/
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
	/**************************** END OF CSS **************************************/
     

    
    /************************* SOCIAL MEDIA ITEMS *************************/
    //facebook
    if(strpos($meta_arr['list'], 'fb')!==FALSE){
    	$fb_rand = rand(1, 5000);
        $html .= '<div id="fb_desc-'.$fb_rand.'" data-ism_description="'.$description.'"></div>';
    	if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'javascript:void(0)';
		$fb_id = get_option('facebook_id');
     	if($fb_id!=false && $fb_id!=''){
	     	if(!defined('ISM_FB_APP_ID')){
	     		wp_localize_script( 'ism_front_end_f', 'ism_facebook_id', $fb_id );
	     		define('ISM_FB_APP_ID', 1);
	     	}     		
        	$args['onClick'] = 'shareOnFacebook( \''.$post_title.'\', \''.$url.'\', \''.$feat_img.'\', \''.$attr['locker_div_id'].'\', \''.$attr['content_id'].'\', \'#fb_desc-'.$fb_rand.'\' );';
        }else{
              $args['onClick'] = 'shareFacebookWI(\''.$url.'\',\''.$post_title.'\', \''.$attr['locker_div_id'].'\', \''.$attr['content_id'].'\', \'#fb_desc-'.$fb_rand.'\');';
        }
        $args['sm_type'] = 'fb';
        $args['sm_class'] = 'facebook';
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['facebook'];//'Facebook';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'facebook';
        }
        
        $html_arr[] = $args;
    }

    //twitter
    if(strpos($meta_arr['list'], 'tw')!==FALSE){   	
        if(isset($args)) unset($args);
        $args = array();
        @$twitter_name = get_option('twitter_name');
        
        if(isset($attr['locker_div_id']) && $attr['locker_div_id']!='0'){
        	/********************* LOCKER **********************/
        	if(!defined('ISM_TW_SET')){
        		wp_localize_script( 'ism_front_end_f', 'ism_twitter_set', '1' );
        		wp_enqueue_script('ism_twitter');
        		define('ISM_TW_SET', 1);
        	}
        	
        	$args['link'] = 'https://twitter.com/intent/tweet?text='.$post_title.' '.$url;
        	$args['onClick'] = 'setIds(\''.$attr['locker_div_id'].'\', \''.$attr['content_id'].'\', \''.$url.'\');';
        }else{
        	/****************** WIDHOUT LOCKER ***********/
        	$args['link'] = 'https://twitter.com/intent/tweet?text='.$post_title.' '.$url;
        	$args['new_window'] = true;
        	$args['twitter_href'] = true;
        	$args['onClick'] = '';
        } 
        
        if( isset($twitter_name) && $twitter_name!='' ) $args['link'] .= ' %40'.$twitter_name;
        $args['sm_type'] = 'tw';
        $args['sm_class'] = 'twitter';
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['twitter'];//'Twitter';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'twitter';
        }
        
        $html_arr[] = $args;
    }

    //google plus
    if(strpos($meta_arr['list'], 'goo')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'https://plus.google.com/share?url='.$url;
        $args['new_window'] = true;
        $args['sm_type'] = 'goo';
        $args['sm_class'] = 'google';
        $args['onClick'] = '';
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['google'];//'Google+';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'google';
        }
        
        $html_arr[] = $args;
    }
      
    //google +1button  ############## AVAILABLE ONLY IN LOCKER #################
    if(strpos($meta_arr['list'], 'go1')!==FALSE){
    	if( isset($meta_arr['display_counts']) && $meta_arr['display_counts']=='true' ){
    		$ismitems_arr[] = 'google';
    	}
    	$args['google_plus_one'] = true;
    	$args['locker_rand'] = $attr['locker_rand'];
    	$args['display_counts'] = $meta_arr['display_counts'];
    	$args['display_full_name'] = $meta_arr['display_full_name'];
    	if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['google_plus'];//'Google+';
    	$args['url'] = $url;
    	$html_arr[] = $args;
    }

    //pinterest
    if(strpos($meta_arr['list'], 'pt')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        if(isset($meta_arr['ISI_image']) && $meta_arr['ISI_image']==TRUE){
        	//ISI
        	$args['link'] = 'http://pinterest.com/pin/create/bookmarklet/?media='.$feat_img.'&amp;url='.$url.'&amp;title='.$post_title.'&amp;description='.$description;
        	$args['new_window'] = true;
        	$args['onClick'] = '';
        }else{
        	//DEFAULT
        	$rand_pin = rand(1,10000);
        	$html .= '<input type="hidden" value="&amp;url='.$url.'&amp;title='.$post_title.'&amp;description='.$description.'" id="pin_hide_info_'.$rand_pin.'"/>';
        	$html .= '<input type="hidden" value="'.$feat_img.'" id="pin_default_feat_img_'.$rand_pin.'" />';
        	$args['link'] = 'javascript:void(0)';
        	$args['onClick'] = 'indeedPinterestPopUp('.$rand_pin.');';
        }
        $args['sm_type'] = 'pt';
        $args['sm_class'] = 'pinterest';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'pinterest';
        }
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['pinterest'];//'Pinterest';
        
        $html_arr[] = $args;
    }

    //linkedin
    if(strpos($meta_arr['list'], 'li')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();      
        if( $attr['locker_div_id']!==0 && $attr['content_id']!==0 ){
        	//locker
        	wp_enqueue_script('ism_linkedinjs');
        	$args['link'] = 'javascript:void(0)';
        	$args['onClick'] = 'ism_linkedin_share(\''.$url.'\', \''.$attr['locker_div_id'].'\', \''.$attr['content_id'].'\');';
        }else{
        	$args['link'] = 'http://www.linkedin.com/shareArticle?mini=true&amp;url='.$url.'&title='.$post_title.'&summary='.$description;
        	$args['new_window'] = true;
        }
        $args['sm_type'] = 'li';
        $args['sm_class'] = 'linkedin';
        $args['custom_height'] = 450;
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['linkedin'];//'Linkedin';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'linkedin';
        }
        
        $html_arr[] = $args;
    }

    //digg
    if(strpos($meta_arr['list'], 'dg')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['new_window'] = true;
        $args['link'] = 'http://digg.com/submit?phase=2%20&amp;url='.$url.'&title='.$post_title;
        $args['sm_type'] = 'dg';
        $args['sm_class'] = 'digg';
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['digg'];//'DiggDigg';
        
        $html_arr[] = $args;
    }

    //stumbleupon
    if(strpos($meta_arr['list'], 'su')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'http://www.stumbleupon.com/badge/?url='.$url.'&title='.$post_title;
        $args['new_window'] = true;
        $args['sm_type'] = 'su';
        $args['sm_class'] = 'stumbleupon';
        $args['custom_height'] = 575;
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['stumbleupon'];//'Stumbleupon';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'stumbleupon';
        }
        
        $html_arr[] = $args;
    }

    //tumblr
    if(strpos($meta_arr['list'], 'tbr')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $title = str_replace('%26', ' ', $post_title);
        $title = str_replace('%3C', ' ', $title);
        $title = str_replace('%3E', ' ', $title);
        $args['link'] = 'https://www.tumblr.com/share/link?url=' . urlencode($url) . '&name=' . $title . '&description=' . $description;
        $args['new_window'] = true;
        $args['sm_type'] = 'tbr';
        $args['sm_class'] = 'tumblr';
        $args['custom_height'] = 530;
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['tumblr'];//'Tumblr';
        
        $html_arr[] = $args;
    }

    //vkontakte
    if(strpos($meta_arr['list'], 'vk')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'http://vkontakte.ru/share.php?url='.$url.'&image='.$feat_img.'&title='.$post_title.'&description='.$description;
        $args['new_window'] = true;
        $args['sm_type'] = 'vk';
        $args['sm_class'] = 'vk';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'vk';
        }
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['vk'];//'VKontakte';
        
        $html_arr[] = $args;
    }

	//reddit
	if(strpos($meta_arr['list'], 'rd')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'http://www.reddit.com/submit?url='.$url;
        $args['new_window'] = true;
        $args['sm_type'] = 'rd';
        $args['sm_class'] = 'reddit';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'reddit';
        }
		if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['reddit'];//'Reddit';
		
		$html_arr[] = $args;
	}
	
	//delicious
	if(strpos($meta_arr['list'], 'dl')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'http://delicious.com/post?url='.$url;
        $args['new_window'] = true;
        $args['sm_type'] = 'dl';
        $args['sm_class'] = 'delicious';
		if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['delicious'];//'Delicious';
		
		$html_arr[] = $args;
	}

	//weibo
	if(strpos($meta_arr['list'], 'wb')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $title = str_replace('%26', ' ', $post_title);
        $title = str_replace('%3C', ' ', $title);
        $title = str_replace('%3E', ' ', $title);
        $args['link'] = 'http://service.weibo.com/share/share.php?url='.$url.'&appkey='.'&title='.$title.'&pic='.$feat_img;
        $args['new_window'] = true;
        $args['sm_type'] = 'wb';
        $args['sm_class'] = 'weibo';
		if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['weibo'];//'Weibo';
		
		$html_arr[] = $args;
	}
	
	//xing
	if(strpos($meta_arr['list'], 'xg')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'https://www.xing.com/social_plugins/share?url='.$url;
        $args['new_window'] = true;
        $args['sm_type'] = 'xg';
        $args['sm_class'] = 'xing';
		if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['xing'];//'Xing';
		
		$html_arr[] = $args;
	}
	
	//printfriendly
	if(strpos($meta_arr['list'], 'pf')!==FALSE){
        if(isset($args)) unset($args);
        $args = array();
        $args['link'] = 'http://www.printfriendly.com/print/?source=site&url='.$url;
        $args['new_window'] = true;
		$args['onClick'] = 'indeedPrintFriendlyCount(\''.$url.'\');';
        $args['sm_type'] = 'pf';
        $args['sm_class'] = 'print';
        $args['custom_height'] = 600;
		$args['custom_width'] = 1040;
		if($meta_arr['display_counts']=='true'){
			$args['display_counts'] = true;
			$ismitems_arr[] = 'print';
		}
		if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['print'];//'Print';
		
		$html_arr[] = $args;
	}		

    //email
    if(strpos($meta_arr['list'], 'email')!==FALSE){
        if(isset($args)) unset($args);
        $args['link'] = 'javascript:void(0)';
        $args['onClick'] = 'indeedPopUpEmail(\''.ISM_DIR_URL.'\', \''.$url.'\');';
        $args['sm_type'] = 'email';
        $args['sm_class'] = 'email';
        if($meta_arr['display_counts']=='true'){
        	$args['display_counts'] = true;
        	$ismitems_arr[] = 'email';
        }
        if($meta_arr['display_full_name']=='true') $args['label'] = $ism_list['email'];//'E-Mail';
        
        $html_arr[] = $args;
    }
    /************************* END OF SOCIAL MEDIA ITEMS *************************/	
	
    
	# MOBILE SPECIAL HTML
    if(isset($meta_arr['mobile_special_template']) && $meta_arr['mobile_special_template']!=''){ 
    	$html .= ism_special_mobile_template($html_arr, @$sm_num_count, $meta_arr['mobile_special_template'], $url, $indeed_wrap_id);
    }
    else{
	#DEFAULT HTML 
   		foreach($html_arr as $val){
   			$html .= ism_return_item($val, $url);
   		}
   }  
    //parent inline style 
	$inline_style = '';
	if(isset($attr['locker_div_id']) && $attr['locker_div_id']!='') $inline_style = 'text-align: center;display: block;';
	
	/***************** FINAL HTML **************************/
    $html = '<div class="ism_wrap '.$meta_arr['template'].'" id="'.$indeed_wrap_id.'" style="'.$inline_style.'" >' . $html . '</div>';
    

    if($print_outside==true){
        $js .= "jQuery(window).bind('load', function(){ismDisplayInsidePost('#$indeed_wrap_id', '#$before_wrap_id', '{$meta_arr['top_bottom']}', '{$meta_arr['top_bottom_value']}', '{$meta_arr['left_right']}', '{$meta_arr['left_right_value']}');});";
        $html = '<div id="'.$before_wrap_id.'" class="indeed_second_before_wrapp">' . $html . '</div>';
        $html = '<div class="indeed_before_wrapp">' . $html . '</div>';
    }
	
    #DISPLAY COUNTS
    if($meta_arr['display_counts']=='true' && isset($ismitems_arr) ) $js .= ism_display_counts_js($ismitems_arr, $url, $indeed_wrap_id);
    
    #STATISTICS
    $statistics = get_option('ism_enable_statistics');
    if(isset($statistics) && $statistics==1) $js .= 'var ism_enable_statistics=1;';

	if(isset($mobile_css) && $mobile_css!='') $css .= $mobile_css;
	$css = "<style>" . $css . "</style>";
    if(isset($js) && $js!='') $js = "<script>" . $js . "</script>";
    wp_enqueue_script('ism_front_end_f');

?>