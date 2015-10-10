<?php
/**
 * Template name: Package Enquiry Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */

?>

<?php include_once "template-travel-header.php";?>

<?php while ( have_posts() ) : the_post();
		
		global $post;
		$page_name = $post->post_name;
		$form_type = "";
		
		switch ($page_name){
			case "package-enquiry":
				$form_type = "enquiry"; 
				break;
			case "package-booking":
				$form_type = "booking";
				break;
		}
		
		if(isset($_GET['package_name']) && isset($_GET['package_pricing'])){
			global $package_information;
			
			$package_information = array(
					'package_name' => esc_attr(strip_tags($_GET['package_name'])),
					'package_pricing' => esc_attr(strip_tags($_GET['package_pricing'])),			
			);
			
		}else{
			
			global $package_information;
				
			$package_information = array();
			
		}
?>

<form id="contactForm" class="form-horizontal" action="" method="post">
  	<h1 class="contact-heading center-block"><?php echo ucwords("$form_type form");?></h1>
  	
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
  	            <div class="col-md-8">
  	            	
  	                  
  	               <div class="g-recaptcha" data-sitekey="6LcPcg4TAAAAACu4VxJMGV3ei2ALZxwwS2Gov5Kv" data-callback='recaptcha_callback'></div>
  	          
  	            </div>
  	            </div>
  	                 
  	                   
  	          </div>

  	          
  	          <div class="form-group">
  	          	<div class="row-fluid">
  	            <div class="col-md-7">
  	              <label class="pull-left footer-checkbox"><input type="checkbox" name="newsletter" value="newsletterChecked" checked>Get the latest deals &amp; specials.</label>
  	              
  	            </div>
  	            </div>
  	          		
  	          </div>
  	          
  	          <div class="form-group">
  	             <input id="package_hidden" type="hidden" name="package" value="General Enquiry">
  	             <input id="contact-method-hidden" type="hidden" name="contact-method" value="Email">
  	             <input id="travel-occasion-hidden" type="hidden" name="travel-occasion" value="Romantic Getaway">
  	             <input id="spend-hidden" type="hidden" name="spend" value="$5000 - $10000">
  	             <input id="time-to-call-hidden" type="hidden" name="time-to-call" value="Any Time">
         
  	             <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
  	          </div>
  	          
  	          <div class="form-group">
  	          	<div class="row-fluid">
  	            <div class="col-md-8">            	
  	        	 	<button type="submit" name="submit" value="<?php echo $form_type;?>" class="mobile-enquiry-button btn btn-success">Send It!</button> <p class="help-block pull-left text-danger hide">&nbsp; The form is not valid. </p>
  	        
  	            </div>
  	            </div>
  	          	
  	          </div>
  	          
  	       </div>
  	   	 
  	 </div>        
 	        
</form>

<?php endwhile; ?>

<script type="text/javascript">
	  jQuery(document).ready(function($){
		  <?php if(!empty($package_information['package_name'])){?>
		  	$("#package_hidden").val("<?php echo $package_information['package_name']." / ".$package_information['package_pricing'];?>");
		  <?php } ?>
		  
		  var contact_validator = $("#contactForm").validate(); 

	      $("#contactForm button[type=submit]").on("click", function(event){

	       		contact_validator.form();

	       		if (grecaptcha.getResponse() == "") {
	  			    var errors;
	  			    /* Build up errors object, name of input and error message: */
	  			    errors = { "g-recaptcha-response" : "You have to go through CAPTCHA check" };
	  			    /* Show errors on the form */
	  			    contact_validator.showErrors(errors);
	  			    event.preventDefault();            
	  			}
	           	 
	      });
		  
	  });
</script>


<?php include_once "template-travel-footer.php";?>
