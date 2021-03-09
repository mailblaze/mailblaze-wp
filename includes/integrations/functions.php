<?php

/**
 * Gets an array of all registered integrations
 *
 * @since 3.0
 * @access public
 *
 * @return MB4WP_Integration[]
 */
function mb4wp_get_integrations() {
	return mb4wp('integrations')->get_all();
}

/**
 * Get an instance of a registered integration class
 *
 * @since 3.0
 * @access public
 *
 * @param string $slug
 *
 * @return MB4WP_Integration
 */
function mb4wp_get_integration( $slug ) {
	return mb4wp('integrations')->get( $slug );
}

/**
 * Register a new integration with MailBlaze for WordPress
 *
 * @since 3.0
 * @access public
 *
 * @param string $slug
 * @param string $class
 *
 * @param bool $always_enabled
 */
function mb4wp_register_integration( $slug, $class, $always_enabled = false ) {
	return mb4wp('integrations')->register_integration( $slug, $class, $always_enabled );
}

/**
 * Deregister a previously registered integration with MailBlaze for WordPress
 *
 * @since 3.0
 * @access public
 * @param string $slug
 */
function mb4wp_deregister_integration( $slug ) {
	mb4wp('integrations')->deregister_integration( $slug );
}