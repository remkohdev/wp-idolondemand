<?php

/** ****************************************************************************
IDOL OnDemand page
 ***************************************************************************** */

function wp_idolondemand_display_content() {
	if(!current_user_can('manage_options')) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$apikey = wp_idolondemand_get_setting('apikey');
	$apikey_exists = wp_idolondemand_apikey_exists();
	?>	
	<div class="wrap">
	    <h1>IDOL OnDemand for WordPress</h1>
		
		<h2>Account Settings</h2>
		<?php
		// if successfully saved
		if(isset( $_GET['m'] ) && $_GET['m'] == '1') { ?>
			<div id='message' class='updated fade'><p><strong>You successfully saved your apikey.</strong></p></div>
		<?php }
		if(isset($_GET['result']) && !(empty($_GET['result'])) ) { ?>
			<div id='message' class='updated fade'><p><strong><?php echo $_GET['result']; ?></strong></p></div>
		<?php }
		$apikey_value = $apikey;
		if(!$apikey_exists){
			$apikey_value = "your idol ondemand apikey";		
		}
		?>
		<form method="post" action="admin-post.php">
	         <?php wp_nonce_field( 'wp-idolondemand_op_verify' ); ?>
	         <input type="hidden" name="action" value="wp_idolondemand_save_apikey" />
	         <label>API Key</label> <input type="text" name="idolondemand-apikey" id="idolondemand-apikey" value="<?php echo $apikey_value; ?>"><br>
			 <br>
			 <input type="submit" value="Save" class="button-primary"/>
      	</form>
      	<br>

	</div>
<?php 
}

/** ****************************************************************************
INDEXING page
***************************************************************************** */

/**
 * 
 */
function wp_idolondemand_display_content_indexing() {

	if(!current_user_can('manage_options')) {
		wp_die( __( 'You do not have sufficient permissions to access the \'Indexing\' page.' ) );
	}
	$apikey_exists = wp_idolondemand_apikey_exists();
	
	?>
	
	<div class="wrap">
	    <h1>IDOL OnDemand - Indexing</h1>
		<p>For more information about indexing, see the IDOL OnDemand Indexing docs:<br>
		<a href="https://www.idolondemand.com/developer/apis#indexing" window="_blank">https://www.idolondemand.com/developer/apis#indexing</a><br>
		</p>
		<h2>Text Indexes</h2>
		<?php
		// if successfully saved
		if ( isset( $_GET['m'] ) && $_GET['m'] == '2') { ?>
			<div id='message' class='updated fade'><p><strong>You successfully created a new index.</strong></p></div>
		<?php 
		}
		 
		if(! $apikey_exists){ ?> 
			<p>You must have a valid APIKEY for IDOL OnDemand to see your indexes.</p>
		<?php 
		}else{ 
	    	$indexes = wp_idolondemand_list_indexes();
	    	$indexes_size = sizeof($indexes);
	    	if($indexes && $indexes_size > 0){
	    	// TODO distinct between 200 response with 0 indexes and error response causing indexes to be empty
	    	?>
				<p>Your existing indexes</p>
				
				<form method="post" action="admin-post.php">
			    <?php wp_nonce_field( 'wp-idolondemand_op_verify' ); ?>
			    <input type="hidden" name="action" value="wp_idolondemand_use_this_index" />
			    
			    <table border="1">
				<tr>
				    <th>add to text index</th>
					<th>index</th>
					<th>flavor</th>
					<th>type</th>
					<th>created</th>
					<th>num_components</th>
				</tr>
				<?php
				$max = ($indexes_size)-1;
				for ($i=0; $i<=$max; $i++) {

					$checked = "";
					$checkedindex = $i;
					$index = wp_idolondemand_get_index();
					if($index && !empty($index) && $indexes[$i]->index){
						if($indexes[$i]->index==$index){
							$checked = "CHECKED";
						}					
					}
				?>
					<tr>
					    <td>
					    	<input  type="checkbox" 
					    			name="use_this_index[]" 
					    			value="<?php echo $indexes[$i]->index; ?>" 
					    		   	class="use_this_index_checkbox" 
					    		   	<?php echo $checked; ?> >
					    </td>
						<td><?php echo $indexes[$i]->index; ?></td>
						<td><?php echo $indexes[$i]->flavor; ?></td>
						<td><?php echo $indexes[$i]->type; ?></td>
						<td><?php echo $indexes[$i]->date_created; ?></td>
						<td><?php echo $indexes[$i]->num_components; ?></td>
					</tr>
				<?php
				}				
				?>
				</table>
				<br>
				<input type="submit" value="Save" class="button-primary"/>
				</form>
				
				<script type="text/javascript">
				jQuery('.use_this_index_checkbox').click(function(){
					var isChecked = jQuery(this).is(":checked");
					jQuery('.use_this_index_checkbox').prop('checked',false);
					if(isChecked){
						jQuery(this).prop('checked',true);
					}else{
						jQuery(this).prop('checked',false);
					}
				});				
				</script>
			<?php 
			}else{
			?> 
				You currently have no indexes.<br>
				<br>
			
				<form method="post" action="admin-post.php">
			         <?php wp_nonce_field( 'wp-idolondemand_op_verify' ); ?>
			         <input type="hidden" name="action" value="wp_idolondemand_create_index"/>
					<label>Create new index</label> <input type="text" name="wp-idolondemand-create-new-index-name" >
					<input type="submit" value="Create" class="button-primary"/>
				</form>
			
			<?php 	
			
			
			} // end-if(indexes)
		
		} // end-if(apikey_exists) ?>
		
		<br>
		<h2>Add to Text Index Options</h2>
		
		<?php 
		$add_to_text_index_input_type = wp_idolondemand_get_add_to_text_index_input_type();
		$post_sections_checked = array('title'=>'CHECKED DISABLED',
									   'body'=>'CHECKED DISABLED',
										'tags'=>'',
                                        'categories'=>'');
		$input_type_checked = array('json'=>'','file'=>'','reference'=>'','url'=>'');
		
		// enforce/only allow json
		//if($add_to_text_index_input_type=='json'){
		$input_type_checked['json'] = "CHECKED";
		$add_to_text_index_post_sections = wp_idolondemand_get_add_to_text_index_post_sections();
		if($add_to_text_index_post_sections){
			if($add_to_text_index_post_sections['tags']){ $post_sections_checked['tags'] = "CHECKED"; }
			if($add_to_text_index_post_sections['categories']){ $post_sections_checked['categories'] = "CHECKED"; }
		}
		/**	
		}else{ 
			if($add_to_text_index_input_type=='file'){
				$input_type_checked['file'] = "CHECKED";
			}else if($add_to_text_index_input_type=='reference'){
				$input_type_checked['reference'] = "CHECKED";
			}else if($add_to_text_index_input_type=='url'){
				$input_type_checked['url'] = "CHECKED";
			}
			
			$post_sections_checked['title'] = "DISABLED";
			$post_sections_checked['body'] = "DISABLED";
			$post_sections_checked['tags'] = "DISABLED";
			$post_sections_checked['categories'] = "DISABLED";
		}
		*/
		?>
		<form method="post" action="admin-post.php" id="iod_index_options_form">
	        <?php wp_nonce_field( 'wp-idolondemand_op_verify' ); ?>
	        <input type="hidden" name="action" value="wp_idolondemand_add_to_text_index_options" />
	        <p>Each post can be added to the Text Index. Select here what sections of the post you want to be indexed.</p>
			<ul>
				<li><input type="radio" name="add_to_text_index_input_type" value="json" class="add_to_text_index_type_radio" <?php echo $input_type_checked['json']; ?> DISABLED>Include the following sections of posts when indexing:</li>
				<ul>
					<li class="index-options"><input type="checkbox" name="add_to_text_index_post_sections[]" value="title" <?php  echo $post_sections_checked['title'];  ?>  class="add_to_text_index_post_sections_checkbox" id="add_to_text_index_post_sections_title_checkbox">Title</li>
					<li class="index-options"><input type="checkbox" name="add_to_text_index_post_sections[]" value="body" <?php  echo $post_sections_checked['body'];  ?> class="add_to_text_index_post_sections_checkbox" id="add_to_text_index_post_sections_body_checkbox">Content</li>
					<li class="index-options"><input type="checkbox" name="add_to_text_index_post_sections[]" value="tags" <?php  echo $post_sections_checked['tags'];  ?> class="add_to_text_index_post_sections_checkbox" id="add_to_text_index_post_sections_tags_checkbox">Tags</li>
					<li class="index-options"><input type="checkbox" name="add_to_text_index_post_sections[]" value="categories" <?php  echo $post_sections_checked['categories']; ?> class="add_to_text_index_post_sections_checkbox" id="add_to_text_index_post_sections_categories_checkbox">Categories</li>
				</ul>
				<?php 
				/**
		        <li><input type="radio" name="add_to_text_index_input_type" value="file" class="add_to_text_index_type_radio" <?php echo $input_type_checked['file']; ?>>file</li>
				<li><input type="radio" name="add_to_text_index_input_type" value="reference" class="add_to_text_index_type_radio" <?php echo $input_type_checked['reference']; ?>>reference</li>
				 */ ?>
			</ul>
			<input type="submit" value="Save" class="button-primary"/>
		</form>
		<style>
		li.index-options {
			margin-left:100px;
		}
		</style>
		<br>
		
		<h2>Bulk Indexing</h2>
		<br>
		<?php
		// if successfully saved
		if ( isset( $_GET['m'] ) && $_GET['m'] == '3') { ?>
			<div id='message' class='updated fade'><p><strong>You successfully indexed all posts.</strong></p></div>
		<?php }
		if(!$apikey_exists){ ?>
			<p>You must have a valid APIKEY to index all your posts.</p>
		<?php 
		}else{ 
		?>
		<form method="post" action="admin-post.php">
	         <?php wp_nonce_field( 'wp-idolondemand_op_verify' ); ?>
	         <input type="hidden" name="action" value="wp_idolondemand_index_all_posts" />
			 <input type="submit" value="Index All Posts" class="button-primary"/>
		</form>
		<br>
		
		<h2>Previous JobIDs Log</h2>
		<?php 
			$logs = wp_idolondemand_get_logs("wp_idolondemand_job_id");
			// if jobIDs exist check status
			if($logs){
				// if status of job is completed, delete jobID
				// if status of job is not completed, display jobID and status here
				?>
				
				<form method="post" action="admin-post.php">
	         		<?php wp_nonce_field( 'wp-idolondemand_op_verify' ); ?>
	         		<input type="hidden" name="action" value="wp_idolondemand_check_jobid_status" />
			 		
			 
				<table border="1">
				<tr><th>Check Status</th><th>JobId</th><th>Status</th><th>Last updated</th></tr>
				<?php 
				foreach($logs as $log){
					?>
					<tr>
					  <td><?php 
					  if($log['status']!='finished' &&
					  	 $log['status']!='failed') { ?>
					  <input type="checkbox" value="<?php echo $log['jobId']; ?>" name="jobids_to_check[]"> 
					  <?php } ?>
					  </td>
					  <td><?php echo $log['jobId']; ?></td>
					  <td><?php echo $log['status']; ?></td>
					  <td><?php echo $log['datetime1']; ?></td>
					</tr>
					<?php
				}
				?>
				</table>
				<br>
					<input type="submit" value="Check Status" class="button-primary"/>
				</form>
				<?php
			}
		?>
		<?php 
		} /** end-if(apikey_exists) */ ?>
		<br>
	</div>
<?php 
}


function wp_idolondemand_display_content_search() {

	if(!current_user_can('manage_options')) {
		wp_die( __( 'You do not have sufficient permissions to access the \'Indexing\' page.' ) );
	}
	$apikey_exists = wp_idolondemand_apikey_exists();

	?>
	
	<div class="wrap">
	    <h1>IDOL OnDemand - Search</h1>
	    <p>For more information about IDOL OnDemand search, see the IDOL OnDemand Search docs:<br>
		<a href="https://www.idolondemand.com/developer/apis#search" window="_blank">https://www.idolondemand.com/developer/apis#search</a><br>
		</p>	
		<h2>Search</h2>
		<p>placeholder</p>
<?php 
}	


function wp_idolondemand_display_content_sentiment() {

	if(!current_user_can('manage_options')) {
		wp_die( __( 'You do not have sufficient permissions to access the \'Sentiment Analysis\' page.' ) );
	}
	$apikey_exists = wp_idolondemand_apikey_exists();

	?>
	
	<div class="wrap">
	    <h1>IDOL OnDemand - Sentiment Analysis</h1>
	    <p>For more information about Sentiment Analysis, see the IDOL OnDemand Sentiment Analysis docs:<br>
		<a href="https://www.idolondemand.com/developer/apis/analyzesentiment" window="_blank">https://www.idolondemand.com/developer/apis/analyzesentiment</a><br>
		</p>
		<h2>Sentiment Analysis</h2>
		<p>placeholder</p>
<?php 
}	

?>
