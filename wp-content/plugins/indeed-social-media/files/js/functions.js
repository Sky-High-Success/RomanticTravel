function make_inputh_string(divCheck, showValue, hidden_input_id){
    str = jQuery(hidden_input_id).val();
    if(str!='') show_arr = str.split(',');
    else show_arr = new Array();
    if(jQuery(divCheck).is(':checked')){
        show_arr.push(showValue);
    }else{
        var index = show_arr.indexOf(showValue);
        show_arr.splice(index, 1);
    }
    str = show_arr.join(',');
    jQuery(hidden_input_id).val(str);
}
function ism_shortcode_update(tab){
    // CREATE SHORTCODE
    if(tab=='shortcode_locker') var str = "[indeed-social-locker ";
    else var str = "[indeed-social-media ";
    str += "sm_list='"+jQuery('#sm_items').val()+"' ";
    str += "sm_template='"+jQuery('#template').val()+"' ";
    str += "sm_list_align='"+jQuery('#list_align_type').val()+"' ";
    if(jQuery('#display_counts').is(':checked')) str += "sm_display_counts='true' ";
    else str += "sm_display_counts='false' ";
    if(jQuery('#display_full_name').is(':checked')) str += "sm_display_full_name='true' ";
    else str += "sm_display_full_name='false' ";
    if( tab=='shortcode' && jQuery('#disable_mobile').is(':checked') ){
    	str += "sm_disable_mobile=1 ";
    }
    if(tab=='shortcode_locker'){
    	str += "locker_template="+jQuery('#locker_template').val()+" ";
    	if(jQuery('#locker_template').val()==1){
            str += "sm_lock_padding=" + jQuery('#locker_padding').val() + " ";
            str += "sm_lock_bk='" + jQuery('#locker_background').val() + "' ";    		
    	}
        locker_text = jQuery('#display_text').val();
        locker_text = locker_text.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        str += "sm_d_text='" + locker_text + "' ";
        if(jQuery('#not_registered_u').val()==1) str += "not_registered_u=1 ";
        if(jQuery('#enable_timeout_lk').val()==1){
        	str += "enable_timeout_lk=1 ";
        	if(jQuery('#ism_timeout_locker').val()!='') str += "sm_timeout_locker="+jQuery('#ism_timeout_locker').val()+" ";
        }        
        if( jQuery('#reset_locker').val()==1 && jQuery('#locker_reset_after').val()!='' ){
        	str += "reset_locker=1 ";
        	str += "locker_reset_after="+jQuery('#locker_reset_after').val()+" ";
        	str += "locker_reset_type='"+jQuery('#locker_reset_type').val()+"' ";        	
        }      
        str += "ism_overlock='"+jQuery('#ism_overlock').val()+"' ";
        
        //end of locker
        str += "]&nbsp;&nbsp;&nbsp;";
        str += "[/indeed-social-locker]";
        //AJAX CALL
        ism_preview_locker(str);        
    }else str += "]";
    if(jQuery('#sm_items').val()!=''){
        jQuery('.the_shortcode').html(str);
        jQuery(".php_code").html('&lt;?php echo do_shortcode("'+str+'");?&gt;');
    }else{
        msg = "Please Select Some Social Media Buttons";
        jQuery('.the_shortcode').html(msg);
        jQuery(".php_code").html(msg);
    }
}
function check_and_h(from, target){
	if (jQuery(from).is(":checked")) jQuery(target).val(1);
	else jQuery(target).val(0);
}
function ism_check_and_h(from, where){
	if (jQuery(from).is(":checked")) jQuery(where).val('true');
	else jQuery(where).val('false');
}
///preview templates
function ism_preview_templates_be(){
    jQuery('#ism_preview').fadeOut(100);
      jQuery.ajax({
         type : "post",
         url : window.ism_base_path+'/wp-admin/admin-ajax.php',
         data : {
                    action: "ism_admin_items_preview",
                    template: jQuery('#template').val(),
                },
         success: function(response){
                jQuery('#ism_preview').html(response);
                jQuery('#ism_preview').fadeIn(600);
         }
      });
}
jQuery(document).on('keyup', '#display_text', function() {
    ism_shortcode_update('shortcode_locker');
});
jQuery(document).on('blur', '#display_text', function() {
    ism_shortcode_update('shortcode_locker');
});
function updateTextarea(){
    content = jQuery( "#display_text_ifr" ).contents().find( '#tinymce' ).html();
    jQuery('#display_text').val(content);
    ism_shortcode_update('shortcode_locker');
}
jQuery(document).on('click', '#display_text-html', function() {
    jQuery('#ism_update_textarea_bttn').css('display', 'none');
});
jQuery(document).on('click', '#display_text-tmce', function() {
    jQuery('#ism_update_textarea_bttn').css('display', 'block');
});
jQuery(window).bind('load', function(){
    display = jQuery('#display_text').css('display');
    if(display=='none') jQuery('#ism_update_textarea_bttn').css('display', 'block');
});

function ism_deleteSpecialCount(url, type, div){
    jQuery.ajax({
        type : "post",
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        data : {
            action: "ism_delete_special_count",
            the_url: url,
            the_type: type
        },
	    success: function(data){
    			jQuery(div).fadeOut(600); 
	    },
    });
}

//PREVIEW LOCKER 
function ism_preview_locker(the_shortcode){
    jQuery.ajax({
        type : "post",
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        data : {
            action: "ism_preview_shortcode",
            shortcode: the_shortcode,	
        },
	    success: function(data){
    			jQuery('#ISM_preview_shortcode').html(data); 
	    },
    });	
}
function ism_disable_style_table(value){
	if(value==1){
		jQuery('#ism_shortcode_style-table').fadeIn();
		return;
	}else{
		jQuery('#ism_shortcode_style-table').fadeOut();
	}
	
}

//////////////////////MIN COUNTS
function ism_update_minim_counts(){
	jQuery('#ism_near_bttn_loading').css('visibility', 'visible');
    jQuery.ajax({
        type : "post",
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        data : {
            action: "ism_update_min_count",
            sm: jQuery('#sm_type_min_count').val(),
            count: jQuery('#sm_min_count_value').val()
        },
	    success: function(data){
	    	if(parseInt(data)==1){
	    		ism_update_html_min_counts();
	    	}
	    	jQuery('#ism_near_bttn_loading').css('visibility', 'hidden');
	    },
    });	
}
function ism_update_html_min_counts(){
    jQuery.ajax({
        type : "post",
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        data : {
            action: "ism_return_min_count_table",
        },
        success: function(data){
        	if(data!=0){
        		jQuery('#ism_minim_counts_table').html(data);
        	}
        },
    });	
}
function ism_deleteMinCount(value, id){
    jQuery.ajax({
        type : "post",
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        data : {
            action: "ism_delete_min_count",
            sm: value,
        },
        success: function(data){
	    	if(parseInt(data)!=0){
	    		jQuery(id).fadeOut();
	    	}
        },
    });		
}

function ism_enable_disable_c(check, target){
	if (jQuery(check).is(":checked")) jQuery(target).removeAttr('disabled');
	else jQuery(target).attr('disabled', 'disabled'); 
}

function ism_c_opacity(check_id, div, a_parent, a_check, h_id){
	if(jQuery(check_id).is(':checked')){
		jQuery(div).css('opacity', '1');
		jQuery(a_parent).css('opacity', '0.5');
		jQuery(a_check).prop('checked', false);
		jQuery(h_id).val(0);
	}else{
		jQuery(div).css('opacity', '0.5');
	}
	
}

function ism_change_dropdown(d, v){
	//d is id and v is the new value
	jQuery(d).val(v);
}

function ism_clear_statistic_data(){
	jQuery('#ism_near_bttn_clear_statistic').css('visibility', 'visible');
	var o = jQuery('#clear_statistic').val();//clear data older than
    jQuery.ajax({
        type : "post",
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        data : {
            action: "ism_delete_statistic_data",
            older_than: o,
        },
        success: function(data){
	    	if(parseInt(data)!=0){
	    		jQuery('#ism_near_bttn_clear_statistic').css('visibility', 'hidden');
	    	}
        },
    });		
}
