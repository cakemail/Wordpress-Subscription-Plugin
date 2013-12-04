newsletter-subscription-form
============================

*WordPress Plugin*

**In order to configure the administrative console for the widget, you will need:**
* An editing program (Sublime 2, TextEdit or Notepad)
* The .zip folder for the Newsletter Widget
* Your logo as a 90 px x 90 px png file named “logo.png”
* A CSS color code for the following items:
	- background-color
	- font colors for your clients’ user name
	- font color for your clients’ company name

**To override the default text values, color and logo of the widget to reflect your brand:**

Change the default text values using notepad or an editing program to change the values found in:
  `../newsletter-subscription-form/newsletter-subscription-form-overrides.php`

```
<?php

$name = 'Your Company Name';
$description = 'Your mission, vision or a funny anecdote';

$widgetTitle = 'Newsletter';
$widgetDescription = 'Sign up for my newsletter';
$widgetConfirmationMessage = 'Thanks for signing up! Check your emails for the confirmation email we just sent you.';
$widgetSubmitButton = 'Subscibe';
```

Change the file for your logo: Replace the following file
  `../newsletter-subscription-form/img/logo.png`


Change the style sheet using notepad or an editing program to change the **Hex values only** found in:
  `...newsletter-subscription-form/css/newsletter_subscription_backend_overrides.css`

```
div.newsletter div.header {
    background-color: #fcfcfc;
}
div.newsletter div.header div.username {
    color: #333333;
}
div.newsletter div.header div.company {
    color: #999999;
}
```
