{**
 * plugins/generic/registrationNotification/templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Registration Notification plugin settings
 *
 *}
<div id="registrationNotificationSettings">
<div id="description">{translate key="plugins.generic.registrationNotification.description"}</div>

<h3>{translate key="navigation.settings"}</h3>

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#registrationNotificationSettingsForm')
			.on('click', '.remove-button', function() {ldelim}
				$(this).parents('.section').remove();
			{rdelim})
			.on('click', '.insert-button', function() {ldelim}
				var section = $(this).parents('.section');
				var clone = section.clone();
				section.find(':input:not(button)').val('');
				clone.find('.insert-button')
					.removeClass('insert-button pkp_button_primary')
					.addClass('remove-button')
					.text('{translate key="common.remove"}');
				clone.insertBefore(section);
			{rdelim})
			.pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="registrationNotificationSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="registrationNotificationSettingsFormNotification"}

	{fbvFormArea id="registrationNotificationSettingsFormArea"}
		{foreach from=$recipientList key=email item=name name=email}
			{fbvFormSection}
				{assign var="index" value=$smarty.foreach.email.index}
				{fbvElement type="text" required="required" label="email.email" id="email-`$index`" name="email[]" value=$email inline=true size=$fbvStyles.size.SMALL}
				{fbvElement type="text" label="common.name" id="name-`$index`" name="name[]" value=$name inline=true size=$fbvStyles.size.MEDIUM}
				{fbvElement type="button" label="common.remove" id="remove-`$index`" inline=true class="default remove-button"}
			{/fbvFormSection}
		{/foreach}
		{fbvFormSection}
			{fbvElement type="text" required="required" label="email.email" id="new-email" name="email[]" inline=true size=$fbvStyles.size.SMALL}
			{fbvElement type="text" label="common.name" id="new-name" name="name[]" inline=true size=$fbvStyles.size.MEDIUM}
			{fbvElement type="button" label="common.more" id="insert" inline=true class="pkp_button_primary default insert-button"}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
	<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</form>
</div>
