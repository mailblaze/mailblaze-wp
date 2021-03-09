<?php

mb4wp_register_integration( 'wpforms', 'MB4WP_WPForms_Integration', true );

function _mb4wp_wpforms_register_field() {
    if( ! class_exists( 'WPForms_Field' ) ) {
        return;
    }

    new MB4WP_WPForms_Field();
}

add_action( 'init', '_mb4wp_wpforms_register_field' );
