<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h2><?php the_field('title');?></h2>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h3><?php the_field('company');?></h3>
			<p><?php the_field('address_1');?></p>

			<?php if(get_field('address_2')){?>
				<p><?php the_field('address_2');?></p>
			<?php }?>

			<p><?php echo get_field('city').', '.get_field('state_province_region')." ".get_field('zip');?></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h3><?php the_field('title_section');?></h3>
			<p>Toll Free: <strong><?php the_field('toll_free');?></strong></p>
			<p>Phone: <strong><?php echo get_field('phone');?></strong></p>
			<p>Fax: <strong><?php echo get_field('fax');?></strong></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h3><?php the_field('title_feedback_section');?></h3>
			<p><?php the_field('description');?></p>
			<?php gravity_form( 1, false, false, false, '', false ); ?>
		</div>
	</div>
</div>