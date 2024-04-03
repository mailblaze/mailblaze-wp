<?php

/**
 * Class MB4WP_Forms_Admin
 *
 * @ignore
 * @access private
 */
class MB4WP_Forms_Admin {

	/**
	 * @var MB4WP_Admin_Messages
	 */
	protected $messages;

	/**
	 * @var MB4WP_MailBlaze
	 */
	protected $mailblaze;

	/**
	 * @param MB4WP_Admin_Messages $messages
	 * @param MB4WP_MailBlaze $mailblaze
	 */
	public function __construct( MB4WP_Admin_Messages $messages, MB4WP_MailBlaze $mailblaze ) {
		$this->messages = $messages;
		$this->mailblaze = $mailblaze;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'register_shortcode_ui', array( $this, 'register_shortcake_ui' ) );
		add_action( 'mb4wp_save_form', array( $this, 'update_form_stylesheets' ) );
		add_action( 'mb4wp_admin_edit_form', array( $this, 'process_save_form' ) );
		add_action( 'mb4wp_admin_add_form', array( $this, 'process_add_form' ) );
		add_filter( 'mb4wp_admin_menu_items', array( $this, 'add_menu_item' ), 5 );
		add_action( 'mb4wp_admin_show_forms_page-edit-form', array( $this, 'show_edit_page' ) );
		add_action( 'mb4wp_admin_show_forms_page-add-form', array( $this, 'show_add_page' ) );
		add_action( 'mb4wp_admin_enqueue_assets', array( $this, 'enqueue_assets' ), 10, 2 );
	}

	/**
	 * @param string $suffix
	 * @param string $page
	 */
	public function enqueue_assets( $suffix, $page = '' ) {

		if( $page !== 'forms' || empty( $_GET['view'] ) || $_GET['view'] !== 'edit-form' ) {
			return;
		}

		wp_register_script( 'mb4wp-forms-admin', MB4WP_PLUGIN_URL . 'assets/js/forms-admin' . $suffix . '.js', array( 'mb4wp-admin' ), MB4WP_VERSION, true );
		wp_enqueue_script( 'mb4wp-forms-admin');
		wp_localize_script( 'mb4wp-forms-admin', 'mb4wp_forms_i18n', array(
			'addToForm'     => __( "Add to form", 'mailblaze-for-wp' ),
			'agreeToTerms' => __( "I have read and agree to the terms & conditions", 'mailblaze-for-wp' ),
			'agreeToTermsShort' => __( "Agree to terms", 'mailblaze-for-wp' ),
			'agreeToTermsLink' => __( 'Link to your terms & conditions page', 'mailblaze-for-wp' ),
			'city'          => __( 'City', 'mailblaze-for-wp' ),
			'checkboxes'    => __( 'Checkboxes', 'mailblaze-for-wp' ),
			'choices'       => __( 'Choices', 'mailblaze-for-wp' ),
			'choiceType'    => __( "Choice type", 'mailblaze-for-wp' ),
			'chooseField'   => __( "Choose a field to add to the form", 'mailblaze-for-wp' ),
			'close'         => __( 'Close', 'mailblaze-for-wp' ),
			'country'       => __( 'Country', 'mailblaze-for-wp' ),
			'dropdown'      => __( 'Dropdown', 'mailblaze-for-wp' ),
            'fieldType'     => __( 'Field type', 'mailblaze-for-wp' ),
			'fieldLabel'    => __( "Field label", 'mailblaze-for-wp' ),
			'formAction'    => __( 'Form action', 'mailblaze-for-wp' ),
			'formActionDescription' => __( 'This field will allow your visitors to choose whether they would like to subscribe or unsubscribe', 'mailblaze-for-wp' ),
			'formFields'    => __( 'Form fields', 'mailblaze-for-wp' ),
            'forceRequired' => __( 'This field is marked as required in MailBlaze.', 'mailblaze-for-wp' ),
            'initialValue'  		=> __( "Initial value", 'mailblaze-for-wp' ),
            'interestCategories'    => __( 'Interest categories', 'mailblaze-for-wp' ),
			'isFieldRequired' => __( "Is this field required?", 'mailblaze-for-wp' ),
			'listChoice'    => __( 'List choice', 'mailblaze-for-wp' ),
			'listChoiceDescription' => __( 'This field will allow your visitors to choose a list to subscribe to.', 'mailblaze-for-wp' ),
            'listFields'    => __( 'List fields', 'mailblaze-for-wp' ),
			'min'           => __( 'Min', 'mailblaze-for-wp' ),
			'max'           => __( 'Max', 'mailblaze-for-wp' ),
			'noAvailableFields' => __( 'No available fields. Did you select a MailBlaze list in the form settings?', 'mailblaze-for-wp' ),
			'optional' 		=> __( 'Optional', 'mailblaze-for-wp' ),
			'placeholder'   => __( 'Placeholder', 'mailblaze-for-wp' ),
			'placeholderHelp' => __( "Text to show when field has no value.", 'mailblaze-for-wp' ),
			'preselect' 	=> __( 'Preselect', 'mailblaze-for-wp' ),
			'remove' 		=> __( 'Remove', 'mailblaze-for-wp' ),
			'radioButtons'  => __( 'Radio buttons', 'mailblaze-for-wp' ),
			'streetAddress' => __( 'Street Address', 'mailblaze-for-wp' ),
			'state'         => __( 'State', 'mailblaze-for-wp' ),
			'subscribe'     => __( 'Subscribe', 'mailblaze-for-wp' ),
			'submitButton'  => __( 'Submit button', 'mailblaze-for-wp' ),
			'wrapInParagraphTags' => __( "Wrap in paragraph tags?", 'mailblaze-for-wp' ),
			'value'  		=> __( "Value", 'mailblaze-for-wp' ),
			'valueHelp' 	=> __( "Text to prefill this field with.", 'mailblaze-for-wp' ),
			'zip'           => __( 'ZIP', 'mailblaze-for-wp' ),
		));
	}

	/**
	 * @param $items
	 *
	 * @return mixed
	 */
	public function add_menu_item( $items ) {

		$items['forms'] = array(
			'title' => __( 'Forms', 'mailblaze-for-wp' ),
			'text' => __( 'Form', 'mailblaze-for-wp' ),
			'slug' => 'forms',
			'callback' => array( $this, 'show_forms_page' ),
			'load_callback' => array( $this, 'redirect_to_form_action' ),
			'position' => 10
		);

		return $items;
	}

	/**
	 * Act on the "add form" form
	 */
	public function process_add_form() {

		check_admin_referer( 'add_form', '_mb4wp_nonce' );

		$form_data = $_POST['mb4wp_form'];
		$form_content = include MB4WP_PLUGIN_DIR . 'config/default-form-content.php';

		// Fix for MultiSite stripping KSES for roles other than administrator
		remove_all_filters( 'content_save_pre' );

		$form_id = wp_insert_post(
			array(
				'post_type' => 'mb4wp-form',
				'post_status' => 'publish',
				'post_title' => $form_data['name'],
				'post_content' => $form_content,
			)
		);

        // if settings were passed, save those too.
        if( isset( $form_data['settings'] ) ) {
            update_post_meta( $form_id, '_mb4wp_settings', $form_data['settings'] );
        }

        // set default form ID
        $this->set_default_form_id( $form_id );

		$this->messages->flash( __( "<strong>Success!</strong> Form successfully saved.", 'mailblaze-for-wp' ) );
		wp_redirect( mb4wp_get_edit_form_url( $form_id ) );
		exit;
	}

	/**
	 * Saves a form to the database
	 *
	 * @param array $data
	 * @return int
	 */
	public function save_form( $data ) {
		$keys = array(
			'settings' => array(),
			'messages' => array(),
			'name' => '',
			'content' => ''
		);

		$data = array_merge( $keys, $data );
		$data = $this->sanitize_form_data( $data );

		$post_data = array(
			'post_type'     => 'mb4wp-form',
			'post_status'   => ! empty( $data['status'] ) ? $data['status'] : 'publish',
			'post_title'    => $data['name'],
			'post_content'  => $data['content']
		);

		// if an `ID` is given, make sure post is of type `mb4wp-form`
		if( ! empty( $data['ID'] ) ) {
			$post = get_post( $data['ID'] );

			if( $post instanceof WP_Post && $post->post_type === 'mb4wp-form' ) {
				$post_data['ID'] = $data['ID'];

				// merge new settings  with current settings to allow passing partial data
				$current_settings = get_post_meta( $post->ID, '_mb4wp_settings', true );
				if( is_array( $current_settings ) ) {
					$data['settings'] = array_merge( $current_settings, $data['settings'] );
				}
			}
		}

		// Fix for MultiSite stripping KSES for roles other than administrator
		remove_all_filters( 'content_save_pre' );

		$form_id = wp_insert_post( $post_data );
		update_post_meta( $form_id, '_mb4wp_settings', $data['settings'] );

		// save form messages in individual meta keys
		foreach( $data['messages'] as $key => $message ) {
			update_post_meta( $form_id, 'text_' . $key, $message );
		}

		/**
		 * Runs right after a form is updated.
		 *
		 * @since 3.0
		 *
		 * @param int $form_id
		 */
		do_action( 'mb4wp_save_form', $form_id );

		return $form_id;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function sanitize_form_data( $data ) {

		$raw_data = $data;

		// strip <form> tags from content
		$data['content'] =  preg_replace( '/<\/?form(.|\s)*?>/i', '', $data['content'] );

		// replace lowercased name="name" to prevent 404
		$data['content'] = str_ireplace( ' name=\"name\"', ' name=\"NAME\"', $data['content'] );

		// sanitize text fields
		$data['settings']['redirect'] = sanitize_text_field( $data['settings']['redirect'] );

		// strip tags from messages
		foreach( $data['messages'] as $key => $message ) {
			$data['messages'][$key] = strip_tags( $message, '<strong><b><br><a><script><u><em><i><span><img>' );
		}

		// make sure lists is an array
		if( ! isset( $data['settings']['lists'] ) ) {
			$data['settings']['lists'] = array();
		}

		$data['settings']['lists'] = array_filter( (array) $data['settings']['lists'] );

		/**
		 * Filters the form data just before it is saved.
		 *
		 * @param array $data Sanitized array of form data.
		 * @param array $raw_data Raw array of form data.
		 *
		 * @since 3.0.8
         * @ignore
		 */
		$data = (array) apply_filters( 'mb4wp_form_sanitized_data', $data, $raw_data );

		return $data;
	}

	/**
	 * Saves a form
	 */
	public function process_save_form( ) {
		$failed_post = 0;
		check_admin_referer( 'edit_form', '_mb4wp_nonce' );
		$form_id = (int) $_POST['mb4wp_form_id'];
	

		$form_data = $_POST['mb4wp_form'];
		$form_data['ID'] = $form_id;

		//When we save the form check if they have just enabled the use of coupons. 
		//print_r($this->form->settings['model_coupon']);
		$new_coupon_status = isset($form_data['settings']['coupon_enabled']) ? $form_data['settings']['coupon_enabled'] : false;
		$previous_form_settings = mb4wp_get_form( $form_id )->settings;

		$list_ids = $form_data['settings']['lists'];	
		$previous_form_settings = mb4wp_get_form( $form_id )->settings;
		//Condition is if the coupon form was resaved or if they add a list. 		

		
		if ((($new_coupon_status == 1)&&($previous_form_settings['coupon_enabled'] == 0))||(!empty(array_diff($list_ids, $previous_form_settings['lists'])))){
			//They have just enabled Coupons. Let's make sure their is a field ID in their list!			
			$mailblaze = new MB4WP_MailBlaze();
			
			//We need to loop through lists to create this 				
			$blnAllCreated = true;

			foreach($list_ids as $list_id){
				$result = $mailblaze->create_list_field($list_id, 1, "WooCommerce Coupon", "WC_COUPON_CODE");				

				if ($result != "Success"){	
					$blnAllCreated = false;
					$resultMsg = $result;
				}
			}
			
			if ($blnAllCreated == false){
				//Need to disable the coupons and alert user to what has happened. 
				$this->messages->flash( __( "<strong>Failed to create a new field in Mail Blaze!</strong> " . $resultMsg, 'mailblaze-for-wp' ), "failed" );
				$failed_post = 1;
			}else{
				//Refresh the lists 
				$lists = $mailblaze->fetch_lists();

				if( ! empty( $lists ) ) {
					$this->messages->flash( __( 'Success! The cached configuration for your MailBlaze lists has been renewed.', 'mailblaze-for-wp' ) );
				}
			}
		}
				
		if ($failed_post == 0){
			$this->save_form( $form_data );
			$this->set_default_form_id( $form_id );

			$this->messages->flash( __( "<strong>Success!</strong> Form successfully saved.", 'mailblaze-for-wp' ) );
		}
		
	}

    /**
     * @param int $form_id
     */
	private function set_default_form_id( $form_id ) {
        $default_form_id = (int) get_option( 'mb4wp_default_form_id', 0 );

        if( empty( $default_form_id ) ) {
            update_option( 'mb4wp_default_form_id', $form_id );
        }
    }

	/**
	 * Goes through each form and aggregates array of stylesheet slugs to load.
	 *
	 * @hooked `mb4wp_save_form`
	 */
	public function update_form_stylesheets() {
		$stylesheets = array();

		$forms = mb4wp_get_forms();
		foreach( $forms as $form ) {

			$stylesheet = $form->get_stylesheet();

			if( ! empty( $stylesheet ) && ! in_array( $stylesheet, $stylesheets ) ) {
				$stylesheets[] = $stylesheet;
			}
		}

		update_option( 'mb4wp_form_stylesheets', $stylesheets );
	}

	/**
	 * Redirect to correct form action
	 *
	 * @ignore
	 */
	public function redirect_to_form_action() {

		if( ! empty( $_GET['view'] ) ) {
			return;
		}

		try{
			// try default form first
			$default_form = mb4wp_get_form();
			$redirect_url = mb4wp_get_edit_form_url( $default_form->ID );
		} catch(Exception $e) {
			// no default form, query first available form and go there
			$forms = mb4wp_get_forms( array( 'numberposts' => 1 ) );

			if( $forms ) {
				// if we have a post, go to the "edit form" screen
				$form = array_pop( $forms );
				$redirect_url = mb4wp_get_edit_form_url( $form->ID );
			} else {
				// we don't have a form yet, go to "add new" screen
				$redirect_url = mb4wp_get_add_form_url();
			}
		}

		if( headers_sent() ) {
			echo sprintf( '<meta http-equiv="refresh" content="0;url=%s" />', $redirect_url );
		} else {
			wp_redirect( $redirect_url );
		}

		exit;
	}

	/**
	 * Show the Forms Settings page
	 *
	 * @internal
	 */
	public function show_forms_page() {

		$view = ! empty( $_GET['view'] ) ? $_GET['view'] : '';

		/**
		 * @ignore
		 */
		do_action( 'mb4wp_admin_show_forms_page', $view );

		/**
		 * @ignore
		 */
		do_action( 'mb4wp_admin_show_forms_page-' . $view );
	}

	/**
	 * Show the "Edit Form" page
	 *
	 * @internal
	 */
	public function show_edit_page() {
		$form_id = ( ! empty( $_GET['form_id'] ) ) ? (int) $_GET['form_id'] : 0;
		$lists = $this->mailblaze->get_lists();

		try{
			$form = mb4wp_get_form( $form_id );
		} catch( Exception $e ) {
			echo '<h2>' . __( "Form not found.", 'mailblaze-for-wp' ) . '</h2>';
			echo '<p>' . $e->getMessage() . '</p>';
			echo '<p><a href="javascript:history.go(-1);"> &lsaquo; '. __( 'Go back' ) .'</a></p>';
			return;
		}

		$opts = $form->settings;
		$active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'fields';


		$form_preview_url = add_query_arg( array( 
            'mb4wp_preview_form' => $form_id,
        ), site_url( '/', 'admin' ) );

		require dirname( __FILE__ ) . '/views/edit-form.php';
	}

	/**
	 * Shows the "Add Form" page
	 *
	 * @internal
	 */
	public function show_add_page() {
		$lists = $this->mailblaze->get_lists();
		$number_of_lists = count( $lists );
		require dirname( __FILE__ ) . '/views/add-form.php';
	}

	/**
	 * Get URL for a tab on the current page.
	 *
	 * @since 3.0
	 * @internal
	 * @param $tab
	 * @return string
	 */
	public function tab_url( $tab ) {
		return add_query_arg( array( 'tab' => $tab ), remove_query_arg( 'tab' ) );
	}

	/**
	 * Registers UI for when shortcake is activated
	 */
	public function register_shortcake_ui() {

		$assets = new MB4WP_Form_Asset_Manager();
		$assets->load_stylesheets();

		$forms = mb4wp_get_forms();
		$options = array();
		foreach( $forms as $form ) {
			$options[ $form->ID ] = $form->name;
		}

		/**
		 * Register UI for your shortcode
		 *
		 * @param string $shortcode_tag
		 * @param array $ui_args
		 */
		shortcode_ui_register_for_shortcode( 'mb4wp_form', array(
				'label' => esc_html__( 'MailBlaze Sign-Up Form', 'mailblaze-for-wp' ),
				'listItemImage' => 'dashicons-feedback',
				'attrs' => array(
					array(
						'label'    => esc_html__( 'Select the form to show' ,'mailblaze-for-wp' ),
						'attr'     => 'id',
						'type'     => 'select',
						'options'  => $options
					)
				),
			)
		);
	}
}
