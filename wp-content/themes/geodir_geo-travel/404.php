<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>

<?php include_once "template-travel-header.php";?>



    <section id="error-404-page" class="row-fluid">
        
        <div >

        	<img class="center-block visible-lg visible-md" alt="404 image" src="<?php echo get_stylesheet_directory_uri() . '/images/fof.png'; ?>" />

        	<img class="center-block visible-sm" alt="404 image" src="<?php echo get_stylesheet_directory_uri() . '/images/fof-500.png'; ?>" />
        	
        	<img class="center-block visible-xs" alt="404 image" src="<?php echo get_stylesheet_directory_uri() . '/images/fof-300x103.png'; ?>" />
        	
        	
			<div class="btn-container">
				<a class="center-block btn btn-danger get-home-button" href="<?php echo get_home_url();?>">Go back to home</a>
			</div>
			
        </div>

    </section>

 <?php include_once "template-travel-footer.php";?>