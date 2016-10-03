<ul>
	<?php foreach($slides as $slide): ?>
	<li>
		<h4><?php echo implode(', ', $slide['categories']); ?></h4>
		<span><?php echo $slide['options']['description']; ?></span>
		<img src="<?php echo $slide['options']['image']; ?>" alt="<?php echo $slide['options']['title']; ?>" />
	</li>
	<?php endforeach; ?>
</ul>
<?php if(!empty($all_categories)): ?>
	<h3>Categories</h3>
	<ul>
		<?php foreach($all_categories as $category_slug => $category_name): ?>
			<li><?php echo $category_name; ?> - <a href="#"><?php echo $category_slug; ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>