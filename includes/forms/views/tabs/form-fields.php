<?php add_thickbox(); ?>

<div class="alignright">
	<a href="#TB_inline?width=0&height=550&inlineId=mb4wp-form-variables" class="thickbox button-secondary">
		<span class="dashicons dashicons-info"></span>
		<?php _e( 'Form variables', 'mailblaze-for-wp' ); ?>
	</a>
	<a href="#TB_inline?width=600&height=400&inlineId=mb4wp-add-field-help" class="thickbox button-secondary">
		<span class="dashicons dashicons-editor-help"></span>
		<?php _e( 'Add more fields', 'mailblaze-for-wp' ); ?>
	</a>
</div>
<h2><?php _e( "Form Fields", 'mailblaze-for-wp' ); ?></h2>

<!-- Placeholder for the field wizard -->
<div id="mb4wp-field-wizard"></div>

<div class="mb4wp-row">
	<div class="mb4wp-col mb4wp-col-3 mb4wp-form-editor-wrap">
		<h4 style="margin: 0"><label><?php _e( 'Form code', 'mailblaze-for-wp' ); ?></label></h4>
		<!-- Textarea for the actual form content HTML -->
		<textarea class="widefat" cols="160" rows="20" id="mb4wp-form-content" name="mb4wp_form[content]" placeholder="<?php _e( 'Enter the HTML code for your form fields..', 'mailblaze-for-wp' ); ?>" autocomplete="false" autocorrect="false" autocapitalize="false" spellcheck="false"><?php echo htmlspecialchars( $form->content, ENT_QUOTES, get_option( 'blog_charset' ) ); ?></textarea>
	</div>
	<div class="mb4wp-col mb4wp-col-3 mb4wp-form-preview-wrap">
		<h4 style="margin: 0;">
			<label><?php _e( 'Form preview', 'mailblaze-for-wp' ); ?> 
			<span class="mb4wp-tooltip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'The form may look slightly different than this when shown in a post, page or widget area.', 'mailblaze-for-wp' ); ?>"></span>
			</label>
		</h4>
		<iframe id="mb4wp-form-preview" src="<?php echo esc_attr( $form_preview_url ); ?>"></iframe>
	</div>
</div>


<!-- This field is updated by JavaScript as the form content changes -->
<input type="hidden" id="required-fields" name="mb4wp_form[settings][required_fields]" value="<?php echo esc_attr( $form->settings['required_fields'] ); ?>" />

<?php submit_button(); ?>

<p class="mb4wp-form-usage">
	<?php 
		// translators: The shortcode to display the form in a post/page/widget
		printf( __( 'Use the shortcode %s to display this form inside a post, page or text widget.' ,'mailblaze-for-wp' ), '<input type="text" onfocus="this.select();" readonly="readonly" value="'. esc_attr( sprintf( '[mb4wp_form id="%d"]', $form->ID ) ) .'" size="'. ( strlen( $form->ID ) + 18 ) .'">' ); ?>
</p>


<?php // Content for Thickboxes ?>
<div id="mb4wp-form-variables" style="display: none;">
	<?php include dirname( __FILE__ ) . '/../parts/dynamic-content-tags.php'; ?>
</div>

<div id="mb4wp-add-field-help" style="display: none;">
	<?php include dirname( __FILE__ ) . '/../parts/add-fields-help.php'; ?>
</div>
