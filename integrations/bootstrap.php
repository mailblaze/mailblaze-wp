<?php

/**
 * Try to include a file before each integration's settings page
 *
 * @param MB4WP_Integration $integration
 * @param array $opts
 * @ignore
 */
function mb4wp_admin_before_integration_settings( MB4WP_Integration $integration, $opts ) {

	$file = dirname( __FILE__ ) . sprintf( '/%s/admin-before.php', $integration->slug );

	if( file_exists( $file ) ) {
		include $file;
	}
}

/**
 * Try to include a file before each integration's settings page
 *
 * @param MB4WP_Integration $integration
 * @param array $opts
 * @ignore
 */
function mb4wp_admin_after_integration_settings( MB4WP_Integration $integration, $opts ) {
	$file = dirname( __FILE__ ) . sprintf( '/%s/admin-after.php', $integration->slug );

	if( file_exists( $file ) ) {
		include $file;
	}
}

add_action( 'mb4wp_admin_before_integration_settings', 'mb4wp_admin_before_integration_settings', 30, 2 );
add_action( 'mb4wp_admin_after_integration_settings', 'mb4wp_admin_after_integration_settings', 30, 2 );

// Register core integrations
mb4wp_register_integration( 'ninja-forms-2', 'MB4WP_Ninja_Forms_v2_Integration', true );
mb4wp_register_integration( 'wp-comment-form', 'MB4WP_Comment_Form_Integration' );
mb4wp_register_integration( 'wp-registration-form', 'MB4WP_Registration_Form_Integration' );
mb4wp_register_integration( 'buddypress', 'MB4WP_BuddyPress_Integration' );
mb4wp_register_integration( 'woocommerce', 'MB4WP_WooCommerce_Integration' );
mb4wp_register_integration( 'easy-digital-downloads', 'MB4WP_Easy_Digital_Downloads_Integration' );
mb4wp_register_integration( 'contact-form-7', 'MB4WP_Contact_Form_7_Integration', true );
mb4wp_register_integration( 'events-manager', 'MB4WP_Events_Manager_Integration' );
mb4wp_register_integration( 'memberpress', 'MB4WP_MemberPress_Integration' );
mb4wp_register_integration( 'affiliatewp', 'MB4WP_AffiliateWP_Integration' );

mb4wp_register_integration( 'custom', 'MB4WP_Custom_Integration', true );
$dir = dirname( __FILE__ );
require $dir . '/ninja-forms/bootstrap.php';
require $dir . '/wpforms/bootstrap.php';
require $dir . '/gravity-forms/bootstrap.php';

