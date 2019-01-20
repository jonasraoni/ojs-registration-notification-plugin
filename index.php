<?php

/**
 * @defgroup plugins_generic_registrationNotification
 */

/**
 * @file plugins/generic/registrationNotification/index.php
 *
 * Copyright (c) 2003-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @ingroup plugins_generic_registrationNotification
 * @brief Wrapper for registrationNotification checking plugin.
 *
 */
require_once('RegistrationNotificationPlugin.inc.php');

return new RegistrationNotificationPlugin();
