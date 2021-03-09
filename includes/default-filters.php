<?php

defined( 'ABSPATH' ) or exit;

add_filter( 'mb4wp_form_data', 'mb4wp_add_name_data', 60 );
add_filter( 'mb4wp_integration_data', 'mb4wp_add_name_data', 60 );

add_filter( 'mctb_data', '_mb4wp_update_groupings_data', PHP_INT_MAX - 1 );
add_filter( 'mb4wp_form_data', '_mb4wp_update_groupings_data', PHP_INT_MAX - 1 );
add_filter( 'mb4wp_integration_data', '_mb4wp_update_groupings_data', PHP_INT_MAX - 1 );
add_filter( 'mailblaze_sync_user_data', '_mb4wp_update_groupings_data', PHP_INT_MAX - 1 );
add_filter( 'mb4wp_use_sslverify', '_mb4wp_use_sslverify', 1 );
