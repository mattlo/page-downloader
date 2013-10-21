/**
 * Application Namespace
 * @type {Object}
 */
var App = {};

App.Constants = (function () {
	'use strict';
	
	return {
		/**
		 * @type {Number}
		 */
		SECTION_ANIMATION_DURATION: 700,
		/**
		 * @type {String}
		 */
		SECTIONS_CSS_SELECTOR: '.input-form > section'
	};
}());

// Main Process
(function () {
	'use strict';

	$(document).ready(function () {
		// bind all step sections
		var sections = $(App.Constants.SECTIONS_CSS_SELECTOR);
		
		// show first one
		sections.eq(0).slideDown(App.Constants.SECTION_ANIMATION_DURATION);
	});

}());