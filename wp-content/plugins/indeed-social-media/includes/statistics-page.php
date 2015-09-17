<script>
jQuery(function() {
	jQuery( "#ism_custom_date_statistics_from" ).datepicker( {dateFormat: 'yy-mm-dd'});
	jQuery( "#ism_custom_date_statistics_until" ).datepicker( {dateFormat: 'yy-mm-dd'});
});
</script>
<?php
	$the_time = 'last_month';
	$sm_type = 'all';
	$the_limit = 10;
	$time_arr = array(  'today' => 'Today',
						'yesterday' => 'Yesterday',
						'last_week' => 'Last Week',
						'last_month' => 'Last Month'
					 );
	if(isset($_REQUEST['ism_time_statistics']))	$the_time = $_REQUEST['ism_time_statistics'];
	if(isset($_REQUEST['sm_type']) && $_REQUEST['sm_type']) $sm_type = $_REQUEST['sm_type'];
	if(isset($_REQUEST['limit']) && $_REQUEST['limit']!='') $the_limit = $_REQUEST['limit'];
	
	global $ism_list;
	$ism_list = ism_return_general_labels_sm();

?>
<form method="post" id="ism_form" action="">
<div class="stats-top">
    <div class="stats-top-center">
	Statistics Page provides the most important stats about Social Share on your pages. You can check each Social Network based on certain period of time and the Total of them.
Also, a list of the most share pages are showing up.
	</div>
	<div class="stats-top-left">
		<div style="display:inline-block; margin-right:10px; font-weight:bold;">Select The Interval:</div>
		<select name="ism_time_statistics" onChange="jQuery('#ism_custom_date_statistics_from').val('');jQuery('#ism_custom_date_statistics_until').val('');jQuery('#ism_form').submit();" style="min-width:200px;">
			<?php 
				foreach($time_arr as $k=>$v){
					$selected = '';
					if($the_time==$k) $selected='selected="selected"';
					?>
						<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
					<?php 	
				}
				?>
		</select>					
	</div>

	<div class="stats-top-right">
		<div style="display:inline-block; margin-right:10px; font-weight:bold;">Date Range: </div>
		<div style="display:inline-block;">
			<span class="ism_small_desc_span"></span> <input type="text" value="<?php if(isset($_REQUEST['ism_statistics_from'])) echo $_REQUEST['ism_statistics_from'];?>" name="ism_statistics_from" id="ism_custom_date_statistics_from" style="width:120px; height:30px;"/> 
			<span class="ism_small_desc_span"> - </span> <input type="text" value="<?php if(isset($_REQUEST['ism_statistics_until'])) echo $_REQUEST['ism_statistics_until'];?>" name="ism_statistics_until" id="ism_custom_date_statistics_until" style="width:120px; height:30px;"/>
		</div>
		<div style="display:inline-block; cursor:pointer; color: #333; vertical-align: top; margin-left:5px;" onClick="jQuery('#ism_form').submit();" title="Refresh">
			<!--i class="icon-refresh"></i-->
			<button class="button button-primary button-large" value="Apply">Apply</button>
		</div>
	</div>
	<div class="clear"></div>
	</div>	
	
	<?php
	
	$statistic_arr = ism_statistics_arr_time_return($the_time);
	/**************************** SECTION 1 **************************/
	$totals = ism_statistics_return_counts( $statistic_arr['min_date'], $statistic_arr['max_date'] );
	if(isset($totals) && count($totals)>0){
		echo '<div class="stats_gen">';
		foreach($totals as $key=>$value){
			echo '<div class="ism_item ism_box_'.$key.'">
					<i class="fa-ism fa-'.$key.'-ism"></i>
					<span class="ism_share_label">'.$key.'</span>
					<span class="ism_share_counts '.$key.'_share_count">'.ism_statistics_format_number($value).'</span>
					<div class="clear"></div>
				   </div>';
		}
	echo '</div>';	
	}
	/**************************** end of SECTION 1 **************************/	
	?>		
					

			
			<div class="ism_flot_wrapper">
				<div class="ism_flot_title">
					<h3 style="float:left;">Total Number Of Shares</h3>
					<div style="float:right;">
					<div style="display:inline-block; font-weight:bold;">Social Network:</div>
					<select name="sm_type" onChange="jQuery('#ism_form').submit();" style="min-width:160px; margin-left:10px;">
						<option value='all'>All</option>
						<?php 
							global $ism_list;
							foreach($ism_list as $k=>$v){
								$selected = '';
								if($sm_type==$k) $selected='selected="selected"';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
								<?php 	
							}
						?>
					</select>						
				</div>
				<div class="clear"></div>
				</div>
				<div class="ism_flot_content">
					<div id="flot-totals" class="flot medium" style="width: 100%;height: 200px;"></div>
				</div>
			</div>
	
<?php 	

	/**************************** SECTION 2 **************************/						
	if($statistic_arr['division']=='day'){
		#DAYS
		$return_arr = ism_statistics_return_counts_per_days($statistic_arr['ism_time_arr'], $sm_type);
	}else{
		#HOURS	
		$return_arr = ism_statistics_return_counts_per_hours($statistic_arr['max_time'], $statistic_arr['time'], $sm_type);	
	}
	$js_string_time = ism_statistics_time_data_for_js($return_arr);//date for js
		
	/**************************** end of SECTION 2 **************************/
	
		
	/**************************** SECTION 3 *************************/
	$data = ism_statistics_share_per_url($statistic_arr['min_date'], $statistic_arr['max_date'], $the_limit);
	?>
					<div class="stats-url-list">
					<h3 style="float:left;">Top Shared Pages</h3>
					<div style="float:right;">
					<div style="display:inline-block; font-weight:bold">Show up to: </div>
					<select name="limit" onChange="jQuery('#ism_form').submit();" style="min-width:100px; margin-left:10px;">
						<?php 
							$limits = array(10, 50, 100);
							foreach($limits as $limit){
								$selected = '';
								if( $limit==$the_limit ) $selected = 'selected="selected"';
								?>
									<option value="<?php echo $limit;?>" <?php echo $selected;?> ><?php echo $limit;?></option>
								<?php 
							}
						?>
					</select>
					</div>
					<div class="clear"></div>
	<table class="ism_table_statistics">
		<tr style="background-color: #fff;">
			<td class="stat-top-table" style="font-weight:bold; text-align:left;">Post URL</td>
			<td class="stat-top-table" style="font-weight:bold; color: rgb(28, 134, 188);font-size: 12px;">Total</td>
			<?php
				foreach($ism_list as $k=>$v){
					echo '<td class="stat-top-table"><i class="fa-ism fa-'.$k.'-ism"></i></td>';
				}
			?>
		</tr>
		<?php 
		foreach($data as $obj){
			echo '<tr>';
				echo '<td style=" text-align:left;">'.$obj->url.'</td>';
				echo '<td style=" text-align:middle;">'.$obj->count.'</td>';
				foreach($ism_list as $k=>$v){
					echo '<td>'.$obj->$k.'</td>';
				}
			echo '</tr>';
		}
		?>
	</table>
   </div>	
</form>	
	<?php 
	/**************************** end of SECTION 3 *************************/
		?>
<script>
if (jQuery("#flot-totals").length > 0) {
	jQuery.plot(jQuery("#flot-totals"), [ {
			label : "Share",
			data : [<?php echo $js_string_time;?>],
			color : "#3498db"
	} ], {
				xaxis : {
						min : <?php echo strtotime($statistic_arr['min_date']).'000'; ?>,
						max : <?php echo strtotime($statistic_arr['max_date']).'000';?>,
						mode : "time",
						tickSize : [ 1, "<?php echo $statistic_arr['division'];?>" ],
                        tickLength: 0
					},
					yaxis : {
						min: 0,
                		tickColor: "#efefef"
					},
					series : {
						bars: {
        					show: true
    					},
						points: {
							show: true,
							radius: 0
						},
						lines : {
							//show : true,
							//fill : true
						},
					},
					bars: {
    					align: "center",
    					lineWidth: 40,
					},
					grid : {
						hoverable : true,
						clickable : false,
                		borderWidth: false,
                		borderColor: "#633200",
					},
					legend : {
						show : false
					}
	});

	/* tool tip */
	jQuery("<div id='ism_tooltip'></div>").css({
		position: "absolute",
		display: "none",
		border: "1px solid #fdd",
		padding: "2px 8px",
		'background-color': "#333",
		color: '#fff',
		opacity: 0.80
	}).appendTo("body");

	jQuery("#flot-totals").bind("plothover", function (event, pos, item) {
		if(item){
			var y = parseInt( item.datapoint[1].toFixed(2) );
			if(y==1) t = '1 Share';
			else t = y + ' Shares';
			jQuery("#ism_tooltip").html( t ).css({top: item.pageY+5, left: item.pageX+5}).fadeIn(200);
		} else {
			jQuery("#ism_tooltip").hide();
		}
	});
	/* end of tool tip */	
}
</script>