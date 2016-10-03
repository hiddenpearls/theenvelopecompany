/* ================= START LOGIN / REGISTER ================= */
jQuery(document).ready(function($) {
    $(".open_login").click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(".login_form").show();
        $(".register_form").hide();
        $(".open_login").addClass('active');
        $(".open_register").removeClass('active');
        return false;
    });

    $(".open_register").click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(".register_form").show();
        $(".login_form").hide();
        $(".open_register").addClass('active');
        $(".open_login").removeClass('active');
        return false;
    });

    $("body").click(function() {
        $(".register_form").css({
            display: "none"
        });
        $(".login_form").css({
            display: "none"
        });
        $(".open_register, .open_login").removeClass('active');
    });
    
    $('.login_form.sing_in_form, .register_form.sing_in_form').click(function(event){
        event.stopPropagation();
    });
});
/* ================= END LOGIN / REGISTER ================= */






/* ================= START HOVER THEMES ================= */
jQuery(document).ready(function($) {
    $(".image_box").hover(function() {
        $(this).find(".image_box_hover").fadeIn(200);
    }, function() {
        $(this).find(".image_box_hover").fadeOut(200);
    });
});
/* ================= END HOVER THEMES ================= */






/* ================= SCROOL TOP ================= */
jQuery(document).ready(function($) {
    $('.backtotop').click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 1200, 'swing');
        return false;
    });
});


/* ================= IE fix ================= */
jQuery(document).ready(function($) {
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(obj, start) {
            for (var i = (start || 0), j = this.length; i < j; i++) {
                if (this[i] === obj) {
                    return i;
                }
            }
            return -1;
        };
    }
});

/* ================= START PLACE HOLDER ================= */
jQuery(document).ready(function($) {
    $('input[placeholder], textarea[placeholder]').placeholder();
});
/* ================= END PLACE HOLDER ================= */

/* =================Twitter============================ */
var load_twitter = function() {
    var linkify = function(text) {
        text = text.replace(/(https?:\/\/\S+)/gi, function(s) {
            return '<a href="' + s + '">' + s + '</a>';
        });
        text = text.replace(/(^|)@(\w+)/gi, function(s) {
            return '<a href="http://twitter.com/' + s + '">' + s + '</a>';
        });
        text = text.replace(/(^|)#(\w+)/gi, function(s) {
            return '<a href="http://search.twitter.com/search?q=' + s.replace(/#/, '%23') + '">' + s + '</a>';
        });
        return text;
    };
    jQuery('.tt_twitter').each(function() {
        var t = $(this);
        var t_date_obj = new Date();
        var t_loading = 'Loading tweets..'; //message to display before loading tweets
        var t_container = $('<ul>').addClass('twitter').append('<li>' + t_loading + '</li>');
        t.append(t_container);
        var t_user = t.attr('data-user');
        var t_posts = parseInt(t.attr('data-posts'), 10);
        $.getJSON("http://api.twitter.com/1/statuses/user_timeline/" + t_user + ".json?callback=?", function(t_tweets) {
            t_container.empty();
            for (var i = 0; i < t_posts && i < t_tweets.length; i++) {
                var t_date = Math.floor((t_date_obj.getTime() - Date.parse(t_tweets[i].created_at)) / 1000);
                var t_date_str;
                var t_date_seconds = t_date % 60;
                t_date = Math.floor(t_date / 60);
                var t_date_minutes = t_date % 60;
                if (t_date_minutes) {
                    t_date = Math.floor(t_date / 60);
                    var t_date_hours = t_date % 60;
                    if (t_date_hours) {
                        t_date = Math.floor(t_date / 60);
                        var t_date_days = t_date % 24;
                        if (t_date_days) {
                            t_date = Math.floor(t_date / 24);
                            var t_date_weeks = t_date % 7;
                            if (t_date_weeks)
                                t_date_str = t_date_weeks + ' week' + (1 == t_date_weeks ? '' : 's') + ' ago';
                            else
                                t_date_str = t_date_days + ' day' + (1 == t_date_days ? '' : 's') + ' ago';
                        }
                        else
                            t_date_str = t_date_hours + ' hour' + (1 == t_date_hours ? '' : 's') + ' ago';
                    }
                    else
                        t_date_str = t_date_minutes + ' minute' + (1 == t_date_minutes ? '' : 's') + ' ago';
                }
                else
                    t_date_str = t_date_seconds + ' second' + (1 == t_date_seconds ? '' : 's') + ' ago';
                var t_message =
                '<li' + (i + 1 == t_tweets.length ? ' class="last"' : '') + '>' +
                linkify(t_tweets[i].text) +
                '<span class="date">' +
                t_date_str +
                '</span>' +
                '</li>';
                t_container.append(t_message);
            }
            load_twitter_rotator();
        });
    });
};
var load_twitter_rotator = function() {
    var t_interval = 6000;   //time for tweet rotation in miliseconds
    var t_time = 1000;   //time for fade effect in miliseconds; NOTE: must be equal or lower then t_interval
    var t_active_class = 'active';
    var t_active_selector = '.' + t_active_class;
    var t_items = $('.tt_twitter ul li');
    var t_current = 0;
    var t_max = t_items.length;
    var t_height = 0;
    t_items.each(function() {
        t_height = Math.max(t_height, $(this).outerHeight(true));
    });
    $('.tt_twitter').css({
        height: t_height
    });
    t_items.filter(':first').addClass('active').css({
        opacity: 1
    });
    if (t_max) {
        t_max--;
        setInterval(function() {
            t_items.filter(':eq(' + t_current + ')').removeClass(t_active_class).stop().fadeOut('slow', function() {
                t_items.filter(':eq(' + t_current + ')').addClass(t_active_class).stop().fadeIn('slow');
            });
            t_current = (t_current < t_max) ? t_current + 1 : 0;
        }, t_interval);
    }
};
//load modules-------------

jQuery(document).ready(function($) {
    load_twitter();
    load_carousel();
});


//==============END TWITTER====================================

//=============Subscription =======================================
function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}

jQuery("#subscribe").submit(function(event) {
    //preventing from normal submition
    event.preventDefault();
    //validating email
    var sEmail = jQuery('#subscribe .subscribe_line').val();
    if (jQuery.trim(sEmail).length === 0) {
        jQuery('.subscribe_info').html('Email is missing').fadeIn('fast', function() {
            setTimeout(function() {
                $('.subscribe_info').fadeOut('slow');
            }, 3000);
        });
        event.preventDefault();//stops script execution
        return false;
    }
    //if valid email send info to php
    if (validateEmail(sEmail)) {
        jQuery.post("php/subscribes.php", jQuery("#subscribe").serialize(), function(result) {
            jQuery('.subscribe_info').html(result).fadeIn('fast', function() {
                setTimeout(function() {
                    $('.subscribe_info').fadeOut('slow');
                }, 3000);
            });
        });
    } else {
        jQuery('.subscribe_info').html('Invalid Email').fadeIn('fast', function() {
            setTimeout(function() {
                $('.subscribe_info').fadeOut('slow');
            }, 3000);
        });
        event.preventDefault();
        return false;
    }
});

/* ================= CONTACTS FORM ================= */

jQuery('.contact_form').each(function() {
    var t = jQuery(this);
    var t_result = jQuery('.contact_send');
    var t_result_init_val = t_result.val();
    var validate_email = function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };
    t.submit(function(event) {
        //t_result.val('');
        event.preventDefault();
        var t_values = {};
        var t_values_items = t.find('input[name],textarea[name]');
        t_values_items.each(function() {
            t_values[this.name] = jQuery(this).val();
        });
        if (t_values['name'] === '' || t_values['email'] === '' || t_values['message'] === '') {
            t_result.val('Please fill in all the required fields.');
        } else
        if (!validate_email(t_values['email']))
            t_result.val('Please provide a valid e-mail.');
        else
            jQuery.post("php/contacts.php", t.serialize(), function(result) {
                t_result.val(result);
            });
        setTimeout(function() {
            t_result.val(t_result_init_val);
        }, 3000);
    });

});

/* ================= Project FORM ================= */

jQuery('.project_form').each(function() {
    var t = jQuery(this);
    var t_result = jQuery('.project_send');
    var t_result_init_val = t_result.val();
    var validate_email = function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };
    t.submit(function(event) {
        //t_result.val('');
        event.preventDefault();
        var t_values = {};
        var t_values_items = t.find('input[name],textarea[name]');
        t_values_items.each(function() {
            t_values[this.name] = jQuery(this).val();
        });
        if (t_values['name'] === '' || t_values['email'] === '' || t_values['description'] === '') {
            t_result.val('Fill in all fields.');
            setTimeout(function() {
                t_result.val(t_result_init_val);
            }, 3000);
        } else
        if (!validate_email(t_values['email'])) {
            t_result.val('Please provide a valid e-mail.');
            setTimeout(function() {
                t_result.val(t_result_init_val);
            }, 3000);
        }
        else {
            t_result.val('Sending project details...');
            jQuery.post("php/project.php", t.serialize(), function(result) {
                t_result.val(result);
                setTimeout(function() {
                    t_result.val(t_result_init_val);
                }, 3000);
            });
        }
    });

});

/* ================= CAROUSEL ================= */
var load_carousel = function() {
    jQuery('.slide_content').each(function() {
        $=jQuery;
        var t_time = 500;   //time for animation effect
        var t = $(this);
        jQuery.browser = {};
        jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
        jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
        jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
        jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
        var t_scroll_width = $.browser.mozilla || $.browser.opera || $.browser.msie ? scrollbarWidth() : 0;
        var t_prev = t.find('.slide_nav_back');
        var t_next = t.find('.slide_nav_next');
        var t_items = t.find('.slide_content_full>div').not('.clear');
        var t_items_n = t_items.length;
        var t_items_container_visible_width;
        var t_items_width;
        var t_visible;
        var t_index;
        var t_index_max;
        var t_prev_function;
        var t_next_function;
        var t_pre_process_specific;
        var t_pre_process = function() {
            t_items.attr('style', 'overflow:hidden; padding:5px 0px;');
            t_index = 0;
            t_items_container_visible_width = t.find('.slide_content_show').width();
            t_items_width = t_items.outerWidth(true);
            t_visible = Math.round(t_items_container_visible_width / t_items_width);
            t_index_max = t_items.length - Math.min(t_items.length, t_visible);
            t_pre_process_specific();
        };
        var t_img = t.find('img');
        var t_img_n = t_img.length;
        var t_img_loaded = function() {
            t_prev.click(function(event) {
                t_prev_function();
                if (event.preventDefault !== undefined)
                    event.preventDefault();
                else
                    return false;
            });
            t_next.click(function(event) {
                t_next_function();
                if (event.preventDefault !== undefined)
                    event.preventDefault();
                else
                    return false;
            });
            var t_w = $(window);
            var t_w_x = -1;
            var t_d = $(document);
            var t_w_width_get = function() {
                var t_w_width = t_w.width();
                var t_w_height = t_w.height();
                var t_d_height = t_d.height();
                if (t_w_height < t_d_height)
                    t_w_width += t_scroll_width;
                return t_w_width;
            };
            var t_w_resize_function = function() {
                var t_w_width = t_w_width_get();
                if (t_w_width >= 768) {
                    if (t_w_x !== 1) {
                        t_w_x = 1;

                        t_pre_process_specific = function() {
                            if (t_index_max)
                                t_items.filter(':gt(' + String(t_visible - 1) + ')').each(function() {
                                    var t_items_hidden = $(this);
                                    t_items_hidden.css({
                                        marginTop: t_items_hidden.height() / 2
                                    }).css({
                                        display: 'none'
                                    });
                                });
                        };
                        t_pre_process();
                        t_prev_function = function() {
                            if (t_index > 0) {
                                t_index--;
                                var t_items_current = t_items.filter(':eq(' + t_index + ')');
                                t_items_current.stop(true, true).animate({
                                    marginTop: 0,
                                    height: 'toggle',
                                    width: 'toggle',
                                    marginLeft: 'toggle'
                                }, {
                                    queue: false,
                                    duration: t_time,
                                    easing: 'swing'
                                });
                                var t_index_opposite = t_index + t_visible;
                                var t_items_copposite = t_items.filter(':eq(' + t_index_opposite + ')');
                                t_items_copposite.stop(true, true).animate({
                                    marginTop: t_items_copposite.height() / 2,
                                    height: 'toggle',
                                    width: 'toggle',
                                    marginLeft: 'toggle'
                                }, {
                                    queue: false,
                                    duration: t_time,
                                    easing: 'swing'
                                });
                            }
                        };
                        t_next_function = function() {
                            if (t_index < t_index_max) {
                                var t_index_opposite = t_index + t_visible;
                                var t_items_copposite = t_items.filter(':eq(' + t_index_opposite + ')');
                                t_items_copposite.stop(true, true).animate({
                                    marginTop: 0,
                                    height: 'toggle',
                                    width: 'toggle',
                                    marginLeft: 'toggle'
                                }, {
                                    queue: false,
                                    duration: t_time,
                                    easing: 'swing'
                                });
                                var t_items_current = t_items.filter(':eq(' + t_index + ')');
                                t_items_current.stop(true, true).animate({
                                    marginTop: t_items_current.height() / 2,
                                    height: 'toggle',
                                    width: 'toggle',
                                    marginLeft: 'toggle'
                                }, {
                                    queue: false,
                                    duration: t_time,
                                    easing: 'swing'
                                });
                                t_index++;
                            }
                        };
                    }
                } else {
                    if (t_w_x !== 2) {
                        t_w_x = 2;

                        t_pre_process_specific = function() {
                            t_visible = 1;
                            t_index_max = t_items_n - 1;
                            if (t_index_max)
                                t_items.filter(':gt(' + String(t_visible - 1) + ')').css({
                                    display: 'none'
                                });
                        };
                        t_pre_process();
                        t_prev_function = function() {
                            if (t_index > 0) {
                                t_items.filter(':eq(' + t_index + ')').css({
                                    display: 'none'
                                });
                                t_index--;
                                t_items.filter(':eq(' + t_index + ')').css({
                                    display: 'block'
                                });
                            }
                        };
                        t_next_function = function() {
                            if (t_index < t_index_max) {
                                t_items.filter(':eq(' + t_index + ')').css({
                                    display: 'none'
                                });
                                t_index++;
                                t_items.filter(':eq(' + t_index + ')').css({
                                    display: 'block'
                                });
                            }
                        };
                    }
                }
            };
            t_w.resize(t_w_resize_function);
            t_w_resize_function();
        };
        var t_img_ready = [];
        var t_img_ready_complete = false;
        var t_img_ready_all = function() {
            var i = 0;
            for (i = 0; i < t_img_n && (t_img_ready[i] === 1 || t_img[i].complete); i++)
            ;
            return i === t_img_n;
        };
        var t_img_ready_check = function() {
            var t_img_ready_complete_temp = t_img_ready_all();
            if (!t_img_ready_complete && t_img_ready_complete_temp) {
                t_img_ready_complete = t_img_ready_complete_temp;
                t_img_loaded();
            }
        };
        t_img.each(function(index) {
            t_img_ready[index] = 0;
        });
        if ($.browser.msie) {
            t_img.each(function() {
                this.src = this.src;
            });
        }
        t_img.load(function(index) {
            t_img_ready[index] = 1;
            t_img_ready_check();
        });
        t_img_ready_check();
        t_prev.mousedown(function() {
            return false;
        });
        t_next.mousedown(function() {
            return false;
        });
    });
};
function scrollbarWidth() {
    var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
    // Append our div, do our calculation and then remove it 
    $('body').append(div);
    var w1 = $('div', div).innerWidth();
    div.css('overflow-y', 'scroll');
    var w2 = $('div', div).innerWidth();
    $(div).remove();
    return (w1 - w2);
}

//==============Project slider image changer=========================
jQuery('.project_image_more .slide_content_full .hover').on('click', function() {
    var src = $(this).find('img').attr('src');
    $('.project .project_image img').attr('src', src);
});

//==============Filters====================================
jQuery('.image_grid_filter a').on('click', function(event) {
    event.preventDefault();
    var filter = $(this).attr('data-filter');
    $(this).toggleClass('active');
    if (filter === 'all') {
        if ($(this).hasClass('active'))
            $('.image_grid_filter a,.image_box').addClass('active');
        else
            $('.image_grid_filter a,.image_box').removeClass('active');
    } else {
        $('.image_box[data-filter-item=' + filter + ']').toggleClass('active');
        $('.image_grid_filter a[data-filter=all]').removeClass('active');
    }
});

//============Team Members effects============================
jQuery('.team_box_loop li[data-team]').hover(function() {
    $('.team_box_loop li[data-team] .team_box').removeClass('active');
    $(this).children('.team_box').addClass('active');
    var member = $(this).attr('data-team');
    console.log(member);
    $('div[data-team-description]').removeClass('active');
    $('div[data-team-description=' + member + ']').addClass('active');
});

/* ================= START MENU ================= */
jQuery(document).ready(function ($) {
    "use strict";
    $(".menu-responsive").click(function (e) {
        $(".header .top_menu .menu>ul").css({display: "block"});
        e.stopPropagation();
        if (e.preventDefault)
            e.preventDefault();
        return false;
    });
    $("body").click(function () {
        $(".header .top_menu .menu>ul").css({display: "none"});
    });
    $(".category-responsive").click(function (e) {
        $(".category_menu>ul").css({display: "block"});
        e.stopPropagation();
        if (e.preventDefault)
            e.preventDefault();
        return false;
    });
    $("body").click(function () {
        $(".category_menu>ul").css({display: "none"});
    });
});
/* ================= END MENU ================= */



jQuery(document).ready(function($){
    $('.item_image .item_view').click(function(){
        $('#item_popup_qw').show();
        //load product details into quick view container
        request_product_qw($(this).data('product-id'), update_quick_view);
    });
    
    $('#item_popup_qw .pop_up_close, #item_popup_qw').click(function(){
        $('#item_popup_qw').hide()
    });
    
    $('#item_popup_qw .popup_item').click(function(event){
        event.stopPropagation();
    });
    
    $('.orderby .orderby_li').click(function(event){
        $form=$(this).parents('form');
        $form.find('input[name="orderby"]').val($(this).data('value'));
        $form.submit();
        return false;
    });
    
    $('.orderby a').click(function(){
        event.preventDefault()
    });
        
})

var update_quick_view=function (product) {
    $=jQuery;
    $('#qw_pop_image').html(product.image);
    $('#qw_pop_more_details').attr('href',product.url);
    $('#qw_pop_h3 a').attr('href',product.url).html(product.title);
    $('#qw_pop_rating').html(product.rating_html);
    $('#qw_pop_h4').html(product.short_description);
    $('#qw_pop_excerpt').html(product.excerpt);
    $('#qw_pop_atc_url').attr('href',product.url);
    $('#qw_pop_price').html(product.price);
    $('#qw_pop_attributes').html(product.attributes);
}

function request_product_qw(product_id, func_cb) {
    jQuery.ajax({
        url: ajaxurl,
        data:{
            'action':'get_product_qw',
            'fn':'hudson_get_product_qw',
            'product_id':product_id
        },
        type: 'POST',
        dataType: 'JSON',
        success:function(data){
            func_cb(data.product);
        },
        error: function(errorThrown){
            alert('error');
            console.log(errorThrown);
        }
    });
}

(function ( $ ) {
    
    //Product slider    

    $.fn.dragos_slider = function(options) {
        
        defaults={
            'show_most': 3,
            'elm_cnt_selector':'.collection_selection'
        };
        
        // Build main options before element iteration.
        var opts = $.extend( {}, defaults, options );
        return this.each(function() {
            var $this = $( this );
            var total_elms=0;
            $this.find(opts.elm_cnt_selector).each(function(i){
                $(this).data('index',i);
                total_elms++;
            });
            //load first preview
            $this.find('.product_images .large_preview').attr('src',$this.find(opts.elm_cnt_selector).first().data('fullsrc')).parent('.zoom').attr('href',$this.find(opts.elm_cnt_selector).first().data('fullsrc'));
            
            $this.find('.collection_box_mini .collection_right').click(function(){
                if($this.find(opts.elm_cnt_selector+':visible:first').data('index')+opts.show_most<=total_elms-1) {
                    tmp=$this.find(opts.elm_cnt_selector+':visible:first');
                    $this.find(opts.elm_cnt_selector).hide();
                    tmp.nextAll(opts.elm_cnt_selector).slice(0,opts.show_most).show();
                }
            });
            $this.find('.collection_box_mini .collection_left').click(function(){
                if($this.find(opts.elm_cnt_selector+':visible:first').data('index')-1>=0) {
                    tmp=$this.find(opts.elm_cnt_selector+':visible:first').prev(opts.elm_cnt_selector);
                    $this.find(opts.elm_cnt_selector).hide();
                    tmp.nextAll(opts.elm_cnt_selector).slice(0,opts.show_most-1).show();
                    tmp.show();
                }
            })
            $this.find(opts.elm_cnt_selector).click(function(){
                $this.find('.product_images .large_preview').attr('src',$(this).data('fullsrc')).attr('data-zoom-image',$(this).data('fullsrc')).parent('.zoom').attr('href',$(this).data('fullsrc'));
            })
        //...
 
        });
 
    };   
 
}( jQuery ));

(function ( $ ) {
    
    //Product slider    

    $.fn.dragos_slider_lr = function(options) {
        
        defaults={
            'show_most': 4,
            'elm_cnt_selector':'.span3'
        };
        
        // Build main options before element iteration.
        var opts = $.extend( {}, defaults, options );
        return this.each(function() {
            var $this = $( this );
            var total_elms=0;
            $this.find(opts.elm_cnt_selector).each(function(i){
                $(this).data('index',i);
                total_elms++;
            });
            
            $this.find('.navigation_arrows .right_arrow').click(function(){
                if($this.find(opts.elm_cnt_selector+':visible:first').data('index')+opts.show_most<=total_elms-1) {
                    tmp=$this.find(opts.elm_cnt_selector+':visible:first');
                    $this.find(opts.elm_cnt_selector).hide();
                    tmp.nextAll(opts.elm_cnt_selector).slice(0,opts.show_most).show();
                }
            });
            $this.find('.navigation_arrows .left_arrow').click(function(){
                if($this.find(opts.elm_cnt_selector+':visible:first').data('index')-1>=0) {
                    tmp=$this.find(opts.elm_cnt_selector+':visible:first').prev(opts.elm_cnt_selector);
                    $this.find(opts.elm_cnt_selector).hide();
                    tmp.nextAll(opts.elm_cnt_selector).slice(0,opts.show_most-1).show();
                    tmp.show();
                }
            });
 
        });
 
    };   
 
}( jQuery ));

(function($){
    $('.prod_slider').dragos_slider();    
    $('.collection_slider').dragos_slider({
        'show_most':6
    });
    $('.slider_lr').dragos_slider_lr({
        'elm_cnt_selector':'.span3'
    });
    if($.fn.magnificPopup)
        $('.collection .zoom').magnificPopup({
            type:'image'
        });
})(jQuery);

// Affix cart floating

(function($){
    //set affix menu
    if($('.cart_affix').length>0)
        setTimeout(function () {
            $obj=$('.cart_affix');
            var offset=$obj.offset();
            $obj.affix({
                offset: {
                    top: offset.top-20
                }
            });
        }, 100);
        
    $(document).on('added_to_cart','body',function(event, fragments){
        $('.dynamic_cart_contents_count').html(fragments.contents_count);
        $('.dynamic_cart_total').html(fragments.total);
    });
    
    $('.similar_item_img').tooltip({
        placement:'top'
    });

    //FIX FOR DIFFERENT PRODUCTS HEIGHT
    var productHelper = function () {
        var heightInit = -1;

        $('.type-product').each(function (i, obj) {

            if ( $(this).height() > heightInit && !$(this).hasClass('single-big-item')) {
                heightInit = $(this).height();
                console.log(heightInit);
            }
        });
        console.log(heightInit);
        $('.type-product').each(function(){
            if(!$(this).hasClass('single-big-item'))
                $(this).height(heightInit);
        });

        var ItemsheightInit = -1;

        $('.item h1').each(function (i, obj) {

            if ( $(this).height() > ItemsheightInit) {
                ItemsheightInit = $(this).height();
            }
        });
        $('.item h1').height(ItemsheightInit);
    };

    $(window).load(function(){
        var maxHeight = -1;

        productHelper();
        $('.items .item').each(function() {
            if(!$(this).hasClass('single-big-item'))
                maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
        });

        $('.slider_lr .items,.slider_lr .items .item').each(function() {
            if(!$(this).hasClass('single-big-item'))
                $(this).height(maxHeight);
        });
    });
    $(document).ready(function(){
        //Subscription-------------
        $('#tt_subscribe_form').tt_subscription({
            success: function(result, config, form) {
                $(config.result_container_selector).show()
            },
            error: function(error, config) {
                $(config.result_container_selector).show()
            },
            required: function(config) {
                $(config.result_container_selector).show()
            },
            invalid_email: function(config) {
                $(config.result_container_selector).show()
            }
        });
    });

}(jQuery));