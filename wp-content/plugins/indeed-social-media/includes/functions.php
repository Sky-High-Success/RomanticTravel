<?php
function ism_return_item($args=array(), $url=''){
	if(isset($args['google_plus_one']) && $args['google_plus_one']==true) return ism_return_googleplusone_str($args);
    $str = "";
    if(isset($args['twitter_href']) && $args['twitter_href']==true) $str .= '<a class="ism_link"';
    else $str .= '<a href="'.$args['link'].'" class="ism_link"';
    $str .= 'onClick="';
    if(isset($args['onClick']) && $args['onClick']!='') $str .= $args['onClick'];
    $str .= 'ism_fake_increment(\'.'.$args['sm_class'].'_share_count\', \''.$args['sm_class'].'\', \''.$url.'\');';
    if(isset($args['new_window'])){
		$height = 313;
		$width = 700;
      	if(isset($args['custom_height'])) $height = $args['custom_height'];
	  	if(isset($args['custom_width'])) $width = $args['custom_width'];
	  	if(isset($args['twitter_href']) && $args['twitter_href']==true) $str .= 'return !window.open(\''.$args['link'].'\', \'\', \'width='.$width.',height='.$height.'\');'; #twitter default share
		else $str .= 'return !window.open(this.href, \'\', \'width='.$width.',height='.$height.'\');';
    }
    

    
    $str .= '"';//close onClick event
    $str .= '>';
    $str .= '<div class="ism_item ism_box_'.$args['sm_class'].'">';
    
    //mobile
    $i_class = '';
    if(isset($args['i_color_class']) && $args['i_color_class']==true) $i_class = ' ism_box_'.$args['sm_class'];
    
    $str .= '<i class="fa-ism fa-'.$args['sm_class'].'-ism'.$i_class.'"></i>';
    if(isset($args['label']) && $args['label']!= '' ) $str .= '<span class="ism_share_label">'.$args['label'].'</span>';
    if(isset($args['display_counts']) ){
    	if(ism_return_min_count_sm($args['sm_class'])) $str .= '<span class="ism_share_counts '.$args['sm_class'].'_share_count" ></span>';
    	else{
			//mobile
			if(isset($args['i_color_class']) && $args['i_color_class']==true) $str .= '<span class="mob-count-label">shares</span> ';
			 $str .= '<span class="ism_share_counts '.$args['sm_class'].'_share_count" >0</span>';
		}
    }
    $str .= '<div class="clear"></div>';
    $str .= '</div>';
    $str .= '</a>';
    return $str;
}

function ism_return_googleplusone_str($args){
	$str = '';
	$str .= '
	<style>
		.ismgplusbutton{
			position:relative;
		}
		#ism_custom_gplus_'.$args['locker_rand'].'{
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
	$str .= '
	<a href="#" class="ism_link">
		<div class="ism_item ism_box_google" >
			<i class="fa-ism fa-google-ism"></i>';
	if($args['display_full_name']=='true') $str .= '<span class="ism_share_label" >'.$args['label'].'</span>';
	if( isset($args['display_counts']) && $args['display_counts']=='true' ){
		if(ism_return_min_count_sm('google')) $str .= '<span class="ism_share_counts google_share_count" ></span>';
		else $str .= '<span class="ism_share_counts google_share_count" >0</span>';
	}
	$str .= '
	<div class="ismgplusbutton">
		<div id="ism_custom_gplus_'.$args['locker_rand'].'" >
			<div class="g-plusone" data-callback="gpRemoveLocker_'.$args['locker_rand'].'"  data-locker="" data-annotation="none" data-recommendations="false" data-href="'.$args['url'].'"></div>
			<div class="g-plusone" data-callback="gpRemoveLocker_'.$args['locker_rand'].'"  data-locker="" data-annotation="none" data-recommendations="false" data-href="'.$args['url'].'"></div>
		</div>
	</div>
	<script type="text/javascript">
		gapi.plusone.go("ism_custom_gplus_'.$args['locker_rand'].'");
		function gpRemoveLocker_'.$args['locker_rand'].'(data){
			jQuery("#indeed_locker_content_' . $args['locker_rand'] . '").css({"display": "block", "opacity": 1 });
			jQuery("#indeed_locker_' . $args['locker_rand'] . '").css("display", "none");
			jQuery.jStorage.set("'.$args['url'].'", ism_return_current_date());
		}
	</script>
	</div>
	</a>
	';	
	return $str;
}

function ism_preview_items_be( $arr=array(), $align ){
    $str = '';
    $str .= '<a href="#" class="ism_link">';
    if($align=='vertical') $display = 'block';
    else $display = 'inline-block';
    $str .= '<div class="ism_item ism_box_' . $arr['type'] . '" style="display: '.$display.'">';
    if($arr['icon']==true) $str .= '<i class="fa-ism fa-' . $arr['type'] . '-ism"></i>';
    if($arr['label']!='') $str .= '<span class="ism_share_label">' . $arr['label'] . '</span>';
    if($arr['count']==true) $str .= '<span class="ism_share_counts ' . $arr['type'] . '_share_count" >' . rand(0,50) . '</span>';
    $str .= '<div class="clear"></div>';
    $str .= '</div>';
    $str .= '</a>';
    return $str;
}

function ism_checkSelected($val1, $val2, $type){
    // check if val1 is equal with val2 and return an select attribute for checkbox, radio or select tag
    if($val1==$val2){
        if($type=='select') return 'selected="selected"';
        else return 'checked="checked"';
    }else return '';
}

function ism_check_select_str($haystack, $needle){
    if(strpos($haystack, $needle)!==FALSE) return 'checked="checked"';
    else return '';
}

function ism_return_arr( $type ){
	switch($type){
		case 'wd': //website display metas
			$meta_arr = array(
					"wd_list"=>"fb,tw,li,goo,pt,dg,tbr,su,vk,rd,dl,wb,xg,pf,email",
					"wd_template"=>"ism_template_0",
					"wd_display_counts"=>'false',
					"wd_display_full_name" => 'false',
					"wd_display_where"=>"home,post,page",
					"wd_top_bottom" => "top",
					"wd_top_bottom_value" => 20,
					"wd_top_bottom_type" => "%",
					"wd_left_right" => "left",
					"wd_left_right_value" => 0,
					"wd_left_right_type" => "px",
					"wd_floating" => "yes",
					"wd_list_align" => "vertical",
					"wd_disable_mobile" => 0,
			);			
		break;
		
		case 'id': //inside display metas
			$meta_arr = array(
					"id_list"=>"fb,tw,li,goo,pt,dg,tbr,su,vk,rd,dl,wb,xg,pf,email",
					"id_template"=>"ism_template_0",
					"id_display_counts"=>'true',
					"id_display_where"=>"post,page",
					"id_display_full_name" => 'true',
					"id_list_align" => "horizontal",
					"id_position" => "before",
					"id_top_bottom" => "top",
					"id_top_bottom_value" => 20,
					"id_left_right" => "left",
					"id_left_right_value" => 20,
					"id_disable_mobile" => 0,
			);			
		break;
		
		case 'g_o': //general options metas
			$meta_arr = array(
					"twitter_name" => "",
					"ism_twitter_share_img" => 0,
					"facebook_id" => "",
					"feat_img" => ISM_DIR_URL . "files/images/wordpress-logo.png",
					"email_box_title" => "Share This Page",
					"email_subject" => "Take a look on this page #LINK#",
					"email_message" => "I've found something very interesting here.
Check the next link: #LINK#",
					"email_capcha" => 0,
					"email_send_copy" => "",
					"email_success_message" => "Thank You!",
					"ism_url_type" => "url",
					"ism_check_counts_everytime" => 0,
					"ism_enable_statistics" => 0,
					'ism_general_sm_labels' => '',
			);			
		break;
		
		case 'md': //mobile display metas
			$meta_arr = array(
				"md_list" => "fb,tw,li,goo,pt,dg,tbr,su,vk,rd,dl,wb,xg,pf,email",
				"md_template" => "ism_template_0",
				"md_display_counts" => 'false',
				"md_display_full_name" => 'false',
				"md_display_where" => "home,post,page",	
				"md_floating" => "yes",
				"md_list_align" => "horizontal",
				"md_top_bottom" => "top",
				"md_top_bottom_value" => 20,
				"md_top_bottom_type" => "%",
				"md_left_right" => "left",
				"md_left_right_value" => 0,
				"md_left_right_type" => "px",
				"md_predefined_position" => 'bottom',
				"md_zoom" => 1,
				"md_opacity" => 1,	
				"md_custom_position" => 0,		
				"md_pred_position" => 1,	
				"md_behind_bk" => 0,
				"md_mobile_special_template" => '',
			);		
		break;
	}
    return $meta_arr;
}

function ism_return_arr_val( $type ){
    $meta_arr = ism_return_arr( $type );
    foreach($meta_arr as $k=>$v){
        if( get_option( $k )===FALSE ) add_option($k, $v);
        else $meta_arr[$k] = get_option($k);
    }
    return $meta_arr;
}

function ism_return_arr_update( $type ){
    $wd_meta_arr = ism_return_arr( $type );
    foreach($wd_meta_arr as $k=>$v){
        if(get_option($k)===FALSE) add_option($k, $_REQUEST[$k]);
        else update_option($k, $_REQUEST[$k]);
    }
}

function ism_if_display($where){
    if(strpos($where, 'home')!==FALSE){
        if(is_home()) return TRUE;
    }
    if(strpos($where, 'post')!==FALSE){
        if( get_post_type(get_the_ID())== "post" && !is_home() && !is_category() && !is_tag() ) return TRUE;
    }
    if(strpos($where, 'page')!==FALSE){
        if( is_page() ) return TRUE;
    }
    if(strpos($where, 'cat')!==FALSE){
        if(is_category() && !is_archive() ) return TRUE;
    }
    if(strpos($where, 'tag')!==FALSE){
        if(is_tag()) return TRUE;
    }
    if(strpos($where, 'archive')!==FALSE){
        if(is_archive() && !is_category() && !is_tag() ) return TRUE;
    }
    //woo
    if(strpos($where, 'product')!==FALSE){
        if( get_post_type(get_the_ID())== "product") return TRUE;
    }
    //bp
    if(strpos($where, 'bp_group')!==FALSE){
        if( get_post_type(get_the_ID())== "bp_group") return TRUE;
    }
    if(strpos($where, 'bp_activity')!==FALSE){
        if( get_post_type(get_the_ID())== "bp_activity") return TRUE;
    }
    if(strpos($where, 'bp_members')!==FALSE){
        if( get_post_type(get_the_ID())== "bp_members") return TRUE;
    }
    
    if( get_post_type(get_the_ID())== "travel_package") return TRUE;
    
    if(is_tax("package_taxonomy")) return TRUE;

    return FALSE;
}

function ism_return_post_types(){
    $post_types = array(
                   'home' => 'Home',
                   'post' => 'Post',
                   'page' => 'Page',
                   'cat' => 'Categories',
                   'tag' => 'Tags',
                   'archive' => 'Archives'
    );
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    //buddyPress
    if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
        $post_types['bp_group'] = 'BuddyPress Group';
        $post_types['bp_activity'] = 'BuddyPress Activity';
        $post_types['bp_members'] = 'BuddyPress Members';
    }
    //woo
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        $post_types['product'] = 'Products';
    }
    //jigoshop
    if ( is_plugin_active( 'jigoshop/jigoshop.php' ) ) {
        $post_types['product'] = 'Products';
    }
    return $post_types;
}



function ism_remove_arr_prefix( $arr, $prefix ){
    $temp_arr = array();
    foreach( $arr as $k=>$v ){
        $new_k = str_replace( $prefix, '',$k );
        $temp_arr[$new_k] = $v;
    }
    $arr = $temp_arr;
    return $arr;
}

/////SHORTCODE MANAGE
function ism_return_shortcode_manage($type){
    ?>
    <div class="metabox-holder indeed">
              <div class="shortcode_wrapp">
                      <div class="content_shortcode">
                          <div>
                              <span style="font-weight:bolder; color: #333; font-style:italic; font-size:11px;">ShortCode : </span>
                              <span class="the_shortcode"></span>
                          </div>
                          <div style="margin-top:10px;">
                              <span style="font-weight:bolder; color: #333; font-style:italic; font-size:11px;">PHP Code:</span>
                              <span class="php_code"></span>
                          </div>
                      </div>
              </div>
        <form method="post" action="">
            <div class="stuffbox">
                <h3>
                    <label>Social NetWork List:</label>
                </h3>
                <div class="inside">
					<div style="display:inline-block;vertical-align: top;margin-top: 20px;">
                            <?php
                            if($type=="shortcode_locker"){
                                $selected_sm = 'fb,tw,go1,li';
                                $sm_items = ism_return_labels_for_checkboxes(true);
                            }else{
                                $selected_sm = 'fb,tw,goo,pt,li,dg,tbr,su,vk,rd,dl,wb,xg,pf,email';
                                $sm_items = ism_return_labels_for_checkboxes();
                            }
                            $i = 1;
                            foreach($sm_items as $k=>$v){
                            	if($i==9){
                            		?>
                            	   	</div>
                            	    <div style="display:inline-block;vertical-align: top;margin-top: 20px;">
                            	    <?php
                            	}
                            	$i++;
                            ?>
                                <div style="min-width: 400px;">
                                	<div style="display:inline-block;width: 50%;line-height: 1.3;padding: 7px 5px;font-weight: bold;vertical-align: top; color: #222;font-size: 14px;">
                                    	<?php 
                                    		$icon = $k;
                                    		if($icon=='go1') $icon = 'goo';
                                    	?>
                                        <img src="<?php echo ISM_DIR_URL;?>/files/images/icons/<?php echo $icon;?>.png" class="indeed_icons_admin" />
                                        <?php echo $v;?>
                                    </div>
                                	<div style="display:inline-block;line-height: 1.3;padding: 7px 5px;">
                                        <?php
                                            $checked = '';
                                            if(strpos($selected_sm, $k)!==false) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" value="<?php echo $v;?>" id="" <?php echo $checked;?> onClick="make_inputh_string(this, '<?php echo $k;?>', '#sm_items');ism_shortcode_update('<?php echo $type;?>');"/>
									</div>
								</div>
								<?php if(($type=="shortcode_locker") && ($k == 'go1')){ ?><tr><td colspan="2" style="margin:0px; padding:0px;"><span class="ism-info" style=" margin-top:-10px;">This is an experimental feature. Please, don't use it if you are not comfortable with it.</span></td></tr> <?php } ?>
                            <?php
                                }
                            ?>
                    </div>
                    <input type="hidden" value="<?php echo $selected_sm;?>" id="sm_items"/>
                </div>
                <?php 
                	if($type=='shortcode'){
                		?>
			            </div>
			
			            <div class="stuffbox">
			                <h3>
			                    <label>Template:</label>
			                </h3>
                		<?php 	
                	}
                ?>

                <div class="inside" style="vertical-align: top;">
                    <table class="form-table">
	           	        <tbody>
                            <tr valign="top">
                                <th scope="row">
                                    Select a Template:
                                </th>
                                <td>
                                    <select id="template" onChange="ism_shortcode_update('<?php echo $type;?>');ism_preview_templates_be();">
                                    <?php
                                         foreach(ism_return_templates() as $key=>$value){
                                             ?>
                                                <option value="<?php echo $key;?>" ><?php echo $value;?></option>
                                            <?php
                                        }
                                    ?>
                                    </select>
                                </td>
                            </tr>
							</tbody>
						</table>
						<div style="display:inline-block;">
							<div id="ism_preview" style="display: inline-block;padding: 5px 0px;"></div>
							<span class="ism-info">Some of the templates are recommended for <strong>Vertical</strong> Align (like template 9) and others for <strong>Horizontal</strong> Align (like template 5). <strong>Check the Hover Effects!</strong></span>
						</div>
					   <table class="form-table" style="width: 450px;">
							<tbody>
                            <tr valign="top">
                                <th scope="row">
                                    List Align:
									<span class="ism-info">Select how the the list should be displayed.</span>
                                </th>
                                <td>
                                    <input type="radio" value="vertical" onClick="jQuery('#list_align_type').val(this.value);ism_shortcode_update('<?php echo $type;?>');" name="list_type_algin" /><span class="indeedcrlabel">Vertical</span>
                                    <input type="radio" value="horizontal" checked="checked" onClick="jQuery('#list_align_type').val(this.value);ism_shortcode_update('<?php echo $type;?>');" name="list_type_algin" /><span class="indeedcrlabel">Horizontal</span>
                                    <input type="hidden" value="horizontal" id="list_align_type" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <?php 
                	if($type=='shortcode'){
                		?>
				            </div>
				
				            <div class="stuffbox">
				                <h3>
				                    <label>Options:</label>
				                </h3>
				         <?php 
                	}         
				?>
                
                <div class="inside">
                    <table class="form-table">
    	                <tbody>
                            <tr valign="top">
                                <th scope="row">
                                    Display Counts
									<span class="ism-info">Number of shares on each network will be displayed.</span>
                                </th>
                                <td>
                                	<label>
                                    	<input type="checkbox" checked="checked" class="ism-switch" id="display_counts" onClick="ism_shortcode_update('<?php echo $type;?>');"/>
										<div class="switch" style="display:inline-block;"></div>
									</label>                                    
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    Display Full Name Of Social Network
                                </th>
                                <td>
                                	<label>
                                    	<input type="checkbox" checked="checked" class="ism-switch" id="display_full_name" onClick="ism_shortcode_update('<?php echo $type;?>');"/>
										<div class="switch" style="display:inline-block;"></div>
									</label>                                      
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    <?php
        if($type=='shortcode'){
        	?>
        <div class="stuffbox">
            <h3>
                <label>Mobile:</label>
            </h3>
            <div class="inside">
                <table class="form-table">
	                <tbody>
                        <tr valign="top">
                            <th scope="row">
                               Disable On Mobile:
                            </th>
                            <td>
                            	<label>
    	                			<input type="checkbox" class="ism-switch" id="disable_mobile" onClick="ism_shortcode_update('shortcode');"/>
    	                			<div class="switch" style="display:inline-block;"></div>
								</label>            
                            </td>
                        </tr>
                     </tbody>
                </table>         
            </div>
       </div>         	
        	<?php 
        }
    ?>
               
    <?php
        if($type=='shortcode_locker'){
        ?>

            <div class="stuffbox">
                <h3>
                    <label>Locker Display:</label>
                </h3>
                <div class="inside">
                    <table class="form-table" style="margin-bottom: 0px;">
    	                <tbody>
    	                	<tr>
    	                		<th>Theme:</th>
    	                		<td>
    	                		<?php 
    	                			$templates = array(1=>'Default', 2=>'Basic', 3=>'Zipped', 4=>'Zone', 5=>'Majic Transparent', 6=>'Star', 7=>'Clouddy', 8=>'Darks');
    	                		?>
    	                			<select id="locker_template" onChange="ism_shortcode_update('<?php echo $type;?>');ism_disable_style_table(this.value);" style="min-width:225px;">
										<?php 
											foreach($templates as $k=>$v){
												$selected = '';
												if($k==2)$selected= "selected='selected'";
												?>
													<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
												<?php 	
											}
										?>    	                			
    	                			</select>
    	                		</td>
    	                	</tr>
                        </tbody>
                    </table>
                    
                    <table class="form-table" style="margin-bottom: 0px;">
    	                <tbody>
    	                	<tr>
    	                		<th>Delay Time:</th>
    	                		<td>
    	                			<label><input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#enable_timeout_lk');ism_shortcode_update('<?php echo $type;?>');ism_enable_disable_c(this, '#ism_timeout_locker');" />
    	                			<div class="switch" style="display:inline-block;"></div></label>
    	                			<input type="hidden" value="0" id="enable_timeout_lk" />
									<input type="number" disabled="disabled" onChange="ism_shortcode_update('<?php echo $type;?>');" id="ism_timeout_locker" min="1" value="30" style="width: 60px;"/> sec(s)
    	                		</td>
    	                	</tr>
                        </tbody>
                    </table>
                    
                    <table class="form-table" style="margin-bottom: 0px;">
    	                <tbody>
    	                	<tr>
    	                		<th>Disable Locker For Registered Users</th>
    	                		<td>
    	                			<label><input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#not_registered_u');ism_shortcode_update('<?php echo $type;?>');" /><div class="switch" style="display:inline-block;"></div></label>
    	                			<input type="hidden" id="not_registered_u" value="0" />
    	                		</td>
    	                	</tr>
    	                </tbody>
    	            </table>
    	            
                    <table class="form-table" style="margin-bottom: 0px;">
    	                <tbody>
    	                	<tr>
    	                		<th>Reset Locker After: </th>
    	                		<td>
    	                			<label><input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#reset_locker');ism_shortcode_update('<?php echo $type;?>');ism_enable_disable_c(this, '#locker_reset_after');" /><div class="switch" style="display:inline-block;"></div></label>
    	                			<input type="hidden" id="reset_locker" value="0" />
    	                			<input type="number" min="1" disabled="disabled" value="30" id="locker_reset_after" onChange="check_and_h(this, '#not_registered_u');ism_shortcode_update('<?php echo $type;?>');" style="width: 60px; height:28px;"/>
    	                			<select id="locker_reset_type" onChange="onClick="check_and_h(this, '#not_registered_u');ism_shortcode_update('<?php echo $type;?>');">
    	                				<option value="hours">Hours</option>
    	                				<option value="days" selected="selected">Days</option>
    	                			</select>
    	                		</td>
    	                	</tr>
    	                </tbody>
    	            </table>
                        	   
                    <table class="form-table" style="margin-bottom: 0px;">
    	                <tbody>
    	                	<tr>
    	                		<th>Overlock: </th>
    	                		<td>
    	                			<select id="ism_overlock" onChange="ism_shortcode_update('<?php echo $type;?>');" style="min-width:225px;">
    	                				<option value="default">Default</option>
    	                				<option value="opacity">Opacity</option>
    	                			</select>
    	                		</td>
    	                	</tr>
    	                </tbody>
    	            </table>
                     <hr/>   	                	
                    <table class="form-table" style="margin-bottom: 0px;margin-top:10px; display:none;" id="ism_shortcode_style-table">
    	                <tbody>   	       
                            <tr>
                                <th scope="row" style="padding-bottom:5px !important;">
                                    Background-Color:
                                </th>
                                <td style="padding-bottom:5px !important;">
                                   <input type="text" id="locker_background" style="width: 75px;"/>
									<span class="ism-info" style="display:inline-block; font-style:italic; padding-left:10px;">Use the ColorPicker to set your Background color.</span>
                                    <script>
                                		jQuery('#locker_background').ColorPicker({
                                		    onChange: function (hsb, hex, rgb) {
                                			    jQuery('#locker_background').val('#' + hex);
                                                ism_shortcode_update('<?php echo $type;?>');
                                			}
                                		});
                                    </script>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="padding-top: 5px !important;padding-bottom:5px !important;">
                                    Padding:
									<span class="ism-info">General Padding for the Locker Box can be set.</span>
                                </th>
                                <td style="padding-top: 5px !important;padding-bottom:5px !important;">
                                    <input type="number" value="50" id="locker_padding" onClick="ism_shortcode_update('<?php echo $type;?>');" class="indeed_number"/> px
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="form-table" style="margin-bottom: 0px;min-width: 650px;">
    	                <tbody>
                            <tr>
                                <td>
                                    <?php
                                        $settings = array( 'textarea_rows' => 6 );
                                        $textarea_content = '<h2>This content is locked</h2><p>Share This Page To Unlock The Content!</p>';
                                        wp_editor( $textarea_content, 'display_text', $settings );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div onClick="updateTextarea();" id="ism_update_textarea_bttn" style="max-width: 70px;font-size: 12px;display: none;" class="button button-primary button-large">Update</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
					<span class="ism-info">This message will show up above the share buttons to unlock the content.</span>
					<br/>
                </div>
            </div>
            
            <div class="stuffbox">
                <h3>
                    <label>Preview:</label>
                </h3>
                <div class="inside">
                	<div id="ISM_preview_shortcode" style="padding: 20px 10px;"></div>
                </div>
            </div>
        <?php
        }
	    ?>
        </form>
    </div>
              <div class="shortcode_wrapp">
                      <div class="content_shortcode">
                          <div>
                              <span style="font-weight:bolder; color: #333; font-style:italic; font-size:11px;">ShortCode : </span>
                              <span class="the_shortcode"></span>
                          </div>
                          <div style="margin-top:10px;">
                              <span style="font-weight:bolder; color: #333; font-style:italic; font-size:11px;">PHP Code:</span>
                              <span class="php_code"></span>
                          </div>
                      </div>
              </div>
    <script>
        jQuery(document).ready(function(){
            ism_shortcode_update('<?php echo $type;?>');
        });
    </script>
    <?php
}

function ism_return_description($str, $num_words){
    $return = $str;
    $arr = explode(" ", $str);
    if (count($arr)<=$num_words){
        $return = $str;
    }
    else{
        array_splice($arr, $num_words);
        $return = implode(" ", $arr) . " ...";
    }
    return $return;
}

function ism_capcha_q( $k ){
    $arr = array(
                    91 => "2+3-1",
                    92 => "10-9+1",
                    93 => "0+1+2",
                    94 => "1+2*3",
                    95 => "1*2+2"
                );
    return $arr[$k];
}

function ism_capcha_a( $k ){
    $arr = array(
                    91 => 4,
                    92 => 2,
                    93 => 3,
                    94 => 7,
                    95 => 4
                );
    return $arr[$k];
}

function ism_return_templates(){
    //default templates 1-10
    for($i=1;$i<11;$i++){
        $t_arr["ism_template_".$i] = "Template ".$i;
    }
    $templates_dir = ISM_DIR_PATH . 'templates/' ;
    if(is_readable($templates_dir)){
        if ($handle = opendir( $templates_dir )) {
            while (false !== (@$entry = readdir($handle))) {
                if(strpos($entry, '.')>1){
                    $ism_content_file = file_get_contents( $templates_dir . $entry );
                    $templ_arr = explode('#INDEED_TEMPLATES#', $ism_content_file);
                    if(isset($templ_arr[1])){
                        $templ = (array)json_decode($templ_arr[1]);
                         $t_arr = array_merge($t_arr, $templ);
                    }
                }
            }
        }
    }
    return $t_arr;
}

function ism_enqueue_additional_templates(){
    $templates_dir = ISM_DIR_PATH . 'templates/' ;
    if(is_readable($templates_dir)){
        if ($handle = opendir( $templates_dir )) {
            $j = 0;
            while (false !== (@$entry = readdir($handle))) {
                if(strpos($entry, '.')>1){
                    wp_enqueue_style ( 'ism_additional_template_' . $j, ISM_DIR_URL . 'templates/' . $entry  );
                    $j++;
                }
            }
        }
    }
}

function ism_get_data_from_url( $url ) {
	if(in_array('curl', get_loaded_extensions())){
		///////////////////CURL
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		@$data = curl_exec($ch);
		curl_close($ch);		
	}elseif( function_exists(file_get_contents) ){
		//FILE GET CONTENTS
		@$data = file_get_contents( $url );
	}else{
		$data = 0;
	}
	return $data;
}

function ism_return_pinterest_popup(){
	?>
		<div class="popup_wrapp" id="popup_box">
		    <div class="the_popup">
		            <div class="popup_top pinterest_popup">
		                <div class="title">Share On Pinterest</div>
		                <div class="close_bttn" onclick="closePopup();"></div>
		                <div class="clear"></div>
		            </div>
		            <div class="popup_content">
		            	<div>Select Image:</div>
		            	<?php 
		            		$j = 0;
		            		foreach($_REQUEST['ism_images'] as $image){
		            			?>
		            				<img src="<?php echo $image;?>" class="popup_mini_img" id="ism_pin_img_<?php echo $j;?>" onClick="pinterest_select_img(this);"/>
		            			<?php 
		            			$j++;	
		            		}
		            	?>
		            	<input type="hidden" value="<?php echo $_REQUEST['other_info'];?>" id="pin_hide_info"/>
		            	<div class="clear"></div>
		            </div>
		    </div>
		</div>
	<?php 	
}

function ism_test_special_counts($sm_type, $url){
	$count = 0;
	if( get_option('ism_special_count_'.$sm_type)!==FALSE ){
		$arr = get_option('ism_special_count_'.$sm_type);
		if(isset($arr[$url]) && $arr[$url]!=''){
			$count = (int)$arr[$url];
		}
	}
	if( $count==0 && get_option('ism_special_count_all')!==FALSE ){
		$arr = get_option('ism_special_count_all');
		if(isset($arr[$sm_type]) && $arr[$sm_type]!=''){
			$count = (int)$arr[$sm_type];
		}		
	}
	return $count;
}

function ism_update_special_counts(){
	$_REQUEST['the_url'] = str_replace(' ', '', $_REQUEST['the_url']);
	if($_REQUEST['the_url']=='all' || $_REQUEST['the_url']=='All'){
		if(get_option('ism_special_count_all')!==FALSE){
			$arr = get_option('ism_special_count_all');
			$arr[$_REQUEST['sm_type']] = $_REQUEST['the_counts'];
			update_option('ism_special_count_all', $arr);
		}else{
			add_option('ism_special_count_all', array($_REQUEST['sm_type']=>$_REQUEST['the_counts']));
		}
	}else{
		if(get_option('ism_special_count_'.$_REQUEST['sm_type'])!==FALSE){
			$arr = get_option('ism_special_count_'.$_REQUEST['sm_type']);
			$arr[$_REQUEST['the_url']] = $_REQUEST['the_counts'];
			update_option('ism_special_count_'.$_REQUEST['sm_type'], $arr);		
		}else{
			add_option('ism_special_count_'.$_REQUEST['sm_type'], array($_REQUEST['the_url']=>$_REQUEST['the_counts']));
		}
	}
}

function ism_return_special_counts($type){
	if($type=='all'){
		if(get_option('ism_special_count_all')!==FALSE){
			return get_option('ism_special_count_all');
		}		
	}else{
		if(get_option('ism_special_count_'.$type)!==FALSE){
			return get_option('ism_special_count_'.$type);
		}		
	}
}

function ism_return_min_count_sm($sm){
	@$arr = get_option('ism_min_count');
	if(isset($arr[$sm]) && $arr[$sm]!='') return $arr[$sm];
	else return FALSE;
}
function ism_add_timeout($content_id, $locker_id, $timeout){
	if($timeout!=FALSE && $timeout!=''){
		return '<script>
					jQuery(document).ready(function(){
						var ism_delaylocker = new ism_the_TimeOut('.$timeout.', "#'.$content_id.'", "'.$locker_id.'");
					});
				</script>';
	}
}

function ism_return_reset_after($reset, $type, $url, $str){
	if($type=='hours') $m = 60;//minutes in hour
	else $m = 1440;//minutes in day
	$reset_after = $reset * $m * 60;//seconds
	$str .= "<script>
				old_time = jQuery.jStorage.get('{$url}');
				if(old_time){
					current_time = ism_return_current_date(); 
					locker_reset_after = {$reset_after};
					end_time = old_time + locker_reset_after;
					if(current_time>=end_time){
						jQuery.jStorage.set('{$url}', '');
						jQuery.jStorage.deleteKey('{$url}');
					}
				}
			</script>";
	return $str;
}

function ism_is_mobile(){
	$mobile_devices = "/Mobile|Android|BlackBerry|iPhone|iPad|Windows Phone/";
	if(preg_match($mobile_devices, $_SERVER['HTTP_USER_AGENT'])) return TRUE;
	else return FALSE;
}

function ism_return_current_url(){
	//permalink or url
	global $post;
	$url_type = get_option('ism_url_type');
	if(isset($url_type) && $url_type=='permalink') $url = get_permalink();
	else $url = ISM_PROTOCOL . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	return $url;
}

function ism_return_post_title(){
	//return post title
	global $post;
	$post_title = get_the_title();
	$post_title = html_entity_decode($post_title);
	if(strpos($post_title, '&rsquo;')!==FALSE) $post_title = str_replace('&rsquo', "'", $post_title);
	if(strpos($post_title, '&#8217;')!==FALSE) $post_title = str_replace('&#8217;', "'", $post_title);
	if(strpos($post_title, '&#8216;')!==FALSE) $post_title = str_replace('&#8216;', "'", $post_title);
	if(strpos($post_title, '&#8221;')!==FALSE) $post_title = str_replace('&#8221;', '"', $post_title);
	$post_title = rawurlencode($post_title);
	return $post_title;	
}

function ism_return_post_description(){
	//post description
	global $post;
	$description = strip_shortcodes(get_the_content());
	$description = str_replace(array("\n", "\r"), '', strip_tags(addslashes($description)));
	$description = ism_return_description($description, 50);
	return $description;	
}

function ism_return_feat_image($meta_arr){
	//feat image of #ISI_IMG#, tag that will be replace with current image 
	global $post;
	@$feature_img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ));
	if(isset($feature_img_arr[0]))$feat_img = $feature_img_arr[0];
	else{
		@$feat_img = get_option('feat_img');
		if( $feat_img===FALSE ) $feat_img = "";
	}
	//////ISI share image
	if(isset($meta_arr['ISI_image']) && $meta_arr['ISI_image']==TRUE){
		$feat_img = '#ISI_IMG#';
	}
	return $feat_img;
}

function ism_display_counts_js($ismitems_arr, $url, $indeed_wrap_id){
	#return string with javascript function to load the counts
	$js = '';
	if(isset($ismitems_arr) && count($ismitems_arr)>0){
		$ismItems = implode(',', $ismitems_arr);
	
		if(isset($ismItems) && $ismItems!=''){
			$js .= 'jQuery(document).ready(function(){';//start
			/***************** LOAD COUNTS ********************/
			$js .= 'items_str = "'.$ismItems.'";
			items_arr = items_str.split(",");';
			$check_everytime = get_option('ism_check_counts_everytime');
			if($check_everytime===FALSE){
				$h_arr = ism_return_arr('g_o');
				if(isset($h_arr['ism_check_counts_everytime']))	$check_everytime = $h_arr['ism_check_counts_everytime'];
			}
			if(isset($check_everytime) && $check_everytime==0){
				////////////////////FROM OUR DB
				$js .= '
				ism_load_counts_from_db(items_arr, \''.$url.'\', \'#'.$indeed_wrap_id.'\');
				ism_save_share_counts = 1;
				';
			}else{
				//////////////////DEFAULT: FROM SERVER
				$js .= '
				ism_load_counts(0, \''.$url.'\', \'#'.$indeed_wrap_id.'\', items_arr);
				';
			}
			$js .= '});';//end
		}
	}
	return $js; 
}

function ism_special_mobile_template($html_arr, $sm_num_count, $template, $url, $indeed_wrap_id){
	$html = '';
	switch($template){
		case 'ism_template_mob_1':
			# MOBILE 1
			if(isset($sm_num_count) && $sm_num_count>4){
				$hidden_html = array();
				$visible_html = '';
				foreach($html_arr as $k=>$v){
					if($k>2){
						$v['i_color_class'] = true;
						$hidden_html[] = ism_return_item($v, $url);
					}
					else{
						unset($v['display_counts']);
						unset($v['label']);
						$visible_html .= ism_return_item($v, $url);
					}
				}
				$html .= '<div class="ism_mobile-items_hidden" style="display: none;" id="ism_hidden_popup_sm" ><div class="top-share">Share On</div>';
				foreach($hidden_html as $val){
					$html .= '<div class="ism_mobile_h_item">'.$val.'</div>';
				}
				$html .= '</div>';
				$html .= '<div class="ism_mobi-parent_visible">'.$visible_html;
				$html .= '<div class="ism_item mobi-more" onClick="ismMobilePopup(\'#ism_hidden_popup_sm\', \'down\');"></div>'; #read more special button
				$html .= '</div>';				
			}else{
				foreach($html_arr as $val){
					unset($val['display_counts']);
					unset($val['label']);
					$html .= ism_return_item($val, $url);
				}				
			}
		break;
		case 'ism_template_mob_2':
	   		# MOBILE 2
	   		$html .= '<div class="ism_mobile-items_hidden" style="display: none;" id="ism_hidden_popup_sm" ><div class="top-share">Share On</div>';
	   		foreach($html_arr as $k=>$v){
	   			$v['i_color_class'] = true;
	   			$html .= '<div class="ism_mobile_h_item">'.ism_return_item($v, $url).'</div>';
	   		}
	   		$html .= '</div>';
	   		$html .= '<div class="mobi-more" onClick="ismMobilePopup(\'#ism_hidden_popup_sm\', \'down\');"><img src="'.ISM_DIR_URL.'/files/images/share_ics_icon.png" /> Share On</div>';
		break;
		case 'ism_template_mob_3':
			# MOBILE 3
			$html .= '<div class="ism_mobile-items_hidden" id="ism_hidden_popup_sm" >';
			foreach($html_arr as $k=>$v){
				unset($v['display_counts']);
				unset($v['label']);
				$v['i_color_class'] = true;
				$html .= '<div class="ism_mobile_h_item">'.ism_return_item($v, $url).'</div>';
			}
			$html .= '</div>';
			$html .= '<div class="mobi-more" onClick="ismMoveDiv(\'#'.$indeed_wrap_id.'\');"><img src="'.ISM_DIR_URL.'/files/images/share_ics_icon_3.png" /></div>';					
		break;
	}
	return $html;	
}

function ism_return_general_labels_sm($google_plus = false){
	$arr = array(
						'facebook' => 'Facebook',
						'twitter' => 'Twitter',
						'google' => 'Google+',
						'google_plus' => 'Google +1',
						'pinterest' => 'Pinterest',
						'linkedin' => 'Linkedin',
						'digg' => 'DiggDigg',
						'tumblr' => 'Tumblr',
						'stumbleupon' => 'Stumbleupon',
						'vk' => 'VKontakte',
						'reddit' => 'Reddit',
						'delicious' => 'Delicious',
						'weibo' => 'Weibo',
						'xing' => 'Xing',
						'print' => 'Print',
						'email' => 'E-Mail',
			);	
	$data = get_option('ism_general_sm_labels');
	foreach( $arr as $k=>$v ){
		if(!isset($data[$k]) || $data[$k]==''){
			$data[$k] = $v;
		}
	}
	if(!isset($google_plus) || $google_plus==false) unset($data['google_plus']);
	return $data;
}

function ism_return_labels_for_checkboxes($locker=false){
	$ism_list = ism_return_general_labels_sm(true);
	if($locker==true){
		$arr = array(   'fb' => $ism_list['facebook'],
						'tw' => $ism_list['twitter'],
						'go1' => $ism_list['google_plus'],
						'li' => $ism_list['linkedin'],
					);	
		return $arr;	
	}
	$arr = array( 
						'fb' => $ism_list['facebook'],//'Facebook',
						'tw' => $ism_list['twitter'],//'Twitter',
						'goo' => $ism_list['google'],//'Google+',
						'pt' => $ism_list['pinterest'],//'Pinterest',
						'li' => $ism_list['linkedin'],//'Linkedin',
						'dg' => $ism_list['digg'],//'DiggDigg',
						'tbr' => $ism_list['tumblr'],//'Tumblr',
						'su' => $ism_list['stumbleupon'],//'Stumbleupon',
						'vk' => $ism_list['vk'],//'VKontakte',
						'rd' => $ism_list['reddit'],//'Reddit',
						'dl' => $ism_list['delicious'],//'Delicious',
						'wb' => $ism_list['weibo'],//'Weibo',
						'xg' => $ism_list['xing'],//'Xing',
						'pf' => $ism_list['print'],//'Print',
						'email' => $ism_list['email']
					);//'E-Mail' );
	return $arr;
}