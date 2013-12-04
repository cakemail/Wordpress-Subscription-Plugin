newsletter-subscription-form
============================

*WordPress Plugin*

**In order to configure the administrative console for the widget, you will need:**
* An editing program (Sublime 2, TextEdit or Notepad)
* The .zip folder for the Newsletter Widget
* Your logo as a 90 px x 90 px png file named “logo.png”
* An Admin header image as a 250 px x 100 px png file named “admin_header.png”
* A CSS style sheet for the following items:
	- background-color
	- font colors for your clients’ user name
	- font color for your clients’ company name

**To override the default text values, color and logo of the widget to reflect your brand:**

Change the default text values using notepad or an editing program to change the values found in:
  `../newsletter-subscription-form/newsletter-subscription-form-overrides.php`
  
```
<?php

$name = 'CakeMail';
$description = __('CakeMail is an easy-to-use email marketing application that lets you build & send professional-looking email campaigns in minutes and track results.', 'newsletter-subscription-widget' );

$widgetTitle = __('Newsletter','newsletter-subscription-widget');
$widgetDescription = __('Subscribe to our newsletter!','newsletter-subscription-widget');
$widgetConfirmationMessage = __('We just sent you an email that you need to click on before you get added to the list','newsletter-subscription-widget');
$widgetSubmitButton = __('Subscribe','newsletter-subscription-widget');
```

Change the files for your logo and admin_header : Replace the following files
  `../newsletter-subscription-form/img/logo.png`
  `../newsletter-subscription-form/img/admin_header.png`


Change the style sheet using notepad or an editing program to change the values found in:
  `[plugins/path]/newsletter-subscription-form/css/newsletter_subscription_backend_overrides.css`


