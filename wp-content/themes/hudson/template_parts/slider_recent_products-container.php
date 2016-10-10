<?php
global $tesla_trans_var;
?>
<div class="tt-products-carousel" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item="&gt;div" data-tesla-rotate="false" data-tesla-autoplay="false" data-tesla-hide-effect="false">
    <ul class="navigation_arrows float_right">
        <li class="left_arrow prev"></li>
        <li class="right_arrow next"></li>
    </ul>

    <h4 class="headline"><?php print $tesla_trans_var['headline']; ?></h4>
    <div class="items">
        <div class="row tesla-carousel-items">
            <?php print $tesla_trans_var['looped_products']; ?>      
        </div>
    </div>
</div>