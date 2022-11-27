# PHP Magazine
PHP Magazine! is a website for sharing blog posts, it is an open source solution that is available for free to everyone. With PHP Magazine you can create your own site to upload news or information that you think is relevant, PHP Magazine gives you the ability to have a presence on the internet, whether you are a newspaper or blogger. This platform was released in December 2022 after hard coding work and I guarantee that it will continue to be maintained.

![PHP Magazine Banner](https://soyvillareal.com/docs/php-magazine/images/banner-dark.png)

## Live [Demo](https://phpmagazine.soyvillareal.com/)
We will keep this demo updated with our newest releases as they come, so that you can check out new features there first before deciding to deploy them in your own environments. 

To see all the features available to create a post visit the [example post](https://phpmagazine.soyvillareal.com/example-post-all-inputs-and-features) hosted on our demo.

## Donate
<span class="badge-paypal"><a href="https://www.paypal.me/SoyVillareal" title="Donate to this project using Paypal" target="_blank"><img src="https://img.shields.io/badge/paypal-donate-yellow.svg?style=for-the-badge&logo=paypal" alt="PayPal donate button" /></a></span>

If you want to donate you can do it through Paypal. Any help greatly appreciated and would help me a lot with server costs. 😊

## Table of contents:
- [Important Information](#important-information)
- [Server Requirements](#server-requirements)
- [Dependencies installed](#make-sure-you-have-the-following-dependencies-installed)
- [For install](#for-install)
- [Change platform information](#change-platform-information)
- [Personalization](#personalization)
  - [Images](#images)
  - [Color palette](#color-palette) 
- [For your curiosity](#for-your-curiosity)
- [Version BETA](#version-beta)
- [Contributing](#contributing)
- [License](#license)

## Important Information

> PHP Magazine can be installed on any server, it is made to consume few resources. However, this will vary depending on the traffic flow you handle.

This beta version does not have a user-friendly installer, so you will have to upload the files to your server and configure the different features manually. But I assure you it won't be that complex 😅.

### Server Requirements
- Node.js 19.x+ (Optional)
- PHP 7.4.x+
- MySQL 8.0.x+
- Apache 2.4.x+ (with mod_rewrite enabled)

### Make sure you have the following dependencies installed
- Curl
- GD Library
- Mbstring
- Mail
- OpenSSL

Once Prerequisites Are Installed
---------------

Now that the prerequisites are ready to go it's a few simple commands to get your instance up and running.

```bash
# Get the latest version of PHP Magazine
git clone https://github.com/soyvillareal/PHP-Magazine.git

# Install PHP libraries
composer install

# If you won't be using NodeJS, you can skip this step, but if you want to use it in the future, you'll need to go back and do it.
# Install Node modules
npm install

# Then simply start your app
npm start
```

## For install:
Once your files, dependencies, and requirements are ready, make sure to:

- Locate the `PHP-Magazine.sql` file and import it to your server.

- locate the info.php file, it is located in the path `/assets/includes/info.php`. Once here, do what the comments in the code indicate:

  <img src="https://soyvillareal.com/docs/php-magazine/images/info.png">


## Change platform information

At the moment, to make these types of changes you need to visit the database and locate yourself in the `settings` table, here you will find the following:

**Note:** I know that it can be cumbersome to have to modify manually, I assure you that the following table is only here until I develop the administration panel, for now enjoy the beta version. And remember everything that doesn't appear here but does appear in the `settings` table of the database. It must not be manually modified. DO NOT TOUCH IT! 😑

name | expected values | description
:---: | :---: | :---:
title | `string` | Website title
description | `string` | Website Description
keywords | `string separate by comma (,)` | Website keywords.
contact_email | `email` | Contact email for the contact form on the website.
facebook_page | `URL` | Website Facebook profile url
twitter_page | `URL` | URL of the Twitter profile of the website.
instagram_page | `URL` | Url of the Instagram profile of the website.
language | `two letter string` | Default language of the website (look at the `language` table in the database and note what is available. eg: `en`)
timezone | `timezone` | Time zone of the place where your business or enterprise is located, for example: `America/Bogota`. See [list of supported time zones](https://www.php.net/manual/en/timezones.php)
switch_mode | `on or off` | Allow to change the mode of the site (Dark or light), if it is left in `off` the value that `theme_mode` has will be used by default
dir_pages | `ltr or rtl` | Address of the terms and conditions and habeas data pages (You must modify it according to the language of the letter)
dismiss_cookie | `on or off` | Show cookies alert or not
server_type | `string` | Mail server type (eg. smtp)
smtp_encryption | `ssl or tls` | Encryption of your mail server
smtp_host | `domain or IP` | Host of your mail server
form_email | `email` | Email from where they are sent
smtp_username | `anything` | Username of your mail server
smtp_password | `anything` | Password of your mail server
smtp_port | `int` | Port of your mail server
token_expiration_attempts | `int` | Number of times it is allowed to request a new token, either; email verification, email change, password recovery or two-factor authentication
token_expiration_hours | `int` | Hours to wait to request a new token after exceeding the `token_expiration_attempts` limit
verify_email | `on or off` | Send a 6-digit code to the email of the person who registers to verify the account.
show_palette | `on or off` | Show or hide window to modify the color palette of the website (it can only be seen by administrators anyway).
2check | `on or off` | Turn factor authentication for users on or off.
newsletter | `on or off` | Activate or deactivate the sending of newsletters
post_article | `all or publisher` | Allow everyone or only editors, moderators and admins to upload posts
approve_posts | `on or off` | Allow the publication of articles only if they are approved (`on`) or without the need for this (`off`)
fb_comments | `on or off` | Activate or deactivate the Facebook comments plugin.
fb_app_id | `int` | Facebook Application ID (You will need to put this here to allow registration or login to an account with this social network if you want to use the Facebook comments plugin: `fb_comments = on`)
fb_secret_id | `string` | Secret id of your facebook application (It is necessary to register or enter an account with this social network)
tw_api_key | `string` | Public key of the Twitter application (You must place this here to allow registering or entering an account with this social network)
tw_api_key_secret | `string` | Twitter application private key (You must place this here to allow registration or login to an account with this social network)
google_analytics | `string` | Google Analytics Tracking ID.
go_app_id | `string` | Google App ID (You will need to put this here to allow signing up or signing into an account with this method).
go_secret_id | `string` | Secret key of the Google application (You must place this here to allow registering or entering an account with this means.
recaptcha | `on or off` | Activate or deactivate Recaptcha.
recaptcha_private_key | `string` | Recaptcha API private key.
recaptcha_public_key | `string` | Recaptcha API public key.
nodejs | `on or off` | Enable or disable nodejs.
node_hostname | `domain or IP` | NodeJS hostname.
node_server_port | `int` | Port where NodeJS is running.
file_size_limit | `int (bytes)` | Maximum size for files, this applies to messages and publications.
max_words_about | `int` | Maximum number of characters for the user's description in their settings.
max_words_comments | `int` | Maximum number of characters for system comments.
max_words_report | `int` | Maximum number of characters for the reports sent, can be: user, publication, comment or a response.
max_words_unsub_newsletter | `int` | Maximum number of characters to unsubscribe from the newsletter with the `Other` option.
number_labels | `int` | Number of tags allowed when creating or editing a publication.
number_of_fonts | `int` | Number of sources allowed when creating or editing a publication.
censored_words | `string separate by comma (,)` | Words censored each time a comment is made or a message is sent.
hidden_domains | `string separate by comma (,)` | Prohibited domains for the website, this applies to system messages and comments.

And that's it! Your first user registered will automatically be an admin user and you will be able to see the admin and moderation functionality. Each additional user will be a regular user.

## Personalization

### Images
In the path `themes/default/images` you will find the following list of images:

- favicon.ico (Website icon)
- logo-light.png (Light mode logo)
- logo-night.png (Dark mode logo)
- opengraph.jpeg (Image displayed on social media for all pages except user and post)
- preloader.gif (Don't touch it unless you know what you're doing)

You can replace the above images with the ones that best represent your website.

### Color palette
If you need to modify the color palette of the website, you can do it without much problem. Just make sure you have `show_palette` (see [table above](#change-platform-information) if you don't know what this means) set to `on` and log in with an administrator account to do this.

![Edit color palette](https://soyvillareal.com/docs/php-magazine/images/edit-color-palette.gif)

### For your curiosity
The demo of this version is running on a server with the following characteristics:

- Ubuntu 20.04.5
- Node.js 19.0.0
- PHP 7.4.33
- MySQL 8.0.31
- Apache 2.4.41

# Version BETA
The beta version of PHP Magazine is released, it contains most of the functional features for the end user. In this version its installation and manipulation by the web site administrator must be manual.

# Changelog
Please refer to the description of each [release](https://github.com/soyvillareal/PHP-Magazine/releases) or the git log.

# Roadmap
PHP Magazine is a project developed with a quality interface, designed to be functional and put into production for whoever requires it. This project will continue to be maintained by the author. New features and bug fixes will be added frequently.

For all our feature and bug tracking, we use the [Issues Section](https://github.com/soyvillareal/PHP-Magazine/issues). PHP Magazine 'roadmap' is currently to work through the feature requests and improvements that are in the issue tracker.  Take a look at the milestones for what we intend to add for upcoming releases.

# Contributing
### Features, Improvements, and Bugfixes
PHP Magazine is currently only maintained by the author so we are grateful for any additional contributions. Send through a Pull Request and we will review it ASAP.

If you're not sure what to work on, take a look at the [issues](https://github.com/soyvillareal/PHP-Magazine/issues). There are lots to do!

# License
PHP Magazine is distributed under the terms of the [MIT License](LICENSE.md).
