<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Thirteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>


<?php include_once "template-travel-header.php";?>



    <section id="error-404-page" class="row-fluid">
        
        <div >

        	<img class="center-block" alt="404 image" src="<?php echo get_stylesheet_directory_uri() . '/images/fof.png'; ?>" />

			<div class="btn-container">
				<a class="center-block btn btn-danger get-home-button" href="<?php echo get_home_url();?>">Go back to home</a>
			</div>
			
        </div>

    </section>

 <?php include_once "template-travel-footer.php";?>