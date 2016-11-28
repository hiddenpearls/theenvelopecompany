<div class="container">
	<div class="row content-centered">
		<div class="col-md-8 col-md-offset-2 text-center">
			<?php the_content(); ?>	
		</div>
	</div>
	<?php 
	if( get_field('resources') ) { ?>
		<div class="row resources">
			<div class="col-md-8 col-md-offset-2 text-center">
				<ul>
					<?php
					//this is only needed if it's on the text variation
					if( get_field('link_or_text') == 'text' ):
						$resources = get_field('resources');
						$order = array();
						foreach ($resources as $i => $row) {
							//echo gettype($row['resource_text_title']);
							$order[$i] = strtolower($row['resource_text_title']);
						}
						//echo "<pre>";
						//print_r($order);
						//echo "</pre>";
						array_multisort( $order, SORT_ASC, SORT_LOCALE_STRING, $resources );

						if($resources):
							foreach ($resources as $i => $row) { ?>
								<li>
									<strong><?php echo $row['resource_text_title']; ?></strong> – <?php echo $row['resource_text_content']; ?>
								</li>
								<hr>
							<?php }
						endif;
					elseif( get_field('link_or_text') == 'link' ):
						if( have_rows('resources') ):
							while ( have_rows('resources') ) : the_row(); ?>
								<li>
									<a target="_blank" href="<?php the_sub_field('resource_link_url'); ?>">
										<?php the_sub_field('resource_link_label'); ?>
									</a>
								</li>
									
							<?php endwhile;
						endif;
					endif;

					?>
				</ul>
			</div>
		</div>
	<?php
	} 
	?>
	<?php if( get_field('sub_content') ){ ?>
		<div class="row content-centered">
			<div class="col-md-8 col-md-offset-2 text-center">
				<?php the_field('sub_content'); ?>
			</div>
		</div>
	<?php } ?>
</div>
<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>