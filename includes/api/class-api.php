<?php

/**
 * Class MB4WP_API
 */
class MB4WP_API {

	/**
	 * @var MB4WP_API_Client
	 */
	protected $client;

	/**
	 * @var bool Are we able to talk to the MailBlaze API?
	 */
	protected $connected;

	/**
	 * Constructor
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {
		$this->client = new MB4WP_API_Client( $api_key );
	}

	/**
	 * Gets the API client to perform raw API calls.
	 *
	 * @return MB4WP_API_Client
	 */
	public function get_client() {
		return $this->client;
	}

	/**
	 * Pings the MailBlaze API to see if we're connected
	 *
	 * The result is cached to ensure a maximum of 1 API call per page load
	 *
	 * @return boolean
	 * @throws MB4WP_API_Exception
	 */
	public function is_connected() {

		if( is_null( $this->connected ) ) {
			$data = $this->client->get( '/general' );
			$this->connected = is_object( $data ) && isset( $data->status);
		}

		return $this->connected;
	}

	/**
	 * @param $email_address
	 *
	 * @return string
	 */
	public function get_subscriber_hash( $email_address ) {
		return md5( strtolower( trim( $email_address ) ) );
	}

	/**
	 * Get recent daily, aggregated activity stats for a list.
	 *
	 * @param string $list_id
	 * @param array $args
	 *
	 * @return array
	 * @throws MB4WP_API_Exception
	 */
	public function get_list_activity( $list_id, array $args = array() ) {
		$resource = sprintf( '/lists/%s/activity', $list_id );
		$data = $this->client->get( $resource, $args );

		if( is_object( $data ) && isset( $data->activity ) ) {
			return $data->activity;
		}

		return array();
	}

	/**
	 * Gets the interest categories for a given List
	 *
	 * @param string $list_id
	 * @param array $args
	 *
	 * @return array
	 * @throws MB4WP_API_Exception
	 */
	public function get_list_interest_categories( $list_id, array $args = array() ) {
		$resource = sprintf( '/lists/%s/interest-categories', $list_id );
		$data = $this->client->get( $resource, $args );

		if( is_object( $data ) && isset( $data->categories ) ) {
			return $data->categories;
		}

		return array();
	}

	/**
	 *
	 * @param string $list_id
	 * @param string $interest_category_id
	 * @param array $args
	 *
	 * @return array
	 * @throws MB4WP_API_Exception
	 */
	public function get_list_interest_category_interests( $list_id, $interest_category_id, array $args = array() ) {
		$resource = sprintf( '/lists/%s/interest-categories/%s/interests', $list_id, $interest_category_id );
		$data = $this->client->get( $resource, $args );

		if( is_object( $data ) && isset( $data->interests ) ) {
			return $data->interests;
		}

		return array();
	}

	/**
	 * Get merge vars for a given list
	 *
	 * @param string $list_id
	 * @param array $args
	 *
	 * @return array
	 * @throws MB4WP_API_Exception
	 */
	public function get_list_merge_fields( $list_id, array $args = array() ) {
		$resource = sprintf( '/lists/%s/fields', $list_id );
		$data = $this->client->get( $resource, $args );

		if( is_object( $data ) && isset( $data->data->records ) ) {
			return $data->data->records;
		}

		return array();
	}

	/**
	 *
	 * @param string $list_id
	 * @param array $args
	 *
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_list( $list_id, array $args = array() ) {
		$resource = sprintf( '/lists/%s', $list_id );
		$data = $this->client->get( $resource, $args );
		return $data->data->record->general;
	}

	/**
	 *
	 * @param array $args
	 *
	 * @return array
	 * @throws MB4WP_API_Exception
	 */
	public function get_lists( $args = array() ) {
		$resource = '/lists?per_page=150';
		$data = $this->client->get( $resource, $args );

		if( is_object( $data ) && isset( $data->data->records ) ) {
			return $data->data->records;
		}

		return array();
	}

	/**
	 *
	 * @param string $list_id
	 * @param string $email_address
	 * @param array $args
	 *
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_list_member( $list_id, $email_address, array $args = array() ) {
		$resource = sprintf( '/lists/%s/subscribers/search-by-email', $list_id );

		$data = $this->client->get( $resource, $args );

		return $data;
	}

	/**
	 * Batch subscribe / unsubscribe list members.
	 *
	 * @param string $list_id
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function add_list_members( $list_id, array $args ) {
		$resource = sprintf( '/lists/%s', $list_id );
		return $this->client->post( $resource, $args );
	}

	/**
	 * Add a member to a MailBlaze list.
	 *
	 * @param string $list_id
	 * @param array $args
	 *
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function add_list_member( $list_id, array $args ) {
		//$subscriber_hash = $this->get_subscriber_hash( $args['email_address'] );
		$resource = sprintf( '/lists/%s/subscribers', $list_id );

		// convert merge fields from object into args array
		if( isset( $args['merge_fields'] ) ) {
			$args['merge_fields'] = (object) $args['merge_fields'];
			foreach ($args['merge_fields'] as $key => $value) {
				$args[$key] = $value;
			}
			unset ($args['merge_fields']);
		}

		unset ($args['interests']);
		unset ($args['email_address']);
		unset ($args['status']);
		unset ($args['email_type']);

		// "put" updates the member if it's already on the list... take notice
		$data = $this->client->post( $resource, $args );

		return $data;
	}

	/**
	 *
	 * @param $list_id
	 * @param $email_address
	 * @param array $args
	 *
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function update_list_member( $list_id, $subscriber_uid, array $args) {
		
		$resource = sprintf( '/lists/%s/subscribers/%s', $list_id, $subscriber_uid );

		// convert merge fields from object into args array
		if( isset( $args['merge_fields'] ) ) {
			$args['merge_fields'] = (object) $args['merge_fields'];
			foreach ($args['merge_fields'] as $key => $value) {
				$args[$key] = $value;
			}
			unset ($args['merge_fields']);
		}

		unset ($args['interests']);
		unset ($args['email_address']);
		unset ($args['status']);
		unset ($args['email_type']);

		$data = $this->client->put( $resource, $args );
		
		return $data;
	}

	/**
	 *
	 * @param string $list_id
	 * @param string $email_address
	 *
	 * @return bool
	 * @throws MB4WP_API_Exception
	 */
	public function delete_list_member( $list_id, $subscriber_uid ) {

		$resource = sprintf( '/lists/%s/subscribers/%s', $list_id, $subscriber_uid );
		$data = $this->client->delete( $resource );
		return !!$data;
	}

	/**
	 * Get a list of an account's available templates
	 *
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_templates( array $args = array() ) {
		$resource = '/templates';
		return $this->client->get( $resource, $args );
	}

	/**
	 * Get information about a specific template.
	 *
	 * @param string $template_id
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_template( $template_id, array $args = array() ) {
		$resource = sprintf( '/templates/%s', $template_id );
		return $this->client->get( $resource, $args );
	}

	/**
	* Get template default content.
	 *
	 * @param string $template_id
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_template_default_content( $template_id, array $args = array() ) {
		$resource = sprintf( '/templates/%s/default-content', $template_id );
		return $this->client->get( $resource, $args );
	}

	/**
	 * Create a new campaign
	 *
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function add_campaign( array $args ) {
		$resource = '/campaigns';
		return $this->client->post( $resource, $args );
	}

	/**
	 * Get all campaigns in an account
	 *
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_campaigns( array $args = array() ) {
		$resource = '/campaigns';
		return $this->client->get( $resource, $args );
	}

	/**
	 * Get information about a specific campaign.
	 *
	 * @param string $campaign_id
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_campaign( $campaign_id, array $args = array() ) {
		$resource = sprintf( '/campaigns/%s', $campaign_id );
		return $this->client->get( $resource, $args );
	}

	/**
	 * Update some or all of the settings for a specific campaign.
	 *
	 * @param string $campaign_id
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function update_campaign( $campaign_id, array $args ) {
		$resource = sprintf( '/campaigns/%s', $campaign_id );
		return $this->client->patch( $resource, $args );
	}

	/**
	 * Remove a campaign from the MailBlaze account
	 * @param string $campaign_id
	 * @return bool
	 * @throws MB4WP_API_Exception
	 */
	public function delete_campaign( $campaign_id ) {
		$resource = sprintf( '/campaigns/%s', $campaign_id );
		return !! $this->client->delete( $resource );
	}

	/**
	 * Perform an action on a MailBlaze campaign
	 *
	 * @param string $campaign_id
	 * @param string $action
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function campaign_action( $campaign_id, $action, array $args = array() ) {
		$resource = sprintf( '/campaigns/%s/actions/%s', $campaign_id, $action );
		return $this->client->post( $resource, $args );
	}

	/**
	 * Get the HTML and plain-text content for a campaign
	 *
	 * @param string $campaign_id
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function get_campaign_content( $campaign_id, array $args = array() ) {
		$resource = sprintf( '/campaigns/%s/content', $campaign_id );
		return $this->client->get( $resource, $args );
	}

	/**
	 * Set the content for a campaign
	 *
	 * @link https://developer.mailblaze.com/documentation/mailblaze/reference/campaigns/content/#edit-put_campaigns_campaign_id_content
	 * @param string $campaign_id
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function update_campaign_content( $campaign_id, array $args ) {
		$resource = sprintf( '/campaigns/%s/content', $campaign_id );
		return $this->client->put( $resource, $args );
	}

	/**
	 * @return string
	 */
	public function get_last_response_body() {
		return $this->client->get_last_response_body();
	}

	/**
	 * @return array
	 */
	public function get_last_response_headers() {
		return $this->client->get_last_response_headers();
	}

	/**
	 * Create a field for a list.
	 *
	 * @param string $list_uid
	 * @param array $args
	 * @return object
	 * @throws MB4WP_API_Exception
	 */
	public function add_list_field( $list_uid, array $args ) {		

		$resource = sprintf( '/lists/%s/fields', $list_uid );

		return $this->client->post( $resource, $args );
	}


}
