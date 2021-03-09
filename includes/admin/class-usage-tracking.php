<?php

/**
 * Class MB4WP_Usage_Tracking
 *
 * @access private
 * @since 2.3
 * @ignore
 */
class MB4WP_Usage_Tracking {

	/**
	 * @var string
	 */
	protected $tracking_url = 'https://mb4wp.com/api/usage-tracking';

	/**
	 * @var MB4WP_Usage_Tracking The One True Instance
	 */
	protected static $instance;

	/**
	 * @return MB4WP_Usage_Tracking
	 */
	public static function instance() {

		if( ! self::$instance instanceof MB4WP_Usage_Tracking ) {
			self::$instance = new MB4WP_Usage_Tracking();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'mb4wp_usage_tracking', array( $this, 'track' ) );
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
	}

	/**
	 * Registers a new schedule with WP Cron
	 *
	 * @param array $schedules
	 *
	 * @return array
	 */
	public function cron_schedules( $schedules ) {
		$schedules['monthly'] = array(
			'interval' => 30 * DAY_IN_SECONDS,
			'display' => __( 'Once a month' )
		);
		return $schedules;
	}

	/**
	 * Enable usage tracking
	 *
	 * @return bool
	 */
	public function enable() {
		// only schedule if not yet scheduled
		if( ! wp_next_scheduled( 'mb4wp_usage_tracking' ) ) {
			return wp_schedule_event( time(), 'monthly', 'mb4wp_usage_tracking' );
		}

		return true;
	}

	/**
	 * Disable usage tracking
	 */
	public function disable() {
		wp_clear_scheduled_hook( 'mb4wp_usage_tracking' );
	}

	/**
	 * Toggle tracking (clears & sets the scheduled tracking event)
	 *
	 * @param bool $enable
	 */
	public function toggle( $enable ) {
		$enable ? $this->enable() : $this->disable();
	}

	/**
	 * Sends the tracking request. Non-blocking.
	 *
	 * @return bool
	 */
	public function track() {
		$data = $this->get_tracking_data();

		// send non-blocking request and be done with it
		wp_remote_post( $this->tracking_url, array(
				'body' => json_encode( $data ),
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept' => 'application/json'
				),
				'blocking' => false,
			)
		);

		return true;
	}

	/**
	 * @return array
	 */
	protected function get_tracking_data() {

		$data = array(
			// use md5 hash of home_url, we don't need/want to know the actual site url
			'site' => md5( home_url() ),
			'number_of_mailblaze_lists' => $this->get_mailblaze_lists_count(),
			'mb4wp_version' => $this->get_mb4wp_version(),
			'mb4wp_premium_version' => $this->get_mb4wp_premium_version(),
			'plugins' => (array) get_option( 'active_plugins', array() ),
			'php_version' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
			'curl_version' => $this->get_curl_version(),
			'wp_version' => $GLOBALS['wp_version'],
			'wp_language' => get_locale(),
			'server_software' => $this->get_server_software(),
			'using_https' => $this->is_site_using_https()
		);

		return $data;
	}

	/**
	 * @return string
	 */
	public function get_mb4wp_premium_version() {
		return defined( 'MB4WP_PREMIUM_VERSION' ) ? MB4WP_PREMIUM_VERSION : 0;
	}

	/**
	 * Returns the MailBlaze for WordPress version
	 *
	 * @return string
	 */
	protected function get_mb4wp_version() {
		return MB4WP_VERSION;
	}

	/**
	 * @return int
	 */
	protected function get_mailblaze_lists_count() {
		$mailblaze = new MB4WP_MailBlaze();
		$list_ids = $mailblaze->get_list_ids( false );
		return count( $list_ids );
	}


	/**
	 * @return string
	 */
	protected function get_curl_version() {

		if( ! function_exists( 'curl_version' ) ) {
			return 0;
		}

		$curl_version_info = curl_version();
		return $curl_version_info['version'];
	}

	/**
	 * @return bool
	 */
	protected function is_site_using_https() {
		$site_url = site_url();
		return strpos( $site_url, 'https://' ) === 0;
	}

	/**
	 * @return string
	 */
	protected function get_server_software() {

		if( ! isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return '';
		}

		return $_SERVER['SERVER_SOFTWARE'];
	}
}
