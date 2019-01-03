<?php

/**
 * @file plugins/generic/registrationNotification/RegistrationNotificationPlugin.inc.php
 *
 * Copyright (c) 2003-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief registrationNotification plugin
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class RegistrationNotificationPlugin extends GenericPlugin {
	/** @const EMAIL_SEPARATOR The email separator */
	const EMAIL_SEPARATOR = ',';

	/** @var string The detected username */
	private $_username = null;

	/** @var array The recipient list */
	private $_recipientList = null;

	/**
	 * @copydoc Plugin::register()
	 */
	public function register($category, $path, $mainContextId = null) {
		$isRegistered = parent::register($category, $path, $mainContextId);
		if ($isRegistered && $this->getEnabled($mainContextId)) {
			if($this->getRecipientList()) {
				HookRegistry::register('pkpuserdao::_insertobject', [$this, 'triggerHook']);
			}
			$this->_registerTemplateResource();
		}
		return $isRegistered;
	}

	/**
	 * Detects the beginning of a new user registration and subscribes to the method PKPUserDao::getByUsername
	 * @param $hookName string
	 * @param $args array
	 * @return bool Always false in order to allow other hooks to be processed
	 */
	public function triggerHook($hookName, $args) {
		if(!empty($args[1][0])) {
			$this->_username = $args[1][0];
			HookRegistry::register('pkpuserdao::_getbyusername', [$this, 'commitHook']);
		}
		return false;
	}

	/**
	 * Asserts that the detected username matches the one that the application is currently trying to load and sends the notification
	 * @param $hookName string
	 * @param $args array
	 * @return bool Always false in order to allow other hooks to be processed
	 */
	public function commitHook($hookName, $args) {
		if(!empty($args[1][0]) && $this->_username === $args[1][0]) {
			$this->_username = null;
			$this->notify($args[1][0]);
		}
		return false;
	}

	/**
	 * Retrieves the list of recipients from the plugin settings and caches it
	 * @return array List of recipients, where key is the email and value is the name
	 */
	private function getRecipientList() {
		if(
			!is_array($this->_recipientList)
			&& !is_array($this->_recipientList = $this->getSetting($this->getCurrentContextId(), 'recipientList'))
		) {
			$this->_recipientList = [];
		}
		return $this->_recipientList;
	}

	/**
	 * Sends the email notification
	 * @param $username string The username of the new user
	 * @return boolean True if the notification was successfully sent
	 */
	private function notify($username) {
		$userDao = DAORegistry::getDAO('UserDAO');
		if(!($user = $userDao->getByUsername($username))) {
			return false;
		}

		import('lib.pkp.classes.mail.MailTemplate');

		$mail = new MailTemplate('REGISTRATION_NOTIFICATION');
		$mail->setReplyTo(null);
		foreach($this->getRecipientList() as $email => $name) {
			$mail->addRecipient($email, $name);
		}
		$mail->assignParams(array_map('htmlspecialchars', [
			'date' => $user->getDateRegistered(),
			'userFullName' => $user->getFullName(),
			'userName' => $user->getUsername(),
			'userEmail' => $user->getEmail()
		]));

		return $mail->send();
	}

 	/**
	 * @copydoc Plugin::manage()
	 */
	public function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'settings':
				AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_MANAGER);
				$this->import('RegistrationNotificationSettingsForm');
				$form = new RegistrationNotificationSettingsForm($this, $request->getContext()->getId());

				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						$notificationManager = new NotificationManager();
						$notificationManager->createTrivialNotification($request->getUser()->getId());
						return new JSONMessage(true);
					}
				} else {
					$form->initData();
				}
				return new JSONMessage(true, $form->fetch($request));
		}
		return parent::manage($args, $request);
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	public function getActions($request, $verb) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()
			? [
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, ['verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic']),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				)
			]
			: [],
			parent::getActions($request, $verb)
		);
	}

	/**
	 * @copydoc PKPPlugin::getTemplatePath
	 */
	public function getTemplatePath($inCore = false) {
		return $this->getTemplateResourceName() . ':templates/';
	}

	/**
	 * @see Plugin::getInstallEmailTemplatesFile
	 */
	public function getInstallEmailTemplatesFile() {
		return $this->getPluginPath() . DIRECTORY_SEPARATOR . 'emailTemplates.xml';
	}

	/**
	 * @see Plugin::getInstallEmailTemplateDataFile
	 */
	public function getInstallEmailTemplateDataFile() {
		return $this->getPluginPath() . '/locale/{$installedLocale}/emailTemplates.xml';
	}

	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	public function getDisplayName() {
		return __('plugins.generic.registrationNotification.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	public function getDescription() {
		return __('plugins.generic.registrationNotification.description');
	}

	/**
	 * @see Plugin::getInstallSitePluginSettingsFile()
	 */
	public function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}
}
