<?php 
/*

Plugin Name: 301 Redirects
Plugin URI: http://tonyspiro.com
Description: A plugin that helps you add 301 redirects to your site.
Version: 0.3
Author: Tony Spiro
Author URI: http://tonyspiro.com
License: GPL2

Copyright 2015  Tony Spiro (email: tspiro@tonyspiro.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*	301 Redirects
==================================== */

/*

Testing

ini_set('display_errors',1); 
error_reporting(E_ALL);

*/

include('controllers.php');

function load_301_redirect_assets($adminpage)
{
	if ($adminpage == 'settings_page_301-redirects')
	{
	wp_enqueue_style( 'redirect_301_bootstrap_css', plugin_dir_url( __FILE__ ) . 'lib/bootstrap-3.3.4.css', false, '1.0.0' );
	wp_enqueue_style(  'redirect_301_custom_css', plugin_dir_url( __FILE__ ) . 'style.css', false, '1.0.0' );
	wp_enqueue_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'lib/bootstrap-3.3.4.js', array(), '1.0.0', true );
	}
}

add_action( 'admin_enqueue_scripts', 'load_301_redirect_assets' );

$siteurl = get_bloginfo('url');

function getUrl() {
  $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] :  'https://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  return $url;
}

$actual_link = getUrl();

$all_redirects = $GLOBALS['redirectsplugins']->getAll();

if($all_redirects){
	foreach($all_redirects as $redirect_id){
		$la_redirect = $GLOBALS['redirectsplugins']->getFields($redirect_id);
		$la_old_link = str_replace($siteurl, "", $la_redirect['old_link']);
		$la_new_link = str_replace($siteurl, "", $la_redirect['new_link']);
		if($actual_link == $siteurl . $la_old_link){
			header("Location: " . $siteurl . $la_new_link, true, 301);
			die();
		}
	}
}

function redirects_301_options() {

	$redirects = new Redirects;

	/* Processes ========================= */

	$savedsuccesfully = false;
	if (isset($_POST['custom_id']) && isset($_POST['delete_custom']))
	{
		$custom_id = sanitize_text_field($_POST['custom_id']);
		$redirects->remove($custom_id);
		die();
	}

	if (isset($_POST['links_audit_submit']) && !isset($_POST['delete_custom']))
	{

		$redirects->delete();

		$redirect_arr = $_POST['title'];

		foreach($redirect_arr as $key => $redirect_title){

			$title = sanitize_text_field($redirect_title);
			$section = sanitize_text_field($_POST['section'][$key]);
			$new_link = esc_url($_POST['new_link'][$key]);
			$old_link = esc_url($_POST['old_link'][$key]);

			$redirects->edit($title, $section, $new_link, $old_link);
		}

		$savedsuccesfully = true;
	}


	?>

	<div class="col-sm-12">
		<?php
		if ($savedsuccesfully)
		{
			?>
			<div class="alert alert-success">Redirects saved.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>
			<?php
		}
		?>
		<h1>301 Redirects</h1>
		<p>
			Add your old paths <code>/old-path-from-old-site</code> in the old link field and the new path <code>/new-path-in-new-site</code> in the new link fields. Title and Section are there for your organization and convenience.  
			301 Redirects works automatically by redirecting users from your old links to your new ones.			
		</p>
		<form action="" method="post">
			<input type="hidden" name="links_audit_submit" value="true">
			<table class="table table-striped table-bordered">
				<tr>
					<td class="col-md-3">Title</td>
					<td class="col-md-3">Section</td>
					<td class="col-md-3">Old Link</td>
					<td class="col-md-3">New Link</td>
				</tr>
				<?php
				/// Custom redirects
				$custom_redirects = $redirects->getAll();

				if($custom_redirects){
					?>
					<?php
					foreach($redirects->getAll() as $custom_id){

						$fields = $redirects->getFields($custom_id);
						?>
						<tr id="customRow<?php echo $custom_id; ?>">
							<td><input type="text" class="form-control" placeholder="Title" name="title[]" value="<?php echo $fields['title']; ?>" /></td>
							<td><input type="text" class="form-control" placeholder="Section" name="section[]" value="<?php echo $fields['section']; ?>" /></td>
							<td><input placeholder="Old Link" name="old_link[]" class="form-control" value="<?php echo $fields['old_link']; ?>" /></td>
							<td>
								<table class="no-border col-sm-12">
									<tr><td><input type="text" class="form-control" placeholder="New Link" name="new_link[]" value="<?php echo $fields['new_link']; ?>" /></td><td><a title="remove row" class="remove-custom pull-right close" href="#" data-id="<?php echo $custom_id; ?>">&times;</a></td></tr>
								</table>
							</td>
						</tr>
						<?php
					}

				}
				?>
				<tr id="addRow">
					<td colspan="10"><a id="addRowBtn" class="btn btn-default pull-right" href="#">+ Add a new row</a></td>
				</tr>
				<tr>
					<td colspan="10" class="text-right"><button type="submit" class="btn btn-default btn-success">Save All</button></td></td>
				</tr>
			</table>
		</form>
	</div><!-- .col-sm-12 -->
	<script>

		jQuery(function(){

			var rowId = 0;

			jQuery('#addRowBtn').on('click', function(e){

				e.preventDefault();

				var newRow = '<tr id="row' + rowId + '">' + 
				'<td><input type="text" class="form-control" placeholder="Title" name="title[]" /></td>' + 
				'<td><input type="text" class="form-control" placeholder="Section" name="section[]" /></td>' + 
				'<td><input name="old_link[]" class="pull-left form-control" placeholder="Old Link" /></td>' + 
				'<td><table class="no-border col-sm-12">' + 
				'<tr><td><input type="text" class="form-control" placeholder="New Link" name="new_link[]" /></td><td><a title="remove row" class="remove-row pull-right close" href="#" data-id="' + rowId + '">&times;</a></td>' +
				'</tr></table></td>' + 
				'</tr>';

				jQuery('#addRow').before(newRow);

				rowId++;

				jQuery('.remove-row').on('click', function(e){
					e.preventDefault();
					var id = jQuery(this).data('id');
					jQuery('#row' + id).fadeOut(function(){
						jQuery(this).remove();
					});
				});

			});

		});
	</script>
	<script>

		jQuery(function(){
			
			jQuery('.remove-custom').on('click', function(e){

				e.preventDefault();

				var confirmDelete = confirm("Are you sure you want to delete this 1 row?  This cannot be undone.");

				if(confirmDelete){

					var id = jQuery(this).data('id');

					jQuery.ajax({
						type: "POST",
						url: '',
						data: { delete_custom : 'true', custom_id: id }
					
					}).done(function( html ) {

						jQuery('#customRow' + id).fadeOut(function(){
							jQuery(this).remove();
						});

					});
				}
			});
		});
	</script>  
<?php
}

add_action( 'admin_menu', 'redirects_301' );

function redirects_301() {
	add_options_page( '301 Redirects', '301 Redirects', 'manage_options', '301-redirects', 'redirects_301_options' );
}
