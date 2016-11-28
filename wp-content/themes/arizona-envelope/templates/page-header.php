<?php use Roots\Sage\Titles; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 text-center">
        	<?php if(is_page('resources')){ ?>
        		<h1 class="title-pages"><?= the_field('title'); ?></h1>
        	<?php }elseif(is_wc_endpoint_url( 'edit-account' )){ ?>
				<h1 class="title-pages">Account Details</h1>
            

            <?php }elseif(is_wc_endpoint_url( 'edit-address' )){ ?>
                <?php $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; ?>
                <?php  
                if (strpos($url,'billing') !== false) {
                    echo '<h1 class="title-pages">Edit Billing Address</h1>';
                } elseif(strpos($url,'shipping') !== false) {
                    echo '<h1 class="title-pages">Edit Shipping Address</h1>';
                } else {
                    echo '<h1 class="title-pages">My Addresses</h1>';
                }
                ?>
            <?php }elseif(is_wc_endpoint_url( 'orders' )){ ?>
                <h1 class="title-pages">My Orders</h1>
            <?php }elseif(is_wc_endpoint_url( 'view-order' )){ ?>
                <h1 class="title-pages">View Order</h1>
        	<?php }else{ ?>
            	<h1 class="title-pages"><?= Titles\title(); ?></h1>
            <?php }?>
        </div>
    </div>  
</div>

