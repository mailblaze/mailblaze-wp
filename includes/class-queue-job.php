<?php

/**
 * Class MB4WP_Queue_Job
 *
 * @ignore
 */
class MB4WP_Queue_Job {

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var mixed
	 */
	public $data;

	/**
	 * MB4WP_Queue_Job constructor.
	 *
	 * @param $data
	 */
	public function __construct( $data ) {
		$this->id = (string) microtime( true ) . wp_rand( 1, 10000 );
		$this->data = $data;
	}
}