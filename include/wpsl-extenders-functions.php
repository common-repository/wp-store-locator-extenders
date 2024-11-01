<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Run when the plugin is actived.
 * 
 * Check whether the plugin is activated network wide.
 *
 * @since 1.0.0
 * @param boolean $network_wide True when the plugin is activated network wide.
 * @return void
 */
function wpsl_ext_install( $network_wide ) {

	require_once( WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php' );

	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		if ( $network_wide ) {
			$args = array(
				'archived' => 0,
				'spam'     => 0,
				'deleted'  => 0
			);

			// As of WP 4.6 use get_sites instead of wp_get_sites.
			if ( function_exists( 'get_sites' ) ) {
				$mu_sites = get_sites( $args );
			} else {
				$mu_sites = wp_get_sites( $args );
			}

			if ( $mu_sites ) {
				foreach ( $mu_sites as $mu_site ) {
					$mu_site = (array) $mu_site;

					switch_to_blog( $mu_site['blog_id'] );

					wpsl_ext_create_roles();
				} 
			}

			restore_current_blog();     
		} else {
			wpsl_ext_create_roles();
		}
	} else {
		wpsl_ext_create_roles();
	}
}

/**
 * Simplify the plugin debugMP interface.
 *
 * @param string $type
 * @param string $hdr
 * @param string $msg
 */
function wpsl_ext_make_slug($title = '', $prefix = '', $use_dashes = true ) {

	$wpsl_ext_slug = $prefix . $title;
	$wpsl_ext_slug = sanitize_key( $wpsl_ext_slug );
	if ( $use_dashes ) {
		$wpsl_ext_slug = str_replace('_','-',$wpsl_ext_slug);
	} else {
		$wpsl_ext_slug = str_replace('-','_',$wpsl_ext_slug);
	}

	return $wpsl_ext_slug;
}

/**
 * Upload directory issue warning.
 */
function wpsl_ext_add_notice( $notice_level = '', $notice_string = '' ) {
	global $wpsl_ext_notices;

	$wpsl_ext_notices->save( $notice_level, $notice_string );
	// WPSL_EXT_debugMP('msg', '========================================> NOTICE!!! level[' . $notice_level . ']: ' . $notice_string);
}

/**
 * Check if we need to run the uninstall for a single or mu installation.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_ext_uninstall() {
	if ( !is_multisite() ) {
		wpsl_ext_uninstall_single_blog();
	} else {

		global $wpdb;

		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		$original_blog_id = get_current_blog_id();

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			wpsl_ext_uninstall_single_blog();
		}

		switch_to_blog( $original_blog_id );
	}
}

/**
 * Remove the Extenders roles on uninstall.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_ext_uninstall_single_blog() {

	// Remove the Extenders caps.
	require_once( WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php' );
	wpsl_ext_remove_caps_and_roles();

	// Remove the Extenders options.
	delete_option( WPSL_EXT_OPTION_NAME );
}
