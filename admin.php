<?php

function wp_idolondemand_apikey_exists(){
	
	$apikey = wp_idolondemand_get_setting('apikey');
	if($apikey){
		return true;
	}else{
		return false;
	}
}

function wp_idolondemand_get_setting($setting_key) {
	
	global $wpdb;
	$result = array();
	
	$tbl_wp_idolondemand_settings = $wpdb->prefix . "idolondemand_settings";	
	$query_get_setting = "SELECT tbl1.id, tbl1.key1, tbl1.value1
			  FROM $tbl_wp_idolondemand_settings AS tbl1
	 		  WHERE tbl1.key1 = %s";
	
	$sql = $wpdb->prepare($query_get_setting, $setting_key);
	
	// dont expect duplicate keys 	
	/**
	$wp_idolondemand_settings = $wpdb->get_results($sql);
	if($wp_idolondemand_settings){
	$nrOfSettings = sizeof($wp_idolondemand_settings);
	foreach ( $wp_idolondemand_settings as $wp_idolondemand_setting ){
	*/
	// only expect 1 row to ensure unique keys
	$wp_idolondemand_setting = $wpdb->get_row($sql);
	$nrOfRows = $wpdb->num_rows . ' rows found.';
	if($wp_idolondemand_setting && ($wp_idolondemand_setting->key1)==$setting_key){
		return $wp_idolondemand_setting->value1;
	}else{
		return false;//$sql.", ".$nrOfRows;
	}
}


function wp_idolondemand_save_apikey_to_db($new_iod_apikey) {
	
	global $wpdb;
	$tbl_wp_idolondemand_settings = $wpdb->prefix . "idolondemand_settings";
	
	$result = "";
	
	$setting_key = 'apikey';
	$setting = wp_idolondemand_get_setting($setting_key);
	if($setting){
		// update
		$result += "update";
		$data = array(
				'value1' => $new_iod_apikey
				//'created' => current_time('mysql', 1),
				//'user_id' => $current_user->ID
		);
		$where = array(
			'key1' => $setting_key
		);
		$wpdb->update($tbl_wp_idolondemand_settings, $data, $where);
		
	}else{
		// insert
		$result += "insert";
		$data = array(
				'id' => NULL,
				'key1' => $setting_key,
				'value1' => $new_iod_apikey
				//'created' => current_time('mysql', 1),
				//'user_id' => $current_user->ID
		);
		$wpdb->insert($tbl_wp_idolondemand_settings, $data);
		$new_row_id = $wpdb->insert_id;
		$result += "row_id_new_api_key: "+$new_row_id."<br>";
	}
	return $result;
}

function wp_idolondemand_admin_notice() {
	
    //print the message
    global $post;
    
    $notice = get_option('otp_notice');
    if(! empty($notice)){
	    foreach($notice as $pid => $m){
	    	if ($post && ($post->ID == $pid) ){
	            echo '<div id="message" class="notices"><p>Message: '.$m.'</p></div>';
	            unset($notice[$pid]);
	            update_option('otp_notice',$notice);
	            break;
	        }
	    }
    }
}



?>