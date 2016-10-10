<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!$errors)
    return;
?>
<div class="alert alert-error woocommerce-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php foreach ($errors as $error) : ?>
        <span><?php echo wp_kses_post($error); ?></span>
    <?php endforeach; ?>
</div>
