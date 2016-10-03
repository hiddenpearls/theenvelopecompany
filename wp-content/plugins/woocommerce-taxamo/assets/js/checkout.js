jQuery( function ( $ ) {
    "use strict";

    // parse EU countries
    var eu_countries = jQuery.parseJSON( wc_taxamo.eu_countries );

    jQuery( '#billing_country' ).change( function () {
        jQuery( '.location_confirmation input' ).attr( 'checked', false );
    } );

    jQuery( document.body ).bind( 'update_checkout', function () {
        jQuery( '.location_confirmation input' ).unbind( 'change' );
        jQuery( '.taxamo-vat-number input' ).unbind( 'blur' );
    } );

    jQuery( document.body ).bind( 'updated_checkout', function () {

        jQuery( '.location_confirmation input' ).on( 'change', function () {
            jQuery( 'body' ).trigger( 'update_checkout' );
        } );

        jQuery( '.taxamo-vat-number input' ).on( 'blur', function () {
            jQuery( this ).val( jQuery( this ).val().toString().replace( /\-|\s/ig, '' ) );
            jQuery( 'body' ).trigger( 'update_checkout' );
            return false;
        } );

        // Dynamically display or hide eu vat number field
        if ( jQuery.inArray( jQuery( '#billing_country option:selected' ).val(), eu_countries ) !== -1 ) {
            // display EU number field
            jQuery( "#vat_number_field" ).show();
        } else {
            // hide EU number field
            jQuery( "#vat_number_field" ).hide();
        }

    } );


} );