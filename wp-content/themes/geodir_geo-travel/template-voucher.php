<?php
/**
 * Template name: Voucher Main Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */

?>


<?php

$is_valid = True;

if(isset($_POST['email']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	
	// get email address from post data
	$email = esc_attr(strip_tags($_POST['email']));
	
	$first_name = esc_attr(strip_tags($_POST['first_name']));
	// VALIDATION
	$is_valid = FALSE;  // Initialise is_valid as false
	
	// VALID EMAIL TEST
	if(filter_var($email, FILTER_VALIDATE_EMAIL)){
		// Does domain name actually exist?
		if(checkdnsrr(array_pop(explode("@",$email)),"MX")){
			$is_valid = TRUE; // email is valid
			// could go even further and lookup ip address in spam lists, and do disposable email address checks here
		}
	}
	

	// PROCESS
	// test if form is valid.
	if ($is_valid == TRUE) {
		// Mailchimp Add Subscriber part
		include_once 'Drewm/MailChimp.php';
		
		$MailChimp = new \Drewm\MailChimp('b54b29c0661fc003f612c8a1526ad5b5-us10');
		$result = $MailChimp->call('lists/subscribe', array(
				'id'                => '0d54271254',
				'email'             => array('email'=> $email ),
				'merge_vars'        => array('FNAME'=> $first_name),
				'double_optin'      => false,
				'update_existing'   => true,
				'replace_interests' => false,
				'send_welcome'      => false,
		));
		
		wp_redirect( "../voucher-thanks" );
		exit;
		
	}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ROMANTIC TRAVEL Vouchers</title>
<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link href="http://cdn.en.gd/1.0/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo get_stylesheet_directory_uri();?>/css/font-awesome.min.css" media="screen, projection" rel="stylesheet" type="text/css"/>
<link href="<?php echo get_stylesheet_directory_uri();?>/css/voucher_custom.css" media="screen, projection" rel="stylesheet" type="text/css"/>
<!--[if lt IE 9]>
     <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
     <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->


<!-- Facebook Conversion Code for Romantic Travel -->
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6019261201008', {'value':'0.00','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6019261201008&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" /></noscript>




</head>
<body>

<!--container-->
<div class="container main">
  
  <div class="row">
    <div class="col-lg-7 col-md-7">
       <div class="left-col">
       <a href="" class="logo"><img src="<?php echo get_stylesheet_directory_uri();?>/images/logo.png" width="191" height="80" alt=""></a>
       <p class="logo-title">Travel Deals You’ll Fall in Love With!</p>
       <h1>Are you looking for the <span>ultimate romantic getaway</span> for your special someone?</h1>
       <p>Want to save up to 75% on your next romantic holiday?</p>
       <p>Welcome to <a href=""><strong>RomanticTravel.com.au</strong></a>  where every destination is a romantic getaway for two. </p>
       <p>Our extensive range of travel packages for two won't break the bank they'll make memories. </p>
       <p>To celebrate our launch and Valentines day, we are offering new members a $50 voucher to spend on any of our already discounted packages.</p>
       <p class="sign-up"><strong>Signup to the right to Claim Your Voucher Today!</strong></p>
      </div>  
    </div>
    <div class="col-lg-5 col-md-5">
      <div class="form-wrapper">
       <div class="visible-lg visible-md">
       <span class="bubble bubble1">REGISTER HERE</span> 
       <span class="bubble bubble2">TO CLAIM YOUR</span>
       <span class="bubble bubble3">$50 DISCOUNT<br> <strong>VOUCHER!</strong></span>
       </div>
       
       <div class="visible-sm">
       <span class="bubble bubble1">REGISTER HERE</span> 
       <span class="bubble bubble2">TO CLAIM YOUR</span>
       <span class="bubble bubble3">$50 DISCOUNT<br> <strong>VOUCHER!</strong></span>
       </div>
       
       <div class="visible-xs">
       <span class="bubble bubble1">REGISTER HERE</span> 
       <span class="bubble bubble2">TO CLAIM YOUR</span>
       <span class="bubble bubble3">$50 DISCOUNT<br> <strong>VOUCHER!</strong></span>
       </div>
       
       <div class="clearfix">
        <?php

   
    	if(!$is_valid){
	        echo do_shortcode('[notification_box]You must use a real email address. Fake or disposable email addresses are not accepted. Please try again.[/notification_box]');
    	}

  
        ?>
       </div>
       <div class="form-container">
        <form id="form" role="form" action="" method="post"  autocomplete="on"  data-parsley-validate>
              <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
          
              <div class="form-group">
               <input type="text" class="form-control" id="first_name" name="first_name" placeholder="enter your ﬁrstname here..." autocomplete="family-name" required data-parsley-trigger="blur">
              </div>
              
              <div class="form-group">
                  <input type="email" class="form-control" id="email" name="email" placeholder="enter your email address here..."  autocomplete="email" data-parsley-trigger="change" data-parsley-error-message="You must use a valid email address.">
              </div>
             
              <div class="form-group">
                <input type="submit" class="btn-cta" value="GET YOUR $50 VOUCHER" />
              </div>
         </form> 
       </div>
      </div>  
    </div>
  </div>
</div>
<!--/container--> 

<script src="http://code.jquery.com/jquery-latest.min.js"></script> 
<script src="htp://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> 
<script src="http://cdn.en.gd/1.0/bootstrap/js/bootstrap.min.js"></script> 
<script src="http://cdn.en.gd/1.0/js/placeholder/jquery.placeholder.js"></script> 
<script type="text/javascript">

 $(function() {
	 // supports placeholders in earlier browsers
    $('input, textarea').placeholder();
 });

// remote  validation
window.ParsleyExtend = {
  asyncValidators: {
    mycustom: {
      fn: function (xhr) {
        return 200 === xhr.status;
      },
      url: 'http://ws.en.gd/email/validate.php'
    },
    postcode_validator: {
      fn: function (xhr) {
        return 200 === xhr.status;
      },
      url: 'http://ws.en.gd/auspost/validate.php'
    }   
  }
};
</script> 
<script src="http://aws-cdn.en.gd/1.0/js/parsley/parsley.remote.min.js"></script> 
<script src="http://aws-cdn.en.gd/1.0/js/parsley/parsley.min.js"></script> 
<script>
// format errors using bootstrap css
  $("#form, #lowerform").parsley({
      successClass: "has-success",
      errorClass: "has-error",
      classHandler: function(el) {
          return el.$element.closest(".form-group");
      },
      errorsWrapper: "<span class='help-block'></span>",
      errorTemplate: "<span></span>"
  });  
</script>
</body>
</html>
