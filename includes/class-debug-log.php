<?php

/**
 * Class MB4WP_Debug_Log
 *
 * Simple logging class which writes to a file, loosely based on PSR-3.
 */
class MB4WP_Debug_Log{

	/**
	 * Detailed debug information
	 */
	const DEBUG = 100;

	/**
	 * Interesting events
	 *
	 * Examples: Visitor subscribed
	 */
	const INFO = 200;

	/**
	 * Exceptional occurrences that are not errors
	 *
	 * Examples: User already subscribed
	 */
	const WARNING = 300;

	/**
	 * Runtime errors
	 */
	const ERROR = 400;

	/**
	 * Logging levels from syslog protocol defined in RFC 5424
	 *
	 * @var array $levels Logging levels
	 */
	protected static $levels = array(
		self::DEBUG     => 'DEBUG',
		self::INFO      => 'INFO',
		self::WARNING   => 'WARNING',
		self::ERROR     => 'ERROR',
	);

	/**
	 * @var string The file to which messages should be written.
	 */
	public $file;

	/**
	 * @var int Only write messages with this level or higher
	 */
	public $level;

	/**
	 * @var resource
	 */
	protected $stream;

	/**
	 * MB4WP_Debug_Log constructor.
	 *
	 * @param string $file
	 * @param mixed $level;
	 */
	public function __construct( $file, $level = self::DEBUG ) {
		$this->file = $file;
		$this->level = self::to_level( $level );
	}

	/**
	 * @param mixed $level
	 * @param string $message
	 * @return boolean
	 */
	public function log( $level, $message ) {
		// Convert the log level to an integer
		$level = self::to_level( $level );
	
		// Only log if message level is higher than log level
		if( $level < $this->level ) {
			return false;
		}
	
		// Obfuscate email addresses in log message since log might be public
		$message = mb4wp_obfuscate_email_addresses( (string) $message );
	
		// Generate line
		$level_name = self::get_level_name( $level );
		$datetime = gmdate( 'Y-m-d H:i:s', ( time() - gmdate('Z') ) + ( get_option( 'gmt_offset', 0 ) * 3600 ) );
		$message = sprintf( '[%s] %s: %s', $datetime, $level_name, $message ) . PHP_EOL;
	
		// Make sure the filesystem API is available
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
	
		// Initialize the WordPress filesystem
		WP_Filesystem();
	
		// Write the message to the file using the WordPress filesystem API
		global $wp_filesystem;
		$result = $wp_filesystem->put_contents( $this->file, $message, FILE_APPEND );
	
		return $result;
	}
	

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function warning( $message ) {
		return $this->log( self::WARNING, $message );
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function info( $message ) {
		return $this->log( self::INFO, $message );
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function error( $message ) {
		return $this->log( self::ERROR, $message );
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function debug( $message ) {
		return $this->log( self::DEBUG, $message );
	}

	/**
	 * Converts PSR-3 levels to local ones if necessary
	 *
	 * @param string|int Level number or name (PSR-3)
	 * @return int
	 */
	public static function to_level( $level ) {

		if ( is_string( $level ) ) {

			$level = strtoupper( $level );
			if( defined( __CLASS__ . '::' . $level ) ) {
				return constant( __CLASS__ . '::'  . $level );
			}

			throw new InvalidArgumentException( 'Level "' . $level . '" is not defined, use one of: ' . implode( ', ', array_keys( self::$levels ) ) );
		}

		return $level;
	}

	/**
	 * Gets the name of the logging level.
	 *
	 * @param  int    $level
	 * @return string
	 */
	public static function get_level_name( $level ) {

		if ( ! isset( self::$levels[ $level ] ) ) {
			throw new InvalidArgumentException( 'Level "' . $level . '" is not defined, use one of: ' . implode( ', ', array_keys( self::$levels ) ) );
		}

		return self::$levels[ $level ];
	}

	/**
	 * Tests if the log file is writable
	 *
	 * @return bool
	 */
	public function test() {
		// Make sure the filesystem API is available
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Initialize the WordPress filesystem
		WP_Filesystem();
		global $wp_filesystem;

		// Check if the file is writable using the WordPress filesystem API
		$writable = $wp_filesystem->is_writable( $this->file );

		return $writable;
	}

}

