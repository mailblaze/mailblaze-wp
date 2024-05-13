<?php

/**
 * Class MB4WP_Admin_Ads
 *
 * @ignore
 * @access private
 */
class MB4WP_Admin_Ads {

	/**
	 * @return bool Adds hooks
	 */
	public function add_hooks() {

		// don't hook if Premium is activated
		if( defined( 'MB4WP_PREMIUM_VERSION' ) ) {
			return false;
		}

		add_filter( 'mb4wp_admin_plugin_meta_links', array( $this, 'plugin_meta_links' ) );
		add_action( 'mb4wp_admin_form_after_behaviour_settings_rows', array( $this, 'after_form_settings_rows' ) );
		add_action( 'mb4wp_admin_form_after_appearance_settings_rows', array( $this, 'after_form_appearance_settings_rows' ) );
		add_action( 'mb4wp_admin_sidebar', array( $this, 'admin_sidebar' ) );
		add_action( 'mb4wp_admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'mb4wp_admin_other_settings', array( $this, 'ecommerce' ), 90 );

		add_action( 'mb4wp_admin_after_woocommerce_integration_settings', array( $this, 'ecommerce' ) );
		return true;
	}

	/**
	 * Add text row to "Form > Appearance" tab.
	 */
	public function after_form_appearance_settings_rows() {
		echo '<tr valign="top">';
		echo '<td colspan="2">';
		echo '<p class="help">';
		echo '</p>';
		echo '</td>';
		echo '</tr>';
	}

	/**
	 * Add text row to "Form > Settings" tab.
	 */
	public function after_form_settings_rows() {
		echo '<tr valign="top">';
		echo '<td colspan="2">';
		echo '<p class="help">';

		if( wp_rand( 1, 2 ) === 1 ) {
		} else {
		}

		echo '</p>';
		echo '</td>';
		echo '</tr>';
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function plugin_meta_links( $links ) {
		$links[] = '';
		return $links;
	}

	/**
	 * Add several texts to admin footer.
	 */
	public function admin_footer() {

		if( isset( $_GET['view'] ) && $_GET['view'] === 'edit-form' ) {
			
			// WPML & Polylang specific message
			if( defined( 'ICL_LANGUAGE_CODE' ) ) {
				echo '<p class="help">';
				return;
			}

			// General "edit form" message
			echo '<p class="help">';
			return;
		}

		// General message
		echo '<p class="help"></p>';
	}

	/**
	 * Add email opt-in form to sidebar
	 */
	public function admin_sidebar() {

		/*
		echo '<div class="mb4wp-box">';
			echo '<div style="border: 5px dotted #cc4444; padding: 0 20px; background: white;">';
			echo '</div>';
		echo '</div>';
		*/

		?>
		<div class="mb4wp-box" id="mb4wp-optin-box">

			<?php $user = wp_get_current_user(); ?>
			<!-- Begin MailBlaze Signup Form -->
			<div id="mc_embed_signup">
				<h4 class="mb4wp-title"></h4>
			</div>
		</div>
		<?php
	}

	/**
	 * Show notice about E-Commerce integration in Premium.
	 */
	public function ecommerce() {

		// detect whether WooCommerce is installed & activated.
		if( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		echo '<div class="medium-margin">';
		echo '</div>';
	}

	public function show_extensions_page() {
		require MB4WP_PLUGIN_DIR . 'includes/views/extensions.php';
	}

}
