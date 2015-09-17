<?php
        //templates
        $ism_templates = ism_return_templates();

        /////////////////////SHARE SHORTCODE
        vc_map( array(
        			   "name" => "Indeed Social Share",
        			   "base" => "indeed-social-media",
        			   "icon" => "ism_vc_logo",
                       "description" => "Indeed Social Share",
    			       "class" => "indeed-social-media",
    			       "category" => __('Content', "js_composer"),
        			   "params" => array(
                                        array(
                                                "type" => "ism_checkbox_icons",
                                                "label" => "Social Network List",
                                                "value" => 'fb,tw,goo,pt,li,dg,tbr,su,vk,rd,dl,wb,xg,pf,email',
                                                "param_name" => "sm_list",
												"heading" => "Social Networks",
                                                "admin_label" => TRUE,
                                                "ism_items" => ism_return_labels_for_checkboxes(),
                                            ),
                                        array(
                                                "type" => "ism_dropdown_picture",
                                                "label" => "Social Network List",
                                                "value" => '',
                                                "onchange" => 'ism_preview_templates_be();',
                                                "onload_script" => 'ism_base_path="'.get_site_url().'";jQuery(document).ready(function(){ism_preview_templates_be();});',
                                                "param_name" => "template",
                                                "ism_items" => $ism_templates,
                                                "ism_select_id" => "template",
                                                "aditional_info" => "Some of the templates are recommended for Vertical Align (like template 9) and others for Horizontal Align (like template 5). Check the Hover Effects!",
                                            ),
                                        array(
                                                      "type" => "ism_return_radio",
                                                      "param_name" => "list_align",
                                                      "ism_items" => array('vertical'=>"Vertical", 'horizontal'=>"Horizontal"),
                                                      "id_hidden" => "hidden_list_align_type",
                                                      "ism_label" => "List Align",
                                                      "value" => "horizontal",
                                                      "aditional_info" => "Select how the the list should be displayed.",
                                              ),
                                        array(
                                                "type" => "ism_return_checkbox",
                                        		"onClick_function" => "ism_check_and_h",
                                                "param_name" => "display_counts",
                                                "id_hidden" => "display_counts",
                                                "checkbox_id" => "checkbox_display_counts",
                                                "value" => 'false',
                                                "ism_label" => "Display Counts",
                                                "aditional_info" => "Number of shares on each network will be displayed.",
                                              ),
                                        array(
                                                "type" => "ism_return_checkbox",
                                                "param_name" => "display_full_name",
                                        		"onClick_function" => "ism_check_and_h",
                                                "id_hidden" => "display_full_name",
                                                "checkbox_id" => "checkbox_display_full_name",
                                                "value" => 'false',
                                                "ism_label" => "Display Full Name Of Social Network",
                                                "aditional_info" => "",
                                              ),
                                    )
                       )
               );

        //////////////////////////LOCKER SHORTCODE
	if (function_exists('vc_add_param')) {
		// Row Setting Parameters
		vc_add_param("vc_row", array(
			"type"              			=> "seperator",
			"heading"           			=> "Indeed Social Locker",
			"param_name"        			=> "seperator_indeed_locker",
			"value"             			=> "Indeed Social Locker",
			"group" 						=> "Indeed Social Locker",
		));
		vc_add_param("vc_row", array(
                                                    "type" => "ism_checkbox_icons",
                                                    "label" => "Social Network List",
                                                    "value" => '',
                                                    "param_name" => "sm_list",
                                                    "ism_items" => ism_return_labels_for_checkboxes(true),
                                                    "ism_select_id" => "template",
													"group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
                                                      "type" => "ism_dropdown_picture",
                                                      "label" => "Social Network List",
                                                      "value" => '',
                                                      "onchange" => 'ism_preview_templates_be();',
                                                      "onload_script" => 'ism_base_path="'.get_site_url().'";jQuery(document).ready(function(){ism_preview_templates_be();});',
                                                      "param_name" => "template",
                                                      "ism_items" => $ism_templates,
                                                      "ism_select_id" => "template",
                                                      "aditional_info" => "Some of the templates are recommended for Vertical Align (like template 9) and others for Horizontal Align (like template 5). Check the Hover Effects!",
													  "group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
                                                      "type" => "ism_return_radio",
                                                      "param_name" => "list_align",
                                                      "ism_items" => array('vertical'=>"Vertical", 'horizontal'=>"Horizontal"),
                                                      "id_hidden" => "hidden_list_align_type",
                                                      "ism_label" => "List Align",
                                                      "value" => "horizontal",
                                                      "aditional_info" => "Select how the the list should be displayed.",
				                                      "group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
                                                      "type" => "ism_return_checkbox",
                                                      "param_name" => "display_counts",
                                                      "id_hidden" => "hidden_display_counts",
													  "onClick_function" => "ism_check_and_h",
                                                      "checkbox_id" => "checkbox_display_counts",
                                                      "value" => 'true',
                                                      "ism_label" => "Display Counts",
                                                      "aditional_info" => "Number of shares on each network will be displayed.",
													  "group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
                                                      "type" => "ism_return_checkbox",
                                                      "param_name" => "display_full_name",
                                                      "id_hidden" => "display_full_name",
													  "onClick_function" => "ism_check_and_h",
                                                      "checkbox_id" => "checkbox_display_full_name",
                                                      "value" => 'true',
                                                      "ism_label" => "Display Full Name Of Social Network",
                                                      "aditional_info" => "",
													  "group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
													"type" => "ism_return_dropdown",
													"ism_label" => "Locker Theme",
													"value" => '2',
													"onchange" => '',
													"param_name" => "locker_template",
													"ism_items" => array(1=>'Default', 2=>'Basic', 3=>'Zipped', 4=>'Zone', 5=>'Majic Transparent', 6=>'Star', 7=>'Clouddy', 8=>'Darks'),
													"ism_select_id" => "locker_template",
													"group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
													"type" => "ism_return_checkbox",
													"param_name" => "enable_timeout_lk",
													"id_hidden" => "enable_timeout_lk",
												    "onClick_function" => "check_and_h",
													"checkbox_id" => "checkbox_enable_timeout_lk",
													"value" => '0',
													"ism_label" => "Enable Delay Time",
													"aditional_info" => "",
													"group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
													"type" => "ism_return_number",
													"param_name" => "sm_timeout_locker",
													"ism_input_id" => "sm_lock_timeout",
													"value" => "30",
													"ism_label" => "Delay Time:",
													"count_type" => "sec(s)",
													"aditional_info" => "",
													"group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
													"type" => "ism_return_checkbox",
													"param_name" => "not_registered_u",
													"id_hidden" => "not_registered_u",
													"onClick_function" => "check_and_h",
													"checkbox_id" => "checkbox_not_registered_u",
													"value" => '0',
													"ism_label" => "Disable Locker For Registered Users",
													"aditional_info" => "",
													"group" => "Indeed Social Locker"
		));		
		vc_add_param("vc_row", array(
													"type" => "ism_return_checkbox",
													"param_name" => "reset_locker",
													"id_hidden" => "reset_locker",
													"onClick_function" => "check_and_h",
													"checkbox_id" => "checkbox_reset_locker",
													"value" => '0',
													"ism_label" => "Enable Reset Locker",
													"aditional_info" => "",
													"group" => "Indeed Social Locker"
		));			
		vc_add_param("vc_row", array(
													"type" => "ism_return_number",
													"param_name" => "locker_reset_after",
													"ism_input_id" => "locker_reset_after",
													"value" => "30",
													"ism_label" => "Reset Locker After:",
													"count_type" => "",
													"aditional_info" => "",
													"group" => "Indeed Social Locker"
		));	
		vc_add_param("vc_row", array(
													"type" => "ism_return_dropdown",
													"ism_label" => "Reset Locker After Type:",
													"value" => 'days',
													"onchange" => '',
													"param_name" => "locker_reset_type",
													"ism_items" => array('hours'=>"Hours", "days"=>"Days"),
													"ism_select_id" => "locker_reset_type",
													"group" => "Indeed Social Locker"
		));	
		vc_add_param("vc_row", array(
													"type" => "ism_return_dropdown",
													"ism_label" => "Overlock:",
													"value" => '',
													"onchange" => '',
													"param_name" => "ism_overlock",
													"ism_items" => array('default'=>"Default", "opacity"=>"Opacity"),
													"ism_select_id" => "ism_overlock",
													"group" => "Indeed Social Locker"
		));		
		vc_add_param("vc_row", array(
                                                      "type" => "colorpicker",
                                                      "heading" => "Background-Color:",
                                                      "param_name" => "sm_lock_bk",
                                                      "description" => "",
                                                      "edit_field_class" => 'col-md-6',
													  "group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
                                                      "type" => "ism_return_number",
                                                      "param_name" => "sm_lock_padding",
                                                      "ism_input_id" => "sm_lock_padding",
                                                      "value" => "50",
                                                      "ism_label" => "Padding:",
                                                      "count_type" => "px",
                                                      "aditional_info" => "General Padding for the Locker Box can be set.",
													  "group" => "Indeed Social Locker"
		));
		vc_add_param("vc_row", array(
                                                        "type" => "textarea_html",
                                                        "holder" => "div",
                                                        "heading" => "Locker Message",
                                                        "param_name" => "sm_d_text",
                                                        "value" => "<h2>This content is locked</h2><p>Share This Page To Unlock The Content!</p>",
														"group" => "Indeed Social Locker"
		));
    }
?>