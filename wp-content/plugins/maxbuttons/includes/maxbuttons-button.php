<?php
include_once 'arrays.php';
//include_once 'constants.php';


$button = MB()->getClass("button"); //new maxButton();
$button_id = 0; 


if ($_POST) {
	 
	$button_id = intval($_POST["button_id"]); 

	if ($button_id > 0) 
		$button->set($button_id);
	$return = $button->save($_POST); 
	if (is_int($return) && $button_id <= 0) 
		$button_id = $return;
 
 	if ($button_id === 0) 
 	{
 		error_log(__("Maxbuttons Error: Button id should never be zero","maxbuttons")); 
 
 	}
 	
	$button->set($button_id);	
	 wp_redirect(admin_url('admin.php?page=maxbuttons-controller&action=button&id=' . $button_id));
	 exit();
}
	
if (isset($_GET['id']) && $_GET['id'] != '') { 
	$button = MB()->getClass('button'); // reset
	$button_id = intval($_GET["id"]); 
	if ($button_id == 0) 
	{
		$error = __("Maxbuttons button id is zero. Your data is not saved correctly! Please check your database.","maxbuttons");
		maxButtons::add_notice('error', $error); 
	}
		// returns bool
		$return = $button->set($button_id);
	if ($return === false)
	{
		$error = __("MaxButtons could not find this button in the database. It might not be possible to save this button! Please check your database or contact support! ", "maxbuttons");
		maxButtons::add_notice('error', $error); 
	}
}

 
?>
<div id="maxbuttons">
	<div class="wrap">
		<div class="icon32">
			<a href="http://maxbuttons.com" target="_blank"><img src="<?php echo maxButtons::get_plugin_url() ?>/images/mb-peach-icon.png" alt="MaxButtons" /></a>
		</div>
		
		<h2 class="title"><?php _e('MaxButtons: Add/Edit Button', 'maxbuttons') ?></h2>
		
		<div class="logo">
			<?php do_action("mb-display-logo"); ?> 
		</div>
		
		<div class="clear"></div>

			<?php do_action('mb-display-tabs'); ?> 
	
		<form id="new-button-form" action="<?php echo admin_url('admin.php?page=maxbuttons-controller&action=button&noheader=true'); ?>" method="post">
			<input type="hidden" name="button_id" value="<?php echo $button_id ?>"> 
			<?php wp_nonce_field("button-edit","maxbuttons_button") ?>
			
			<div class="form-actions">				
				<a class="button-primary button-save"><?php _e('Save', 'maxbuttons') ?></a>
				<a id="button-copy" class="button" href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=copy&id=<?php echo $button_id ?>"><?php _e('Copy', 'maxbuttons') ?></a>
				<a id="button-trash" class="button" href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=trash&id=<?php echo $button_id ?>"><?php _e('Move to Trash', 'maxbuttons') ?></a>
				<a  class="button" href="#delete-button" rel="leanModal"><?php _e("Delete","maxbuttons"); ?> </a>
			</div>
			
			<div class="max-modal" id="delete-button">
				<div class="modal_header">
					<?php _e("Removing button","maxbuttons"); ?>
					<div class="modal_close tb-close-icon"></div>
				</div>
					<p><?php _e("You are about to permanently remove this button. Are you sure?", "maxbuttons"); ?></p>
					<p><a href="<?php admin_url() ?>admin.php?page=maxbuttons-controller&action=delete&id=<?php echo $button_id ?>" type="button" name="" class="button-primary big"><?php _e("Yes","maxbuttons"); ?></a>
					&nbsp;&nbsp;
					<input type="button" name="#" onClick="javascript:$('.modal_close').click();" class="button-primary" value="<?Php _e("No", "maxbuttons"); ?>">
					</p>

			</div>
			
			<?php do_action("mb_display_notices"); ?> 
			
			<?php if ($button_id > 0): ?>
			<div class="mb-message shortcode">
				<?php $button_name = $button->getName(); ?>
				<?php _e('To use this button, place the following shortcode anywhere in your site content:', 'maxbuttons') ?>
				<strong>[maxbutton id="<?php echo $button_id ?>"]</strong> <?php _e("or","maxbuttons"); ?> <strong>  [maxbutton name="<?php echo $button_name; ?>"]  </strong> 
				<span class='shortcode-expand closed'><?php _e("See more examples","maxbuttons"); ?>
					<span class="dashicons-before dashicons-arrow-down"></span>
				</span> 
				
				<div class="expanded"> 
					<p class="example">
					<strong><?php _e("Add the same button with different link","maxbuttons");  ?></strong>
						&nbsp; [maxbutton id="<?php echo $button_id ?>" url="http://yoururl"]
					</p>
					 
					<p class="example"><strong><?php _e("Use the same button but change the text","maxbuttons"); ?> </strong>
						&nbsp; [maxbutton id="<?php echo $button_id ?>" text="yourtext"]
					</p>
					<p class="example"><strong><?php _e("All possible shortcode options","maxbuttons"); ?></strong>
						&nbsp; [maxbutton id="<?php echo $button_id ?>" text="yourtext" url="http://yoururl" window="new" nofollow="true"] 
					</p>
					
					<h4><?php _e("Some tips","maxbuttons"); ?></h4>
					<p><?php _e("If you use the button on a static page, on multiple pages, or upload your theme to another WordPress installation choose an unique name and use ", 
						"maxbuttons"); ?>  <strong>[maxbutton name='my-buy-button' url='http://yoururl']</strong>.

		 
					 <?php _e("Using this syntax if you change your button it will change throughout the entire site.  If you delete the button and create a new one with the same name the site will use the new button. ","maxbuttons"); ?>
				 	</p>
					
				</div>
			</div>
			<?php endif; ?>
			
			<?php #### STARTING FIELDS; 
			
				
			$button->admin_fields();
			
			?> 

			<div class="form-actions">				
				<a class="button-primary button-save"><?php _e('Save', 'maxbuttons') ?></a>
			</div>
		</form>

		<div class="output">
			<div class="header"><?php _e('Button Output', 'maxbuttons') ?></div>
			<div class="inner">
 
				<?php _e('The top is the normal button, the bottom one is the hover.', 'maxbuttons') ?>
				<div class="result">

					<?php $button->display(array("mode" => 'editor', "load_css" => "element"));  ?> 
 
					<p>&nbsp;</p>
 
					<?php $button->display(array("mode" => 'editor', "preview_part" => ":hover", "load_css" => "element")); ?> 
					
					<?php $button->display_field_map(); ?> 
				</div>
				
				<input type='hidden' id='colorpicker_current' value=''>
				
				<span class="colorpicker-box" id="button_preview_box">
					
					<span></span>
				</span>
				<input  type="hidden" id="button_preview" value='' />
				<input style="display: none;" type="text" id="button_output" name="button_output" value="" />
				<div class="note"><?php _e('Change this color to see your button on a different background.', 'maxbuttons') ?></div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
