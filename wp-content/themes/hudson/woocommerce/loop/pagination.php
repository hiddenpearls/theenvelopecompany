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

if ( $wp_query->max_num_pages <= 1 ) {
    return;
}
?>

<div class="h_pagination">
    <?php
        echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
            'show_all' => FALSE,
            'end_size' => 1,
            'total' => $wp_query->max_num_pages,
            'current' => max( 1, get_query_var( 'paged' ) ),
            'mid_size' => 2,
            'prev_next' => True,
            'prev_text' => __('Previous', 'hudson'),
            'next_text' => __('Next', 'hudson'),
            'type' => 'list',
            'add_args' => False,
            'add_fragment' => '',
            'base' => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
            'format' => '',
        ) ) );
    ?>                                            
</div>