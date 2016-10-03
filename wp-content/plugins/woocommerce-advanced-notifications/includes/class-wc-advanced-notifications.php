<?php

/**
 * WC_Advanced_Notifications class.
 */
class WC_Advanced_Notifications {

	var $mailer;
	var $admin;
	var $plugin_path;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Admin section
		if ( is_admin() ) {
			include_once( 'class-wc-advanced-notifications-admin.php' );
			$this->admin = new WC_Advanced_Notifications_Admin();
		}

		include_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );

		if ( ! class_exists( 'Emogrifier' ) && class_exists( 'DOMDocument' ) ) {
			include_once(  WC()->plugin_path() . '/includes/libraries/class-emogrifier.php' );
		}

		// Hook emails
		add_action( 'woocommerce_order_status_pending_to_processing', array( $this, 'new_order' ) );
		add_action( 'woocommerce_order_status_pending_to_completed', array( $this, 'new_order' ) );
		add_action( 'woocommerce_order_status_pending_to_on-hold', array( $this, 'new_order' ) );
		add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'new_order' ) );
		add_action( 'woocommerce_order_status_failed_to_completed', array( $this, 'new_order' ) );
		add_action( 'woocommerce_low_stock_notification', array( $this, 'low_stock' ), 1, 2 );
		add_action( 'woocommerce_no_stock_notification', array( $this, 'out_of_stock' ), 1, 2 );
		add_action( 'woocommerce_product_on_backorder_notification', array( $this, 'backorder' ), 1, 2 );
	}


	/**
	 * Get the plugin path
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
	}


	/**
	 * get_notifcations_for_order function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_notifcations_for_order( $order ) {
		global $wpdb;

		$notifications = array();
		$product_ids = $shipping_classes = $product_cats = array( 0 );

		// Check line items
		foreach( $order->get_items() as $item ) {

			$_product = $order->get_product_from_item( $item );

			// Product ID
			$product_ids[] = $_product->id;

			// Class IDs
			$shipping_classes[] = $_product->get_shipping_class_id();

			// Cats
			$product_cats = array_merge( $product_cats, wp_get_post_terms( $_product->id, 'product_cat', array( "fields" => "ids" ) ) );
		}

		$product_ids       = array_map( 'intval', $product_ids );
		$shipping_classes  = array_map( 'intval', $shipping_classes );
		$product_cats      = array_map( 'intval', $product_cats );

		// Get notifications which match
		$notifications = $wpdb->get_results( "
			SELECT * FROM {$wpdb->prefix}advanced_notifications
			LEFT JOIN {$wpdb->prefix}advanced_notification_triggers ON ( {$wpdb->prefix}advanced_notifications.notification_id = {$wpdb->prefix}advanced_notification_triggers.notification_id )
			WHERE (
				object_id = 0
				OR
					( object_type = 'product_cat' AND object_id IN ( " . implode( ',', $product_cats ) . " ) )
				OR
					( object_type = 'product_shipping_class' AND object_id IN ( " . implode( ',', $shipping_classes ) . " ) )
				OR
					( object_type = 'product' AND object_id IN ( " . implode( ',', $product_ids ) . " ) )
			)
			GROUP BY {$wpdb->prefix}advanced_notifications.notification_id
		" );

		if ( $notifications ) {
			foreach ( $notifications as $key => $notification ) {

				 if ( ! in_array( 'purchases', maybe_unserialize( $notification->notification_type ) ) ) {
				 	unset( $notifications[ $key ] );
				 	continue;
				 }

				 $product_ids = $shipping_classes = $product_cats = array();

				 $triggers = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}advanced_notification_triggers WHERE notification_id = %d", $notification->notification_id ) );

				 $all = false;

				 if ( $triggers ) {
					 foreach( $triggers as $trigger ) {
					 	switch( $trigger->object_type ) {
						 	case 'product' :
						 		$product_ids[] = $trigger->object_id;
						 	break;
						 	case 'product_cat' :
						 		$product_cats[] = $trigger->object_id;
						 	break;
						 	case 'product_shipping_class' :
						 		$shipping_classes[] = $trigger->object_id;
						 	break;
					 	}

					 	if ( $trigger->object_id == '0' )
					 		$all = true;
					 }
				 }

				 $notification->triggers = array(
				 	'product_ids'      => $product_ids,
				 	'shipping_classes' => $shipping_classes,
				 	'product_cats'     => $product_cats,
				 	'all'              => $all
				 );
			}
		}

		return $notifications;
	}


	/**
	 * get_notifcations_for_product function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_notifcations_for_product( $product, $type = 'low_stock' ) {
		global $wpdb;

		$notifications = array();
		$product_ids = $shipping_classes = $product_cats = array( 0 );

		// Product ID
		$product_ids[] = $product->id;

		// Class IDs
		$shipping_classes[] = $product->get_shipping_class_id();

		// Cats
		$product_cats = array_merge( $product_cats, wp_get_post_terms( $product->id, 'product_cat', array( "fields" => "ids" ) ) );

		$product_ids      = array_map( 'intval', $product_ids );
		$shipping_classes = array_map( 'intval', $shipping_classes );
		$product_cats     = array_map( 'intval', $product_cats );

		// Get notifications which match
		$notifications = $wpdb->get_results( "
			SELECT * FROM {$wpdb->prefix}advanced_notifications
			LEFT JOIN {$wpdb->prefix}advanced_notification_triggers ON ( {$wpdb->prefix}advanced_notifications.notification_id = {$wpdb->prefix}advanced_notification_triggers.notification_id )
			WHERE (
				object_id = 0
				OR
					( object_type = 'product_cat' AND object_id IN ( " . implode( ',', $product_cats ) . " ) )
				OR
					( object_type = 'product_shipping_class' AND object_id IN ( " . implode( ',', $shipping_classes ) . " ) )
				OR
					( object_type = 'product' AND object_id IN ( " . implode( ',', $product_ids ) . " ) )
			)
			GROUP BY {$wpdb->prefix}advanced_notifications.notification_id
		" );

		if ( $notifications ) {
			foreach ( $notifications as $key => $notification ) {

				 if ( ! in_array( $type, maybe_unserialize( $notification->notification_type ) ) ) {
				 	unset( $notifications[ $key ] );
				 	continue;
				 }

				 $product_ids = $shipping_classes = $product_cats = array();

				 $triggers = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}advanced_notification_triggers WHERE notification_id = %d", $notification->notification_id ) );

				 $all = false;

				 if ( $triggers ) {
					 foreach( $triggers as $trigger ) {
					 	switch( $trigger->object_type ) {
						 	case 'product' :
						 		$product_ids[] = $trigger->object_id;
						 	break;
						 	case 'product_cat' :
						 		$product_cats[] = $trigger->object_id;
						 	break;
						 	case 'product_shipping_class' :
						 		$shipping_classes[] = $trigger->object_id;
						 	break;
					 	}

					 	if ( $trigger->object_id == '0' )
					 		$all = true;
					 }
				 }

				 $notification->triggers = array(
				 	'product_ids'      => $product_ids,
				 	'shipping_classes' => $shipping_classes,
				 	'product_cats'     => $product_cats,
				 	'all'              => $all
				 );
			}
		}

		return $notifications;
	}

	/**
	 * sends an email through WooCommerce with the correct headers
	 */
	public function send( $id, $object, $is_plain_text, $to, $subject, $message ) {
		$email = new WC_Email();

		if ( $is_plain_text ) {
			$message = wordwrap( strip_tags( $message ), 60 );
			$email->email_type = 'plain';
		} else {
			$email->email_type = 'html';
		}

		$email->send( $to, $subject, $message, $email->get_headers(), $email->get_attachments() );
	}

	/**
	 * new_order function.
	 *
	 * @access public
	 * @return void
	 */
	public function new_order( $order_id ) {
		global $woocommerce, $wpdb;

		$order = new WC_Order( $order_id );

		// Get notifications
		$notifications = $this->get_notifcations_for_order( $order );

		if ( $notifications ) {

			// Prepare email
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			$email_heading = __( 'New Customer Order', 'woocommerce-advanced-notifications' );

			$subject = apply_filters( 'woocommerce_email_subject_new_order', sprintf( __( '[%s] New Order (%s)', 'woocommerce-advanced-notifications' ), $blogname, $order->get_order_number() ), $order );

			foreach ( $notifications as $notification ) {

				// load the mailer class
				$mailer = WC()->mailer();

				// Buffer
				ob_start();

				// Get mail template
				woocommerce_get_template( $notification->notification_plain_text ? 'emails/new_order_plain.php' : 'emails/new_order.php', array(
					'order'          => $order,
					'email_heading'  => $email_heading,
					'recipient_name' => $notification->recipient_name,
					'show_totals'    => $notification->notification_totals,
					'show_prices'    => $notification->notification_prices,
					'triggers'       => $notification->triggers,
					'blogname'       => $blogname
				), 'woocommerce-advanced-notifications/', $this->plugin_path() . '/templates/' );

				// Get contents
				$message = ob_get_clean();

				$wc_email = new WC_Email();

				// wrap the content with the email template and then add styles
				$message = $wc_email->style_inline( $mailer->wrap_message( $email_heading, $message ) );

				// Send the email
				$this->send( 'new_order', $order, $notification->notification_plain_text, $notification->recipient_email, $subject, $message );

				// Increase count
				$wpdb->update(
					"{$wpdb->prefix}advanced_notifications",
					array( 'notification_sent_count' =>  ( $notification->notification_sent_count + 1 ) ),
					array( 'notification_id' => $notification->notification_id )
				);
			}

		}
	}


	/**
	 * low_stock function.
	 *
	 * @access public
	 * @param mixed $product
	 * @return void
	 */
	public function low_stock( $product ) {
		global $woocommerce, $wpdb;

		// Get notifications
		$notifications = $this->get_notifcations_for_product( $product, 'low_stock' );

		if ( $notifications ) {

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			$subject = apply_filters( 'woocommerce_email_subject_low_stock', sprintf( '[%s] %s', $blogname, __( 'Product low in stock', 'woocommerce' ) ), $product );

			$sku = ($product->sku) ? '(' . $product->sku . ') ' : '';

			if ( ! empty( $product->variation_id ) )
				$title = sprintf(__('Variation #%s of %s', 'woocommerce'), $product->variation_id, get_the_title($product->id)) . ' ' . $sku;
			else
				$title = sprintf(__('Product #%s - %s', 'woocommerce'), $product->id, get_the_title($product->id)) . ' ' . $sku;

			foreach ( $notifications as $notification ) {

				$message = sprintf( __( 'Hi %s,', 'woocommerce-advanced-notifications' ), $notification->recipient_name ) . "\n\n";

				$message .= $title . __('is low in stock.', 'woocommerce') . "\n\n";

				$message .= "Regards,\n" . $blogname;

				// Send the email
				$this->send( 'low_stock', $product, true, $notification->recipient_email, $subject, $message );

				// Increase count
				$wpdb->update(
					"{$wpdb->prefix}advanced_notifications",
					array( 'notification_sent_count' =>  ( $notification->notification_sent_count + 1 ) ),
					array( 'notification_id' => $notification->notification_id )
				);
			}
		}
	}

	/**
	 * out_of_stock function.
	 *
	 * @access public
	 * @param mixed $product
	 * @return void
	 */
	public function out_of_stock( $product ) {
		global $woocommerce, $wpdb;

		// Get notifications
		$notifications = $this->get_notifcations_for_product( $product, 'out_of_stock' );

		if ( $notifications ) {

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			$subject = apply_filters( 'woocommerce_email_subject_no_stock', sprintf( '[%s] %s', $blogname, __( 'Product out of stock', 'woocommerce' ) ), $product );

			$sku = ($product->sku) ? '(' . $product->sku . ') ' : '';

			if ( ! empty( $product->variation_id ) )
				$title = sprintf(__('Variation #%s of %s', 'woocommerce'), $product->variation_id, get_the_title($product->id)) . ' ' . $sku;
			else
				$title = sprintf(__('Product #%s - %s', 'woocommerce'), $product->id, get_the_title($product->id)) . ' ' . $sku;

			foreach ( $notifications as $notification ) {

				$message = sprintf( __( 'Hi %s,', 'woocommerce-advanced-notifications' ), $notification->recipient_name ) . "\n\n";

				$message .= $title . __('is out of stock.', 'woocommerce') . "\n\n";

				$message .= "Regards,\n" . $blogname;

				// Send the email
				$this->send( 'no_stock', $product, true, $notification->recipient_email, $subject, $message );

				// Increase count
				$wpdb->update(
					"{$wpdb->prefix}advanced_notifications",
					array( 'notification_sent_count' =>  ( $notification->notification_sent_count + 1 ) ),
					array( 'notification_id' => $notification->notification_id )
				);
			}

		}
	}

	/**
	 * backorder function.
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function backorder( $args ) {

		$defaults = array(
			'product'  => '',
			'quantity' => '',
			'order_id' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		if ( ! $product || ! $quantity ) {
			return;
		}

		global $woocommerce, $wpdb;

		// Get notifications
		$notifications = $this->get_notifcations_for_product( $product, 'out_of_stock' );

		if ( $notifications ) {

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			$subject = apply_filters( 'woocommerce_email_subject_backorder', sprintf( '[%s] %s', $blogname, __( 'Product Backorder', 'woocommerce' ) ), $product );

			$sku = ($product->sku) ? ' (' . $product->sku . ')' : '';

			if ( ! empty( $product->variation_id ) )
				$title = sprintf(__('Variation #%s of %s', 'woocommerce'), $product->variation_id, get_the_title($product->id)) . $sku;
			else
				$title = sprintf(__('Product #%s - %s', 'woocommerce'), $product->id, get_the_title($product->id)) . $sku;

			foreach ( $notifications as $notification ) {

				$message = sprintf( __( 'Hi %s,', 'woocommerce-advanced-notifications' ), $notification->recipient_name ) . "\n\n";

				$message .= sprintf(__('%s units of %s have been backordered in order #%s.', 'woocommerce'), $quantity, $title, $order_id ) . "\n\n";

				$message .= "Regards,\n" . $blogname;

				// Send the email
				$this->send( 'backorder', $product, true, $notification->recipient_email, $subject, $message );

				// Increase count
				$wpdb->update(
					"{$wpdb->prefix}advanced_notifications",
					array( 'notification_sent_count' =>  ( $notification->notification_sent_count + 1 ) ),
					array( 'notification_id' => $notification->notification_id )
				);
			}
		}
	}

}

$GLOBALS['wc_advanced_notifications'] = new WC_Advanced_Notifications();
