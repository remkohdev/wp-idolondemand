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

function send_multipart_post_message_with_json_via_file_get_contents($sync_or_async, $json1){

	$url = "https://api.idolondemand.com/1/api/".$sync_or_async."/addtotextindex/v1";

	$index1 = wp_idolondemand_get_index();
	$apikey = wp_idolondemand_get_setting('apikey');

	$eol = "\r\n";
	$data = '';

	$mime_boundary=md5(time());

	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="apikey"' . $eol . $eol;
	$data .= $apikey . $eol;
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="index"' . $eol . $eol;
	$data .= $index1 . $eol;
	$data .= '--' . $mime_boundary . $eol;
	$data .= 'Content-Disposition: form-data; name="json"; filename="allposts.json"' . $eol;
	$data .= 'Content-Type: application/json' . $eol;

	//$data .= 'Content-Transfer-Encoding: base64' . $eol . $eol;
	//$data .= chunk_split(base64_encode($json1)) . $eol;
	$data .= 'Content-Transfer-Encoding: 8bit' . $eol . $eol;
	$data .= $json1 . $eol;

	$data .= "--" . $mime_boundary . "--" . $eol . $eol; // finish with two eol's!!

	//error_log($data, 3, "C:/dev/xampp/apache/logs/remkohde_idolondemand_data.txt");

	$params = array('http' => array(
			'method' => 'POST',
			'header' => 'Content-Type: multipart/form-data; boundary=' . $mime_boundary . $eol,
			'content' => $data
			//, 'proxy' => 'tcp://localhost:8888' // for Charles http traffic
	));
	$ctx = stream_context_create($params);
	$response = file_get_contents($url, FILE_TEXT, $ctx);

	//error_log($response, 3, "C:/dev/xampp/apache/logs/remkohde_idolondemand_response.txt");

	return $response;
}


function send_multipart_post_message($apiname, $sync_or_async, $params){

	$url = "https://api.idolondemand.com/1/api/".$sync_or_async."/".$apiname."/v1";

	$apikey = wp_idolondemand_get_setting('apikey');
	$params['apikey'] = $apikey;
	
	$eol = "\r\n";
	$data = '';

	$mime_boundary=md5(time());

	$keys = array_keys($params);
	$max = sizeof($keys);
	
	for($i=0; $i<$max; $i++){
		$key = $keys[$i];
		$value = $params[$key];
		
		$data .= '--' . $mime_boundary . $eol;
		$data .= 'Content-Disposition: form-data; name="'.$key.'"' . $eol . $eol;
		$data .= $value . $eol;
	}	
	$data .= "--" . $mime_boundary . "--" . $eol . $eol; // finish with two eol's!!
	
	$params = array('http' => array(
			'method' => 'POST',
			'header' => 'Content-Type: multipart/form-data; boundary=' . $mime_boundary . $eol,
			'content' => $data
			//, 'proxy' => 'tcp://localhost:8888' // for Charles http traffic
	));
	$ctx = stream_context_create($params);
	
	$response = file_get_contents($url, FILE_TEXT, $ctx);

	error_log($response, 3, "C:/dev/xampp/apache/logs/remkohde_idolondemand_response.txt");

	return $response;
}

?>