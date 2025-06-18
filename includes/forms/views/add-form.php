<?php defined( 'ABSPATH' ) or exit; ?>
<div id="mb4wp-admin" class="wrap mb4wp-settings">

	<p class="breadcrumbs">
		<span class="prefix"><?php echo __('You are here: ', 'mailblaze-for-wp'); ?></span>
		<a href="<?php echo admin_url('admin.php?page=mailblaze-for-wp'); ?>">Mail Blaze for WordPress</a> &rsaquo;
		<a href="<?php echo mb4wp_get_list_forms_url(); ?>"><?php _e('Forms', 'mailblaze-for-wp'); ?></a>
		&rsaquo;
		<span class="current-crumb"><strong><?php echo __('Add New Form', 'mailblaze-for-wp'); ?></strong></span>
	</p>

	<div class="row">

		<!-- Main Content -->
		<div class="main-content col col-4">

			<h1 class="page-title">
				<?php _e( "Add new form", 'mailblaze-for-wp' ); ?>
			</h1>

			<h2 style="display: none;"></h2><?php // fake h2 for admin notices ?>

			<div style="max-width: 480px;">

				<!-- Wrap entire page in <form> -->
				<form method="post">

					<input type="hidden" name="_mb4wp_action" value="add_form" />
					<?php wp_nonce_field( 'add_form', '_mb4wp_nonce' ); ?>


					<div class="small-margin">
						<h3>
							<label>
								<?php _e( 'What is the name of this form?', 'mailblaze-for-wp' ); ?>
							</label>
						</h3>
						<input type="text" name="mb4wp_form[name]" class="widefat" value="" spellcheck="true" autocomplete="off" placeholder="<?php _e( 'Enter your form title..', 'mailblaze-for-wp' ); ?>">
					</div>

					<div class="small-margin">

						<h3>
							<label>
								<?php _e( 'To which Mail Blaze lists should this form subscribe?', 'mailblaze-for-wp' ); ?>
							</label>
						</h3>

						<?php if( ! empty( $lists ) ) { ?>
						<ul id="mb4wp-lists">
							<?php foreach( $lists as $list ) { ?>
								<li>
									<label>
										<input type="checkbox" name="mb4wp_form[settings][lists][<?php echo esc_attr( $list->id ); ?>]" value="<?php echo esc_attr( $list->id ); ?>" <?php selected( $number_of_lists, 1 ); ?> >
										<?php echo esc_html( $list->name ); ?>
									</label>
								</li>
							<?php } ?>
						</ul>
						<?php } else { ?>
						<p class="mb4wp-notice">
							<?php 
								// translators: link to the MailBlaze settings page
								printf( __( 'No lists found. Did you <a href="%s">connect with MailBlaze</a>?', 'mailblaze-for-wp' ), admin_url( 'admin.php?page=mailblaze-for-wp' ) ); ?>
						</p>
						<?php } ?>

					</div>

					<?php submit_button( __( 'Add new form', 'mailblaze-for-wp' ) ); ?>


				</form><!-- Entire page form wrap -->

			</div>


			<?php include MB4WP_PLUGIN_DIR . 'includes/views/parts/admin-footer.php'; ?>

		</div><!-- / Main content -->

		<!-- Sidebar -->
		<div class="sidebar col col-2">
			<?php include MB4WP_PLUGIN_DIR . 'includes/views/parts/admin-sidebar.php'; ?>
		</div>


	</div>

</div>