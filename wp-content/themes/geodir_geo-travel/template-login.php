<?php
/**
 * Template name: Login Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>

<?php

if ( is_user_logged_in() ) {
	
	wp_redirect(get_home_url());
	exit();
}

if(isset($_POST['type_hidden']) && $_POST['type_hidden'] == "login" && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$email = esc_sql ( isset ( $_REQUEST ['login_email'] ) ? $_REQUEST ['login_email'] : '' );
	$password = esc_sql ( isset ( $_REQUEST ['login_password'] ) ? $_REQUEST ['login_password'] : '' );
	$remember = esc_attr(strip_tags($_POST['remember_me']));
	
	if ($remember)
		$remember = true;
	else
		$remember = false;
	
	error_log($email);
	error_log($password);
	
	$creds = array();
	$creds['user_login'] = $email;
	$creds['user_password'] = $password;
	$creds['remember'] = $remember;
	
	$user = wp_signon( $creds, false );
	if ( is_wp_error($user) ){
		echo $user->get_error_message();
	}
	else{
		wp_redirect(get_home_url());
		exit();
	}
	
}


wp_enqueue_style ( 'Boxes-style', get_stylesheet_directory_uri () . '/css/Boxes.css', array (), '1' );

include_once "template-travel-header.php";



?>



<div class="login-container">
	<div class="row">
		<div class="col-xs-12 col-sm-offset-1 col-sm-10 col-md-12 col-lg-offset-1 col-lg-10">
			<div class="box">
				<div class="box-icon">
					<span class="fa fa-4x fa-lock"></span>
				</div>
				<div class="info">

					<!-- Form -->
					<form  role="form" id="loginform" action="" method="post"
						class="form-signin">
						<div class="row">
							<div class="col-md-6 column-1">
								<h4 class="text-center text-warning">Login</h4>
					
								<div class="form-group has-warning">
									
									<input type="text" required="" id="userid" name="login_email" placeholder="ENTER YOUR USER ID (EMAIL)"
											class="form-control  empty">
		
						
									
								</div>
								<div class="form-group has-warning">
									
									<input type="password" id="password" placeholder="ENTER PASSWORD" required="" name="login_password" class="form-control empty">
		
								</div>
								
								<div class="form-group has-warning item-group-2">
									<div class="form-control-wrapper">
									<div class="row">
										<div class="checkbox col-md-6">
										<label>
											<input type="checkbox" name="remember_me" value="remember_me">
											 Remember Me 
										</label>
  	              						</div>
  	              						
  	              						<div class="col-md-6 forget-password">
  	              							<a class="" href="#"> Forgot your password?</a>
  	              						</div>
  	              					</div>
		
									</div>
								</div>
								
								<div class="form-group has-warning">
								
									 <input type="hidden" name="type_hidden" value="login">
         
  	             					<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
								
									<button id="loginbtn" type="submit" class="btn btn-warning center-block">Sign in</button>
								</div>
								
								<div class="form-group has-warning">
								
									<p class="switch-forms c-text-center">
										<span id="switch-form-login">
											Not registered with Romantic Travel?
											<a class="c-dark-green c-underline c-pointer">Register now</a>
											</span>
									</p>
								</div>
								
							</div>
							<div class="col-md-6 login-form-border column-2">
							
							   <h4 class="text-center text-warning social-text">Social</h4>
							
							   <div class="form-group has-warning">
							   		<a class="btn social-login-button social-login-button-facebook c-rounded-corners5 center-block" href="<?php echo wp_login_url( get_home_url()); ?>&action=wordpress_social_authenticate&provider=Facebook" id="loginFacebookButton">
               								 <span class="social-login-icon"><i class="fa fa-facebook"></i></span>
               								 <span class="social-login-text"><span class="login-text c-hide"><span class="link-text">Sign in with </span><span class="link">Facebook</span></span></span>
           							</a>
							   </div>
							   
							   <div class="form-group has-warning">
								    <a class="btn social-login-button social-login-button-google c-rounded-corners5 center-block" href="<?php echo wp_login_url( get_home_url());?>&action=wordpress_social_authenticate&provider=Google" id="loginGoogleButton">
						                <span class="social-login-icon"><i class="fa fa-google-plus"></i></span>
						                <span class="social-login-text">
						                	<span class="login-text c-hide">
						                		<span class="link-text">Sign in with </span>
						                		<span class="link">Google</span>
						                	</span>
						                </span>
						            </a>
            					</div>
							   
							</div>
						
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<?php 
/*
       * echo do_shortcode('[layerslider id="2"]');
       */
?>


<script>

            jQuery(document).ready(function ($) {

                $(".login-container .box-icon").addClass("animated rubberBand");
                $("#loginform h4").addClass("animated rubberBand");

                $("#loginform .column-2 .form-group ").addClass("animated slideInRight");

                $("#loginform .column-1 .form-group").addClass("animated slideInLeft");
                //$(".ads-main-page").css("background-image",'url("<?php echo get_stylesheet_directory_uri () . '/images/6885777-blurred.jpg'; ?>")');
                // This command is used to initialize some elements and make them work properly
                
            });
        
</script>

<?php include_once "template-travel-footer.php";?>