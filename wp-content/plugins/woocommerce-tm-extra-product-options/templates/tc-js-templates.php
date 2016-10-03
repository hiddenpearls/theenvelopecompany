<?php
/**
 * Price display template
 *
 * This is a javascript-based template for single variations (see https://codex.wordpress.org/Javascript_Reference/wp.template).
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<script type="text/template" id="tmpl-tc-cart-options-popup">
	 <div class='header'>
	 	<h3>{{{ data.title }}}</h3>
	 </div>
	 <div id='{{{ data.id }}}' class='float_editbox'>{{{ data.html }}}</div>
	<div class='footer'>
		<div class='inner'>
			<span class='tm-button button button-secondary button-large details_cancel'>{{{ data.close }}}</span>
		</div>
	</div>
</script>
<script type="text/template" id="tmpl-tc-lightbox">
	<div class="tc-lightbox-wrap">
		<span class="tc-lightbox-button tcfa tcfa-search tc-transition tcinit"></span>
	</div>
</script>
<script type="text/template" id="tmpl-tc-lightbox-zoom">
	<span class="tc-lightbox-button-close tcfa tcfa-close"></span>
	{{{ data.img }}}
</script>
<script type="text/template" id="tmpl-tc-final-totals">
	<dl class="tm-extra-product-options-totals tm-custom-price-totals">
		<# if (data.show_options_total==true){ #>
		<dt class="tm-options-totals">{{{ data.options_total }}}</dt>
		<dd class="tm-options-totals">
			<span class="price amount options">{{{ data.formatted_options_total }}}</span>
		</dd>
		<# } #>
		<# if (data.show_extra_fee==true){ #>
		<dt class="tm-extra-fee">{{{ data.extra_fee }}}</dt>
		<dd class="tm-extra-fee">
			<span class="price amount options extra-fee">{{{ data.formatted_extra_fee }}}</span>
		</dd>
		<# } #>
		<# if (data.show_final_total==true){ #>
		<dt class="tm-final-totals">{{{ data.final_total }}}</dt>
		<dd class="tm-final-totals">
			<span class="price amount final">{{{ data.formatted_final_total }}}</span>
		</dd>
		<# } #>
		<# if (data.sign_up_fee==true){ #>
		<dt class="tm-subscription-fee">{{{ data.sign_up_fee }}}</dt>
		<dd class="tm-subscription-fee">
			<span class="price amount subscription-fee">{{{ data.formatted_subscription_fee_total }}}</span>
		</dd>
		<# } #>
	</dl>
</script>
<script type="text/template" id="tmpl-tc-price">
	<span class="amount">{{{ data.price.price }}}</span>
</script>
<script type="text/template" id="tmpl-tc-sale-price">
   	<del>
   		<span class="tc-original-price amount">{{{ data.price.original_price }}}</span>
   	</del>
   	<ins>
   		<span class="amount">{{{ data.price.price }}}</span>
   	</ins>
</script>
<script type="text/template" id="tmpl-tc-section-pop-link">
	<div id="tm-section-pop-up" class="tm-extra-product-options flasho tm_wrapper tm-section-pop-up single tm-animated appear">		
		<div class='header'><h3>{{{ data.title }}}</h3></div>
		<div class="float_editbox" id="temp_for_floatbox_insert"></div>
		<div class='footer'>
			<div class='inner'>
				<span class='tm-button button button-secondary button-large details_cancel'>{{{ data.close }}}</span>
			</div>
		</div>		
	</div>
</script>
<script type="text/template" id="tmpl-tc-floating-box-nks">
		{{{ data.html_before }}}
		<div class="tc-row tm-fb-labels">
			<span class="tc-cell tc-col-3 tm-fb-title">{{{ data.option_label }}}</span>
			<span class="tc-cell tc-col-3 tm-fb-value">{{{ data.option_value }}}</span>
			<span class="tc-cell tc-col-3 tm-fb-quantity">{{{ data.option__qty }}}</span>
			<span class="tc-cell tc-col-3 tm-fb-price">{{{ data.option_lpric }}}</span>
		</div>
		<# for (var i = 0; i < data.values.length; i++) { #>
		<div class="tc-row">
			<span class="tc-cell tc-col-3 tm-fb-title">{{{ data.values[i].title }}}</span>
			<span class="tc-cell tc-col-3 tm-fb-value">{{{ data.values[i].value }}}</span>
			<span class="tc-cell tc-col-3 tm-fb-quantity">{{{ data.values[i].quantity }}}</span>
			<span class="tc-cell tc-col-3 tm-fb-price">{{{ data.values[i].price }}}</span>
		</div>
		<# } #>
		{{{ data.html_after }}}
		{{{ data.totals }}}
</script>
<script type="text/template" id="tmpl-tc-floating-box">
		{{{ data.html_before }}}
		<dl class="tm-fb">
			<# for (var i = 0; i < data.values.length; i++) { #>
			<# if (data.values[i].label_show=='') {#>
			<dt class="tm-fb-title">{{{ data.values[i].title }}}</dt>
			<# } #>
			<# if (data.values[i].value_show=='') {#>
			<dd class="tm-fb-value">{{{ data.values[i].value }}}</dd>
			<# } #>
			<# } #>
		</dl>
		{{{ data.html_after }}}
		{{{ data.totals }}}
</script>
<script type="text/template" id="tmpl-tc-chars-remanining">
	<span class="tc-chars">
		<span class="tc-chars-remanining">{{{ data.maxlength }}}</span>
		<span class="tc-remaining"> {{{ data.characters_remaining }}}</span>
	</span>
</script>