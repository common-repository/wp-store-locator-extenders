<?php

// Define some constants for use by this add-on
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SECTION_PREFIX', 'wpsl_' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SECTION_PARAM', 'wpsl_ext_section' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SECTION_ALL', 'wpsl_ext_section_all' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SECTION_EXT', 'wpsl_ext_section_general' );
//
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SECTION_UML', 'wpsl_ext_section_usermanaged' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SECTION_SETTINGS', 'wpsl_ext_section_settings' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_TEXT', 'wpsl_ext_text' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_TEXTAREA', 'wpsl_ext_textarea' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_CHECKBOX', 'wpsl_ext_checkbox' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_DROPDOWN', 'wpsl_ext_dropdown' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_SUBHEADER', 'wpsl_ext_subheader' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_READONLY', 'wpsl_ext_readonly' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_BUTTON', 'wpsl_ext_button' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_CUSTOM', 'wpsl_ext_custom' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_ICONLIST', 'wpsl_ext_iconlist' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_HIDDEN', 'wpsl_ext_hidden' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_SETTINGS_TYPE_DATETIME', 'wpsl_ext_datetime' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_STORE_USER_SLUG', 'users' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_STORE_USER_COL_ALLOWED', 'wpsl_user_col_allowed' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_STORE_USER_COL_LOCATIONS', 'wpsl_user_col_locations' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_SAVE', 'wpsl_ext_action_save' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_UPDATE', 'wpsl_ext_action_update' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_REQUEST', 'wpsl_ext_action_request' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_SETTINGS', 'wpsl_ext_action_settings' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_USER_ALLOW', 'wpsl_ext_action_user_allow' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_USER_DISALLOW', 'wpsl_ext_action_user_disallow' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_ACTION_STORE_SEARCH', 'store_search' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_NOTICE_SUCCESS', 'success' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_NOTICE_INFO', 'info' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_NOTICE_WARNING', 'warning' );
wpsl_ext_maybe_define_constant( 'WPSL_EXT_NOTICE_ERROR', 'error' );
if ( !class_exists( 'WPSL_Extenders' ) ) {
    class WPSL_Extenders
    {
        public  $ext_date_format ;
        public  $ext_time_format ;
        public  $ext_datetime_format ;
        /**
         * Class constructor.
         */
        function __construct()
        {
            // Create objects for options
            WPSL_EXT_create_object( 'WPSL_EXT_Options', 'include/' );
            $this->maybe_update_wpsl();
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
        }
        
        /**
         * Make sure WPSL meets the min required version,
         * before including the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function maybe_update_wpsl()
        {
            $this->includes();
            // Make sure WP Store Locator itself is active and has the right version.
            
            if ( !class_exists( 'WP_Store_locator' ) ) {
                add_action( 'all_admin_notices', array( $this, 'install_wpsl_notice' ) );
                return;
            }
            
            
            if ( version_compare( WPSL_VERSION_NUM, WPSL_EXT_MIN_WPSL, '<' ) ) {
                add_action( 'all_admin_notices', array( $this, 'update_wpsl_notice' ) );
                return;
            }
            
            $this->setup_license();
            $this->initialize();
            $this->add_hooks_and_filters();
            add_action( 'init', array( $this, 'init_includes' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'add_frontend_styles' ) );
        }
        
        /**
         * Show a notice telling the user to update WPSL
         * before they can use this add-on.
         *
         * @since 1.0.0
         * @return void
         */
        public function install_wpsl_notice()
        {
            echo  '<div class="error"><p>' . sprintf( __( 'Please install and activate the latest version of %s WP Store Locator%s before using the WPSL Extenders add-on.', 'wp-store-locator-extenders' ), '<a href="https://wordpress.org/plugins/wp-store-locator/">', '</a>' ) . '</p></div>' ;
        }
        
        /**
         * Show a notice telling the user to update WPSL
         * before they can use this add-on.
         *
         * @since 1.0.0
         * @return void
         */
        public function update_wpsl_notice()
        {
            echo  '<div class="error"><p>' . sprintf( __( 'Please upgrade WP Store Locator to the %s latest version%s before using the WPSL Extenders add-on.', 'wp-store-locator-extenders' ), '<a href="https://wordpress.org/plugins/wp-store-locator/">', '</a>' ) . '</p></div>' ;
        }
        
        /**
         * Run these things during invocation. (called from base object in __construct)
         */
        protected function initialize()
        {
            // Get date and time formats
            $this->ext_date_format = get_option( 'date_format' );
            if ( empty($this->ext_date_format) ) {
                $this->ext_date_format = 'dd-mm-yyyy';
            }
            $this->ext_time_format = get_option( 'time_format' );
            if ( empty($this->ext_time_format) ) {
                $this->ext_time_format = 'H:i';
            }
            $this->ext_datetime_format = $this->ext_date_format . ' ' . $this->ext_time_format;
        }
        
        /**
         * Add cross-element hooks & filters.
         *
         * Haven't yet moved all items to the AJAX and UI classes.
         */
        function add_hooks_and_filters()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Add Extenders info to the content of the wpsl_store Custom Post Type
            add_filter( 'the_content', array( $this, 'wpsl_ext_cpt_template' ) );
            // Add an extended wpsl_templates
            add_filter( 'wpsl_templates', array( $this, 'wpsl_ext_wpsl_templates' ) );
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes()
        {
            require_once WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-functions.php';
            require_once WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php';
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function init_includes()
        {
            // Create objects for functionality
            WPSL_EXT_create_object( 'WPSL_EXT_UserManaged', 'include/usermanaged/' );
            if ( is_admin() ) {
                // require_once( WPSL_EXT_PLUGIN_DIR . 'include/admin/class-WPSL_EXT_Admin.php' );
                WPSL_EXT_create_object( 'WPSL_EXT_Admin', 'include/admin/' );
            }
        }
        
        /**
         * Handle the addon license.
         *
         * @since 1.0.0
         * @return void
         */
        public function setup_license()
        {
            // if ( class_exists( 'WPSL_License_Manager' ) ) {
            // $license = new WPSL_License_Manager( 'WPSL Extenders', WPSL_EXT_VERSION_NUM, 'DeBAAT', __FILE__ );
            // }
        }
        
        /**
         * Load the required css styles.
         *
         * @since 1.0.0
         * @return void
         */
        public function add_frontend_styles()
        {
            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
            wp_enqueue_style(
                'wpsl-ext-styles',
                WPSL_EXT_PLUGIN_URL . '/css/wpsl-ext-styles' . $min . '.css',
                '',
                WPSL_VERSION_NUM
            );
        }
        
        /**
         * Load the translations from the language folder.
         *
         * @since 1.0.0
         * @return void
         */
        public function load_plugin_textdomain()
        {
            $domain = 'wp-store-locator-extenders';
            $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
            // Load the language file from the /wp-content/languages/wp-store-locator-extenders folder, custom + update proof translations
            load_textdomain( $domain, WP_LANG_DIR . '/wp-store-locator-extenders/' . $domain . '-' . $locale . '.mo' );
            // Load the language file from the /wp-content/plugins/wp-store-locator-extenders/languages/ folder
            load_plugin_textdomain( $domain, false, dirname( WPSL_EXT_BASENAME ) . '/languages/' );
        }
        
        /**
         * Create a timestamp for the current time
         *
         * @return timestamp
         */
        function create_timestamp_now( $timezone_format = '' )
        {
            if ( $timezone_format == '' ) {
                $timezone_format = _x( 'Y-m-d G:i:s', 'timezone date format', 'wp-store-locator-extenders' );
            }
            return date_i18n( $timezone_format );
        }
        
        /**
         * Add to the wpsl post type output.
         *
         * If you want to create a custom template you need to
         * create a single-WPSL_POST_TYPE.php file in your theme folder.
         * You can see an example here https://wpstorelocator.co/document/create-custom-store-page-template/
         *
         * @since  5.8.0
         * @param  string $content
         * @return string $content
         */
        public function wpsl_ext_cpt_template( $content )
        {
            global  $post ;
            global  $wpsl_ext_usermanaged ;
            $skip_cpt_template = apply_filters( 'wpsl_skip_cpt_template', false );
            // Only add content for the right post_type
            if ( isset( $post->post_type ) && $post->post_type == WPSL_POST_TYPE && is_single() && in_the_loop() && !$skip_cpt_template ) {
                // $content .= '[wpsl_EXTENDERS]';
                $content .= $wpsl_ext_usermanaged->wpsl_show_user_buttons( $post->ID );
            }
            return $content;
        }
        
        /**
         * Include an extended wpsl_templates.
         *
         * @since 1.0.0
         * @return void
         */
        public function wpsl_ext_wpsl_templates( $templates )
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            $templates['extended_default'] = array(
                'id'   => 'extended_default',
                'name' => __( 'Default with extended search options', 'wp-store-locator-extenders' ),
                'path' => WPSL_GFL_PLUGIN_DIR . 'templates/ext-default.php',
            );
            $templates['extended_below_map'] = array(
                'id'   => 'extended_below_map',
                'name' => __( 'Below with extended search options', 'wp-store-locator-extenders' ),
                'path' => WPSL_GFL_PLUGIN_DIR . 'templates/ext-store-listings-below.php',
            );
            $this->debugMP( 'pr', __FUNCTION__ . ' returning templates:', $templates );
            return $templates;
        }
        
        /**
         * Simplify the plugin debugMP interface.
         *
         * Typical start of function call: $this->debugMP('msg',__FUNCTION__);
         *
         * @param string $type
         * @param string $hdr
         * @param string $msg
         */
        function debugMP( $type, $hdr, $msg = '' )
        {
            if ( $type === 'msg' && $msg !== '' ) {
                $msg = esc_html( $msg );
            }
            if ( $hdr !== '' ) {
                // Adding __CLASS__ to non-empty hdr
                $hdr = __CLASS__ . '::' . $hdr;
            }
            WPSL_EXT_debugMP(
                $type,
                $hdr,
                $msg,
                NULL,
                NULL,
                true
            );
        }
    
    }
}