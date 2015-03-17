*** THIS PLUGIN IS NO LONGER SUPPORTED OR MAINTAINED ***

# App Display Page

This is a simple Wordpress plugin that you can 
modify to fit your own needs. Use your iOS's 
application id and it will spit out some basic HTML 
that you can then apply your own styling to.

# Why?

I wanted to make sure that I had accurate
descriptions and current pricing information
for all my apps wherever I display this data
on my personal websites. This was the natural
thing to do.

# How to use:

* Clone or download the repos.
* Upload the folder 'app-display-page' to your Wordpress installation's plugins folder.
* Log into Wordpress and activate the plugin.
* In any post or page, insert the following shortcode: ```[ios_app id="1234"]``` (where "1234" is your application's App Store ID).
* You can also specify a download link like this: ```[ios_app id="1234" download_url="http://www.yourlinktrackerurl.com"]```
* Save the page and check out the result!
* You can also enter your iTunes Affilliate code (and an optional campaign ID) to automatically convert all links over to the affiliate referral.

# Advanced usage:

You can also use the following shortcodes to pull only _parts_ of the
application's data. Please note that you must also pass the ```id``` to 
these shortcodes as well of the plugin won't know what application you
want the data for.

* ios_app_name
* ios_app_icon
* ios_app_icon_url
* ios_app_version
* ios_app_price
* ios_app_release_notes
* ios_app_description
* ios_app_rating
* ios_app_iphoness
* ios_app_ipadss
* ios_app_itunes_link

---
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/markrickert/app-display-page/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
