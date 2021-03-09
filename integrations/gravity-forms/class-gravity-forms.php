<?php

defined( 'ABSPATH' ) or exit;

/**
 * Class MB4WP_Ninja_Forms_Integration
 *
 * @ignore
 */
class MB4WP_Gravity_Forms_Integration extends MB4WP_Integration {

    /**
     * @var string
     */
    public $name = "Gravity Forms";

    /**
     * @var string
     */
    public $description = "Subscribe visitors from your Gravity Forms forms.";


    /**
     * Add hooks
     */
    public function add_hooks() {
        add_action( 'gform_field_standard_settings', array( $this, 'settings_fields' ), 10, 2);
        add_action( 'gform_editor_js', array( $this, 'editor_js' ) );
        add_action( 'gform_after_submission', array( $this, 'after_submission' ), 10, 2 );
    }

    public function after_submission( $submission, $form ) {

        $subscribe = false;
        $email_address = '';
        $mailblaze_list_id = '';
        $double_optin = $this->options['double_optin'];

        // find email field & checkbox value
        foreach( $form['fields'] as $field ) {
            if( $field->type === 'email' && empty( $email_address ) && ! empty( $submission[ $field->id ] ) ) {
                $email_address = $submission[ $field->id ];
            }

            if( $field->type === 'mailblaze' && ! empty( $submission[ $field->id ] ) ) {
                $subscribe = true;
                $mailblaze_list_id = $field->mailblaze_list;

                if( isset( $field->mailblaze_double_optin ) ) {
                    $double_optin = $field->mailblaze_double_optin;
                }
            }
        }

        if( ! $subscribe || empty( $email_address ) ) {
            return;
        }

        // override integration settings with field options
        $orig_options = $this->options;
        $this->options['lists'] = array( $mailblaze_list_id );
        $this->options['double_optin'] = $double_optin;

        // perform the sign-up
        $this->subscribe( array( 'EMAIL' => $email_address ), $submission['form_id'] );

        // revert back to original options in case request lives on
        $this->options = $orig_options;
    }

    public function editor_js() {
        ?>
        <script type="text/javascript">
            /*
            * When the field settings are initialized, populate
            * the custom field setting.
            */
            jQuery(document).on('gform_load_field_settings', function(ev, field) {
                jQuery('#field_mailblaze_list').val(field.mailblaze_list || '');
                jQuery('#field_mailblaze_double_optin').val(field.mailblaze_double_optin || "1");
            });
        </script>
        <?php
    }

    public function settings_fields( $pos, $form_id ) {
        if( $pos !== 0 ) { 
            return; 
        }
        
        $mailblaze = new MB4WP_MailBlaze();
        $lists = $mailblaze->get_cached_lists();
        ?>
        <li class="mailblaze_list_setting field_setting">
            <label for="field_mailblaze_list" class="section_label">
                <?php esc_html_e( 'MailBlaze list', 'mailblaze-for-wp' ); ?>
            </label>
            <select id="field_mailblaze_list" onchange="SetFieldProperty('mailblaze_list', this.value)">
                <option value="" disabled><?php _e( 'Select a MailBlaze list', 'mailblaze-for-wp' ); ?></option>
                <?php foreach( $lists as $list ) {
                    echo sprintf( '<option value="%s">%s</option>', $list->id, $list->name );
                } ?>
            </select>
        </li>
        <li class="mailblaze_double_optin field_setting">
            <label for="field_mailblaze_double_optin" class="section_label">
                <?php esc_html_e( 'Double opt-in?', 'mailblaze-for-wp' ); ?>
            </label>
            <select id="field_mailblaze_double_optin" onchange="SetFieldProperty('mailblaze_double_optin', this.value)">
                <option value="1"><?php echo __( 'Yes' ); ?></option>
                <option value="0"><?php echo __( 'No' ); ?></option>
            </select>
        </li>
        <?php
    }

    /**
     * @return bool
     */
    public function is_installed() {
        return class_exists( 'GF_Field' ) && class_exists( 'GF_Fields' );
    }

    /**
     * @since 3.0
     * @return array
     */
    public function get_ui_elements() {
        return array();
    }

    /**
     * @param int $form_id
     * @return string
     */
    public function get_object_link( $form_id ) {
        return '<a href="'. admin_url( sprintf( 'admin.php?page=gf_edit_forms&id=%d', $form_id ) ) . '">Gravity Forms</a>';
    }

}
