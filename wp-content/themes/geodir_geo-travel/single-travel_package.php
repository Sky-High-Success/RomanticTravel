<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>

<?php include_once "template-travel-header.php";?>

<?php while ( have_posts() ) : the_post(); 
      global $post;
      global $package_information;
          
      $page_name = $post->post_name;
      
      $package_information = array(
      		'package_name' => $post->post_title ,
      		'package_pricing' => get_post_meta($post->ID, 'package_pricing', true),
      		'package_excerpt' => get_post_meta($post->ID, 'package_excerpt', true),
      		'package_discount' => get_post_meta($post->ID, 'package_discount', true),
      		'package_cover_photo_url' =>  get_post_meta($post->ID, 'package_cover_photo_url', true),
      		'package_layer_slider_id' => get_post_meta($post->ID, 'package_layer_slider_id', true),
      		'package_detail_include' =>  get_post_meta($post->ID, 'package_detail_include', true),
      		'package_detail_validity' =>  get_post_meta($post->ID, 'package_detail_validity', true),     		
      		
      );
      
      $obj_arr = get_the_terms( $post->ID, "package_taxonomy");
      
      if(count($obj_arr)>0){
      	
      	foreach ($obj_arr as $obj ) {
      		$package_information['package_taxonomy_url'] = get_term_link($obj);
      		break;
      	}
          	
      }else{
      	$package_information['package_taxonomy_url'] = "#";
      }
      
   
      
?>

<?php
echo do_shortcode('[layerslider id="'.$package_information['package_layer_slider_id'].'"]');
?>   	


<h2><?php echo $package_information['package_name'];?> - <?php echo $package_information['package_pricing']; ?></h2>
<div class="row">
  <div class="visible-lg visible-md col-lg-4 col-md-5">
    <a class="enquiry_anchor" href="#" data-toggle="modal" data-package_quote="specific" data-target="#enquiryModal"><img src="<?php echo get_stylesheet_directory_uri()."/images/enquire-now-300x75.png"; ?>" alt="enquire now" /></a>
  </div>

  <div class="visible-xs visible-sm col-sm-6 col-xs-8">
    <a class="enquiry_anchor" id="enquiry-button-2" data-package_quote="specific" href="<?php echo get_home_url(null,"package-enquiry"); ?>" ><img src="<?php echo get_stylesheet_directory_uri()."/images/enquire-now-300x75.png"; ?>" alt="enquire now" /></a>
  </div>
  
  <div class="visible-lg visible-md col-lg-2 col-md-2 col-lg-offset-4 col-md-offset-4">
    <div class="individual-discount">
      <span class="individual-discount-text"><?php echo empty($package_information['package_discount'])?"5%":$package_information['package_discount'];?></span>
      <span class="individual-discount-text-off">OFF</span>
    </div>
  </div>
  
  <div class="visible-xs visible-sm col-sm-2 col-xs-3 col-sm-offset-3 col-xs-offset-1">
    <div class="individual-discount">
      <span class="individual-discount-text"><?php echo empty($package_information['package_discount'])?"5%":$package_information['package_discount'];?></span>
      <span class="individual-discount-text-off">OFF</span>
    </div>
  </div>


</div>


<div class="row">
<div class="in-sec col-xs-12 col-lg-10 col-md-12" >
  <h3>INCLUDES:</h3>
  <p>
  <?php echo $package_information['package_detail_include'];?>
  </p>
  <h3>Valid for stays:</h3>
  <p>
  <?php echo $package_information['package_detail_validity'];?>
  </p>
  <h3>*Conditions apply.</h3>
    <p>Prices are per person, based on twin share and in AU Dollars. See terms and conditions below.</p>

    <?php
      if($page_name == "hot-deal-pimalai-resort-and-spa"){
      	$expiry_date = date('jS M Y', mktime(0, 0, 0, 2 , 28, 2015));
      }else{
      	//$expiry_date = date('jS M Y', mktime(0, 0, 0, date("m")  , date("d")+30, date("Y")));
      	$expiry_date = "31 OCT 2015";
      }
    
    ?>
    
    <p>Travel restrictions and conditions may apply, please ask us for further details. Prices are correct as at 01 FEB 2015 and are subject to change without notice. Package is on sale until <?php echo $expiry_date; ?> unless otherwise stated or sold out prior. Prices are subject to availability. Seasonal surcharges and blackout dates may apply depending on date of travel. Advertised price includes any bonus nights. Minimum/maximum stay restrictions may apply. Images may not be representative of this offer.
    </p>
</div>
</div>
<div class="bottom-button-style row">
  <div id="bottom_enquiry_button" class="visible-lg visible-md col-md-3 col-md-offset-2">
    <a class="fa-envelope enquiry_anchor" href="#" data-toggle="modal" data-package_quote="specific" data-target="#enquiryModal" > Enquiry</a>
  </div>
  
  <div id="bottom_back_button" class="visible-lg visible-md col-md-3 col-md-offset-2">
    <a class="fa-arrow-left" href="<?php echo $package_information['package_taxonomy_url'];?>"> Back</a>
  </div>
  
  <div id="bottom_enquiry_button" class="visible-xs visible-sm center-block btn-group btn-group-block" role="group" aria-label="Button Group">
    <button class="fa-envelope enquiry_anchor btn btn-warning" data-package_quote="specific" onclick="location.href ='<?php echo get_home_url(null,"package-enquiry"); ?>'"> Enquiry</button>
    <button class="fa-arrow-left btn btn-warning" onclick="location.href ='<?php echo $package_information['package_taxonomy_url'];?>'"> Back</button>
  </div>
  
  
  
  
   
  
</div>

<?php endwhile; ?>

<script type="text/javascript">
    jQuery(document).ready(function($){

    	$("#enquiry-button-1").attr("href", $("#enquiry-button-1").attr("href") + "?package_name=<?php echo urlencode($package_information['package_name']);?>&package_pricing=<?php echo urlencode($package_information['package_pricing']); ?>");
        
        $("#enquiry-button-2").attr("href", $("#enquiry-button-2").attr("href") + "?package_name=<?php echo urlencode($package_information['package_name']);?>&package_pricing=<?php echo urlencode($package_information['package_pricing']); ?>");
        
		
    });
        
</script>

<?php include_once "template-travel-footer.php";?>