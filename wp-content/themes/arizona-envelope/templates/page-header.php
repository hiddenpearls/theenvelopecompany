<?php use Roots\Sage\Titles; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 text-center">
        	<?php if(is_page('resources')){ ?>
        		<h1 class="title-pages"><?= the_field('title'); ?></h1>
        	<?php }elseif(is_wc_endpoint_url()){ ?>
				<h1 class="title-pages"><?= Titles\title(); ?></h1>
        	<?php }else{ ?>
            	<h1 class="title-pages"><?= Titles\title(); ?></h1>
            <?php }?>
        </div>
    </div>
</div>

