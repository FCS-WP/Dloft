/* global jQuery:false */

(function() {
	"use strict";
	jQuery(document).on( 'sowsetupformfield', '.siteorigin-widget-field-type-icons', function(e) {
		jQuery(document).trigger('action.init_hidden_elements', [jQuery('.so-panels-dialog')]);
	});
})();