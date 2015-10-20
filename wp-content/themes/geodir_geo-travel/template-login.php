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

wp_enqueue_style( 'material-style', get_stylesheet_directory_uri() . '/css/material.min.css', array(), '1' );

wp_enqueue_style( 'ripples-style', get_stylesheet_directory_uri() . '/css/ripples.min.css', array(), '1' );

wp_enqueue_style( 'Boxes-style', get_stylesheet_directory_uri() . '/css/Boxes.css', array(), '1' );

wp_enqueue_script( 'material', get_stylesheet_directory_uri() . '/js/material.min.js', array( 'jquery' ), '2014-07-18', true );

wp_enqueue_script( 'ripples', get_stylesheet_directory_uri() . '/js/ripples.min.js', array( 'jquery' ), '2014-07-18', true );



include_once "template-travel-header.php";?>



<?php /*
echo do_shortcode('[layerslider id="2"]');
*/
?> 


<script>

            $(document).ready(function ($) {
                // This command is used to initialize some elements and make them work properly
                $.material.init();
            });
        
</script>

<?php include_once "template-travel-footer.php";?>