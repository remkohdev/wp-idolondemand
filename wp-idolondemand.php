<?php

/*
Plugin Name: IDOL OnDemand for WordPress
Plugin URI: http://it.remkohde.com/wp/plugins/wp-idolondemand
Description: This IDOL OnDemand for WordPress plugin brings the power of IDOL to your blog: index your content to add enterprise search intelligence to your blog all by using the IDOL OnDemand APIs.
Version: 0.1.0
Revision Date: July 19, 2014
Requires at least: WP 3.9.1
Tested up to: WP 3.9.1
Author: @remkohdev
Author URI: http://www.remkohde.com
License: (Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Network: true

=== RELEASE NOTES ===

WP-IDOLONDEMAND Plugin 
Contributors: @remkohdev

2014-07-21 - v1.0.0 - init. preview release 
0000-00-00 - v1.1.0 - 

*/

/** ************************************************************************ */

function wp_idolondemand_add_frontend_scripts() {
	if( ! is_admin() ) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
	}
}
add_action('init', 'wp_idolondemand_add_frontend_scripts');

register_activation_hook( __FILE__, 'wp_idolondemand_activate' );
register_deactivation_hook( __FILE__, 'wp_idolondemand_deactivate' );
register_uninstall_hook( __FILE__, 'wp_idolondemand_uninstall' );


include_once(dirname(__FILE__).'/admin.php');
include_once(dirname(__FILE__).'/widgets/wp-idolondemand-meta-boxes.php');
include_once(dirname(__FILE__).'/widgets/wp-idolondemand-widgets.php');
include_once(dirname(__FILE__).'/inc/display-options.inc.php');
include_once(dirname(__FILE__).'/inc/iod-indexing.php');
include_once(dirname(__FILE__).'/inc/iod-sentiment-analysis.php');
include_once(dirname(__FILE__).'/inc/iod-search.php');
include_once(dirname(__FILE__).'/inc/menu.inc.php');
include_once(dirname(__FILE__).'/inc/process.php');


add_action("plugins_loaded", "init_wp_idolondemand_widgets");

add_action( 'admin_menu', 'wp_idolondemand_menu' );// see inc/menu.inc.php
// hook name: 'admin_post_'.(value from hidden action field in form)
add_action( 'admin_post_wp_idolondemand_save_apikey', 'wp_idolondemand_save_apikey' );// see inc/process.php
add_action( 'admin_post_wp_idolondemand_use_this_index', 'wp_idolondemand_use_this_index' );// see inc/process.php
add_action( 'admin_post_wp_idolondemand_add_to_text_index_options', 'wp_idolondemand_add_to_text_index_options');// see inc/process.php

add_action( 'admin_post_wp_idolondemand_create_index', 'wp_idolondemand_create_index' );
add_action( 'admin_post_wp_idolondemand_index_all_posts', 'wp_idolondemand_index_all_posts' );
add_action( 'admin_post_wp_idolondemand_check_jobid_status', 'wp_idolondemand_check_jobid_status' );

add_action( 'wp_idolondemand_get_sentiment', 'wp_idolondemand_get_sentiment_for_post' );

add_action( 'edit_post', 'wp_idolondemand_edit_post' );
add_action( 'save_post', 'wp_idolondemand_save_post' );
add_action( 'delete_post', 'wp_idolondemand_delete_post' );
add_action( 'admin_notices', 'wp_idolondemand_admin_notice' );

add_filter('bulk_actions-posts-edit','bulk_index');
//do_action( 'bulk_action-'. $wp_list_table->current_action() );

// Define a constant that can be checked to see if the component is installed or not.
define( 'WP_IDOLONDEMAND_IS_INSTALLED', 1 );
// Define a constant that will hold the current version number of the component
// This can be useful if you need to run update scripts or do compatibility checks in the future
define( 'WP_IDOLONDEMAND_VERSION', '1.0.0' );
define ( 'WP_IDOLONDEMAND_DB_VERSION', '1.0.0' );

// Define a constant that we can use to construct file paths throughout the component
define( 'WP_IDOLONDEMAND_PLUGIN_DIR', dirname( __FILE__ ) );


/** ************************************************************************ */

function wp_idolondemand_activate() {
	
	wp_idolondemand_check_WP_version();
	wp_idolondemand_install();
	wp_idolondemand_install_data();
	
}

function wp_idolondemand_check_WP_version() {
	
	if(version_compare(get_bloginfo( 'version' ), '3.9', '<' )) {
		wp_die("You must update WordPress to use this plugin!");
	}
	
	if (get_option( 'wp_idolondemand_op_array' ) === false){
		
		$options_array['wp_idolondemand_op_version'] = '1.0.0';
		add_option( 'wp_idolondemand_op_array', $options_array );
	}
}

function wp_idolondemand_install() {
	
	global $wpdb;
	
	$tbl_wp_idolondemand_settings = $wpdb->prefix . "idolondemand_settings";
    $sql_create_wp_idolondemand_settings = 
		"CREATE TABLE $tbl_wp_idolondemand_settings (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		key1 tinytext NOT NULL,
		value1 VARCHAR(100) NOT NULL,
		UNIQUE KEY id (id)
		);";

	$tbl_wp_idolondemand_post_metadata = $wpdb->prefix . "idolondemand_post_metadata";
	$sql_create_wp_idolondemand_post_metadata =
		"CREATE TABLE $tbl_wp_idolondemand_post_metadata (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		post_id mediumint(9) NOT NULL,
		key1 VARCHAR(100) NOT NULL,
		value1 VARCHAR(255) NOT NULL,
		PRIMARY KEY (id),
		UNIQUE INDEX post_id_key1 (post_id, key1)
		);";
	
	$tbl_wp_idolondemand_log = $wpdb->prefix . "idolondemand_log";
	$sql_create_wp_idolondemand_log =
		"CREATE TABLE $tbl_wp_idolondemand_log (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		key1 VARCHAR(100) NOT NULL,
		value1 LONGTEXT NOT NULL,
		datetime1 TIMESTAMP,
		PRIMARY KEY (id)
		);";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	// dbDelta only creates the delta with existing table
	dbDelta( $sql_create_wp_idolondemand_settings );
	dbDelta( $sql_create_wp_idolondemand_post_metadata );
	dbDelta( $sql_create_wp_idolondemand_log );
	
}

function wp_idolondemand_install_data() {
	
	// add to wp_options table
	// get_option(), update_option(), delete_option().
	add_option( "wp_idolondemand_db_version", "1.0.0" );
	
}

function wp_idolondemand_deactivate() {
	// TODO (probably nothing to do on deactivate. See uninstall.
}

function wp_idolondemand_uninstall(){
	// cleanup database variables etc.
	// TODO
}

/** ************************************************************************ */

?>
