<?php

/**
 * WPSL Extenders UserManaged class
 * 
 * @since  1.0.0
 * @author DeBAAT
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WPSL_EXT_Activate' ) ) {
    class WPSL_EXT_Activate
    {
        /**
         * Class constructor
         */
        function __construct()
        {
            $this->includes();
            $this->init();
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes()
        {
            require_once WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php';
        }
        
        /**
         * Init the required classes.
         *
         * @since 1.0.0
         * @return void
         */
        public function init()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // global $wpsl_ext_usermanaged;
            // global $wpsl_ext_events;
            // global $wpsl_ext_social;
        }
        
        /**
         * Check updates for the plugin.
         *
         * @since 1.0.0
         * @return void
         */
        public function update( $option_version = '' )
        {
            global  $wpsl_ext_options ;
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            
            if ( version_compare( $option_version, '0.1.0', '<' ) ) {
                require_once WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php';
                wpsl_ext_create_roles();
            }
            
            $this->check_options( $option_version );
            // Update the options.
            $wpsl_ext_options->set_value( 'wpsl_ext_version', WPSL_EXT_VERSION_NUM );
            $wpsl_ext_options->update_ext_options();
        }
        
        /**
         * Check updates for the plugin.
         *
         * @since 1.0.0
         * @return void
         */
        public function check_options( $option_version = '' )
        {
            global  $wpsl_ext_options ;
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            $option_version = $wpsl_ext_options->get_value( 'wpsl_ext_version' );
            // Add default values for new options
            if ( version_compare( $option_version, '1.1.0', '<' ) ) {
            }
        }
        
        /**
         * Copy non-empty, readable files to destination if they are newer than the destination file.
         * OR if the destination file does not exist.
         *
         * @param $source_file
         * @param $destination_file
         */
        public function copy_newer_files( $source_file, $destination_file )
        {
            if ( empty($source_file) ) {
                return;
            }
            if ( !is_readable( $source_file ) ) {
                return;
            }
            if ( !file_exists( $destination_file ) || file_exists( $destination_file ) && filemtime( $source_file ) > filemtime( $destination_file ) ) {
                copy( $source_file, $destination_file );
            }
        }
        
        /**
         * Recursively copy source directory (or file) into destination directory.
         *
         * @param string $source can be a file or a directory
         * @param string $dest   can be a file or a directory
         */
        private function copyr( $source, $dest )
        {
            if ( !file_exists( $source ) ) {
                return;
            }
            // Make destination directory if necessary
            //
            if ( !is_dir( $dest ) ) {
                wp_mkdir_p( $dest );
            }
            // Loop through the folder
            $dir = dir( $source );
            
            if ( is_a( $dir, 'Directory' ) ) {
                while ( false !== ($entry = $dir->read()) ) {
                    // Skip pointers
                    if ( $entry == '.' || $entry == '..' ) {
                        continue;
                    }
                    $source_file = "{$source}/{$entry}";
                    $dest_file = "{$dest}/{$entry}";
                    // Copy Files
                    //
                    if ( is_file( $source_file ) ) {
                        $this->copy_newer_files( $source_file, $dest_file );
                    }
                    // Copy Symlinks
                    //
                    if ( is_link( $source_file ) ) {
                        symlink( readlink( $source_file ), $dest_file );
                    }
                    // Directories, go deeper
                    //
                    if ( is_dir( $source_file ) ) {
                        $this->copyr( $source_file, $dest_file );
                    }
                }
                // Clean up
                $dir->close();
            }
        
        }
        
        /**
         * Update the data structures on new db versions.
         *
         * @global object $wpdb
         * @param string $sql
         * @param string $table_name
         * @return string
         */
        private function dbupdater( $sql, $table_name )
        {
            global  $wpdb ;
            $retval = ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ? 'new' : 'updated' );
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
            global  $EZSQL_ERROR ;
            $EZSQL_ERROR = array();
            return $retval;
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
    // $GLOBALS['wpsl_ext_activate'] = new WPSL_EXT_Activate();
}
