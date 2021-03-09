<?php

defined( 'ABSPATH' ) or exit;

/**
 * @ignore
 */
function _mb4wp_admin_sidebar_support_notice() {

}

/**
 * @ignore
 */
function _mb4wp_admin_sidebar_other_plugins() {

}

add_action( 'mb4wp_admin_sidebar', '_mb4wp_admin_sidebar_other_plugins', 40 );
add_action( 'mb4wp_admin_sidebar', '_mb4wp_admin_sidebar_support_notice', 50 );

/**
 * Runs when the sidebar is outputted on MailBlaze for WordPress settings pages.
 *
 * Please note that not all pages have a sidebar.
 *
 * @since 3.0
 */
do_action( 'mb4wp_admin_sidebar' );
