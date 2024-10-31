=== PagedPWA ===
Tags: progressive web app,firebase,smart page designer,use widget builder for homescreen,push notifications
Requires at least: 5.5.12
Tested up to: 6.5.2
Stable tag: 2.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://scriptbyyou.com
Contributors: philiscriptbyyou

Turns all wordpress websites into a Progressive Web App (PWA) with a few clicks and drags but no code.
Uses drag and drop pages for your own pages.

== Description ==

Allows smart pages to be designed within your website using this plugin.
* Drag and drop your own add to home screen 'page' (or page container)
* Drag and drop your own subscribe to push messages 'page' (or page container)
* Drag and drop your own receive push messages page (real page for your customer to interact with)
* Supports offline pages
* Pages may be cached
* Push messages may be received by your customer on your customised page
* Free version allows sending of push messages in your google console
* Pro version allows you to create(no coding) your own Google Firebase app to send messages
	(Privacy and terms here: scriptbyyou.com/myprivacy)
* UTM tracking showing data posts from requests made
* Multi site support
* Cacheing strategies
* Service Worker Version, allowing your users to keep up date with any changes to your website


== Highlight ==

Seamless integration with scriptbyyou.com, allowing you to design pages immediately on your wordpress website.
You can design, change visuals (skin) and run all within your WP site.

Smart Page Designer uses normal drag and drop techniques.
So you will never write a single line of code.
The code is maintained and managed by the system itself, nothing for you to worry about.

The layouts are not fixed but are simply based on want you want.
You specify the number of columns for the row and then just drag the widgets into the column.
The column or cell can contain as many widgets as you like (the order can just be dragged).
Any kind of widget.

= Live Editing. =

The designer is so advanced the page can be 'run' as you design with events firing immediately
(if you want). This is just 1 mode of operation there are many to choose from.
WordPress 'Page preview' will always show the page as it runs normally.

= It's free, and always will be. =

Smart Page Designer is our commitment to the democratization of content creation. 
Supported always and always will be free. 
Web support always there for free.

= Actively Developed =

PagedPWA is actively being developed all the time, work never stops for our developers.

== Open Issue ==
None.

== Demo ==
1. https://scriptbyyou.com
	has a playground to demo the drag and drop designer.

== Download ==
1. WordPress Plugins Libraries: https://wordpress.org/plugins/pwaappy/


== Installation ==
1. Upload the plugin files to the '/wp-content/plugins/pwaappy' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Documentation ==

[Documentation](https://scriptbyyou.com) is a blog site showing posts that may be commented on.

== Frequently Asked Questions ==

= How to send Push Notifications? =
This plugin is compatible with Firebase: https://firebase.google.com/,just sign in to your Google Firebase console and go to the
compose page. eg (https://console.firebase.google.com/project/MYPROJECT/notification/compose).
Google firebase privacy: https://firebase.google.com/support/privacy

Specify the device id shown on the subscriptions posts admin page. A Subscription post will be created when a customer subscribes to push messages.
Choose the post that you want and there you will find a meta box which will allow you to copy the id to the clipboard. 
Paste this in on Google's compose page, after specifying the title and main text.
The page the customer will receive this is named 'notifyappy'. You can change the design of this.

= The Pro version allows you to copy and customise an app already developed for admins to send push messages from you own app.
Go to scriptbyyou.com/firebaseapps for an explanation.
Download here: scriptbyyou.com/pwaappypro
This app allows you to specify any page your customer will receive the message on.

When sending push messages it is now the policy of some governments, because of privacy issues to send them directly from your 
own account you have signed up to. That is why for free you can use Google console, and for a small monthly(11.99GBP) you can develop 
your own Google Firebase app hosted on Google Cloud with just a drag and drop.


== Changelog ==

= 2.1.5 =
	changes to be sure to be sure
= 2.1.4 =
	changes for worry-free security
= 2.1.3 =
	changes for badge api
= 2.1.2 =
	changes for wordpress.org playground, no fighting now
= 2.1.1 =
	Smooth now and stable
= 1.2 =
    drag and drop everywhere!

= 1.1 =
    options,properties,events etc

= 1.0 =
Nov, 2019 – Version 1.0
* Version 1.0 Initial Release

== Support ==
Author: philiscriptbyyou
Web: https://scriptbyyou.com
Email: phil@scriptbyyou.com

== Privacy (at a glance and SaaS) ==
Your privacy is respected to a maximum by us, we only collect details that are needed to make the system work.
And to comply with government regulations with regard to messaging verification and checking of private keys.
Rest assured no third parties are passed your data.

This plugin is providing software as a service (SaaS) to allow your Wordpress website to receive push messages.
Additionally the free version allows sending of push message using your Google console account, and the Pro version allows an additional service for creation of an app on ScriptByYou.com and Google Firebase allowing you to use your own app to send push messagging.
Both free and pro versions have a service with ScriptByYou.com to allow creation of your own pages for receiving the messages. This is done within the plugin WP admin pages itself live with an internet connection needed.

Full details are here:
	(Privacy and terms here: scriptbyyou.com/myprivacy)
Google firebase privacy:
https://firebase.google.com/support/privacy


You’re done. Enjoy.

