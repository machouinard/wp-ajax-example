"use strict";

console.log( 'This should only run on a post or page with a slug of "ajax-example' );

// Grab entry-content so we can append an element
var entry        = document.getElementsByClassName( 'entry-content' )[ 0 ];
// Create h2 element that we'll append to entry-content
var exEl        = document.createElement( 'h2' );
// Give h2 element an ID
exEl.id         = 'ex-id';
// Give h2 some temporary text
exEl.innerText  = 'This text is temporary';
// Create button for, you know, clicking
var button       = document.createElement( 'button' );
// Set button text
button.innerText = 'Click';
// Append h2 to entry-content
entry.appendChild( exEl );
// Append button to entry-content
entry.appendChild( button );
// Add event listener to button to trigger Ajax function
button.addEventListener( 'click', function( e ) {
	// If we were handling a form with ajax and this was a submit button, we'd need to prevent
	// the button's default behavior (form submission)
	//e.preventDefault();
	// Call our Ajax function
	ajaxGo();
} );

/**
 * AJAX function
 */
function ajaxGo() {
	jQuery.ajax( {
		url: opts.ajaxurl,
		data: {
			'action': 'get_meta_time',// the action we registered
			'post_id': opts.postID,// the localized post ID
			'security': opts.security// the localized nonce
		},
		success( res ) {
			if ( res.success ) {
				// res.data.time was sent from our PHP function `get_meta_time()`. Its value is
				// post meta.
				if ( res.data.hasOwnProperty( 'time' ) ) {
					document.getElementById( 'ex-id' ).innerText = res.data.time;
				}
			} else {
				// res.data was sent from our PHP function `get_meta_time()` if no meta or post ID
				// was found.
				if ( res.data.hasOwnProperty( 'error' ) ) {
					document.getElementById( 'ex-id' ).innerText = res.data.error;
				}
			}
		},
		// If there was an error in the actual request...
		error( err ) {
			console.log( 'err', err );
		}
	} );
}



