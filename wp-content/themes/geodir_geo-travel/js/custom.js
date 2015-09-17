/*-----------------------------------------------------------------------------------*/
/*	Custom Script
/*-----------------------------------------------------------------------------------*/

jQuery.noConflict();
jQuery(document).ready(function($){
	
	  

	  // See if this is a touch device
	   if ('ontouchstart' in window)
	   {
	      // Set the correct [touchscreen] travel class
	      $('travel').removeClass('no-touch').addClass('touch');
	    // Add the touch toggle to show text when tapped
	      $('div.boxInner img').click(function(){
	         $(this).closest('.boxInner').toggleClass('touchFocus');
	      });

	   }
	   
	   $('.date-input-top').on('changeDate', function(ev){
		    $(this).datepicker('hide');
	   });
	   
	   
	   $(".date-input-top").on('focus',function(){
	        $(this).trigger('blur');
	    });
	   
	   
	   $("#package_list li a").click(function(){
		   
		   var selText = $(this).text();
		   var selValue = $(this).data("slug");
		   $("#des-hidden").val(selValue);
		   $("#des-text-hidden").val(selText);
		   $("#package_dropdown").html(selText+' <span class="caret"></span>');
		   
		 });
	   
       $("#package_list_mobile li a").click(function(){
		   
		   var selText = $(this).text();
		   var selValue = $(this).data("slug");
		   $("#des-hidden_mobile").val(selValue);
		   $("#des-text-hidden_mobile").val(selText);
		   $("#package_dropdown_mobile").html(selText+' <span class="caret"></span>');
		   
		 });
       
       $("#contact-method-div li a").click(function(){
		   
		   var selText = $(this).text();
		   
		   switch (selText){
			   case "Email": 
				   $("#phone-wrap").css("display","none");
				   $("#time-to-call-wrap").css("display","none");
				   break;
			   case "Phone": 
				   $("#phone-wrap").css("display","");
				   $("#time-to-call-wrap").css("display","");
				   break;
		   };
		   
		   $("#contact-method-hidden").val(selText);
		   
		   $("#contact-method .result").text(selText);
		   
       });
       
       $("#travel-occasion-div li a").click(function(){
		   
		   var selText = $(this).text();
		   
		   $("#travel-occasion-hidden").val(selText);
		   
		   $("#travel-occasion .result").text(selText);
		   
       });
       
       $("#spend-div li a").click(function(){
		   
		   var selText = $(this).text();
		   
		   $("#spend-hidden").val(selText);
		   
		   $("#spend .result").text(selText);
		   
       });
       
       $("#time-to-call-div li a").click(function(){
		   
		   var selText = $(this).text();
		   
		   $("#time-to-call-hidden").val(selText);
		   
		   $("#time-to-call .result").text(selText);
		   
       });
	   
       
       if((typeof search_date != 'undefined') && search_date[0].trim()){
    	   $.each($('.date-input-top'), function(){
    		   $(this).datepicker('setDate', new Date(search_date));
    	   });
       }
       
       if((typeof search_des != 'undefined') && search_des[0].trim()){
    	   $("#des-hidden").val(search_des);
    	   $("#des-hidden_mobile").val(search_des);
    	   $("#des-text-hidden").val(search_des_text);
    	   $("#des-text-hidden_mobile").val(search_des_text);
    	   $("#package_dropdown").html(search_des_text+' <span class="caret"></span>');
    	   $("#package_dropdown_mobile").html(search_des_text+' <span class="caret"></span>');	   
       }
       
	   
//	   $('#package_dropdown').click(function(){
//		   $('#package_list').empty();
//		   
//		   var date_input = $('.date-input-top').val();
//		   
//		   if( date_input != ""){
//			   
//			   var date = moment(date_input, "YYYY-MM-DD");
//			   
//			   for(x in package_arr){
//				   
//				   var from_arr = package_arr[x].from;
//				   var to_arr = package_arr[x].to;
//				   
//				   for(i=0; i<from_arr.length; i++){
//					   
//					   var from_date = moment(from_arr[i], "DD-MMM-YYYY");
//					   var to_date = moment(to_arr[i], "DD-MMM-YYYY");
//					   
//					   if(from_date <= date && date <= to_date){
//						   $('#package_list').append('<li role="presentation"><a role="menuitem" tabindex="-1" href="' + package_base_url + '/'+ package_arr[x].post_name + '">' + package_arr[x].post_title + '</a></li>');
//						   break;
//					   }
//				   }
//					 
//			   } 
//			   
//		   }
//		   else{
//			  for(x in package_arr){
//				  if(x < 10){
//					  $('#package_list').append('<li role="presentation"><a role="menuitem" tabindex="-1" href="' + package_base_url + '/'+ package_arr[x].post_name + '">' + package_arr[x].post_title + '</a></li>');
//				  }
//				  else{
//					  break;
//				  }
//			  } 
//		   }
//		   
//	   });
	   

});