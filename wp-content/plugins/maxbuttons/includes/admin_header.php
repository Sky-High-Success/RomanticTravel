<?php 

global $page_title; 
?>

<div id="maxbuttons">
	<div class="wrap">
		<div class="icon32">
			<a href="http://maxbuttons.com" target="_blank"><img src="<?php echo maxButtons::get_plugin_url() ?>images/mb-32.png" alt="MaxButtons" /></a>
		</div>
		
		<h2 class="title"><?php echo $page_title ?></h2>
		
		<div class="logo">
			<?php do_action("mb-display-logo"); ?> 

		</div>
		
		<div class="clear"></div>
		<div class="main">
			<?php do_action('mb-display-tabs'); ?> 
			
