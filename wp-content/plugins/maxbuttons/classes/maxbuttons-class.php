<?php

define('MAXBUTTONS_VERSION_KEY', 'maxbuttons_version');
//define("MAXBUTTONS_PLUGIN_URL",  plugin_dir_url( __FILE__ ));
//define("MAXBUTTONS_PLUGIN_PATH", plugin_basename(__FILE__)); 

class maxButtons
{

	protected $installed_version;
	protected $plugin_name; 
	protected $plugin_url;
	protected $plugin_path;
	protected $footer = array();
	
	protected static $notices = array(); 
	
	protected $mainClasses = array(); 
	
	protected static $instance;
	
	/* Class constructor 
	   Add hooks and actions used by this plugin. Sets plugin environment information
	*/
	function __construct()
	{
		$this->plugin_url =  self::get_plugin_url(); //plugins_url() . '/' . $this->plugin_name;
		$this->plugin_path = self::get_plugin_path(); //plugin_dir_path($rootfile); 
		$this->plugin_name = trim(basename($this->plugin_path), '/');
		
		$this->installed_version = get_option('MAXBUTTONS_VERSION_KEY'); 
	 	
	 	maxInstall::check_database(); // sigh
	 	
		add_action('init', array($this, 'load_textdomain'));
		add_action('admin_init', array($this, 'addthick')); 
		add_filter('widget_text', 'do_shortcode');
		add_shortcode('maxbutton', array($this, 'shortcode')); 

		add_action("mb-footer", array($this, 'do_footer'),10,3); 
		add_action("wp_footer", array($this, "footer")); 
		
		add_action('media_buttons_context', array($this,'maxbuttons_media_button'));

		add_filter('plugin_action_links', array($this, "plugin_action_links"), 10, 2);
		add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
		
		add_filter("admin_footer_text",array($this, "admin_footer_text"));
		
		if( is_admin())
		{
			add_action('admin_enqueue_scripts', array($this,'add_admin_styles'));
			add_action('admin_enqueue_scripts', array($this,'add_admin_scripts'));	
					
			add_action('admin_init', array($this,'register_settings' ));
			add_action('admin_menu', array($this, 'admin_menu'));
			add_action('admin_footer', array($this,'media_button_admin_footer'));
			add_action('admin_footer', array($this, "footer"));
	
			// errors in user space. No internal error but user output friendly issues
			add_action("mb_display_notices", array($this,"display_notices"));
			
			add_action("wp_ajax_getAjaxButtons", array('maxButtonsAdmin', 'getAjaxButtons'));
		}
		
		$this->setMainClasses(); // struct for override functionality
 		
 		// The second the blocks are being loaded, check dbase integrity 
 		add_action("mb_blockclassesloaded", array($this, "check_database")); 
 
 		self::$instance = $this;
	}
	
	public static function getInstance()
	{
		return self::$instance;
	}
 
	public function setMainClasses()
	{
		$classes = array(
			"button" => "maxButton",
			"block" => "maxBlock", 
			"admin" => "maxButtonsAdmin", 
			"install" => "maxInstall", 
			"groups" => "maxGroups",

		); 
		
		$this->mainClasses = $classes; 		
	
	}
	
	// from block loader action. Checks if all parts of the table are there, or panic if not.
	public function check_database($blocks)
	{
		maxButtonsUtils::addTime("Check database");
			
		$sql = "SELECT id,name,status,cache"; 
		foreach ($blocks as $block => $class) 
		{
			$sql .= ", $block"; 
		} 
		$sql .= " from " . maxButtonsUtils::get_buttons_table_name() . " limit 1"; 
		
		
 
		global $wpdb; 
		$wpdb->hide_errors();
		$result = $wpdb->get_results($sql); 
		 
	
		// check this query for errors. If there is an error, one or more database fields are missing. Fix that. 
		if (isset($wpdb->last_error) && $wpdb->last_error != '') 
		{
			//echo $wpdb->last_error;  echo "ERREUR!";
		 	$install = $this->getClass("install"); 
			$install::create_database_table();
			$install::migrate();
		}
	 
		
		maxButtonsUtils::addTime("End check database");
	}
	
	public function getClass($class)
	{
		if (isset($this->mainClasses[$class])) 
		{
			$load_class = $this->mainClasses[$class]; 
			return new $load_class;
		}
	}
	 
	/* Load the plugin textdomain */
	public function load_textdomain()
	{
		// see: http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way 
		$domain = 'maxbuttons';
		// The "plugin_locale" filter is also used in load_plugin_textdomain()
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR.'/maxbuttons/'.$domain.'-'.$locale.'.mo');	
		$res = load_plugin_textdomain('maxbuttons', false, $this->plugin_name . '/languages/');
 
 	}
 	
 	function addthick()
 	{
 		if (is_admin())
 			add_thickbox();
 	}
 	
 	public function register_settings()
 	{
 		register_setting( 'maxbuttons_settings', 'maxbuttons_user_level' );
 		register_setting( 'maxbuttons_settings', 'maxbuttons_noshowtinymce' );
 
	}

 	public static function get_plugin_path()
 	{
 		return plugin_dir_path(MAXBUTTONS_ROOT_FILE); 
 	}
 	
 	public static function get_plugin_url()
 	{
 		return plugin_dir_url(MAXBUTTONS_ROOT_FILE);
 	}

	function admin_menu() {
		$maxbuttons_capabilities = get_option('maxbuttons_user_level');
		if(!$maxbuttons_capabilities) {
			$maxbuttons_capabilities = 'manage_options';
			settings_fields( 'maxbuttons_settings' );
			update_option('maxbuttons_user_level', $maxbuttons_capabilities);
		}
		$admin_pages = array();

		$page_title = __('MaxButtons : Buttons', 'maxbuttons');
		$menu_title = __('MaxButtons', 'maxbuttons');
		$capability = $maxbuttons_capabilities;
		$admin_capability = 'manage_options';
		$menu_slug = 'maxbuttons-controller';
		$function =  array($this, 'load_admin_page'); 
		$icon_url = $this->plugin_url . '/images/mb-peach-icon.png';
		$submenu_function = array($this, 'load_admin_page'); 
		
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
	
		// We add this submenu page with the same slug as the parent to ensure we don't get duplicates
		$sub_menu_title = __('Buttons', 'maxbuttons');
		$admin_pages[] = add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);
	
		// Now add the submenu page for the Add New page
		$submenu_page_title = __('MaxButtons : Add/Edit Button', 'maxbuttons');
		$submenu_title = __('Add New', 'maxbuttons');
		$submenu_slug = 'maxbuttons-button';
		//$submenu_function = 'maxbuttons_button';
		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
	
		// Now add the submenu page for the Go Pro page
		$submenu_page_title = __('MaxButtons : Upgrade to Pro', 'maxbuttons');
		$submenu_title = __('Upgrade to Pro', 'maxbuttons');
		$submenu_slug = 'maxbuttons-pro';
		//$submenu_function = 'maxbuttons_pro';
		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

		// Now add the submenu page for the Settings page
		$submenu_page_title = __('MaxButtons : Settings', 'maxbuttons');
		$submenu_title = __('Settings', 'maxbuttons');
		$submenu_slug = 'maxbuttons-settings';
		//$submenu_function = 'maxbuttons_settings';
		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $admin_capability, $submenu_slug, $submenu_function);

		// Now add the submenu page for the Support page
		$submenu_page_title = __('MaxButtons : Support', 'maxbuttons');
		$submenu_title = __('Support', 'maxbuttons');
		$submenu_slug = 'maxbuttons-support';
		//$submenu_function = 'maxbuttons_support';
		$admin_pages[] = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $admin_capability, $submenu_slug, $submenu_function);
 
	}	
 	
	function load_admin_page($page)
	{
		$page = $_GET["page"];
		 
		switch($page) 
		{
			case "maxbuttons-button": 
				$pagepath = "includes/maxbuttons-button.php"; 
			break;
			case "maxbuttons-support": 
				$pagepath = "includes/maxbuttons-support.php";
			break;
			case "maxbuttons-settings": 
				$pagepath = "includes/maxbuttons-settings.php"; 
			break;
			case "maxbuttons-pro": 
				$pagepath = "includes/maxbuttons-pro.php"; 
			break;
			default:
				$pagepath = "includes/maxbuttons-controller.php"; 
			break;
		}
		$pagepath = $this->plugin_path . $pagepath; 
		
		include(apply_filters("mb-load-admin-page-$page", $pagepath)); 
	}


	function add_admin_styles($hook) {	

 
		// only hook in maxbuttons realm. 
		if ( strpos($hook,'maxbuttons') === false && $hook != 'post.php' && $hook != 'post-new.php' )
			return;
  
		wp_enqueue_style('maxbuttons-newcss', $this->plugin_url . 'assets/css/style.css');		
		wp_enqueue_style('maxbuttons-css', $this->plugin_url . 'styles.css');
		wp_enqueue_style('maxbuttons-colorpicker-css', $this->plugin_url . 'js/colorpicker/css/colorpicker.css');
	}

	function add_admin_scripts($hook) {	
 
		// only hook in maxbuttons realm.
		if ( strpos($hook,'maxbuttons') === false && $hook != 'post.php' && $hook != 'post-new.php' )
			return;
			
		wp_enqueue_script('jquery-ui-draggable');
 
		wp_enqueue_script('maxbuttons-colorpicker-js', $this->plugin_url . 'js/colorpicker/colorpicker.js', array('jquery'));
		wp_enqueue_script('maxbuttons-modal', $this->plugin_url . 'js/leanModal/jquery.leanModal.min.js', array('jquery'));
		wp_enqueue_script('maxbutton-admin', $this->plugin_url . 'js/maxbuttons-admin.js', array('jquery'), true); 
		wp_enqueue_script('maxbutton-js-init', $this->plugin_url . 'js/init.js', array('maxbutton-admin'), true);
		
		$local = array();
		$local["ajaxurl"] = admin_url( 'admin-ajax.php' );
		$local["maxurl"] = $this->plugin_url; 
		$local["windowtitle"] = __("Select a MaxButton","maxbuttons"); 
		
		wp_localize_script('maxbutton-admin', 'wp_obj', $local);  
	}	
	
	function admin_footer_text($text)
	{
		if (! isset($_GET["page"]))
			return $text;
			
		if ( strpos($_GET["page"],'maxbuttons') === false)
			return $text; 
		$text = '';
		//$text .=  " <p><i>" . sprintf("MaxButtons release: %s", MAXBUTTONS_RELEASE) . "</i><p>"; 
		$text .=   sprintf("If you like MaxButtons please give us a  %s★★★★★%s rating!", 
			"<a href='https://wordpress.org/support/view/plugin-reviews/maxbuttons#postform' target='_blank'>", 
			"</a>")  ; 
		return $text; 
	
	}


	function media_button_admin_footer() { 
		require_once (self::get_plugin_path() . 'includes/maxbuttons-media-button.php');
	}	
	
	function shortcode($atts) 
	{
		 $button = new maxButton();
		 return $button->shortcode($atts); 
	}


	function plugin_action_links($links, $file) {
 
		if ($file == plugin_basename(dirname(MAXBUTTONS_ROOT_FILE) . '/maxbuttons.php')) {
			$label = __('Buttons', 'maxbuttons');
			$dashboard_link = '<a href="' . admin_url() . 'admin.php?page=maxbuttons-controller&action=list">' . $label . '</a>';
			array_unshift($links, $dashboard_link);
		}

		return $links;
	}


	function plugin_row_meta($links, $file) {
		if ($file == plugin_basename(dirname(__FILE__) . '/maxbuttons.php')) {
			$links[] = sprintf(__('%sUpgrade to Pro Version%s', 'maxbuttons'), '<a href="http://maxbuttons.com/?ref=mbfree" target="_blank">', '</a>');
		}
	
		return $links;
	}

	function maxbuttons_media_button($context) {
		global $pagenow, $wp_version;
		$output = '';

		// options 
		if (get_option('maxbuttons_noshowtinymce') == 1) return;
		
		// Only run in post/page creation and edit screens
		if (in_array($pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php'))) {
			$title = __('Add Button', 'maxbuttons');
			$icon = $this->plugin_url . 'images/mb-peach-icon.png';
			$img = '<span class="wp-media-buttons-icon" style="background-image: url(' . $icon . '); width: 16px; height: 16px; margin-top: 1px;"></span>';
			$output = '<a href="" class="maxbutton_thickbox button" title="' . $title . '" style="padding-left: .4em;">' . $img . ' ' . $title . '</a>'; 
		}

		return $context . $output;
}

		function do_footer($id, $code, $type = "css")
		{
			$this->footer[$id][$type] = $code; 
			
		}
		function footer()
		{

 			if(count($this->footer) == 0) return; // nothing
 				
			foreach ($this->footer as $id => $part) 
			{
				foreach ($part as $statements)
				{
					echo $statements; 
				}
			}
		
		}
		
		/* 
			Adds an notice to an notice array for later display on interface 
			@param $type string message | notice | error | fatal
			@param $message string User understandable message
			@param $fatal bool Indication of further panic or not. 
		
		*/
		public static function add_notice($type, $message)
		{
			self::$notices[] = array("type" => $type, 
									"message" => $message
								); 
				
		}
		
		/* Display all notices from notice array - Filter: mb_display_notices
		
		@param $echo echo the results or silently return.
		*/
		public function display_notices($echo = true)
		{
			if ($echo == '') $echo = true;
			$notices = maxButtons::$notices; 
			$output = ''; 
			if (count($notices) == 0)
				return;
				
			foreach($notices as $index => $notice)
			{
				$type = $notice["type"]; 
				$message = $notice["message"]; 
				$output .= "<div class='mb-message $type'> "; 
				$output .= $message ; 
				$output .= "</div>"; 
			
			} 

			if ($echo) echo $output; 
			else return $output;
		}
		
}  // class
 
function maxbuttons_log_me($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}
?>
