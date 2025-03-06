=== Mail Blaze for WP ===
Contributors: Mail Blaze
Tags: mailblaze, email marketing, subscribe, contact form, newsletter
Requires at least: 4.1
Tested up to: 4.9.6
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.2.4

Mail Blaze for WordPress, the official Mail Blaze plugin.

== Description ==

#### Mail Blaze for WordPress

*Allowing your visitors to subscribe to your Mail Blaze lists.*

This plugin helps you grow your Mail Blaze lists and write better newsletters through various methods. You can create good looking opt-in forms or integrate with any existing form on your site, like your comment, contact or checkout form.

#### Some (but not all) features

- Connect with your Mail Blaze account.

- Sign-up forms which are good looking, user-friendly and mobile optimized. You have complete control over the form fields and can send anything you like to Mail Blaze.

- Seamless integration with the following plugins:
	- Default WordPress Comment Form
	- Default WordPress Registration Form
	- Contact Form 7
	- WooCommerce
	- Gravity Forms
	- Ninja Forms 3
	- WPForms
	- BuddyPress
    - MemberPress
	- Events Manager
	- Easy Digital Downloads


#### What is Mail Blaze?

Mail Blaze is a newsletter service that allows you to send out email campaigns to a list of email subscribers.

This plugin allows you to tightly integrate your WordPress site with your Mail Blaze account.

If you are not yet using Mail Blaze, create an account here: http://mailblaze.com/pricing.

== Installation ==

#### Installing the plugin
1. In your WordPress admin panel, go to *Plugins > Add New*. Click on the *Upload Plugin* button and upload mailblaze-for-wp.zip.
2. Activate the plugin
3. Set [your API key](https://control.mailblaze.com/customer/index.php/api-keys/index) in the plugin settings.

#### Configuring Sign-Up Form(s)
1. Go to *Mail Blaze > Forms*
2. Select at least one list to subscribe people to.
3. *(Optional)* Add more fields to your form.
4. Embed a sign-up form in pages or posts using the `[mb4wp_form]` shortcode.
5. Show a sign-up form in your widget areas using the "MailBlaze Sign-Up Form" widget.
6. Show a sign-up form from your theme files by using the following PHP function.

`
<?php

if( function_exists( 'mb4wp_show_form' ) ) {
	mb4wp_show_form();
}
`

#### Need help?
Please take a look at the [Mail Blaze support page](https://www.mailblaze.com/support). 

Alternatively, contact support@mailblaze.com.

== Frequently Asked Questions ==

#### How to display a form in posts or pages?
Use the `[mb4wp_form]` shortcode.

#### How to display a form in widget areas like the sidebar or footer?
Go to **Appearance > Widgets** and use the **MailBlaze for WP Form** widget that comes with the plugin.

#### Where can I find my API key to connect to MailBlaze?
[You can find your API key here](https://control.mailblaze.com/customer/index.php/api-keys/index)

#### How to add a sign-up checkbox to my Contact Form 7 form?
Use the following shortcode in your CF7 form to display a newsletter sign-up checkbox.

`
[mb4wp_checkbox "Subscribe to our newsletter?"]
`

#### The form shows a success message but subscribers are not added to my list(s)?
If the form shows a success message, there is no doubt that the sign-up request succeeded. MailBlaze could have a slight delay sending the confirmation email though, please just be patient and make sure to check your SPAM folder.

If you have double opt-in set in your list, then subscribers will be sent an email to confirm their subscription. This is the recommended setting.

#### How can I style the sign-up form?
You can use custom CSS to style the sign-up form if you do not like the themes that come with the plugin. The following selectors can be used to target the various form elements.

`
.mb4wp-form { ... } /* the form element */
.mb4wp-form p { ... } /* form paragraphs */
.mb4wp-form label { ... } /* labels */
.mb4wp-form input { ... } /* input fields */
.mb4wp-form input[type="checkbox"] { ... } /* checkboxes */
.mb4wp-form input[type="submit"] { ... } /* submit button */
.mb4wp-alert { ... } /* success & error messages */
.mb4wp-success { ... } /* success message */
.mb4wp-error { ... } /* error messages */
`

You can add your custom CSS to your theme stylesheet or (easier) by using a plugin like [Simple Custom CSS](https://wordpress.org/plugins/simple-custom-css/#utm_source=wp-plugin-repo&utm_medium=mailblaze-for-wp&utm_campaign=after-css-link)

#### I'm getting an "HTTP Error" when trying to connect to Mail Blaze

If you're getting an `HTTP Error` after entering your API key, please contact your webhost and ask them if they have PHP CURL installed and updated to the latest version (7.58.x). Make sure requests to `https://api.mailblaze.com/` are allowed as well.

#### How do I show a sign-up form in a pop-up?

We recommend the [Boxzilla pop-up plugin](https://wordpress.org/plugins/boxzilla/) for this. You can use the form shortcode in your pop-up box to show a sign-up form.


== Other Notes ==

#### Support

Please take a look at the [Mail Blaze support page](https://www.mailblaze.com/support).


== Changelog == 



