<?php

class MB4WP_Admin_Ajax {

    /**
     * @var MB4WP_Admin_Tools
     */
    protected $tools;

    /**
     * MB4WP_Admin_Ajax constructor.
     *
     * @param MB4WP_Admin_Tools $tools
     */
    public function __construct( MB4WP_Admin_Tools $tools )
    {
        $this->tools = $tools;
    }

    /**
     * Hook AJAX actions
     */
    public function add_hooks() {
        add_action( 'wp_ajax_mb4wp_renew_mailblaze_lists', array( $this, 'refresh_mailblaze_lists' ) );
    }

    /**
     * Empty lists cache & fetch lists again.
     */
	public function refresh_mailblaze_lists() {
        if( ! $this->tools->is_user_authorized() ) {
            wp_send_json(false);
        }

        $mailblaze = new MB4WP_MailBlaze();
        $success = $mailblaze->fetch_lists();
        wp_send_json( $success );
    }


}