<?php

/**
 * WPSL Extenders Admin class
 * 
 * @since  1.0.0
 * @author DeBAAT
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WPSL_EXT_Admin' ) ) {
    class WPSL_EXT_Admin
    {
        /**
         * A cache for the taxonomy objects.
         *
         * @var 
         */
        public  $taxonomyCache = array() ;
        /**
         * Class constructor
         */
        function __construct()
        {
            $this->includes();
            $this->wpsl_admin_init();
            $this->add_hooks_and_filters();
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function includes()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            require_once WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php';
            // Create objects for basic Admin functionality
            WPSL_EXT_create_object( 'WPSL_EXT_Activate', 'include/admin/' );
            WPSL_EXT_create_object( 'WPSL_EXT_Notices', 'include/admin/' );
            WPSL_EXT_create_object( 'WPSL_EXT_Render_Settings', 'include/admin/' );
        }
        
        /**
         * Include the required files.
         *
         * @since 1.0.0
         * @return void
         */
        public function wpsl_ext_includes()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Create objects for functionality
            WPSL_EXT_create_object( 'WPSL_EXT_UserManaged', 'include/usermanaged/' );
        }
        
        /**
         * Add cross-element hooks & filters.
         *
         * Haven't yet moved all items to the AJAX and UI classes.
         */
        function add_hooks_and_filters()
        {
            // $this->debugMP('msg', __FUNCTION__ . ' started.');
            add_action( 'admin_init', array( $this, 'wpsl_ext_includes' ) );
            add_action( 'admin_init', array( $this, 'wpsl_ext_check_upgrade' ) );
            add_action( 'admin_menu', array( $this, 'wpsl_admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'wpsl_admin_scripts' ) );
            add_filter( 'wpsl_post_type_args', array( $this, 'filter_wpsl_post_type_args' ) );
            add_filter( 'parse_query', array( $this, 'filter_parse_query' ) );
            add_action(
                'wp_insert_post',
                array( $this, 'action_wp_insert_post' ),
                90,
                3
            );
        }
        
        /**
         * Init the required classes.
         *
         * @since 1.0.0
         * @return void
         */
        public function wpsl_admin_init()
        {
            // global $wpsl_extenders;
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            $this->debugMP( 'pr', __FUNCTION__ . ' started with _GET:', $_GET );
            $this->debugMP( 'pr', __FUNCTION__ . ' started with _POST:', $_POST );
            $this->debugMP( 'pr', __FUNCTION__ . ' started with _REQUEST:', $_REQUEST );
            $this->handle_wpsl_ext_action();
        }
        
        /**
         * Process the save_post action after it has been processed by WPSL
         *
         * @since 1.0.0
         * @return void
         */
        function action_wp_insert_post( $post_id, $post, $update )
        {
            global  $wpsl_ext_options ;
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // Only process posts of type WPSL_POST_TYPE
            if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == WPSL_POST_TYPE ) {
                // Apply ext_uml_publish_location setting
                
                if ( !$wpsl_ext_options->is_true( 'ext_uml_publish_location' ) ) {
                    // $this->debugMP('msg',__FUNCTION__.' removing wpsl_latlng post_meta data.');
                    update_post_meta( $post_id, 'wpsl_lat', '' );
                    update_post_meta( $post_id, 'wpsl_lng', '' );
                }
            
            }
        }
        
        /**
         * If the db doesn't hold the current version, run the upgrade procedure
         *
         * @since 1.0.0
         * @return void
         */
        function wpsl_ext_check_upgrade()
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            global  $wpsl_ext_activate ;
            global  $wpsl_ext_options ;
            $option_version = $wpsl_ext_options->get_value( 'wpsl_ext_version' );
            $update_to_new_version = version_compare( $option_version, WPSL_EXT_VERSION_NUM, '<' );
            $this->debugMP( 'msg', __FUNCTION__ . ' started for option_version = ' . $option_version . '!' );
            $this->debugMP( 'msg', __FUNCTION__ . ' started for WPSL_EXT_VERSION_NUM = ' . WPSL_EXT_VERSION_NUM . '!' );
            $this->debugMP( 'msg', __FUNCTION__ . ' started for update_to_new_version = ' . $update_to_new_version . '!' );
            
            if ( $update_to_new_version ) {
                $this->debugMP( 'msg', __FUNCTION__ . ' activated!!!' );
                // Create and run the WPSL_EXT_Activate class
                // require_once( WPSL_EXT_PLUGIN_DIR . 'include/admin/class-WPSL_EXT_Activate.php' );
                WPSL_EXT_create_object( 'WPSL_EXT_Activate', 'include/admin/' );
                $wpsl_ext_activate->update( $option_version );
            }
        
        }
        
        /**
         * Init the required classes.
         *
         * @since 1.0.0
         * @return void
         */
        function filter_wpsl_post_type_args( $args )
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            global  $wpsl_settings ;
            // $args['rewrite'] = array( 'slug' => $wpsl_settings['permalink_slug'], 'with_front' => false );
            // $this->debugMP('pr',__FUNCTION__.' Returns args:', $args);
            return $args;
        }
        
        /**
         * Init the required classes.
         *
         * @since 1.0.0
         * @return void
         */
        function filter_parse_query( $query )
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            global  $pagenow ;
            if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == WPSL_POST_TYPE && $pagenow == 'edit.php' ) {
                
                if ( !wpsl_ext_is_admin() ) {
                    $cur_user_id = get_current_user_id();
                    $query->query_vars['author'] = $cur_user_id;
                }
            
            }
            // $this->debugMP('pr',__FUNCTION__.' Returns args:', $query);
            return $query;
        }
        
        /**
         * Get the action defined by WPSL_EXT_ACTION_REQUEST or action
         *
         * @since 1.0.0
         * @return void
         */
        public function get_wpsl_ext_action()
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            if ( isset( $_REQUEST[WPSL_EXT_ACTION_REQUEST] ) ) {
                return sanitize_key( $_REQUEST[WPSL_EXT_ACTION_REQUEST] );
            }
            if ( isset( $_REQUEST['action'] ) ) {
                return sanitize_key( $_REQUEST['action'] );
            }
            if ( isset( $_REQUEST['action2'] ) ) {
                return sanitize_key( $_REQUEST['action'] );
            }
            return false;
        }
        
        /**
         * Handle the actions defined by WPSL_EXT_ACTION_REQUEST
         *
         * @since 1.0.0
         * @return void
         */
        public function handle_wpsl_ext_action()
        {
            // check whether action is set
            $cur_action = $this->get_wpsl_ext_action();
            // $this->debugMP('msg',__FUNCTION__.' cur_action: ' . $cur_action);
            
            if ( $cur_action ) {
                $user_ids = $this->get_ids_from_array( $_REQUEST, WPSL_EXT_STORE_USER_SLUG );
                switch ( $cur_action ) {
                    case WPSL_EXT_ACTION_USER_ALLOW:
                        // $this->debugMP('pr',__FUNCTION__.' Users allowed as store editor:', $user_ids);
                        foreach ( $user_ids as $user_id ) {
                            wpsl_ext_user_allow( $user_id );
                        }
                        break;
                    case WPSL_EXT_ACTION_USER_DISALLOW:
                        // $this->debugMP('pr',__FUNCTION__.' Users disallowed as store editor:', $user_ids);
                        foreach ( $user_ids as $user_id ) {
                            wpsl_ext_user_disallow( $user_id );
                        }
                        break;
                    default:
                        break;
                }
            }
        
        }
        
        /**
         * Create the WPSL_EXT_Settings class.
         *
         * @since 1.0.0
         * @return void
         */
        public function get_ids_from_array( $input_array = null, $input_key = '' )
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            $output_array = array();
            // Check input parameters
            if ( $input_array == null ) {
                return $output_array;
            }
            if ( $input_key == '' ) {
                return $output_array;
            }
            if ( !isset( $input_array[$input_key] ) ) {
                return $output_array;
            }
            // Get intvals for IDs
            
            if ( is_array( $input_array[$input_key] ) ) {
                foreach ( $input_array[$input_key] as $input_value ) {
                    $output_array[] = intval( $input_value );
                }
            } else {
                $output_array[] = intval( $input_array[$input_key] );
            }
            
            return $output_array;
        }
        
        /**
         * Add the 'WPSL Extenders' sub menu to the 
         * existing WP Store Locator menu.
         * 
         * @since  1.0.0
         * @return void
         */
        public function wpsl_admin_menu()
        {
            // $this->debugMP('msg',__FUNCTION__.' started.');
            add_submenu_page(
                'edit.php?post_type=' . WPSL_POST_TYPE,
                __( 'Extenders', 'wp-store-locator-extenders' ),
                __( 'Extenders', 'wp-store-locator-extenders' ),
                WPSL_EXT_CAP_MANAGE_WPSL_ADMIN,
                WPSL_EXT_ADMIN_MENU_SLUG,
                array( $this, 'wpsl_ext_render_admin' )
            );
        }
        
        /**
         * Render the admin page with sections.
         * 
         * @since  1.0.0
         * @return void
         */
        public function wpsl_ext_render_admin()
        {
            global  $wpsl_ext_settings ;
            global  $wpsl_ext_notices ;
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            // $this->debugMP('pr', __FUNCTION__.' started with _GET:',     $_GET );
            // $this->debugMP('pr', __FUNCTION__.' started with _POST:',    $_POST );
            // $this->debugMP('pr', __FUNCTION__.' started with _REQUEST:', $_REQUEST );
            $cur_section = WPSL_EXT_SECTION_SETTINGS;
            $render_output = '';
            $nav_template = '<a class="nav-tab %s" href="%s">%s</a>';
            // Default section items
            //
            $section_items = array();
            $section_items[WPSL_EXT_SECTION_SETTINGS] = __( 'Extenders Settings', 'wp-store-locator-extenders' );
            // Render top section header
            //
            $render_output .= '<div class="wrap wpsl-ext-settings">';
            $render_output .= '<h2>' . __( 'WPSL Extenders', 'wp-store-locator-extenders' ) . '</h2>';
            $render_output .= '<h2 class="nav-tab-wrapper" id="wpsl-tabs">';
            // Render top section navigations
            //
            foreach ( $section_items as $section_slug => $section_label ) {
                $section_active = ( $cur_section == $section_slug ? 'nav-tab-active' : '' );
                $section_href = admin_url( 'edit.php?post_type=' . WPSL_POST_TYPE . '&page=' . WPSL_EXT_ADMIN_MENU_SLUG . '&' . WPSL_EXT_SECTION_PARAM . '=' . $section_slug );
                $render_output .= sprintf(
                    $nav_template,
                    $section_active,
                    $section_href,
                    $section_label
                );
            }
            $render_output .= '</h2>';
            switch ( $cur_section ) {
                case WPSL_EXT_SECTION_SETTINGS:
                default:
                    WPSL_EXT_create_object( 'WPSL_EXT_Settings', 'include/admin/' );
                    $render_output .= $wpsl_ext_settings->wpsl_ext_render_section();
                    break;
            }
            // Close top section header and generate output
            //
            $render_output .= '</div>';
            $rendered_output = $wpsl_ext_notices->render_notices() . $render_output;
            echo  $rendered_output ;
        }
        
        /**
         * Add the required admin scripts.
         *
         * @since  1.0.0
         * @return void
         */
        public function wpsl_admin_scripts()
        {
            $this->debugMP( 'msg', __FUNCTION__ . ' started.' );
            $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
            $min = '';
            wp_enqueue_style( 'wpsl-ext-admin', WPSL_EXT_PLUGIN_URL . '/css/wpsl-ext-admin' . $min . '.css', false );
            wp_enqueue_style( 'datetimepicker', WPSL_EXT_PLUGIN_URL . '/css/jquery.datetimepicker' . $min . '.css', false );
            // Include the jQuery DatePicker
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            // wp_enqueue_script( 'datetimepicker_js',    WPSL_EXT_PLUGIN_URL . '/js/jquery.js');
            wp_enqueue_script( 'wpsl_datetimepicker', WPSL_EXT_PLUGIN_URL . '/js/jquery.datetimepicker.full.js' );
            wp_enqueue_script( 'wpsl_ext_script', WPSL_EXT_PLUGIN_URL . '/js/wpsl-ext-admin.js' );
            // wp_enqueue_script( 'datetimepicker',  WPSL_EXT_PLUGIN_URL . '/js/jquery.datetimepicker.js', array ( 'jquery' ), 1.1, true);
        }
        
        /**
         * Get all Taxonomy Objects from the database and put them in an indexed cache
         *
         * @param boolean $taxonomy
         * @return array taxonomyObjects
         */
        public function get_taxonomy_names( $allTaxonomies, $taxonomyIds = '', $nameGlue = ',' )
        {
            $searchIds = explode( ',', $taxonomyIds );
            $resultNames = array();
            foreach ( $searchIds as $taxID ) {
                if ( isset( $allTaxonomies[$taxID] ) ) {
                    $resultNames[] = $allTaxonomies[$taxID]->name;
                }
            }
            $taxonomyNames = implode( $nameGlue, $resultNames );
            // $this->debugMP('msg',__FUNCTION__ . ' translated (' . $taxonomyIds . ') into ' . $taxonomyNames);
            return $taxonomyNames;
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
    // $GLOBALS['wpsl_ext_admin'] = new WPSL_EXT_Admin();
}
