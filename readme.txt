=== App Display Page ===
Contributors: mjar81
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mjar81%40gmail%2ecom&lc=US&item_name=Mark%20Rickert&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: iOS, App Store, iTunes, apps, appstore, iphone, ipad, objective-c, obj-c, affiliate, phg, linkshare
Requires at least: 2.7
Tested up to: 3.3
Stable tag: 1.7.1

Adds a shortcode to display information about iOS apps from Apple's App Store.

== Description ==

This is a simple Wordpress plugin that you can modify to fit your own needs. Use your iOS's application id and it will spit out some basic HTML that you can then apply your own styling to. You can even use your PHG iTunes Affiliate code to earn some money from your links!

Why?

I wanted to make sure that I had accurate descriptions and current pricing information for all my apps wherever I display this data on my personal websites. This was the natural thing to do.

Check out the example of what it produces here: http://www.mohawkapps.com/checkout-helper/

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place the shortcode `[ios_app id="1234"]` (where "1234" is your application's App Store ID) anywhere in a post or page.

== Frequently Asked Questions ==

None.

== Screenshots ==

Please visit http://www.mohawkapps.com/checkout-helper/ for a working example.

== Changelog ==

= 1.7.1 =
* Fix some caching issues.
* Simplify codebase for easier maintainability.
* Addressed issue where same screenshot would be displayed when caching on your server.

= 1.7 =
* Now allows the user to turn on off the smart banner functionality in the admin.
* Updated code for new iTunes affiliate program and removed old Linkshare code. Set your new PHG Affiliate code and campaign in the admin.

= 1.6 =
* Added store country selector to the admin page to change store search countries. Defaults to USA.

= 1.5.1 =
* Added linkshare ID option to settings. If filled in, it will change the links to the iTunes store to use your affiliate ID.

= 1.5 =
* Added the Smart App Banner (new feature in iOS 6's version of Safari) for pages with the shortcode included. http://developer.apple.com/library/ios/Documentation/AppleApplications/Reference/SafariWebContent/PromotingAppswithAppBanners/PromotingAppswithAppBanners.html

= 1.4 =
* New min required wordpresss version: 2.7
* Added round corners for modern browsers on the app icons.
* Legit settings page now. No more hacking at the plugin file.

= 1.3.1 =
* Fixed bug where apps with zero ratings would display the ratings text incorrectly.

= 1.3 =
* Initial public release on Wordpress.org

== Upgrade Notice ==

None.

== Advanced Usage ==

You can also use the following shortcodes to pull only parts of the application's data. Please note that you must also pass the id to these shortcodes as well of the plugin won't know what application you want the data for.

* name
* icon
* icon_url
* version
* price
* release_notes
* description
* rating
* iphoness
* ipadss
* itunes_link

This plugin will cache the data from your application in the Wordpress database for 24 hours. Application icons and screenshots are cached on your server by default but if you'd like to pull those images directly from Apple's CDN servers (usually faster), change the value of IOS_APP_PAGE_CACHE_IMAGES to false
