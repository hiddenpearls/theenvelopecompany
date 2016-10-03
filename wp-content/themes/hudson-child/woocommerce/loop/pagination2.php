<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wp_query;
?>

<style>
#going-left {left: -80px; top: -40px; float: left; position: relative !important; z-index: 85  !important;
 
    border-radius: 5px 0 0 5px;
    width: 80px;
    height: 64px;
    background: url('https://shop.azenvelope.com/wp-content/themes/hudson/images/slide_left.png') no-repeat center center;
}
#going-right { top: -40px; right: -80px; float: right; position: relative !important;

    border-radius: 5px 0 0 5px;
    width: 80px;
    height: 64px;
    background: url('https://shop.azenvelope.com/wp-content/themes/hudson/images/slide_right.png') no-repeat center center;
}
</style>

<ul class="navigation_arrows">
            
<?php
previous_posts_link( '<li class="slide-left" id="going-left">%1</li>' );
?>
<?php add_filter('<div></div>', woocommerce_result_count()); ?>
<?php    
next_posts_link( '<li class="slide-right" id="going-right">%1</li>');
?>

</ul>
 

</div>
<div style="clear:both;"></div>


