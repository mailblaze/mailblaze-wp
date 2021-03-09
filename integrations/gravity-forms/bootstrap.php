<?php

defined( 'ABSPATH' ) or exit;

mb4wp_register_integration( 'gravity-forms', 'MB4WP_Gravity_Forms_Integration', true );

if ( class_exists( 'GF_Fields' ) ) {
    GF_Fields::register( new MB4WP_Gravity_Forms_Field() );
}
