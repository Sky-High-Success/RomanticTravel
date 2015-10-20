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

wp_enqueue_style ( 'Boxes-style', get_stylesheet_directory_uri () . '/css/Boxes.css', array (), '1' );

include_once "template-travel-header.php";
?>



<div class="container login-container">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-10">
			<div class="box">
				<div class="box-icon">
					<span class="fa fa-4x fa-lock"></span>
				</div>
				<div class="info">

					<!-- Form -->
					<form  role="form" id="loginform" action="" method="post"
						class="form-signin">
						<div class="row">
							<div class="col-lg-6">
								<h4 class="text-center text-warning">Login</h4>
					
								<div class="form-group has-warning">
									<div class="form-control-wrapper">
										<input type="text" required="" id="userid" name="login_email"
											class="form-control empty">
		
										<div class="floating-label">ENTER YOUR USER ID (EMAIL)</div>
										<span class="material-input"></span>
		
									</div>
								</div>
								<div class="form-group has-warning">
									<div class="form-control-wrapper">
										<input type="password" id="password" name="login_password" class="form-control empty">
		
		
										<div class="floating-label">ENTER PASSWORD</div>
										<span class="material-input"></span>
		
									</div>
								</div>
							</div>
							<div class="col-lg-6">
							</div>
						
						</div>
						

						<button id="loginbtn" class="btn btn-warning ">Sign in</button>

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
                // This command is used to initialize some elements and make them work properly
                
            });
        
</script>

<?php include_once "template-travel-footer.php";?>