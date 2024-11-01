<?php
/**
 * WPSL Extenders UserManaged class
 * 
 * @since  1.0.0
 * @author DeBAAT
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_EXT_UserManaged' ) ) {

	class WPSL_EXT_UserManaged {

		/**
		 * Class constructor
		 */
		function __construct() {

			$this->includes();
			$this->init();
		}

		/**
		 * Include the required files.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function includes() {
			require_once( WPSL_EXT_PLUGIN_DIR . 'include/wpsl-extenders-roles.php' );
		}

		/**
		 * Init the required classes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init() {
			$this->debugMP('msg',__FUNCTION__.' started.');

			add_filter('wp_print_styles',                        array($this, 'wpsl_ext_wp_print_styles'              )           );

			// Some WordPress Admin Actions and Filters for non-admin users
			add_action('show_user_profile',                      array($this,'action_show_user_profile'               )           );
			add_action('edit_user_profile',                      array($this,'action_show_user_profile'               )           );

			// WordPress Admin Actions and Filters
			add_action('user_register',                          array($this,'action_user_register'                   )           );

			add_filter('user_row_actions',                       array($this,'filter_user_row_actions'                ), 10,  2   );
			add_filter('bulk_actions-users',                     array($this,'filter_bulk_actions_users'              )           );

			add_filter('manage_users_columns',                   array($this,'filter_manage_users_columns'            )           );
			add_action('manage_users_custom_column',             array($this,'filter_manage_users_custom_column'      ), 10,  3   );

			add_filter('wpsl_store_meta',                        array($this,'filter_uml_wpsl_store_meta'             ), 10,  2   );
			add_filter('wpsl_store_header_template',             array($this,'filter_uml_wpsl_store_header_template'  )           );
			add_filter('wpsl_infobox_settings',                  array($this,'filter_uml_wpsl_infobox_settings'       )           );
			add_filter('wpsl_js_settings',                       array($this,'filter_uml_wpsl_js_settings'            )           );

			$this->debugMP('msg',__FUNCTION__.' add_filter for several functions.');
		}

		/**
		 * Initialize all our jquery goodness.
		 *
		 */
		public function wpsl_ext_wp_print_styles() {

			// $this->debugMP('msg',__FUNCTION__.' started.');
			// wp_enqueue_style( 'wpsl-ext-style', WPSL_EXT_PLUGIN_URL . '/css/wpsl-uml-styles.css' );
		}

		/**
		 * Add UML buttons data to the wpsl_infobox_settings when ext_uml_show_uml_buttons checked
		 *
		 */
		function filter_uml_wpsl_js_settings( $wpsl_js_settings ) {

			global $wpsl_ext_options;

			// $this->debugMP('pr',__FUNCTION__.' wpsl_js_settings =', $wpsl_js_settings);

			// Apply ext_uml_show_uml_buttons setting
			if ( $wpsl_ext_options->is_true('ext_uml_show_uml_buttons') ) {
				// $wpsl_js_settings['wpsl_ext_user_location_allowed'] = "J1";
			} else {
				// $wpsl_js_settings['wpsl_ext_user_location_allowed'] = "J0";
			}

			$this->debugMP('pr',__FUNCTION__.' wpsl_js_settings set to:', $wpsl_js_settings);
			return $wpsl_js_settings;
		}

		/**
		 * Add UML buttons data to the wpsl_infobox_settings when ext_uml_show_uml_buttons checked
		 *
		 */
		function filter_uml_wpsl_infobox_settings( $wpsl_infobox_settings ) {

			global $wpsl_ext_options;

			// $this->debugMP('pr',__FUNCTION__.' wpsl_infobox_settings =', $wpsl_infobox_settings);

			// Apply ext_uml_show_uml_buttons setting
			if ( $wpsl_ext_options->is_true('ext_uml_show_uml_buttons') ) {
				$wpsl_infobox_settings['wpsl_ext_user_location_allowed'] = "I1";
			} else {
				$wpsl_infobox_settings['wpsl_ext_user_location_allowed'] = "I0";
			}

			$this->debugMP('pr',__FUNCTION__.' wpsl_infobox_settings set to:', $wpsl_infobox_settings);
			return $wpsl_infobox_settings;
		}

		/**
		 * Add UML buttons data when ext_uml_show_uml_buttons checked
		 *
		 */
		function filter_uml_wpsl_store_meta( $store_meta, $store_ID ) {
			// $this->debugMP('pr',__FUNCTION__.' store_meta['. $store_ID .']=', $store_meta);

			// $store_meta['wpsl_ext_store_meta_store_id']   = $store_ID;
			if ( $this->wpsl_ext_is_user_location( $store_ID ) ) {
				// $store_meta['wpsl_ext_store_meta_user_id']    = get_post_field( 'post_author', $store_ID );
				$store_meta['wpsl_ext_user_location_allowed'] = "M1";
			} else {
				// $store_meta['wpsl_ext_store_meta_user_id']    = 'NoUSER' . $store_ID;
				$store_meta['wpsl_ext_user_location_allowed'] = "M0";
			}

			$this->debugMP('pr',__FUNCTION__.' found store_meta['. $store_ID .']=', $store_meta);
			return $store_meta;
		}

		/**
		 * Add UML buttons when ext_uml_show_uml_buttons checked
		 *
		 */
		function filter_uml_wpsl_store_header_template( $header_template ) {

			global $wpsl_ext_options;

			$this->debugMP('msg',__FUNCTION__.' started.');

			$wpsl_ext_user_location_allowed_string = '<%= wpsl_ext_user_location_allowed %>';
			$header_template .= '<span>#' . $wpsl_ext_user_location_allowed_string . '#</span>';

			// Apply ext_uml_show_uml_buttons setting
			if ( $wpsl_ext_options->is_true('ext_uml_show_uml_buttons') ) {
				$location_id = '<%= id %>';
/*				$header_template .= 'JdB+<%= id %>by<%= wpsl_ext_store_meta_user_id %>+JdB';
*/
				// Only show the UML buttons if user is allowed for this location
				$header_template .= '<% if ( wpsl_ext_user_location_allowed ) { %>';
				$header_template .= ' ';
				$header_template .= $this->createstring_uml_buttons( $location_id );
				$header_template .= '<% } %>';

			} else {
				// $header_template .= 'NO_JdB';
			}

			return $header_template;
		}

		/**
		 * Add UML buttons when ext_uml_show_uml_buttons checked
		 *
		 */
		function wpsl_show_user_buttons( $location_id ) {

			global $wpsl_ext_options;

			$this->debugMP('msg',__FUNCTION__.' started for location_id: ' . $location_id );

			$user_buttons_output = '';
			$user_buttons_string = '';

			// Apply ext_uml_show_uml_buttons setting
			if ( $wpsl_ext_options->is_true('ext_uml_show_uml_buttons') ) {

				// Check whether this user is allowed to edit this store wpsl_ext_user_location_allowed
				if ( $this->wpsl_ext_is_user_location( $location_id ) ) {
					$user_buttons_string = $this->createstring_uml_buttons( $location_id );
				}

				// Only show the output when there are uml_buttons to show
				if ( $user_buttons_string != '' ) {
					$user_buttons_output .= '<div class="wpsl-user-wrap">';
					$user_buttons_output .= '<div style="float:left;vertical-align: middle;"><strong>';
					$user_buttons_output .= esc_html( __( 'Edit location:', 'wp-store-locator-extenders' ) );
					$user_buttons_output .= ' &nbsp;</strong></div>';
					$user_buttons_output .= '<div style="float:left;text-align: center;">';
					$user_buttons_output .= $user_buttons_string;
					$user_buttons_output .= '</div>';
					$user_buttons_output .= '</div>';
					$user_buttons_output .= '<div style="clear:both;">';
					$user_buttons_output .= '</div>';
				}
			} else {
				// $user_buttons_output .= 'NO_JdB for location_id ' . $location_id;
			}

			return $user_buttons_output;
		}

		/**
		 * Build the action buttons HTML string on the first column of the manage locations panel.
		 *
		 * Applies the wpsl_manage_locations_uml_buttons filter.
		 *
		 * @return string
		 */
		private function createstring_uml_buttons( $location_id = '' ) {
			$this->debugMP('msg',__FUNCTION__.' started.');

			$buttons_HTML  = '';
			$buttons_URL   = admin_url( 'post.php' );

			// Add edit_button
			$edit_URL   = add_query_arg( array( 'action' => 'edit', 'post' => $location_id ), $buttons_URL);

			$buttons_HTML .=
				sprintf(
					'<a class="dashicons dashicons-welcome-write-blog wpsl-no-box-shadow uml-buttons" alt="%s" title="%s" data-action="edit" href="%s#" target="_blank" data-id="%s"></a>',
					__( 'edit location', 'wp-store-locator-extenders' ),
					__( 'edit location', 'wp-store-locator-extenders' ),
					$edit_URL,
					$location_id
				);

			/**
			 * Filter to Build UML action buttons
			 *
			 * @filter      wpsl_manage_locations_uml_buttons
			 *
			 * @params      string  current HTML
			 * @params      string  current location_id
			 */

			return apply_filters( 'wpsl_manage_locations_uml_buttons', $buttons_HTML, (array) $location_id );

		}

		/**
		 * Show the Store User settings of the requested user.
		 *
		 * @param WP_User $profileuser The current WP_User object.
		 */
		public function action_show_user_profile( $profileuser ) {

			if (!@is_object($profileuser)) {
				$profileuser = wp_get_current_user();
			}
			$this->debugMP('msg', __FUNCTION__ . ' started for profileuser->user_login: ' . $profileuser->user_login . ', profileuser->ID: ' . $profileuser->ID );

			// Prepare some variables
			$user_allowed = wpsl_ext_is_user_allowed( $profileuser->ID );
			if ( $user_allowed ) {
				$user_locations  = $this->wpsl_count_filtered_locations( $profileuser->ID );
				$user_text       = __( 'User is allowed to manage locations.', 'wp-store-locator-extenders' );
				$user_text      .= ' ';
				$user_text      .= sprintf(_n( 'Currently managing one location.', 'Currently managing %d locations.', $user_locations, 'wp-store-locator-extenders' ), $user_locations );
				$this->debugMP('msg',__FUNCTION__ . ' continued with user_allowed: ' . $user_allowed);
			} else {
				$user_text       = __( 'User is not allowed to manage any location.', 'wp-store-locator-extenders' );
			}

			// Generate the output to show
			?>
			<h3><?php _e( 'WP Store Locator', 'wp-store-locator-extenders' ); ?></h3>
			<table class="form-table">
				<tr class="show-admin-bar user-admin-bar-front-wrap">
					<th scope="row"><?php _e( 'User Managed Locations', 'wp-store-locator-extenders' ); ?></th>
					<td>
						<?php if ( $user_allowed ) : ?>
							<span class="dashicons dashicons-yes" color="green"></span>
						<?php else: ?>
							<span class="dashicons dashicons-no" color="red"></span>
						<?php endif; ?>
						<?php echo $user_text; ?>
					</td>
				</tr>
			</table>
			<?php			

		}

		/**
		 * Count all locations related to the requested user, count all locations when wpsl_ext_is_admin 
		 *
		 * @params string $sqlStatement the existing SQL command for Select All
		 * @return integer
		 */
		function wpsl_count_filtered_locations( $uml_user_id ) {

			// Query the WPSL_POST_TYPE for this user
			$args = array();
			$args['post_type'] = WPSL_POST_TYPE;
			$args['author']    = $uml_user_id;
			$args['fields']    = 'ids';
			$this->debugMP('pr', __FUNCTION__ . ': args= ', $args);
			$wp_post_query    = new WP_Query( $args );
			$all_wpsl_posts   = $wp_post_query->get_posts();
			//$this->debugMP('pr',__FUNCTION__.' all_wpsl_posts=',$all_wpsl_posts);

			// Count the posts found
			$total_wpsl_posts = '<span class="dashicons dashicons-no"></span>';
			if ( $all_wpsl_posts ) {
				$total_wpsl_posts = count($all_wpsl_posts);
			}
			$this->debugMP('msg',__FUNCTION__.' found ' . $total_wpsl_posts . ' locations for user ' . $uml_user_id . ' .');

			return $total_wpsl_posts;
		}

		/**
		 * Check whether this user is allowed to manage this location.
		 *
		 * @param id       $location_id - the ID of the location to check
		 * @param string   $uml_user_id - the id of the user to check
		 * @return boolean
		 */
		public function wpsl_ext_is_user_location( $location_id = '', $uml_user_id = '' ) {
			$this->debugMP('msg',__FUNCTION__.' started for location_id:'.  $location_id);

			global $current_user;

			// Admin is always allowed
			if ( wpsl_ext_is_admin() ) { return true; }

			// If no user provided, use current user
			if ($uml_user_id == '') {
				$uml_user_id = $current_user->ID;
			}
			$this->debugMP('msg',__FUNCTION__.' started for uml_user_id:'.  $uml_user_id);

			// Get the location_post to check
			$location_post = get_post( $location_id );
			$this->debugMP('pr',__FUNCTION__.' location_post=', $location_post);
			if ( ! $location_post || $location_post->post_author != $uml_user_id ) {
				return false;
			}

			return true;
		}

		/**
		 * Applies the default allowed setting to newly registered users.
		 *
		 * @param int $user_id User ID.
		 */
		public function action_user_register( $user_id ) {

			global $wpsl_ext_options;

			$this->debugMP('msg',__FUNCTION__.' started for user_id: ' . $user_id);

			// Validate access and parameters
			if ( ! wpsl_ext_is_admin() ) { return false; }

			// Apply ext_uml_default_user_allowed setting
			if ( $wpsl_ext_options->is_true('ext_uml_default_user_allowed') ) {
				wpsl_ext_user_allow( $user_id );
			} else {
				wpsl_ext_user_disallow( $user_id );
			}

		}

		/**
		 * Add new bulk_actions to the Users list table
		 *
		 * @param WP_User $columns The current WP_User object.
		 */
		function filter_bulk_actions_users( $actions ) {
			$this->debugMP('msg',__FUNCTION__ . ' started.');

			$actions[WPSL_EXT_ACTION_USER_ALLOW]    =  __( 'Allow',    'wp-store-locator-extenders' );
			$actions[WPSL_EXT_ACTION_USER_DISALLOW] =  __( 'Disallow', 'wp-store-locator-extenders' );
			return $actions;
		}

		/**
		 * Add a new column to the Users list table
		 *
		 * @param WP_User $columns The current WP_User object.
		 */
		function filter_manage_users_columns( $columns ) {
			$this->debugMP('msg',__FUNCTION__ . ' started.');

			$columns[WPSL_EXT_STORE_USER_COL_ALLOWED]   =  __( 'Manage Stores', 'wp-store-locator-extenders' );
			$columns[WPSL_EXT_STORE_USER_COL_LOCATIONS] =  __( 'Locations',     'wp-store-locator-extenders' );
			return $columns;
		}

		/**
		 * Add the value for the new column to the Users list table
		 *
		 * @param string $output Custom column output. Default empty.
		 * @param string $column_name Column name.
		 * @param int $user_id ID of the currently-listed user.
		 */
		function filter_user_row_actions( $actions, $user_object ) {
			$this->debugMP('pr', __FUNCTION__ . ' started for actions :', $actions );

			// Create query_args and urls
			$query_args = array();
			$query_args[ 'wp_http_referer' ]        = urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			$query_args[ WPSL_EXT_STORE_USER_SLUG ] = $user_object->ID;
			$query_args[ WPSL_EXT_ACTION_REQUEST ]  = WPSL_EXT_ACTION_USER_ALLOW;
			$action_link_allow                      = wp_nonce_url( add_query_arg( $query_args, admin_url( 'users.php' ) ) );
			$query_args[ WPSL_EXT_ACTION_REQUEST ]  = WPSL_EXT_ACTION_USER_DISALLOW;
			$action_link_disallow                   = wp_nonce_url( add_query_arg( $query_args, admin_url( 'users.php' ) ) );

			$action_template = '<a href="%s">%s</a>';

			$actions[ WPSL_EXT_ACTION_USER_ALLOW ]    = sprintf( $action_template, $action_link_allow,    __( 'Allow',    'wp-store-locator-extenders' ));
			$actions[ WPSL_EXT_ACTION_USER_DISALLOW ] = sprintf( $action_template, $action_link_disallow, __( 'Disallow', 'wp-store-locator-extenders' ));

			return $actions;
		}

		/**
		 * Add the value for the new column to the Users list table
		 *
		 * @param string $output Custom column output. Default empty.
		 * @param string $column_name Column name.
		 * @param int $user_id ID of the currently-listed user.
		 */
		function filter_manage_users_custom_column( $value, $column_name, $user_id ) {
			$this->debugMP('msg', __FUNCTION__ . ' started for column_name :' . $column_name . ' .');

			switch ( $column_name ) {

				case WPSL_EXT_STORE_USER_COL_ALLOWED:
					$user_allowed  = __( 'Disallowed','wp-store-locator-extenders' );
					$cur_user = get_user_by( 'id', $user_id );
					if ( ( $cur_user ) && ( wpsl_ext_is_user_allowed( $user_id ) ) ) {
						// User is allowed
						$user_allowed = __( 'Allowed','wp-store-locator-extenders' );
						$user_allowed = '<span style="color: green;">' . $user_allowed . '</span>';
					} else {
						// User is disallowed
						$user_allowed = __( 'Disallowed','wp-store-locator-extenders' );
						$user_allowed = '<span style="color: red;">' . $user_allowed . '</span>';
					}
					return $user_allowed;
					break;

				case WPSL_EXT_STORE_USER_COL_LOCATIONS:

					// Get a value from a valid user object from the input
					$user_locations = $this->wpsl_count_filtered_locations( $user_id );
					return $user_locations;
					break;
				default:
					return $value;
					break;
			}
			return $value;
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
		function debugMP($type,$hdr,$msg='') {
			if (($type === 'msg') && ($msg!=='')) {
				$msg = esc_html($msg);
			}
			if (($hdr!=='')) {   // Adding __CLASS__ to non-empty hdr
				$hdr = __CLASS__ . '::' . $hdr;
			}

			WPSL_EXT_debugMP($type,$hdr,$msg,NULL,NULL,true);
		}

	}

	// $GLOBALS['wpsl_ext_usermanaged'] = new WPSL_EXT_UserManaged();

}
