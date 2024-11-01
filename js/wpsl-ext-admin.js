/*****************************************************************
 * file: wpsl-ext-admin.js
 *
 *****************************************************************/

// jQuery(document).ready(function($) {
	// $('#wpsl-event-start-date, #wpsl-event-end-date').datetimepicker({
    	// var $element = jQuery(this);
		// // dateFormat : 'dd-mm-yyyy HH:mm'
		// // dateFormat : $( element ).val().closest( 'fieldset' ).find( '.wpsl_ext_datetime_format' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );
	    // wpslInitSingleDatepicker( $element) ;
	// });
// });

// function wpslInitDatepicker() {
    // jQuery('.datepicker').each(function () {
jQuery(document).ready(function($) {
    jQuery('#wpsl-event-start-date, #wpsl-event-end-date').each(function () {
    	var $element = jQuery(this);
	    wpslInitSingleDatepicker( $element) ;
    });
});
// }

function wpslInitSingleDatepicker( $element ) {
	var inputId = $element.attr( 'id' ) ? $element.attr( 'id' ) : '',
		optionsObj = {
			format: 'Y-m-d H:i',
			// format: $element.parent().siblings("[id^='wpsl_ext_datetime_format']").val(),
			// format: 'd-m-Y H:i',
			// formatDate: 'd-m-Y',
			// formatTime: 'H:i',
			// dateformat: 'Y-m-d d-m-Y',
			// timeformat: 'H:i',
		};
	optionsObj.format = $element.parent().find( '.wpsl-ext-datetime-format' ).text();
	// optionsObj.format = $element.parent().siblings("[id^='wpsl_ext_datetime_format']").val();
	// optionsObj.formatDate = $element.parent().siblings("[id^='wpsl_ext_date_format']").val();
	// optionsObj.formatTime = $element.parent().siblings("[id^='wpsl_ext_time_format']").val();

	$element.datetimepicker(optionsObj);

	// We give the input focus after selecting a date which differs from default datetimepicker behavior; this prevents
	// users from clicking on the input again to open the datetimepicker. Let's add a manual click event to handle this.
	if( $element.is( ':input' ) ) {
		$element.click( function() {
			$element.datetimepicker( 'show' );
		} );
	}
}
