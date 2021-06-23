<h2><?php echo __( 'Coupons', 'mailblaze-for-wp' ); ?></h2>

<p><?php echo __( 'These coupons are associated to your WooCommerce plugin. You can choose from your existing coupons or generate a unique coupon based on your own criteria', 'mailblaze-for-wp' ); ?></p>

<div class="small-margin"></div>

<table class="form-table" style="table-layout: fixed;">

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_before_coupon_settings_rows', $opts, $form );
	?>

	<tr valign="top">
		<th scope="row"><?php _e( 'Enable coupon codes', 'mailblaze-for-wp' ); ?></th>
		<td class="nowrap">
			<label>
				<input type="radio" name="mb4wp_form[settings][coupon_enabled]" value="1" <?php checked( $opts['coupon_enabled'], 1 ); ?> />&rlm;
				<?php _e( 'Yes' ); ?>
			</label> &nbsp;
			<label>
				<input type="radio" name="mb4wp_form[settings][coupon_enabled]" value="0" <?php checked( $opts['coupon_enabled'], 0 ); ?> />&rlm;
				<?php _e( 'No' ); ?>
		</label>
			<p class="help"><?php _e( 'Select "yes" if you want subscribers to have coupon code associated to their signup', 'mailblaze-for-wp' ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Choose the type of subscription coupon codes', 'mailblaze-for-wp' ); ?></th>
		<td class="nowrap">
			<label>
				<input type="radio" name="mb4wp_form[settings][unique_coupon]" value="1" <?php checked( $opts['unique_coupon'], 1 ); ?> />&rlm;
				<?php _e( 'Unique' ); ?>
			</label> &nbsp;
			<label>
				<input type="radio" name="mb4wp_form[settings][unique_coupon]" value="0" <?php checked( $opts['unique_coupon'], 0 ); ?> />&rlm;
				<?php _e( 'Generic' ); ?>
		</label>
			<p class="help"><?php _e( 'Select "Unique" if you want each subscriber to receive a unique coupon code that only applies to them. Alternatively choose "Generic" if you\'d like a single couple to apply to all subscribers. You will be able to choose from a dropdown of existing WooCommerce coupon codes ', 'mailblaze-for-wp' ); ?></p>
		</td>
	</tr>

	<?php 


		//Get all the coupon codes ava

		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'shop_coupon',
			'post_status' => 'publish',
			'page' => 100000
		);

		$query = new WP_Query( $query_args );
		$coupons_list = array();

		foreach ( $query->posts as $coupon_id ) {
			//$coupons[] = current( $this->get_coupon( $coupon_id, $fields ) );
			$coupon = new WC_Coupon( $coupon_id );
			$coupons_list[$coupon_id] = $coupon->get_code() . ": " . wc_format_decimal( $coupon->get_amount(), 2 ) . " " . $coupon->get_discount_type();
			
		}

	?>
		
	<tr valign="top">
		<th scope="row"><label for="mb4wp_load_stylesheet_select"><?php _e( 'Existing WooCommerce Coupons' ,'mailblaze-for-wp' ); ?></label></th>
		<td class="nowrap valigntop">
			<select name="mb4wp_form[settings][model_coupon]" id="mb4wp_load_stylesheet_select">
				

				<?php foreach( $coupons_list as $key => $option ) {					
						printf( '<option value="%s" %s>%s</option>', $key, selected( $opts['model_coupon'], $key, false ), $option );					
				} ?>
			</select>
			<p class="help">
				<?php _e( 'Select from the list of existing coupon codes. If you cannot find a code that suits your requirements please add the coupon code via WooCommerce and navigate back here. If you have chosen "Unique" coupon codes above the option you select will act as a template. All the options will be duplicated from that coupon, all besides the actual code, which will be unique.' , 'mailblaze-for-wp' ); ?>
			</p>
		</td>
	</tr>
	

		


	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_after_coupon_settings_rows', $opts, $form );
	?>

</table>

<?php submit_button(); ?>
