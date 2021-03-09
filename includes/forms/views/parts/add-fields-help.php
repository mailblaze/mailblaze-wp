<?php defined( 'ABSPATH' ) or exit; ?>

<div class="mb4wp-admin">
	<h2><?php _e( 'Add more fields', 'mailblaze-for-wp' ); ?></h2>

	<div class="help-text">

		<p>
			<?php echo __( 'To add more fields to your form, you will need to create those fields in MailBlaze first.', 'mailblaze-for-wp' ); ?>
		</p>

		<p><strong><?php echo __( "Here's how:", 'mailblaze-for-wp' ); ?></strong></p>

		<ol>
			<li>
				<p>
					<?php echo __( 'Log in to your MailBlaze account.', 'mailblaze-for-wp' ); ?>
				</p>
			</li>
			<li>
				<p>
					<?php echo __( 'Add list fields to any of your selected lists.', 'mailblaze-for-wp' ); ?>
					<?php echo __( 'Clicking the following links will take you to the right screen.', 'mailblaze-for-wp' ); ?>
				</p>
				<ul class="children lists--only-selected">
					<?php foreach( $lists as $list ) { ?>
					<li data-list-id="<?php echo $list->id; ?>" class="<?php echo in_array( $list->id, $opts['lists'] ) ? '' : 'hidden'; ?>">
						<a href="https://control.mailblaze.com/customer/index.php/lists/<?php echo $list->id; ?>/fields" target="_blank">
							<span class="screen-reader-text"><?php _e( 'Edit list fields for', 'mailblaze-for-wp' ); ?> </span>
							<?php echo $list->name; ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</li>
			<li>
				<p>
					<?php echo __( 'Click the following button to have MailBlaze for WordPress pick up on your changes.', 'mailblaze-for-wp' ); ?>
				</p>

				<p>
					<a class="button button-primary" href="<?php echo esc_attr( add_query_arg( array( '_mb4wp_action' => 'empty_lists_cache' ) ) ); ?>">
						<?php _e( 'Renew MailBlaze lists', 'mailblaze-for-wp' ); ?>
					</a>
				</p>
			</li>
		</ol>


	</div>
</div>