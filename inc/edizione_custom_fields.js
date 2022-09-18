jQuery(document).ready(function ($) {

	'use strict';

	$(document).ajaxComplete(function (event, xhr, settings) {
		$('body.taxonomy-edizione #publication_date').val('');
	});

});
