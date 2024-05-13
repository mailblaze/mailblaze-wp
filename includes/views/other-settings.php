<?php
defined( 'ABSPATH' ) or exit;

/** @var MB4WP_Debug_Log $log */
/** @var MB4WP_Debug_Log_Reader $log_reader */

/**
 * @ignore
 * @param array $opts
 */
function _mb4wp_usage_tracking_setting( $opts ) {
	?>
	<div class="medium-margin" >
		<h3><?php _e( 'Miscellaneous settings', 'mailblaze-for-wp' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><?php _e( 'Usage Tracking', 'mailblaze-for-wp' ); ?></th>
				<td>
					<label>
						<input type="radio" name="mb4wp[allow_usage_tracking]" value="1" <?php checked( $opts['allow_usage_tracking'], 1 ); ?> />
						<?php _e( 'Yes' ); ?>
					</label> &nbsp;
					<label>
						<input type="radio" name="mb4wp[allow_usage_tracking]" value="0" <?php checked( $opts['allow_usage_tracking'], 0 ); ?>  />
						<?php _e( 'No' ); ?>
					</label>

					<p class="help">
						<?php echo __( 'Allow us to anonymously track how this plugin is used to help us make it better fit your needs.', 'mailblaze-for-wp' ); ?>
						<a href="https://kb.mb4wp.com/what-is-usage-tracking/#utm_source=wp-plugin&utm_medium=mailblaze-for-wp&utm_campaign=settings-page" target="_blank">
							<?php _e( 'This is what we track.', 'mailblaze-for-wp' ); ?>
						</a>
					</p>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Logging', 'mailblaze-for-wp' ); ?></th>
				<td>
					<select name="mb4wp[debug_log_level]">
						<option value="warning" <?php selected( 'warning', $opts['debug_log_level'] ); ?>><?php _e( 'Errors & warnings only', 'mailblaze-for-wp' ); ?></option>
						<option value="debug" <?php selected( 'debug', $opts['debug_log_level'] ); ?>><?php _e( 'Everything', 'mailblaze-for-wp' ); ?></option>
					</select>
					<p class="help">
						<?php 
						/* translators: URL to the KB article on how to enable log debugging */
						printf( __( 'Determines what events should be written to <a href="%s">the debug log</a> (see below).', 'mailblaze-for-wp' ), 'https://kb.mb4wp.com/how-to-enable-log-debugging/#utm_source=wp-plugin&utm_medium=mailblaze-for-wp&utm_campaign=settings-page' ); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

add_action( 'mb4wp_admin_other_settings', '_mb4wp_usage_tracking_setting', 70 );
?>
<div id="mb4wp-admin" class="wrap mb4wp-settings">

	<p class="breadcrumbs">
		<span class="prefix"><?php echo __( 'You are here: ', 'mailblaze-for-wp' ); ?></span>
		<a href="<?php echo admin_url( 'admin.php?page=mailblaze-for-wp' ); ?>">MailBlaze for WordPress</a> &rsaquo;
		<span class="current-crumb"><strong><?php _e( 'Other Settings', 'mailblaze-for-wp' ); ?></strong></span>
	</p>


	<div class="row">

		<!-- Main Content -->
		<div class="main-content col col-4">

			<h1 class="page-title">
				<?php _e( 'Other Settings', 'mailblaze-for-wp' ); ?>
			</h1>

			<h2 style="display: none;"></h2>
			<?php settings_errors(); ?>

			<?php
			/**
			 * @ignore
			 */
			do_action( 'mb4wp_admin_before_other_settings', $opts );
			?>

			<!-- Settings -->
			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">
				<?php settings_fields( 'mb4wp_settings' ); ?>

				<?php
				/**
				 * @ignore
				 */
				do_action( 'mb4wp_admin_other_settings', $opts );
				?>

				<div style="margin-top: -20px;"><?php submit_button(); ?></div>
			</form>

			<!-- Debug Log -->
			<div class="medium-margin">
				<h3><?php _e( 'Debug Log', 'mailblaze-for-wp' ); ?> <input type="text" id="debug-log-filter" class="alignright regular-text" placeholder="<?php esc_attr_e( 'Filter..', 'mailblaze-for-wp' ); ?>" /></h3>

				<?php
				if( ! $log->test() ) {
					echo '<p>';					
					echo __( 'Log file is not writable.', 'mailblaze-for-wp' ) . ' ';					
					// translators: a link to the Codex article on file permissions
					echo  sprintf( __( 'Please ensure %1$s has the proper <a href="%2$s">file permissions</a>.', 'mailblaze-for-wp' ), '<code>' . $log->file . '</code>', 'https://codex.wordpress.org/Changing_File_Permissions' );
					echo '</p>';

					// hack to hide filter input
					echo '<style type="text/css">#debug-log-filter { display: none; }</style>';
				} else {
					?>
					<div id="debug-log" class="mb4wp-log widefat">
						<?php
						$line = $log_reader->read_as_html();

						if (!empty($line)) {
							while( is_string( $line ) ) {
								echo '<div class="debug-log-line">' . $line . '</div>';
								$line = $log_reader->read_as_html();
							}
						} else {
							echo '<div class="debug-log-empty">';
							echo '-- ' . __('Nothing here. Which means there are no errors!', 'mailblaze-for-wp');
							echo '</div>';
						}
						?>
					</div>

					<form method="post">
						<input type="hidden" name="_mb4wp_action" value="empty_debug_log">
						<p>
							<input type="submit" class="button"
								   value="<?php esc_attr_e('Empty Log', 'mailblaze-for-wp'); ?>"/>
						</p>
					</form>
					<?php
				} // end if is writable

				if( $log->level >= 300 ) {
					echo '<p>';
					echo __( 'Right now, the plugin is configured to only log errors and warnings.', 'mailblaze-for-wp' );
					echo '</p>';
				}
				?>

				<script>
					(function() {
						'use strict';
						// scroll to bottom of log
						var log = document.getElementById("debug-log"),
							logItems;
						log.scrollTop = log.scrollHeight;
						log.style.minHeight = '';
						log.style.maxHeight = '';
						log.style.height = log.clientHeight + "px";

						// add filter
						var logFilter = document.getElementById('debug-log-filter');
						logFilter.addEventListener('keydown', function(e) {
							if(e.keyCode == 13 ) {
								searchLog(e.target.value.trim());
							}
						});

						// search log for query
						function searchLog(query) {
							if( ! logItems ) {
								logItems = [].map.call(log.children, function(node) {
									return node.cloneNode(true);
								})
							}

							var ri = new RegExp(query.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&"), 'i');
							var newLog = log.cloneNode();
							logItems.forEach(function(node) {
								if( ! node.textContent ) { return ; }
								if( ! query.length || ri.test(node.textContent) ) {
									newLog.appendChild(node);
								}
							});

							log.parentNode.replaceChild(newLog,log);
							log = newLog;
							log.scrollTop = log.scrollHeight;
						}
					})();
				</script>
			</div>
			<!-- / Debug Log -->



			<?php include dirname( __FILE__ ) . '/parts/admin-footer.php'; ?>
		</div>

		<!-- Sidebar -->
		<div class="sidebar col col-2">
			<?php include dirname( __FILE__ ) . '/parts/admin-sidebar.php'; ?>
		</div>


	</div>

</div>

