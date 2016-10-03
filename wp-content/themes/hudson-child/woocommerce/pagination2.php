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

<div style="margin: 0 auto !important; !important;">
<p class="woocommerce-result-count" id="pre"><?php
$previous_btn = previous_posts_link( 'Previous Page ' );
if(empty($previous_btn)){
	echo 'Previous Page ';
	} else {
	previous_posts_link( 'Previous Page ' ); 
	}
?></p><p align="center">
<?php <woocommerce_result_count(); ?></p>
<p class="woocommerce-result-count" id="post"><?php    
next_posts_link( ' Next Page' );
 ?></p>

</div>
<div style="clear:both;"></div>


