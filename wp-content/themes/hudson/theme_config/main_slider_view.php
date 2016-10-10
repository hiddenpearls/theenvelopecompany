<ul>
	<?php foreach($slides as $slide): ?>
	<li>
		<h4><?php echo implode(', ', $slide['categories']); ?></h4>
		<span><?php print $slide['options']['description']; ?></span>
		<img src="<?php echo esc_attr($slide['options']['image']); ?>" alt="<?php echo esc_attr($slide['options']['title']); ?>" />
	</li>
	<?php endforeach; ?>
</ul>
<?php if(!empty($all_categories)): ?>
	<h3><?php _e('Categories','hudson') ?></h3>
	<ul>
		<?php foreach($all_categories as $category_slug => $category_name): ?>
			<li><?php print $category_name; ?> - <a href="#"><?php print $category_slug; ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>