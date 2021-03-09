<?php

mb4wp_register_integration( 'ninja-forms', 'MB4WP_Ninja_Forms_Integration', true );

if( class_exists( 'Ninja_Forms' ) && method_exists( 'Ninja_Forms', 'instance' ) ) {
    $ninja_forms = Ninja_Forms::instance();

    if( isset( $ninja_forms->fields ) ) {
        $ninja_forms->fields['mb4wp_optin'] = new MB4WP_Ninja_Forms_Field();
    }

    if( isset( $ninja_forms->actions ) ) {
        $ninja_forms->actions['mb4wp_subscribe'] = new MB4WP_Ninja_Forms_Action();
    }
}
