<?php

/**
 * Class MB4WP_Debug_Log_Reader
 */
class MB4WP_Debug_Log_Reader {

	/**
	 * @var resource|null
	 */
	private $handle;

	private $current_line;

	/**
	 * @var string
	 */
	private static $regex = '/^(\[[\d \-\:]+\]) (\w+\:) (.*)$/S';

    /**
     * @var string
     */
    private static $html_template = '<span class="time">$1</span> <span class="level">$2</span> <span class="message">$3</span>';

	/**
	 * @var string The log file location.
	 */
	private $file;

	/**
	 * MB4WP_Debug_Log_Reader constructor.
	 *
	 * @param $file
	 */
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * @return string
	 */
	public function all() {
		global $wp_filesystem;		
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();

		return $wp_filesystem->get_contents( $this->file );
	}

    /**
     * Sets file pointer to $n of lines from the end of file.
     *
     * @param int $n
     */
	private function seek_line_from_end( $n ) {
        $line_count = 0;

        // get line count
        while( ! feof( $this->handle ) ) {
            fgets( $this->handle );
            $line_count++;
        }

        // rewind to beginning
        rewind( $this->handle );

        // calculate target
        $target = $line_count - $n;
        $target = $target > 1 ? $target : 1; // always skip first line because oh PHP header
        $current = 0;

        // keep reading until we're at target
        while( $current < $target ) {
            fgets( $this->handle );
            $current++;
        }
    }

	/**
	 * @return string|null
	 */
	public function read() {
		// Make sure the filesystem API is available
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Initialize the WordPress filesystem
		WP_Filesystem();

		// Check if the file exists
		if ( ! file_exists( $this->file ) ) {
			return null;
		}

		// Read the file using the WordPress filesystem API
		global $wp_filesystem;
		$contents = wp_remote_retrieve_body( $wp_filesystem->get_contents($this->file ) );

		// Check if the contents were retrieved successfully
		if ( empty( $contents ) ) {
			return null;
		}

		// Split the content into lines
		$lines = explode( "\n", $contents );

		// Reverse the array to start reading from the end
		$lines = array_reverse( $lines );

		// Start reading from the end of the file
		$this->current_line = 0;

		// Read each line until we reach a non-empty line or the beginning of the file
		while ( isset( $lines[ $this->current_line ] ) ) {
			$line = trim( wp_strip_all_tags( $lines[ $this->current_line ] ) );
			$this->current_line++;

			if ( ! empty( $line ) ) {
				return $line;
			}
		}

		return null;
	}


	/**
	 * @return string
	 */
	public function read_as_html() {
		$line = $this->read();

        if( is_null( $line ) ) {
            return null;
        }

		$line = preg_replace( self::$regex, self::$html_template, $line );
		return $line;
	}

	/**
	 * Reads X number of lines.
	 *
	 * If $start is negative, reads from end of log file.
	 *
	 * @param int $start
	 * @param int $number
	 * @return string
	 */
	public function lines( $start, $number ) {
		// Initialize WP_Filesystem
		WP_Filesystem();
	
		global $wp_filesystem;
	
		// Check if WP_Filesystem initialization was successful
		if ( ! $wp_filesystem ) {
			// WP_Filesystem initialization failed, handle error
			return false;
		}
	
		// Read the file using WP_Filesystem API
		$contents = $wp_filesystem->get_contents( $start );
	
		if ( $contents === false ) {
			// Error occurred while reading the file, handle error
			return false;
		}
	
		// Explode the contents into lines
		$lines = explode( "\n", $contents );
	
		// Take the first $number lines
		$lines = array_slice( $lines, 0, $number );
	
		// Implode the lines back into a string
		$result = implode( "\n", $lines );
	
		return $result;
	}

}