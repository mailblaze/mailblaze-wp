<h2><?php echo __( 'Google reCaptcha', 'mailblaze-for-wp' ); ?></h2>

<p><?php echo __( 'Learn how to get your <a href="https://www.a2hosting.com/kb/security/obtaining-google-recaptcha-site-key-and-secret-key" target="_blank">reCaptcha Keys here</a>', 'mailblaze-for-wp' ); ?></p>

<div class="small-margin"></div>



<table class="form-table" style="table-layout: fixed;">

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_before_spam_settings_rows', $opts, $form );
	?>

	<tr valign="top">
		<th scope="row"><?php _e( 'Add google recaptcha to the form?', 'mailblaze-for-wp' ); ?></th>
		<td class="nowrap">
			<label>
				<input type="radio" name="mb4wp_form[settings][recaptcha_enabled]" value="1" <?php checked( $opts['recaptcha_enabled'], 1 ); ?> />&rlm;
				<?php _e( 'Yes' ); ?>
			</label> &nbsp;
			<label>
				<input type="radio" name="mb4wp_form[settings][recaptcha_enabled]" value="0" <?php checked( $opts['recaptcha_enabled'], 0 ); ?> />&rlm;
				<?php _e( 'No' ); ?>
			</label>
			<p class="help">
				<?php _e( 'Select "yes" to enable google\'s recaptcha to be appended to your form.', 'mailblaze-for-wp' ); ?>
			</p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="mb4wp_form_redirect"><?php _e( 'Site Key', 'mailblaze-for-wp' ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="mb4wp_form[settings][site_key]" id="mb4wp_form_site_key" placeholder="<?php printf( __( '6LeUYD8bGOOGLESECRTETECZ7ir-gZAP', 'mailblaze-for-wp' ), esc_attr( site_url( '/thank-you/' ) ) ); ?>" value="<?php echo esc_attr( $opts['site_key'] ); ?>" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="mb4wp_form_redirect"><?php _e( 'Secret Key', 'mailblaze-for-wp' ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="mb4wp_form[settings][secret_key]" id="mb4wp_form_secret_key" placeholder="<?php printf( __( '6LeUYD8bGOOGLESECRTET8GZItd4aSYdxq', 'mailblaze-for-wp' ), esc_attr( site_url( '/thank-you/' ) ) ); ?>" value="<?php echo esc_attr( $opts['secret_key'] ); ?>" />
			<p class="help">
				<?php _e( 'Please ensure you are <strong>using google reCaptcha version 3</strong>. Version 2 will not work', 'mailblaze-for-wp' ); ?>
			</p>
		</td>
	</tr>	

		


	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_after_spam_settings_rows', $opts, $form );
	?>

</table>

<?php submit_button(); ?>
