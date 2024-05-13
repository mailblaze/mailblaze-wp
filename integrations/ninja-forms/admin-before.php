<p>
    <?php 
    // translators: Placeholder represents the URL for editing Ninja Forms.
    printf( 
        esc_html__( 'To integrate with Ninja Forms, add the "MailBlaze" action to <a href="%s">one of your Ninja Forms forms</a>.', 'mailblaze-for-wp' ), 
        esc_url(admin_url( 'admin.php?page=ninja-forms' )) 
    ); 
    ?>
</p>