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



wp_enqueue_style( 'Boxes-style', get_stylesheet_directory_uri() . '/css/Boxes.css', array(), '1' );


include_once "template-travel-header.php";?>



<?php /*
echo do_shortcode('[layerslider id="2"]');
*/
?> 


<script>

            jQuery(document).ready(function ($) {
                // This command is used to initialize some elements and make them work properly
                
            });
        
</script>

<?php include_once "template-travel-footer.php";?>