<?php
function wp_idolondemand_save_apikey()
{
	if(!current_user_can( 'manage_options' )) {
		wp_die( 'You are not allowed to \'save_apikey\'.' );
	}
	// Check that nonce field
	check_admin_referer('wp-idolondemand_op_verify');

	$options = get_option( 'wp_idolondemand_op_array' );
	$plugin_version = $options['wp_idolondemand_op_version'];
	//update_option( 'wp_idolondemand_op_array', $options );

	//hidden action=wp_idolondemand_save_apikey
	//idolondemand_apikey=""
	if(isset($_POST['idolondemand-apikey'])){
		$iod_apikey = sanitize_text_field( $_POST['idolondemand-apikey'] );
		$result = wp_idolondemand_save_apikey_to_db($iod_apikey);
	}
	
	// redirect
	wp_redirect(admin_url( 'admin.php?page=wp-idolondemand/inc/menu.inc.php_idol_ondemand&m=1'));

	exit;
}

function wp_idolondemand_create_index()
{
	if(!current_user_can( 'manage_options' )) {
		wp_die( 'You are not allowed to \'create_index\'.' );
	}
	// Check that nonce field
	check_admin_referer('wp-idolondemand_op_verify');

	// process
	if(isset($_POST['wp-idolondemand-create-new-index-name'])){
		
		$new_index_name = sanitize_text_field( $_POST['wp-idolondemand-create-new-index-name'] );
		// TODO make options (flavor and description) available in form
		$response = wp_idolondemand_create_text_index($new_index_name);
		
	}else{
		// TODO 
		// form validation instead of blindly calling function
		// error handling
	}
	
	// TODO put messages in a central place, static constants or possibly in config db
	wp_redirect(admin_url( 'admin.php?page=wp-idolondemand/inc/menu.inc.php_indexing&m=2'));
	
	exit;
}

function wp_idolondemand_use_this_index() {

	if(!current_user_can( 'manage_options' )) {
		wp_die( 'You are not allowed to \'create_index\'.' );
	}
	// Check that nonce field
	check_admin_referer('wp-idolondemand_op_verify');
	
	// process
	//$index_name = wp_idolondemand_get_use_this_index();
	$index_name = wp_idolondemand_get_index();
	$use_this_index = $_POST['use_this_index'];
	
	if(isset($use_this_index) && !empty($use_this_index) ){
		
		if (is_array($use_this_index)) {
			foreach($use_this_index as $checkedindexname){
				
				$index_name = $checkedindexname;
				// TODO make options (flavor and description) available in form
				update_option( "wp_idolondemand_use_this_index", $index_name );
			
			}
		} else {
			
			update_option("wp_idolondemand_use_this_index", $use_this_index);
		}
		
	}else{
		delete_option( "wp_idolondemand_use_this_index");
		// TODO
		// form validation instead of blindly calling function
		// error handling
	}
	
	// TODO put messages in a central place, static constants or possibly in config db
	wp_redirect(admin_url( 'admin.php?page=wp-idolondemand/inc/menu.inc.php_indexing&m=3'));
	
	exit;	
}

/**
 * 
 */
function wp_idolondemand_add_to_text_index_options() {
	
	if(!current_user_can( 'manage_options' )) {
		wp_die( 'You are not allowed to \'add_to_text_index_options\'.' );
	}
	// Check that nonce field
	check_admin_referer('wp-idolondemand_op_verify');
	
	// process
	$msg = "";
	// index input options: json, file, reference, url
	$add_to_text_index_input_type_current = wp_idolondemand_get_add_to_text_index_input_type();
	
	// only allow/enforce json
	//if(isset($_POST['add_to_text_index_input_type']) && !empty($_POST['add_to_text_index_input_type']) ){
	$add_to_text_index_input_type_new = 'json';//$_POST['add_to_text_index_input_type'];
	if($add_to_text_index_input_type_new=='json'){
			
		// post sections to index: title, content, tags, categories
		$add_to_text_index_post_section_array = array('title'=>0,'body'=>0,'tags'=>0,'categories'=>0);
		if(isset($_POST['add_to_text_index_post_sections']) && !empty($_POST['add_to_text_index_post_sections']) ){
			$add_to_text_index_post_sections = $_POST['add_to_text_index_post_sections'];
			foreach($add_to_text_index_post_sections as $value){
				//if($value=='title'){
				$add_to_text_index_post_section_array['title']=1;
	      		//}
	      		//if($value=='body'){
	      		$add_to_text_index_post_section_array['body']=1;
	      		//}
	      		if($value=='tags'){
	      			$add_to_text_index_post_section_array['tags']=1;
	      		}
	      		if($value=='categories'){
	      			$add_to_text_index_post_section_array['categories']=1;
	      		}
	    	}
		}
			
    	update_option( 'wp_idolondemand_add_to_text_index_post_sections', $add_to_text_index_post_section_array );
			
	}else{
		delete_option( 'wp_idolondemand_add_to_text_index_post_sections');
	}
	
	update_option( "wp_idolondemand_add_to_text_index_input_type", $add_to_text_index_input_type_new );
	/**
	}else{
		
		delete_option( "wp_idolondemand_add_to_text_index_input_type");
	}
	*/
	
	// TODO put messages in a central place, static constants or possibly in config db
	wp_redirect(admin_url( 'admin.php?page=wp-idolondemand/inc/menu.inc.php_indexing&m=4'));
	
	exit;
}

function wp_idolondemand_index_all_posts() {

	if(!current_user_can( 'manage_options' )) {
		wp_die( 'You are not allowed to \'index_all_posts\'.' );
	}
	// Check that nonce field
	check_admin_referer('wp-idolondemand_op_verify');

	// process action
	$result = wp_idolondemand_add_to_text_index_all_posts();

	wp_redirect(admin_url( 'admin.php?page=wp-idolondemand/inc/menu.inc.php_indexing&m=5'));

	exit;
}

?>