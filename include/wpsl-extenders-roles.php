<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add WPSL Extenders Roles to all admin.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_ext_create_roles() {

	$role = get_role( 'administrator' );
	if ( is_object( $role ) ) {
		$role->add_cap( WPSL_EXT_CAP_MANAGE_WPSL          );
		$role->add_cap( WPSL_EXT_CAP_MANAGE_WPSL_ADMIN    );
		$role->add_cap( WPSL_EXT_CAP_MANAGE_WPSL_SETTINGS );
		$role->add_cap( WPSL_EXT_CAP_MANAGE_WPSL_USER     );
	}

}

/**
 * Check whether the current user is a Store Admin.
 *
 * @param boolean $noAdmin - whether to validate for non-admins only, default = false
 * @return boolean
 */
function wpsl_ext_is_admin( $noAdmin = false ) {
	WPSL_EXT_debugMP('msg',__FUNCTION__.' started.');

	// User must be logged in
	if (!is_user_logged_in()) { return false; }

	// User can be wordpress admin
	if ($noAdmin && current_user_can('manage_options')) { return true; }

	// Check what current_user_can manage
	if (current_user_can(WPSL_EXT_CAP_MANAGE_WPSL_ADMIN)) { return true; }

	return false;
}

/**
 * Check whether the current_user is allowed to manage locations.
 * Admin is always allowed
 *
 * @param string $userLogin - the login name of the user to check
 * @return boolean
 */
function wpsl_ext_is_user( $noAdmin = false ) {
	WPSL_EXT_debugMP('msg',__FUNCTION__.' started.');

	// User must be logged in
	if ( !is_user_logged_in() ) { return false; }

	// User can be wordpress admin
	if ($noAdmin && current_user_can('manage_options')) { return true; }

	// Check requested user has WPSL_EXT_CAP_MANAGE_WPSL_USER
	if (current_user_can(WPSL_EXT_CAP_MANAGE_WPSL_USER)) { return true; }

	return false;
}

/**
 * Check whether the user is allowed to manage locations
 * Admin is always allowed
 *
 * @param string $uml_user_id - the id of the user to check
 * @return boolean
 */
function wpsl_ext_is_user_allowed( $uml_user_id = '' ) {
	WPSL_EXT_debugMP('msg',__FUNCTION__.' started.');

	// User must be logged in
	if ($uml_user_id == '') { return false; }

	// Check requested user has WPSL_EXT_CAP_MANAGE_WPSL_USER
	$cur_user = get_user_by( 'id', $uml_user_id );
	if ( $cur_user ) {
		//$this->debugMP('pr',__FUNCTION__ . ': get_user_by(id, ' . $uml_user_id . ' ) found: ',$cur_user);
		return $cur_user->has_cap(WPSL_EXT_CAP_MANAGE_WPSL_USER);
	}

	return false;
}

/**
 * Allow the user for User Managed Locations
 *
 * @params string $uml_user_id the id of the user to allow User Managed Locations
 * @return boolean true when success
 */
function wpsl_ext_user_allow( $uml_user_id = '' ) {
	WPSL_EXT_debugMP('msg', __FUNCTION__ . ' started for user :' . $uml_user_id . ' .');

	// Validate access and parameters
	if ( ! wpsl_ext_is_admin() ) { return false; }
	if ( $uml_user_id == '' )    { return false; }

	$user = get_user_by( 'id', $uml_user_id );
	if ( ! $user ) { return false; }

	$user->add_cap( WPSL_EXT_CAP_MANAGE_WPSL );
	$user->add_cap( WPSL_EXT_CAP_MANAGE_WPSL_USER );
	WPSL_EXT_debugMP('pr',__FUNCTION__ . ' user = ', $user);

	return true;
}

/**
 * Disallow the user for User Managed Locations
 *
 * @params string $uml_user_id the id of the user to disallow User Managed Locations
 * @return boolean true when success
 */
function wpsl_ext_user_disallow( $uml_user_id = '' ) {
	WPSL_EXT_debugMP('msg', __FUNCTION__ . ' started for user :' . $uml_user_id . ' .');

	// Validate access and parameters
	if ( ! wpsl_ext_is_admin() ) { return false; }
	if ( $uml_user_id == '' )    { return false; }

	$user = get_user_by( 'id', $uml_user_id );
	if ( ! $user ) { return false; }

	$user->remove_cap( WPSL_EXT_CAP_MANAGE_WPSL );
	$user->remove_cap( WPSL_EXT_CAP_MANAGE_WPSL_USER );
	WPSL_EXT_debugMP('pr',__FUNCTION__ . ' user = ', $user);

	return true;
}

/**
 * Get the WPSL EXT Manager capabilities.
 *
 * @since 1.0.0
 * @return array $capabilities The EXT Manager capabilities
 */
function wpsl_ext_get_post_caps() {

	$capabilities = array(
		'wpsl_ext_manager',
		'wpsl_ext_manager_export',
		'wpsl_ext_manager_tools'
	);

	return $capabilities;
}

/**
 * Remove the WPSL caps and roles.
 * 
 * Only called from uninstall.php
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_ext_remove_caps_and_roles() {
	  
	global $wp_roles;

	if ( class_exists( 'WP_Roles' ) ) {
		if ( !isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
	}
	
	if ( is_object( $wp_roles ) ) {
		$capabilities = wpsl_ext_get_post_caps();
		
		foreach ( $capabilities as $cap ) {
			$wp_roles->remove_cap( 'wpsl_store_locator_manager', $cap );
			$wp_roles->remove_cap( 'administrator',              $cap );
		}
	}
}