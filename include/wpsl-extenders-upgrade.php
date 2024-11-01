<?php
add_action( 'admin_init',                      'wpsl_ext_check_upgrade' );

/**
 * If the db doesn't hold the current version, run the upgrade procedure
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_ext_check_upgrade() {

	$current_version  = '';
	$wpsl_ext_options = get_option( WPSL_EXT_OPTION_NAME );
	if ( isset( $wpsl_ext_options[ 'wpsl_ext_version' ] ) ) {
		$current_version  = $wpsl_ext_options[ 'wpsl_ext_version' ];
	}

	if ( version_compare( $current_version, WPSL_EXT_VERSION_NUM, '===' ) ) {
		return;
	}

	if ( version_compare( $current_version, '0.1.0', '<' ) ) {
		require_once( WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php' );

		wpsl_ext_create_roles();
	}

	// Update the options.
	$wpsl_ext_options['wpsl_ext_version'] = WPSL_EXT_VERSION_NUM;
	update_option( WPSL_EXT_OPTION_NAME, $wpsl_ext_options );
}
