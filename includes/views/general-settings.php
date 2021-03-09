<?php
defined( 'ABSPATH' ) or exit;
?>
<div id="mb4wp-admin" class="wrap mb4wp-settings">

	<p class="breadcrumbs">
		<span class="prefix"><?php echo __( 'You are here: ', 'mailblaze-for-wp' ); ?></span>
		<span class="current-crumb"><strong>Mail Blaze</strong></span>
	</p>


	<div class="row">

		<!-- Main Content -->
		<div class="main-content col col-4">

			<h1 class="page-title">
				<?php _e( 'General Settings', 'mailblaze-for-wp' ); ?>
			</h1>

			<h2 style="display: none;"></h2>
			<?php
			settings_errors();
			$this->messages->show();
			?>

			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">
				<?php settings_fields( 'mb4wp_settings' ); ?>

				<h3>
					<?php _e( 'Mail Blaze API Settings', 'mailblaze-for-wp' ); ?>
				</h3>

				<table class="form-table">

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Status', 'mailblaze-for-wp' ); ?>
						</th>
						<td>
							<?php if( $connected ) { ?>
								<span class="status positive"><?php _e( 'CONNECTED' ,'mailblaze-for-wp' ); ?></span>
							<?php } else { ?>
								<span class="status neutral"><?php _e( 'NOT CONNECTED', 'mailblaze-for-wp' ); ?></span>
							<?php } ?>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row"><label for="mailblaze_api_key"><?php _e( 'API Key', 'mailblaze-for-wp' ); ?></label></th>
						<td>
							<input type="text" class="widefat" placeholder="<?php _e( 'Your MailBlaze API key', 'mailblaze-for-wp' ); ?>" id="mailblaze_api_key" name="mb4wp[api_key]" value="<?php echo esc_attr( $obfuscated_api_key ); ?>" />
							<p class="help">
								<?php _e( 'This is the API key to connect with your Mail Blaze account.', 'mailblaze-for-wp' ); ?>
								<a target="_blank" href="https://control.mailblaze.com/customer/index.php/api-keys/index"><?php _e( 'Get your API key here.', 'mailblaze-for-wp' ); ?></a>
							</p>
						</td>

					</tr>

				</table>

				<?php submit_button(); ?>

			</form>

			<?php

			/**
			 * Runs right after general settings are outputted in admin.
			 *
			 * @since 3.0
			 * @ignore
			 */
			do_action( 'mb4wp_admin_after_general_settings' );

			if( ! empty( $opts['api_key'] ) ) {
				echo '<hr />';
				include dirname( __FILE__ ) . '/parts/lists-overview.php';
			}

			include dirname( __FILE__ ) . '/parts/admin-footer.php';

			?>
		</div>

		<!-- Sidebar -->
		<div class="sidebar col col-2">

		</div>

	</div>

</div>

