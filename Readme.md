# App Display Page

This is a simple PHP script paired with a 
Wordpress plugin that you can modify to fit
your own needs. Use your iOS's application id
and it will spit out some basic HTML that you can
then apply your own styling to.

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
* In any post or page, insert the following shortcode: \[ios-app id="1234"\] (where "1234" is your application's App Store ID).
* You can also specify a download link like this: \[ios-app id="1234" download_url="http://www.yourlinktrackerurl.com"]
* Save the page and check out the result!

This plugin will cache the data from your application
in the Wordpress database for 24 hours, however, the
application icon and screenshots are pulled directly
from apple's CDN servers.