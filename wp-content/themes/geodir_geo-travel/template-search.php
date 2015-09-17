<?php
/**
 * Template name: Package Search
 * 
 * The template for displaying Package Taxonomy pages.
 *
 * Used to display archive-type pages for posts with a post format.
 * If you'd like to further customize these Post Format views, you may create a
 * new template file for each specific one.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>

<?php

$__ELEMENT_PER_PAGE = 10;


if(isset($_GET['date']) && isset($_GET['des'])){
	
	// Retrieve the URL variables (using PHP).
	$raw_date = esc_attr($_GET['date']);
	$the_date = date_create($raw_date);
	
	$the_des = esc_attr($_GET['des']);
	
	$all_packages = get_all_packages_meta();
	
	global $filtered_package_arr;
	
	$filtered_package_arr = array();
	
	foreach($all_packages as $the_package){
	  if(empty($the_des)){
	  	
	  	if(empty($raw_date)){
	  		$filtered_package_arr[] = $the_package->id;
	  	}
	  	else{
	  	
	  		$from_arr = $the_package->from;
	  		$to_arr = $the_package->to;
	  	
	  		if(count($from_arr) == count($to_arr)){
	  			for($i=0;$i<count($from_arr);$i++){
	  	
	  				$from_date = date_create_from_format("d-M-Y", $from_arr[$i]);
	  				$to_date = date_create_from_format("d-M-Y", $to_arr[$i]);
	  	
	  	
	  				if($from_date <= $the_date && $the_date <= $to_date){
	  					$filtered_package_arr[] = $the_package->id;
	  					break;
	  				}
	  	
	  			}
	  		}
	  		 
	  	}
	  	
	  }	
	  else{
	  	if(has_term($the_des, 'package_taxonomy', $the_package->id)){
	  		if(empty($raw_date)){
	  			$filtered_package_arr[] = $the_package->id;
	  		}
	  		else{
	  			
	  			$from_arr = $the_package->from;
	  			$to_arr = $the_package->to;
	  			
	  			if(count($from_arr) == count($to_arr)){
	  				for($i=0;$i<count($from_arr);$i++){
	  			
	  					$from_date = date_create_from_format("d-M-Y", $from_arr[$i]);
	  					$to_date = date_create_from_format("d-M-Y", $to_arr[$i]);
	  			
	  			
	  					if($from_date <= $the_date && $the_date <= $to_date){
	  						$filtered_package_arr[] = $the_package->id;
	  						break;
	  					}
	  			
	  				}
	  			}
	  		
	  		}
	  	}
	  }
	  
		
	}
	
	
}else{
	wp_redirect(get_home_url());
}

?>

<?php include_once "template-travel-header.php";?>

<h3 class="travel-h3">Search result of <?php echo (empty($_GET['date']))?"All Departure Dates":"Departure Date ".$_GET['date'] ;?> &amp; <?php echo (empty($_GET['des']))?"All Destinations":"Destination ".$_GET['des-text'] ;?></h3>

<div class="containertravel">

<?php if(count($filtered_package_arr) == 0){

?>
  <h4>No results found</h4>
<?php
}else{ 
	
	$num_page = ceil(count($filtered_package_arr)/$__ELEMENT_PER_PAGE);
	$start = $__ELEMENT_PER_PAGE * (max( 1, get_query_var('paged') ) - 1 );
	
?>


<?php for($i=$start;$i<min(count($filtered_package_arr), $start + $__ELEMENT_PER_PAGE );$i++){
	
	    $package = $filtered_package_arr[$i];
		global $package_information;
		$package_information = array(
				'package_name' => get_post($package)->post_title ,
				'package_pricing' => get_post_meta($package, 'package_pricing', true),
				'package_excerpt' => get_post_meta($package, 'package_excerpt', true),
				'package_cover_photo_url' =>  get_post_meta($package, 'package_cover_photo_url', true),
				'package_layer_slider_id' => get_post_meta($package, 'package_layer_slider_id', true),
				'package_detail_include' =>  get_post_meta($package, 'package_detail_include', true),
				'package_detail_validity' =>  get_post_meta($package, 'package_detail_validity', true),
				'package_url' => get_permalink($package),

		);

		//error_log("bb".$package_information['package_cover_photo_url']);
		
		

?>

  <div class="sectiontravel row">
    <div class="col-xs-6 texttravel">
      <h3 class="hidden-xs hidden-sm">
        <a title="<?php echo $package_information['package_name'];?>" href="<?php echo $package_information['package_url'];?>"><?php echo $package_information['package_name']." | ".$package_information['package_pricing'];?></a>
      </h3>
      
      <h3 class="visible-xs visible-sm">
        <a title="<?php echo $package_information['package_name'];?>" href="<?php echo $package_information['package_url'];?>"><?php echo $package_information['package_name'];?></a>
      </h3>
      
      <p class="text-p"><?php echo $package_information['package_excerpt'];?></p>
      <a href="<?php echo $package_information['package_url'];?>">more info...</a>
    </div>
    <div class="col-xs-6">
      <div class="icontravel icon-headphones">
        <a href="<?php echo $package_information['package_url'];?>"><img src="<?php echo  get_site_url(null, $package_information['package_cover_photo_url']);?>" alt="" /></a>
      </div>
    </div>
  </div>
  
  <div class="clearfix"></div>

<?php }
} ?>

<?php

$wpcrown_pagination = array(
	                'total' => $num_page,
	                'current' => max( 1, get_query_var('paged') ),
	                'show_all' => false,
	                'type' => 'plain',
					'mid_size' => 1,
	        );
			
?>

<div id="bottom_pagination_button" class="visible-xs pagination clearfix"> 
  <div class="center-block btn-group btn-group-block" role="group" aria-label="Navigation Button">
  <?php if($num_page > 1){ 
    if(max( 1, get_query_var('paged') ) == 1){ ?>
      <button class="btn btn-warning pagination-button" disabled type="button">Previous</button>
      <button class="btn btn-warning pagination-button" onclick="location.href = '<?php echo get_home_url(null,"package-search")."/page/".(max( 1, get_query_var('paged') ) + 1)."/?".http_build_query($_GET); ?>';" type="button">Next</button>
    
    <?php	
    }
    elseif(max( 1, get_query_var('paged') ) == $num_page){ ?>
      <button class="btn btn-warning pagination-button" onclick="location.href = '<?php echo get_home_url(null,"package-search")."/page/".(max( 1, get_query_var('paged') ) - 1)."/?".http_build_query($_GET); ?>';" type="button">Previous</button>
      <button class="btn btn-warning pagination-button" disabled type="button">Next</button>
   
    <?php	
    }
    else{ ?>
      <button class="btn btn-warning pagination-button" onclick="location.href = '<?php echo get_home_url(null,"package-search")."/page/".(max( 1, get_query_var('paged') ) - 1)."/?".http_build_query($_GET); ?>';" type="button">Previous</button>
      <button class="btn btn-warning pagination-button" onclick="location.href = '<?php echo get_home_url(null,"package-search")."/page/".(max( 1, get_query_var('paged') ) + 1)."/?".http_build_query($_GET); ?>';" type="button">Next</button>
  
    <?php	
    }
  ?>
  
  <?php 
  } ?>
  </div>
</div>

<div class="visible-lg visible-md visible-sm pagination clearfix"> 
  <?php echo paginate_links($wpcrown_pagination); ?>
</div>


</div>


<div id="bottom_button">
  <a  class="center-block" href="../">Back</a>
</div>

<script type="text/javascript">

jQuery(document).ready(function($){

	
	
});

</script>
					

<?php include_once "template-travel-footer.php";?>
