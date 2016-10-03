<?php

class WC_TA_Admin_Ebook {

	/**
	 * The setup method
	 */
	public function setup() {
		// Simple products
		add_filter( 'product_type_options', array( $this, 'display_ebook_field' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_ebook_field' ) );

		// Variable products
		add_action( 'woocommerce_variation_options', array( $this, 'display_variation_ebook_field' ), 10, 3 );

		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_ebook_field' ), 10, 2 );
	}

	/**
	 * Display the E-Book field
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function display_ebook_field( $options ) {
		$options['ebook'] = array(
			'id'            => '_ebook',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'E-Book', 'woocommerce-taxamo' ),
			'description'   => __( 'E-books are always virtual products but may have different tax rules.', 'woocommerce-taxamo' ),
			'default'       => 'no'
		);

		return $options;
	}

	/**
	 * Display the ebook field for variations
	 *
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 */
	public function display_variation_ebook_field( $loop, $variation_data, $variation ) {
		$is_ebook = ( isset( $variation_data['_ebook'] ) && 'yes' == $variation_data['_ebook'][0] );
		echo '<label><input type="checkbox" class="checkbox variable_ebook" name="variable_is_ebook[' . $loop . ']"' . checked( true, $is_ebook, false ) . '> E-Book</label>' . PHP_EOL;
	}

	/**
	 * Save the E-Book field
	 *
	 * @param $post_id
	 */
	public function save_ebook_field( $post_id ) {
		$is_ebook = isset( $_POST['_ebook'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_ebook', $is_ebook );

		// If it's an ebook, it's also a virtual product
		if ( 'yes' == $is_ebook ) {
			update_post_meta( $post_id, '_virtual', 'yes' );
		}
	}

	/**
	 * Save the variation E-Book field
	 *
	 * @param $variation_id
	 * @param $i
	 */
	public function save_variation_ebook_field( $variation_id, $i ) {
		$is_ebook = isset( $_POST['variable_is_ebook'][ $i ] ) ? 'yes' : 'no';
		update_post_meta( $variation_id, '_ebook', $is_ebook );

		// If it's an ebook, it's also a virtual product
		if ( 'yes' == $is_ebook ) {
			update_post_meta( $variation_id, '_virtual', 'yes' );
		}
	}

}