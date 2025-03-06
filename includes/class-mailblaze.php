<?php

/**
* Class MB4WP_MailBlaze
*
* @access private
* @ignore
*/
class MB4WP_MailBlaze {

	/**
	* @var string
	*/
	public $error_code = '';

	/**
	* @var string
	*/
	public $error_message = '';

	/**
	*
	* Sends a subscription request to the MailBlaze API
	*
	* @param string  $list_id           The list id to subscribe to
	* @param string  $email_address             The email address to subscribe
	* @param array    $args
	* @param boolean $update_existing   Update information if this email is already on list?
	* @param boolean $replace_interests Replace interest groupings, only if update_existing is true.
	*
	* @return object
	*/
	public function list_subscribe( $list_id, $email_address, array $args = array(), $update_existing = false, $replace_interests = true ) {
		$this->reset_error();

		$default_args = array(
			'status' => 'unconfirmed',
			'EMAIL' => $email_address,
			'merge_fields' => array(),
			'SEND_OPTIN' => true, // this field is needed to trigger MailBlaze to send the confirmation email
		);
		$already_on_list = false;

		// setup default args
		$args = $args + $default_args;

		// first, check if subscriber is already on the given list
		try {

			$existing_member_data = $this->get_api()->get_list_member( $list_id, $email_address, $args );			

			if( $existing_member_data->status == 'success' ) {

				//Subscriber Uid is needed to perform actions on existing subscriber records
				$subscriber_uid = $existing_member_data->data->subscriber_uid;

				if( $existing_member_data->data->status == 'confirmed' ) {
					$already_on_list = true;

					// if we're not supposed to update, bail.
					if( ! $update_existing ) {
						$this->error_code = 214;
						$this->error_message = 'Email address is already subscribed.';
						return null;
					}

					$args['status'] = 'confirmed';

					// this key only exists if list actually has interests
					/*
					if( isset( $existing_member_data->interests ) ) {
						$existing_interests = (array) $existing_member_data->interests;

						// if replace, assume all existing interests disabled
						if( $replace_interests ) {
							$existing_interests = array_fill_keys( array_keys( $existing_interests ), false );
						}

						// TODO: Use array_replace here (PHP 5.3+)
						$new_interests = $args['interests'];
						$args['interests'] = $existing_interests;
						foreach( $new_interests as $interest_id => $interest_status ) {
							$args['interests']["{$interest_id}"] = $interest_status;
						}
					*/
				} elseif( $existing_member_data->data->status == 'unsubscribed' ) {

					//Must contact to re-subscribe
					$this->error_code = 214;
					$this->error_message = 'Email address previously unsubscribed. Please contact us to subscribe again.';
					return null;
		

					$args['status'] = 'unsubscribed';
				} elseif ( $existing_member_data->data->status == 'blacklisted' ) {
	
					//Must contact to look into blacklisting issue
					$this->error_code = 214;
					$this->error_message = 'Previously not able to send successfully to this email. Please contact us to subscribe again.';
					return null;

					$args['status'] = 'blacklisted';
				} else {
					// If previously subscribed but didn't confirm then delete list member so we can re-add it...
					$this->get_api()->delete_list_member( $list_id, $subscriber_uid );

					$args['status'] = 'unconfirmed';
				}
			} 
		} catch( MB4WP_API_Resource_Not_Found_Exception $e ) {
			// subscriber does not exist (not an issue in this case)
		} catch( MB4WP_API_Exception $e ) {
			// other errors.
			$this->error_code = $e->getCode();
			$this->error_message = $e;
			return null;
		}

		//If existing confirmed subscriber then update, otherwise add
		if ( $args['status'] == 'confirmed' ) {
			try {
				$data = $this->get_api()->update_list_member( $list_id, $subscriber_uid, $args );
			} catch ( MB4WP_API_Exception $e ) {
				$this->error_code = $e->getCode();
				$this->error_message = $e;
				return null;
			}
		} else {
			try {
				$data = $this->get_api()->add_list_member( $list_id, $args );
			} catch ( MB4WP_API_Exception $e ) {
				$this->error_code = $e->getCode();
				$this->error_message = $e;
				return null;
			}
		}	

		$data->was_already_on_list = $already_on_list;

		return $data;
	}

	/**
	* Changes the subscriber status to "unsubscribed" 
	*
	* @param string $list_id
	* @param string $email_address
	*
	* @return boolean
	*/
	public function list_unsubscribe( $list_id, $email_address ) {
		$this->reset_error();

		try {
			$this->get_api()->update_list_member( $list_id, $email_address, array( 'status' => 'unsubscribed' ) );
		} catch( MB4WP_API_Resource_Not_Found_Exception $e ) {
			// if email wasn't even on the list: great.
			return true;
		} catch( MB4WP_API_Exception $e ) {
			$this->error_code = $e->getCode();
			$this->error_message = $e;
			return false;
		}

		return true;
	}

	/**
	* Deletes the subscriber from the given list.
	*
	* @param string $list_id
	* @param string $email_address
	*
	* @return boolean
	*/
	public function list_unsubscribe_delete( $list_id, $email_address ) {
		$this->reset_error();

		try {
			$this->get_api()->delete_list_member( $list_id, $email_address );
		} catch( MB4WP_API_Resource_Not_Found_Exception $e ) {
			// if email wasn't even on the list: great.
			return true;
		} catch( MB4WP_API_Exception $e ) {
			$this->error_code = $e->getCode();
			$this->error_message = $e;
			return false;
		}

		return true;
	}

	/**
	* Checks if an email address is on a given list with status "subscribed"
	*
	* @param string $list_id
	* @param string $email_address
	*
	* @return boolean
	*/
	public function list_has_subscriber( $list_id, $email_address ) {
		try{
			$data = $this->get_api()->get_list_member( $list_id, $email_address );
		} catch( MB4WP_API_Resource_Not_Found_Exception $e ) {
			return false;
		}

		return ! empty( $data->id ) && $data->status === 'subscribed';
	}


	/**
	* Empty the Lists cache
	*/
	public function empty_cache() {
		global $wpdb;

		delete_option( 'mb4wp_mailblaze_list_ids' );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mb4wp_mailblaze_list_%'" );
		delete_transient( 'mb4wp_list_counts' );
	}

	/**
	* Get MailBlaze lists from cache.
	*
	* @param boolean deprecated parameter.
	* @return array
	*/
	public function get_cached_lists() {
		return $this->get_lists( false );
	}

	/**
	* Get a specific MailBlaze list from local DB.
	*
	* @param string $list_id
	* @return MB4WP_MailBlaze_List
	*/
	public function get_cached_list( $list_id ) {
		return $this->get_list( $list_id, false );
	}

	/**
	* Get MailBlaze lists, from cache or remote API.
	*
	* @param boolean $force Whether to force a result by hitting MailBlaze API
	* @return array
	*/
	public function get_lists( $force = true ) {

		// first, get all list id's
		$list_ids = $this->get_list_ids( $force );

		// then, fill $lists array with individual list details
		$lists = array();
		foreach( $list_ids as $list_id ) {
			$list = $this->get_list( $list_id, $force );
			if ($list->merge_fields) {
				foreach ($list->merge_fields as $field) {
					if ($field->field_type == 'checkbox') {
						$field->acceptsMultipleValues = true;
					}
				}
			}
			$lists["{$list_id}"] = $list;
		}

		return $lists;
	}

	/**
	* @param string $list_id
	*
	* @return MB4WP_MailBlaze_List
	*/
	private function fetch_list( $list_id ) {
		try{
			$list_data = $this->get_api()->get_list( $list_id);

			// create local object
			$list = new MB4WP_MailBlaze_List( $list_data->list_uid, $list_data->name );
			$list->subscriber_count = $list_data->subscriber_count;
			//$list->web_id = $list_data->web_id;
			//$list->campaign_defaults = $list_data->campaign_defaults;

			// get merge fields
				$field_data = $this->get_api()->get_list_merge_fields( $list->id );

				// hydrate data into object
				foreach( $field_data as $data ) {
					//Make sure not to record EMAIL twice or the WC_COUPON_CODE is automatically created and hidden so we don't want to
					if( $data->tag != "EMAIL" ) { 
						$object = MB4WP_MailBlaze_Merge_Field::from_data( $data );
						$list->merge_fields[] = $object;
					}
				}

			/*
			// get interest categories
			$interest_categories_data = $this->get_api()->get_list_interest_categories( $list->id, array( 'count' => 100, 'fields' => 'categories.id,categories.title,categories.type' ) );
			foreach( $interest_categories_data as $interest_category_data ) {
				$interest_category = MB4WP_MailBlaze_Interest_Category::from_data( $interest_category_data );

				// fetch groups for this interest
				$interests_data = $this->get_api()->get_list_interest_category_interests( $list->id, $interest_category->id, array( 'count' => 100, 'fields' => 'interests.id,interests.name') );
				foreach( $interests_data as $interest_data ) {
					$interest_category->interests[ (string) $interest_data->id ] = $interest_data->name;
				}

				$list->interest_categories[] = $interest_category;
			}
			*/

		} catch( MB4WP_API_Exception $e ) {
			return null;
		}

		// save in option
		update_option( 'mb4wp_mailblaze_list_' . $list_id, $list, false );

		
		return $list;
	}

	/**
	* Get MailBlaze list ID's
	*
	* @param bool $force Force result by hitting MailBlaze API
	* @return array
	*/
	public function get_list_ids( $force = false ) {
		$list_ids = (array) get_option( 'mb4wp_mailblaze_list_ids', array() );

		if( empty( $list_ids ) && $force ) {
			$list_ids = $this->fetch_list_ids();
		}

		return $list_ids;
	}

	/**
	* @return array
	*/
	public function fetch_list_ids() {
		try{
			$lists_data = $this->get_api()->get_lists();
		} catch( MB4WP_API_Exception $e ) {
			return array();
		}

		//get the list IDs
		foreach ($lists_data as $list_data) {
			$list_ids[] = $list_data->general->list_uid;
		}

		// store list id's
		update_option( 'mb4wp_mailblaze_list_ids', $list_ids, false );

		return $list_ids;
	}

	/**
	* Fetch list ID's + lists from MailBlaze.
	*
	* @return bool
	*/
	public function fetch_lists() {
		// try to increase time limit as this can take a while
		@set_time_limit(300);
		$list_ids = $this->fetch_list_ids();

		// randomize array order
		shuffle( $list_ids );

		// fetch individual list details
		foreach ( $list_ids as $list_id ) {
			$list = $this->fetch_list( $list_id );
		}

		return ! empty( $list_ids );
	}

	/**
	* Get a given MailBlaze list
	*
	* @param string $list_id
	* @param bool $force Whether to force a result by hitting remote API
	* @return MB4WP_MailBlaze_List
	*/
	public function get_list( $list_id, $force = false ) {
		$list = get_option( 'mb4wp_mailblaze_list_' . $list_id );

		if( empty( $list ) && $force ) {
			$list = $this->fetch_list( $list_id );
		}

		if( empty( $list ) ) {
			return new MB4WP_MailBlaze_List( $list_id, 'Unknown List' );
		}

		return $list;
	}

	/**
	* Get an array of list_id => number of subscribers
	*
	* @return array
	*/
	public function get_subscriber_counts() {

		// get from transient
		$list_counts = get_transient( 'mb4wp_list_counts' );
		if( is_array( $list_counts ) ) {
			return $list_counts;
		}

		// transient not valid, fetch from API
		try {
			$lists = $this->get_api()->get_lists();
		} catch( MB4WP_API_Exception $e ) {
			return array();
		}

		$list_counts = array();

		// we got a valid response
		foreach ( $lists as $list ) {
			$list_counts["{$list->id}"] = $list->general->subscriber_count;
		}

		$seconds = 3600;

		/**
		* Filters the cache time for MailBlaze lists configuration, in seconds. Defaults to 3600 seconds (1 hour).
		*
		* @since 2.0
		* @param int $seconds
		*/
		$transient_lifetime = (int) apply_filters( 'mb4wp_lists_count_cache_time', $seconds );
		set_transient( 'mb4wp_list_counts', $list_counts, $transient_lifetime );

		// bail
		return $list_counts;
	}


	/**
	* Returns number of subscribers on given lists.
	*
	* @param array|string $list_ids Array of list ID's, or single string.
	* @return int Total # subscribers for given lists.
	*/
	public function get_subscriber_count( $list_ids ) {

		// make sure we're getting an array
		if( ! is_array( $list_ids ) ) {
			$list_ids = array( $list_ids );
		}

		// if we got an empty array, return 0
		if( empty( $list_ids ) ) {
			return 0;
		}

		// get total number of subscribers for all lists
		$counts = $this->get_subscriber_counts();

		// start calculating subscribers count for all given list ID's combined
		$count = 0;
		foreach ( $list_ids as $id ) {
			$count += ( isset( $counts["{$id}"] ) ) ? $counts["{$id}"] : 0;
		}

		/**
		* Filters the total subscriber_count for the given List ID's.
		*
		* @since 2.0
		* @param string $count
		* @param array $list_ids
		*/
		return apply_filters( 'mb4wp_subscriber_count', $count, $list_ids );
	}

	/**
	* Creates a new list field. 
	*
	*/
	public function create_list_field($list_uid, $type_id, $label, $tag, array $args = array()) {
		$default_args = [
			'type_id' => $type_id,
			'label' => $label,
			'tag' => $tag,
		];

		$args = $args + $default_args;

		try {
			$create_new_field = $this->get_api()->add_list_field( $list_uid, $args );

			if( $create_new_field->status == 'success' ) {
				$args['status'] = 'confirmed';
			}
		} catch( MB4WP_API_Resource_Not_Found_Exception $e ) {
			// subscriber does not exist (not an issue in this case)
			$ErrorReturned = json_decode($e->response['body']);
			if ($ErrorReturned->exists != true){
				//Give the user an alert as to what happened when trying to create the field
				return $ErrorReturned->error;
			}else{
				return "Success";
			}
			
		} catch( MB4WP_API_Exception $e ) {			
			// other errors.
			$ErrorReturned = json_decode($e->response['body']);
			return $ErrorReturned->error;
		}
		
		return "Success";

	}

	/**
	* Resets error properties.
	*/
	public function reset_error() {
		$this->error_message = '';
		$this->error_code = '';
	}

	/**
	* @return bool
	*/
	public function has_error() {
		return ! empty( $this->error_code );
	}

	/**
	* @return string
	*/
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	* @return string
	*/
	public function get_error_code() {
		return $this->error_code;
	}

	/**
	* @return MB4WP_API
	*/
	private function get_api() {
		return mb4wp( 'api' );
	}

}
