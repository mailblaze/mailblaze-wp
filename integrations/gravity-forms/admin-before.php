<p>
    <?php 
    // translators: Placeholder represents the URL for editing Gravity Forms.
    printf( 
        esc_html__( 'To integrate with Gravity Forms, add the "MailBlaze" field to <a href="%s">one of your Gravity Forms forms</a>.', 'mailblaze-for-wp' ), 
        esc_url(admin_url( 'admin.php?page=gf_edit_forms' ))
    ); 
    ?>
</p>