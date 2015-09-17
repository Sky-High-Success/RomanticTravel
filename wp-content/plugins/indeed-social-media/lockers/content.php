<?php
function GetLockerContent($type, $ism, $attr_val){
	$content = '';
			    
	$lock_msg = htmlspecialchars_decode($attr_val['sm_d_text']);
	
	//$style = "display: inline-block;";
	switch($type){
		case 1:
		{
		//Default
		$style = 'max-width:640px; margin:auto; ';
		if(isset($attr_val['sm_lock_padding']) && $attr_val['sm_lock_padding']!='') $style .= "padding: " . $attr_val['sm_lock_padding'] . "px;";
		if(isset($attr_val['sm_lock_bk']) && $attr_val['sm_lock_bk']!='') $style .= "background-color: " . $attr_val['sm_lock_bk'] . ";";
		
		$content = "
				<div style='$style' class='ism_locker_1'>
				   <div>".
					 $lock_msg
				  ."</div>". 
				   $ism;
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 2:{
		$style = '';
		
		$content = "
				<div style='$style' class='ism_locker_2'>
				   <div class='lock_content'>".
					 $lock_msg
				  ."</div>
				   <div class='lock_buttons'>". 
				      $ism
				   ."</div>";
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 3:{
		$style = ' ';
		
		$content = "
				<div style='$style' class='ism_locker_3'>
				<div  class='lk_wrapper'></div>
				   <div class='lock_content'>".
					 $lock_msg
				  ."</div>
				   <div class='lock_buttons'>". 
				      $ism
				   ."</div>";
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 4:{
		$style = '';
		
		$content = "
				<div style='$style' class='ism_locker_4'>
				<div  class='lk_wrapper'></div>
				   <div class='lk_left_side'></div>
				   <div class='lock_content'>".
					 $lock_msg
				  ."  <div class='lock_buttons'>". 
				      $ism
				   ."</div>
				   </div>";				 
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 5:{
		$style = '';
		
		$content = "
				<div style='$style' class='ism_locker_5'>
				  <div class='lk_top_side'></div>
				   <div class='lock_content'>".
					 $lock_msg
				  ."  <div class='lock_buttons'>". 
				      $ism
				   ."</div>
				   </div>";				 
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 6:{
		$style = '';
		
		$content = "
				<div style='$style' class='ism_locker_6'>
				   <div class='lk_top_side'></div>
				   <div class='lock_content'>".
					 $lock_msg
				  ."  <div class='lock_buttons'>". 
				      $ism
				   ."</div>
				   </div>";				 
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 7:{
		$style = '';
		
		$content = "
				<div style='$style' class='ism_locker_7'>
				  <div class='lk_wrapper'></div>
				  <div class='lk_top_side'></div>
				   <div class='lock_content'>".
					 $lock_msg
				  ."  <div class='lock_buttons'>". 
				      $ism
				   ."</div>
				   </div>";				 
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		case 8:{
		$style = ' ';
		
		$content = "
				<div style='$style' class='ism_locker_8'>
				<div class='lk_top_side'></div>
				<div  class='lk_wrapper_top'></div>
				<div  class='lk_wrapper_bottom'></div>
				   <div class='lock_content'>".
					 $lock_msg
				  ."</div>
				   <div class='lock_buttons'>". 
				      $ism
				   ."</div>";
			if(isset($attr_val['enable_timeout_lk']) && $attr_val['enable_timeout_lk']==1) $content .= "<div class='ism_timeout_wrapper'><div id='line_{$attr_val['locker_div_id']}' class='ism_timeout_line'></div></div>";
			$content .= "</div>";
			break;
		}
		default:
			$content = '';
	}
	
	return $content;
}
?>