<?php

/**
 * Handle the WPSL Extenders plugin settings.
 *
 * @author DeBAAT
 * @since  1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WPSL_EXT_Settings' ) ) {
    class WPSL_EXT_Settings
    {
        /**
         * Parameters for handling the settable options for this plugin.
         *
         * @var mixed[] $options
         */
        public  $ext_settings_params = array() ;
        public function __construct()
        {
            // Get some settings
            $this->initialize();
        }
        
        public function initialize()
        {
            // Get some settings
            $this->set_ext_settings_params();
        }
        
        /**
         * Render the settings for options
         * 
         * @since 1.0.0
         * @return html
         */
        public function set_ext_settings_params()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Reset ext_settings_params
            $this->ext_settings_params = array();
            $this->set_ext_settings_params_usermanaged();
        }
        
        /**
         * Render the settings for user managed options
         * 
         * @since 1.0.0
         * @return html
         */
        public function set_ext_settings_params_usermanaged()
        {
            $ext_settings_params_section = WPSL_EXT_SECTION_UML;
            $this->debugMP( 'msg', __FUNCTION__ . ' started for section ' . $ext_settings_params_section );
            global  $wpsl_ext_usermanaged_options ;
            // Create and run the WPSL_EXT_UserManaged_Options class
            WPSL_EXT_create_object( 'WPSL_EXT_UserManaged_Options', 'include/usermanaged/' );
            $this->ext_settings_params = $wpsl_ext_usermanaged_options->set_ext_settings_params( $this->ext_settings_params );
        }
        
        /**
         * Render the settings.
         * 
         * @since 1.0.0
         * @return html contents
         */
        public function wpsl_ext_render_section()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Check actions for settings
            $this->wpsl_ext_check_action_settings();
            // Render the output for this section
            return $this->wpsl_ext_render_section_output();
        }
        
        /**
         * Render the settings.
         * 
         * @since 1.0.0
         * @return html contents
         */
        public function wpsl_ext_check_action_settings()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            if ( isset( $_REQUEST[WPSL_EXT_ACTION_REQUEST] ) && sanitize_key( $_REQUEST[WPSL_EXT_ACTION_REQUEST] ) == WPSL_EXT_ACTION_SETTINGS ) {
                $this->process_ext_settings();
            }
        }
        
        /**
         * Sanitize the submitted plugin settings.
         * 
         * @since 1.0.0
         * @return array $output The setting values
         */
        public function process_ext_settings()
        {
            global  $wpsl_ext_render_settings ;
            global  $wpsl_ext_options ;
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            $output = array();
            // Check if the ext_checkboxes are checked.
            //
            $settings_post_values = $wpsl_ext_render_settings->get_post_values( $this->ext_settings_params );
            foreach ( $this->ext_settings_params as $settings_name => $settings_params ) {
                $new_option_value = ( isset( $settings_post_values[$settings_name] ) ? $settings_post_values[$settings_name] : '' );
                $wpsl_ext_options->set_value( $settings_name, $new_option_value );
            }
            // Update the changed options.
            $wpsl_ext_options->update_ext_options();
            $this->set_ext_settings_params();
        }
        
        /**
         * Render the settings.
         * 
         * @since 1.0.0
         * @return html contents
         */
        public function wpsl_ext_render_section_output()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            global  $wpsl_ext_render_settings ;
            // Set some defaults
            $render_output = '';
            $input_form_action = admin_url( 'edit.php?post_type=' . WPSL_POST_TYPE . '&page=' . WPSL_EXT_ADMIN_MENU_SLUG . '&' . WPSL_EXT_SECTION_PARAM . '=' . WPSL_EXT_SECTION_SETTINGS );
            $input_hidden_template = '<input type="hidden" name="%s" value="%s"/>';
            // Input hidden items
            //
            $input_hidden_items = array(
                'option_page'           => WPSL_EXT_ADMIN_MENU_SLUG,
                WPSL_EXT_SECTION_PARAM  => WPSL_EXT_SECTION_SETTINGS,
                WPSL_EXT_ACTION_REQUEST => WPSL_EXT_ACTION_SETTINGS,
            );
            // Render top section header
            //
            $render_output .= '<div id="wplsl_ext_uml_settings" class="wrap wpsl-settings">';
            $render_output .= '<form id="wpsl-ext-settings-form" method="post" action="' . $input_form_action . '" autocomplete="off" accept-charset="utf-8">';
            // Render input_hidden_items
            //
            foreach ( $input_hidden_items as $input_hidden_name => $input_hidden_value ) {
                $render_output .= sprintf( $input_hidden_template, $input_hidden_name, $input_hidden_value );
            }
            // Render the settings sections
            //
            $button_label = __( 'Save Settings', 'wp-store-locator-extenders' );
            $header_label = __( 'User Managed Locations Settings', 'wp-store-locator-extenders' );
            $render_output .= $wpsl_ext_render_settings->render_settings_sections(
                WPSL_EXT_SECTION_UML,
                $this->ext_settings_params,
                $header_label,
                $button_label
            );
            // Close top section header and return output
            //
            $render_output .= '</form>';
            // for id="wpsl-settings-form"
            $render_output .= '</div>';
            // for id="wplsl_ext_uml_settings"
            return $render_output;
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
    // $GLOBALS['wpsl_ext_settings'] = new WPSL_EXT_Settings();
}
