<?php
/**
 * Custom Loop by You Are Here Media, LLC.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="variation">
    <?php
        foreach ( $item_data as $data ) :
               $new_key = sanitize_text_field( $data['key'] );
			   if (empty($new_key)) {
				   if ($i<1){ $i=1;}
				     $i=$i+$i; 
				   if ($i==4) {
					echo '<hr style="margin: 5px 0 0 0 !important;" /><br />ADD OPTION: Latex Seal ';
					echo wp_kses_post( $data['key'] ) . ':'; }				 
			   }
			   else {  
			   
			   ?>
			  
              	<?php               
				   switch ($new_key) {
					case 'Please enter a single PMS Color code:':  
					echo '<hr style="margin: 5px 0 0 0 !important;" /><span style="font-weight:normal;text-transform:capitalize;">CUSTOM ENVELOPE OPTIONS<br />PMS Colors: ';
			 		break; 
					
					case 'Please enter a second PMS Color code:':
					echo ', ';
			 		break; 
					
					case 'Upload Files:':
					echo '<br />Graphics File: ';
			 		break; 

					case 'Quantity:':
					echo '<strong>ENVELOPE QUANTITY: </strong>';
					break; 
					
					default:
					echo '<br />CUSTOM OPTION ';
					echo wp_kses_post( $data['key'] ) . ':';
					break;
	    	
						}
   					echo wp_kses_post( $data['value'] ) ;
  				?>
	<?php 	}
		endforeach; 
	?>
 </span>
</div>
