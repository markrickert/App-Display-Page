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

# Advanced usage:

You can also use the following shortcodes to pull only _parts_ of the
application's data. Please note that you must also pass the ```id``` to 
these shortcodes as well of the plugin won't know what application you
want the data for.

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

This plugin will cache the data from your application
in the Wordpress database for 24 hours. The application
icons and screenshots are cached on your server by default
but if you'd like to pull those images directly from Apple's
CDN servers (usually faster), change the value of ```IOS_APP_PAGE_CACHE_IMAGES``` 
to ```false```