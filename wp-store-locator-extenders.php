<?php

/*
Plugin URI:  https://www.de-baat.nl/wp-store-locator-extenders/
Plugin Name: WP Store Locator - Extenders
Description: Manage the users
Author:      DeBAAT
Author URI:  https://www.de-baat.nl/
Version:     1.4.0
Text Domain: wp-store-locator-extenders
Domain Path: /languages/
License:     GPL v3


Copyright (C) 2020 DeBAAT wpsl@de-baat.nl

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
defined( 'ABSPATH' ) || exit;
// Define some constants for use by this add-on
wpsl_ext_maybe_define_constant( 'WPSL_EXT_FREEMIUS_ID', '7247' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SHORT_SLUG', 'wp-store-locator-extenders' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_PREMIUM_SLUG', 'wp-store-locator-extenders-premium' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_CLASS_PREFIX', 'WPSL_Extenders_' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ADMIN_PAGE_SLUG', 'wpsl_ext_admin_menu' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ADMIN_PAGE_SLUG_FRE', 'wpsl_ext_admin_menu-pricing' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_MIN_WPSL', '2.2.0' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_FILE', __FILE__ );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_REL_DIR', plugin_dir_path( WPSL_EXT_FILE ) );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_BASENAME', plugin_basename( __FILE__ ) );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_PLUGIN_URL', plugins_url( '', __FILE__ ) );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ADMIN_MENU_SLUG', 'wpsl_ext_admin_menu' );
wpsl_ext_maybe_define_constant( 'WPSL_POST_TYPE', 'wpsl_stores' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_OPTION_NAME', 'wp-store-locator-extenders-options' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_NOTICE_OPTION', 'wpsl_ext_notices' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_CAP_MANAGE_WPSL', 'wpsl_store_locator_manager' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_CAP_MANAGE_WPSL_ADMIN', 'wpsl_store_locator_admin' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_CAP_MANAGE_WPSL_SETTINGS', 'manage_wpsl_settings' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_CAP_MANAGE_WPSL_USER', 'wpsl_store_locator_user' );
if ( !function_exists( 'get_plugin_data' ) ) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$this_plugin = get_plugin_data( WPSL_EXT_FILE, false, false );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_VERSION_NUM', $this_plugin['Version'] );

if ( defined( 'WP_FS__DEV_MODE' ) ) {
    error_log( 'WPSL-EXT::' . __FILE__ . '::' . __LINE__ . '::' . __FUNCTION__ . ' : DEFINED WP_FS__DEV_MODE = ' . print_r( WP_FS__DEV_MODE, true ) );
} else {
    error_log( 'WPSL-EXT::' . __FILE__ . '::' . __LINE__ . '::' . __FUNCTION__ . ' : NOT defined WP_FS__DEV_MODE ' );
}

wpsl_ext_maybe_define_constant( 'WPSL_EXT_NO_INSTALLED_VERSION', '0.0.0' );
//
/**
 * Define a constant if it is not already defined.
 *
 * @param string $name  Constant name.
 * @param string $value Value.
 *
 * @since  1.0.0
 */
function wpsl_ext_maybe_define_constant( $name, $value )
{
    if ( !defined( $name ) ) {
        define( $name, $value );
    }
}

// Include Freemius SDK integration

if ( function_exists( 'wpsl_ext_freemius' ) ) {
    wpsl_ext_freemius()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'wpsl_ext_freemius' ) ) {
        // Create a helper function for easy SDK access.
        function wpsl_ext_freemius()
        {
            global  $wpsl_ext_freemius ;
            
            if ( !isset( $wpsl_ext_freemius ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wpsl_ext_freemius = fs_dynamic_init( array(
                    'id'               => WPSL_EXT_FREEMIUS_ID,
                    'slug'             => WPSL_EXT_SHORT_SLUG,
                    'premium_slug'     => WPSL_EXT_PREMIUM_SLUG,
                    'type'             => 'plugin',
                    'public_key'       => 'pk_1ae4da322847cdca51acea569a24e',
                    'is_premium'       => false,
                    'premium_suffix'   => 'Premium',
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => true,
                    'trial'            => array(
                    'days'               => 30,
                    'is_require_payment' => false,
                ),
                    'menu'             => array(
                    'slug'    => WPSL_EXT_ADMIN_PAGE_SLUG,
                    'account' => false,
                    'contact' => false,
                    'support' => false,
                    'parent'  => array(
                    'slug' => 'edit.php?post_type=' . WPSL_POST_TYPE,
                ),
                ),
                    'is_live'          => true,
                ) );
            }
            
            return $wpsl_ext_freemius;
        }
        
        // Init Freemius.
        wpsl_ext_freemius();
        // Signal that SDK was initiated.
        do_action( 'wpsl_ext_freemius_loaded' );
        function wpsl_ext_freemius_plugins_url()
        {
            return admin_url( 'plugins.php' );
        }
        
        function wpsl_ext_freemius_settings_url()
        {
            return admin_url( 'edit.php?post_type=' . WPSL_POST_TYPE . '&page=' . WPSL_EXT_ADMIN_PAGE_SLUG );
        }
        
        function wpsl_ext_freemius_pricing_url( $pricing_url )
        {
            $my_pricing_url = 'https://www.de-baat.nl/wp-store-locator-extenders/';
            WPSL_EXT_debugMP(
                'pr',
                __FUNCTION__ . ' Changed pricing_url:',
                $pricing_url . ' into my_pricing_url:',
                $my_pricing_url
            );
            return $my_pricing_url;
        }
        
        wpsl_ext_freemius()->add_filter( 'connect_url', 'wpsl_ext_freemius_settings_url' );
        wpsl_ext_freemius()->add_filter( 'after_skip_url', 'wpsl_ext_freemius_settings_url' );
        wpsl_ext_freemius()->add_filter( 'after_connect_url', 'wpsl_ext_freemius_settings_url' );
        wpsl_ext_freemius()->add_filter( 'after_pending_connect_url', 'wpsl_ext_freemius_settings_url' );
        wpsl_ext_freemius()->add_filter( 'pricing_url', 'wpsl_ext_freemius_pricing_url' );
    }
    
    /**
     * Get the Freemius object.
     *
     * @return string
     */
    function wpsl_ext_freemius_get_freemius()
    {
        return freemius( WPSL_EXT_FREEMIUS_ID );
    }
    
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX && !empty($_POST['action']) && $_POST['action'] === 'heartbeat' ) {
        return;
    }
    function WPSL_Extenders_loader()
    {
        // Make sure WP Store Locator itself is active.
        
        if ( !class_exists( 'WP_Store_locator' ) ) {
            WPSL_EXT_debugMP( 'msg', __FUNCTION__ . ' WP_Store_locator DOES NOT EXIST.' );
            // return;
        }
        
        require_once 'include/class-WPSL_Extenders.php';
        $GLOBALS['wpsl_extenders'] = new WPSL_Extenders();
    }
    
    add_action( 'plugins_loaded', 'WPSL_Extenders_loader' );
    function WPSL_Extenders_Get_Instance()
    {
        global  $wpsl_extenders ;
        return $wpsl_extenders;
    }
    
    function WPSL_Extenders_admin_init()
    {
        global  $_registered_pages ;
        global  $hook_suffix ;
        $_registered_pages[WPSL_POST_TYPE . '_page_' . WPSL_EXT_ADMIN_PAGE_SLUG] = true;
        // error_log( 'WPSL::' . __FILE__ . '::' . __LINE__ . '::' . __FUNCTION__ . ' : _registered_pages= ' . print_r( $_registered_pages, true ) );
    }
    
    function WPSL_Extenders_admin_menu()
    {
        global  $_registered_pages ;
        global  $hook_suffix ;
        $_registered_pages['admin_page_' . WPSL_EXT_ADMIN_PAGE_SLUG] = true;
        // error_log( 'WPSL::' . __FILE__ . '::' . __LINE__ . '::' . __FUNCTION__ . ' : _registered_pages= ' . print_r( $_registered_pages, true ) );
    }
    
    // Register the additional admin pages!!!
    add_action( 'admin_init', 'WPSL_Extenders_admin_init', 25 );
    add_action( 'user_admin_menu', 'WPSL_Extenders_admin_menu' );
    // ADMIN
    add_action( 'admin_menu', 'WPSL_Extenders_admin_menu' );
    // ADMIN
}

/**
 * Run when the Extenders plugin is activated.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_ext_activate( $network_wide )
{
    require_once WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-functions.php';
    wpsl_ext_install( $network_wide );
}

register_activation_hook( __FILE__, 'wpsl_ext_activate' );
// Use Freemius action to do uninstall
// Not like register_uninstall_hook(), you do NOT have to use a static function.
wpsl_ext_freemius()->add_action( 'after_uninstall', 'wpsl_ext_uninstall' );
/**
 * Create a Map Settings Debug My Plugin panel.
 *
 * @return null
 */
function WPSL_EXT_create_object( $class = '', $path = '' )
{
    if ( $class == '' ) {
        return;
    }
    if ( class_exists( $class ) == false ) {
        // require_once( WPSL_EXT_PLUGIN_DIR . 'include/usermanaged/class-WPSL_EXT_UserManaged.php' );
        require_once WPSL_EXT_PLUGIN_DIR . $path . 'class-' . $class . '.php';
    }
    // Create the object if not defined yet
    $global_var = strtolower( $class );
    
    if ( !isset( $GLOBALS[$global_var] ) ) {
        // error_log( 'WPSL::' . __FILE__ . '::' . __LINE__ . '::' . __FUNCTION__ . ' : creating class: ' . $class . ', for global_var: ' . $global_var );
        error_log( 'WPSL::' . __LINE__ . '::' . __FUNCTION__ . ' : creating class: ' . $class . ', for global_var: ' . $global_var );
        $GLOBALS[$global_var] = new $class();
    }

}

/**
 * Create a Map Settings Debug My Plugin panel.
 *
 * @return null
 */
function WPSL_EXT_create_DMPPanels()
{
    if ( !isset( $GLOBALS['DebugMyPlugin'] ) ) {
        return;
    }
    if ( class_exists( 'DMPPanelWPSLEXT' ) == false ) {
        require_once WPSL_EXT_PLUGIN_DIR . 'include/class.dmppanels.php';
    }
    $GLOBALS['DebugMyPlugin']->panels['wpsl.ext'] = new DMPPanelWPSLEXT();
}

add_action( 'dmp_addpanel', 'WPSL_EXT_create_DMPPanels' );
/**
 * Upload directory issue warning.
 */
function wpsl_upload_dir_notice()
{
    global  $wpsl_upload_error ;
    echo  "<div class='error'><p>" . __( 'WP Store Locator Extenders upload directory error.', 'wp-store-locator-extenders' ) . $wpsl_upload_error . "</p></div>" ;
}

/**
 * Simplify the plugin debugMP interface.
 *
 * @param string $type
 * @param string $hdr
 * @param string $msg
 */
function WPSL_EXT_debugMP(
    $type = 'msg',
    $header = '',
    $message = '',
    $file = null,
    $line = null,
    $notime = true
)
{
    $panel = 'wpsl.ext';
    // Only use error_log when WP_DEBUG_LOG_WPSL_EXT defined as true in wp-config
    if ( defined( 'WP_DEBUG_LOG_WPSL_EXT' ) && WP_DEBUG_LOG_WPSL_EXT ) {
        switch ( strtolower( $type ) ) {
            case 'pr':
                error_log( 'HDR: ' . $header . ' PR is no MSG ' . print_r( $message, true ) );
                break;
            default:
                error_log( 'HDR: ' . $header . ' MSG: ' . $message );
                break;
        }
    }
    // Panel not setup yet?  Return and do nothing.
    //
    if ( !isset( $GLOBALS['DebugMyPlugin'] ) || !isset( $GLOBALS['DebugMyPlugin']->panels[$panel] ) ) {
        return;
    }
    // Do normal real-time message output.
    //
    switch ( strtolower( $type ) ) {
        case 'pr':
            $GLOBALS['DebugMyPlugin']->panels[$panel]->addPR(
                $header,
                $message,
                $file,
                $line,
                $notime
            );
            break;
        default:
            $GLOBALS['DebugMyPlugin']->panels[$panel]->addMessage(
                $header,
                $message,
                $file,
                $line,
                $notime
            );
            break;
    }
}
