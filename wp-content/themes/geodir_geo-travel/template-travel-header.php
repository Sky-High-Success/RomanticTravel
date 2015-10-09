<?php
/**
 * The Template Header for displaying all single posts.
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>



<?php


    wp_enqueue_style( 'plan-style', get_stylesheet_directory_uri() . '/plan.css', array(), '1' );

	//Template
	wp_enqueue_style( 'bootstrap3-style', get_stylesheet_directory_uri() . '/css/bootstrap3.min.css', array(), '1.0.0' );
	
	wp_enqueue_style( 'select-style', get_stylesheet_directory_uri() . '/css/bootstrap-select.min.css', array(bootstrap3-style), '1.0.0' );
	
	//Template
	wp_enqueue_style( 'travel-style', get_stylesheet_directory_uri() . '/css/travel.css', array(), '1.0.0' );
	
	wp_enqueue_style( 'update-travel-style', get_stylesheet_directory_uri() . '/update-travel.css', array(), '1.0.0' );
	
	wp_enqueue_style( 'datepicker-style', get_stylesheet_directory_uri() . '/css/datepicker3.css', array(), '1.0.0' );
	
	wp_enqueue_style( 'fontawesome-style', get_stylesheet_directory_uri() . '/css/font-awesome.min.css', array(), '1.0.0' );
	
	
	//Template
	wp_enqueue_style( 'templatemo-style', get_stylesheet_directory_uri() . '/css/templatemo_style.css', array('bootstrap3-style'), '1.0.0' );

	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'bootstrap3-js', get_stylesheet_directory_uri() . '/js/bootstrap3.min.js', array( 'jquery' ), '2014-07-18', true );
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'select-js', get_stylesheet_directory_uri() . '/js/bootstrap-select.min.js', array( 'bootstrap3-js' ), '2014-07-18', true );
	
	
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'easing', get_stylesheet_directory_uri() . '/js/jquery.easing.1.3.js', array( 'jquery' ), '2014-07-18', true );
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'mobile-customized', get_stylesheet_directory_uri() . '/js/jquery.mobile.customized.min.js', array( 'jquery','easing' ), '2014-07-18', true );
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'unslider', get_stylesheet_directory_uri() . '/js/unslider.min.js', array( 'jquery' ), '2014-07-18', true );
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'singlePageNav', get_stylesheet_directory_uri() . '/js/jquery.singlePageNav.min.js', array( 'jquery' ), '2014-07-18', true );
	
	wp_enqueue_script( 'datepicker-script', get_stylesheet_directory_uri() . '/js/bootstrap-datepicker.js', array( 'jquery', 'bootstrap3-js' ), '2014-07-18', true );
	
	
	wp_enqueue_script( 'moment', get_stylesheet_directory_uri() . '/js/moment.min.js', array(), '2014-07-18', true );
	
	
	// Loads JavaScript file with functionality specific to classiads.
	wp_enqueue_script( 'templatemo_scrip', get_stylesheet_directory_uri() . '/js/templatemo_script.js', array( 'jquery', 'bootstrap3-js','mobile-customized'), '2014-07-18', true );
	
	// load local script
	wp_register_script( 'custom_script', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery'), '2014-07-18', true );
	
	wp_localize_script('custom_script', 'package_base_url', get_home_url( null, get_post_type_object( 'travel_package' )->rewrite['slug'] ));
	
	if(is_page_template("template-search.php")){
	
		wp_localize_script('custom_script', 'search_date', esc_attr($_GET['date']));
		wp_localize_script('custom_script', 'search_des', esc_attr($_GET['des']));
		wp_localize_script('custom_script', 'search_des_text', esc_attr($_GET['des-text']));
	
	}
	
	//wp_localize_script('custom_script', 'package_arr', get_all_packages_meta());
	wp_enqueue_script( 'custom_script' );
	
	
?> 

<?php

if(isset($_POST['submit']) && isset($_POST['package']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$is_honeymoon = false;
	$is_flight = false;
	$is_newsletter = false;
	$is_honeymoon_human = "no";
	$is_flight_human = "no";
	$is_newsletter_human = "no";

	$post_honeymoon = esc_attr(strip_tags($_POST['honeymoon']));
	$post_flight = esc_attr(strip_tags($_POST['flight']));
	$post_date_depart = esc_attr(strip_tags($_POST['date-depart']));
	
	$post_contact_method = esc_attr(strip_tags($_POST['contact-method']));
	$post_number_of_nights = esc_attr(strip_tags($_POST['number-of-nights']));
	$post_spend= esc_attr(strip_tags($_POST['spend']));
	$post_time_to_call = esc_attr(strip_tags($_POST['time-to-call']));
	$post_travel_occasion = esc_attr(strip_tags($_POST['travel-occasion']));
	
	

	$post_newsletter = esc_attr(strip_tags($_POST['newsletter']));

	if(!empty($post_honeymoon)) {
		$is_honeymoon = true;
		$is_honeymoon_human = "yes";
	}

	if(!empty($post_flight)) {
		$is_flight = true;
		$is_flight_human = "yes";
	}

	if(!empty($post_newsletter)) {
		$is_newsletter = true;
		$is_newsletter_human = "yes";
	}

	if(!empty($post_date_depart)) {
		$departure_date = date('Y-m-d', strtotime(esc_attr(strip_tags($_POST['date-depart']))));
	}

	$current_date = date('Y-m-d', current_time( 'timestamp'));

	$enquiry_category = esc_attr(strip_tags($_POST['submit']));

	$enquiry_information = array(
			'first_name' => esc_attr(strip_tags($_POST['first-name'])),
			'last_name' => esc_attr(strip_tags($_POST['last-name'])),
			'city_depart' => esc_attr(strip_tags($_POST['city-depart'])),
			'departure_date' => $departure_date,
			'email'    => esc_attr(strip_tags($_POST['email'])),
			'phone' => esc_attr(strip_tags($_POST['phone'])),
			'package' => esc_attr(strip_tags($_POST['package'])),
			'promo_code' => esc_attr(strip_tags($_POST['promo-code'])),
			'honeymoon' => $is_honeymoon,
			'flight' => $is_flight,
			'message' => esc_attr(strip_tags($_POST['message'])),
			'category' => $enquiry_category,
			'enquiry_date' => $current_date,
			'contact_method' => $post_contact_method,
			'number_of_nights' => $post_number_of_nights,
			'spend' => $post_spend,
			'time_to_call' => $post_time_to_call,
			'travel_occasion' => $post_travel_occasion,
				
	);


	// insert listing meta for each listing post in separate table
	$wpdb->insert ( $wpdb->prefix . 'travel_enquiry_booking', array (
			'First Name' => $enquiry_information["first_name"],
			'Last Name' => $enquiry_information["last_name"],
			'City Depart' => $enquiry_information["city_depart"],
			'Departure Date' => $enquiry_information["departure_date"],
			'Email' => $enquiry_information["email"],
			//'Region' => $obj->region,
			'Phone' => $enquiry_information["phone"],
			'Package' => $enquiry_information["package"],
			'Promo Code' => $enquiry_information["promo_code"],
			'Honeymoon' => $enquiry_information["honeymoon"],
			'Flight' => $enquiry_information["flight"],
			'Message' => $enquiry_information["message"],
			'category' => $enquiry_information["category"],
			'Enquiry Date' => $enquiry_information["enquiry_date"],
	)
	);

	$multiple_to_recipients = array(
			'enquiries@honeymoon.com.au',
			'deals@romantictravel.com.au',
			
	);

	$content_here = <<<DOC
  First Name: {$enquiry_information["first_name"]}
  Last Name: {$enquiry_information["last_name"]}
  City Depart: {$enquiry_information["city_depart"]}
  Departure Date: {$enquiry_information["departure_date"]}
  Email: {$enquiry_information["email"]}
  Phone: {$enquiry_information["phone"]}
  Contact me via: {$enquiry_information["contact_method"]}
  Best time to call: {$enquiry_information["time_to_call"]}
  Package: {$enquiry_information["package"]}
  Promo Code: {$enquiry_information["promo_code"]}
  Number of nights stay: {$enquiry_information["number_of_nights"]}
  Maximum Spend AUD $: {$enquiry_information["spend"]}
  Travel Occasion: {$enquiry_information["travel_occasion"]} 
  Flights required: $is_flight_human
  Deals subscription checked: $is_newsletter_human
  Message:
-------------------------

{$enquiry_information["message"]}

-------------------------
  Category: {$enquiry_information["category"]}
DOC;

	$mail_subject = ucwords($enquiry_category)." - Package: ".$enquiry_information["package"]." - Date: ".$current_date;

			wp_mail($multiple_to_recipients, $mail_subject, $content_here);

			$contact_message = $enquiry_category;


			// Mailchimp Add Subscriber part

			if($is_newsletter){

			include_once 'Drewm/MailChimp.php';

			$MailChimp = new \Drewm\MailChimp('b54b29c0661fc003f612c8a1526ad5b5-us10');
			$result = $MailChimp->call('lists/subscribe', array(
					'id'                => '0d54271254',
					'email'             => array('email'=> $enquiry_information["email"] ),
					'merge_vars'        => array('FNAME'=>$enquiry_information["first_name"], 'LNAME'=>$enquiry_information["last_name"],'CITYDEPART'=>$enquiry_information["city_depart"]),
					'double_optin'      => false,
					'update_existing'   => true,
					'replace_interests' => false,
					'send_welcome'      => false,
			));
			}


			//wp_redirect( home_url() ); exit;

}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Romantic Travel</title>
    <meta name="description" content="We want your honeymoon, anniversary or romantic holiday to be remembered and talked about for a long time, so let us share in your joy and you won’t be disappointed. " />
    <meta name="author" content="romantic group">
    <meta name="contact" content="harrietsmith@harrietsmith.us" />
    <meta name="copyright" content="Copyright (c)2014 Romantic Group. All Rights Reserved." />
    <meta name="keywords" content="romantic, travel, honeymoon, love, package, cheap, deal" />
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri();?>/images/favicon.ico" type="image/x-icon" />
	<link rel='stylesheet' id='geo-travel-theme-css'  href='http://romantictravel.com.au/wp-content/themes/geodir_geo-travel/style.css' type='text/css' media='all' />
    <?php wp_head(); ?>
    
    <script src='https://www.google.com/recaptcha/api.js' async></script>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<style type="text/css">
		.site-header .rightheader {
		  float: right;
		  padding: 10px;
		  text-align: right;
		  width: 800px;
		}
		h1.site-title {
  margin: 0;
}
.site-header .site-title > a {
  background: rgba(0, 0, 0, 0) url("images/logo.png") no-repeat scroll left center;
  float: left;
  min-height: 80px;
  width: 100%;
}
#text-3 a {
  font-family: Lato,sans-serif;
  font-size: 18px;
  font-weight: 300!important;
}
#text-3 li > b {
  font-family: Lato,sans-serif;
  font-size: 18px;
  color: #333;
  font-weight: 700;
}
ul#menu-menu-1 { margin:0 !important; }
ul#menu-menu-1 a {
  font-size: 16px;
  font-family: Lato,sans-serif;font-weight:300!important; padding: 16px 24px!important;
}
ul#menu-menu-1 a:hover { text-decoration:none; }
.newAddtionalButtons {
  float: left;
  width: 80%;
}
.newAddtionalButtons > a.CustmHedaerBtns {
  background: #d4131b none repeat scroll 0 0;
  color: #fff;
  display: inline-block;
  float: none;
  font-size: 16px;
  font-weight: 300;
  padding: 15px 28px;font-family: Lato,sans-serif;
}
.newAddtionalButtons > a.CustmHedaerBtns:first-child {
  margin-right: 10px;
}
.newAddtionalButtons > a.CustmHedaerBtns:hover { text-decoration:none; }

</style>
	
</head>
  <body class="travel-style <?php if(is_page(16)) { ?> CruiseBlogTemplate full-width-content " <?php } ?> >
  

  
	<header itemtype="http://schema.org/WPHeader" itemscope="itemscope" role="banner" class="site-header">
		<div class="wrap" style="margin:0 auto;">
			<div class="title-area">
				<?php $description = get_bloginfo( 'description', 'display' ); ?>
				<h1 itemprop="headline" class="site-title"><a href="<?php echo home_url('/'); ?>"  style="display: block; text-indent: -9999px;"><?php  bloginfo( 'name' );  ?></a></h1>
				<!--<h2 itemprop="description" class="site-description"><?php echo $description; ?></h2>-->
			</div> 
			
			<div class="rightheader">
				<?php if(!is_page(16)) { ?> 
				<div class="newAddtionalButtons">
					<!--<a href="javascript:;" class="CustmHedaerBtns">Get a Quote</a>-->
 <div class="dropdown hidden-sm hidden-xs">
    <button class="btn plantrip CustmHedaerBtns dropdown-toggle" type="button" data-toggle="dropdown">Plan Your Trip
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="http://worldtravelgroup.reslogic.com/?pl=21&screen=scnWizardSteps">Holiday</a></li>
      <li><a href="http://worldtravelgroup.reslogic.com/?pl=56&screen=scnWizardSteps">Honeymoon</a></li>
    </ul>
  </div>
					<a href="#" class="hidden-xs enquiry_anchor CustmHedaerBtns" data-toggle="modal" data-target="#enquiryModal" data-package_quote="general">Online Enquiry</a>
					<a href="<?php echo get_home_url(null,"package-enquiry"); ?>" class="visible-xs-inline-block enquiry_anchor CustmHedaerBtns" data-package_quote="general">Online Enquiry</a>
				</div>
				
			<?php } ?>
				<div class="EnquiryWidgetText">
					<?php dynamic_sidebar('Header Right'); ?>
				</div>
			</div>
		</div>
	</header>
	<nav class="nav-primary" itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" role="navigation">
		<div class="wrap" style="margin: 0 auto !important;">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'menu genesis-nav-menu menu-primary responsive-menu') ); ?>
		</div>
	</nav>
	
   <div class="container">
   <?php

    if(!empty($contact_message)){
    	if($contact_message == "enquiry"){
	        echo do_shortcode('[notification_box]Thank you, your enquiry has been received, our consultant will be in touch shortly to answser your questions.[/notification_box]');
    	}
    	else if($contact_message == "booking"){
    		echo do_shortcode('[notification_box]Thank you, your booking request has been received, you will be contacted by our booking agent shortly to confirm your booking.[/notification_box]');
    	}
    	else{
    		echo do_shortcode('[notification_box]Your contact form submission is forged, Please submit a legal form.[/notification_box]');
    		 
    	}
    }
   ?>
   </div>
   <?php if(!is_page(16)) { ?> 
   <section class="ads-main-page">

    	<div class="container">

	    	<div class="full" style="padding: 0 0;">

				<div class="ad-detail-content">

   <?php } ?>	    			