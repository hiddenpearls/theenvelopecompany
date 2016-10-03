<?php
/**
 * Custom Loop by You Are Here Media, LLC.
 * Line 19-59 switch case / 46-55 simple product
 * 
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product->is_in_stock() ) : ?>

    <a href="<?php echo apply_filters( 'out_of_stock_add_to_cart_url', get_permalink( $product->id ) ); ?>" class="button"><?php echo apply_filters( 'out_of_stock_add_to_cart_text', __( 'Read More', 'woocommerce' ) ); ?></a>

    <?php

	else : 

        $link = array(
            'url'   => '',
            'label' => '',
            'class' => ''
        );

        switch ( $product->product_type ) {
            case "variable" :
                $link['url']    = apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
                $link['label']  = apply_filters( 'variable_add_to_cart_text', __( 'Select options', 'woocommerce' ) );
            break;
            case "grouped" :
                $link['url']    = apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
                $link['label']  = apply_filters( 'grouped_add_to_cart_text', __( 'View options', 'woocommerce' ) );
            break;
            case "external" :
                $link['url']    = apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
                $link['label']  = apply_filters( 'external_add_to_cart_text', __( 'Read More', 'woocommerce' ) );
            break;
            default :
                if ( $product->is_purchasable() ) {
                    $link['label']  = apply_filters( 'add_to_cart_text', __( '', 'woocommerce' ) );
                    $link['class']  = apply_filters( 'add_to_cart_class', 'add_to_cart_button' );
                } else {
                    $link['url']    = apply_filters( 'not_purchasable_url', get_permalink( $product->id ) );
                    $link['label']  = apply_filters( 'not_purchasable_text', __( 'Read More', 'woocommerce' ) );
                }
            break;
        }

	endif; 
?>

