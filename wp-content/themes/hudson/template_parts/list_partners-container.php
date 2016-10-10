<?php
global $tesla_trans_var;
?>
<div class="partners" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item="&gt;div" data-tesla-rotate="false" data-tesla-autoplay="false" data-tesla-hide-effect="false">
    <ul class="navigation_arrows">
        <li class="left_arrow prev"></li>
        <li class="right_arrow next"></li>
    </ul>
	<div class="row tesla-carousel-items">
    	<?php print $tesla_trans_var['looped_partners']; ?>
	</div>
</div>