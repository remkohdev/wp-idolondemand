<?php

/**
 * Adds an IDOL OnDemand meta_box to the main column on the Post and Page edit screens.
 */
function wp_idolondemand_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
		'myplugin_sectionid',
		__( 'IDOL OnDemand', 'wp_idolondemand_textdomain' ),
		'wp_idolondemand_display_meta_box',
		$screen
		);
	}
}
add_action( 'add_meta_boxes', 'wp_idolondemand_add_meta_box' );

/**
 * Prints the meta box content.
 *
 * @param WP_Post $post The object for the current post/page.
*/
function wp_idolondemand_display_meta_box( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'wp_idolondemand_meta_box', 'wp_idolondemand_meta_box_nonce' );

	$post_id = $post->ID;
	$key1 = "wp_idolondemand_post_index_datetime";
	$result = wp_idolondemand_get_post_index_metadata($post_id, $key1);
	if(! $result){
		$result = "false";
	}
	echo '<label for="wp_idolondemand_indexed">';
	_e( 'Indexed', 'wp_idolondemand_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="wp_idolondemand_indexed" name="wp_idolondemand_indexed" value="' . esc_attr( $result ) . '" size="25" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wp_idolondemand_save_meta_box_data( $post_id ) {

	// Verify this came from correct screen with proper authorization,
	// because the save_post action can be triggered at other times.
	// Check if our nonce is set.
	if ( ! isset( $_POST['wp_idolondemand_meta_box_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['wp_idolondemand_meta_box_nonce'], 'wp_idolondemand_meta_box' ) ) {
		return;
	}
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	if ( ! isset( $_POST['wp_idolondemand_indexed'] ) ) {
		return;
	}
	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['wp_idolondemand_indexed'] );
	// Update the meta field in the database.
	// update_post_meta( $post_id, '_my_meta_value_key', $my_data );
}
add_action( 'save_post', 'wp_idolondemand_save_meta_box_data' );
?>