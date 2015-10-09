<?php
/**
 * The Template footer for displaying all single posts.
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>

 <?php if(!is_page(16)) { ?> 

	    		</div>
	    		
	    		

	    		
	    	</div>

	    </div>

    </section>
    
 <?php } 
  $modal_type = array("enquiry","booking");
  for ($i=0;$i<count($modal_type);$i++){
?> 	

  <div id="<?php echo $modal_type[$i];?>Modal" class="contactModal modal fade" tabindex="-1" role="dialog" aria-labelledby="<?php echo $modal_type[$i];?>ModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
  	<div class="modal-content">
  	<div class="modal-header">
  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  	<h3 id="<?php echo $modal_type[$i];?>ModalLabel"></h3>
  	      </div>
  	      <form id="<?php echo $modal_type[$i];?>Form" class="form-horizontal" action="" method="post">
  	      <div class="modal-body">
  	          <div class="row">
  	            <div class="col-md-6 modal-right">
  	              <div class="form-group">
  	                <div class="row-fluid">
  	                <div class="col-md-4">
  	                  <label>First Name<abbr class="required" title="required">*</abbr></label>
  	                </div>
  	                <div class="col-md-8">
  	                  <input class="form-control required" name="first-name" placeholder="Your first name" data-msg-required="Please enter your first name" data-rule-minlength="2" type="text">
  	                </div>
  	                </div>
  	            </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Last Name<abbr class="required" title="required">*</abbr></label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control required" name="last-name" placeholder="Your last name" data-msg-required="Please enter your last name"  data-rule-minlength="2" type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>E-Mail<abbr class="required" title="required">*</abbr></label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control email" name="email" placeholder="Email (so that we can contact you)" data-msg-email="email address e.g. (you@gmail.com)" data-msg-required="Please enter your email address" data-rule-email="true" data-rule-required="true" type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Contact me via</label>
  	            </div>
  	            <div class="col-md-8" >
  	              <div class="dropdown" id="contact-method-div">
  					<button class="btn btn-default dropdown-toggle modal-dropdown-btn" type="button" id="contact-method" data-toggle="dropdown" aria-expanded="true">
    					<span class="result">Email</span>
    					<span class="caret"></span>
 					</button>
  					<ul class="dropdown-menu" role="menu" aria-labelledby="contact-method">
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Email</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Phone</a></li>
  					</ul>
				  </div>
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div id="phone-wrap" class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Phone</label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control phone" placeholder="9999-999-999" name="phone" type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div id="time-to-call-wrap" class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Best time to call</label>
  	            </div>
  	            <div class="col-md-8" >
  	              <div class="dropdown" id="time-to-call-div">
  					<button class="btn btn-default dropdown-toggle modal-dropdown-btn" type="button" id="time-to-call" data-toggle="dropdown" aria-expanded="true">
    					<span class="result">Any Time</span>
    					<span class="caret"></span>
 					</button>
  					<ul class="dropdown-menu" role="menu" aria-labelledby="time-to-call">
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Any Time</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Morning</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Afternoon</a></li>
  					</ul>
				  </div>
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Departure Date</label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control" placeholder="yyyy-mm-dd" data-provide="datepicker" name="date-depart" data-date-format="yyyy-mm-dd" data-date-start-date="-1d"  data-date-orientation='bottom' type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	           <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>City Departing from</label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control" placeholder="Where are you" name="city-depart" type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Number of nights</label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control" placeholder="How long do you want to stay" name="number-of-nights" type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Promo code<br>(if applicable)</label>
  	            </div>
  	            <div class="col-md-8">
  	              <input class="form-control" placeholder="Promo code" name="promo-code" type="text">
  	            </div>
  	            </div>
  	          </div>
  	          
  	           
  	              
  	        </div>
  	        <div class="col-md-6">
  	                
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Maximum Spend AUD</label>
  	            </div>
  	            <div class="col-md-8" >
  	              <div class="dropdown" id="spend-div">
  					<button class="btn btn-default dropdown-toggle modal-dropdown-btn" type="button" id="spend" data-toggle="dropdown" aria-expanded="true">
    					<span class="result">$5000 - $10000</span>
    					<span class="caret"></span>
 					</button>
  					<ul class="dropdown-menu" role="menu" aria-labelledby="spend">
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Under $3000</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >$3000 - $5000</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >$5000 - $10000</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >$10000 Above</a></li>
  					</ul>
				  </div>
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-4">
  	              <label>Travel Occasion</label>
  	            </div>
  	            <div class="col-md-8" >
  	              <div class="dropdown" id="travel-occasion-div">
  					<button class="btn btn-default dropdown-toggle modal-dropdown-btn" type="button" id="travel-occasion" data-toggle="dropdown" aria-expanded="true">
    					<span class="result">Romantic Getaway</span>
    					<span class="caret"></span>
 					</button>
  					<ul class="dropdown-menu" role="menu" aria-labelledby="travel-occasion">
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Romantic Getaway</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Honeymoon</a></li>
    					<li role="presentation"><a role="menuitem" tabindex="-1" >Other</a></li>
  					</ul>
				  </div>
  	            </div>
  	            </div>
  	          </div>
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-7">
  	              <label>Flights required? <input type="checkbox" name="flight" value="flightChecked"></label>
  	              
  	            </div>
  	            </div>
  	          </div>
  	              
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-7">
  	            	<label>Message</label>
  	                  
  	                <textarea class="form-control" placeholder="Your message here.." name="message"  rows="6" cols="6"></textarea>
 	              
  	            </div>
  	            </div>
  	                 
  	                   
  	          </div>
  	          
  	          
  	          
  	          <div class="form-group">
  	            <div class="row-fluid">
  	            <div class="col-md-7">
  	            	
  	                  
  	               <div class="g-recaptcha" data-sitekey="6LcPcg4TAAAAACu4VxJMGV3ei2ALZxwwS2Gov5Kv" data-callback='recaptcha_callback'></div>
  	          
  	            </div>
  	            </div>
  	                 
  	                   
  	          </div>
  	
  	          		
  	          <div class="form-group">
  	             <input id="<?php echo $modal_type[$i];?>_package_hidden" type="hidden" name="package" value="General Enquiry">
  	             <input id="contact-method-hidden" type="hidden" name="contact-method" value="Email">
  	             <input id="travel-occasion-hidden" type="hidden" name="travel-occasion" value="Romantic Getaway">
  	             <input id="spend-hidden" type="hidden" name="spend" value="$5000 - $10000">
  	             <input id="time-to-call-hidden" type="hidden" name="time-to-call" value="Any Time">
  	             
  	          </div>
  	          
  	        </div>
  	        </div>     
  	          
  	          
  	          
  	          
  	      </div>
  	      <div class="modal-footer">
  	        <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
  	        <label class="pull-left footer-checkbox"><input type="checkbox" name="newsletter" value="newsletterChecked" checked>Get the latest deals &amp; specials.</label>
  	        <button type="submit" name="submit" value="<?php echo $modal_type[$i];?>" class="btn btn-success">Send It!</button> <p class="help-block pull-left text-danger hide">&nbsp; The form is not valid. </p>
  	        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
  	      </div>
  	      </form>
  	    </div>
  	  </div>
  	</div>
<?php 	
  } 
?>
	
   <div id="call-us-bottom" class="visible-xs container_wapper">
        <div class="container">
            <div class="row-fluid">
               <div>
                	<a class="center-block" href="tel:1300 766 666"> <strong> Call us: 1300 766 666</strong></a>
               </div>
            </div>
        </div>
   </div>

    
   <div id="templatemo_footer" class="container_wapper">
        <div class="container">
            <div class="row">
               <div class="hidden-xs">
                	<p>Copyright © 2015 <strong> <a href="#">Romantic Group™</a></strong> · <strong><a href="<?php echo get_home_url(null,"/terms-and-conditions"); ?>">T&amp;C</a></strong> · <strong><a href="<?php echo get_home_url(null,"/privacy-policy"); ?>">Privacy Policy</a></strong>  · <strong><a href="<?php echo get_home_url(null,"/special-conditions"); ?>">Special Conditions</a></strong></p>
            
                </div>
                <div class="visible-xs col-xs-12">
                    <div>
                    	<p>Copyright © 2015 <strong> <a href="#">Romantic Group™</a></strong> · <strong><a href="<?php echo get_home_url(null,"/terms-and-conditions"); ?>">T&amp;C</a></strong>  </p> 
                    </div>
                    <div class="clearfix">
                    	<p> <strong><a href="<?php echo get_home_url(null,"/privacy-policy"); ?>">Privacy Policy</a></strong>  · <strong><a href="<?php echo get_home_url(null,"/special-conditions"); ?>">Special Conditions</a></strong></p>                  	
                    </div>
                </div>
            </div>
        </div>
   </div>
   
   
      
   <script type="text/javascript">
                (function () {
                    var head = document.getElementsByTagName("head").item(0);
                    var script = document.createElement("script");
                    var src = (document.location.protocol == 'https:' ? 'https://www.formilla.com/scripts/feedback.js' : 'http://www.formilla.com/scripts/feedback.js');
                    script.setAttribute("type", "text/javascript"); script.setAttribute("src", src); script.setAttribute("async", true);
                    var complete = false;

                    script.onload = script.onreadystatechange = function () {
                        if (!complete && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
                            complete = true;
                            Formilla.guid = '0327e879-5d41-45cd-a7dd-cc53ec06ff0a';
                            Formilla.loadFormillaChatButton();
                        }
                    };

                    head.appendChild(script);

                })();
   </script>
   
   <script type="text/javascript">
                jQuery(document).ready(function($){

                  $('.date-input-top').val("");
              	  
              	  $(".enquiry_anchor").click(function(){
              		  $("#enquiryModal").css("z-index","1042");
              		  $("#bookingModal").css("z-index","1041");
              	  });

              	  $(".booking_anchor").click(function(){
              		  $("#enquiryModal").css("z-index","1041");
              		  $("#bookingModal").css("z-index","1042");
              	  });

              	  $("#enquiryForm").validate(); 
              	  $("#bookingForm").validate();

              	  $('#enquiryModal').on('show.bs.modal', function (e) {
              		 // package_quote = $(e.relatedTarget).data("package_quote");
              		  <?php if(empty($package_information['package_name'])){?>
              			  $("#enquiryModalLabel").text("General Enquiry");
              			  $("#enquiry_package_hidden").val("General Enquiry");
              		  <?php }else{?>
              		  
              			  $("#enquiryModalLabel").text("Enquiry - <?php echo $package_information['package_name']." / ".$package_information['package_pricing'];?>");
              			  $("#enquiry_package_hidden").val("<?php echo $package_information['package_name']." / ".$package_information['package_pricing'];?>");
              		  <?php }?>
              	  });

              	  $('#bookingModal').on('show.bs.modal', function (e) {
              		  package_quote = $(e.relatedTarget).data("package_quote");
              		  <?php if(empty($package_information['package_name'])){?>
              			  $("#bookingModalLabel").text("General Booking");
              			  $("#booking_package_hidden").val("General Booking");
              		  <?php }else{?>
              			  $("#bookingModalLabel").text("Booking - <?php echo $package_information['package_name']." / ".$package_information['package_pricing'];?>");
              			  $("#booking_package_hidden").val("<?php echo $package_information['package_name']." / ".$package_information['package_pricing'];?>");
              		  <?php }?>
              	  });

                });
   </script>

   
   <?php wp_footer(); ?>
   </body>
</html>