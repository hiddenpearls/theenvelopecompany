<?php
/**
 * Show options for ordering
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $woocommerce, $wp_query;

if (1 == $wp_query->found_posts || !woocommerce_products_will_display())
    return;
?>
<form class="woocommerce-ordering" method="get">

    <?php
    if (!isset($catalog_orderby_options[$orderby]))
        $orderby = 'date';
    $orderby_selected = array('id' => $orderby, 'html' => '<a href="#">' . esc_attr($catalog_orderby_options[$orderby]) . '</a>');
    $orderby_select = array();
    foreach ( $catalog_orderby_options as $id => $name ) {
        if ($id == $orderby_selected['id'])
            continue;
        $orderby_select[] = '<li><a class="orderby_li" data-value="' . esc_attr($id) . '" href="#">' . esc_attr($name) . '</a></li>';
    }
    if ($orderby_selected === NULL)
        $orderby_selected = array_shift($orderby_select);
    ?>

    <div class="orderby">
        <ul>
            <li>
                <?php print $orderby_selected['html']; ?>
                <ul>
                    <?php echo implode('', $orderby_select); ?>
                </ul>
            </li>
        </ul>  
        <?php _e('Order by','hudson') ?>
    </div>

    <?php
        // Keep query string vars intact
        foreach ( $_GET as $key => $val ) {
            if ( 'orderby' === $key || 'submit' === $key ) {
                continue;
            }
            if ( is_array( $val ) ) {
                foreach( $val as $innerVal ) {
                    echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
                }
            } else {
                echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
            }
        }
    ?>

    <input type="hidden" name="orderby" value="" />

</form>