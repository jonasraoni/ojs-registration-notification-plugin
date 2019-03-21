/**
 * @file js/RegistrationNotificationFormHandler.js
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.registrationNotification
 * @class RegistrationNotificationFormHandler
 *
 * @brief Registration Notification form handler.
 */
(function ($) {

	/** @type {Object} */
	$.pkp.controllers.form.registrationNotification = $.pkp.controllers.form.registrationNotification || {};

	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.form.AjaxFormHandler
	 *
	 * @param {jQueryObject} $formElement A wrapped HTML element that
	 *  represents the approved proof form interface element.
	 * @param {Object} options Tabbed modal options.
	 */
	$.pkp.controllers.form.registrationNotification.RegistrationNotificationFormHandler = function ($formElement, options) {
		// Attach the form handler.
		$formElement
		.on('click', '.remove-button', function () {
			$(this).parents('.section').remove();
		})
		.on('click', '.insert-button', function () {
			var section = $(this).parents('.section');
			var clone = section.clone();
			section.find('.error').remove();
			clone.find('[id]').each(function () {
				var
					item = $(this),
					oldId = item.attr('id'),
					newId = 'id-' + Math.random().toString(36);
				item.attr('id', newId);
				clone.find('[for=' + oldId + ']').attr('for', newId);
			});
			section.find(':input:not(button)').val('');
			clone.find('.insert-button')
				.removeClass('insert-button pkp_button_primary')
				.addClass('remove-button')
				.text(options.removeCaption);
			clone.insertBefore(section);
		});
		this.parent($formElement, options);
	};
	$.pkp.classes.Helper.inherits(
		$.pkp.controllers.form.registrationNotification.RegistrationNotificationFormHandler,
		$.pkp.controllers.form.AjaxFormHandler
	);

	/** @param {jQuery} $ jQuery closure. */
}(jQuery));
