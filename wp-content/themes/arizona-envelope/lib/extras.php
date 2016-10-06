<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Add options page(s)
 */
if( function_exists('acf_add_options_page') ) {
  
  acf_add_options_page(array(
    'page_title'  => 'Theme General Settings',
    'menu_title'  => 'Theme Settings',
    'menu_slug'   => 'theme-general-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
  /* acf_add_options_sub_page(array(
    'page_title'  => 'Theme Footer Settings',
    'menu_title'  => 'Footer',
    'parent_slug' => 'theme-general-settings',
  ));
  acf_add_options_sub_page(array(
    'page_title'  => 'Theme Header Settings',
    'menu_title'  => 'Header',
    'parent_slug' => 'theme-general-settings',
  ));
  
  */
  
}
/**
 * Remove wordpress' auto <p> tags on the excerpt
 */
remove_filter( 'the_excerpt', 'wpautop' );

/**
 * WooCommerce Theme integration
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'Roots\Sage\Extras\my_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'Roots\Sage\Extras\my_theme_wrapper_end', 10);

function my_theme_wrapper_start() {
  echo '<div class="main-content">';
}
function my_theme_wrapper_end() {
  echo '</div>';
}

/**
 * Remove WooCommerce Breadcrumbs
 */
add_action( 'init', 'Roots\Sage\Extras\shop_remove_wc_breadcrumbs' );
function shop_remove_wc_breadcrumbs() {
  remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

/**
 * Reinsert breadcrumbs
 */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'Roots\Sage\Extras\custom_breadcrumb', 30);
function custom_breadcrumb() {
  echo woocommerce_breadcrumb();
}

/**
 * Change product's name position within the DOM.
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'Roots\Sage\Extras\woocommerce_template_loop_attributes', 10 );
function woocommerce_template_loop_attributes(){
  global $product;
  //echo $product->list_attributes();
  $size = $product->get_attribute( 'size' ) ;
  echo '<p>'.$size.'</p>';
  $color = $product->get_attribute( 'color' ) ;
  echo '<p>'.$color.'</p>';
  

}

/**
 * Change Add To Cart to Select Options
 */
function sfws_woocommerce_product_add_to_cart_text() {
  global $product;
  return __( 'View Product', 'woocommerce' );
}
//* Change the Add To Cart Link
add_filter( 'woocommerce_loop_add_to_cart_link', 'Roots\Sage\Extras\sfws_add_product_link' );
function sfws_add_product_link( $link ) {
 global $product;
 $product_id = $product->id;
 $product_sku = $product->get_sku();
 $link = '<a href="'.get_permalink().'" rel="nofollow" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" data-quantity="1" class="button add_to_cart_button product_type_variable">'.sfws_woocommerce_product_add_to_cart_text().'</a>';
 return $link;
}

