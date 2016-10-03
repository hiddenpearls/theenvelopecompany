<?php

class WC_TA_Admin_EUexempt {

	/**
	 * The setup method
	 */
	public function setup() {
		// Simple products
		add_filter( 'product_type_options', array( $this, 'display_euexempt_field' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_euexempt_field' ) );

		// Variable products
		add_action( 'woocommerce_variation_options', array( $this, 'display_variation_euexempt_field' ), 10, 3 );

		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_euexempt_field' ), 10, 2 );
	}

	/**
	 * Display the EU Exempt field
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function display_euexempt_field( $options ) {
		$options['euexempt'] = array(
			'id'            => '_euexempt',
			'wrapper_class' => 'show_if_virtual',
			'label'         => __( 'EU Exempt', 'woocommerce-taxamo' ),
			'description'   => __( 'A virtual product that is exempt from EU VAT.', 'woocommerce-taxamo' ),
			'default'       => 'no'
		);

		return $options;
	}

	/**
	 * Display the euexempt field for variations
	 *
	 * @param $loop
	 * @param $variation_data
	 * @param $variation
	 */
	public function display_variation_euexempt_field( $loop, $variation_data, $variation ) {
		$is_euexempt = ( isset( $variation_data['_euexempt'] ) && 'yes' == $variation_data['_euexempt'][0] );
		echo '<label><input type="checkbox" class="checkbox variable_euexempt" name="variable_is_euexempt[' . $loop . ']"' . checked( true, $is_euexempt, false ) . '> EU Exempt</label>' . PHP_EOL;
	}

	/**
	 * Save the EU Exempt field
	 *
	 * @param $post_id
	 */
	public function save_euexempt_field( $post_id ) {
		$is_euexempt = isset( $_POST['_euexempt'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_euexempt', $is_euexempt );

		// If it's an euexempt, it's also a virtual product
		if ( 'yes' == $is_euexempt ) {
			update_post_meta( $post_id, '_virtual', 'yes' );
		}
	}

	/**
	 * Save the variation EU Exempt field
	 *
	 * @param $variation_id
	 * @param $i
	 */
	public function save_variation_euexempt_field( $variation_id, $i ) {
		$is_euexempt = isset( $_POST['variable_is_euexempt'][ $i ] ) ? 'yes' : 'no';
		update_post_meta( $variation_id, '_euexempt', $is_euexempt );

		// If it's an euexempt, it's also a virtual product
		if ( 'yes' == $is_euexempt ) {
			update_post_meta( $variation_id, '_virtual', 'yes' );
		}
	}

}