///////////////////FACEBOOK
jQuery(document).ready(function(){
    window.fbAsyncInit = function() {
        FB.init({
          appId                : window.ism_facebook_id,
          status               : true,
          cookie               : true,
          xfbml                : true,
        });
      };
      // Load the SDK Asynchronously
      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;

        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
     }(document, "script", "facebook-jssdk"));
});

////fb share
function shareOnFacebook(i_title, i_link, i_img, locker_id, content_id, description_id ) {
	if(description_id!=''){
		desc = jQuery(description_id).attr('data-ism_description');
		if(desc!=undefined && desc!='') fb_description = jQuery(description_id).attr('data-ism_description');
		else fb_description = '';
	}
	FB.ui(
      {
        method        : 'feed',
        display       : 'iframe',
        name          :  decodeURIComponent(i_title),
        link          :  i_link,
        picture       :  i_img,
        description   :  fb_description
      },
      function(response) {
        if (response && response.post_id) {
            jQuery.jStorage.set(i_link, ism_return_current_date());
            if(locker_id!='0' && content_id!='0'){
                jQuery(locker_id).css('display', 'none');
                jQuery(content_id).css({
    		    	'display': 'block',
    		    	'opacity': 1
    		    });
            }
        }
      }
    );
}

function shareFacebookWI( url, title, locker_id, content_id, description_id ){
	if(description_id!=''){
		desc = jQuery(description_id).attr('data-ism_description');
		if(desc!=undefined && desc!='') fb_description = jQuery(description_id).attr('data-ism_description');
		else fb_description = '';
	}
    window.open('http://www.facebook.com/sharer/sharer.php?s=100&p[title]='+ decodeURIComponent(title) + '&p[url]=' + url + '&p[summary]=' + fb_description , 'sharer', 'toolbar=0,status=0,width=600,height=360');
    if(locker_id!='0' && content_id!='0'){
        jQuery.jStorage.set(url, ism_return_current_date());
        jQuery(locker_id).css('display', 'none');
        jQuery(content_id).css({
	    	'display': 'block',
	    	'opacity': 1
	    });
    }
}

///twitter share
jQuery(document).ready(function() {
	if(typeof window.ism_twitter_set!='undefined' && window.ism_twitter_set==1){
		
	    twttr.ready(function (twttr) {
	        twttr.events.bind('tweet', function(event) {
	              jQuery.jStorage.set(window.i_link, ism_return_current_date());
	              if(window.lockerId!='0' && window.contentId!='0'){
	                  jQuery(window.lockerId).css('display', 'none');
	                  jQuery(window.contentId).css({
	      		    	'display': 'block',
	      		    	'opacity': 1
	      		    });
	              }
	        });
	      });	
	    
	}
});

////linkedin share with callback
function ism_linkedin_share(ism_link, locker_id, content_id){	
	share_window = IN.UI.Share().params({
		url: ism_link
	}).place();

	//LOCKER
    if(locker_id!='0' && content_id!='0'){
		share_window.success(function() {
			// callback function
		    jQuery.jStorage.set(ism_link, ism_return_current_date());
		    jQuery(locker_id).css('display', 'none');
		    jQuery(content_id).css({
		    	'display': 'block',
		    	'opacity': 1
		    });			
		});	
	}
}

////
function setIds(lockerId, contentId, link){
    window.lockerId = lockerId;
    window.contentId = contentId;
    window.i_link = link;
}

function ism_fake_increment(target, type, url){
    var value = jQuery(target).html();
    if( value%1===0 && value!='' ) value++;
    jQuery(target).html(value);
    
    //share counts in db
    if(typeof window.ism_save_share_counts!='undefined' && window.ism_save_share_counts==1){
        jQuery.ajax({
            type : "post",
            url : window.ism_base_path+'/wp-admin/admin-ajax.php',
            data : {
                   		action: "ism_update_db_share_count_share_bttn_action",
                   		sm: type,
                   		the_url: url,
                   },
        });
    }
    
    //Statistics
    if(typeof window.ism_enable_statistics!='undefined' && window.ism_enable_statistics==1){
        jQuery.ajax({
            type : "post",
            url : window.ism_base_path+'/wp-admin/admin-ajax.php',
            data : {
                   		action: "ism_update_share_db_wd",
                   		sm: type,
                   		the_url: url,
                   },
        });    	
    }
}

function ism_load_counts(i, ism_url, wrapp_id, items){
        if(typeof items[i]=='undefined') return;
        the_sm_type = items[i];
        jQuery.ajax({
           type : "post",
           url : window.ism_base_path+'/wp-admin/admin-ajax.php',
           data : {
                  action: "ism_a_return_counts",
                  dir_url: ism_url,
                  sm_type: the_sm_type
                  },
           success: function(response){
               num = i+1;
               ism_load_counts(num, ism_url, wrapp_id, items);
               num = response;
               if(num=='') return;
               if(isNaN(num)) num = 0; //if num is not a number
                  setNumDiv(0, parseInt(num), wrapp_id+' .'+items[i]+'_share_count');
             }
         });
}

function setNumDiv(current, max, div){
    if(current>max) return;
    cnt = current;
    if(cnt>999){
    	if(cnt>999999){
    		cnt = cnt/1000000;
            type_num = 'M';
        }else{
        	cnt = cnt/1000;
            type_num = 'k';
        }
        if(cnt<=99 && cnt%1>0.09){
        	entire_num = cnt.toString();
            arr = entire_num.split('.');
            d = parseInt(arr[1][0]);
            if(arr[1][1]>=5) d = d + 1;
            	cnt = arr[0] +'.'+ d;
            }else cnt = parseInt(cnt);
            	cnt += type_num;
            }
  
    jQuery(div).html(cnt);
    setTimeout(function(){
        step = 1;
        if(max-current>1000) step = 500;
        if(max-current<1001) step = 300;
        if(max-current<501) step = 100;
        if(max-current<101) step = 10;
        if(max-current<11) step = 1;

        current=current+step;
        setNumDiv(current, max, div);
    },1);
}

function ism_load_counts_from_db(the_sm_list, ism_url, wrapp_id){
    jQuery.ajax({
       type : "post",
       url : window.ism_base_path+'/wp-admin/admin-ajax.php',
       data : {
              action: "ism_get_sm_db_share_counts_return_list",
              the_url: ism_url,
              sm_list: the_sm_list,
              },
       success: function(response){
    	   			response = jQuery.parseJSON(response);
    	   			for(var key in response) {
 		               num = response[key];
 		               if(num!='not_show'){
 	 		               if(num==null || isNaN(num)) num = 0;
 			               setNumDiv(0, parseInt(num), wrapp_id+' .'+key+'_share_count');   		            	   
 		               }   	   					
    	   			}
         		}		
     });

}

function indeedPopUpEmail(ism_url, currentURL){
    jQuery.ajax({
        type : "post",
        data : {
                    action: "ism_send_email_ajax_popup",
                    url: ism_url,
                    permalink: currentURL
                },
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        success: function (data) { jQuery(data).hide().appendTo('body').fadeIn('normal'); },
        dataType: 'html'
    });
}

function indeedPrintFriendlyCount(ism_url, postId){
	    jQuery.ajax({
			type : "post",
			data : {
						action: "ism_print_friendly",
						the_url: ism_url,
					},
			url : window.ism_base_path+'/wp-admin/admin-ajax.php',
			success: function (data) { jQuery(data).hide().appendTo('body').fadeIn('normal'); },
			dataType: 'html'
    	});
	
}

function ism_func_sendEmail(){
    jQuery('#loading_img').css('display', 'inline-block');
    jQuery.ajax({
        type : "post",
        data : {
                    action: "ism_sendEmail",
                    name: jQuery('#ism_email_name').val(),
                    from: jQuery('#ism_email_from').val(),
                    sentTo: jQuery('#ism_email_sentto').val(),
                    subject: jQuery('#ism_email_subject').val(),
                    message: jQuery('#ism_email_message').val(),
                    capcha: jQuery('#capcha_answer').val(),
                    capcha_key: jQuery('#cp_ar_k').val(),
                    the_url: jQuery('#popup_ism_theurl').val(),
                },
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        success: function (data) {
            jQuery('#loading_img').fadeOut();
            if(jQuery('#capcha_error').length) jQuery('#capcha_error').remove();
            if(jQuery('#ism_form_error').length) jQuery('#ism_form_error').remove();
            if(data==1){
                jQuery('.popup_content').append('<p id="succes_message" class="popup_success">'+jQuery('#email_success_message').val()+'</p>');
                setTimeout(function(){
                    closePopup();
                }, 2000);
            }else if(data==2){
                //capcha error
                jQuery('.popup_content').append('<p class="popup_error" id="capcha_error">Wrong Answer!</p>');
            }else{
                //error
                jQuery('.popup_content').append('<p  class="popup_error" id="ism_form_error">Something Went Wrong. Please Try Again!</p>');
            }
        },
        dataType: 'html'
    });
}

function closePopup(){
    jQuery('#popup_box').fadeOut('normal', function(){
        jQuery('#popup_box').remove();
    });
}

jQuery(document).ready(function(){
	jQuery('.ism-before-row').each(function(){
		ism_content_id = jQuery(this).attr('data-id');
		ism_locker_id = jQuery(this).attr('data-lockerId');
		ism_vc_set = jQuery(this).attr('data-vc_set');
		url = jQuery(this).attr('data-ism_url');
		overlock = jQuery(this).attr('data-ism_overlock');

		v = jQuery.jStorage.get(url);

        if(v){       	
            //show content, hide locker
        	jQuery('#'+ism_locker_id).css('display', 'none');
        	jQuery('#'+ism_content_id).css('display', 'block');
        }else{
            //show locker hide content
    		if(overlock=='default'){
    			//DEFAULT LOCKER
            	jQuery('#'+ism_locker_id).css('display', 'block');
            	if(ism_vc_set==1){
            		//visual composer
            		jQuery(this).next().attr('id', ism_content_id);
            		jQuery(this).next().css('display', 'none');    
            	}else{
            		jQuery('#'+ism_content_id).css('display', 'none');
            	}
            	jQuery('#'+ism_locker_id+', #'+ism_content_id+' ').wrapAll('<div class="ismLockerWrap" style="position: relative;">');    			
    		
    		}else{
    			//LOCKER OVER CONTENT (opacity)
            	if(ism_vc_set==1){
            		//visual composer
            		jQuery(this).next().attr('id', ism_content_id);
            	}

            	locker_height = jQuery('#'+ism_locker_id).outerHeight();
    			margin_top = parseInt(locker_height)/2;
    			margin_top = '-'+margin_top+'px';

    			jQuery('#'+ism_locker_id).css({
    			    'display': 'block', 
    			    'position': 'absolute',
    			    'top': '50%',
    			    'margin-top': margin_top,
    			    'left': '0px',
    			    'width': '100%',
    			    'z-index': 2,
    			});
    			jQuery('#'+ism_content_id).css({
    				'opacity': '0.15',
    				'display': 'block',
    			});
    			jQuery('#'+ism_locker_id+', #'+ism_content_id+' ').wrapAll('<div class="ismLockerWrap" style="position: relative;min-height: '+locker_height+'px;">'); 
    			
    		}

        }
        jQuery(this).remove();       
        
	});
});



///pinterest
function openPinterestShare(){
	closePopup();
	if(window.pinterest_feat_img && window.pinterest_feat_img!=''){
		window.open('http://pinterest.com/pin/create/bookmarklet/?media='+window.pinterest_feat_img+jQuery('#pin_hide_info').val(), 'sharer', 'toolbar=0,status=0,width=750,height=330');
	}else{
		window.open('http://pinterest.com/pin/create/bookmarklet/?media='+jQuery('#pin_default_feat_img').val()+jQuery('#pin_hide_info').val(), 'sharer', 'toolbar=0,status=0,width=750,height=330');
	}
}

function indeedPinterestPopUp(rand_pin){
	window.pinterest_feat_img = '';
	//getting the images
	parent = jQuery('#indeed_top_ism').parent();
	if(jQuery(parent).attr('id')) selector = '#'+jQuery(parent).attr('id');
	else selector = '.'+jQuery(parent).attr('class');

	var imgSrc = new Array();
	jQuery(selector+" img").each(function(){
		if(jQuery(this).attr('src')) imgSrc.push(jQuery(this).attr('src'));
	});
	def_img = jQuery('#pin_default_feat_img_'+rand_pin).val();
	if(jQuery.inArray(def_img, imgSrc)===-1) imgSrc.push(def_img);
	//send request
    jQuery.ajax({
        type : "post",
        data : {
                    action: "ism_pinterest_popup",
                    ism_images: imgSrc,
                    other_info: jQuery('#pin_hide_info_'+rand_pin).val()
                },
        url : window.ism_base_path+'/wp-admin/admin-ajax.php',
        success: function (data) { jQuery(data).hide().appendTo('body').fadeIn('normal'); },
        dataType: 'html'
    });
}

function pinterest_select_img(target){
	window.pinterest_feat_img = jQuery(target).attr('src');
	openPinterestShare();
}

///TIMEOUT LOCKER
function ism_the_TimeOut(timeout, content_id, locker_id){
	var id = '#line_'+locker_id;
	locker_id = '#'+locker_id;
	var wd = 100;
	timeout = timeout *10;
	counter = setTimeout(ism_countDown, timeout);
	function ism_countDown(){
		if(wd<=0){
			jQuery(locker_id).css('display', 'none');
			jQuery(content_id).css({
		    	'display': 'block',
		    	'opacity': 1
		    });
			return;
		}
		wd = wd - 1;
		jQuery(id).css('width', wd+'%');
		setTimeout(ism_countDown, timeout);
	}		
}

function ismMobilePopup(id, the_direction){
	jQuery(id).toggle('slide', { direction: the_direction}, 500);
}
function ismMoveDiv(id){
	if(jQuery(id).css('left')=='-45px'){
		jQuery(id).animate({ "left": "+=45px" }, "slow" );		
	}else{
		jQuery(id).animate({ "left": "-=45px" }, "slow" );
	}

}