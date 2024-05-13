<?php

$position_options = array(
	'checkout_billing' => __( "After billing details", 'mailblaze-for-wp' ),
	'checkout_shipping' => __( 'After shipping details', 'mailblaze-for-wp' ),
	'checkout_after_customer_details' => __( 'After customer details', 'mailblaze-for-wp' ),
	'review_order_before_submit' => __( 'Before submit button', 'mailblaze-for-wp' ),
);




/** @var MB4WP_Integration $integration */

?>
<table class="form-table">
	<?php $config = array( 'element' => 'mb4wp_integrations['. $integration->slug .'][implicit]', 'value' => '0' ); ?>
	<tr valign="top" data-showif="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
		<th scope="row">
			<?php _e( 'Position', 'mailblaze-for-wp' ); ?>
		</th>
		<td>
			<select name="mb4wp_integrations[<?php echo $integration->slug; ?>][position]">
				<?php

				foreach( $position_options as $value => $label ) {
					printf( '<option value="%s" %s>%s</option>', esc_attr( $value ), selected( $value, $opts['position'], false ), esc_html( $label ) );
				}
				?>

			</select>
		</td>
	</tr>
</table>
