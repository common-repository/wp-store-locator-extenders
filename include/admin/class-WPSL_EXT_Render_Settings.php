<?php
/**
 * Handle the WPSL Extenders plugin settings.
 *
 * @author DeBAAT
 * @since  1.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_EXT_Render_Settings' ) ) {
	
	class WPSL_EXT_Render_Settings {

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
		 * Get the values for parameters set via _POST
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function get_post_values( $wpsl_ext_params = array(), $wpsl_ext_section = WPSL_EXT_SECTION_ALL ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');

			$wpsl_post_values = array();

			// Check if the _POST values set
			//
			foreach ($wpsl_ext_params as $section_name => $settings_params ) {
				if ( ($settings_params['section'] == $wpsl_ext_section ) || (WPSL_EXT_SECTION_ALL == $wpsl_ext_section )) {
					$settings_name = $settings_params['name'];
					switch ( $settings_params['type'] ) {
						case WPSL_EXT_SETTINGS_TYPE_CHECKBOX:
							$wpsl_post_values[$settings_name] = isset( $_POST[WPSL_EXT_SETTINGS_TYPE_CHECKBOX][$settings_name] ) ? 1 : 0; 
							break;
						case WPSL_EXT_SETTINGS_TYPE_DROPDOWN:
							$wpsl_post_values[$settings_name] = isset( $_POST[WPSL_EXT_SETTINGS_TYPE_DROPDOWN][$settings_name] ) ? sanitize_key($_POST[WPSL_EXT_SETTINGS_TYPE_DROPDOWN][$settings_name]) : ''; 
							break;
						case WPSL_EXT_SETTINGS_TYPE_CUSTOM:
							$wpsl_post_values[$settings_name] = isset( $_POST[WPSL_EXT_SETTINGS_TYPE_CUSTOM][$settings_name] ) ? sanitize_text_field($_POST[WPSL_EXT_SETTINGS_TYPE_CUSTOM][$settings_name]) : ''; 
							break;
						case WPSL_EXT_SETTINGS_TYPE_DATETIME:
							$wpsl_post_values[$settings_name] = isset( $_POST[WPSL_EXT_SETTINGS_TYPE_DATETIME][$settings_name] ) ? $this->sanitize_type_datetime($_POST[WPSL_EXT_SETTINGS_TYPE_DATETIME][$settings_name]) : ''; 
							break;
						case WPSL_EXT_SETTINGS_TYPE_ICONLIST:
							$wpsl_post_values[$settings_name] = isset( $_POST[WPSL_EXT_SETTINGS_TYPE_ICONLIST][$settings_name] ) ? esc_url($_POST[WPSL_EXT_SETTINGS_TYPE_ICONLIST][$settings_name]) : ''; 
							break;
						case WPSL_EXT_SETTINGS_TYPE_TEXT:
						case WPSL_EXT_SETTINGS_TYPE_TEXTAREA:
							$wpsl_post_values[$settings_name] = isset( $_POST[WPSL_EXT_SETTINGS_TYPE_TEXT][$settings_name] ) ? sanitize_text_field($_POST[WPSL_EXT_SETTINGS_TYPE_TEXT][$settings_name]) : ''; 
							break;
						default:
							break;
					}
				}
			}

			// Update the changed options.
			$this->debugMP('pr',__FUNCTION__.' found wpsl_post_values:', $wpsl_post_values );
			return $wpsl_post_values;

		}

		/**
		 * Render the settings for sections
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_sections( $wpsl_ext_section = WPSL_EXT_SECTION_EXT, $wpsl_ext_params = array(), $wpsl_ext_section_label = '', $wpsl_ext_button_label = '' ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			$button_template  = '<input type="submit" value="%s" class="button-primary"/>';

			// Render top section header
			//
			$render_output .= '<div class="postbox-container">';
			$render_output .= '<div class="metabox-holder">';
			$render_output .= '<div id="wpsl-ext-settings" class="postbox">';

			// Render the section header only when wpsl_ext_section_label given
			//
			if ( $wpsl_ext_section_label != '' ) {
				$render_output .= '<h3 class="hndle"><span>';
				$render_output .= $wpsl_ext_section_label;
				$render_output .= '</span></h3>';
			}
			$render_output .= '<div class="inside">';

			// Render settings for section $wpsl_ext_section
			//
			foreach ($wpsl_ext_params as $section_name => $settings_params ) {
				if ( $settings_params['section'] == $wpsl_ext_section ) {
					switch ( $settings_params['type'] ) {
						case WPSL_EXT_SETTINGS_TYPE_CHECKBOX:
							$render_output .= $this->render_settings_checkbox( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_DROPDOWN:
							$render_output .= $this->render_settings_dropdown( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_SUBHEADER:
							$render_output .= $this->render_settings_subheader( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_HIDDEN:
							$render_output .= $this->render_settings_hidden( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_READONLY:
							$render_output .= $this->render_settings_readonly( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_BUTTON:
							$render_output .= $this->render_settings_button( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_CUSTOM:
							$render_output .= $this->render_settings_custom( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_DATETIME:
							$render_output .= $this->render_settings_datetime( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_ICONLIST:
							$render_output .= $this->render_settings_iconlist( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_TEXTAREA:
							$render_output .= $this->render_settings_textarea( $settings_params );
							break;
						case WPSL_EXT_SETTINGS_TYPE_TEXT:
						default:
							$render_output .= $this->render_settings_text( $settings_params );
							break;
					}

				}
			}

			// Render the button only when wpsl_ext_button_label given
			//
			if ( $wpsl_ext_button_label != '' ) {
				$render_output .= '<p class="submit">';
				$render_output .= sprintf( $button_template, $wpsl_ext_button_label );
				$render_output .= '</p>';        // for class="submit"
			}

			// Close top section header and return output
			//
			$render_output .= '</div>';      // for class="inside"
			$render_output .= '</div>';      // for id="wpsl-search-settings"
			$render_output .= '</div>';      // for class="metabox-holder"
			$render_output .= '</div>';      // for class="postbox-container"
			return $render_output;

		}

		/**
		 * Render the settings for a label
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_label( $text_input = false, $extra_styles = '' ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}

			// Define templates
			$text_label_template       = '<label for="%s" style="%s" >%s';
			$text_description_template = '<span class="wpsl-info"><span class="wpsl-info-text wpsl-hide">%s</span></span>';

			$render_output .= sprintf( $text_label_template,       $text_input['slug'], $extra_styles, $text_input['label'] );
			if ( isset( $text_input['description'] ) && $text_input['description'] != '' ) {
				$render_output .= sprintf( $text_description_template, $text_input['description'] );
			}
			$render_output .= '</label>';

			// Return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a text box
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_subheader( $text_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			// Define templates
			$text_subheader_template = '<h3 class="hndle"><span><em>%s</em></span></h3>';
			$text_description_template = '<span>%s</span>';

			// Render text input
			//
			$render_output .= '<p>';
			if ( isset($text_input['label']) && $text_input['label'] != '' ) {
				$render_output .= sprintf( $text_subheader_template, $text_input['label'] );
			}
			if ( isset($text_input['description']) && $text_input['description'] != '' ) {
				$render_output .= sprintf( $text_description_template, $text_input['description'] );
			}
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a hidden item
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_hidden( $text_input = false, $extra_styles = '' ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}

			// Define templates
			$input_hidden_template = '<input type="hidden" name="%s" value="%s"/>';

			$render_output .= sprintf( $input_hidden_template, $text_input['name'], $extra_styles, $text_input['value'] );

			// Return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a text box
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_readonly( $text_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			// Define templates
			$text_input_template = '<span>%s</span>';
			$text_input_template = '<input type="text" readonly="readonly" value="%s" name="' . WPSL_EXT_SETTINGS_TYPE_TEXT . '[%s]" class="textinput" id="%s"/>';
			if ( isset($text_input['textarea']) ) {
				$text_input_template = '<textarea readonly="readonly" cols="30" rows="3" style="width:275px;">%s</textarea>';
			}

			// Render text input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $text_input, 'vertical-align:top' );
			if ( isset($text_input['textarea']) ) {
				$render_output .= sprintf( $text_input_template, $text_input['value'] );
			} else {
				$render_output .= sprintf( $text_input_template, $text_input['value'], $text_input['name'], $text_input['slug'] );
			}
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a datetime type
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_datetime( $text_input = false ) {

			global $wpsl_extenders;

			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			// Define templates
			// $datetime_template = '<input type="text" value="%s" name="%s" class="textinput" id="%s">';
			// $datetime_template = '<input type="text" value="%s" name="' . WPSL_EXT_SETTINGS_TYPE_DATETIME . '[%s]" id="' . WPSL_EXT_SETTINGS_TYPE_DATETIME . '[%s]">';
			// $datetime_template = '<input type="text" value="%s" name="' . WPSL_EXT_SETTINGS_TYPE_DATETIME . '[%s]" id="%s">';
			$datetime_template = '<input type="text" value="%s" name="' . WPSL_EXT_SETTINGS_TYPE_DATETIME . '[%s]" class="datetimepicker" id="%s">';
			$date_value        = $this->get_datepicker_datetime($text_input['value'], $wpsl_extenders->ext_datetime_format);

			// Render text input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $text_input );
			// $render_output .= sprintf( $datetime_template, $date_value, $text_input['name'], $text_input['name'] );
			$render_output .= sprintf( $datetime_template, $date_value, $text_input['name'], $text_input['slug'] );
			$render_output .= '<span class="wpsl-ext-datetime-format">' . $wpsl_extenders->ext_datetime_format . '</span> ';
			$render_output .= '<span class="wpsl-ext-date-format">' . $wpsl_extenders->ext_date_format . '</span> ';
			$render_output .= '<span class="wpsl-ext-time-format">' . $wpsl_extenders->ext_time_format . '</span> ';
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for sections
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function sanitize_type_datetime( $wpsl_ext_datetime_input = '' ) {

			global $wpsl_extenders;

			// $this->debugMP('msg',__FUNCTION__.' started for wpsl_ext_datetime_input:' . $wpsl_ext_datetime_input );

			if ($wpsl_ext_datetime_input != '') {

				// Reformat the date to UTC standard
				// $formatted_date   = date_i18n( $wpsl_extenders->ext_datetime_format, strtotime( $wpsl_ext_datetime_input ) );
				$formatted_date   = date_i18n( 'Y-m-d H:i:s', strtotime( $wpsl_ext_datetime_input ) );
				// $this->debugMP('msg',__FUNCTION__ . ' wpsl_ext_datetime_input = ' . $wpsl_ext_datetime_input . ' formatted_date = ' . $formatted_date . '!' );

				return $formatted_date;
			}

		}

		/**
		 * Get a part of the value according to the input_format
		 *
		 * @param type $url
		 * @return type
		 */
		public function get_datepicker_datetime($input_value, $input_format = null) {

			global $wpsl_extenders;

			// $this->debugMP('msg',__FUNCTION__ . ' input_value = ' . $input_value . ', input_format = ' . $input_format );

			if ( null === $input_format ) {
				// Get date and time formats
				$input_format = $wpsl_extenders->ext_date_format ;
				if ( empty( $input_format ) ) {
					$input_format = 'Y-m-d';
				}
			}
			// $formatted_date = date_i18n( 'Y-m-d H:i:s', strtotime( $input_value ) );

			// if ( ! empty( $formatted_date ) ) {
				// $this->debugMP('msg',__FUNCTION__ . ' return mysql2date formatted_date = ' . mysql2date( $input_format, $formatted_date ) . ', input_format = ' . $input_format );
				// return mysql2date( $input_format, $formatted_date );
			// }
			if ( ! empty( $input_value ) ) {
				// $this->debugMP('msg',__FUNCTION__ . ' return mysql2date input_value = ' . mysql2date( $input_format, $input_value ) . ', input_format = ' . $input_format );
				return mysql2date( $input_format, $input_value );
			}
			return '';
		}

		/**
		 * Render the settings for a button
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_button( $text_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			// Define templates
			$button_template  = '<input type="submit" value="%s" class="button-primary"/>';

			// Render text input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $text_input, 'vertical-align:top' );
			$render_output .= sprintf( $button_template, $text_input['value'] );
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for some custom html
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_custom( $text_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			// Define templates
			$text_input_template = '<span>%s</span>';
			// $text_input_template = '<input type="text" readonly="readonly" value="%s" name="' . WPSL_EXT_SETTINGS_TYPE_CUSTOM . '[%s]" class="textinput" id="%s">';
			$text_input_template = '<textarea readonly="readonly" style="width:275px;">%s</textarea>';

			// Render text input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $text_input, 'vertical-align:top' );
			$render_output .= sprintf( $text_input_template, $text_input['value'], $text_input['name'], $text_input['slug'] );
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a text box
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_text( $text_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			// Define templates
			$text_input_template = '<input %s type="text" value="%s" name="' . WPSL_EXT_SETTINGS_TYPE_TEXT . '[%s]" class="textinput" id="%s"/>';

			$readonly = '';
			if ( isset($text_input['readonly']) && $text_input['readonly'] ) {
				$readonly = 'readonly="readonly" ';
			}

			// Render text input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $text_input );
			$render_output .= sprintf( $text_input_template, $readonly, $text_input['value'], $text_input['name'], $text_input['slug'] );
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a text box
		 * 
		 * @since 1.1.0
		 * @return html
		 */
		public function render_settings_textarea( $text_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $text_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with text_input:', $text_input );

			$readonly = '';
			if ( isset($text_input['readonly']) && $text_input['readonly'] ) {
				$readonly = 'readonly="readonly" ';
			}

			// Define templates
			$text_input_template = '<textarea %s cols="30" rows="3" name="' . WPSL_EXT_SETTINGS_TYPE_TEXT . '[%s]" class="textinput" id="%s">%s</textarea>';

			// Render text input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $text_input, 'vertical-align:top' );
			$render_output .= sprintf( $text_input_template, $readonly, $text_input['name'], $text_input['slug'], $text_input['value'] );
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a checkbox
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function render_settings_checkbox( $checkbox_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $checkbox_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with checkbox_input:', $checkbox_input );

			// Define templates
			$checkbox_input_template = '<input type="checkbox" value="" %s name="' . WPSL_EXT_SETTINGS_TYPE_CHECKBOX . '[%s]" id="%s"/>';

			// Render checkbox input
			//
			$checked_value = ($checkbox_input['value'] == 1) ? 'checked="checked"' : '';
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $checkbox_input );
			$render_output .= sprintf( $checkbox_input_template, $checked_value, $checkbox_input['name'], $checkbox_input['slug'] );
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a dropdown
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function render_settings_dropdown( $dropdown_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $dropdown_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with dropdown_input:', $dropdown_input );

			// Define templates
			$dropdown_input_template = '<select id="%s" name="' . WPSL_EXT_SETTINGS_TYPE_DROPDOWN . '[%s]">';

			// Render dropdown input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $dropdown_input );
			$render_output .= sprintf( $dropdown_input_template, $dropdown_input['slug'], $dropdown_input['name'] );
			$render_output .= $this->render_settings_dropdown_options( $dropdown_input );
			$render_output .= '</select>';
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a dropdown
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function render_settings_dropdown_options( $input_options = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			// Check input_options
			if ( $input_options == false ) {
				return $render_output;
			}
			if ( ! isset( $input_options['options'] ) ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with input_options:', $input_options );

			// Define templates
			$option_list_template = '<option value="%s" %s>%s</option>';

			// Set default option if no selection available yet
			//
			if ( $input_options['value'] == '' ) {
				$input_options['value'] = $input_options['default'];
			}

			// Render dropdown input for all options
			//
			foreach ( $input_options['options'] as $input_option ) {  
				$selected_value = ($input_options['value'] == $input_option['option_value']) ? 'selected="selected"' : '';
				$render_output .= sprintf( $option_list_template, $input_option['option_value'], $selected_value, $input_option['option_label'] );
			}

			// Return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a dropdown
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function render_settings_iconlist( $icon_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			if ( $icon_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with icon_input:', $icon_input );

			// Define templates
			$iconlist_input_template = '<select id="%s" name="' . WPSL_EXT_SETTINGS_TYPE_ICONLIST . '[%s]">';

			// Render dropdown input
			//
			$render_output .= '<p>';
			$render_output .= $this->render_settings_label( $icon_input );
			// $render_output .= sprintf( $iconlist_input_template, $icon_input['slug'], $icon_input['name'] );
			$render_output .= $this->render_settings_iconlist_options( $icon_input );
			// $render_output .= '</select>';
			$render_output .= '</p>';

			// Close top section header and return output
			//
			return $render_output;

		}

		/**
		 * Render the settings for a dropdown
		 * 
		 * @since 1.0.0
		 * @return html
		 */
		public function render_settings_iconlist_options( $icon_input = false ) {
			// $this->debugMP('msg',__FUNCTION__.' started.');
			$render_output = '';

			// Check icon_input
			if ( $icon_input == false ) {
				return $render_output;
			}
			// $this->debugMP('pr',__FUNCTION__.' started with icon_input:', $icon_input );

			// Define templates
			$icon_list_class_template = '<li %s>';
			$icon_list_image_template = '<img src="%s"/>';
			$icon_list_input_template = '<input %s type="radio" name="' . WPSL_EXT_SETTINGS_TYPE_ICONLIST . '[%s]" value="%s" />';

			$icon_urls = $this->render_settings_iconlist_get_icon_urls( $icon_input );
			// $this->debugMP('pr',__FUNCTION__.' found icon_urls:', $icon_urls );

			// Render dropdown input for all icons
			//
			$render_output .= '<ul class="wpsl-marker-list wpsl-ext-marker-list">';
			foreach ( $icon_urls as $icon_filename => $icon_url ) {
				// Show the selected icon
				if ( $icon_input['value'] == $icon_url ) {
					$checked   = 'checked="checked"';
					$css_class = 'class="wpsl-active-marker"';
				} else {
					$checked   = '';
					$css_class = '';
				}
				$render_output .= sprintf( $icon_list_class_template, $css_class );
				$render_output .= sprintf( $icon_list_image_template, $icon_url );
				$render_output .= sprintf( $icon_list_input_template, $checked, $icon_input['name'], $icon_url );
				$render_output .= '</li>';   // for icon_list_class_template
			}
			$render_output .= '</ul>';   // for ul class="wpsl-ext-marker-list"

			// Return output
			//
			return $render_output;

		}

		/**
		 * Return the icon selector HTML for the icon images in saved markers and default icon directories.
		 *
		 * @param type $inputFieldID
		 * @param type $inputImageID
		 * @return string
		 */
		 function render_settings_iconlist_get_icon_urls( $icon_input = false ) {

			$icon_urls = array();

			// Check input parameters
			if ( ! isset($icon_input['icon_object']) || ($icon_input['icon_object'] == null)) { return $icon_urls; }

			$wpsl_ext_icon_object = $icon_input['icon_object'];     // e.g. $wpsl_ext_social
			$wpsl_ext_icon_subdir = $icon_input['icon_subdir'];     // e.g. 'social-icons/'

			$htmlStr = '';
			$files = array();
			$fqURL = array();

			// If we already got a list of icons and URLS, just use those
			//
			if (
				isset($wpsl_ext_icon_object->iconselector_files ) &&
				isset($wpsl_ext_icon_object->iconselector_urls  ) &&
				($wpsl_ext_icon_object->iconselector_files != '') &&
				($wpsl_ext_icon_object->iconselector_urls  != '')
			   ) {
				$files = $wpsl_ext_icon_object->iconselector_files;
				$fqURL = $wpsl_ext_icon_object->iconselector_urls;

			// If not, build the icon info but remember it for later
			// this helps cut down looping directory info twice (time consuming)
			// for things like icon processing.
			//
			} else {

				// Load the file list from our directories
				//
				// using the same array for all allows us to collapse files by
				// same name, last directory in is highest precedence.
				$icon_assets = apply_filters('wpsl_icon_directories',
						array(
								array('dir' => WPSL_UPLOAD_DIR . $icon_input['icon_subdir'],
									  'url' => WPSL_UPLOAD_URL . $icon_input['icon_subdir']
									 ),
							)
						);

				$fqURLIndex = 0;
				foreach ($icon_assets as $icon) {
					if (is_dir($icon['dir'])) {
						if ($iconDir = opendir($icon['dir'])) {
							$fqURL[] = $icon['url'];
							// $this->debugMP('pr',__FUNCTION__.' found icon:', $icon );
							while ($filename = readdir($iconDir)) {
								if (strpos($filename, '.') === 0) { continue; }
								$files[$filename] = $fqURLIndex;
							};
							closedir($iconDir);
							$fqURLIndex++;
						} else {
							wpsl_ext_add_notice( WPSL_EXT_NOTICE_WARNING,
									sprintf( __('Could not read icon directory %s','wp-store-locator-extenders'), $directory ) );
						}
				   }
				}
				ksort($files);
				$wpsl_ext_icon_object->iconselector_files = $files;
				$wpsl_ext_icon_object->iconselector_urls  = $fqURL;
			}
			// $this->debugMP('pr',__FUNCTION__.' found wpsl_ext_icon_object->iconselector_files:', $wpsl_ext_icon_object->iconselector_files );
			// $this->debugMP('pr',__FUNCTION__.' found wpsl_ext_icon_object->iconselector_urls:', $wpsl_ext_icon_object->iconselector_urls );

			// Build our icon array now that we have a full file list.
			//
			foreach ($files as $filename => $fqURLIndex) {
				if (
					(preg_match('/\.(png|gif|jpg)/i', $filename) > 0) &&
					(preg_match('/shadow\.(png|gif|jpg)/i', $filename) <= 0)
					) {
					$icon_urls[$filename] = $fqURL[$fqURLIndex].$filename;
				}
			}

			// return files found
			return $icon_urls;
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
