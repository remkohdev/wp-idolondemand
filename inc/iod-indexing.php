<?php

function wp_idolondemand_get_index(){
	
	$index = get_option("wp_idolondemand_use_this_index");
	return $index;
	
}

function wp_idolondemand_get_add_to_text_index_input_type(){
	
	$add_to_text_index_input_type = get_option("wp_idolondemand_add_to_text_index_input_type");
	
	return $add_to_text_index_input_type;
	
}

function wp_idolondemand_get_add_to_text_index_post_sections(){
	
	$add_to_text_index_post_sections = get_option("wp_idolondemand_add_to_text_index_post_sections");
	
	return $add_to_text_index_post_sections;
	
}

/**
 * index post on create and update
 */
function wp_idolondemand_save_post()
{
	// TODO not sure this is the way to do this, too error prone imo, to investigate
	$whitelist = array(
			'127.0.0.1',
			'::1'
	);
	$input_type = wp_idolondemand_get_add_to_text_index_input_type();
	
	if(! in_array($_SERVER['REMOTE_ADDR'], $whitelist) || $input_type=='json'){
		
		// TODO if status changes to un-publish remove from index
		global $post;
		if($post){
			
			$post_id = $post->ID;
			$post_ids = array($post_id);
			$response = wp_idolondemand_add_to_text_index($post_ids, "sync");
			 
			$notice = get_option('otp_notice');
			$notice[$post_id] = $response;
			update_option('otp_notice',$notice);
			
			return $response;
		}
		
	}else{
		
		// TODO on localhost cannot index by url
		// either override to url or throw errror message
		echo "On localhost you cannot index by url. Change index input type to json.";
	}
}


function wp_idolondemand_add_to_text_index_all_posts() {

	//TODO posts_per_page should be -1, currently only 20 works
	$args = array(
			'post_type'        =>  array( 'post','page'),
			'post_status'      => 'publish',
			'posts_per_page'   => 20 // TODO -1 = all
	);
	$posts = get_posts($args);

	$post_ids = array();
	foreach ( $posts as $post ) {
		array_push($post_ids, $post->ID);
	}
	$result = wp_idolondemand_add_to_text_index($post_ids, "async");
}


function wp_idolondemand_add_to_text_index($post_ids, $sync_or_async){
	
	// TODO onsave un-index when status changes
	$response = "";
	$msg = "";
	
	$input_type = wp_idolondemand_get_add_to_text_index_input_type();
	if($input_type=='json'){
		
		// use POST for 'json'
		$index1 = wp_idolondemand_get_index();
		$apikey = wp_idolondemand_get_setting('apikey');
		$json1 = wp_idolondemand_get_posts_as_json($post_ids);
		
		$response = send_multipart_post_message_with_json_via_file_get_contents($sync_or_async, $json1);
		
	} else {	
		// currently only json is allowed	
		// use GET for url
		// if(input-type != 'json') { default=url; }
		$addtoindexUrl = wp_idolondemand_get_add_to_text_index_get_url();
		$response = file_get_contents($addtoindexUrl);
	}
	
	// response processing
	if ($response) {
		$msg .= process_response($response, $sync_or_async);
	}else{
		// TODO proper error handling 
		$msg .= "Error: No response. HTTP statuscode: ";		
	}
	
	if($msg){
		if($response){
			echo "response: ".$response."<br><br>";
		}
		echo "message: ".$msg;
	}
}

function process_response($response, $sync_or_async){
	$msg = "";
	$json = json_decode($response);
	
	if($json){
		if( $sync_or_async=="sync"){
	
			if(isset($json->references)){
					
				$references = $json->references;
				foreach($references as $references_item) {
	
					$post_id = $references_item->reference;
					$key1 = "wp_idolondemand_post_index_datetime";
					$value1 = date('Y-m-d G:i:s');
					$result = wp_idolondemand_save_reference_to_post_metadata($post_id, $key1, $value1);
				}
			}else{
				// error
				// TODO proper error handling
				if(isset($json->error)){
	
					var_dump($json1);
					$error = $json->error;
					$reason = $json->reason;
					$actions = $json->actions;
					foreach($actions as $action){
						$errors = $action->errors;
						foreach($errors as $error1){
							$error2 = $error1->error;
							$reason2 = $error1->reason;
							$detail2 = $error1->detail;
						}
						$status1 = $action->status;
						$action1 = $action->action;
						$version = $action->version;
					}
					$status = $json->status;
					$msg = "Error: ". $error;
				}else{
					$msg .= "Error: json.error not set.<br><br>";
				}
				$msg .= "Error: json.references not set.<br><br>";
			}
			
	
		}elseif($sync_or_async=="async"){
			
			if(isset($json->jobID)){
				// TODO
				// save jobIDs to post_metadata table, remove if indexing was fully complete and successful
				// Note: what happens if indexing returns partial result? is this an option or will
				// a rollback take place?
				$jobId = $json->jobID;
				$key1 = "wp_idolondemand_job_id";
				$value1 = $jobId;
				$result = wp_idolondemand_save_log($key1, $value1,'');
				$msg = "";
			}else{
				// error
				// TODO display message probably
				$msg .= "Error: JobID is not set.<br><br>".$response."<br><br>";
			}
		}else{
			// error
			// TODO display message probably
			$msg .= "Error: Unknown HTTP method.<br><br>";
		}
	}
	return $msg;
}

function send_x_www_form_post_message_with_json_via_curl($sync_or_async, $json1){
	
	$url = "https://api.idolondemand.com/1/api/".$sync_or_async."/addtotextindex/v1";
	
	$index1 = wp_idolondemand_get_index();
	$apikey = wp_idolondemand_get_setting('apikey');
	
	$ch = curl_init($url);
	
	// to capture http traffic in e.g. Charles http traffic
	//curl_setopt($ch, CURLOPT_PROXY, 'localhost:8888');
	
	// required to avoid SSLHandShake failure
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// required for multipart/form-data POST request
	curl_setopt($ch, CURLOPT_POST, true);
	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	$postFields = array(
		'apikey' => $apikey,
		'index' => $index1,
		'json' => $json1
	);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$response = curl_exec($ch);
	return $response;
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

function wp_idolondemand_list_indexes(){
	$indexes = null;
	$apikey = wp_idolondemand_get_setting('apikey');
	
	// TODO
	// error handle HTTP error codes 
	// Warning: file_get_contents(url): 
	// failed to open stream: HTTP request failed! HTTP/1.1 504 Gateway Time-out in 
	$response = file_get_contents('https://api.idolondemand.com/1/api/sync/listindexes/v1?flavor=standard&apikey='.$apikey);
	if($response) { // error condition
		
		$json = json_decode($response);
		if($json){
			$indexes = $json->index;
		}
		return $indexes;
		
	}else{
		echo "<p>Could not gather data!!!</p>";
		$error = "Error: ".$response;
		return $error;
	}
}


function wp_idolondemand_create_text_index($new_index_name){

	$indexes = null;
	$apikey = wp_idolondemand_get_setting('apikey');

	// TODO
	// error handle HTTP error codes
	// Warning: file_get_contents(url):
	// failed to open stream: HTTP request failed! HTTP/1.1 504 Gateway Time-out in
	$new_index_flavor = "standard";
	$blog_title = get_bloginfo('name');
	$new_index_description = urlencode("wp-idolondemand create new index for ".$blog_title);
	
	$apikey = wp_idolondemand_get_setting('apikey');
	$create_index_url = sprintf("https://api.idolondemand.com/1/api/sync/createtextindex/v1?index=%s&flavor=%s&description=%s&apikey=%s",
			$new_index_name, $new_index_flavor, $new_index_description, $apikey);
	
	$response = file_get_contents($create_index_url);
	
	if ($response === false) { // error condition

		echo "<p>Could not gather data!!!</p>";
		$error = "Error: ".$response;
		return $error;

	}else{

		$json = json_decode($response);
		if($json){
			$indexes = $json->index;
		}
		return $indexes;

	}
}

function wp_idolondemand_get_add_to_text_index_get_url(){
	global $post;
	
	$post_id = $post->ID;
	$post_url = get_permalink($post_id);
	$index1 = wp_idolondemand_get_index();
	$apikey = wp_idolondemand_get_setting('apikey');
	
	$addtoindexUrl = sprintf("https://api.idolondemand.com/1/api/sync/addtotextindex/v1?url=%s&index=%s&apikey=%s",
			$post_url, $index1, $apikey);
	
	return $addtoindexUrl;
}

function wp_idolondemand_get_posts_as_json($post_ids){
	
	$json1pre = "{\"document\":[";
	$json1 = "";
	$json1post = "]}";
	$requestMaxLength = 10240;
	$requestMetadataLength = 4566;
	$json1TotalLength = $requestMetadataLength + strlen($json1pre.$json1post);
	
	foreach($post_ids as $post_id){
	
		$post = get_post($post_id);
		
		$post_title = $post->post_title;
		$post_title = wp_idolondemand_get_value_as_json($post_title);
		
		$post_reference = $post_id;
		$post_reference = wp_idolondemand_get_value_as_json($post_reference);
		
		$post_content = $post->post_content;
		$post_content = wp_idolondemand_get_value_as_json($post_content);
		
		$post_sections = wp_idolondemand_get_add_to_text_index_post_sections();
		$json2 = '{'.
				'"title":"'. $post_title .'",'.
				'"reference":"'. $post_reference .'",'.
				'"content":"'. $post_content .'",';
		
		if($post_sections){
			if($post_sections['tags']){
				$post_tags = wp_get_post_tags($post_id);
				$max = sizeof($post_tags)-1;
				$post_tags_string = "";
				for ($i=0; $i<=$max; $i++) {
					$post_tags_string .= $post_tags[$i]->name .",";
				}
				$post_tags_string = rtrim($post_tags_string, ",");
				$post_tags_string = wp_idolondemand_get_value_as_json($post_tags_string);
				$json2 .= '"tags":["'. $post_tags_string .'"],';
			}
			if($post_sections['categories']){
				$post_category_ids = wp_get_post_categories($post_id);
				$post_categories_string = "";
				foreach($post_category_ids as $post_category_id){
					$post_category = get_category( $post_category_id );
					if($post_category){
						$post_categories_string .= $post_category->name .",";
					}
				}
				$post_categories_string = rtrim($post_categories_string, ",");
				$post_categories_string = wp_idolondemand_get_value_as_json($post_categories_string);
				$json2 .= '"categories":["'. $post_categories_string .'"],';
			}
		}
		$json2 = rtrim($json2, ",");
		$json2 .= "},";
		
		$json1 .= $json2;

	}
	$json1 = rtrim($json1,",");
	$json1 = $json1pre.$json1.$json1post;
	return $json1;
}

function wp_idolondemand_get_value_as_json($value){
	$value = strip_tags($value);
	$value = json_encode($value);
	$value = ltrim($value,'"');
	$value = rtrim($value,'"');
	// shouldnt happen, but there was a post with ending backlash breaking json
	$value = rtrim($value,'\\');
	return $value;
}

function wp_idolondemand_save_reference_to_post_metadata($post_id, $key1, $value1) {
	
	global $wpdb;
	$tbl_wp_idolondemand_post_metadata = $wpdb->prefix . "idolondemand_post_metadata";
	
	$result = $wpdb->replace(
		$tbl_wp_idolondemand_post_metadata,
		array(
			'post_id' => $post_id,
			'key1' => $key1,
			'value1' => $value1
		)
	);
	if($result){
		$result_msg = "Command 'replace' for [".$post_id."][".$key1."][".$value1."] was successful. ".
			 "Number of rows affected: ". $result;
	}else{
		// TODO error handle
	}
	return $result;
}

function wp_idolondemand_get_logs($key1) {

	$result = null;

	global $wpdb;
	$tbl_wp_idolondemand_log = $wpdb->prefix . "idolondemand_log";

	$post_index_logs = $wpdb->get_results(
			" SELECT * FROM ". $tbl_wp_idolondemand_log .
			" WHERE key1 = '".$key1."'");

	if($post_index_logs){
		$result = array();
		foreach($post_index_logs as $post_index_log){
			if($post_index_log->value1){
				$jobId = $post_index_log->value1;
				$datetime1 = $post_index_log->datetime1;
				$status1 = $post_index_log->status;
				$log = array('jobId'=>$jobId, 'datetime1'=>$datetime1, 'status'=>$status1);
				array_push($result, $log);
			}
		}
	}else{
		// Not Found
		// TODO
	}

	return $result;
}

function wp_idolondemand_save_log($key1, $value1, $status) {

	global $wpdb;
	$tbl_wp_idolondemand_log = $wpdb->prefix . "idolondemand_log";

	$result = $wpdb->replace(
			$tbl_wp_idolondemand_log,
			array(
					'key1' => $key1,
					'value1' => $value1,
					'datetime1' => date('Y-m-d G:i:s'),
					'status' => $status
			)
	);
	if($result){
		$result_msg = "Command 'replace' for [".$key1."][".$value1."] was successful, status[".$status."]".
			 "Number of rows affected: ". $result;
	}else{
		// TODO error handle
	}
	return $result;
}

function wp_idolondemand_get_post_index_metadata($post_id, $key1) {
	
	$result = null;
	
	global $wpdb;
	$tbl_wp_idolondemand_post_metadata = $wpdb->prefix . "idolondemand_post_metadata";
	
	$post_index_metadata = $wpdb->get_row(
			" SELECT * FROM ". $tbl_wp_idolondemand_post_metadata .
			" WHERE post_id = ".$post_id.
			" AND key1 = '".$key1."'");
	if($post_index_metadata){
		$result = $post_index_metadata->value1;
	}else{
		$result = false;
	}
	
	return $result;
}

function wp_idolondemand_check_job_status_jobid($jobids_to_check){
	$msg = "";
	foreach($jobids_to_check as $jobid){
		
		// check jobid status
		$jobstatusUrl = wp_idolondemand_get_jobstatus_get_url($jobid);
		$response = file_get_contents($jobstatusUrl);
		
		$jobstatus = "";
		// response processing
		if ($response) {
			
			// poll for jobid status
			$status = process_jobstatus_response($response);
			
			// save jobid status to log
			$key1 = "wp_idolondemand_job_id";
			$value1 = $jobid;
			$result = wp_idolondemand_save_log($key1, $value1, $status);
			
		}else{
			// TODO proper error handling
			$msg .= "Error: No response. HTTP statuscode: ";
		}
		
		$msg .= $jobid .'&status='.$jobstatus;
	}
	return $s;	
}

function wp_idolondemand_get_jobstatus_get_url($jobid){
	$apikey = wp_idolondemand_get_setting('apikey');
	$jobstatusUrl = sprintf("https://api.idolondemand.com/1/job/status/%s?apikey=%s",
			$jobid, $apikey);
	return $jobstatusUrl;
}

function process_jobstatus_response($response) {
	
	$status = "";
	$json = json_decode($response);
	if($json){
		if(isset($json->status)){
			$status = $json->status;
		}
	}
	
	return $status;
}

function wp_idolondemand_edit_post()
{
	// index post on publish and update
}

function wp_idolondemand_delete_post()
{
	// remove post from index on delete
}

?>