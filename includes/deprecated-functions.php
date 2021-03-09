<?php

/**
 * @use mb4wp_add_name_merge_vars()
 * @deprecated 4.0
 * @ignore
 *
 * @param array $merge_vars
 * @return array
 */
function mb4wp_guess_merge_vars( $merge_vars = array() ) {
	_deprecated_function( __FUNCTION__, 'MailBlaze for WordPress v4.0' );
	$merge_vars = mb4wp_add_name_data( $merge_vars );
	$merge_vars = _mb4wp_update_groupings_data( $merge_vars );
	return $merge_vars;
}

/**
 * Echoes a sign-up checkbox.
 *
 * @ignore
 * @deprecated 3.0
 *
 * @use mb4wp_get_integration()
 */
function mb4wp_checkbox() {
	_deprecated_function( __FUNCTION__, 'MailBlaze for WordPress v3.0' );
	mb4wp_get_integration('wp-comment-form')->output_checkbox();
}

/**
 * Echoes a MailBlaze for WordPress form
 *
 * @ignore
 * @deprecated 3.0
 * @use mb4wp_show_form()
 *
 * @param int $id
 * @param array $attributes
 *
 * @return string
 *
 */
function mb4wp_form( $id = 0, $attributes = array() ) {
	_deprecated_function( __FUNCTION__, 'MailBlaze for WordPress v3.0', 'mb4wp_show_form' );
	return mb4wp_show_form( $id, $attributes );
}

/**
 * @deprecated 4.1.12
 * @return string
 */
function mb4wp_get_current_url()
{
   return $mb4wp_get_current_url();
}
