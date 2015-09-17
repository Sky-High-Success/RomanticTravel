<?php
  $url = get_admin_url() . 'admin.php?page=ism_manage';
  if(isset($_REQUEST['tab'])) $tab = $_REQUEST['tab'];
  else $tab = 'general_options';
?>
<script>
    ism_base_path = "<?php echo get_site_url();?>";
    jQuery(document).ready(function(){
        ism_preview_templates_be();
    });
</script>
<div class="ism-wrap">
<!--top menu -->      
<div class="ism_admin_header">
	<div class="ism_left_side">
		<img src="<?php echo ISM_DIR_URL;?>files/images/dashboard-logo.jpg"/>
	</div>
	<div class="ism_right_side">
		<ul>
            <?php
                $menu_items = array('website_display'=>'Website Display', 
                					'inside_display'=>'Content Display', 
                					'mobile_display' => 'Mobile Display',
                					'shortcode'=>'Shortcode Display',
                					);
                if( is_plugin_active('indeed-share-image/indeed-share-image.php') ){
                	$menu_items['share_image'] = 'Image Display';
                	?>
                	<style>
						.ism_admin_header .ism_right_side ul li{
							width: 9%;	
							padding: 4px;
						}
						.ism-icon-share_image:before{
							content: "\f03e";
						}	
						.ism_admin_header .ism_right_side ul li .ism_page_title{
							padding: 18px 0px;	
						}
					</style>
					<?php 
                }
                
                $menu_items['shortcode_locker'] = 'Shortcode Locker';
                $menu_items['general_options'] = 'General Options';
                $menu_items['special_counts'] = 'Initial Counts';
                $menu_items['statistics'] = 'Statistics';
                $menu_items['help'] = 'Help';
                foreach($menu_items as $k=>$v){
                    $class = '';
                    if($k==$tab) $class .= 'selected';
                    ?>
						<li class="<?php echo $class;?>">
							<a href="<?php echo $url.'&tab='.$k;?>">
								<div class="ism_page_title">
								<i class="ism-fa-menu ism-icon-<?php echo $k;?>"></i>
								<?php echo $v;?>
								</div>
							</a>
						</li>
                    <?php 
                }
            ?>
		</ul>
	</div>
	<div class="clear"></div>
</div>
        
<!-- /top menu-->
<?php
    switch($tab){
        case "website_display":
      /*************************** WEBSITE DISPLAY *************************/
        if(isset($_REQUEST['wd_submit_bttn'])){
            ism_return_arr_update( 'wd' );
        }
        //default settings
        $meta_arr = ism_return_arr_val( 'wd' );
?>
<div class="metabox-holder indeed">
    <form method="post" action="">
        <div class="stuffbox">
            <h3>
                <label>Social Network List:</label>
            </h3>
            <div class="inside">
                <div style="display:inline-block;vertical-align: top;margin-top: 20px;">
                      <?php
                          $sm_items = ism_return_labels_for_checkboxes();
                          $i = 1;
                          foreach($sm_items as $k=>$v){
                          	  if($i==9){
                          	  		?>
                          	  		</div>
                          	  		<div style="display:inline-block;vertical-align: top;margin-top: 20px;">
                          	  		<?php
                          	  }
                          	  $i++;
                              $checked = ism_check_select_str($meta_arr['wd_list'], $k);
                              ?>
                                <div style="min-width: 400px;">
                                	<div style="display:inline-block;width: 50%;line-height: 1.3;padding: 7px 5px;font-weight: bold;vertical-align: top; color: #222;font-size: 14px;">
                                        <img src="<?php echo ISM_DIR_URL;?>/files/images/icons/<?php echo $k;?>.png" class="indeed_icons_admin" />
                                        <?php echo $v;?>
                                    </div>
                                	<div style="display:inline-block;line-height: 1.3;padding: 7px 5px;">
                                        <input type="checkbox" value="<?php echo $v;?>" id="" onClick="make_inputh_string(this, '<?php echo $k;?>', '#sm_items');" class="" <?php echo $checked;?>/>
                                	</div>
                                </div>
                              <?php
                          }
                      ?>                
                </div>
                <input type="hidden" value="<?php echo $meta_arr['wd_list'];?>" name="wd_list" id="sm_items"/>

                <div class="submit">
                    <input type="submit" value="Save changes" name="wd_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Template:</label>
            </h3>
            <div class="inside" style="vertical-align: top;">
                <table class="form-table">
	                <tbody>
                        <tr valign="top">
                            <th scope="row">
                                Select a Template:
                            </th>
                            <td>
                                <select id="template" name="wd_template" onChange="ism_preview_templates_be();">
                                    <?php
                                        foreach(ism_return_templates() as $key=>$value){
                                            $select = ism_checkSelected($meta_arr['wd_template'], $key, 'select');
                                            ?>
                                                <option value="<?php echo $key;?>" <?php echo $select;?>><?php echo $value;?></option>
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
                                <?php
                                    $checked = ism_checkSelected('vertical', $meta_arr['wd_list_align'], 'radio');
                                ?>
                                <input type="radio" name="wd_list_align" value="vertical" <?php echo $checked;?> class="" /><span class="indeedcrlabel">Vertical</span>
                                <?php
                                    $checked = ism_checkSelected('horizontal', $meta_arr['wd_list_align'], 'radio');
                                ?>
                                <input type="radio" name="wd_list_align" value="horizontal" <?php echo $checked;?> class="" /><span class="indeedcrlabel">Horizontal</span>
                            </td>
                        </tr>
                	</tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="wd_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
			
		</div>
        <div class="stuffbox">
            <h3>
                <label>Options:</label>
            </h3>
            <div class="inside">
                <table class="form-table">
	                <tbody>
                        <tr valign="top">
                            <th scope="row">
                                Display Counts
								<span class="ism-info">Number of shares on each network will be displayed.</span>
                            </th>
                            <td>
                                <?php
                                    $checked = ism_checkSelected('true', $meta_arr['wd_display_counts'], 'checkbox');
                                ?>
                                <label>
                                	<input type="checkbox" class="ism-switch" onClick="ism_check_and_h(this, '#display_counts');" <?php echo $checked;?> />
                                	<div class="switch" style="display:inline-block;"></div>
                                </label>
                                <input type="hidden" value="<?php echo $meta_arr['wd_display_counts'];?>" name="wd_display_counts" id="display_counts" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                Display Full Name Of Social Network
                            </th>
                            <td>
                                <?php
                                    $checked = ism_checkSelected('true', $meta_arr['wd_display_full_name'], 'checkbox');
                                ?>
                                <label>
                                	<input type="checkbox" onClick="ism_check_and_h(this, '#wd_display_full_name');" class="ism-switch" <?php echo $checked;?> />
                                	<div class="switch" style="display:inline-block;"></div>
                                </label>
                                <input type="hidden" value="<?php echo $meta_arr['wd_display_full_name'];?>" name="wd_display_full_name" id="wd_display_full_name" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="wd_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Where To Display:</label>
            </h3>
            <div class="inside">
			<span class="ism-info" style="padding-top:20px;">If none of them is not selected, the Social Icons will <strong>not show up</strong> using the "Website Display".</span>
            <?php
                  $ism_post_types = ism_return_post_types();
            ?>
                <table class="form-table">
                    <tbody>
                        <?php
                            foreach($ism_post_types as $k=>$v){
                                ?>
                                <tr valign="top">
                                    <th scope="row">
                                        <?php echo ucfirst($v);?>
                                    </th>
                                    <td>
                                        <?php $checked = ism_check_select_str($meta_arr['wd_display_where'], $k);?>
                                        <input type="checkbox" id="" class="" <?php echo $checked;?> onClick="make_inputh_string(this, '<?php echo $k;?>', '#display_where');"/>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                    <input type="hidden" value="<?php echo $meta_arr['wd_display_where'];?>" name="wd_display_where" id="display_where" />
                <div class="submit">
                    <input type="submit" value="Save changes" name="wd_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Position:</label>
            </h3>
            <div class="inside">
                <table class="form-table">
	                <tbody>
                        <tr valign="top">
                            <th scope="row">
                               Floating:
                            </th>
                            <td>
                                <?php $checked = ism_checkSelected($meta_arr['wd_floating'], 'yes', 'radio');?>
                                <input type="radio" name="wd_floating" value="yes" <?php echo $checked;?> /><span class="indeedcrlabel">Yes</span>
                                <?php $checked = ism_checkSelected($meta_arr['wd_floating'], 'no', 'radio');?>
                                <input type="radio" name="wd_floating" value="no" <?php echo $checked;?> /><span class="indeedcrlabel">No</span>
								<span class="ism-info" style="display:inline-block; font-style:italic;">The Social Icons will stay all the time on the screen despite the scroll position</span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                Position
								<span class="ism-info">Set the Box's icons position. Can be used positive or negative values.</span>
                            </th>
                            <td>
                               <table>
                                  <tr>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_top_bottom'], 'top', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="wd_top_bottom" value="top" <?php echo $checked;?>/><span class="indeedcrlabel">Top</span></td>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_top_bottom'], 'bottom', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="wd_top_bottom" value="bottom" <?php echo $checked;?>/><span class="indeedcrlabel">Bottom</span></td>
                                  </tr>
								  <tr style="height:40px; vertical-align:top;">    
									  <td class="indeed_td_np"><input type="number" value="<?php echo $meta_arr['wd_top_bottom_value'];?>" name="wd_top_bottom_value" class="indeed_number" style="margin-top:4px;"/></td>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_top_bottom_type'], '%', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="wd_top_bottom_type" value="%" <?php echo $checked;?>  style="margin-left:20px;"/><span class="indeedcrlabel" style="margin-right:3px;">% | </span>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_top_bottom_type'], 'px', 'radio');?>
                                      <input type="radio" class="" name="wd_top_bottom_type" value="px" <?php echo $checked;?>/><span class="indeedcrlabel">px</span></td>
                                  </tr>
                                  <tr>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_left_right'], 'left', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="wd_left_right" value="left" <?php echo $checked;?>/><span class="indeedcrlabel">Left</span></td>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_left_right'], 'right', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="wd_left_right" value="right" <?php echo $checked;?>/><span class="indeedcrlabel">Right</span></td>
                                  </tr>
								  <tr>
									  <td class="indeed_td_np"><input type="number" value="<?php echo $meta_arr['wd_left_right_value'];?>" name="wd_left_right_value" class="indeed_number" style="margin-top:4px;"/></td>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_left_right_type'], '%', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="wd_left_right_type" value="%" <?php echo $checked;?>  style="margin-left:20px;"/><span class="indeedcrlabel" style="margin-right:3px;">% | </span>
                                          <?php $checked = ism_checkSelected($meta_arr['wd_left_right_type'], 'px', 'radio');?>
                                      <input type="radio" class="" name="wd_left_right_type" value="px" <?php echo $checked;?>/><span class="indeedcrlabel">px</span></td>
                                  </tr>
                               </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="wd_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
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
    	                			<?php 
    	                				$checked = '';
    	                				if(isset($meta_arr['wd_disable_mobile']) && $meta_arr['wd_disable_mobile']==1) $checked = 'checked="checked"';
    	                			?>
    	                			<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#wd_disable_mobile');" <?php echo $checked;?> />
    	                		<div class="switch" style="display:inline-block;"></div>
    	                		</label>
    	                		<input type="hidden" value="<?php echo $meta_arr['wd_disable_mobile'];?>" name="wd_disable_mobile" id="wd_disable_mobile" />                     
                            </td>
                        </tr>
                     </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="wd_submit_bttn" class="button button-primary button-large" />
                </div>            
            </div>
       </div>
    </form>
</div>
<?php
    break;
    case "inside_display":
        if(isset($_REQUEST['id_submit_bttn'])){
            ism_return_arr_update( 'id' );
        }
        //default settings
        $meta_arr = ism_return_arr_val( 'id' );
        ?>
<div class="metabox-holder indeed">
    <form method="post" action="">
        <div class="stuffbox">
            <h3>
                <label>Social Network List:</label>
            </h3>
            <div class="inside">
                <div style="display:inline-block;vertical-align: top;margin-top: 20px;">
                      <?php
                          $sm_items = ism_return_labels_for_checkboxes();
                          $i = 1;
                          foreach($sm_items as $k=>$v){
                          		if($i==9){
                          		?>
                          	        </div>
                          	        <div style="display:inline-block;vertical-align: top;margin-top: 20px;">
                          	    <?php
                          	    }
                          	    $i++;
                              $checked = ism_check_select_str($meta_arr['id_list'], $k);
                              ?>
                                <div style="min-width: 400px;">
                                	<div style="display:inline-block;width: 50%;line-height: 1.3;padding: 7px 5px;font-weight: bold;vertical-align: top; color: #222;font-size: 14px;">
									    <img src="<?php echo ISM_DIR_URL;?>/files/images/icons/<?php echo $k;?>.png" class="indeed_icons_admin" />
                                        <?php echo $v;?>
                                   </div>
                                   <div style="display:inline-block;line-height: 1.3;padding: 7px 5px;">
                                        <input type="checkbox" value="<?php echo $v;?>" id="" onClick="make_inputh_string(this, '<?php echo $k;?>', '#sm_items');" class="" <?php echo $checked;?>/>
                                   </div>
                                </div>
                              <?php
                          }
                      ?>
                      <input type="hidden" value="<?php echo $meta_arr['id_list'];?>" name="id_list" id="sm_items"/>
                </div>
                <div class="submit">
                    <input type="submit" value="Save changes" name="id_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Template:</label>
            </h3>
            <div class="inside" style="vertical-align: top;">
                <table class="form-table">
	                <tbody>
                        <tr valign="top">
                            <th scope="row">
                                Select a Template:
                            </th>
                            <td>
                                <select id="template" name="id_template" onChange="ism_preview_templates_be();">
                                    <?php
                                        foreach(ism_return_templates() as $key=>$value){
                                            $select = ism_checkSelected($meta_arr['id_template'], $key, 'select');
                                            ?>
                                                <option value="<?php echo $key;?>" <?php echo $select;?>><?php echo $value;?></option>
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
                                <?php
                                    $checked = ism_checkSelected('vertical', $meta_arr['id_list_align'], 'radio');
                                ?>
                                <input type="radio" name="id_list_align" value="vertical" <?php echo $checked;?> class="" /><span class="indeedcrlabel">Vertical</span>
                                <?php
                                    $checked = ism_checkSelected('horizontal', $meta_arr['id_list_align'], 'radio');
                                ?>
                                <input type="radio" name="id_list_align" value="horizontal" <?php echo $checked;?> class="" /><span class="indeedcrlabel">Horizontal</span>
                            </td>
                        </tr>
                	</tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="id_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
            
        </div>
        <div class="stuffbox">
            <h3>
                <label>Options:</label>
            </h3>
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
	                                <?php
	                                    $checked = ism_checkSelected('true', $meta_arr['id_display_counts'], 'checkbox');
	                                ?>
	                                <input type="checkbox" onClick="ism_check_and_h(this, '#display_counts');" class="ism-switch" <?php echo $checked;?> />
	                                <div class="switch" style="display:inline-block;"></div>
	                            </label>
                                <input type="hidden" value="<?php echo $meta_arr['id_display_counts'];?>" name="id_display_counts" id="display_counts" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                Display Full Name Of Social Network
                            </th>
                            <td>
                            	<label>
	                                <?php
	                                    $checked = ism_checkSelected('true', $meta_arr['id_display_full_name'], 'checkbox');
	                                ?>
	                                <input type="checkbox" onClick="ism_check_and_h(this, '#id_display_full_name');" class="ism-switch" <?php echo $checked;?> />
	                                <div class="switch" style="display:inline-block;"></div>
	                            </label>
                                <input type="hidden" value="<?php echo $meta_arr['id_display_full_name'];?>" name="id_display_full_name" id="id_display_full_name" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="id_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Where To Display:</label>
            </h3>
            <div class="inside">
			<span class="ism-info" style="padding-top:20px;">If none of them is not selected, the Social Icons will <strong>not show up</strong> using the "Content Display".</span>
             <?php
                $ism_post_types = ism_return_post_types();
            ?>
                <table class="form-table">
                    <tbody>
                        <?php
                            foreach($ism_post_types as $k=>$v){
                                ?>
                                <tr valign="top">
                                    <th scope="row">
                                        <?php echo ucfirst($v);?>
                                    </th>
                                    <td>
                                        <?php $checked = ism_check_select_str($meta_arr['id_display_where'], $k);?>
                                        <input type="checkbox" id="" class="" <?php echo $checked;?> onClick="make_inputh_string(this, '<?php echo $k;?>', '#display_where');"/>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                    <input type="hidden" value="<?php echo $meta_arr['id_display_where'];?>" name="id_display_where" id="display_where" />
                <div class="submit">
                    <input type="submit" value="Save changes" name="id_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Position:</label>
            </h3>
            <div class="inside">
                <table class="form-table">
	                <tbody>
                        <tr valign="top">
                            <td>
                                <?php
                                    $checked = ism_checkSelected('before', $meta_arr['id_position'], 'radio');
                                ?>
                                <input type="radio" name="id_position" value="before" <?php echo $checked;?> class="" onClick="jQuery('#custom_id_position').css('display', 'none');"/><span class="indeedcrlabel" style="font-weight:bold; color: #6cc072;">Before Content</span>
                             </td>
						</tr>
						<tr valign="top">
                            <td>   
								<?php
                                    $checked = ism_checkSelected('after', $meta_arr['id_position'], 'radio');
                                ?>
                                <input type="radio" name="id_position" value="after" <?php echo $checked;?> class="" onClick="jQuery('#custom_id_position').css('display', 'none');" /><span class="indeedcrlabel" style="font-weight:bold; color: #6cc072;">After Content</span>
                              </td>
						</tr>
						<tr valign="top">
                            <td>   
								<?php
                                    $checked = ism_checkSelected('both', $meta_arr['id_position'], 'radio');
                                ?>
                                <input type="radio" name="id_position" value="both" <?php echo $checked;?> class="" onClick="jQuery('#custom_id_position').css('display', 'none');" /><span class="indeedcrlabel" style="font-weight:bold; color: #6cc072;">Both ( Before & After Content)</span>
                              </td>
						</tr>
						<tr valign="top">
                            <td>   
								<?php
                                    $checked = ism_checkSelected('custom', $meta_arr['id_position'], 'radio');
                                ?>
                                <input type="radio" name="id_position" value="custom" <?php echo $checked;?> class="" onClick="jQuery('#custom_id_position').css('display', 'block');" /><span class="indeedcrlabel" style="font-weight:bold; color: #369;">Custom</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                        <?php
                            $display = 'none';
                            if($meta_arr['id_position']=='custom') $display = 'block';
                        ?>
                <table class="form-table" style="display: <?php echo $display;?>; margin-left:50px;" id="custom_id_position">
	                <tbody>
                        <tr valign="top">
                                          <?php $checked = ism_checkSelected($meta_arr['id_top_bottom'], 'top', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="id_top_bottom" value="top" <?php echo $checked;?>/><span class="indeedcrlabel">Top</span></td>
                                          <?php $checked = ism_checkSelected($meta_arr['id_top_bottom'], 'bottom', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="id_top_bottom" value="bottom" <?php echo $checked;?>/><span class="indeedcrlabel">Bottom</span></td>
                                      <td class="indeed_td_np"><input type="number" value="<?php echo $meta_arr['id_top_bottom_value'];?>" name="id_top_bottom_value" class="indeed_number"/> px</td>
                        </tr>
                        <tr valign="top">
                                          <?php $checked = ism_checkSelected($meta_arr['id_left_right'], 'left', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="id_left_right" value="left" <?php echo $checked;?>/><span class="indeedcrlabel">Left</span></td>
                                          <?php $checked = ism_checkSelected($meta_arr['id_left_right'], 'right', 'radio');?>
                                      <td class="indeed_td_np"><input type="radio" class="" name="id_left_right" value="right" <?php echo $checked;?>/><span class="indeedcrlabel">Right</span></td>
                                      <td class="indeed_td_np"><input type="number" value="<?php echo $meta_arr['id_left_right_value'];?>" name="id_left_right_value" class="indeed_number"/> px</td>
                        </tr>
	                </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="id_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
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
    	                			<?php 
    	                				$checked = '';
    	                				if(isset($meta_arr['id_disable_mobile']) && $meta_arr['id_disable_mobile']==1) $checked = 'checked="checked"';
    	                			?>
    	                			<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#id_disable_mobile');" <?php echo $checked;?> />
    	                		<div class="switch" style="display:inline-block;"></div>
    	                		</label>
    	                		<input type="hidden" value="<?php echo $meta_arr['id_disable_mobile'];?>" name="id_disable_mobile" id="id_disable_mobile" />                     
                            </td>
                        </tr>
                     </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="id_submit_bttn" class="button button-primary button-large" />
                </div>            
            </div>
       </div>    
    </form>
</div>
<?php
    break;
    case "shortcode":
      /*************************** SHORTCODE GENERATOR *******************************/
        ism_return_shortcode_manage( $tab );
    break;
    case "shortcode_locker":
        /**************************** SHORTCODE LOCKER *******************************/
        ism_return_shortcode_manage( $tab );
    break;
    case "general_options":
      /*************************** General Options *******************************/
        if(isset($_REQUEST['g_submit_bttn'])){
            ism_return_arr_update( 'g_o' );
        }
        //default settings
        $meta_arr = ism_return_arr_val( 'g_o' );
        
        global $ism_list;
        $ism_list = ism_return_general_labels_sm(true);

?>
<div class="metabox-holder indeed">
    <form method="post" action="">
        <div class="stuffbox">
            <h3>
                <label>Twitter:</label>
            </h3>
            <div class="inside">
            
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
                        <tr valign="top">
                            <td>
                                <strong>Username To Be Mentioned: @</strong> <input type="text" value="<?php echo $meta_arr['twitter_name'];?>" name="twitter_name" />
                            </td>
                        </tr>
                    </tbody>
                </table>
				<span class="ism-info">Set a twitter username that can be mentioned into shared tweets. Can be any username, but especially yours.</span>
				
                <table class="form-table" style="margin-bottom: 0px;margin-top: 25px;">
        	        <tbody>
                        <tr valign="top">
                            <td>
                                <strong>Enable Share Feature Image: </strong>                        
    	                			<label>
    	                				<?php 
    	                					$checked = '';
    	                					if(isset($meta_arr['ism_twitter_share_img']) && $meta_arr['ism_twitter_share_img']==1) $checked = 'checked="checked"';
    	                				?>
    	                				<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#ism_twitter_share_img');" <?php echo $checked;?> />
    	                			<div class="switch" style="display:inline-block;"></div></label>
    	                			<input type="hidden" value="<?php echo $meta_arr['ism_twitter_share_img'];?>" name="ism_twitter_share_img" id="ism_twitter_share_img" />     
    	                	</td>
                        </tr>
                    </tbody>
                </table>
                <span class="ism-info">
                	<strong>Experimental:</strong> In further to Share the "Featured Image" on Twitter, the website needs to be validated after the option was activated: <a href="https://cards-dev.twitter.com/validator" target="_blank">https://cards-dev.twitter.com/validator</a>
                </span>	
                
                			
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Facebook App ID:</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
                        <tr valign="top">
                            <td>
                                <input type="text" value="<?php echo $meta_arr['facebook_id'];?>" name="facebook_id" id="facebook_id"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
				<span class="ism-info">For a proper share and tracking the share counts on FaceBook you need to set a Facebook App ID.</span>
                <span class="ism-info"></span>
				<div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>Default Feature Image:</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
                        <tr valign="top">
                            <td>
                                <input type="text" value="<?php echo $meta_arr['feat_img'];?>" name="feat_img" id="feat_img" onClick="open_media_up(this);" class="indeed_large_input_text"/> <i class="icon-trash" onclick="jQuery('#feat_img').val('');" title="Remove Default Feature Image Value"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
				<span class="ism-info">A image it will be used when a post without an featured image is shared. The option is available only on some Social NetWorks (like Facebook, Pinterest).</span>
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>E-Mail Share Options:</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
                        <tr valign="top">
                            <td>
                                <span style="width:130px; font-weight:bold; display: inline-block;">Box Title: </span><input type="text" value="<?php echo $meta_arr['email_box_title'];?>" name="email_box_title" style="min-width: 300px;"/>
                            </td>
                        </tr>
						<tr valign="top">
                            <td>
                                <span style="width:130px; font-weight:bold; display: inline-block;">Subject: </span><input name="email_subject" style="min-width: 300px;" value="<?php echo stripslashes($meta_arr['email_subject']);?>"/>
							</td>
                        </tr>
						<tr valign="top">
                            <td>
                                <span style="width:130px; font-weight:bold; display: inline-block; vertical-align:top;">Message: </span><textarea name="email_message" style="min-width: 300px; min-height:150px;"><?php echo stripslashes( $meta_arr['email_message'] );?></textarea>
								<span class="ism-info">#LINK# is the Link that it will be shared.</span>
							</td>
                        </tr>
						<tr valign="top">
                            <td>
                                <?php
                                    $checked = '';
                                    if($meta_arr['email_capcha']==1) $checked = 'checked="checked"';
                                ?>
                                <span style="width:130px; font-weight:bold; display: inline-block;">Activate Capcha: </span><input type="checkbox" onClick="check_and_h(this, '#capcha_hidden');" <?php echo $checked;?> />
								<input type="hidden" name="email_capcha" value="<?php echo $meta_arr['email_capcha'];?>" id="capcha_hidden" />
							</td>
                        </tr>
						<tr valign="top">
                            <td>
                                <span style="width:130px; font-weight:bold; display: inline-block;">Alternative Email:</span><input type="text" value="<?php echo $meta_arr['email_send_copy'];?>" name="email_send_copy" style="min-width: 300px;" />
							</td>
                        </tr>
						<tr valign="top">
                            <td>
                                <span style="width:130px; font-weight:bold; display: inline-block;">Success Message: </span><input type="text" value="<?php echo $meta_arr['email_success_message'];?>" name="email_success_message" style="min-width: 300px;" />
							</td>
                        </tr>
                    </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>
        <div class="stuffbox">
            <h3>
                <label>URL Settings:</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
                        <tr valign="top">
                            <td>
                                <strong>URL Type: </strong>
                                <div style="margin-top:15px;">
                                    <div>
                                        <?php
                                            $checked = '';
                                            if($meta_arr['ism_url_type']=='url') $checked = 'checked="checked"';
                                        ?>
                                        <input type="radio" name="ism_url_type" value="url" <?php echo $checked;?> /> Current URL
										<span class="ism-info">Use this option to avoid different Shares Counts on the same page (especially when you use a "One Page" theme).</span>
                                    </div>
                                    <div style="margin-top:10px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['ism_url_type']=='permalink') $checked = 'checked="checked"';
                                        ?>
                                        <input type="radio" name="ism_url_type" value="permalink" <?php echo $checked;?> /> Permalink
										<span class="ism-info">Use this option when you wanna display multiple posts with sharing icons inside on the same page (like some Home pages).</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>
            </div>
        </div>        
        <div class="stuffbox">
            <h3>
                <label>Showing Counts After:</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
  							<tr valign="middle">
			                            <td>
											Social Media Type:
			                            </td>
			                            <td>
											<select id="sm_type_min_count">
											<?php 
												$sm_items = array( 
																'facebook',
																'twitter',
																'google',
																'pinterest',
																'linkedin',
																'stumbleupon',
																'vk',
																'reddit',
																'print',
																'email' 
																);
												foreach($sm_items as $k){
													?>
														<option value="<?php echo $k;?>"><?php echo $ism_list[$k];?></option>
													<?php 	
												}
											?>											
											</select>			                            
			                            </td>
			                            <td>
			                            	Counts: <input type="number" id="sm_min_count_value" style="width: 60px;" min="1"/>
			                            </td>
			                            <td>
			                           	    <div class="submit" style="padding: 0px 10px;">
	                    						<input type="button" value="Add" class="button button-primary button-large" onClick="ism_update_minim_counts();"/>
	                    						<span class="spinner" id="ism_near_bttn_loading" style="display: block;visibility: hidden;"></span>
	                						</div>
			                            </td>
			                    </tr>
                    </tbody>
                </table>
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>                			                    
			                    <tr>
			                    	<td rowspan="4">
			                    		<table id="ism_minim_counts_table">
			                    		<?php
				                    		@$arr = get_option('ism_min_count');
				                    		if($arr!==FALSE && count($arr)>0){
				                    			$str = '';
				                    			foreach($arr as $k=>$v){
				                    				if($v!=''){
				                    					echo "<tr id='ism_count_min_sm-{$k}'>
				                    					<td>{$ism_list[$k]}</td>
				                    					<td>{$v}</td>
				                    					<td>
				                    					<i class='icon-trash' title='Delete' onClick='ism_deleteMinCount(\"{$k}\", \"#ism_count_min_sm-{$k}\");'></i>
				                    					</td>
				                    					</tr>";
				                    				}
				                    			}
				                    		}			                    		
			                    		?>
			                    		</table>
			                    	</td>
			                    </tr>
                    </tbody>
                </table>
            </div>
        </div>  
        <div class="stuffbox">
            <h3>
                <label>Check Counts Every Time:</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
  							<tr valign="middle">
			                            <td>
											Enable:
			                            </td>
    	                		<td>
    	                			<label>
    	                				<?php 
    	                					$checked = '';
    	                					if(isset($meta_arr['ism_check_counts_everytime']) && $meta_arr['ism_check_counts_everytime']==1) $checked = 'checked="checked"';
    	                				?>
    	                				<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#ism_check_counts_everytime');" <?php echo $checked;?> />
    	                			<div class="switch" style="display:inline-block;"></div></label>
    	                			<input type="hidden" value="<?php echo $meta_arr['ism_check_counts_everytime'];?>" name="ism_check_counts_everytime" id="ism_check_counts_everytime" />
    	                		</td>
    	                	</tr>
    	             </tbody>
    	         </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>    	                    	
            </div>
        </div>
        
        <div class="stuffbox">
            <h3>
                <label>Statistics</label>
            </h3>
            <div class="inside">
                 <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
  							<tr valign="middle">
			                    <td>
									Enable:
			                    </td>
    	                		<td>
    	                			<label>
    	                				<?php 
    	                					$checked = '';
    	                					if(isset($meta_arr['ism_enable_statistics']) && $meta_arr['ism_enable_statistics']==1) $checked = 'checked="checked"';
    	                				?>
    	                				<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#ism_enable_statistics');" <?php echo $checked;?> />
    	                			<div class="switch" style="display:inline-block;"></div></label>
    	                			<input type="hidden" value="<?php echo $meta_arr['ism_enable_statistics'];?>" name="ism_enable_statistics" id="ism_enable_statistics" />
    	                		</td>
    	                	</tr>
  							<tr valign="middle">
			                    <td>
									Clear Data Older Than:
			                    </td>
    	                		<td>
    	                			<select id="clear_statistic">
    	                				<option value="day">One Day</option>
    	                				<option value="week">One Week</option>
    	                				<option value="month">One Month</option>
    	                			</select>
    	                		</td>
    	                		<td>
    	                			<input type="button" onClick="ism_clear_statistic_data();" value="Clear Data" class="button button-primary button-large" />
    	                			<span class="spinner" id="ism_near_bttn_clear_statistic" style="display: block;visibility: hidden;"></span>
    	                		</td>
    	                	</tr>    	                	
    	             </tbody>
    	         </table>    
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>    	    	                 
            </div>
       </div> 
       
       
        <div class="stuffbox">
            <h3>
                <label>Social Media Labels</label>
            </h3>
            <div class="inside">
                <table class="form-table" style="margin-bottom: 0px;">
        	        <tbody>
        	        	<?php 
        	        		global $ism_list;
        	        		foreach($ism_list as $k=>$v){
        	        			?>
		  							<tr valign="middle">
					                    <td>
					                    	<div>
					                    		<i class="fa-ism fa-<?php echo $k;?>-ism"></i>
					                    	</div>
					                    </td>
					                    <td>
					                    	<input type="text" name="ism_general_sm_labels[<?php echo $k;?>]" value="<?php echo $v;?>"/>
					                    </td>
					                </tr>        	    
        	        			<?php 		
        	        		}
        	        	?>
			       </tbody>
			    </table>
                <div class="submit">
                    <input type="submit" value="Save changes" name="g_submit_bttn" class="button button-primary button-large" />
                </div>    	            
            </div>
        </div>                       
    </form>
</div>
<?php
    break;
    case "special_counts":
    	if(isset($_REQUEST['g_submit_bttn'])){
    		ism_update_special_counts();
    	}
		?>
			<div class="metabox-holder indeed">
			    <form method="post" action="">
			        <div class="stuffbox">
			        	    <h3>
                				<label>Add New Initial Count:</label>
            				</h3>
	            		<div class="inside" style="margin: 10px 0px;">
			                <table class="" style="margin-bottom: 0px;">
			        	        <tbody>
			                        <tr valign="middle">
			                            <td>
											Social Media Type:
			                            </td>
			                            <td>
											<select name="sm_type">
											<?php 
												$ism_list = ism_return_general_labels_sm(true);
												$sm_items = array( 
																'facebook' => $ism_list['facebook'],
																'twitter' => $ism_list['twitter'],
																'google' => $ism_list['google'],
																'pinterest' => $ism_list['pinterest'],
																'linkedin' => $ism_list['linkedin'],
																'stumbleupon' => $ism_list['stumbleupon'],
																'vk' => $ism_list['vk'],
																'reddit' => $ism_list['reddit'],
																'print' => $ism_list['print'],
																'email' => $ism_list['email'], 
																);
												foreach($sm_items as $k=>$v){
													?>
														<option value="<?php echo $k;?>"><?php echo $v;?></option>
													<?php 	
												}
											?>											
											</select>			                            
			                            </td>
			                            <td>
			                            	URL: <input type="text" value="" name="the_url" style="width: 400px;"/>
			                            </td>
			                            <td>
			                            	Counts: <input type="number" value="" name="the_counts" style="width: 60px;" />
			                            </td>
			                            <td>
			                           	    <div class="submit" style="padding: 0px 10px;">
	                    						<input type="submit" value="Save" name="g_submit_bttn" class="button button-primary button-large" />
	                						</div>
			                            </td>
			                         </tr>
			                         <tr>
			                         	<td colspan="5">
			                         		<span class="ism-info">Type 'all' in URL Section if You want to set for all URLs!</span>
			                         	</td>
			                         </tr>
			                    </tbody>
			                </table>
				        </div>
				     </div>

				        <div class="stuffbox" >
			        	    <h3>
                				<label>List Of Initial Counts:</label>
            				</h3>
		            		<div class="inside">
		            					<table>
		            						<tr>
		            							<td>
		            							<table class="special_counts_table" cellspacing="0">
		            					<?php 
		            						$i=0;
		            						$all_sc = ism_return_special_counts('all');
		            						
				                         		foreach($sm_items as $k=>$v){
				                         			$arr = ism_return_special_counts($k);
				                         			if($arr && count($arr)>0){
					                         			foreach($arr as $key=>$value){	
					                         				if(isset($value) && $value!=''){
					                         					?>
					                         						<tr id="special_count_<?php echo $i;?>">
					                         							<td><?php echo $v;?></td> 
					                         							<td>
					                         								<a href="<?php echo $key;?>" target="_blank">
					                         									<?php echo $key;?>
					                         								</a>					                         							
					                         							</td>
					                         							<td><?php echo $value;?></td>
					                         							<td><i class="icon-trash" title="Delete" onClick="ism_deleteSpecialCount('<?php echo $key;?>', '<?php echo $k;?>', '#special_count_<?php echo $i;?>');"></i></td>					                         				
					                         						</tr>
					                         					<?php 
					                         					$i++;
					                         				}
					                         			}
				                         			}
				                         			if(isset($all_sc[$k]) && $all_sc[$k]!=''){
				                         				?>
				                         					<tr id="special_count_all_<?php echo $i;?>">
				                         						<td><?php echo $v;?></td> 
				                         						<td>All URLs</td> 
				                         						<td><?php echo $all_sc[$k];?></td>
				                         						<td><i class="icon-trash" title="Delete" onClick="ism_deleteSpecialCount('all', '<?php echo $k;?>', '#special_count_all_<?php echo $i;?>');"></i></td>				                         				
				                         					</tr>
				                         				<?php 
				                         				$i++;	
				                         			}
				                         		}
				                        ?>
		            							</table>
		            							</td>
		            						</tr>
		            					</table>
		            		</div>
	            		</div>
			    </form>
			</div>
		<?php     	
    break;
    
    case 'mobile_display':
    	/*************************** MOBILE DISPLAY *************************/
    	if(isset($_REQUEST['md_submit_bttn'])){
    		ism_return_arr_update( 'md' );
    	}
    	//default settings
    	$meta_arr = ism_return_arr_val( 'md' );
		?>
		<div class="metabox-holder indeed">
		    <form method="post" action="">
		        <div class="stuffbox">
		            <h3>
		                <label>Social Network List:</label>
		            </h3>
		            <div class="inside">
		                  <div style="display:inline-block;vertical-align: top;margin-top: 20px;">
		                      <?php
		                          $sm_items = ism_return_labels_for_checkboxes();
		                          $i = 1;
		                          foreach($sm_items as $k=>$v){
		                              $checked = ism_check_select_str($meta_arr['md_list'], $k);
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
		                                        <img src="<?php echo ISM_DIR_URL;?>/files/images/icons/<?php echo $k;?>.png" class="indeed_icons_admin" />
		                                        <?php echo $v;?>
		                                    </div>
                                			<div style="display:inline-block;line-height: 1.3;padding: 7px 5px;">
		                                        <input type="checkbox" value="<?php echo $v;?>" id="" onClick="make_inputh_string(this, '<?php echo $k;?>', '#sm_items');" class="" <?php echo $checked;?>/>
		                                	</div>
		                               </div>
		                              <?php
		                          }
		                      ?>
		                      <input type="hidden" value="<?php echo $meta_arr['md_list'];?>" name="md_list" id="sm_items"/>
		                	</div>
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>
		            </div>
		        </div>

		        <div class="stuffbox">
		            <h3>
		                <label>Special Mobile Templates:</label>
		            </h3>
		            <div class="inside" style="vertical-align: top;">
		                <table class="form-table">
			                <tbody>
		                        <tr valign="top">
		                            <th scope="row">
		                                Select a Template:
		                            </th>
		                            <td>
		                                <select id="special_mobile_template" name="md_mobile_special_template" onChange="">
		                                    <?php
		                                    	$mobile_special_templates = array(	
		                                    										'' => '...',
		                                    										'ism_template_mob_1' => 'Mobile Template 1',
		                                    										'ism_template_mob_2' => 'Mobile Template 2',
		                                    										'ism_template_mob_3' => 'Mobile Template 3',
		                                    									 );
		                                        foreach($mobile_special_templates as $key=>$value){
		                                            $select = ism_checkSelected($meta_arr['md_mobile_special_template'], $key, 'select');
		                                            ?>
		                                                <option value="<?php echo $key;?>" <?php echo $select;?>><?php echo $value;?></option>
		                                            <?php
		                                        }
		                                    ?>
		                                </select>
										
		                            </td>
		                        </tr>
								</tbody>
		                </table>
						<span class="ism-info">The Mobile Templates are Predefined Built and will restrict the other options from <strong>Mobile Display</strong> section! </span>
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>		                		            
		            </div>
		        </div>		    
		        
		        <div class="stuffbox">
		            <h3>
		                <label>Standard Template:</label>
		            </h3>
		            <div class="inside" style="vertical-align: top;">
		                <table class="form-table">
			                <tbody>
		                        <tr valign="top">
		                            <th scope="row">
		                                Select a Template:
		                            </th>
		                            <td>
		                                <select id="template" name="md_template" onChange="ism_preview_templates_be();ism_change_dropdown('#special_mobile_template', '');">
		                                    <?php
		                                        foreach(ism_return_templates() as $key=>$value){
		                                            $select = ism_checkSelected($meta_arr['md_template'], $key, 'select');
		                                            ?>
		                                                <option value="<?php echo $key;?>" <?php echo $select;?>><?php echo $value;?></option>
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
		                                <?php
		                                    $checked = ism_checkSelected('vertical', $meta_arr['md_list_align'], 'radio');
		                                ?>
		                                <input type="radio" name="md_list_align" value="vertical" <?php echo $checked;?> class="" /><span class="indeedcrlabel">Vertical</span>
		                                <?php
		                                    $checked = ism_checkSelected('horizontal', $meta_arr['md_list_align'], 'radio');
		                                ?>
		                                <input type="radio" name="md_list_align" value="horizontal" <?php echo $checked;?> class="" /><span class="indeedcrlabel">Horizontal</span>
		                            </td>
		                        </tr>
		                        <tr>
		                        	<td colspan="2" style="margin:0px;padding:0px;">
		                        		<span class="ism-info">Vertical Align is not available for Predifined Position!</span>
		                        	</td>
		                        </tr>
		                	</tbody>
		                </table>
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>
		            </div>
				</div>
				
		        <div class="stuffbox">
		            <h3>
		                <label>Options:</label>
		            </h3>
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
			                                <?php
			                                    $checked = ism_checkSelected('true', $meta_arr['md_display_counts'], 'checkbox');
			                                ?>
			                                <input type="checkbox" onClick="ism_check_and_h(this, '#display_counts');" class="ism-switch" <?php echo $checked;?> />
			                            	<div class="switch" style="display:inline-block;"></div>
										</label>	
		                                <input type="hidden" value="<?php echo $meta_arr['md_display_counts'];?>" name="md_display_counts" id="display_counts" />
		                            </td>
		                        </tr>
		                        <tr valign="top">
		                            <th scope="row">
		                                Display Full Name Of Social Network
		                            </th>
		                            <td>
		                            	<label>
			                                <?php
			                                    $checked = ism_checkSelected('true', $meta_arr['md_display_full_name'], 'checkbox');
			                                ?>
			                                <input type="checkbox" onClick="ism_check_and_h(this, '#md_display_full_name');" class="ism-switch" <?php echo $checked;?> />
			                            	<div class="switch" style="display:inline-block;"></div>
										</label>			                             
		                                <input type="hidden" value="<?php echo $meta_arr['md_display_full_name'];?>" name="md_display_full_name" id="md_display_full_name" />
		                            </td>
		                        </tr>
		                    </tbody>
		                </table>
						<span class="ism-info">For some <strong>Special Mobile Templates</strong> those options are not available! </span>
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>
		            </div>
		        </div>		
        				
		        <div class="stuffbox">
		            <h3>
		                <label>Position <span style="font-weight: 500; font-size: 15px;">(only for Standard Templates)</span>:</label>
		            </h3>
		            <div class="inside">
		                <table class="form-table">
			                <tbody>
		                        <tr valign="top">
		                            <th scope="row">
		                               Floating:
		                            </th>
		                            <td>
		                                <?php $checked = ism_checkSelected($meta_arr['md_floating'], 'yes', 'radio');?>
		                                <input type="radio" name="md_floating" value="yes" <?php echo $checked;?> /><span class="indeedcrlabel">Yes</span>
		                                <?php $checked = ism_checkSelected($meta_arr['md_floating'], 'no', 'radio');?>
		                                <input type="radio" name="md_floating" value="no" <?php echo $checked;?> /><span class="indeedcrlabel">No</span>
										<span class="ism-info" style="display:inline-block; font-style:italic;">The Social Icons will stay all the time on the screen despite the scroll position</span>
		                            </td>
		                        </tr>

		                        <tr valign="top">
		                            <th scope="row">
		                                Predifined Position
		                            </th>
		                            <td style="padding: 0px;">
		                               <table>
		                               	  <tr>
		                               	  	  <td>
		                               	  	    <label>
		                               	  	    	<?php $checked = ism_checkSelected($meta_arr['md_pred_position'], 1, 'checkbox');?>	
		                               	  	      	<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#md_pred_position');ism_c_opacity(this, '#predefined_position', '#custom_position', '#enable_custom_pos', '#md_custom_position');" id="enable_pred_pos" <?php echo $checked;?>/>                               	  	      
    	                						  	<div class="switch" style="display:inline-block;"></div>
    	                						</label>	
    	                						<input type="hidden" value="<?php echo $meta_arr['md_pred_position'];?>" name="md_pred_position" id="md_pred_position" />	                               	  	
		                               	  	  </td>
		                               	  </tr> 
					                        <?php 
					                        	$opacity = 1;
					                        	if(isset($meta_arr['md_custom_position']) && $meta_arr['md_custom_position']==1){
					                        		$opacity= 0.5;	
					                        	}
					                        ?>		                               	
		                               	  <tr id="predefined_position" style="opacity: <?php echo $opacity;?>">		                       		                            
					                            <td style="padding:0px;">
					                                <?php $checked = ism_checkSelected($meta_arr['md_predefined_position'], 'bottom', 'radio');?>
					                                <input type="radio" name="md_predefined_position" value="bottom" <?php echo $checked;?> /><span class="indeedcrlabel">Bottom Full Width</span>
					                                <?php $checked = ism_checkSelected($meta_arr['md_predefined_position'], 'top', 'radio');?>
					                                <input type="radio" name="md_predefined_position" value="top" <?php echo $checked;?> /><span class="indeedcrlabel">Top Full Width</span>
					                                <div style="margin-top: 10px;margin-bottom: 20px;">
					                                	<?php $checked = ism_checkSelected($meta_arr['md_behind_bk'], 1, 'checkbox');?>
					                                	<input type="checkbox" onClick="check_and_h(this, '#md_behind_bk');" <?php echo $checked;?> />
					                                	<input type="hidden" id="md_behind_bk" name="md_behind_bk" value="<?php echo $meta_arr['md_behind_bk'];?>" />					                              
					                                	With Transparent Background 
					                                </div>		                            
					                            </td>
					                     </tr>
					                  </table>
					                </td>
		                        </tr>
		                        <tr valign="top" >
		                            <th scope="row">
		                                Custom Position
										<span class="ism-info">Set the Box's icons position. Can be used positive or negative values.</span>
		                            </th>
		                            <td style="padding: 0px;">
		                               <table>
		                               	  <tr>
		                               	  	  <td colspan="2">
		                               	  	    <label>
		                               	  	    	<?php $checked = ism_checkSelected($meta_arr['md_custom_position'], 1, 'checkbox');?>	
		                               	  	      	<input type="checkbox" class="ism-switch" onClick="check_and_h(this, '#md_custom_position');ism_c_opacity(this, '#custom_position', '#predefined_position', '#enable_pred_pos', '#md_pred_position');" id="enable_custom_pos" <?php echo $checked;?>/>                               	  	      
    	                						  	<div class="switch" style="display:inline-block;"></div>
    	                						</label>	
    	                						<input type="hidden" value="<?php echo $meta_arr['md_custom_position'];?>" name="md_custom_position" id="md_custom_position" />	                               	  	
		                               	  	  </td>
		                               	  </tr> 
		                               </table>
				                        <?php 
				                        	$opacity = 1;
				                        	if(isset($meta_arr['md_pred_position']) && $meta_arr['md_pred_position']==1){
				                        		$opacity= 0.5;	
				                        	}
				                        ?>		                              
		                               <table id="custom_position" style="opacity: <?php echo $opacity;?>">	
		                                  <tr>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_top_bottom'], 'top', 'radio');?>
		                                      <td class="indeed_td_np"><input type="radio" class="" name="md_top_bottom" value="top" <?php echo $checked;?>/><span class="indeedcrlabel">Top</span></td>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_top_bottom'], 'bottom', 'radio');?>
		                                      <td class="indeed_td_np"><input type="radio" class="" name="md_top_bottom" value="bottom" <?php echo $checked;?>/><span class="indeedcrlabel">Bottom</span></td>
		                                  </tr>
										  <tr style="height:40px; vertical-align:top;">    
											  <td class="indeed_td_np"><input type="number" value="<?php echo $meta_arr['md_top_bottom_value'];?>" name="md_top_bottom_value" class="indeed_number" style="margin-top:4px;"/></td>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_top_bottom_type'], '%', 'radio');?>
		                                      <td class="indeed_td_np"><input type="radio" class="" name="md_top_bottom_type" value="%" <?php echo $checked;?>  style="margin-left:20px;"/><span class="indeedcrlabel" style="margin-right:3px;">% | </span>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_top_bottom_type'], 'px', 'radio');?>
		                                      <input type="radio" class="" name="md_top_bottom_type" value="px" <?php echo $checked;?>/><span class="indeedcrlabel">px</span></td>
		                                  </tr>
		                                  <tr>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_left_right'], 'left', 'radio');?>
		                                      <td class="indeed_td_np"><input type="radio" class="" name="md_left_right" value="left" <?php echo $checked;?>/><span class="indeedcrlabel">Left</span></td>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_left_right'], 'right', 'radio');?>
		                                      <td class="indeed_td_np"><input type="radio" class="" name="md_left_right" value="right" <?php echo $checked;?>/><span class="indeedcrlabel">Right</span></td>
		                                  </tr>
										  <tr>
											  <td class="indeed_td_np"><input type="number" value="<?php echo $meta_arr['md_left_right_value'];?>" name="md_left_right_value" class="indeed_number" style="margin-top:4px;"/></td>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_left_right_type'], '%', 'radio');?>
		                                      <td class="indeed_td_np"><input type="radio" class="" name="md_left_right_type" value="%" <?php echo $checked;?>  style="margin-left:20px;"/><span class="indeedcrlabel" style="margin-right:3px;">% | </span>
		                                          <?php $checked = ism_checkSelected($meta_arr['md_left_right_type'], 'px', 'radio');?>
		                                      <input type="radio" class="" name="md_left_right_type" value="px" <?php echo $checked;?>/><span class="indeedcrlabel">px</span></td>
		                                  </tr>
		                               </table>
		                            </td>
		                        </tr>
		                    </tbody>
		                </table>
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>
		            </div>
		        </div>
 
		        <div class="stuffbox">
		            <h3>
		                <label>Custom Show <span style="font-weight: 500; font-size: 15px;">(only for Standard Templates)</span>:</label>
		            </h3>
		            <div class="inside">
		                <table class="form-table">
			                <tbody>
		                        <tr valign="top">
		                            <th scope="row">
		                               Zoom:
		                            </th>
		                            <td>
		                            	<input type="number" value="<?php echo $meta_arr['md_zoom'];?>" name="md_zoom" min="0" max="1" step="0.01" style="width: 65px;" /> 
		                            </td>
		                        </tr>
		                        <tr valign="top">
		                            <th scope="row">
		                               Opacity:
		                            </th>
		                            <td>
		                            	<input type="number" value="<?php echo $meta_arr['md_opacity'];?>" name="md_opacity" min="0" max="1" step="0.01" style="width: 65px;" /> 
		                            </td>
		                        </tr>		                        
		                    </tbody>
		                </table>
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>
		            </div>
		        </div> 
		        		
		        <div class="stuffbox">
		            <h3>
		                <label>Where To Display:</label>
		            </h3>
		            <div class="inside">
					<span class="ism-info" style="padding-top:20px;">If none of them is not selected, the Social Icons will <strong>not show up</strong> using the "Mobile Display".</span>
		            <?php
		                  $ism_post_types = ism_return_post_types();
		            ?>
		                <table class="form-table">
		                    <tbody>
		                        <?php
		                            foreach($ism_post_types as $k=>$v){
		                                ?>
		                                <tr valign="top">
		                                    <th scope="row">
		                                        <?php echo ucfirst($v);?>
		                                    </th>
		                                    <td>
		                                        <?php $checked = ism_check_select_str($meta_arr['md_display_where'], $k);?>
		                                        <input type="checkbox" id="" class="" <?php echo $checked;?> onClick="make_inputh_string(this, '<?php echo $k;?>', '#display_where');"/>
		                                    </td>
		                                </tr>
		                                <?php
		                            }
		                        ?>
		                    </tbody>
		                </table>
		                    <input type="hidden" value="<?php echo $meta_arr['md_display_where'];?>" name="md_display_where" id="display_where" />
		                <div class="submit">
		                    <input type="submit" value="Save changes" name="md_submit_bttn" class="button button-primary button-large" />
		                </div>
		            </div>
		        </div>
		                	
			</form>
		</div>		
		<?php 
    break;

    
    case 'share_image':
    	isi_admin_page();
    break;
    	    
	case 'help':
		?>
			<div class="metabox-holder indeed">
				<div class="stuffbox">
					  <h3>
					    <label style="text-transform: uppercase; font-size:16px;">
					      Contact Support
					    </label>
					  </h3>
					  <div class="inside">
					  	  <div class="submit" style="float:left; width:80%;">
						  In order to contact Indeed support team you need to create a ticket providing all the necessary details via our support system: support.wpindeed.com
						  </div>
						  <div class="submit" style="float:left; width:20%; text-align:center;">
						  		<a href="http://support.wpindeed.com/open.php?topicId=12" target="_blank" class="button button-primary button-large"> Submit Ticket</a>
						  </div>
						  <div class="clear"></div>
					  </div>
				</div>
		<div class="stuffbox">
		  <h3>
		    <label style="text-transform: uppercase; font-size:16px;">
		      Documentation
		    </label>
		  </h3>
		  <div class="inside">
		  	  <iframe src="http://demoism.wpindeed.com/documentation/" width="100%" height="1000px" ></iframe>
		  </div>
		</div>	
			</div>
		<?php 
	break;
	
	case 'statistics':
		include_once ISM_DIR_PATH . 'includes/statistics-functions.php';
		include_once ISM_DIR_PATH . 'includes/statistics-page.php';	
	break;
}//end of switch
?>
</div>