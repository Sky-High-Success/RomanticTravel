<?php
/**
 * Template name: Holiday Packages Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage classiads
 * @since classiads 1.2.2
 */

?>

<?php include_once "template-travel-header.php";?>

<?php /*
echo do_shortcode('[layerslider id="2"]');
*/
?> 

<div id="templatemo_Search_Box" class="container_wapper hidden-sm hidden-xs" style="background:#fff !important; margin-top:20px;">

        <div class="container">

            <div class="row">

                <div class="col-md-12 header-p right">

                  <div class="row">

                    

                      <div class="col-lg-1 col-md-1">

                      </div>

                      

                    

                      <form action="<?php echo get_home_url(null,"package-search"); ?>" method="get">                 

                        <div class="col-md-5 remove-right-padding">

                          <input class="date-input-top" placeholder="Departure Date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-start-date="-1d" name="date" type="text">

                          <input id="des-hidden" name="des" type="hidden">

                          <input id="des-text-hidden" name="des-text" type="hidden">

                        </div>

                        <div class="col-md-5 dropdown remove-right-padding">

                        <button class="btn btn-default dropdown-toggle" id="package_dropdown" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >

                           Destination

                           <span class="caret"></span>

                        </button>

                        <ul id="package_list" class="dropdown-menu" role="menu" aria-labelledby="package_dropdown">

                         

   		    			  <?php 

   		    			  $term_args = array( 'hide_empty=0' );

   					  

   		    			  $terms = get_terms( 'package_taxonomy', $term_args );

   		    			  foreach ( $terms as $term ) {

   		    			  ?>

   	    				  <li role="presentation">

     					  	  <a role="menuitem" tabindex="-1" href="#" data-slug="<?php echo $term->slug; ?>" ><?php echo $term->name; ?></a>

   		    			  </li>

   					  	

   					      <?php

   				    	  }

   				    	  ?>

 					    

 					    </ul>

                        </div>

                        <div class="visible-lg col-lg-1">

                      

                          <button id="package_search_submit1" type="submit" value="Search" style="margin-top: -12px;">

						    <i class="fa fa-search"></i>

					      </button>

                     

                        </div>

                        

                        <div class="visible-md col-md-1">

                      

                          <button id="package_search_submit2" type="submit" value="Search">

						    <i class="fa fa-search"></i>

					      </button>

                     

                        </div>

                        

 					  </form>

                   

 				

 					

 				  </div>

                </div>

               

                

            </div>

        </div>

  </div>

<div class="wrap">
<h3 class="home-travel-h3">Romantic Travel Packages</h3>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "maldives", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/package-maldives1.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "tahiti", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/tahiti.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "fiji", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/FIJI.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "cook-islands", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/cookislands.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "thailand", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/thailand.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "bali", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/bali.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "mauritius", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/mauritius.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "malaysia", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/MALAYSIA.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "vanuatu", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/vanuatu.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "vietnam", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/vietnam.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "samoa", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/samoa.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "philippines", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/PHILIPPINES.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "seychelles", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/SEYCHELLES.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
<div class="box">
<div class="boxInner"><a href="<?php echo get_term_link( "caribbean", "package_taxonomy" );?>"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/CARIBBEAN.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>


<div class="box">
<div class="boxInner"><a href="http://romantictravel.com.au/taxonomy/qld-islands/"><img src="<?php echo get_home_url(null,"wp-content/uploads/2015/08/QLD-ISLANDS.jpg"); ?>" alt="" /></a>
<div class="titleBox">MORE INFO</div>
</div>
</div>
</div>

 
                


<?php include_once "template-travel-footer.php";?>