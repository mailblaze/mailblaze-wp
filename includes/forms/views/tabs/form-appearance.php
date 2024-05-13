<?php
$theme = wp_get_theme();

$css_options = array(
	'0'                                     => /* translators:	The theme name */
											   sprintf( esc_html__( 'Inherit from %s theme', 'mailblaze-for-wp' ), $theme->Name ),
	'basic'                                 => esc_html__( 'Basic', 'mailblaze-for-wp' ),
	esc_html__( 'Form Themes', 'mailblaze-for-wp' ) => array(
		'theme-light' => esc_html__( 'Light Theme', 'mailblaze-for-wp' ),
		'theme-dark'  => esc_html__( 'Dark Theme', 'mailblaze-for-wp' ),
		'theme-red'   => esc_html__( 'Red Theme', 'mailblaze-for-wp' ),
		'theme-green' => esc_html__( 'Green Theme', 'mailblaze-for-wp' ),
		'theme-blue'  => esc_html__( 'Blue Theme', 'mailblaze-for-wp' ),
	),
);

/**
 * Filters the <option>'s in the "CSS Stylesheet" <select> box.
 *
 * @ignore
 */
$css_options = apply_filters( 'mb4wp_admin_form_css_options', $css_options );

?>

<h2><?php _e( 'Form Appearance', 'mailblaze-for-wp' ); ?></h2>

<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="mb4wp_load_stylesheet_select"><?php _e( 'Form Style' ,'mailblaze-for-wp' ); ?></label></th>
		<td class="nowrap valigntop">
			<select name="mb4wp_form[settings][css]" id="mb4wp_load_stylesheet_select">

				<?php foreach( $css_options as $key => $option ) {
					if( is_array( $option ) ) {
						$label = $key;
						$options = $option;
						printf( '<optgroup label="%s">', $label );
						foreach( $options as $key => $option ) {
							printf( '<option value="%s" %s>%s</option>', $key, selected( $opts['css'], $key, false ), $option );
						}
						print( '</optgroup>' );
					} else {
						printf( '<option value="%s" %s>%s</option>', $key, selected( $opts['css'], $key, false ), $option );
					}
				} ?>
			</select>
			<p class="help">
				<?php _e( 'If you want to load some default CSS styles, select "basic formatting styles" or choose one of the color themes' , 'mailblaze-for-wp' ); ?>
			</p>
		</td>
	</tr>

	<?php
	/** @ignore */
	do_action( 'mb4wp_admin_form_after_appearance_settings_rows', $opts, $form );
	?>

</table>

<?php submit_button(); ?>