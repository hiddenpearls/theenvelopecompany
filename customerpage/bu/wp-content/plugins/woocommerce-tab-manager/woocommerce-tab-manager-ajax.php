<?php
/**
 * WooCommerce Tab Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@foxrunsoftware.net so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://wcdocs.woothemes.com/user-guide/extensions/tab-manager/
 *
 * @package     WC-Tab-Manager/Functions/AJAX
 * @author      Justin Stern
 * @copyright   Copyright (c) 2012-2013, Justin Stern
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * WooCommerce Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/** Admin AJAX events **************************************************/


add_action( 'wp_ajax_wc_tab_manager_get_editor', 'wc_tab_manager_get_editor' );

/**
 * Gets a quicktags editor
 *
 * @access public
 */
function wc_tab_manager_get_editor() {

	check_ajax_referer( 'get-editor', 'security' );

	$size = esc_attr( $_POST['size'] );

	// TODO: would be nice to suppress $editor_buttons_css but doesn't seem possible (unless there's some way of hooking into the wp_print_styles('editor-buttons'); and stopping it
	//       (or, maybe call wp_editor twice, discarding the content from the first... ?)
	wp_editor( '', 'producttabcontent' . $size, array( 'textarea_name' => 'product_tab_content[' . $size . ']', 'tinymce' => false, 'textarea_rows' => 10 ) );

	// Quit out
	die();
}
