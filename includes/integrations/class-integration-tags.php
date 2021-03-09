<?php

/**
 * Class MB4WP_Integration_Tags
 *
 * @ignore
 * @access private
 */
class MB4WP_Integration_Tags{

	/**
	 * @var MB4WP_Dynamic_Content_Tags
	 */
	protected $tags;

	/**
	 * @var MB4WP_Integration
	 */
	protected $integration;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->tags = new MB4WP_Dynamic_Content_Tags( 'integrations' );
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_filter( 'mb4wp_dynamic_content_tags_integrations', array( $this, 'register' ) );
		add_filter( 'mb4wp_integration_checkbox_label', array( $this, 'replace' ), 10, 2 );
	}

	/**
	 * Register dynamic content tags for integrations
	 *
	 * @hooked `mb4wp_dynamic_content_tags_integrations`
	 * @param array $tags
	 * @return array
	 */
	public function register( array $tags ) {
		$tags['subscriber_count'] = array(
			'description' => __( 'Replaced with the number of subscribers on the selected list(s)', 'mailblaze-for-wp' ),
			'callback'    => array( $this, 'get_subscriber_count' )
		);
		return $tags;
	}

	/**
	 * @hooked `mb4wp_integration_checkbox_label`
	 * @param string $string
	 * @param MB4WP_Integration $integration
	 * @return string
	 */
	public function replace( $string, MB4WP_Integration $integration ) {
		$this->integration = $integration;
		$string = $this->tags->replace( $string );
		return $string;
	}

    /**
     * Returns the number of subscribers on the selected lists (for the form context)
     *
     * @return int
     */
    public function get_subscriber_count() {
        $mailblaze = new MB4WP_MailBlaze();
        $count = $mailblaze->get_subscriber_count( $this->integration->get_lists() );
        return number_format( $count );
    }
}