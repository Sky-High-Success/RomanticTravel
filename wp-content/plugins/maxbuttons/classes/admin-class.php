<?php


class maxButtonsAdmin
{
	
	protected $wpdb; 
	protected static $instance = null; 
	
	function __construct()
	{
		global $wpdb; 
		$this->wpdb = $wpdb;
	}
	
	public static function getInstance()
	{
		if (is_null(self::$instance)) 
			self::$instance = new maxButtonsAdmin(); 
		
		return self::$instance; 
	
	}


	/* Get multiple buttons 
	
		Used for overview pages, retrieve buttons on basis of passed arguments. 
		
		@return array Array of found buttons with argument
	*/	
	
	function getButtons($args = array())
	{
		
		$defaults = array(
			"status" => "publish", 
			"orderby" => "", 
			"order" => "asc",
			"limit" => 20, 
			"paged" => 1, 
		);
		$args = wp_parse_args($args, $defaults); 
		
		$limit = intval($args["limit"]); 
		$page = intval($args["paged"]);
		$escape = array(); 
		$escape[] = $args["status"];
		
		// 'white-list' escaping
		switch ($args["orderby"])
		{
			case "name": 
			default: 
				$orderby = "name"; 	
			break;

		}
		
		switch($args["order"])
		{
			case "DESC": 
				$order = "DESC"; 
			break;
			case "ASC": 
			default:
				$order = "ASC"; 
			break;
		}

		
		$sql = "SELECT id FROM " . maxButtonsUtils::get_buttons_table_name() . " WHERE status = '%s'"; 
		if ($args["orderby"] != '')
		{
			$sql .=  " ORDER BY $orderby $order"; 
 
		}	 
	 
	 	if ($limit > 0) 
	 	{

	 		if ($page == 1 ) 
	 			$offset = 0; 
	 		else 
	 			$offset = ($page-1) * $limit;
	 		
	 		$sql .= " LIMIT $offset, $limit "; 
		}
		
		$sql = $this->wpdb->prepare($sql,$escape , ARRAY_A); 
 
		$buttons = $this->wpdb->get_results($sql, ARRAY_A);
		return $buttons;
		
	}
	
	function getButtonCount($args = array())
	{
		$defaults = array(
			"status" => "publish", 
 
		);
		$args = wp_parse_args($args, $defaults); 
		
		$sql = "SELECT count(id) FROM " . maxButtonsUtils::get_buttons_table_name() . " WHERE status = '%s'"; 
		$sql = $this->wpdb->prepare($sql, $args["status"] ); 
		$result = $this->wpdb->get_var($sql);
		return $result;
		
	}
	
	function getButtonPages($args = array())
	{
		$defaults = array(
			"limit" => 20, 
			"paged" => 1, 
			"status" => "publish", 
			"output" => "list", 			// not used, future arg. 
			"view" => "all",

		);

		$args = wp_parse_args($args, $defaults); 

		$limit = intval($args["limit"]); 
		$page = intval($args["paged"]); 
		$view = $args["view"];

		$total = $this->getButtonCount(array("status" => $args["status"])); 
		
		$num_pages = ceil($total / $limit); 
		
		$output = ''; 
		$url = $_SERVER['REQUEST_URI'];

		$url = remove_query_arg("view", $url); 
		$url = add_query_arg("view", $view, $url);

		$first_url = ($page != 1 ) ? add_query_arg("paged", 1, $url) : false;
		$last_url = ($page != $num_pages) ? add_query_arg("paged", $num_pages, $url) : false;
		$next_url = ($page != $num_pages) ? add_query_arg("paged", ($page + 1), $url) : false;
		$next_page = ($page != $num_pages) ? ($page + 1) : false;
		$prev_page = ($page != 1)  ? ($page -1 ) : false; 
		$prev_url = ($page != 1 ) ? add_query_arg("paged", ($page -1), $url) : false;
		

		$return = array(
			"first" => 1, 
			"base" => remove_query_arg("paged",$url), 
			"first_url" => esc_url($first_url),
			"last"  => $num_pages,
			"last_url" =>  esc_url($last_url),
			"next_url" => esc_url($next_url), 
			"prev_url" => esc_url($prev_url),
			"prev_page" => $prev_page, 
			"next_page" => $next_page, 
			"total" => $total, 
			"current" => $page, 
			
			
			
		);
		
		return $return;
	}
	
	static function getAjaxButtons()
	{
		
		$admin = self::getInstance();
		$args = array(); 

		$paged = (isset($_REQUEST["paged"])) ? intval($_REQUEST["paged"]) : 1; 
		if ($paged > 0) 
			$args["paged" ] = $paged;

		
		$button = new MaxButton(); 
		$buttons = $admin->getButtons($args);
	
	
		$nav = $admin->getButtonPages($args); 
		$prev = ''; $next = ''; 
		
		if ($nav["prev_page"])
		{
			$prev = " <span class='prev' data-page='" . $nav["prev_page"] . "'> << </span>  "; 
		}		
		if ($nav["next_page"])		
		{
			$next = "<span class='next' data-page='" . $nav["next_page"] . "'> >> </span> ";
		}
 

		echo "<div class='pagination'>$prev $next
				</div>";
		echo "<div id='maxbuttons'><div class='preview-buttons'>";
		foreach($buttons as $b)
		{
			
			$button_id = $b["id"]; 
			$button->set($button_id);
			echo "<div class='button-row'>"; 
			echo "<span class='col col_insert'> "; 
			echo "	<a href='#' onclick='insertButtonShortcode($button_id); return false;'>";
			 _e('Insert This Button', 'maxbuttons'); 
			 echo "</a> &raquo;
			 	<br> <span class='small'>[ID: $button_id ]</span>
			 </span>  ";
			 
			echo "<span class='col col_button'><div class='shortcode-container'>";
			 $button->display(array("mode" => "preview", "load_css" => "inline" ));
			echo "</div></span>"; 
			echo "<span class='col col_name'>" . $button->getName() . "</span>";
			echo "</div>";  
		}
		echo "</div></div>"; 
		echo "<div class='pagination'>$prev $next
				</div>";		
		exit(); 
		
	}

	function get_header()
	{
		include_once(MB()->get_plugin_path() . "includes/admin_header.php"); 
	
	}
	
	function get_footer()
	{
		include_once(MB()->get_plugin_path() . "includes/admin_footer.php"); 
	
	}

}
