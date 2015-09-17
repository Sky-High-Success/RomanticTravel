<?php
/*
Plugin Name: Simple Instant Search
Plugin URI: http://en.bainternet.info
Description: With This Plugin you can eaily add instant search functionalty to your site or blog.
Version: 1.4
Author: Bainternet
Author URI: http://en.bainternet.info
*/
/*
		* 	Copyright (C) 2012 - 2013  Ohad Raz aKa Bainternet
		*	http://en.bainternet.info
		*	admin@bainternet.info

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Disallow direct access to the plugin file */
if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
	die('Sorry, but you cannot access this page directly.');
}



if (!class_exists('Simple_Instant_Search')){
	class Simple_Instant_Search {
	
	   // Class Variables
		var $localization_domain = "sis";
		
	    /**
	     * Class Constractor
	     */
	    function __construct() {
			//add shortcode
	    	add_shortcode('IS', array($this,'I_S_shortcode'));
	    	//check for shortcode
	    	add_action('the_posts', array($this,'check_for_shortcode'));
	        //add_action('admin_menu', array($this, "admin"));
	        //ajax handler
	    	add_action( 'wp_ajax_nopriv_i_s_magic', array($this, 'ajax_search') );
			add_action( 'wp_ajax_i_s_magic',  array($this, 'ajax_search') );
	        add_action('wp_enqueue_scripts',array($this,'I_S_add_scripts_and_styles'));
		
			//Language Setup
			$locale = get_locale();
			load_plugin_textdomain( $this->localization_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

			if (is_admin())
				$this->admin();
	    }
	
	    public function admin() {
	        add_action('init',array($this,'load_admin_panel'),50);			
	    }
	
	    public function load_admin_panel() {
	        include_once('admin/adminp.php');
	        include_once('admin/instant_search_panel.php');
	    }
		
		/**
		 * shortcode to display search form and results Div
		 * @return string - search form and results Div
		 */
		public function I_S_shortcode(){
			$options = get_option('baIS_settings');
			
			if (empty($options)){
				return __('Simple Instant Search needs you to set it up first,<br/> please head over to the plugin admin panel under settings >> instant search', 'sis');	
			}
			$preview_url = plugins_url('simple-instant-search/images/');
			
			
			if (isset($options['Loader_Image'])){
				$src = $preview_url.'preview_00'.$options['Loader_Image'].'.gif'; 
			}
			else{
				$src = $preview_url.'preview_004.gif';
			}
			return '<div class="I_S">
				        <form id="I_S_form" method="GET" action="">
					   <input type="text" id="I_S_Q" name="I_S_Q" />
					   <input type="submit" value="'.__('Search','sis').'" />
					   <div id="I_S_ajax_loader" style="float: left; display: none;"><img src="'.$src.'"></div>
					</form>
				</div>
					<br />
					<div id="results"></div>';
		}
		
		/**
		 * ajax search function
		 * @return array() with title,content JSON
		 */
		public function ajax_search(){
			global $wpdb;
		   if (isset($_GET['I_S_Q'])){
			$q = htmlspecialchars($_GET['I_S_Q']);
			$q = mysql_real_escape_string($q);
			$q = $wpdb->escape($q);
		   }else{
		      echo json_encode(apply_filters( 'instant_search_res', array() ) );
		      die();
		   }
			$options = get_option('baIS_settings'); 
			
						
			/*
			 * post_title LIKE '%".$q."%' OR post_content LIKE '%".$q."%'
				AND post_status = 'publish' 
				AND post_type IN (".implode(',', $p_types).")
			 */
			
			$query = array(
				'post_type' => apply_filters('instant_search_q_post_types', $options['ptype']),
    	        'suppress_filters' => true,
    	        'update_post_term_cache' => false,
       	    	'update_post_meta_cache' => false,
        	    'post_status' => 'publish',
            	'order' => 'DESC',
            	'orderby' => apply_filters('instant_search_q_orderby', 'post_date'),
            	'posts_per_page' => apply_filters('instant_search_q_limit', $options['limit']),
				's' => $q
        	);
			
        	
		 	$get_posts = new WP_Query;
  		    $posts = $get_posts->query( apply_filters('instant_search_q_query',$query) );
        	
			 // Check if any posts were found.
  		    if ( ! $get_posts->post_count ){
  		    	echo json_encode(array());
		      	die();
  		    }

  		    
  		    //Create an array with the results
			
			$results=array();
			foreach ( $posts as $post ) {
			
				$content = $post->post_excerpt;
				if (empty($content)){
					$dom = new DOMDocument();
					$content = apply_filters('the_content', $post->post_content,$post->ID);
					$content = do_shortcode($content);
					@$dom->loadHTML($content);
					$content1 = $dom->getElementsByTagName('p')->item(0);
					$content = $dom->savexml($content1);
					
				}
				
				$content = str_replace("<p>","",$content);
				$content = str_replace("</p>","",$content); 
				$content = apply_filters('instant_search_res_content', $content,$post->ID);
				
				
				$r_title = apply_filters('instant_search_res_title_link',"<a href='".get_permalink($post->ID)."'>".trim( esc_html( strip_tags( get_the_title( $post ) ) ) )."</a>",$post->ID);
				$results[] = array(
				  'title'=> $r_title,
				  'content'=> $content,
				  'url' => get_permalink($post->ID)
				);
			}
			
			$results = apply_filters('instant_search_res',$results,$q);
			//using JSON to encode the array
			echo json_encode($results);
			die();
		}
		
		/**
		 * Check if shortcode is present and if so add scripts and styles.
		 * @param array $posts
		 * @return array $posts
		 */
		function check_for_shortcode($posts) {
		    if ( empty($posts) )
		        return $posts;
		
		    $flag = false;
		
		    foreach ($posts as $post) {
		        if ( stripos($post->post_content, '[IS') )
		            $flag = true;
		            break;
		        }
		
		    if ($flag){
	   			$this->I_S_add_scripts_and_styles();
		    }
		    return $posts;
		}
		
		/**
		 * Void function to add needed scripts and styles.
		 */
		public function I_S_add_scripts_and_styles(){
			$ScriptUrl = WP_PLUGIN_URL . '/simple-instant-search/js/instant.js';
			wp_enqueue_script('jquery');
			wp_enqueue_script('instant', $ScriptUrl, array('jquery'));
		    $StyleUrl = WP_PLUGIN_URL . '/simple-instant-search/css/instant.css';
		    wp_register_style('instant', $StyleUrl);
		    wp_enqueue_style( 'instant');
		    wp_localize_script( 'instant', 'instant', array(
		    	'AjaxUrl' => admin_url( 'admin-ajax.php' ),
		    	'read_more' => __('Read More...','sis')));	
		}
	
		
	}//end class
}//end if
new Simple_Instant_Search();