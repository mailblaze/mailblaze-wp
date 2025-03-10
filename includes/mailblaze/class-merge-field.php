<?php

/**
 * Class MB4WP_MailBlaze_Merge_Field
 *
 * Represents a Merge Field in MailBlaze
 *
 * @access public
 */
class MB4WP_MailBlaze_Merge_Field {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $field_type;

	/**
	 * @var string
	 */
	public $tag;

	/**
	 * @var bool Is this a required field for the list it belongs to?
	 */
	public $required = false;

	/**
	 * @var array
	 */
	public $choices = array();

	/**
	 * @var bool Is this field public? As in, should it show on forms?
	 */
	public $public = true;

	/**
	 * @var string Default value for the field.
	 */
	public $default_value = '';

	/**
	 * @param string $name
	 * @param string $field_type
	 * @param string $tag
	 * @param bool $required
	 * @param array $choices
	 */
	public function __construct( $name, $field_type, $tag, $required = false, $choices = array() ) {
		$this->name = $name;
		$this->field_type = $field_type;
		$this->tag = strtoupper( $tag );
		$this->required = $required;
		$this->choices = $choices;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function __get( $name ) {

		// for backwards compatibility with v3.x, channel these properties to their new names
		if( $name === 'default' ) {
			return $this->default_value;
		}
	}

	/**
	 * Creates our local object from MailBlaze API data.
	 *
	 * @param object $data
	 *
	 * @return MB4WP_MailBlaze_Merge_Field
	 */
	public static function from_data( $data ) {

		//check if required
		if ($data->required == "yes") {
			$required = true;
		} else {
			$required = false;
		}

		$instance = new self( $data->label, $data->type->identifier, $data->tag, $required);


		if( ! empty( $data->options ) ) {
			$instance->choices = (array) $data->options;
		}

		return $instance;
	}

}