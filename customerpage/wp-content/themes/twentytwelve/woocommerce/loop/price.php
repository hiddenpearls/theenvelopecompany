<?php
/**
 * Loop Price
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
global $product;
?>
<?php if ($price_html = $product->is_in_stock()) : ?>
	<span class="price"><?php //* echo $price_html; *// jason petzke ?></span>
<?php endif; ?>