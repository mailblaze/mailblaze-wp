<?php
/*
Plugin Name: Mail Blaze for WP
Plugin URI: https://www.mailblaze.com/support/connect-with-wordpress
Description: Mail Blaze the ability for your website visitors to sign up to your lists on the Mail Blaze email marketing platform.
Version: 1.1.1
Author: Mail Blaze
Author URI: https://www.mailblaze.com
Text Domain: mailblaze-wp
Domain Path: /languages
License: GPL v3

MailBlaze for WordPress

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Prevent direct file access
defined( 'ABSPATH' ) or exit;

/**
 * Bootstrap the MailBlaze for WordPress plugin
 *
 * @ignore
 * @access private
 * @return bool
 */
function _mb4wp_load_plugin() {
	

	global $mb4wp;

	// Don't run if MailBlaze for WP Pro 2.x is activated
	if( defined( 'MB4WP_VERSION' ) ) {
		return false;
	}	

	// bootstrap the core plugin
	define( 'MB4WP_VERSION', '1.1.1' );
	define( 'MB4WP_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
	define( 'MB4WP_PLUGIN_URL', plugins_url( '/' , __FILE__ ) );
	define( 'MB4WP_PLUGIN_FILE', __FILE__ );

	// load autoloader if function not yet exists (for compat with sitewide autoloader)
	if( ! function_exists( 'mb4wp' ) ) {
		require_once MB4WP_PLUGIN_DIR . 'vendor/autoload_52.php';
	}

	/**
	 * @global MB4WP_Container $GLOBALS['mb4wp']
	 * @name $mb4wp
	 */
	$mb4wp = mb4wp();
	$mb4wp['api'] = 'mb4wp_get_api';
	$mb4wp['request'] = array( 'MB4WP_Request', 'create_from_globals' );
	$mb4wp['log'] = 'mb4wp_get_debug_log';

	// forms
	$mb4wp['forms'] = new MB4WP_Form_Manager();
	$mb4wp['forms']->add_hooks();

	// integration core
	$mb4wp['integrations'] = new MB4WP_Integration_Manager();
	$mb4wp['integrations']->add_hooks();

	
	// Doing cron? Load Usage Tracking class.
	if( isset( $_GET['doing_wp_cron'] ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		MB4WP_Usage_Tracking::instance()->add_hooks();
	}

	// Initialize admin section of plugin
	if( is_admin() ) {

		$admin_tools = new MB4WP_Admin_Tools();

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$ajax = new MB4WP_Admin_Ajax( $admin_tools );
			$ajax->add_hooks();
		} else {
			$messages = new MB4WP_Admin_Messages();
			$mb4wp['admin.messages'] = $messages;

			$mailblaze = new MB4WP_MailBlaze();

			$admin = new MB4WP_Admin( $admin_tools, $messages, $mailblaze );
			$admin->add_hooks();

			$forms_admin = new MB4WP_Forms_Admin( $messages, $mailblaze );
			$forms_admin->add_hooks();

			$integrations_admin = new MB4WP_Integration_Admin( $mb4wp['integrations'], $messages, $mailblaze );
			$integrations_admin->add_hooks();
		}
	}

	return true;
}

// bootstrap custom integrations
function _mb4wp_bootstrap_integrations() {
	require_once MB4WP_PLUGIN_DIR . 'integrations/bootstrap.php';
}

add_action( 'plugins_loaded', '_mb4wp_load_plugin', 8 );
add_action( 'plugins_loaded', '_mb4wp_bootstrap_integrations', 90 );

/**
 * Flushes transient cache & schedules refresh hook.
 *
 * @ignore
 * @since 3.0
 */
function _mb4wp_on_plugin_activation() {
	$time_string = sprintf("tomorrow %d:%d%d am", wp_rand(1,6), wp_rand(0,5), wp_rand(0, 9) );
	wp_schedule_event( strtotime( $time_string ), 'daily', 'mb4wp_refresh_mailblaze_lists' );
}

/**
 * Clears scheduled hook for refreshing MailBlaze lists.
 *
 * @ignore
 * @since 4.0.3
 */
function _mb4wp_on_plugin_deactivation() {
	global $wpdb;
	wp_clear_scheduled_hook( 'mb4wp_refresh_mailblaze_lists' );

	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mb4wp_mailblaze_list_%'");
}

register_activation_hook( __FILE__, '_mb4wp_on_plugin_activation' );
register_deactivation_hook( __FILE__, '_mb4wp_on_plugin_deactivation' );

