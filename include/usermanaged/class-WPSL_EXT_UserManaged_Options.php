<?php
/**
 * Handle the WPSL Extenders plugin User Managed settings
 *
 * @author DeBAAT
 * @since  1.2.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_EXT_UserManaged_Options' ) ) {
	
	class WPSL_EXT_UserManaged_Options {

		/**
		 * Parameters for handling the settable options for this plugin.
		 *
		 * @var mixed[] $options
		 */
		public  $ext_settings_params = array();

		public function __construct() {

			// Get some settings
			$this->initialize();

		}

		public function initialize() {

			// Get some settings

		}

		/**
		 * Define the settings for options
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function set_ext_settings_params( $ext_settings_params_input = false) {
			$this->debugMP('msg',__FUNCTION__.' started.');

			// Get some settings
			$this->ext_settings_params = $ext_settings_params_input;

			$this->set_ext_settings_params_general();
			// $this->debugMP('pr',__FUNCTION__.' to return with ext_settings_params:', $this->ext_settings_params );

			return $this->ext_settings_params;

		}

		/**
		 * Render the settings for user managed locations
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function set_ext_settings_params_general() {

			global $wpsl_ext_options;

			$ext_settings_params_section = WPSL_EXT_SECTION_UML;
			$this->debugMP('msg',__FUNCTION__.' started for section ' . $ext_settings_params_section );

			// Set wpsl_ext_options parameters
			//
			$ext_settings_params_name = 'ext_uml_publish_location';
			$this->ext_settings_params[$ext_settings_params_name] = array(
					'label'        => __('Publish location immediately?', 'wp-store-locator-extenders'),
					'name'         => $ext_settings_params_name,
					'slug'         => wpsl_ext_make_slug($ext_settings_params_name, WPSL_EXT_SECTION_PREFIX),
					'type'         => WPSL_EXT_SETTINGS_TYPE_CHECKBOX,
					'section'      => $ext_settings_params_section,
					'value'        => $wpsl_ext_options->get_value( $ext_settings_params_name ),
					'description'  => __( 'When checked a newly entered location is published immediately.', 'wp-store-locator-extenders') . ' ' .
									  __( 'When not checked, the geocode of a newly entered location is removed to block publishing.', 'wp-store-locator-extenders') . ' ' .
									  __( 'This needs the re-geocoding functionality to publish blocked locations.', 'wp-store-locator-extenders'),
				);

			$ext_settings_params_name = 'ext_uml_default_user_allowed';
			$this->ext_settings_params[$ext_settings_params_name] = array(
					'label'        => __('Allow new user by default?', 'wp-store-locator-extenders'),
					'name'         => $ext_settings_params_name,
					'slug'         => wpsl_ext_make_slug($ext_settings_params_name, WPSL_EXT_SECTION_PREFIX),
					'type'         => WPSL_EXT_SETTINGS_TYPE_CHECKBOX,
					'section'      => $ext_settings_params_section,
					'value'        => $wpsl_ext_options->get_value( $ext_settings_params_name ),
					'description'  => __( 'When checked a newly added user is allowed to manage locations immediately.', 'wp-store-locator-extenders'),
				);

			$ext_settings_params_name = 'ext_uml_show_uml_buttons';
			$this->ext_settings_params[$ext_settings_params_name] = array(
					'label'        => __('Show edit buttons?', 'wp-store-locator-extenders'),
					'name'         => $ext_settings_params_name,
					'slug'         => wpsl_ext_make_slug($ext_settings_params_name, WPSL_EXT_SECTION_PREFIX),
					'type'         => WPSL_EXT_SETTINGS_TYPE_CHECKBOX,
					'section'      => $ext_settings_params_section,
					'value'        => $wpsl_ext_options->get_value( $ext_settings_params_name ),
					'description'  => __( 'When checked an edit button is added to assist managing a users locations.', 'wp-store-locator-extenders'),
				);

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

}
