<?php
/**
 * Template Name: Shop Redirect
 *
 * Description: Twenty Twelve loves the no-sidebar look as much as
 * you do. Use this page template to remove the sidebar from any page.
 *
 * Tip: to remove the sidebar from all posts and pages simply remove
 * any active widgets from the Main Sidebar area, and the sidebar will
 * disappear everywhere.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
<?php
global $current_user;
get_currentuserinfo();
$get_username_directory = $current_user->nickname;
$page_title = $wp_query->post->post_slug;
?>
<?php ob_start()?>
<h1>redirecting....</h1>
<?php
if($page_title != $get_username_directory){
    header('Location: http://'.$_SERVER['HTTP_HOST'].'/customerpage/product-category/'.$get_username_directory); 
    exit;
}
else {
    header('Location: http://'.$_SERVER['HTTP_HOST'].'/customerpage/my-account/');
    exit;
}
?>
