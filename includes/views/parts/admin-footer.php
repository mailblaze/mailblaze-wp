<?php defined( 'ABSPATH' ) or exit;

/**
 * @ignore
 */
function _mb4wp_admin_translation_notice() {

	// show for every language other than the default
	if( stripos( get_locale(), 'en_us' ) === 0 ) {
		return;
	}

}

/**
 * @ignore
 */
function _mb4wp_admin_github_notice() {

	if( strpos( $_SERVER['HTTP_HOST'], 'local' ) !== 0 && ! WP_DEBUG ) {
		return;
	}

}

/**
 * @ignore
 */
function _mb4wp_admin_disclaimer_notice() {

}

add_action( 'mb4wp_admin_footer', '_mb4wp_admin_translation_notice' , 20);
add_action( 'mb4wp_admin_footer', '_mb4wp_admin_github_notice', 50 );
add_action( 'mb4wp_admin_footer', '_mb4wp_admin_disclaimer_notice', 80 );
?>

<div class="big-margin">

	<?php

	/**
	 * Runs while printing the footer of every MailBlaze for WordPress settings page.
	 *
	 * @since 3.0
	 */
	do_action( 'mb4wp_admin_footer' ); ?>

</div>
