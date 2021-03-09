<h3><?php _e( 'Your Mail Blaze Account' ,'mailblaze-for-wp' ); ?></h3>
<p><?php _e( 'Below is a listing of your Mail Blaze lists and their details. If you just applied changes to your Mail Blaze lists, please use the "Renew Mail Blaze lists" button below to renew the cached lists configuration.', 'mailblaze-for-wp' ); ?></p>


<div id="mb4wp-list-fetcher">
	<form method="post" action="">
		<input type="hidden" name="_mb4wp_action" value="empty_lists_cache" />

		<p>
			<input type="submit" value="<?php _e( 'Renew Mail Blaze lists', 'mailblaze-for-wp' ); ?>" class="button" />
		</p>
	</form>
</div>

<div class="mb4wp-lists-overview">
	<?php if( empty( $lists ) ) { ?>
		<p><?php _e( 'No lists were found in your Mail Blaze account', 'mailblaze-for-wp' ); ?>.</p>
	<?php } else {
		printf( '<p>' . __( 'A total of %d lists were found in your Mail Blaze account.', 'mailblaze-for-wp' ) . ' Please note that you can specify single or double opt-in per list in the list settings in your Mail Blaze account. Double opt-in is the default and is recommended.</p>', count( $lists ) );

		echo '<table class="widefat striped">';

		$headings = array(
			__( 'List Name', 'mailblaze-for-wp' ),
			__( 'ID', 'mailblaze-for-wp' ),
			__( 'Subscribers', 'mailblaze-for-wp' )
		);

		echo '<thead>';
		echo '<tr>';
		foreach( $headings as $heading ) {
			echo sprintf( '<th>%s</th>', $heading );
		}
		echo '</tr>';
		echo '</thead>';

		foreach ( $lists as $list ) {
			/** @var MB4WP_Mail Blaze_List $list */
			echo '<tr>';
			echo sprintf( '<td><a href="javascript:mb4wp.helpers.toggleElement(\'.list-%s-details\')">%s</a><span class="row-actions alignright"></span></td>', $list->id, esc_html( $list->name ) );
			echo sprintf( '<td><code>%s</code></td>', esc_html( $list->id ) );
			echo sprintf( '<td>%s</td>', esc_html( $list->subscriber_count ) );
			echo '</tr>';

			echo sprintf( '<tr class="list-details list-%s-details" style="display: none;">', $list->id );
			echo '<td colspan="3" style="padding: 0 20px 40px;">';

			echo sprintf( '<p class="alignright" style="margin: 20px 0;"><a href="%s" target="_blank"><span class="dashicons dashicons-edit"></span> ' . __( 'Edit this list in Mail Blaze', 'mailblaze-for-wp' ) . '</a></p>', $list->get_web_url() );

			// Fields
			if ( ! empty( $list->merge_fields ) ) { ?>
				<h3><?php _e('Merge Fields', 'mailblaze-for-wp');?></h3>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php _e('Name', 'mailblaze-for-wp');?></th>
							<th><?php _e('Tag', 'mailblaze-for-wp');?></th>
							<th><?php _e('Type', 'mailblaze-for-wp');?></th>
						</tr>
					</thead>
					<?php foreach ( $list->merge_fields as $merge_field ) { ?>
						<tr title="<?php printf( __( '%s (%s) with field type %s.', 'mailblaze-for-wp' ), esc_html( $merge_field->name ), esc_html( $merge_field->tag ), esc_html( $merge_field->field_type ) ); ?>">
							<td><?php echo esc_html( $merge_field->name );
								if ( $merge_field->required ) {
									echo '<span style="color:red;">*</span>';
								} ?></td>
							<td><code><?php echo esc_html( $merge_field->tag ); ?></code></td>
							<td>
								<?php
									echo esc_html( $merge_field->field_type );

									if( ! empty( $merge_field->choices ) ) {
										echo ' (' . join( ', ', $merge_field->choices ) . ')';
									}
								?>

							</td>
						</tr>
					<?php } ?>
				</table>
			<?php }

			echo '</td>';
			echo '</tr>';

			?>
		<?php } // end foreach $lists
		echo '</table>';
	} // end if empty ?>
</div>
