<?php

/**
 * Class MB4WP_Form_Element
 *
 * @since 3.0
 * @ignore
 * @access private
 */
class MB4WP_Form_Element {

	/**
	 * @var string
	 */
	public $ID;

	/**
	 * @var MB4WP_Form
	 */
	public $form;

	/**
	 * @var array
	 *
	 * Can be used to set element-specific config settings. Accepts the following keys.
	 *
	 * - lists: Customized number of MailBlaze list ID's to subscribe to.
	 * - email_type: The email type
	 */
	public $config = array();

	/**
	 * @var bool
	 */
	public $is_submitted = false;

	/**
	 * @param MB4WP_Form $form
	 * @param string $ID
	 * @param $config array
	 */
	public function __construct( MB4WP_Form $form, $ID, $config = array() ) {
		$this->form = $form;
		$this->ID = $ID;
		$this->config = $config;

		$this->is_submitted = $this->form->is_submitted
		                      && $this->form->config['element_id'] == $this->ID;
	}


	/**
	 * @return string
	 */
	protected function get_visible_fields() {

		$content = $this->form->content;
		$form = $this->form;
		$element = $this;

		/**
		 * Filters the HTML for the form fields.
		 *
		 * Use this filter to add custom HTML to a form programmatically
		 *
		 * @param string $content
		 * @param MB4WP_Form $form
		 * @param MB4WP_Form_Element $element
		 * @since 2.0
		 */
		$visible_fields = (string) apply_filters( 'mb4wp_form_content', $content, $form, $element );

		return $visible_fields;
	}

	/**
	 * @return string
	 */
	protected function get_hidden_fields() {

		// hidden fields
		$hidden_fields =  '<label style="display: none !important;">' . __( 'Leave this field empty if you\'re human:', 'mailblaze-for-wp' ) . ' ' . '<input type="text" name="_mb4wp_honeypot" value="" tabindex="-1" autocomplete="off" /></label>';
		$hidden_fields .= '<input type="hidden" name="_mb4wp_timestamp" value="'. time() . '" />';
		$hidden_fields .= '<input type="hidden" name="_mb4wp_form_id" value="'. esc_attr( $this->form->ID ) .'" />';
		$hidden_fields .= '<input type="hidden" name="_mb4wp_form_element_id" value="'. esc_attr( $this->ID ) .'" />';		

		// was "lists" parameter passed in shortcode arguments?
		if( ! empty( $this->config['lists'] ) ) {
			$lists_string = is_array( $this->config['lists'] ) ? join( ',', $this->config['lists'] ) : $this->config['lists'];
			$hidden_fields .= '<input type="hidden" name="_mb4wp_lists" value="'. esc_attr( $lists_string ) . '" />';
		}

		// was "lists" parameter passed in shortcode arguments?
		if( ! empty( $this->config['email_type'] ) ) {
			$hidden_fields .= '<input type="hidden" name="_mb4wp_email_type" value="'. esc_attr( $this->config['email_type'] ) . '" />';
		}

		return (string) $hidden_fields;
	}

	/**
	 * Get HTML string for a notice, including wrapper element.
	 *
	 * @param MB4WP_Form_Notice $notice
	 *
	 * @return string
	 */
	protected function get_notice_html( MB4WP_Form_Notice $notice ) {
		$html = sprintf( '<div class="mb4wp-alert mb4wp-%s"><p>%s</p></div>', esc_attr( $notice->type ), $notice->text );
		return $html;
	}

	/**
	 * Gets the form response string
	 *
	 * @param boolean $force_show
	 * @return string
	 */
	public function get_response_html( $force_show = false ) {

		$html = '';
		$form = $this->form;

		if( $this->is_submitted || $force_show ) {
			foreach( $this->form->notices as $notice ) {
				$html .= $this->get_notice_html( $notice );
			}
		}

		/**
		 * Filter the form response HTML
		 *
		 * Use this to add your own HTML to the form response. The form instance is passed to the callback function.
		 *
		 * @since 3.0
		 *
		 * @param string $html The complete HTML string of the response, excluding the wrapper element.
		 * @param MB4WP_Form $form The form object
		 */
		$html = (string) apply_filters( 'mb4wp_form_response_html', $html, $form );

		// wrap entire response in div, regardless of a form was submitted
		$html = '<div class="mb4wp-response">' . $html . '</div>';
		return $html;
	}

	/**
	 * @return string
	 */
	protected function get_response_position() {

		$position = 'after';
		$form = $this->form;

		// check if content contains {response} tag
		if( stripos( $this->form->content, '{response}' ) !== false ) {
			return '';
		}

		/**
		 * Filters the position for the form response.
		 *
		 * Valid values are "before" and "after". Will have no effect if `{response}` is used in the form content.
		 *
		 * @param string $position
		 * @param MB4WP_Form $form
		 * @since 2.0
		 */
		$response_position = (string) apply_filters( 'mb4wp_form_response_position', $position, $form );

		return $response_position;
	}

	/**
	 * Get HTML to be added _before_ the HTML of the form fields.
	 *
	 * @return string
	 */
	protected function get_html_before_fields() {

		$html = '';
		$form = $this->form;

		/**
		 * Filters the HTML before the form fields.
		 *
		 * @param string $html
		 * @param MB4WP_Form $form
         * @ignore
		 */
		$html = (string) apply_filters( 'mb4wp_form_before_fields', $html, $form );

		if( $this->get_response_position() === 'before' ) {
			$html = $html . $this->get_response_html();
		}

		return $html;
	}

	/**
	 * Get HTML to be added _after_ the HTML of the form fields.
	 *
	 * @return string
	 */
	protected function get_html_after_fields() {

		$html = '';
		$form = $this->form;

		/**
		 * Filters the HTML after the form fields.
		 *
		 * @param string $html
		 * @param MB4WP_Form $form
         * @ignore
		 */
		$html = (string) apply_filters( 'mb4wp_form_after_fields', $html, $form );

		if( $this->get_response_position() === 'after' ) {
			$html = $this->get_response_html() . $html;
		}

		return $html;
	}

	/**
	 * Get all HTMl attributes for the form element
	 *
	 * @return string
	 */
	protected function get_form_element_attributes() {

		$form = $this;
		$form_action_attribute = null;

		$attributes = array(
			'id' => $this->ID,
			'class' => $this->get_css_classes()
		);

		/**
		 * Filters the `action` attribute of the `<form>` element.
		 *
		 * Defaults to `null`, which means no `action` attribute will be printed.
		 *
		 * @param string $form_action_attribute
		 * @param MB4WP_Form $form
		 */
		$form_action_attribute = apply_filters( 'mb4wp_form_action', $form_action_attribute, $form );
		if( is_string( $form_action_attribute ) ) {
			$attributes['action'] = $form_action_attribute;
		}

		/**
		 * Filters all attributes to be added to the `<form>` element
		 *
		 * @param array $attributes Key-value pairs of attributes.
		 * @param MB4WP_Form $form
		 */
		$attributes = (array) apply_filters( 'mb4wp_form_element_attributes', $attributes, $form );

		// hardcoded attributes, can not be changed.
		$attributes['method'] = 'post';
		$attributes['data-id'] = $this->form->ID;
		$attributes['data-name'] = $this->form->name;

		// build string of key="value" from array
		$string = '';
		foreach( $attributes as $name => $value ) {
			$string .= sprintf( '%s="%s" ', $name, esc_attr( $value ) );
		}

		return $string;
	}

	/**
	 * @param array|null $config Use this to override the configuration for this form element
	 * @return string
	 */
	public function generate_html( array $config = null ) {

		if( $config ) {
			$this->config = $config;
		}

		// Start building content string
		$opening_html = '<!-- MailBlaze for WordPress v' . MB4WP_VERSION . ' - https://wordpress.org/plugins/mailblaze-for-wp/ -->';
		$opening_html .= '<form '. $this->get_form_element_attributes() .'>';
		$before_fields = $this->get_html_before_fields();
		$fields = '';
		$after_fields = $this->get_html_after_fields();				
		$closing_html = '</form><!-- / MailBlaze for WordPress Plugin -->';

		if( ! $this->is_submitted
		    || ! $this->form->settings['hide_after_success']
			|| $this->form->has_errors()) {

			// add HTML for fields + wrapper element.
			$fields = '<div class="mb4wp-form-fields">' .
			    $this->get_visible_fields() .
				'</div>' . 
				$this->get_hidden_fields();
		}

		$fields = ($this->form->settings['recaptcha_enabled']) ? $this->get_recaptcha($fields) : $fields;	
		
		$coupon_field = ($this->form->settings['coupon_enabled']) ? $this->get_coupon_field($this->form->settings['unique_coupon']) : "";	

		// concatenate everything
		$output = $opening_html .
		          $before_fields .
		          $fields .
				  $coupon_field .
		          $after_fields .
		          $closing_html;

		return $output;
	}

	/**
	 * Get a space separated list of CSS classes for this form
	 *
	 * @return string
	 */
	protected function get_css_classes() {

		$classes = array();
		$form = $this->form;

		$classes[] = 'mb4wp-form';
		$classes[] = 'mb4wp-form-' . $form->ID;

		// Add form classes if this specific form element was submitted
		if( $this->is_submitted ) {
			$classes[] = 'mb4wp-form-submitted';

			if( ! $form->has_errors() ) {
				$classes[] = 'mb4wp-form-success';
			} else {
				$classes[] = 'mb4wp-form-error';
			}
		}

		// add class for CSS targeting in custom stylesheets
		if( ! empty( $form->settings['css'] ) ) {

			if( strpos( $form->settings['css'], 'theme-' ) === 0 ) {
				$classes[] = 'mb4wp-form-theme';
			}

			$classes[] = 'mb4wp-form-' . $form->settings['css'];
		}

		// add classes from config array
		if( ! empty( $this->config['element_class'] ) ) {
		    $classes = array_merge( $classes, explode( ' ', $this->config['element_class'] ) );
        }

		/**
		 * Filters `class` attributes for the `<form>` element.
		 *
		 * @param array $classes
		 * @param MB4WP_Form $form
		 */
		$classes = apply_filters( 'mb4wp_form_css_classes', $classes, $form );

		return implode( ' ', $classes );
	}

	protected function get_recaptcha($form_data){
		wp_register_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js');
		wp_enqueue_script( 'google-recaptcha' );

		$dom = new DOMDocument();		

		$dom->loadHTML($form_data);
		$xpath = new DOMXPath($dom);

		$input = $xpath->query('//input[@type="submit"]')->item(0);
				
		$input->setAttribute('class', (!empty($input->getAttribute('class')) ? $input->getAttribute('class') . " " : "") . 'g-recaptcha');		
		$input->setAttribute('data-sitekey', (!empty($input->getAttribute('data-sitekey')) ? $input->getAttribute('data-sitekey') . " " : "") . $this->form->settings['site_key']);		
		$input->setAttribute('data-callback', (!empty($input->getAttribute('data-callback')) ? $input->getAttribute('data-callback') . " " : "") . 'onSubmitMbSignup');
		$input->setAttribute('data-action', (!empty($input->getAttribute('data-action')) ? $input->getAttribute('data-action') . " " : "") . 'submit');

		//Get the ID for the button
		if (!empty($input->getAttribute('id'))){
			$submitID = $input->getAttribute('id');
		}else{
			$submitID = 'mb-subscribe-button';
			$input->setAttribute('id', $submitID);
		}						
						
		$outputHTML = $dom->saveHTML();


			
		//Append the scrip to the end
		$outputHTML .= '<script>
			function onSubmitMbSignup(token) {
			document.getElementById("'.$this->ID.'").submit();
			}
	  	</script>';		

		return $outputHTML;
	}

	protected function get_coupon_field($blnUniqueCode) {
		if ($blnUniqueCode == true){
			// We need to generate a unique code each time the form is loaded
			$coupon_code = $this->generate_unique_coupon_code(); 
		}else{
			$coupon_id = $this->form->settings['model_coupon'];
			$coupon_data = $this->get_coupon($coupon_id);

			$coupon_code = ($coupon_data['code']);			
		}

		$outputHTML = '<input type="hidden" name="WC_COUPON_CODE" value="'.$coupon_code.'">';

		return $outputHTML; 
	}

	protected function generate_unique_coupon_code(){
		//Generate the code 
		$uCode = substr(md5(uniqid(wp_rand(), true)), 0, 8);

		$coupon = $this->get_coupon_by_code($uCode);		

		if (empty($coupon->errors)){				
			return $this->generate_unique_coupon_code();
		}

		return $uCode;
	}

	protected function get_coupon( $id, $fields = null ) {		

		$coupon = new WC_Coupon( $id );

		if ( 0 === $coupon->get_id() ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_coupon_id', __( 'Invalid coupon ID', 'woocommerce' ), 404 );
		}		

		$coupon_data = array(
			'id'                           => $coupon->get_id(),
			'code'                         => $coupon->get_code(),
			'type'                         => $coupon->get_discount_type(),
			'created_at'                   => $this->format_datetime( $coupon->get_date_created() ? $coupon->get_date_created()->getTimestamp() : 0 ), // API gives UTC times.
			'updated_at'                   => $this->format_datetime( $coupon->get_date_modified() ? $coupon->get_date_modified()->getTimestamp() : 0 ), // API gives UTC times.
			'amount'                       => wc_format_decimal( $coupon->get_amount(), 2 ),
			'individual_use'               => $coupon->get_individual_use(),
			'product_ids'                  => array_map( 'absint', (array) $coupon->get_product_ids() ),
			'exclude_product_ids'          => array_map( 'absint', (array) $coupon->get_excluded_product_ids() ),
			'usage_limit'                  => $coupon->get_usage_limit() ? $coupon->get_usage_limit() : null,
			'usage_limit_per_user'         => $coupon->get_usage_limit_per_user() ? $coupon->get_usage_limit_per_user() : null,
			'limit_usage_to_x_items'       => (int) $coupon->get_limit_usage_to_x_items(),
			'usage_count'                  => (int) $coupon->get_usage_count(),
			'expiry_date'                  => $this->format_datetime( $coupon->get_date_expires() ? $coupon->get_date_expires()->getTimestamp() : 0 ), // API gives UTC times.
			'enable_free_shipping'         => $coupon->get_free_shipping(),
			'product_category_ids'         => array_map( 'absint', (array) $coupon->get_product_categories() ),
			'exclude_product_category_ids' => array_map( 'absint', (array) $coupon->get_excluded_product_categories() ),
			'exclude_sale_items'           => $coupon->get_exclude_sale_items(),
			'minimum_amount'               => wc_format_decimal( $coupon->get_minimum_amount(), 2 ),
			'customer_emails'              => $coupon->get_email_restrictions(),
		);

		return $coupon_data;
	}

	protected function get_coupon_by_code( $code, $fields = null ) {
		global $wpdb;

		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1;", $code ) );

		if ( is_null( $id ) ) {
			return new WP_Error( 'woocommerce_api_invalid_coupon_code', __( 'Invalid coupon code', 'woocommerce' ), array( 'status' => 404 ) );
		}

		return $this->get_coupon( $id, $fields );
	}

	protected function format_datetime( $timestamp, $convert_to_utc = false, $convert_to_gmt = false ) {
		if ( $convert_to_gmt ) {
			if ( is_numeric( $timestamp ) ) {
				$timestamp = gmdate( 'Y-m-d H:i:s', $timestamp );
			}

			$timestamp = get_gmt_from_date( $timestamp );
		}

		if ( $convert_to_utc ) {
			$timezone = new DateTimeZone( wc_timezone_string() );
		} else {
			$timezone = new DateTimeZone( 'UTC' );
		}

		try {

			if ( is_numeric( $timestamp ) ) {
				$date = new DateTime( "@{$timestamp}" );
			} else {
				$date = new DateTime( $timestamp, $timezone );
			}

			// convert to UTC by adjusting the time based on the offset of the site's timezone
			if ( $convert_to_utc ) {
				$date->modify( -1 * $date->getOffset() . ' seconds' );
			}
		} catch ( Exception $e ) {

			$date = new DateTime( '@0' );
		}

		return $date->format( 'Y-m-d\TH:i:s\Z' );
	}
}
