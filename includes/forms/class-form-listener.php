<?php

/**
 * Class MB4WP_Form_Listener
 *
 * @since 3.0
 * @access private
 */
class MB4WP_Form_Listener {

	/**
	 * @var MB4WP_Form The submitted form instance
	 */
	public $submitted_form;

	public function add_hooks() {
		add_action( 'init', array( $this, 'listen' ) );
	}

	/**
	 * Listen for submitted forms
	 * @return bool
	 */
	public function listen() {
		if( empty( $_POST['_mb4wp_form_id'] ) ) {
			return false;
		}

		// get form instance
		try {
			$form_id = (int) $_POST['_mb4wp_form_id'];
			$form = mb4wp_get_form( $form_id );
		} catch( Exception $e ) {
			return false;
		}

		// sanitize request data
		$request_data = $_POST;
		$request_data = mb4wp_sanitize_deep( $request_data );
		$request_data = stripslashes_deep( $request_data );

		// bind request to form & validate
		$form->handle_request( $request_data );
		$form->validate();

		// store submitted form
		$this->submitted_form = $form;

		// did form have errors?
		if( ! $form->has_errors() ) {
			switch( $form->get_action() ) {
				case "subscribe":
					$result = $this->process_subscribe_form( $form );
				break;

				case "unsubscribe":
					$result = $this->process_unsubscribe_form( $form );
				break;
			}
		} else {
			foreach( $form->errors as $error_code ) {
				$form->add_notice( $form->get_message( $error_code ), 'error' );
			}

			$this->get_log()->info( sprintf( "Form %d > Submitted with errors: %s", $form->ID, join( ', ', $form->errors ) ) );
		}

		$this->respond( $form );
		return true;
	}

	/**
	 * Process a subscribe form.
	 *
	 * @param MB4WP_Form $form
	 */
	public function process_subscribe_form( MB4WP_Form $form ) {
		$result = false;
		$mailblaze = new MB4WP_MailBlaze();
		$email_type = $form->get_email_type();
		$data = $form->get_data();
		$ip_address = mb4wp_get_request_ip_address();		

		/** @var MB4WP_MailBlaze_Subscriber $subscriber */
		$subscriber = null;

		/**
		 * @ignore
		 * @deprecated 4.0
		 */
		$data = apply_filters( 'mb4wp_merge_vars', $data );

		/**
		 * @ignore
		 * @deprecated 4.0
		 */
		$data = (array) apply_filters( 'mb4wp_form_merge_vars', $data, $form );

		// create a map of all lists with list-specific data
		$mapper = new MB4WP_List_Data_Mapper( $data, $form->get_lists() );

		/** @var MB4WP_MailBlaze_Subscriber[] $map */
		$map = $mapper->map();

		$add_coupon_wc = true;

		// loop through lists
		foreach( $map as $list_id => $subscriber ) {
			$subscriber->status = 'subscribed';
			$subscriber->email_type = $email_type;
			$subscriber->IP_ADDRESS = $ip_address;
			
			/**
			 * Filters subscriber data before it is sent to MailBlaze. Fires for both form & integration requests.
			 *
			 * @param MB4WP_MailBlaze_Subscriber $subscriber
			 */
			$subscriber = apply_filters( 'mb4wp_subscriber_data', $subscriber );

			/**
			 * Filters subscriber data before it is sent to MailBlaze. Only fires for form requests.
			 *
			 * @param MB4WP_MailBlaze_Subscriber $subscriber
			 */
			$subscriber = apply_filters( 'mb4wp_form_subscriber_data', $subscriber );

			// send a subscribe request to MailBlaze for each list			
			$result = $mailblaze->list_subscribe( $list_id, $subscriber->email_address, $subscriber->to_array(), $form->settings['update_existing'], $form->settings['replace_interests'] );
			if( is_object($result) && isset($result->was_already_on_list) && $result->was_already_on_list ) {
				$add_coupon_wc = false;
			}
		}


		//We need to check here if they have coupons enabled, and if they have unique codes selected. 		
		if (($form->settings['coupon_enabled'] == true)&&($form->settings['unique_coupon'] == true)){
			// The coupon functionality is enabled and they have choosen a unique code. 
			//We need to create a new coupon code. 			

			if ((!empty($data['WC_COUPON_CODE']))&&( $add_coupon_wc == true )) {
				$copy_coupon = new WC_Coupon( $form->settings['model_coupon'] );

				$clone_result = $this->clone_coupon($data['WC_COUPON_CODE'], $copy_coupon);
			}
			
			
		}
		

		$log = $this->get_log();

		// do stuff on failure
		if( ! is_object( $result ) || ! isset($result->status) || ( $result->status != "success" ) ) {

			$error_code = $mailblaze->get_error_code();
			$error_message = $mailblaze->get_error_message();

			if( $mailblaze->get_error_code() == 214 ) {
				$form->add_error( 'already_subscribed' );
				$form->add_notice( $form->messages['already_subscribed'], 'notice' );
				$log->warning( sprintf( "Form %d > %s is already subscribed to the selected list(s)", $form->ID, $data['EMAIL'] ) );
			} else {
				$form->add_error( $error_code );
				$form->add_notice( $form->messages['error'], 'error' );
				$log->error( sprintf( 'Form %d > MailBlaze API error: %s %s', $form->ID, $error_code, $error_message ) );

				/**
				 * Fire action hook so API errors can be hooked into.
				 *
				 * @param MB4WP_Form $form
				 * @param string $error_message
				 */
				do_action( 'mb4wp_form_api_error', $form, $error_message );
			}

			// bail
			return;
		}

		// Success! Did we update or newly subscribe?
		if( is_object($result) && isset($result->was_already_on_list) && $result->was_already_on_list ) {
			$form->last_event = 'updated_subscriber';
			$form->add_notice( $form->messages['updated'], 'success' );
			$log->info( sprintf( "Form %d > Successfully updated %s", $form->ID, $data['EMAIL'] ) );

			/**
			 * Fires right after a form was used to update an existing subscriber.
			 *
			 * @since 3.0
			 *
			 * @param MB4WP_Form $form Instance of the submitted form
			 * @param string $email
			 * @param array $data
			 */
			do_action( 'mb4wp_form_updated_subscriber', $form, $subscriber->email_address, $data );
		} elseif (is_object($result) && isset($result->data) && isset($result->data->record) && isset($result->data->record->status) && $result->data->record->status == "confirmed") {
			$form->last_event = 'subscribed';
			$form->add_notice( $form->messages['subscribed'], 'success' );
			$log->info( sprintf( "Form %d > Successfully subscribed %s", $form->ID, $data['EMAIL'] ) );
		} elseif (is_object($result) && isset($result->data) && isset($result->data->record) && isset($result->data->record->status) && $result->data->record->status == "unconfirmed") {
			$form->last_event = 'pending';
			$form->add_notice( $form->messages['pending'], 'success' );
			$log->info( sprintf( "Form %d > Please check your email to confirm subscription for %s", $form->ID, $data['EMAIL'] ) );
		}

		/**
		 * Fires right after a form was used to add a new subscriber (or update an existing one).
		 *
		 * @since 3.0
		 *
		 * @param MB4WP_Form $form Instance of the submitted form
		 * @param string $email
		 * @param array $data
		 * @param MB4WP_MailBlaze_Subscriber[] $subscriber
		 */
		do_action( 'mb4wp_form_subscribed', $form, $subscriber->email_address, $data, $map );
	}

	/**
	 * @param MB4WP_Form $form
	 */
	public function process_unsubscribe_form( MB4WP_Form $form ) {

		$mailblaze = new MB4WP_MailBlaze();
		$log = $this->get_log();
		$result = null;
		$data = $form->get_data();

		// unsubscribe from each list
		foreach( $form->get_lists() as $list_id ) {
			$result = $mailblaze->list_unsubscribe( $list_id, $data['EMAIL'] );
		}

		if( ! $result ) {
			$form->add_notice( $form->messages['error'], 'error' );
			$log->error( sprintf( 'Form %d > MailBlaze API error: %s', $form->ID, $mailblaze->get_error_message() ) );

			// bail
			return;
		}

		// Success! Unsubscribed.
		$form->last_event = 'unsubscribed';
		$form->add_notice( $form->messages['unsubscribed'], 'notice' );
		$log->info( sprintf( "Form %d > Successfully unsubscribed %s", $form->ID, $data['EMAIL'] ) );


		/**
		 * Fires right after a form was used to unsubscribe.
		 *
		 * @since 3.0
		 *
		 * @param MB4WP_Form $form Instance of the submitted form.
		 * @param string $email
		 */
		do_action( 'mb4wp_form_unsubscribed', $form, $data['EMAIL'] );
	}

	/**
	 * @param MB4WP_Form $form
	 */
	public function respond( MB4WP_Form $form ) {

		$success = ! $form->has_errors();

		if( $success ) {

			/**
			 * Fires right after a form is submitted without any errors (success).
			 *
			 * @since 3.0
			 *
			 * @param MB4WP_Form $form Instance of the submitted form
			 */
			do_action( 'mb4wp_form_success', $form );

		} else {

			/**
			 * Fires right after a form is submitted with errors.
			 *
			 * @since 3.0
			 *
			 * @param MB4WP_Form $form The submitted form instance.
			 */
			do_action( 'mb4wp_form_error', $form );

			// fire a dedicated event for each error
			foreach( $form->errors as $error ) {

				/**
				 * Fires right after a form was submitted with errors.
				 *
				 * The dynamic portion of the hook, `$error`, refers to the error that occurred.
				 *
				 * Default errors give us the following possible hooks:
				 *
				 * - mb4wp_form_error_error                     General errors
				 * - mb4wp_form_error_spam
				 * - mb4wp_form_error_invalid_email             Invalid email address
				 * - mb4wp_form_error_already_subscribed        Email is already on selected list(s)
				 * - mb4wp_form_error_required_field_missing    One or more required fields are missing
				 * - mb4wp_form_error_no_lists_selected         No MailBlaze lists were selected
				 *
				 * @since 3.0
				 *
				 * @param   MB4WP_Form     $form        The form instance of the submitted form.
				 */
				do_action( 'mb4wp_form_error_' . $error, $form );
			}

		}

		/**
		 * Fires right before responding to the form request.
		 *
		 * @since 3.0
		 *
		 * @param MB4WP_Form $form Instance of the submitted form.
		 */
		do_action( 'mb4wp_form_respond', $form );

		// do stuff on success (non-AJAX only)
		if( $success && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

			// do we want to redirect?
			$redirect_url = $form->get_redirect_url();
			if ( ! empty( $redirect_url ) ) {
				wp_redirect( $redirect_url );
				exit;
			}
		}
	}

	protected function clone_coupon($CouponCode, $CopyCoupon){
		//First let's make sure that the coupon does not yet exist. 
		$new_coupon = new WC_Coupon();
				
		$new_coupon_id = $new_coupon->get_id();
		$new_coupon = $CopyCoupon;
		$new_coupon->set_code($CouponCode);
		$new_coupon->set_id($new_coupon_id);
		$new_coupon->save();

		if (!empty($new_coupon->errors))
			return "Failure";
		
		return "Success";
	}

	/**
	 * @return MB4WP_API
	 */
	protected function get_api() {
		return mb4wp('api');
	}

	/**
	 * @return MB4WP_Debug_Log
	 */
	protected function get_log() {
		return mb4wp('log');
	}

}
