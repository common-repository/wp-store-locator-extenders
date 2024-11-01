=== WP Store Locator - Extenders ===
Plugin Name:       WP Store Locator - Extenders
Contributors: DeBAAT, freemius
Donate link:       https://www.de-baat.nl/wp-store-locator-extenders/
Tags:              Extenders, Events, Social Media, User Managed Locations
Required PHP:      8
Requires at least: 5.9
License:           GPL3
Tested up to:      6.4.3
Stable tag:        1.4.0

Adds power user features like managing location based events, social media information and locations managed by other logged in users to WP Store Locator.

== Description ==

[WP Store Locator](https://wpstorelocator.co/document/) | [Documentation](https://www.de-baat.nl/wp-store-locator-extenders/)

Adds the features needed by power users such as managing location based events, social media information and locations managed by other logged in users.

= How does it work? =

Ensure you have the right versions of [WP Store Locator](https://wordpress.org/plugins/wp-store-locator/) plugin installed.
Use the post_author field of the locations in WP Store Locator and use it to filter for allowed users.

= Features =

Control whether a user is allowed to manage locations.


= Additional Premium Features =

Via the [Documentation](https://www.de-baat.nl/wp-store-locator-extenders/) site, you can purchase the premium version to enhance this Extenders add-on even more with the following features:

* Adds an additional field to location data for each social media supported.
* Adds an additional field to location data for each event supported.

== Installation ==

= Requirements =

* WP Store Locator: 2.2
* WordPress: 5.6
* PHP: 8

= Install After WP Store Locator =

1. Go fetch and install [WP Store Locator](https://wordpress.org/plugins/wp-store-locator/).
2. Install this plugin directly from the WordPress org site.

OR

2. Download this plugin from the WordPress org site to get the latest .zip file.
3. Go to plugins/add new.
4. Select upload.
5. Upload the zip file.

== Frequently Asked Questions ==

= What are the terms of the license? =

The license for the free plugin is GPL. You get the code, feel free to modify it as you wish. We prefer that our customers pay us for the Premium version because they like what we do and want to support our efforts to bring useful software to market. Learn more on our [DeBAAT License Terms](https://www.de-baat.nl/general-eula/) page.

= How does the add-on work? =

The add-on adds a new page to the set of WP Store Locator configuration pages.
The 'Extenders' page presents the Admin with a section to manage the settings to control the working of this add-on.

It also extends the 'User Page' with additional information and settings to the list of configured users.
The manage locations capability of each individual user can be toggled between Allow and Disallow.
Each allowed user will get access to the Locations configuration page of the WP Store Locator plugin.
Each individual location can be managed by both the Admin and the allowed user.
The Admin also has the capability to change the value of the store user.



== Changelog ==

= 1.4.0 =
* Tested to work with WP 6.4.3
* Updated Freemius SDK to V2.6.0
* Started replacing Twitter with X

= 1.3.2 =
* Tested to work with WP 6.1.1.
* Tested to work with PHP 8
* Fixed use of static functions
* Fixed jquery.js issue
* Updated way to define constants
* Updated Freemius SDK to V2.5.3

= 1.3.1 =
* Tested to work with WP 5.9.1.
* Security fix

= 1.3.0 =
* Tested up to WP 5.9
* Added extended template designs for assisting with searches
* Fixed searching for Events and Social Media

= 1.2.0 =
* Fixed showing notices
* Tested up to WP 5.8
* Added additional content for CPT


= 1.1.5 =
* Reworked options to be handled by object
* Split User Managed options into separate class
* Updated Freemius SDK to V2.4.2


= 1.1.4 =
* Fixed check on active WP Store Locator plugin
* Fixed debugging code


= 1.1.3 =
* Fixed licensing

= 1.1.2 =
* Fixed updating options

= 1.1.1 =
* Fixed uninitialised options

= 1.1.0 =
* Added support for Freemius
* Rearranged folder structure and added WPSL_EXT_Activate class
* Fixed uninitialised options


= 1.0.0 =
* First official version
