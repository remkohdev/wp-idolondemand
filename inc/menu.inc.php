<?php
/**
 * This function is added in 'wp-idolondemand.php' by the following line
 * add_action( 'admin_menu', 'wp_idolondemand_menu' );
 */
function wp_idolondemand_menu() {
	add_menu_page(
		'IDOL OnDemand',                  //$page_title
	    'IDOL OnDemand',                  //$menu_title
	    'manage_options',                 //$capability
	    __FILE__ . '_idol_ondemand',      //$menu_slug
	    'wp_idolondemand_display_content',//$function, defined in 'inc/display-options.php'
	    '',                               //$icon_url
	    110                               //$position
	); 	
	// override title of main menu in submenu
	add_submenu_page(
		__FILE__ . '_idol_ondemand',      //$parent_slug
		'Settings',						  //$page_title
		'Settings',                       //$menu_title
		'manage_options',                 //$capability
		__FILE__ . '_idol_ondemand',   	  //$menu_slug
		'wp_idolondemand_display_content' //$function
	);
	
	add_submenu_page(  
		__FILE__ . '_idol_ondemand',              //$parent_slug
		'Indexing',							      //$page_title
		'Indexing',                               //$menu_title
		'manage_options',                         //$capability
		__FILE__ . '_indexing',   				  //$menu_slug
		'wp_idolondemand_display_content_indexing'//$function
	);

	add_submenu_page(
		__FILE__ . '_idol_ondemand',            //$parent_slug
		'Search',							    //$page_title
		'Search',                               //$menu_title
		'manage_options',                       //$capability
		__FILE__ . '_iod_search',    		    //$menu_slug
		'wp_idolondemand_display_content_search'//$function
	);
	
	add_submenu_page(
		__FILE__ . '_idol_ondemand',               //$parent_slug
		'Sentiment Analysis',					   //$page_title
		'Sentiment Analysis',                      //$menu_title
		'manage_options',                          //$capability
		__FILE__ . '_iod_sentiment',   			   //$menu_slug
		'wp_idolondemand_display_content_sentiment'//$function
	);

}




?>