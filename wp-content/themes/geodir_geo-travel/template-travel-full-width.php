<?php
/**
 * Template name: Romantic Travel Full Width Page.
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */
?>

<?php include_once "template-travel-header.php";?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							
<?php the_content(); ?>
															
<?php endwhile; endif; ?>


<?php include_once "template-travel-footer.php";?>