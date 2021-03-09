<?php
return array(
	'subscribed'               => array(
		'type' => 'success',
		'text' => __( 'Thank you for your sign up. Please click on the link in the email sent to you to confirm your subscription.', 'mailblaze-for-wp' )
	),
	'pending'               => array(
		'type' => 'success',
		'text' => __( 'Thank you. Please click on the link in the email we have sent you to confirm your subscription.', 'mailblaze-for-wp' )
	),
	'updated' 				   => array(
		'type' => 'success',
		'text' => __( 'Thank you, your records have been updated!', 'mailblaze-for-wp' ),
	),
	'unsubscribed'             => array(
		'type' => 'success',
		'text' => __( 'You were successfully unsubscribed.', 'mailblaze-for-wp' ),
	),
	'not_subscribed'           => array(
		'type' => 'notice',
		'text' => __( 'Given email address is not subscribed.', 'mailblaze-for-wp' ),
	),
	'error'                    => array(
		'type' => 'error',
		'text' => __( 'Oops. Something went wrong. Please try again later.', 'mailblaze-for-wp' ),
	),
	'invalid_email'            => array(
		'type' => 'error',
		'text' => __( 'Please provide a valid email address.', 'mailblaze-for-wp' ),
	),
	'already_subscribed'       => array(
		'type' => 'notice',
		'text' => __( 'Given email address is already subscribed, thank you!', 'mailblaze-for-wp' ),
	),
	'required_field_missing'   => array(
		'type' => 'error',
		'text' => __( 'Please fill in the required fields.', 'mailblaze-for-wp' ),
	),
	'no_lists_selected'        => array(
		'type' => 'error',
		'text' => __( 'Please select at least one list.', 'mailblaze-for-wp' )
	),
);
