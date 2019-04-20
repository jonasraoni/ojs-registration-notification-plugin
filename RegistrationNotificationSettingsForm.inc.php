<?php

/**
 * @file plugins/generic/registrationNotification/RegistrationNotificationSettingsForm.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class RegistrationNotificationSettingsForm
 * @ingroup plugins_generic_registrationNotification
 *
 * @brief Form to allow managers to modify the Registration Notification settings
 */

import('lib.pkp.classes.form.Form');

class RegistrationNotificationSettingsForm extends Form {

	/** @var int Associated context ID */
	private $_contextId;

	/** @var RegistrationNotificationPlugin Registration notification plugin */
	private $_plugin;

	/**
	 * Constructor
	 * @param $plugin RegistrationNotificationPlugin Registration notification plugin
	 * @param $contextId int Context ID
	 */
	public function __construct(RegistrationNotificationPlugin $plugin, $contextId) {
		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
		import('lib.pkp.classes.validation.ValidatorEmail');
		$emailValidator = new ValidatorEmail;
		$this->addCheck(new FormValidatorArrayCustom($this, 'email', 'required', 'validator.email', function ($value) use ($emailValidator) {
			return $emailValidator->isValid($value);
		}));
	}

	/**
	 * @copydoc Form::initData()
	 */
	public function initData() {
		$recipientList = $this->_plugin->getSetting($this->_contextId, 'recipientList');
		$this->setData('email', is_array($recipientList) ? array_keys($recipientList) : []);
		$this->setData('name', is_array($recipientList) ? array_values($recipientList) : []);
		return parent::initData();
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	public function readInputData() {
		$this->readUserVars(array('email', 'name'));
		$emails = $this->getData('email');
		$names = $this->getData('name');
		foreach($emails as $i => $email) {
			//clean empty entries
			if(empty($email) && empty($names[$i])){
				unset($emails[$i]);
				unset($names[$i]);
			}
		}
		$this->setData('email', array_values($emails));
		$this->setData('name', array_values($names));
		return parent::readInputData();
	}

	/**
	 * @copydoc Form::fetch()
	 */
	public function fetch($request) {
		$templateManager = TemplateManager::getManager($request);
		$templateManager->assign('pluginName', $this->_plugin->getName());
		$templateManager->addJavaScript(
			'RegistrationNotificationFormHandler',
			$request->getBaseUrl() . '/' . $this->_plugin->getPluginPath() . '/js/RegistrationNotificationFormHandler.js',
			[
				'priority' => STYLE_SEQUENCE_CORE,
				'contexts' => 'RegistrationNotificationSettingsForm'
			]
		);
		return parent::fetch($request);
	}

	/**
	 * @copydoc Form::fetch()
	 */
	public function execute() {
		$this->_plugin->updateSetting($this->_contextId, 'recipientList', array_combine($this->getData('email'), $this->getData('name')), 'object');
		return parent::execute();
	}
}
