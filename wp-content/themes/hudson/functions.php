<?php
/* * ******************************************************************************************** */
/*  Define Constants */
/* * ******************************************************************************************** */
define('TEMPLATEURI', get_template_directory_uri());
define('IMAGES', TEMPLATEURI . '/images');

// Transition variable

global $tesla_trans_var;
$tesla_trans_var = new ArrayObject;

// GLobal variable that keeps custom shortcode registry

$tesla_class_registry = new ArrayObject;

/* * ******************************************************************************************** */
/*  Tesla Framework */
/* * ******************************************************************************************** */
require_once(TEMPLATEPATH . '/tesla_framework/tesla.php');

/* * ******************************************************************************************** */
/* Load JS and CSS Files */
/* * ******************************************************************************************** */
TT_ENQUEUE::$main_css = 'style.css';
TT_ENQUEUE::add_js(array('jquery-ui-core','jquery-ui-slider'));     //takes array also
TT_ENQUEUE::$base_gfonts = array('://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400italic,700italic,400,600,700');
TT_ENQUEUE::$gfont_changer = array(
        _go('logo_text_font')
    );

function hudson_custom_css() {
    $colopickers_css = '';
    if (_go('site_color')) : 
        $colopickers_css .= '
            /* ====== YELLOW ====== */
            .yellow,
            .menu_line,
            .content .filter h3,
            .cart_top,
            .content .item .item_price,
            .addresses address,
            .customer_details,
            .order_details,
            #order_review,
            .woocommerce-page table.shop_table.my_account_orders,
            .fixed_cart {
                background: ' . _go("site_color") . ';
            }
            .sidebar ul li a:hover {
                color: ' . _go('site_color') . ';
            }
            .comments,
            .sidebar ul li:hover,
            .h_contact_form {
                border: 3px solid ' . _go('site_color') . ';
            }
            .content .item .item_image:hover {
                border: 3px solid ' . _go('site_color') . ';
            }';
    endif;
    if (_go('site_color_2')) :
        $colopickers_css .= '
            /* ====== GREEN ====== */
            .header .top_line ul li.open_login:hover,
            .header .top_line ul li.open_register:hover,
            .header .top_line ul li.open_login.active,
            .collection .collection_box a:hover,
            .h_blog .hentry h1 a:hover,
            .footer .copyright a:hover,
            .content .orderby ul li a:hover,
            p a,
            .popup_cover .popup_item .pop_up_link:hover,
            .h_blog .hentry .post_info li a:hover,
            .popup_cover .popup_item .pop_up_link:hover,
            .header .top_line ul li.open_register.active {
                color: ' . _go("site_color_2") .';
            }
            .header .top_menu .menu li.current-menu-item > a,
            .header .top_menu .menu li.current-page-item > a,
            .header .top_menu .menu ul li a:hover,
            .header .top_menu .menu ul li a.active {
                border: 3px solid ' . _go("site_color_2") .';
                color: ' . _go("site_color_2") .';
                box-shadow: 0px 2px 3px ' . _go("site_color_2") .';
            }
            .about_members .about_social ul li a:hover,
            .woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
            .woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle,
            .content .item .item_image .item_view:hover,
            .content .info_line {
                background: ' . _go("site_color_2") .';
            }
            .tagcloud a:hover {
                color: ' . _go("site_color_2") .';
                border: 1px solid ' . _go("site_color_2") .';
            }
            .search_line_all_form .search_line_all_button:hover {
                border: 1px solid ' . _go("site_color_2") .'!important;
                background: ' . _go("site_color_2") . ' url("'.IMAGES.'/search_icon.png")no-repeat 15px -53px !important;
            }
            .content .item_add_cart:hover {
                background: ' . _go("site_color_2") .' url("'.IMAGES.'/cart.png") no-repeat 9px -81px !important;
                box-shadow: 0px 1px 2px ' . _go("site_color_2") .'!important;
            }
            .footer .subscribe_form .newsletter_send:hover {
                box-shadow: 0px 2px 2px ' . _go("site_color_2") .';
                background: ' . _go("site_color_2") .';
            }
            .h_pagination ul li .current {
                border: 1px solid ' . _go("site_color_2") .';
                color: ' . _go("site_color_2") .';
            }
            .woocommerce .widget_price_filter .ui-slider .ui-slider-range,
            .woocommerce-page .widget_price_filter .ui-slider .ui-slider-range {
                background-color: ' . _go("site_color_2") .';
            }
            .cart button,
            .woocommerce-cart .entry-content .woocommerce .button,
            .place-order .button, .woocommerce p input.button,
            .checkout_coupon .button,
            .woocommerce form.login .button,
            .woocommerce-page table.shop_table.my_account_orders .order-actions a,
            #pp_full_res .pp_inline .form-submit input,
            .woocommerce-tabs .panel .button,
            .tt_h_cart .shipping_calculator .button,
            .popup_cover .popup_item .pop_up_add,
            .tt_h_cart .actions .button {
                color: rgba(0,0,0, 0.3) !important;
                background: ' . _go("site_color_2") .'!important;
            }';
    endif;
    if (_go('site_color_3')) :
        $colopickers_css .= '
            /* ====== BROWN ====== */
            .rs_mainslider .rs_mainslider_dots_container ul.rs_mainslider_dots,
            .rs_mainslider .rs_mainslider_dots_container ul.rs_mainslider_dots li.rs_mainslider_dots_active,
            .content .item .item_image .item_view,
            .sidebar .side_element_title,
            .content .rubric_b,
            .content #searchform input[type="submit"],
            .h_button,
            .content #searchform input[type="submit"],
            .content .rubric_b,
            .header .top_line {
                background: ' . _go("site_color_3") .';
            }
            .collection .collection_box a,
            body,
            .content .item h1 a,
            .fixed_cart .fixed_interior h1,
            .fixed_cart .fixed_interior p span,
            .short_description h1,
            .h_blog .hentry h1 a,
            .content .item .item_price,
            .content .item .item_image .item_view:hover,
            .cart button:hover,
            .woocommerce p input.button:hover,
            .woocommerce-page table.shop_table.my_account_orders .order-actions a:hover,
            .place-order .button:hover,
            .woocommerce-cart .entry-content .woocommerce .button:hover,
            .checkout_coupon .button:hover,
            .woocommerce form.login .button:hover,
            #pp_full_res .pp_inline .form-submit input:hover,
            .woocommerce-tabs .panel .button:hover,
            .tt_h_cart .shipping_calculator .button:hover,
            .tt_h_cart .actions .button:hover,
            .h_title,
            .content .options,
            .tt_h_cart .actions .button.checkout-button,
            .search_line_all_form .search_line_all,
            .content .path,
            .entry-content table.shop_table.my_account_orders tr,
            .content .headline {
                color: ' . _go("site_color_3") .';
            }
            .header .cart_search .search_form .search_submit {
                background: ' . _go("site_color_3") .' url("'.IMAGES.'/search_but.png") no-repeat 8px 8px;
            }
            .content .partners {
                border-top: 5px solid ' . _go("site_color_3") .';
            }
            .footer {
                background: ' . _go("site_color_3") .';
            }';
    endif;
    // SITE COLOR CHANGER END -->
    $custom_css = _go('custom_css') ? _go('custom_css') : '';
    $background_color = _go('bg_color') ? "body{background-color: "._go('bg_color')."}" : '';
    
    wp_add_inline_style('tt-main-style', $background_color);
    wp_add_inline_style('tt-main-style', $colopickers_css);
    wp_add_inline_style('tt-main-style', $custom_css);
}

add_action('wp_enqueue_scripts', 'hudson_custom_css', 99);

foreach (glob(TEMPLATEPATH . "/theme_includes/*.php") as $filename) {
    include_once($filename);
}

add_action('woocommerce_before_cart', 'hudson_cart_before_wrap');

function hudson_cart_before_wrap() {
    echo '<div class="tt_h_cart">';
}

add_action('woocommerce_after_cart', 'hudson_cart_after_wrap');

function hudson_cart_after_wrap() {
    echo '</div>';
}

/*add_filter('add_to_cart_fragments', 'hudson_add_to_cart_json');
function hudson_add_to_cart_json($fragments) {
    global $woocommerce;
    $fragments['total'] = $woocommerce->cart->get_cart_total();
    $fragments['contents_count'] = $woocommerce->cart->get_cart_contents_count();
    return $fragments;
}*/


function hudson_wp_title($title, $sep) {
    global $paged, $page;

    if (is_feed())
        return $title;

    // Add the site name.
    $title .= get_bloginfo('name');

    // Add the site description for the home/front page.
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && ( is_home() || is_front_page() ))
        $title = "$title $sep $site_description";

    // Add a page number if necessary.
    if ($paged >= 2 || $page >= 2)
        $title = "$title $sep " . sprintf(__('Page %s', 'hudson'), max($paged, $page));

    return $title;
}

add_filter('wp_title', 'hudson_wp_title', 10, 2);

if (!function_exists('tesla_comment_cb')) :

    /**
     * Template for comments and pingbacks.
     *
     * To override this walker in a child theme without modifying the comments template
     * simply create your own hudson_comment(), and that function will be used instead.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     *     
     */
    function tesla_comment_cb($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback' :
            case 'trackback' :
                // Display trackbacks differently than normal comments.
                ?>
                <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
                    <p><?php _e('Pingback:', 'hudson'); ?> <?php comment_author_link(); ?> <?php edit_comment_link(__('(Edit)', 'hudson'), '<span class="edit-link">', '</span>'); ?></p>
                    <?php
                    break;
                default :
                    // Proceed with normal comments.
                    global $post;
                    ?>
                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                    <div class="comment_image" >
                        <?php echo get_avatar($comment, 44); ?>
                    </div>
                    <div class="comment-info" id="comment-<?php echo comment_ID(); ?>">
                        <div class="comment_autor"><?php echo get_comment_author_link() ?> <span><?php echo get_comment_date(); ?> <?php echo get_comment_time(); ?></span></div>

                        <?php if ('0' == $comment->comment_approved) : ?>
                            <p class="comment-awaiting-moderation label label-warning"><?php _e('Your comment is awaiting moderation.', 'hudson'); ?></p>
                        <?php endif; ?>

                        <?php comment_text(); ?>
                        <a href="<?php comment_link($comment->comment_ID); ?>">#</a> &middot;
                        <?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply', 'hudson'), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                        <?php edit_comment_link(__('Edit', 'hudson'), ' &middot; ', ''); ?>
                    </div>                    
                    <?php
                    break;
            endswitch; // end comment_type check
        }

    endif;

    function hudson_page_menu_args($args) {
        if (!isset($args['show_home']))
            $args['show_home'] = true;
        return $args;
    }

    add_filter('wp_page_menu_args', 'hudson_page_menu_args');

    if (!function_exists('hudson_content_nav')) :

        /**
         * Displays navigation to next/previous pages when applicable.
         *
         */
        function hudson_content_nav($html_id) {
            global $wp_query;

            $html_id = esc_attr($html_id);

            if ($wp_query->max_num_pages > 1) :
                ?>
                <nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
                    <h3 class="assistive-text"><?php _e('Post navigation', 'hudson'); ?></h3>
                    <div class="nav-previous alignleft"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', 'hudson')); ?></div>
                    <div class="nav-next alignright"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', 'hudson')); ?></div>
                </nav><!-- #<?php echo $html_id; ?> .navigation -->
                <?php
            endif;
        }

    endif;


    if (!function_exists('hudson_entry_meta')) :

        /**
         * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
         *
         * Create your own hudson_entry_meta() to override in a child theme.
         *
         */
        function hudson_entry_meta() {
            // Translators: used between list items, there is a space after the comma.
            $categories_list = get_the_category_list(__(', ', 'hudson'));

            // Translators: used between list items, there is a space after the comma.
            $tag_list = get_the_tag_list('', __(', ', 'hudson'));

            $date = sprintf('<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>', esc_url(get_permalink()), esc_attr(get_the_time()), esc_attr(get_the_date('c')), esc_html(get_the_date())
            );

            $author = sprintf('<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(__('View all posts by %s', 'hudson'), get_the_author())), get_the_author()
            );

            // Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
            if ($tag_list) {
                $utility_text = __('This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'hudson');
            } elseif ($categories_list) {
                $utility_text = __('This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'hudson');
            } else {
                $utility_text = __('This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'hudson');
            }

            printf(
                            $utility_text, $categories_list, $tag_list, $date, $author
            );
        }

    endif;

    /**
     * Extends the default WordPress body class to denote:
     * 1. Using a full-width layout, when no active widgets in the sidebar
     *    or full-width template.
     * 2. Front Page template: thumbnail in use and number of sidebars for
     *    widget areas.
     * 3. White or empty background color to change the layout and spacing.
     * 4. Custom fonts enabled.
     * 5. Single or multiple authors.
     *
     * @param array Existing class values.
     * @return array Filtered class values.
     */
    function hudson_body_class($classes) {
        $background_color = get_background_color();

        if (!is_active_sidebar('sidebar-1') || is_page_template('page-templates/full-width.php'))
            $classes[] = 'full-width';

        if (is_page_template('page-templates/front-page.php')) {
            $classes[] = 'template-front-page';
            if (has_post_thumbnail())
                $classes[] = 'has-post-thumbnail';
            if (is_active_sidebar('sidebar-2') && is_active_sidebar('sidebar-3'))
                $classes[] = 'two-sidebars';
        }

        if (empty($background_color))
            $classes[] = 'custom-background-empty';
        elseif (in_array($background_color, array('fff', 'ffffff')))
            $classes[] = 'custom-background-white';

        if (!is_multi_author())
            $classes[] = 'single-author';

        return $classes;
    }

    add_filter('body_class', 'hudson_body_class');

    /**
     * Adjusts content_width value for full-width and single image attachment
     * templates, and when there are no active widgets in the sidebar.
     *
     */
    function hudson_content_width() {
        if (is_page_template('page-templates/full-width.php') || is_attachment() || !is_active_sidebar('sidebar-1')) {
            global $content_width;
            $content_width = 960;
        }
    }

    add_action('template_redirect', 'hudson_content_width');

    add_filter('loop_shop_columns', 'loop_columns');
    if (!function_exists('loop_columns')) {

        function loop_columns() {
            return 3; // 3 products per row
        }

    }

    function tesla_blog_page_url() {
        static $result = NULL;
        if ($result === NULL) {
            if (get_option('show_on_front') == 'page')
                $result = get_permalink(get_option('page_for_posts'));
            else
                $result = FALSE;
        }
        return $result;
    }

    if (!function_exists('collection_posts_number')) {

        function collection_posts_number(&$query) {
            switch (@$query->query_vars['post_type']) {
                case 'collection':  // Post Type named 'collection'
                    $query->query_vars['posts_per_page'] = 4; //display all is -1
                    break;
            }
            return $query;
        }

    }

    if (!is_admin()) {
        add_filter('pre_get_posts', 'collection_posts_number');
    }

    add_image_size( 'homepage-thumb', 66, 66 );

    add_filter('sod_ajax_layered_nav_product_container', 'aln_product_container');
    function aln_product_container($product_container){
        return '.products-container';
    }

    add_filter('sod_ajax_layered_nav_containers', 'aln_add_custom_container');
    function aln_add_custom_container($containers){
        $containers[] = '.products-container';
        return $containers;
    }

add_action( 'init', 'tt_manage_woo_actions' );
function tt_manage_woo_actions() {
    remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
}

//Ajaxify cart
// Ensure cart contents update when products are added to the cart via AJAX
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
 
function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    
    ob_start();?>
    
    <div class="cart_top_region">
        <div class="cart_top">
            <div class="cart_top_interior">

                <?php
                if (count($woocommerce->cart->get_cart()) > 0) {
                    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) {
                        $_product = $cart_item['data'];
                        // Only display if allowed
                        if (!apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key) || !$_product->exists() || $cart_item['quantity'] == 0)
                            continue;

                        // Get price
                        $product_price = get_option('woocommerce_tax_display_cart') == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

                        $product_price = apply_filters('woocommerce_cart_item_price_html', woocommerce_price($product_price), $cart_item, $cart_item_key);
                        ?>

                        <div class="cart_top_item">
                            <div class="cart_top_item_img">
                                <a href="<?php echo get_permalink($cart_item['product_id']); ?>"><?php echo $_product->get_image(); ?></a>
                            </div>

                            <div class="cart_top_item_info">
                                <div class="cart_top_item_remove">
                                    <?php
                                    echo apply_filters('woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">&times;</a>', esc_url($woocommerce->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce')), $cart_item_key);
                                    ?>
                                </div>
                                <h2><a href="<?php echo get_permalink($cart_item['product_id']); ?>"><?php echo apply_filters('woocommerce_widget_cart_product_title', $_product->get_title(), $_product); ?></a></h2>
                                <p><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></p>
                            </div> 
                        </div>

                    <?php } ?>

                    <div class="cart_top_item_all">
                        <?php _e('Total', 'hudson'); ?> : <span class="dynamic_cart_total"><?php echo $woocommerce->cart->get_cart_subtotal(); ?></span>
                    </div>
                <?php } else { ?>
                    <?php _e('No products in cart. Keep shopping.', 'hudson'); ?>
                <?php } ?>
            </div>
            <a href="<?php echo $woocommerce->cart->get_checkout_url(); ?>" class="cart_top_button"><?php _e('Checkout', 'woocommerce'); ?></a>
            <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="cart_top_button"><?php _e('View Cart', 'woocommerce'); ?></a>        
        </div>
    </div>
    
    <?php $fragments['div.cart_top_region'] = ob_get_clean();
    $fragments['total'] = $woocommerce->cart->get_cart_total();
    $fragments['contents_count'] = $woocommerce->cart->get_cart_contents_count();
    return $fragments;
    
}