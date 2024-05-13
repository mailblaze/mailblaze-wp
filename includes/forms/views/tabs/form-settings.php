<h2><?php echo __( 'Form Settings', 'mailblaze-for-wp' ); ?></h2>

<div class="medium-margin"></div>

<h3><?php echo __( 'MailBlaze specific settings', 'mailblaze-for-wp' ); ?></h3>

<table class="form-table" style="table-layout: fixed;">

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_after_mailblaze_settings_rows', $opts, $form );
	?>

	<tr valign="top">
		<th scope="row" style="width: 250px;"><?php _e( 'Lists this form subscribes to', 'mailblaze-for-wp' ); ?></th>
		<?php // loop through lists
		if( empty( $lists ) ) {
			?><td colspan="2"><?php 
				// translators: link to the MailBlaze settings page if connection does not work
				printf( __( 'No lists found, <a href="%s">are you connected to MailBlaze</a>?', 'mailblaze-for-wp' ), admin_url( 'admin.php?page=mailblaze-for-wp' ) ); ?></td><?php
		} else { ?>
			<td >

				<ul id="mb4wp-lists" style="margin-bottom: 20px; max-height: 300px; overflow-y: auto;">
					<?php foreach( $lists as $list ) { ?>
						<li>
							<label>
								<input class="mb4wp-list-input" type="checkbox" name="mb4wp_form[settings][lists][]" value="<?php echo esc_attr( $list->id ); ?>" <?php  checked( in_array( $list->id, $opts['lists'] ), true ); ?>> <?php echo esc_html( $list->name ); ?>
							</label>
						</li>
					<?php } ?>
				</ul>
				<p class="help"><?php _e( 'Select the list(s) to which people who submit this form should be subscribed.' ,'mailblaze-for-wp' ); ?></p>
			</td>
		<?php } ?>

	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Update existing subscribers?', 'mailblaze-for-wp' ); ?></th>
		<td class="nowrap">
			<label>
				<input type="radio" name="mb4wp_form[settings][update_existing]" value="1" <?php checked( $opts['update_existing'], 1 ); ?> />&rlm;
				<?php _e( 'Yes' ); ?>
			</label> &nbsp;
			<label>
				<input type="radio" name="mb4wp_form[settings][update_existing]" value="0" <?php checked( $opts['update_existing'], 0 ); ?> />&rlm;
				<?php _e( 'No' ); ?>
			</label>
			<p class="help"><?php _e( 'Select "yes" if you want to update existing subscribers with the data that is sent.', 'mailblaze-for-wp' ); ?></p>
		</td>
	</tr>	

	<?php $config = array( 'element' => 'mb4wp_form[settings][update_existing]', 'value' => 1 ); ?>

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_after_mailblaze_settings_rows', $opts, $form );
	?>

</table>

<div class="medium-margin"></div>

<h3><?php _e( 'Form behaviour', 'mailblaze-for-wp' ); ?></h3>

<table class="form-table" style="table-layout: fixed;">

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_before_behaviour_settings_rows', $opts, $form );
	?>

	<tr valign="top">
		<th scope="row"><?php _e( 'Hide form after a successful sign-up?', 'mailblaze-for-wp' ); ?></th>
		<td class="nowrap">
			<label>
				<input type="radio" name="mb4wp_form[settings][hide_after_success]" value="1" <?php checked( $opts['hide_after_success'], 1 ); ?> />&rlm;
				<?php _e( 'Yes' ); ?>
			</label> &nbsp;
			<label>
				<input type="radio" name="mb4wp_form[settings][hide_after_success]" value="0" <?php checked( $opts['hide_after_success'], 0 ); ?> />&rlm;
				<?php _e( 'No' ); ?>
			</label>
			<p class="help">
				<?php _e( 'Select "yes" to hide the form fields after a successful sign-up.', 'mailblaze-for-wp' ); ?>
			</p>
		</td>
	</tr>	
	<tr valign="top">
		<th scope="row"><label for="mb4wp_form_redirect"><?php _e( 'Redirect to URL after successful sign-ups', 'mailblaze-for-wp' ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="mb4wp_form[settings][redirect]" id="mb4wp_form_redirect" placeholder="<?php 
				// translators: an example of the thank you page
				printf( __( 'Example: %s', 'mailblaze-for-wp' ), esc_attr( site_url( '/thank-you/' ) ) ); ?>" value="<?php echo esc_attr( $opts['redirect'] ); ?>" />
			<p class="help">
				<?php _e( 'Leave empty or enter <code>0</code> for no redirect. Otherwise, use complete (absolute) URLs, including <code>http://</code>.', 'mailblaze-for-wp' ); ?>
			</p>
			<p class="help">
				<?php _e( 'Your "subscribed" message will not show when redirecting to another page, so make sure to let your visitors know they were successfully subscribed.', 'mailblaze-for-wp' ); ?>
			</p>		
				
		</td>
	</tr>	

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_after_behaviour_settings_rows', $opts, $form );
	?>

</table>

<?php submit_button(); ?>
