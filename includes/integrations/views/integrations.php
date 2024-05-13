<?php defined( 'ABSPATH' ) or exit;
/** @var MB4WP_Integration_Fixture[] $enabled_integrations */
/** @var MB4WP_Integration_Fixture[] $available_integrations */

/**
 * Render a table with integrations
 *
 * @param $integrations
 * @ignore
 */
function _mb4wp_integrations_table( $integrations ) {
	?>
	<table class="mb4wp-table widefat striped">

		<thead>
		<tr>
			<th><?php _e( 'Name', 'mailblaze-for-wp' ); ?></th>
			<th><?php _e( 'Description', 'mailblaze-for-wp' ); ?></th>
		</tr>
		</thead>

		<tbody>

		<?php foreach( $integrations as $integration ) {

			$installed = $integration->is_installed();
			?>
			<tr style="<?php if( ! $installed ) { echo 'opacity: 0.4;'; } ?>">

				<!-- Integration Name -->
				<td>

					<?php
					if( $installed ) {
						printf( '<strong><a href="%s" title="%s">%s</a></strong>', esc_attr( add_query_arg( array( 'integration' => $integration->slug ) ) ), __( 'Configure this integration', 'mailblaze-for-wp' ), $integration->name );
					} else {
						echo $integration->name;
					} ?>


				</td>
				<td class="desc">
					<?php
                   		echo $integration->description;
                    ?>
				</td>
			</tr>
		<?php } ?>

		</tbody>
	</table><?php
}
?>
<div id="mb4wp-admin" class="wrap mb4wp-settings">

	<p class="breadcrumbs">
		<span class="prefix"><?php echo __( 'You are here: ', 'mailblaze-for-wp' ); ?></span>
		<a href="<?php echo admin_url( 'admin.php?page=mailblaze-for-wp' ); ?>">MailBlaze for WordPress</a> &rsaquo;
		<span class="current-crumb"><strong><?php _e( 'Integrations', 'mailblaze-for-wp' ); ?></strong></span>
	</p>

	<div class="main-content row">

		<!-- Main Content -->
		<div class="col col-4">

			<h1 class="page-title"><?php _e( 'Integrations', 'mailblaze-for-wp' ); ?></h1>

			<h2 style="display: none;"></h2>
			<?php settings_errors(); ?>

			<p>
				<?php _e( 'The table below shows all integrations that are available.', 'mailblaze-for-wp' ); ?>
				<?php _e( 'Click on the name of an integration to edit all settings relating to that integration.', 'mailblaze-for-wp' ); ?>
			</p>

			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">

				<?php settings_fields( 'mb4wp_integrations_settings' ); ?>

				<h3><?php _e( 'Enabled integrations', 'mailblaze-for-wp' ); ?></h3>
				<?php _mb4wp_integrations_table( $enabled_integrations ); ?>

				<div class="medium-margin"></div>

				<h3><?php _e( 'Available integrations', 'mailblaze-for-wp' ); ?></h3>
				<?php _mb4wp_integrations_table( $available_integrations ); ?>
                <p><?php echo __( "Greyed out integrations will become available after installing & activating the corresponding plugin.", 'mailblaze-for-wp' ); ?></p>


            </form>

		</div>

		<!-- Sidebar -->
		<div class="sidebar col col-2">
			<?php include MB4WP_PLUGIN_DIR . '/includes/views/parts/admin-sidebar.php'; ?>
		</div>

	</div>

</div>
