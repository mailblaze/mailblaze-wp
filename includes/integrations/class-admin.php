<?php

/**
 * Class MB4WP_Integration_Admin
 *
 * @ignore
 * @access private
 */
class MB4WP_Integration_Admin {

	/**
	 * @var MB4WP_Integration_Manager
	 */
	protected $integrations;

	/**
	 * @var MB4WP_MailBlaze
	 */
	protected $mailblaze;

	/**
	 * @var MB4WP_Admin_Messages
	 */
	protected $messages;

	/**
	 * @param MB4WP_Integration_Manager $integrations
	 * @param MB4WP_MailBlaze           $mailblaze
	 * @param MB4WP_Admin_Messages $messages
	 */
	public function __construct( MB4WP_Integration_Manager $integrations, MB4WP_Admin_Messages $messages, MB4WP_MailBlaze $mailblaze ) {
		$this->integrations = $integrations;
		$this->mailblaze = $mailblaze;
		$this->messages = $messages;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'mb4wp_admin_enqueue_assets', array( $this, 'enqueue_assets' ), 10, 2 );
		add_filter( 'mb4wp_admin_menu_items', array( $this, 'add_menu_item' ) );
	}

	/**
	 * Register settings
	 */
	public function register_setting() {
		register_setting( 'mb4wp_integrations_settings', 'mb4wp_integrations', array( $this, 'save_integration_settings' ) );
	}

	/**
	 * Enqueue assets
	 *
	 * @param string $suffix
	 * @param string $page
	 *
	 * @return void
	 */
	public function enqueue_assets( $suffix, $page = '' ) {

		// only load on integrations pages
		if( $page !== 'integrations' ) {
			return;
		}

		wp_register_script( 'mb4wp-integrations-admin', MB4WP_PLUGIN_URL . 'assets/js/integrations-admin' . $suffix . '.js', array( 'mb4wp-admin' ), MB4WP_VERSION, true );
		wp_enqueue_script( 'mb4wp-integrations-admin');
	}

	/**
	 * @param $items
	 *
	 * @return array
	 */
	public function add_menu_item( $items ) {
		$items[] = array(
			'title' => __( 'Integrations', 'mailblaze-for-wp' ),
			'text' => __( 'Integrations', 'mailblaze-for-wp' ),
			'slug' => 'integrations',
			'callback' => array( $this, 'show_integrations_page' ),
			'position' => 20
		);

		return $items;
	}

	/**
	 * @param array $new_settings
	 * @return array
	 */
	public function save_integration_settings( array $new_settings ) {

		$integrations = $this->integrations->get_all();
		$current_settings = (array) get_option( 'mb4wp_integrations', array() );
		$settings = array();

		foreach( $integrations as $slug => $integration ) {
			$settings[ $slug ] = $this->parse_integration_settings( $slug, $current_settings, $new_settings );
		}

		return $settings;
	}

	/**
	 * @since 3.0
	 * @param $slug
	 * @param $current
	 * @param $new
	 *
	 * @return array
	 */
	protected function parse_integration_settings( $slug, $current, $new ) {
		$settings = array();

		// start with current settings
		if( ! empty( $current[ $slug ] ) ) {
			$settings = $current[ $slug ];
		}

		// if no new settings were given, return current settings.
		if( empty( $new[ $slug ] ) ) {
			return $settings;
		}

		// merge new settings with currents (to allow passing partial setting arrays)
		$settings = array_merge( $settings, $new[ $slug] );

		// sanitize settings
		$settings = $this->sanitize_integration_settings( $settings );

		return $settings;
	}

	/**
	 * @param array $settings
	 *
	 * @return array
	 */
	protected function sanitize_integration_settings( $settings ) {

		// filter null values from lists setting
		if( ! empty( $settings['lists'] ) ) {
			$settings['lists'] = array_filter( $settings['lists'] );
		} else {
			$settings['lists'] = array();
		}

		return $settings;
	}

	/**
	 * Show the Integration Settings page
	 *
	 * @internal
	 */
	public function show_integrations_page() {

		if( ! empty( $_GET['integration'] ) ) {
			$this->show_integration_settings_page( $_GET['integration'] );
			return;
		}

		// get all installed & enabled integrations
		$enabled_integrations = $this->integrations->get_enabled_integrations();

		// get all integrations but remove enabled integrations from the resulting array
		$available_integrations = $this->integrations->get_all();
		$available_integrations = array_diff( $available_integrations, $enabled_integrations );

		require dirname( __FILE__ ) . '/views/integrations.php';
	}

	/**
	 * @param string $slug
	 *
	 * @internal
	 */
	public function show_integration_settings_page( $slug ) {

		try {
			$integration = $this->integrations->get( $slug );
		} catch( Exception $e ) {
			echo sprintf( '<h3>Integration not found.</h3><p>No integration with slug <strong>%s</strong> was found.</p>', esc_html( $slug ) );
			return;
		}

		$opts = $integration->options;
		$lists = $this->mailblaze->get_lists();

		require dirname( __FILE__ ) . '/views/integration-settings.php';
	}


}
