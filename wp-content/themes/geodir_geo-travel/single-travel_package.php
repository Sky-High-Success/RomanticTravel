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

<?php 
	
	$list_package_notes = get_post_meta($post->ID, 'list_package_notes', true);
	$list_package_notes_exclusive_sale = get_post_meta($post->ID, 'list_package_notes_exclusive_sale', true);
	$list_package_notes_added_value = get_post_meta($post->ID, 'list_package_notes_added_value', true);
	$list_package_notes_value_inclusion_1 = trim(get_post_meta($post->ID, 'list_package_notes_value_inclusion_1', true));
	$list_package_notes_value_inclusion_2 = trim(get_post_meta($post->ID, 'list_package_notes_value_inclusion_2', true));
	
	$list_package_other_travel_dates_honeymoon = trim(get_post_meta($post->ID, 'list_package_other_travel_dates_romantic_only_honeymoon', true));
	$list_package_other_travel_dates_holiday = trim(get_post_meta($post->ID, 'list_package_other_travel_dates_romantic_only_holiday', true));

?>


<h2><?php echo $package_information['package_name'];?> - <?php echo $package_information['package_pricing']; ?></h2>

<div class="row">
<div class="in-sec col-xs-12 col-lg-10 col-md-12" >
  <div class="row">
       <?php if($list_package_notes == "exclusive_sale" || $list_package_notes == "added_value" ){
	  ?>
	  <div class="col-lg-8">
	  <?php } else{ ?>
	  <div class="col-lg-12">
	  <?php }?>
    	  
<!-- 		  <div class="visible-lg visible-md col-lg-4 col-md-5 no-left-padding">
		    <a class="enquiry_anchor" href="#" data-toggle="modal" data-package_quote="specific" data-target="#enquiryModal"><img src="<?php echo get_stylesheet_directory_uri()."/images/enquire-now-300x75.png"; ?>" alt="enquire now" /></a>
			  </div>
		
			  <div class="visible-xs visible-sm col-sm-6 col-xs-8">
		    <a class="enquiry_anchor" id="enquiry-button-2" data-package_quote="specific" href="<?php echo get_home_url(null,"package-enquiry"); ?>" ><img src="<?php echo get_stylesheet_directory_uri()."/images/enquire-now-300x75.png"; ?>" alt="enquire now" /></a>
			  </div> -->
		  <div class="clearfix"></div>
		  <h3>INCLUDES:</h3>
		  <p>
		  <?php echo $package_information['package_detail_include'];?>
		  </p>
		  <h3>Valid for stays:</h3>
		  <p>
		  <?php echo $package_information['package_detail_validity'];?>
		  </p>
		  
		  <h3 class="other_dates_text">For Other Travel Dates click below for either a Honeymoon or Holiday:</h3>
		  
		  <div class="row other_dates_div">
		  		<div id="other_dates_honeymoon" class="visible-lg visible-md col-md-3 col-md-offset-2">
    				<a class="buton_custom" href="<?php echo $list_package_other_travel_dates_honeymoon;?>" > Honeymoons</a>
  		  		</div>
  
  		  		<div id="other_dates_holiday" class="visible-lg visible-md col-md-3 col-md-offset-2">
    				<a class="buton_custom" href="<?php echo $list_package_other_travel_dates_holiday;?>" > Holidays</a>
  		 		</div>
  		 		
  		 		<div id="other_dates_btn_group" class="visible-xs visible-sm center-block btn-group" role="group" aria-label="Button Group">
    				<button class="btn buton_custom" onclick="location.href ='<?php echo $list_package_other_travel_dates_honeymoon;?>'"> Honeymoons</button>
    				<button class="btn buton_custom" onclick="location.href ='<?php echo $list_package_other_travel_dates_holiday;?>'"> Holidays</button>
  				</div>
  		 		
		  </div>
		  
		 
		  
	  </div>
	  <?php if($list_package_notes == "exclusive_sale" || $list_package_notes == "added_value" ){
	  ?>
	  
	  <div class="col-lg-4 col-lg-offset-0 col-md-offset-4 col-md-4 col-sm-offset-4 col-sm-5 col-xs-offset-1 col-xs-10">
	  		<div class="promotion-note">
	  			<div class="">
	  				<img class="pin-image center-block" src="<?php echo get_stylesheet_directory_uri();?>/images/note-pin-1.png">
	  			</div>
	  			
	  			<?php if($list_package_notes == "exclusive_sale"){
	  			?> 
	  			<div class="exclusive-text">
	  				Exclusive Sale
	  			</div>
	  			<div class="percentage-text">
	  				<span class="percentage-word">Save</span>
	  				<br>
	  				<?php echo $list_package_notes_exclusive_sale; ?> %
	  			</div>
	  			<div class="clause-text">
	  				If paid in full within 7 days of confirmation
	  			</div>
	  			<?php 
	  			}
	  			
	  			if($list_package_notes == "added_value"){
	  			?>
	  			<div class="add-value-text1">
	  				With over...
	  			</div>
	  			<div class="add-value-text2">
	  				$<?php echo $list_package_notes_added_value; ?>	
	  			</div>
	  			<div class="add-value-text3">
	  				of Added Value included
	  			</div>
	  			

			        
	  			<?php if(!empty($list_package_notes_value_inclusion_1)){?>
	  			<div class="inclusion-text1">
	  				- <?php echo $list_package_notes_value_inclusion_1; ?>	
	  				
	  			</div>
	  			<?php }?>
	  			
	  			<?php if(!empty($list_package_notes_value_inclusion_2)){?>
	  			<div class="inclusion-text2">
	  				- <?php echo $list_package_notes_value_inclusion_2; ?>
	  			</div>
	  			<?php
	  			}?>
	  			<div class="inclusion-bottom">
	  			</div>
	  			<?php
	  			} 
	  			?>
	  		</div>
	  </div>
	  <?php 
	  }
	  ?>
  </div>
  <?php
      if($page_name == "hot-deal-pimalai-resort-and-spa"){
      	$expiry_date = date('jS M Y', mktime(0, 0, 0, 2 , 28, 2015));
      }else{
      	//$expiry_date = date('jS M Y', mktime(0, 0, 0, date("m")  , date("d")+30, date("Y")));
      	$expiry_date = "31 OCT 2015";
      }
    
  ?>
  
  <?php require_once "terms-conditions-content.php";?>
  
</div>

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