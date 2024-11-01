<?php

/**
 * Handle the WPSL Extenders plugin options
 *
 * @author DeBAAT
 * @since  1.2.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !class_exists( 'WPSL_EXT_Options' ) ) {
    class WPSL_EXT_Options
    {
        /**
         * Settable options for this plugin.
         *
         * @var mixed[] $options
         */
        public  $ext_options = array() ;
        /**
         * Settable options for this plugin.
         *
         * @var mixed[] $options
         */
        public  $default_ext_options = array(
            'wpsl_ext_version'             => WPSL_EXT_NO_INSTALLED_VERSION,
            'ext_uml_publish_location'     => '1',
            'ext_uml_default_user_allowed' => '1',
            'ext_uml_show_uml_buttons'     => '0',
        ) ;
        public function __construct()
        {
            // Get some settings
            $this->initialize();
        }
        
        /**
         * Initialize this options object
         * 
         * @since 1.2.0
         * @return html
         */
        public function initialize()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Get some settings
            $this->ext_options = get_option( WPSL_EXT_OPTION_NAME );
            $this->check_options();
        }
        
        /**
         * Check updates for the plugin.
         *
         * @since 1.0.0
         * @return void
         */
        public function check_options()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Add default values for options if not yet available
            if ( !isset( $this->ext_options['wpsl_ext_version'] ) || $this->ext_options['wpsl_ext_version'] === WPSL_EXT_NO_INSTALLED_VERSION || !isset( $this->ext_options['ext_uml_publish_location'] ) ) {
                // Copy default options
                $this->ext_options = $this->default_ext_options;
            }
        }
        
        /**
         * Determines whether an option is true or not
         * 
         * @since 1.2.0
         * @return html
         */
        public function is_true( $option_name = '' )
        {
            // $this->debugMP('msg',__FUNCTION__.' started for ' . $option_name );
            // Check whether the option requested is valid
            if ( $option_name == '' ) {
                return false;
            }
            // Check whether the option exists
            if ( !isset( $this->ext_options[$option_name] ) ) {
                return false;
            }
            // Return the boolean value of the option
            if ( $this->ext_options[$option_name] ) {
                return true;
            }
            return false;
        }
        
        /**
         * Get the value of the option requested, if it exists
         * 
         * @since 1.2.0
         * @return html
         */
        public function get_value( $option_name = '' )
        {
            // $this->debugMP('msg',__FUNCTION__.' started for ' . $option_name );
            // Check whether the option requested is valid
            if ( $option_name == '' ) {
                return '';
            }
            // If option does not exist, return default value
            if ( !isset( $this->ext_options[$option_name] ) ) {
                return $this->default_ext_options[$option_name];
            }
            return $this->ext_options[$option_name];
        }
        
        /**
         * Set the value of the option requested
         * 
         * @since 1.2.0
         * @return html
         */
        public function set_value( $option_name = '', $new_value = '' )
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started for ' . $option_name );
            // Check whether the option requested is valid
            if ( $option_name == '' ) {
                return;
            }
            // Set the new option value
            $this->ext_options[$option_name] = $new_value;
        }
        
        /**
         * Write the options back to the database
         * 
         * @since 1.2.0
         * @return html
         */
        public function update_ext_options()
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            // Get some settings
            $this->debugMP( 'pr', __FUNCTION__ . ' update_ext_options to:', $this->ext_options );
            update_option( WPSL_EXT_OPTION_NAME, $this->ext_options );
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