<?php

/**
 * @file plugins/generic/registrationNotification/RegistrationNotificationSettingsForm.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
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
		parent::__construct($plugin->getTemplatePath() . 'settingsForm.tpl');
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * @copydoc Form::initData()
	 */
	public function initData() {
		$recipientList = $this->_plugin->getSetting($this->_contextId, 'recipientList');
		$this->setData('recipientList', is_array($recipientList) ? $recipientList : []);
		return parent::initData();
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	public function readInputData() {
		$this->readUserVars(array('email', 'name'));
		return parent::readInputData();
	}

	/**
	 * @copydoc Form::fetch()
	 */
	public function fetch($request) {
		TemplateManager::getManager($request)->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request);
	}

	/**
	 * @copydoc Form::fetch()
	 */
	public function execute() {
		import('lib.pkp.classes.validation.ValidatorEmail');

		$emailValidator = new ValidatorEmail;
		$data = array_filter(
			array_combine($this->getData('email'), $this->getData('name')),
			[$emailValidator, 'isValid'],
			ARRAY_FILTER_USE_KEY
		);
		$this->_plugin->updateSetting($this->_contextId, 'recipientList', $data, 'object');
		return parent::execute();
	}
}
