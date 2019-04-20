{**
 * plugins/generic/registrationNotification/templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * Registration Notification plugin settings
 *
 *}
{load_script context="RegistrationNotificationSettingsForm"}
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#registrationNotificationSettingsForm').pkpHandler(
			'$.pkp.controllers.form.registrationNotification.RegistrationNotificationFormHandler', 
			{ldelim}removeCaption: {translate|json_encode key="common.remove"}{rdelim}
		);
	{rdelim});
</script>

<form class="pkp_form" id="registrationNotificationSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	<div id="registrationNotificationSettings">
		<div id="description">{translate key="plugins.generic.registrationNotification.description"}</div>

		<h3>{translate key="navigation.settings"}</h3>	
			{csrf}
			{include file="controllers/notification/inPlaceNotification.tpl" notificationId="registrationNotificationSettingsFormNotification"}

			{fbvFormArea id="registrationNotificationSettingsFormArea"}
				{foreach from=$email key=index item=value}
					{fbvFormSection}
						{fbvElement type="text" label="email.email" id="email-`$index`" name="email[]" value=$value inline=true size=$fbvStyles.size.SMALL}
						{fbvElement type="text" label="common.name" id="name-`$index`" name="name[]" value=$name[$index] inline=true size=$fbvStyles.size.MEDIUM}
						{fbvElement type="button" label="common.remove" id="remove-`$index`" inline=true class="default remove-button"}
					{/fbvFormSection}
				{/foreach}
				{fbvFormSection}
					{fbvElement type="text" label="email.email" id="new-email" name="email[]" inline=true size=$fbvStyles.size.SMALL}
					{fbvElement type="text" label="common.name" id="new-name" name="name[]" inline=true size=$fbvStyles.size.MEDIUM}
					{fbvElement type="button" label="common.more" id="insert" inline=true class="pkp_button_primary default insert-button"}
				{/fbvFormSection}
			{/fbvFormArea}

			{fbvFormButtons}
			<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
	</div>
</form>
