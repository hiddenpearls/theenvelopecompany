<?php
/**
 * Plugin Name: WooCommerce Tab Manager
 * Plugin URI: http://www.woothemes.com/products/woocommerce-tab-manager/
 * Description: A product tab manager for WooCommerce
 * Version: 1.0.5
 * Author: Justin Stern
 * Author URI: http://www.foxrunsoftware.net
 * Text Domain: wc-tab-manager
 * Domain Path: /languages/
 * Tested up to: 3.5
 *
 * Copyright: (c) 2012-2013 Justin Stern
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Tab-Manager
 * @author      Justin Stern
 * @category    Plugin
 * @copyright   Copyright (c) 2012-2013, Justin Stern
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '89a9ac74850855cfe772b4b4ee1e31e0', '132195' );

// Check if WooCommerce is active and deactivate extension if it's not
if ( ! is_woocommerce_active() )
	return;

/**
 * The WC_Tab_Manager global object
 * @name $wc_tab_manager
 * @global WC_Tab_Manager $GLOBALS['wc_tab_manager']
 */
$GLOBALS['wc_tab_manager'] = new WC_Tab_Manager();

/**
 * The WooCommerce Tab Manager allows product tabs to be ordered, created,
 * updated, and removed.
 *
 * For the purposes of this plugin the tabs available for management can be
 * broken down into the following categories:
 *
 * - core tabs: The default Description, Additional Information and Reviews tabs
 * - global tabs: These are tabs which can be added to any product
 * - 3rd party tabs: Tabs added by 3rd party plugins, ie WooCommerce Product Enquiry Form
 * - product level tabs: Tabs added to a particular product
 *
 * Note: the terminology surrounding "product tabs" is somewhat confusing in
 * this plugin as I use the term to refer both to any tab which can be added to
 * a product, as well as the "product level tabs".  Hopefully the code context
 * makes it clear enough though.
 *
 * Global and Product Level tabs themselves are represented by a new post_type
 * named wc_product_tab.
 *
 * Tabs can be configured globally from within the Tab Manager submenu, and
 * overridden at the individual product level.  At the global level the tab layout
 * is stored as a wordpress option.  At the product level, the tab layout is
 * stored in the exact same structure, as a post meta.
 *
 * Database:
 * - Option named 'wc_tab_manager_db_version' with the current plugin version
 * - Option named 'wc_tab_manager_default_layout' with the global default layout
 * - Postmeta named '_override_tab_layout' attached to products, indicating
 *   whether the global default layout is to be used
 * - Postmeta named '_product_tabs' attached to products, with the product-level
 *   tab layout (same structure as the global default layout)
 *
 * @since 1.0
 */
class WC_Tab_Manager {

	/** plugin text domain */
	const TEXT_DOMAIN = 'wc-tab-manager';

	/** Plugin version */
	const VERSION = "1.0.5";

	/** Plugin version option name */
	const VERSION_OPTION_NAME = 'wc_tab_manager_db_version';

	/** The plugins id, used for various slugs and such */
	const ID = 'wc-tab-manager';

	/**
	 * Indicates whether this is a WC < 2.0 that has had the
	 * templates/single-product/tabs/tab-attributes.php, tab-description.php,
	 * tab-reviews.php templates replaced with the ones from WC 2.0 with the tab
	 * title filters, meaning that the tab title can be changed.  This is really
	 * for development purposes.
	 */
	const WC_PRE_2_PATCHED = false;

	/**
	 * Local cache array of product tabs, keyed off product id
	 * @var array
	 */
	private $product_tabs = array();

	/**
	 * Array of third party tabs
	 * @var array
	 */
	private $third_party_tabs;

	/**
	 * Plugin url
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Plugin path
	 * @var string
	 */
	private $plugin_path;


	/**
	 * Setup main plugin class
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'include_template_functions' ), 25 );

		add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );

		// allow the use of shortcodes within the tab content
		add_filter( 'woocommerce_tab_manager_tab_panel_content', 'do_shortcode' );

		// add a 'Configure' link to the plugin action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_manage_link' ), 10, 4 );    // remember, __FILE__ derefs symlinks :(

		// include required files
		$this->includes();
	}


	/**
	 * Init WooCommerce Tab Manager
	 */
	public function init() {

		// localization
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Init user roles
		$this->init_user_roles();

		// Init WooCommerce Product Tab taxonomy
		$this->init_taxonomy();

		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();
	}


	/**
	 * Invoked once WooCommerce is done initializing
	 *
	 * @since 1.0.5
	 */
	public function woocommerce_init() {
		// setup the tab actions
		if ( version_compare( WOOCOMMERCE_VERSION, "2.0" ) >= 0 ) {
			// WC 2.0+
			add_filter( 'woocommerce_product_tabs', array( $this, 'setup_tabs_2' ), 98 );
		} else {
			// WC < 2.0
			add_action( 'wp', array( $this, 'setup_tabs' ) );
		}
	}


	/**
	 * Function used to init WooCommerce Tab Manager Template Functions, making them pluggable by plugins and themes
	 */
	function include_template_functions() {
		include( 'woocommerce-tab-manager-template.php' );
	}


	/**
	 * Files required by both the admin and frontend
	 */
	private function includes() {
		if ( is_admin() ) $this->admin_includes();
		if ( defined( 'DOING_AJAX' ) ) $this->ajax_includes();
	}


	/**
	 * Include required admin files
	 */
	private function admin_includes() {
		include( 'admin/woocommerce-tab-manager-admin-init.php' );  // Admin section
	}


	/**
	 * Include required ajax files.
	 */
	private function ajax_includes() {
		include( 'woocommerce-tab-manager-ajax.php' ); // Ajax functions for admin and the front-end
	}


	/**
	 * Init WooCommerce Tab Manager user role
	 */
	private function init_user_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_tab_manager' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_tab_manager' );
		}
	}


	/**
	 * Init custom post type
	 */
	private function init_taxonomy() {
		if ( current_user_can( 'manage_woocommerce' ) )
			$show_in_menu = 'woocommerce';
		else
			$show_in_menu = true;

		register_post_type( "wc_product_tab",
			array(
				'labels' => array(
					'name'               => __( 'Tabs', self::TEXT_DOMAIN ),
					'singular_name'      => __( 'Tab', self::TEXT_DOMAIN ),
					'menu_name'          => _x( 'Tab Manager', 'Admin menu name', self::TEXT_DOMAIN ),
					'add_new'            => __( 'Add Tab', self::TEXT_DOMAIN ),
					'add_new_item'       => __( 'Add New Tab', self::TEXT_DOMAIN ),
					'edit'               => __( 'Edit', self::TEXT_DOMAIN ),
					'edit_item'          => __( 'Edit Tab', self::TEXT_DOMAIN ),
					'new_item'           => __( 'New Tab', self::TEXT_DOMAIN ),
					'view'               => __( 'View Tabs', self::TEXT_DOMAIN ),
					'view_item'          => __( 'View Tab', self::TEXT_DOMAIN ),
					'search_items'       => __( 'Search Tabs', self::TEXT_DOMAIN ),
					'not_found'          => __( 'No Tabs found', self::TEXT_DOMAIN ),
					'not_found_in_trash' => __( 'No Tabs found in trash', self::TEXT_DOMAIN ),
				),
				'description'     => __( 'This is where you can add new tabs that you can add to products.', self::TEXT_DOMAIN ),
				'public'          => true,
				'show_ui'         => true,
				'capability_type' => 'post',
				'capabilities' => array(
					'publish_posts'       => 'manage_woocommerce_tab_manager',
					'edit_posts'          => 'manage_woocommerce_tab_manager',
					'edit_others_posts'   => 'manage_woocommerce_tab_manager',
					'delete_posts'        => 'manage_woocommerce_tab_manager',
					'delete_others_posts' => 'manage_woocommerce_tab_manager',
					'read_private_posts'  => 'manage_woocommerce_tab_manager',
					'edit_post'           => 'manage_woocommerce_tab_manager',
					'delete_post'         => 'manage_woocommerce_tab_manager',
					'read_post'           => 'manage_woocommerce_tab_manager',
				),
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'show_in_menu'        => $show_in_menu,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title', 'editor' ),
				'show_in_nav_menus'   => false,
			)
		);
	}


	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @param array $actions associative array of action names to anchor tags
	 * @param string $plugin_file plugin file name, ie my-plugin/my-plugin.php
	 * @param array $plugin_data associative array of plugin data from the plugin file headers
	 * @param string $context plugin status context, ie 'all', 'active', 'inactive', 'recently_active'
	 *
	 * @return array associative array of plugin action links
	 */
	function plugin_manage_link( $actions, $plugin_file, $plugin_data, $context ) {

		// add a 'Configure' link to the front of the actions list for this plugin
		return array_merge( array( 'configure' => '<a href="' . admin_url( 'edit.php?post_type=wc_product_tab' ) . '">' . __( 'Configure', self::TEXT_DOMAIN ) . '</a>' ),
		                    $actions );

	}


	/** Frontend methods ******************************************************/


	/**
	 * Organizes the product tabs as configured within the Tab Manager
	 *
	 * $tabs structure:
	 * Array(
	 *   id => Array(
	 *     'title'    => (string) Tab title,
	 *     'priority' => (string) Tab priority,
	 *     'callback' => (mixed) callback function,
	 *   )
	 * )
	 *
	 * @since 1.0.5
	 * @param array $tabs array representing the product tabs
	 * @return array representing the product tabs
	 */
	public function setup_tabs_2( $tabs ) {
		global $product;

		$new_tabs = $tabs;

		$product_tabs = $this->get_product_tabs( $product->id );

		// if product tabs have been configured for this product or globally (otherwise, allow default behavior)
		if ( is_array( $product_tabs ) ) {

			// start fresh
			$new_tabs = array();

			// unhook and load any third party tabs that have been added by this point
			$third_party_tabs = $this->get_third_party_tabs_2( $tabs );

			foreach ( $product_tabs as $key => $tab ) {

				$priority = ( $tab['position'] + 1 ) * 10;
				$tab_id   = $tab['id'];

				if ( 'core' == $tab['type'] ) {

					// the core tabs can be suppressed for a variety of reasons: description tab due to no content, attributes tab due to no attributes, etc
					if ( ! isset( $tabs[ $tab_id ] ) ) continue;

					// set the review (comment) count for the reviews tab, if they used the '%d' substitution
					if ( 'reviews' == $tab['id'] && false !== strpos( $tab['title'], '%d' ) ) $tab['title'] = str_replace( '%d', get_comments_number( $product->id ), $tab['title'] );

					// add the core tab to the new tab set
					$new_tabs[ $tab_id ] = array(
						'title'    => $tab['title'],                 // modified title
						'priority' => $priority,                     // modified priority
						'callback' => $tabs[ $tab_id ]['callback'],  // get the core tab callback
					);

					// handle core tab headings (displays just before the tab content)
					if ( 'additional_information' == $tab_id ) {
						add_filter( 'woocommerce_product_additional_information_heading',   array( $this, 'core_tab_heading' ) );
					} elseif ( 'description' == $tab_id ) {
						add_filter( 'woocommerce_product_description_heading',   array( $this, 'core_tab_heading' ) );
					}

				} elseif ( 'third_party' == $tab['type'] ) {

					// third-party provided tab: ensure it's still available
					if ( ! isset( $third_party_tabs[ $key ] ) || ( isset( $third_party_tabs[ $key ]['ignore'] ) && true == $third_party_tabs[ $key ]['ignore'] ) ) continue;

					// add the 3rd party tab in with the new priority
					$new_tabs[ $tab_id ] = $third_party_tabs[ $key ];
					$new_tabs[ $tab_id ]['priority'] = $priority;

				} else {
					// product/global tabs

					// skip any global/product tabs that have been deleted
					$tab_post = get_post( $tab_id );

					if ( ! $tab_post || 'publish' != $tab_post->post_status || ! $tab_post->post_title ) continue;

					$new_tabs[ $tab['name'] ] = array(
						'title'    => $tab_post->post_title,
						'priority' => $priority,
						'callback' => 'woocommerce_tab_manager_tab_content',
						'id'       => $tab['id'],
					);

				}
			}

			// finally add in any non-managed 3rd party tabs with their own priority
			foreach ( $third_party_tabs as $key => $tab ) {
				if ( isset( $tab['ignore'] ) && true == $tab['ignore'] ) {
					$new_tabs[ $key ] = $tab;
				}
			}

		}

		return $new_tabs;
	}


	/**
	 * Add the tab/panel actions to render all tabs if the currrent page is
	 * a product page.
	 */
	public function setup_tabs() {

		if ( is_product() ) {
			global $post;

			$product_tabs = $this->get_product_tabs( $post->ID );

			// if product tabs have been configured for this product or globally (otherwise, allow default behavior)
			if ( is_array( $product_tabs ) ) {

				// remove woocommerce core tabs
				$this->remove_core_tab_actions();

				// unhook and load any third party tabs that have been added by this point
				$this->get_third_party_tabs();

				foreach ( $product_tabs as $key => $tab ) {

					$priority = ( $tab['position'] + 1 ) * 10;
					$tab_id   = $tab['id'];

					if ( 'core' == $tab['type'] ) {
						// in WC <= 2.0 the Additional Information tab was referred to internally as 'attributes'.  now it's 'additional_information'
						$action_tab_id = 'additional_information' == $tab_id ? 'attributes' : $tab_id;

						add_action( 'woocommerce_product_tabs',       'woocommerce_product_' . $action_tab_id . '_tab',   $priority );
						add_action( 'woocommerce_product_tab_panels', 'woocommerce_product_' . $action_tab_id . '_panel', $priority );

						if ( 'additional_information' == $tab_id ) {
							add_filter( 'woocommerce_product_additional_information_heading',   array( $this, 'core_tab_heading' ) );
						} elseif ( 'description' == $tab_id ) {
							add_filter( 'woocommerce_product_description_heading',   array( $this, 'core_tab_heading' ) );
						}

					} elseif ( 'third_party' == $tab['type'] ) {
						// third-party provided tab: ensure it's still available, and hook it up

						if ( isset( $this->third_party_tabs[ $key ] ) ) {
							add_action( 'woocommerce_product_tabs',       $this->third_party_tabs[ $key ]['woocommerce_product_tabs']['function'],       $priority );
							add_action( 'woocommerce_product_tab_panels', $this->third_party_tabs[ $key ]['woocommerce_product_tab_panels']['function'], $priority );
						}

					} else {
						// product/global tabs

						// skip any global/product tabs that have been deleted
						$tab_post = get_post( $tab_id );

						if ( ! $tab_post || 'publish' != $tab_post->post_status ) continue;

						// use an old-school create_function() to render the correct tab/panel
						//  becuase not everyone is on PHP 5.3 yet!!!
						$tab_callback       = create_function( null, 'return woocommerce_tab_manager_tab( "' . $tab_id . '" );' );
						$tab_panel_callback = create_function( null, 'return woocommerce_tab_manager_tab_panel( "' . $tab_id . '" );' );

						// add the tabs
						add_action( 'woocommerce_product_tabs',       $tab_callback,       $priority );
						add_action( 'woocommerce_product_tab_panels', $tab_panel_callback, $priority );

						// give other plugins the opportunity to unhook these anonymous functions
						do_action( 'woocommerce_tab_manager_tab_actions', $tab, $tab_callback, $tab_panel_callback, $priority );
					}
				}
			}
		}
	}


	/**
	 * Filter to modify the Description and Additional Information core tab headings.
	 * The heading is not what shows up in the "tab" itself, this is the
	 * heading for the tab content area.
	 *
	 * @param string $heading the tab heading
	 *
	 * @return string the tab heading
	 */
	public function core_tab_heading( $heading ) {

		global $product;
		$tabs = $this->get_product_tabs( $product->id );

		$current_filter = current_filter();

		if ( 'woocommerce_product_additional_information_heading' == $current_filter ) {
			return $tabs['core_tab_additional_information']['heading'];
		} elseif ( 'woocommerce_product_description_heading' == $current_filter ) {
			return $tabs['core_tab_description']['heading'];
		}

		return $heading;
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the identified product. Compatible with WC 2.0 and backwards
	 * compatible with previous versions
	 *
	 * @param int $product_id the product identifier
	 * @param array $args optional array of arguments
	 *
	 * @return WC_Product the product
	 */
	public function get_product( $product_id, $args = array() ) {
		$product = null;

		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
			// WC 2.0
			$product = get_product( $product_id, $args );
		} else {

			// old style, get the product or product variation object
			if ( isset( $args['parent_id'] ) && $args['parent_id'] ) {
				$product = new WC_Product_Variation( $product_id, $args['parent_id'] );
			} else {
				// get the regular product, but if it has a parent, return the product variation object
				$product = new WC_Product( $product_id );
				if ( $product->get_parent() ) {
					$product = new WC_Product_Variation( $product->id, $product->get_parent() );
				}
			}
		}

		return $product;
	}


	/**
	 * Get any third party tabs which have been added via the
	 * woocommerce_product_tabs action.  Any third party tabs so found are collected
	 * so they can be re-added by the manager in the appropriate order.  In the
	 * admin a human readable title is automatically generated (allowing for
	 * automatic integration of 3rd party plugin tabs) and three filters are
	 * fired to allow for improved integration with customized titles/descriptions:
	 *
	 * - woocommerce_tab_manager_integration_tab_allowed: allows plugin to mark tab as not available for management, its priority will not be modified and it will not appear within the Tab Manager Admin UI
	 * - woocommerce_tab_manager_integration_tab_title: allows plugin to provide a more descriptive tab title to display within the Tab Manager Admin UI
	 * - woocommerce_tab_manager_integration_tab_description: allows plugin to provide a description to display within the Tab Manager Admin UI
	 *
	 * $tabs structure:
	 * Array(
	 *   key => Array(
	 *     'title'       => (string) Tab title,
	 *     'priority'    => (string) Tab priority,
	 *     'callback'    => (mixed) callback function,
	 *     'description' => (string) An optional tab description added by this method to the return array,
	 *     'ignore'      => (boolean) Optional marker indicating this tab is not managed by the Tab Manager plugin and added by this method to the return array,
	 *     'id'          => (string) the original tab key,
	 *   )
	 * )
	 * Where key is: third_party_tab_{id}
	 *
	 * @since 1.0.5
	 * @param array $tabs optional array representing the product tabs
	 * @return array representing the product tabs
	 */
	public function get_third_party_tabs_2( $tabs = null ) {

		global $wp_filter;

		// gather the tabs if not provided
		if ( null === $tabs ) {
			$tabs = apply_filters( 'woocommerce_product_tabs', array() );
		}

		if ( null === $this->third_party_tabs ) {

			$this->third_party_tabs = array();

			// remove the core tabs (if any) leaving only 3rd party tabs (if any)
			unset( $tabs['additional_information'], $tabs['reviews'], $tabs['description'] );

			foreach ( $tabs as $key => $tab ) {

				// is this tab available for management by the Tab Manager plugin?
				if ( apply_filters( 'woocommerce_tab_manager_integration_tab_allowed', true, $tab ) ) {

					if ( is_admin() ) {
						if ( ! isset( $tab['title'] ) || ! $tab['title'] ) {
							// on the off chance that the 3rd party tab doesn't have a title, provide it a default one based on the callback so it can be identified within the admin

							// get a title for the tab.  Default to humanizing the function name, or class name
							if ( is_array( $tab['callback'] ) ) $tab_title = ( is_object( $tab['callback'][0] ) ? get_class( $tab['callback'][0] ) : $tab['callback'][0] );
							else $tab_title = (string) $tab['callback'];
							$tab_title = ucwords( str_replace( '_', ' ', $tab_title ) );
							$tab_title = str_ireplace( array( 'woocommerce', 'wordpress' ), array( 'WooCommerce', 'WordPress' ), $tab_title );  // fix some common words
							$tab['title'] = $tab_title;
						}

						// improved 3rd party integration by allowing plugins to provide a more descriptive title/description for their tabs
						$tab['title']       = apply_filters( 'woocommerce_tab_manager_integration_tab_title',       $tab['title'], $tab );
						$tab['description'] = apply_filters( 'woocommerce_tab_manager_integration_tab_description', '',            $tab );
					}

					$tab['id'] = $key;

				} else {
					// this tab is not managed by the Tab Manager, so mark it as such
					$tab['ignore'] = true;
				}

				// save the tab
				$this->third_party_tabs[ 'third_party_tab_' . $key ] = $tab;

			}

		}

		return $this->third_party_tabs;
	}


	/**
	 * Get any third party tabs which have been added prior to the wp action.
	 * This method detects third party tabs registered to the
	 * woocommerce_product_tabs and woocommerce_product_tab_panels filters.
	 * Any third party tabs so found are unhooked and collected into a
	 * datastructure containing the tab/panel callback function, a title and
	 * description.  A human readable title is automatically generated
	 * (allowing for automatic integration of 3rd party plugin tabs) and
	 * three filters are fired to allow for improved integration with
	 * customized titles/descriptions:
	 *
	 * - woocommerce_tab_manager_integration_tab_allowed: allows plugin to mark tab as not available for management, its actions will not be modified and it will not appear within the Tab Manager UI
	 * - woocommerce_tab_manager_integration_tab_title: allows plugin to provide a more descriptive tab title to display within the Tab Manager UI
	 * - woocommerce_tab_manager_integration_tab_description: allows plugin to provide a description to display within the Tab Manager UI
	 *
	 * @return array of third party tabs
	 */
	public function get_third_party_tabs() {

		global $wp_filter;

		if ( null === $this->third_party_tabs ) {

			$this->third_party_tabs = array();

			// remove woocommerce core tabs
			$this->remove_core_tab_actions();

			// 3rd party tabs detected
			if ( has_filter( 'woocommerce_product_tabs' ) && has_filter( 'woocommerce_product_tab_panels' ) ) {

				foreach ( $wp_filter['woocommerce_product_tabs'] as $priority => $tab_callbacks ) {

					foreach ( $tab_callbacks as $tab_callback ) {

						if ( isset( $wp_filter['woocommerce_product_tab_panels'][ $priority ] ) ) {
							// found a (hopefully) matching panel

							$tab_panel_callback = current( $wp_filter['woocommerce_product_tab_panels'][ $priority ] );

							// is this tab available?
							if ( apply_filters( 'woocommerce_tab_manager_integration_tab_allowed', true, $tab_callback['function'], $priority ) ) {

								$tab = array(
									'woocommerce_product_tabs'       => array( 'function' => $tab_callback['function'],       'priority' => $priority ),
									'woocommerce_product_tab_panels' => array( 'function' => $tab_panel_callback['function'], 'priority' => $priority )
								);

								// get a title for the tab.  Default to humanizing the function name, or class name - function name
								if ( is_array( $tab_callback['function'] ) ) $tab_title = ( is_object( $tab_callback['function'][0] ) ? get_class( $tab_callback['function'][0] ) : $tab_callback['function'][0] ) . ' - ' . $tab_callback['function'][1];
								else $tab_title = $tab_callback['function'];
								$tab_title = ucwords( str_replace( '_', ' ', $tab_title ) );
								$tab_title = str_ireplace( array( 'woocommerce', 'wordpress' ), array( 'WooCommerce', 'WordPress' ), $tab_title );  // fix some common words

								// improved 3rd party integration by allowing plugins to provide a more descriptive title/description for their tabs
								$tab['title']       = apply_filters( 'woocommerce_tab_manager_integration_tab_title',       $tab_title, $tab_callback['function'], $priority );
								$tab['description'] = apply_filters( 'woocommerce_tab_manager_integration_tab_description', '',         $tab_callback['function'], $priority );

								// use the default class/method-based title to identify the tab, which is less likely to change
								$tab['id'] = sanitize_title( $tab_title );
								$this->third_party_tabs[ 'third_party_tab_' . $tab['id'] ] = $tab;

								// remove the 3rd party tabs as we'll be adding them back later as needed, and where needed
								remove_action( 'woocommerce_product_tabs',       $tab_callback['function'],       $priority );
								remove_action( 'woocommerce_product_tab_panels', $tab_panel_callback['function'], $priority );
							} else {
								// tab was not enabled, according to the 3rd party plugin, so move along
								next( $wp_filter['woocommerce_product_tab_panels'][ $priority ] );
							}
						}
					}
				}
			}
		}

		return $this->third_party_tabs;
	}


	/**
	 * Get the default core tabs datastructure
	 *
	 * @return array the core tabs
	 */
	public function get_core_tabs() {
		// the core woocommerce tabs
		$core_tabs = array(
			'core_tab_description'            => array( 'id' => 'description',            'position' => 0, 'type' => 'core', 'title' => __( 'Description', 'woocommerce' ),            'description' => __( 'Displays the product content set in the main content editor.', 'woocommerce' ),                         'heading' => __( 'Product Description', 'woocommerce' ) ),
			'core_tab_additional_information' => array( 'id' => 'additional_information', 'position' => 1, 'type' => 'core', 'title' => __( 'Additional Information', 'woocommerce' ), 'description' => __( 'Displays the product attributes and properties configured in the Product Data panel.', 'woocommerce' ), 'heading' => __( 'Additional Information', 'woocommerce' ) ),
			'core_tab_reviews'                => array( 'id' => 'reviews',                'position' => 2, 'type' => 'core', 'title' => __( 'Reviews', 'woocommerce' ),                'description' => __( 'Displays the product review form and any reviews.', 'woocommerce' ) )
		);

		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
			// in WC 2.0+ the comment count portion of the Reviews tab title can be modified
			$core_tabs['core_tab_reviews']['title']       = __( 'Reviews (%d)', 'woocommerce' );
			$core_tabs['core_tab_reviews']['description'] = __( 'Displays the product review form and any reviews.  Use %d in the Title to substitute the number of reviews for the product.', 'woocommerce' );
		}

		return $core_tabs;
	}


	/**
	 * Remove the tab actions for the core Description, Additional Information and
	 * Reviews tabs
	 *
	 * WC < 2.0
	 */
	private function remove_core_tab_actions() {
		remove_action( 'woocommerce_product_tabs',       'woocommerce_product_description_tab',   10 );
		remove_action( 'woocommerce_product_tabs',       'woocommerce_product_attributes_tab',    20 );
		remove_action( 'woocommerce_product_tabs',       'woocommerce_product_reviews_tab',       30 );
		remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_description_panel', 10 );
		remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_attributes_panel',  20 );
		remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_reviews_panel',     30 );
	}


	/**
	 * Gets the product tabs (if any) for the identified product.  If not
	 * configured at the product level, the default layout (if any) will be
	 * returned.
	 *
	 * returned tabs structure:
	 * Array(
	 *   key => Array(
	 *     'position' => (int) 0-indexed ordered position from the Tab Manager Admin UI,
	 *     'type'     => (string) one of 'core', 'global', 'third_party' or 'product',
	 *     'id'       => (string) Tab identifier, ie 'description', 'reviews', 'additional_information' for the core tabs, post id for product/global, and woocommerce_product_tabs key for third party tabs,
	 *     'title'    => (string) The tab title to display on the frontend (not used for 3rd party tabs, though with WC 2.0 it could be),
	 *     'heading'  => (string) Tab heading (core description/additional_information tabs only),
	 *     'name'     => (string) Product/Global tabs only, this is the sanitized title, and is used to key the tab in the final woocommerce tab data structure,
	 *   )
	 * )
	 * Where key is: {type}_tab_{id}
	 *
	 * @param int $product_id product identifier
	 *
	 * @return array product tabs data
	 */
	public function get_product_tabs( $product_id ) {

		if ( ! isset( $this->product_tabs[ $product_id ] ) ) {

			$override_tab_layout = get_post_meta( $product_id, '_override_tab_layout', true );

			if ( 'yes' == $override_tab_layout ) {
				// product defines its own tab layout?
				$this->product_tabs[ $product_id ] = get_post_meta( $product_id, '_product_tabs', true );
			} else {
				// otherwise, get the default layout if any
				$this->product_tabs[ $product_id ] = get_option( 'wc_tab_manager_default_layout', false );
			}
		}

		return $this->product_tabs[ $product_id ];
	}


	/**
	 * Gets the product tab or null if the tab cannot be found
	 *
	 * @param int $product_id product identifier
	 * @param int $tab_id tab identifier
	 * @param boolean $get_the_content whether to get the tab content and title
	 *
	 * @return array tab array, or null
	 */
	public function get_product_tab( $product_id, $tab_id, $get_the_content = false ) {

		// load the tabs
		$this->get_product_tabs( $product_id );

		if ( is_array( $this->product_tabs[ $product_id ] ) ) {

			foreach ( $this->product_tabs[ $product_id ] as $id => $tab ) {

				if ( $tab['id'] == $tab_id ) {

					// get the tab content, if needed
					if ( $get_the_content && ! isset( $tab['content'] ) ) {

						$tab_post = get_post( $tab_id );
						$content = apply_filters( 'the_content', $tab_post->post_content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						$this->product_tabs[ $product_id ][ $id ]['content'] = $content;

						$this->product_tabs[ $product_id ][ $id ]['title'] = $tab_post->post_title;
					}

					return $this->product_tabs[ $product_id ][ $id ];
				}
			}
		}

		return null;
	}


	/**
	 * Get the plugin url, ie http://127.0.0.1/wp-content/plugins/woocommerce-tab-manager
	 *
	 * @return string the plugin url
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
	}


	/**
	 * Get the absolute path to this plugin directory
	 *
	 * @return string plugin path
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		// gets the absolute path to this plugin directory
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	private function install() {

		global $wpdb;

		$installed_version = get_option( self::VERSION_OPTION_NAME );

		if ( false == $installed_version ) {
			// initial installation

			// any Custom Product Lite Tabs?
			$results = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='frs_woo_product_tabs'" );

			// prepare the core tabs
			$core_tabs = $this->get_core_tabs();
			foreach ( $core_tabs as $id => $tab ) {
				unset( $core_tabs[ $id ]['description'] );
			}

			// foreach product with a custom lite tab
			foreach ( $results as $result ) {

				$old_tabs = maybe_unserialize( $result->meta_value );

				$new_tabs = array( 'core_tab_description' => $core_tabs['core_tab_description'], 'core_tab_additional_information' => $core_tabs['core_tab_additional_information'] );

				// keep track of tab names to avoid clashes
				$found_names = array( 'description' => 1, 'additional_information' => 1, 'reviews' => 1 );

				foreach ( $old_tabs as $tab ) {

					if ( $tab['title'] && $tab['content'] ) {
						// create the product tab

						$new_tab = array( 'position' => count( $new_tabs ), 'type' => 'product' );

						$new_tab_data = array(
							'post_title'    => $tab['title'],
							'post_content'  => $tab['content'],
							'post_status'   => 'publish',
							'ping_status'   => 'closed',
							'post_author'   => get_current_user_id(),
							'post_type'     => 'wc_product_tab',
							'post_parent'   => $result->post_id,
							'post_password' => uniqid( 'tab_' ), // Protects the post just in case
						);

						// create the post and get the id
						$id = wp_insert_post( $new_tab_data );
						$new_tab['id'] = $id;

						// determine the unique tab name
						$tab_name = sanitize_title( $tab['title'] );
						if ( ! isset( $found_names[ $tab_name ] ) ) $found_names[ $tab_name ] = 1;
						else $found_names[ $tab_name ]++;
						if ( $found_names[ $tab_name ] > 1 ) $tab_name .= '-' . ( $found_names[ $tab_name ] - 1 );
						$new_tab['name'] = $tab_name;

						// tab is complete
						$new_tabs[ 'product_tab_' . $id ] = $new_tab;
					}
				}

				// add the core reviews tab on at the end
				$new_tabs['core_tab_reviews'] = $core_tabs['core_tab_reviews'];
				$new_tabs['core_tab_reviews']['position'] = count( $new_tabs ) - 1;


				if ( count( $new_tabs ) > 3 ) {
					// if we actually had any product tabs
					add_post_meta( $result->post_id, '_product_tabs',        $new_tabs, true );
					add_post_meta( $result->post_id, '_override_tab_layout', 'yes',     true );
				}
			}

			// new db version
			update_option( self::VERSION_OPTION_NAME, self::VERSION );
		}


		// installed version lower than plugin version?
		if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
			$this->upgrade( $installed_version );

			// new version number
			update_option( self::VERSION_OPTION_NAME, self::VERSION );
		}
	}



	/**
	 * Run when plugin version number changes
	 */
	private function upgrade( $installed_version ) {
		global $wpdb;

		if ( version_compare( $installed_version, "1.0.4.1" ) <= 0 ) {
			// in this version and before:
			// * custom product lite tabs were imported but their status was set
			//   to 'future' meaning they appeared in the Tab Manager menu, but
			//   not at the product level
			// * product tab layout had 'tab_name' rather than 'name' for imported
			//   custom product lite tabs
			// * imported custom product lite tabs attached to products did not
			//   have the '_override_tab_layout' meta set
			$tabs = get_posts( array( 'numberposts' => '', 'post_type' => 'wc_product_tab', 'nopaging' => true, 'post_status' => 'future' ) );
			if ( is_array( $tabs ) ) {
				foreach( $tabs as $tab ) {
					// make the tab post status 'publish'
					$update_tab = array(
						'ID'          => $tab->ID,
						'post_status' => 'publish',
					);
					wp_update_post( $update_tab );
					add_post_meta( $tab->ID, '_migrated_future', 'yes' );  // mark the tab as migrated, in case we need to reference them one day

					// fix the product tab layout 'tab_name' field, which should be 'name'
					$fixed = false;
					$product_tabs = get_post_meta( $tab->post_parent, '_product_tabs', true );
					foreach ( $product_tabs as $index => $product_tab ) {
						if ( isset( $product_tab['tab_name'] ) && $product_tab['tab_name'] && ! isset( $product_tab['name'] ) ) {
							$product_tabs[ $index ]['name'] = $product_tab['tab_name'];
							unset( $product_tabs[ $index ]['tab_name'] );
							$fixed = true;
						}
					}
					if ( $fixed ) {
						update_post_meta( $tab->post_parent, '_product_tabs', $product_tabs );
					}

					// It seems that setting the tab layout override in existing stores would be too dangerous, so for now the following is not used
					// enable the product '_override_tab_layout' so the product tab is actually used
					// update_post_meta( $tab->post_parent, '_override_tab_layout', 'yes' );
				}
			}
			unset( $tabs );
		}

		// In version 1.0.5 the core tab previously referred to as 'attributes' now
		//  needs to be referrred to as 'additional_information' for consistency with
		//  WC 2.0+, so fix the global and any product tab layouts
		if ( version_compare( $installed_version, "1.0.5" ) <= 0 ) {

			// fix global tab layout
			$tab_layout = get_option( 'wc_tab_manager_default_layout', false );
			if ( $tab_layout && isset( $tab_layout['core_tab_attributes'] ) ) {
				$tab_layout['core_tab_additional_information'] = $tab_layout['core_tab_attributes'];
				$tab_layout['core_tab_additional_information']['id'] = 'additional_information';
				unset( $tab_layout['core_tab_attributes'] );

				update_option( 'wc_tab_manager_default_layout', $tab_layout );
			}

			// fix any product-level tab layouts
			$results = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_product_tabs'" );

			if ( is_array( $results ) ) {
				foreach ( $results as $row ) {
					$tab_layout = maybe_unserialize( $row->meta_value );

					if ( $tab_layout && isset( $tab_layout['core_tab_attributes'] ) ) {
						$tab_layout['core_tab_additional_information'] = $tab_layout['core_tab_attributes'];
						$tab_layout['core_tab_additional_information']['id'] = 'additional_information';
						unset( $tab_layout['core_tab_attributes'] );

						update_post_meta( $row->post_id, '_product_tabs', $tab_layout );
					}
				}
			}
			unset( $results );
		}
	}

} // WC_Tab_Manager
