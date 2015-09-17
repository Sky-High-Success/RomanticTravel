<?php 

function ism_statistics_return_counts_per_days($ism_time_arr, $sm_type){
	global $wpdb;
	$return_arr = array();
	foreach($ism_time_arr as $v){
		$query = "SELECT COUNT(id) as c";
		$query .= " FROM ".$wpdb->prefix."ism_share_counts WHERE ism_date>='$v 00:00:00' AND ism_date<='$v 23:59:59' ";
		if( $sm_type!='all' ) $query .= "AND sm='{$sm_type}' ";
		$data = $wpdb->get_results($query);
		if(isset($data[0]->c)) $return_arr[$v] = $data[0]->c;
		else $return_arr[$v] = 0;
	}
	return $return_arr;
}

function ism_statistics_return_counts_per_hours($max_time, $time, $sm_type){
	global $wpdb;
	$max_time_arr = explode(':', $max_time);
	if(isset($max_time_arr[0])){
		for($i=0;$i<=$max_time_arr[0];$i++){
			$i_num = $i;
			if($i_num<10) $i_num = '0'.$i_num;
			//loop through hours
				
			$min_time = $time . ' '.$i_num.':00:00';
			$max_time2 = $time;
			if((int)$i_num<(int)$max_time_arr[0]){
				$max_time2 .= ' ' . $i_num .':59:59';
			}else{
				$max_time2 .= ' ' . $max_time_arr[0] .':'.$max_time_arr[1].':'.$max_time_arr[2];
			}
				
			$query = "SELECT COUNT(id) as c";
			$query .= " FROM ".$wpdb->prefix."ism_share_counts WHERE ism_date>='$min_time' AND ism_date<='$max_time2' ";
			if( $sm_type!='all') $query .= "AND sm='{$sm_type}' ";
			
			$data = $wpdb->get_results( $query );
			$the_key = $time.' '.$i_num.':'.$max_time_arr[1].':'.$max_time_arr[2];
	
			if(isset($data[0]->c)){
				$return_arr[ $the_key ] = $data[0]->c;
			}else{
				$return_arr[ $the_key ] = 0;
			}
		}
	}
	return $return_arr;
}


function ism_statistics_return_counts($min_date, $max_date){
	global $wpdb;
	$return_arr = '';
	$data = $wpdb->get_results("SELECT COUNT(id) as c, sm FROM {$wpdb->prefix}ism_share_counts WHERE ism_date>='$min_date 00:00:00' AND ism_date<='$max_date 23:59:59' GROUP BY sm; ");
	if(isset($data)){
		$return_arr['total'] = 0;
		foreach($data as $item){
			if(isset($item->c) && isset($item->sm)){
				$return_arr['total'] = (int)$return_arr['total'] + (int)$item->c;
				$return_arr[$item->sm] = $item->c;				
			}
		}
	}
	return $return_arr;
}

function ism_statistics_share_per_url($min_date, $max_date, $limit){
	global $wpdb;
	global $ism_list;
	$min_date_q = date('Y-m-d', strtotime($min_date) );
	$max_date_q = date('Y-m-d', strtotime($max_date) );
	$query = "SELECT url, ";
	foreach($ism_list as $k=>$v){
		$query .= "SUM(IF(sm='$k', 1, 0)) as $k,";
	}
	$query .= "COUNT(id) as count";
	$query .= " FROM {$wpdb->prefix}ism_share_counts
					WHERE ism_date>='{$min_date_q} 00:00:00' AND ism_date<='{$max_date_q} 23:59:59'
					GROUP BY url
					ORDER BY count DESC
					LIMIT {$limit}; ";
	$data = $wpdb->get_results($query);	
	return $data;
}

function ism_statistics_format_number($num){
	if($num>999){
		if($num>999999){
			$num = $num/1000000;
			$type_num = 'M';
		}else{
			$num = $num/1000;
			$type_num = 'k';
		}
		if(strpos($num, '.')!==FALSE){
			$arr = explode('.', $num);
			if(isset($arr[1])){
				$d = substr($arr[1], 0, 1);
				$second_num = substr($arr[1], 1, 1);
				if($second_num>=5)$d = (int)$d+1;
				$num = $arr[0].'.'.$d;
			}
		}
		$num .= $type_num;
	}
	return $num;
}

function ism_statistics_time_data_for_js($return_arr){
	if(isset($return_arr) && count($return_arr)>0){
		foreach($return_arr as $k=>$v){
			$js_date_arr[] = '['.strtotime($k).'000, '.$v.']';
		}
		$js_string_time = implode(", \n", $js_date_arr);
	}
	return $js_string_time;
}

function ism_statistics_arr_time_return($the_time){
	$arr = array();
	
	switch($the_time){
		case 'today':
			$arr['min_date'] = date('Y-m-d', time());
		break;
		case 'yesterday':
			$arr['min_date'] = date('Y-m-d', time()-(24 * 60 * 60));
		break;
		case 'last_week':
			$arr['min_date'] = date('Y-m-d', time()-(7 * 24 * 60 * 60));
		break;
		case 'last_month':
			$arr['min_date'] = date('Y-m-d', time()-(30 * 24 * 60 * 60));
		break;
	}
	
	if(isset($_REQUEST['ism_statistics_from']) && isset($_REQUEST['ism_statistics_until']) && $_REQUEST['ism_statistics_from']!='' && $_REQUEST['ism_statistics_until']!=''){
		//CUSTOM DATE INTERVAL
		$min_date = strtotime($_REQUEST['ism_statistics_from']);
		$max_date = strtotime($_REQUEST['ism_statistics_until']);
		$arr['min_date'] = $_REQUEST['ism_statistics_from'];
		$arr['max_date'] = $_REQUEST['ism_statistics_until'];
		
		if($min_date<$max_date){
			#days
			$arr['ism_time_arr'][] = $_REQUEST['ism_statistics_from'];				
			$current_date = $min_date;
			while($current_date<$max_date){
				$current_date = $current_date + 24*60*60;
				if($current_date>$max_date) $current_date = $current_date;
				else{
					$arr['ism_time_arr'][] = date('Y-m-d', $current_date);
				}
			}
			$arr['ism_time_arr'][] = $_REQUEST['ism_statistics_until'];
			$arr['division'] = 'day';
		}else{
			#one day, 24 hours
			$arr['max_date'] = $arr['max_date']  . ' 23:59:59';
			$arr['time'] = $_REQUEST['ism_statistics_from'];
			$arr['max_time'] = '23:59:59';
			$arr['min_date'] = $arr['min_date'] . ' 00:00:00';
			$arr['division'] = 'hour';
		}
	}else{
		//predifined date interval
		switch($the_time){
			case 'today':
				$arr['time'] = date('Y-m-d', time());
				$arr['max_time'] = '23:59:59';
				$arr['max_date'] = $arr['time'] .' '. $arr['max_time'];
				$arr['min_date'] = $arr['time'] .' 00:00:00';
				$arr['division'] = 'hour';
			break;
			case 'yesterday':
				$arr['time'] = date('Y-m-d', time()-(24 * 60 * 60));
				$arr['max_time'] = '23:59:59';
				$arr['max_date'] = $arr['time'] .' '. $arr['max_time'];
				$arr['division'] = 'hour';
			break;
			case 'last_week':
				$arr['ism_time_arr'][] = date('Y-m-d', time());
				for($i=1;$i<=6;$i++){
					$arr['ism_time_arr'][] = date('Y-m-d', time()-($i * 24 * 60 * 60));
				}
				$arr['min_date'] = $arr['ism_time_arr'][6].' 00:00:00';
				$arr['max_date'] = $arr['ism_time_arr'][0].' 23:59:59';
				$arr['division'] = 'day';
			break;
			case 'last_month':
				$arr['ism_time_arr'][] = date('Y-m-d', time());
				for($i=1;$i<=29;$i++){
					$arr['ism_time_arr'][] = date('Y-m-d', time()-($i * 24 * 60 * 60));
				}
				$arr['min_date'] = $arr['ism_time_arr'][29].' 00:00:00';
				$arr['max_date'] = $arr['ism_time_arr'][0].' 23:59:59';
				$arr['division'] = 'day';
			break;
		}
	}
	return $arr;
}

?>