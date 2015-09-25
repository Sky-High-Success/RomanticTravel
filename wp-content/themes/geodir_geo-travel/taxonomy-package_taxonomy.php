<?php
/**
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

<?php include_once "template-travel-header.php";?>

<?php if ( have_posts() ) : ?>

<h3 class="travel-h3">POPULAR DESTINATIONS &amp; PACKAGES</h3>

<div class="containertravel">

<?php while ( have_posts() ) : the_post();

		global $post;
		global $package_information;
		$package_information = array(
				'package_name' => $post->post_title ,
				'package_pricing' => get_post_meta($post->ID, 'package_pricing', true),
				'package_excerpt' => get_post_meta($post->ID, 'package_excerpt', true),
				'package_discount' => get_post_meta($post->ID, 'package_discount', true),
				'package_cover_photo_url' =>  get_post_meta($post->ID, 'package_cover_photo_url', true),
				'package_layer_slider_id' => get_post_meta($post->ID, 'package_layer_slider_id', true),
				'package_detail_include' =>  get_post_meta($post->ID, 'package_detail_include', true),
				'package_detail_validity' =>  get_post_meta($post->ID, 'package_detail_validity', true),
				'package_url' => get_permalink($post->ID),

		);

		//error_log("bb".$package_information['package_cover_photo_url']);
		
		$list_package_notes = get_post_meta($post->ID, 'list_package_notes', true);
		
		$list_package_notes_added_value = get_post_meta($post->ID, 'list_package_notes_added_value', true);
		
		$list_package_notes_exclusive_sale = get_post_meta($post->ID, 'list_package_notes_exclusive_sale', true);
		

?>

  <div class="sectiontravel row">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 texttravel">
      <h3 class="hidden-xs hidden-sm">
        <a title="<?php echo $package_information['package_name'];?>" href="<?php echo $package_information['package_url'];?>"><?php echo $package_information['package_name']." | ".$package_information['package_pricing'];?></a>
      </h3>
      
      <h3 class="visible-xs visible-sm">
        <a title="<?php echo $package_information['package_name'];?>" href="<?php echo $package_information['package_url'];?>"><?php echo $package_information['package_name'];?></a>
      </h3>
      
      <p class="text-p"><?php echo $package_information['package_excerpt'];?></p>
      <a href="<?php echo $package_information['package_url'];?>">more info...</a>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
      <div class="icontravel icon-headphones">
      
      		<?php if($list_package_notes == "exclusive_sale" || $list_package_notes == "added_value" ){
      		?>
        	<div class="sticker-discount anim750">
	
 				 <div class="reveal circle_wrapper">
					<div class="circle"></div>
			   	 </div>
						
				 <div class="sticky anim750">
					<div class="front circle_wrapper anim750">
						<div class="circle anim750"></div>
	  				</div>
				 </div>
	
  				<h4 class="sticker-front">
  				<?php 
  				if($list_package_notes == "exclusive_sale"){
  					echo "Exclusive Sale";
  				}
  				
  				if($list_package_notes == "added_value"){
  					echo "Added Value";
  				}
  				?>
  				</h4>
						
  				<div class="sticky anim750">
					<div class="back circle_wrapper anim750">
						<div class="circle anim750"> </div>
					</div>
				</div>
				
				<h4 class="sticker-back">
  				<?php 
  				if($list_package_notes == "exclusive_sale"){
  					echo "Save <br>".$list_package_notes_exclusive_sale."%";
  				}
  				
  				if($list_package_notes == "added_value"){	
  					echo "Over $".$list_package_notes_added_value;
  				}
  				?>
  				</h4>
						
			</div>
			<?php }
			?>
               
        <a href="<?php echo $package_information['package_url'];?>">
          <img src="<?php echo  get_site_url(null, $package_information['package_cover_photo_url']);?>" alt="" />
        </a>
      </div>
    </div>
  </div>
  
  <div class="clearfix"></div>

<?php endwhile; ?>

<?php

global $wp_query;

$wpcrown_pagination = array(
	                'total' => $wp_query->max_num_pages,
	                'current' => max( 1, get_query_var('paged') ),
	                'show_all' => false,
	                'type' => 'plain'
	        );
			
?>

<div id="bottom_pagination_button" class="visible-xs pagination clearfix"> 
  <div class="center-block btn-group btn-group-block" role="group" aria-label="Navigation Button">
  
  <?php previous_posts_link('Previous'); ?>  
  <?php next_posts_link('Next'); ?>
  
  </div>
</div>

<div class="visible-lg visible-md visible-sm pagination clearfix"> 
  <?php echo paginate_links($wpcrown_pagination); ?>
</div>


</div>

<div id="bottom_button">
  <a  class="center-block" href="../../">Back</a>
</div>

<script type="text/javascript">

jQuery(document).ready(function($){

	var obj_len = $("#bottom_pagination_button a").length;

	if(obj_len == 1){
		
		$("#bottom_pagination_button a").each(function( index ){	
			if($(this).text() == "Previous"){
				$(this).after('<button type="button" disabled class="btn btn-warning pagination-button">Next</button>');
				$(this).replaceWith('<button type="button" onclick="location.href = \''+ $(this).attr("href") + '\';" class="btn btn-warning pagination-button">'+$(this).text()+'</button>');
			}else{
				$(this).before('<button type="button" disabled class="btn btn-warning pagination-button">Previous</button>');
				$(this).replaceWith('<button type="button" onclick="location.href = \''+ $(this).attr("href") + '\';" class="btn btn-warning pagination-button">'+$(this).text()+'</button>');
				
			}	
			
		});
		
	}else{
		$("#bottom_pagination_button a").each(function( index ){		
			$(this).replaceWith('<button type="button" onclick="location.href = \''+ $(this).attr("href") + '\';" class="btn btn-warning pagination-button">'+$(this).text()+'</button>');
		});
	}
	
});

</script>
					

<?php else :?>

<?php endif;?>

<?php include_once "template-travel-footer.php";?>
