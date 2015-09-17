<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
require( plugin_dir_path ( __FILE__ ) . 'includes/functions.php' );
$meta_arr = ism_return_arr( 'wd' );
foreach($meta_arr as $k=>$v){
    delete_option($k);
}
unset($meta_arr);
$meta_arr = ism_return_arr( 'id' );
foreach($meta_arr as $k=>$v){
    delete_option($k);
}
unset($meta_arr);
$meta_arr = ism_return_arr( 'g_o' );
foreach($meta_arr as $k=>$v){
    delete_option($k);
}
delete_post_meta_by_key( 'ism_disable_wd' );
delete_post_meta_by_key( 'ism_disable_id' );

$arr = array('facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'stumbleupon', 'vk', 'reddit', 'print', 'email');
$arr2 = array('ism_sm_internal_counts_share', 'ism_special_count_all', 'ism_min_count');
foreach($arr as $v){
	$arr2[] = 'ism_special_count_' . $v;
}
foreach($arr2 as $val){
	if(get_option($val)!==FALSE) delete_option($val);
}
?>