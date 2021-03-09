<?php
defined( 'ABSPATH' ) or exit;

$tags = mb4wp('forms')->get_tags();
?>
<h2><?php _e( 'Add dynamic form variable', 'mailblaze-for-wp' ); ?></h2>
<p>
	<?php echo 'The following list of variables can be used to add some dynamic content to your form or success and error messagess. This allows you to personalise your form or response messages.'; ?>
</p>
<table class="widefat striped">
	<?php foreach( $tags as $tag => $config ) {
		$tag = ! empty( $config['example'] ) ? $config['example'] : $tag;
		?>
		<tr>
			<td>
				<input type="text" class="widefat" value="<?php echo esc_attr( sprintf( '{%s}', $tag ) ); ?>" readonly="readonly" onfocus="this.select();" />
				<p class="help" style="margin-bottom:0;"><?php echo strip_tags( $config['description'], '<strong><b><em><i><a><code>' ); ?></p>
			</td>
		</tr>
	<?php } ?>
</table>
