<?php
/**
 * Template name: Profile Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
error_log("reached");
global $user_ID;
$author = get_user_by( 'slug', get_query_var( 'author_name' ) ); $user_ID = $author->ID;

$userID = $user_ID;

$subscription_consumption_array = get_user_subscription_consumption($userID);
$subscription_counter_array = get_subscription_counter($userID);

$priority_left = $subscription_counter_array['priority'] - $subscription_consumption_array['priority'];
if ($priority_left < 0){
	$priority_left = 0;
}

$basic_left = $subscription_counter_array['basic'] - $subscription_consumption_array['basic'];
if ($basic_left < 0){
	$basic_left = 0;
}

global $listing_query_vars;
$listing_query_vars['catSearchID'] = '-1';

get_header(); 


global $redux_demo; 

global $current_user; get_currentuserinfo(); $user_ID == $current_user->ID;

$contact_email = get_the_author_meta( 'user_email', $user_ID );
$wpcrown_contact_email_error = $redux_demo['contact-email-error'];
$wpcrown_contact_name_error = $redux_demo['contact-name-error'];
$wpcrown_contact_message_error = $redux_demo['contact-message-error'];
$wpcrown_contact_thankyou = $redux_demo['contact-thankyou-message'];

global $nameError;
global $emailError;
global $commentError;
global $subjectError;
global $humanTestError;

//If the form is submitted
if(isset($_POST['submitted'])) {
	
		//Check to make sure that the name field is not empty
		if(trim($_POST['contactName']) === '') {
			$nameError = $wpcrown_contact_name_error;
			$hasError = true;
		} elseif(trim($_POST['contactName']) === 'Name*') {
			$nameError = $wpcrown_contact_name_error;
			$hasError = true;
		}	else {
			$name = trim($_POST['contactName']);
		}

		//Check to make sure that the subject field is not empty
		if(trim($_POST['subject']) === '') {
			$subjectError = $wpcrown_contact_subject_error;
			$hasError = true;
		} elseif(trim($_POST['subject']) === 'Subject*') {
			$subjectError = $wpcrown_contact_subject_error;
			$hasError = true;
		}	else {
			$subject = trim($_POST['subject']);
		}
		
		//Check to make sure sure that a valid email address is submitted
		if(trim($_POST['email']) === '')  {
			$emailError = $wpcrown_contact_email_error;
			$hasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
			$emailError = $wpcrown_contact_email_error;
			$hasError = true;
		} else {
			$email = trim($_POST['email']);
		}
			
		//Check to make sure comments were entered	
		if(trim($_POST['comments']) === '') {
			$commentError = $wpcrown_contact_message_error;
			$hasError = true;
		} else {
			if(function_exists('stripslashes')) {
				$comments = stripslashes(trim($_POST['comments']));
			} else {
				$comments = trim($_POST['comments']);
			}
		}

		//Check to make sure that the human test field is not empty
		if(trim($_POST['humanTest']) != '8') {
			$humanTestError = "Not Human :(";
			$hasError = true;
		} else {

		}
			
		//If there is no error, send the email
		if(!isset($hasError)) {

			$emailTo = $contact_email;
			$subject = $subject;	
			$body = "Name: $name \n\nEmail: $email \n\nMessage: $comments";
			$headers = 'From <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
			
			wp_mail($emailTo, $subject, $body, $headers);

			$emailSent = true;

	}
}

?>
	<div class="ad-title">
	
        <h2><?php echo get_the_author_meta('display_name', $user_ID ); ?></h2> 	
		
	</div>
	
	<div class="container">
	
		<div class="span8 first">
		
			<div class="account-overview clearfix">
					<h3 style="margin-top: 7px;"><?php _e('ACCOUNT OVERVIEW', 'agrg') ?></h3>
					<div class="h3-seprator"></div>
					<div class="span3 first author-avatar-edit-post">
						<?php $profile = $redux_demo['profile']; ?>
							<?php require_once(TEMPLATEPATH . '/inc/BFI_Thumb.php'); ?>
			    			<?php 

								$author_avatar_url = get_user_meta($user_ID, "flatads_author_avatar_url", true); 

								if(!empty($author_avatar_url)) {

									$params = array( 'width' => 120, 'height' => 120, 'crop' => true );

									echo "<img class='author-avatar' src='" . bfi_thumb( "$author_avatar_url", $params ) . "' alt='' />";

								} else { 

							?>

								<?php $avatar_url = wpcook_get_avatar_url ( get_the_author_meta('user_email', $user_ID), $size = '150' ); ?>
								<img class="author-avatar" src="<?php echo $avatar_url; ?>" alt="" />

							<?php } ?>
						<span class="author-profile-ad-details"><a href="" class="button-ag large green"><span class="button-inner"><?php echo get_the_author_meta('display_name', $user_ID ); ?></span></a></span>
					</div>
					<div class="span4">					
							<span class="ad-detail-info"><?php _e( 'Number On Top Of List', 'agrg' ); ?>
								<span class="ad-detail"><?php echo $subscription_consumption_array['top_of_list']; ?></span>
							</span>
											
							<span class="ad-detail-info"><?php _e( 'Free Listing Posted', 'agrg' ); ?>
							  <span class="ad-detail"><?php echo $subscription_consumption_array['free']; ?></span>
					        </span>
				        
							<span class="ad-detail-info"><?php _e( 'Priority Listing Posted', 'agrg' ); ?>
								<span class="ad-detail"><?php echo $subscription_consumption_array['priority']; ?></span>
							</span>
							
							<span class="ad-detail-info"><?php _e( 'Priority Listing Left', 'agrg' ); ?>
								<span class="ad-detail"><?php echo $priority_left; ?></span>
							</span>
							
							<span class="ad-detail-info"><?php _e( 'Basic Listing Posted', 'agrg' ); ?>
								<span class="ad-detail"><?php echo $subscription_consumption_array['basic']; ?></span>
							</span>
							
							<span class="ad-detail-info"><?php _e( 'Basic Listing Left', 'agrg' ); ?>
								<span class="ad-detail"><?php echo $basic_left; ?></span>
							</span>
					</div>
					
				</div>

			<section id="ads-profile" class="category-page-ads">
									
					<h3 class="main-title" style="text-transform:uppercase;"><?php echo get_the_author_meta('display_name', $user_ID ); ?>'s <?php _e( 'LISTINGS', 'agrg' ); ?></h3>
					<div class="h3-seprator"></div>
					
					<div class="pane latest-ads-holder">

						<div class="latest-ads-grid-holder">

						<?php
						
						    global $listing_query_vars, $wp;
						
						    $listing_query_vars['isfeatured_query'] = false;
						
						    $current_page = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
						    
						    error_log($current_page);
							
						    $args = array('post_type' => 'listing',
								'posts_per_page'=> 4,
								'paged' => $current_page,
						    	'post_status'=> 'publish',
								'author' => $userID);
							
						    $the_query = new WP_Query($args);
							
							$current = -1;
							$current2 = 0;
							
							$emptyPost2 = 0;

							?>

							<?php while ($the_query->have_posts()) : $the_query->the_post(); 
							          $current++; $current2++; $emptyPost2++;
							          
							          $params = array( "width" => 370, "height" => 240, "crop" => true );
							          $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "single-post-thumbnail" );
							           
							          $listing_permalink = get_permalink();
							          $listing_thumb_src = bfi_thumb( "$image[0]", $params );
							          $listing_title = get_the_title();
							          
	
							?>

								<div class="ad-box span3 latest-posts-grid <?php if($current%3 == 0) { echo 'first'; } ?>">

							<?php
                                 if ( has_post_thumbnail()) {
								   $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
								   echo '<a class="ad-image" href="' .get_permalink($post->ID). '" title="' . the_title_attribute('echo=0') . '" >';
								   echo get_the_post_thumbnail($post->ID, '270x220'); 
								   echo '</a>';
								 }
							?>
				    		
							<div class="post-title-cat">
				    			<div class="ad-category">
				    					
				    			<?php   $category = get_the_category();

							        	if ($category[0]->category_parent == 0) {

											$tag = $category[0]->cat_ID;
				
											$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
											if (isset($tag_extra_fields[$tag])) {
												$category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
												$category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
											}

										} else {

											$tag = $category[0]->category_parent;

											$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
											if (isset($tag_extra_fields[$tag])) {
												$category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
												$category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
											}

										}

										if(!empty($category_icon_code)) {

									?>

						        	<div class="category-icon-box"><?php $category_icon = stripslashes($category_icon_code); echo $category_icon; ?></div>

						        	<?php } 

						        	$category_icon_code = "";

						        	?>

		

				    			</div>

				    			
				    			
								
				    		
								<div class="post-title">
									<a href="<?php the_permalink(); ?>"><?php $theTitle = get_the_title(); $theTitle = (strlen($theTitle) > 22) ? substr($theTitle,0,22).'...' : $theTitle; echo $theTitle; ?></a>
								</div>
							</div>

						</div>

							<?php endwhile; ?>

						</div>
					<?php 								
					$wpcrown_pagination = array(

	                  'total' => $the_query->max_num_pages,
	                  'current' => $current_page,
	                  'show_all' => false,
	                  'type' => 'plain',
					  'add_args' => $_GET
	                );
			
			        echo '<div class="pagination">' . paginate_links($wpcrown_pagination) . '</div>';
																	
																		
					wp_reset_query(); ?>
					
					<?php if($emptyPost2 == 0) { ?>
				    <div class="full view-empty">
					  <p><?php _e( 'No results found for the selected search criteria.', 'agrg' ); ?></p>
				    </div>
			        <?php } ?>

					</div>

				

			</section>
			
			<div class="full">
						<h3><?php _e( 'LEAVE MESSAGE TO AUTHOR', 'agrg' ); ?></h3>
						<div class="h3-seprator"></div>
						
						<div id="contact-ad-owner-v2">

							<?php if(isset($emailSent) && $emailSent == true) { ?>

								<div class="full">
									<h5><?php echo $wpcrown_contact_thankyou ?></h5> 
								</div>

							<?php } else { ?>

							<?php if($nameError != '') { ?>
								<div class="full">
									<h5><?php echo $nameError;?></h5> 
								</div>										
							<?php } ?>
															
							<?php if($emailError != '') { ?>
								<div class="full">
									<h5><?php echo $emailError;?></h5>
								</div>
							<?php } ?>

							<?php if($subjectError != '') { ?>
								<div class="full">
									<h5><?php echo $subjectError;?></h5>  
								</div>
							<?php } ?>
															
							<?php if($commentError != '') { ?>
								<div class="full">
									<h5><?php echo $commentError;?></h5>
								</div>
							<?php } ?>

							<?php if($humanTestError != '') { ?>
								<div class="full">
									<h5><?php echo $humanTestError;?></h5>
								</div>
							<?php } ?>

							<form name="contactForm" action="<?php the_permalink(); ?>" id="contact-form" method="post" class="contactform" >
																
								<input type="text" placeholder="Full Name" name="contactName" id="contactName" class="input-textarea half" />
															 
								<input type="text" placeholder="Email" name="email" id="email" class="input-textarea half" style="margin-right:0px !important;" />

								<input type="text" placeholder="Subject" name="subject" id="subject" class="input-textarea" />
															 
								<textarea placeholder="Write your message here..." name="comments" id="commentsText" cols="8" rows="5" ></textarea>

								<p class="humantest"><?php _e("Human test. Please input the result of 5+3=?", "agrg"); ?></p>

								<input type="text" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';" name="humanTest" id="humanTest" value="" class="input-textarea half" />
								<div class="clearfix"></div>
								<div class="btn-container">								
									<input name="submitted" type="submit" value="Send Message" class="input-submit"/>	
								</div>
							</form>

							<?php } ?>

						</div>

					</div>
			
		</div>
		
		<div class="span4">
			<?php get_sidebar('pages'); ?>
		</div>
		
		
		
	</div>

	
	<?php 

		global $redux_demo; 

		$featured_ads_option = $redux_demo['featured-options-on'];

	?>

	<?php if($featured_ads_option == 1) { ?>

    <section id="featured-abs">
        
        <div class="container" style="width:100%">
            
            <div id="tabs" class="full">
			    	
                <?php $cat_id = get_cat_ID(single_cat_title('', false)); ?>
			    

                <div class="pane">
                 
                  	<div id="projects-carousel">

			    		<?php

							global $paged, $wp_query, $wp;

							$args = wp_parse_args($wp->matched_query);

							$temp = $wp_query;

							$wp_query= null;

							$wp_query = new WP_Query();

							$wp_query->query('post_type=post&posts_per_page=-1');

							$current = -1;

						?>

						<?php while ($wp_query->have_posts()) : $wp_query->the_post();

							$featured_post = "0";

							$post_price_plan_activation_date = get_post_meta($post->ID, 'post_price_plan_activation_date', true);
							$post_price_plan_expiration_date = get_post_meta($post->ID, 'post_price_plan_expiration_date', true);
							$post_price_plan_expiration_date_noarmal = get_post_meta($current_post, 'post_price_plan_expiration_date_normal', true);
							$todayDate = strtotime(date('m/d/Y h:i:s'));
							$expireDate = $post_price_plan_expiration_date;

							if(!empty($post_price_plan_activation_date)) {

								if(($todayDate < $expireDate) or $post_price_plan_expiration_date == 0) {
									$featured_post = "1";
								}

						} ?>

						<?php if($featured_post == "1") { 

							$current++;

						?>

						<div class="ad-box span3">
							<?php
								if ( has_post_thumbnail()) {
								   $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
								   echo '<a class="ad-image" href="' .get_permalink($post->ID). '" title="' . the_title_attribute('echo=0') . '" >';
								   echo get_the_post_thumbnail($post->ID, 'premium-post-image'); 
								   echo '</a>';
								 }
							?>
			    			

			    			<div class="ad-hover-content">
			    				<div class="ad-category">
			    					
			    					<?php
 
						        		$category = get_the_category();

						        		if ($category[0]->category_parent == 0) {

											$tag = $category[0]->cat_ID;

											$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
											if (isset($tag_extra_fields[$tag])) {
											    $category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
											    $category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
											}

										} else {

											$tag = $category[0]->category_parent;

											$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
											if (isset($tag_extra_fields[$tag])) {
											    $category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
											    $category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
											}

										}

										if(!empty($category_icon_code)) {

									?>

					        		<div class="category-icon-box" ><?php $category_icon = stripslashes($category_icon_code); echo $category_icon; ?></div>

					        		<?php } 

					        		$category_icon_code = "";

					        		?>

			    				</div>
								
								
								<div class="post-title">
									<a href="<?php the_permalink(); ?>"><?php $theTitle = get_the_title(); $theTitle = (strlen($theTitle) > 40) ? substr($theTitle,0,37).'...' : $theTitle; echo $theTitle; ?></a>
								</div>
								
							</div>	
								<?php
								$post_price = get_post_meta($post->ID, 'post_price', true);
								if(!empty($post_price)){								
								?>
								<div class="add-price"><span><?php echo $post_price; ?></span></div> 
								<?php } ?>
								
			    				

			    			

						</div>

			    		<?php } ?>

			    		<?php endwhile; ?>	
												
						<?php wp_reset_query(); ?>

			    	</div>

			    	<?php wp_enqueue_script( 'jquery-carousel', get_stylesheet_directory_uri().'/js/jquery.carouFredSel-6.2.1-packed.js', array('jquery'),'',true); ?>
										
					<script>

						jQuery(document).ready(function () {

							jQuery('#projects-carousel').carouFredSel({
								auto: true,
								prev: '#carousel-prev',
								next: '#carousel-next',
								pagination: "#carousel-pagination",
								mousewheel: true,
								scroll: 2,
								swipe: {
									onMouse: true,
									onTouch: true
								}
							});

						});
											
					</script>
					<!-- end scripts -->

			    </div>

			    

			</div>
        
        </div>

    </section>

    <?php } ?>
	
	
    <script>
		// perform JavaScript after the document is scriptable.
		jQuery(function() {
			jQuery("ul.tabs").tabs("> .pane", {effect: 'fade', fadeIn: 200});
		});
	</script>

<?php get_footer(); ?>