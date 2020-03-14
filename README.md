# WP Ajax Example #

A simple demonstration of AJAX in WordPress

One class, one JavaScript file.

Comments should be enough to figure out what's happening.

## Instructions ##

* Create a page or post making sure the slug is 'ajax-example'
* When that page/post loads you'll see some temporary text and a button.
* When the button is clicked, we check for some post meta (a time string)
* The post meta is deleted when the page loads so the first button click will result in an error message being displayed.
* Post meta is upated with the current time after button click.
* Subsequent clicks will update the text with the latest time that was saved to post meta.
* A nonce is included - Changing the "secret-string" during localization or in `check_ajax_referer()` will force an error.

