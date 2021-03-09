<?php

/**
 * This class takes care of all form related functionality
 *
 * Do not interact with this class directly, use `mb4wp_form` functions tagged with @access public instead.
 *
 * @class MB4WP_Form_Manager
 * @ignore
 * @access private
*/
class MB4WP_Form_Manager {

	/**
	 * @var MB4WP_Form_Output_Manager
	 */
	protected $output_manager;

	/**
	 * @var MB4WP_Form_Listener
	 */
	protected $listener;

	/**
	 * @var MB4WP_Form_Tags
	 */
	protected $tags;

	/**
	* @var MB4WP_Form_Previewer
	*/
	protected $previewer;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->output_manager = new MB4WP_Form_Output_Manager();
		$this->tags = new MB4WP_Form_Tags();
		$this->listener = new MB4WP_Form_Listener();
		$this->previewer = new MB4WP_Form_Previewer();
	}

	/**
	 * Hook!
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'initialize' ) );
		add_action( 'wp', array( $this, 'init_asset_manager' ), 90 );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );

		$this->listener->add_hooks();
		$this->output_manager->add_hooks();
		$this->tags->add_hooks();
		$this->previewer->add_hooks();
	}

	/**
	 * Initialize
	 */
	public function initialize() {
		$this->register_post_type();
	}


	/**
	 * Register post type "mb4wp-form"
	 */
	public function register_post_type() {
		// register post type
		register_post_type( 'mb4wp-form', array(
				'labels' => array(
					'name' => 'MailBlaze Sign-up Forms',
					'singular_name' => 'Sign-up Form',
				),
				'public' => false
			)
		);
	}

	/**
	 * Initialise asset manager
	 *
	 * @hooked `template_redirect`
	 */
	public function init_asset_manager() {
		$assets = new MB4WP_Form_Asset_Manager();
		$assets->hook();
	}

	/**
	 * Register our Form widget
	 */
	public function register_widget() {
		register_widget( 'MB4WP_Form_Widget' );
	}

	/**
	 * @param       $form_id
	 * @param array $config
	 * @param bool  $echo
	 *
	 * @return string
	 */
	public function output_form(  $form_id, $config = array(), $echo = true ) {
		return $this->output_manager->output_form( $form_id, $config, $echo );
	}

	/**
	 * Gets the currently submitted form
	 *
	 * @return MB4WP_Form|null
	 */
	public function get_submitted_form() {
		if( $this->listener->submitted_form instanceof MB4WP_Form ) {
			return $this->listener->submitted_form;
		}

		return null;
	}

	/**
	 * Return all tags
	 *
	 * @return array
	 */
	public function get_tags() {
		return $this->tags->get();
	}
}
