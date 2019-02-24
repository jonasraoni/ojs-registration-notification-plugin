# Registration Notification Plugin

## Description
Once enabled, the plugin will send an email notification to a configurable list of emails whenever a new user gets registered.

## Configuration
Each journal/press must be configured individually since the settings are not shared.

### Recipients
The emails must be defined through the plugin settings, the interface supports adding a list of recipients.

### Email Template
The email content is configurable through the email template named **Registration Notification**. The following variables are provided by the plugin:
- $date
- $userFullName
- $userName
- $userEmail
