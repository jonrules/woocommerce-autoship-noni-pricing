<?php
/*
Plugin Name: WC Auto-Ship Noni Pricing
Plugin URI: http://patternsinthecloud.com
Description: Custom autoship pricing rules for Formula 1 Noni products
Version: 1.0
Author: Patterns in the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
	/**
	 * Activate hook
	 */
	function wc_autoship_noni_pricing_activate() {
		
	}
	register_activation_hook( __FILE__, 'wc_autoship_noni_pricing_activate' );
	
	/**
	 * Deactivate hook
	 */
	function wc_autoship_noni_pricing_deactivate() {
		
	}
	register_deactivation_hook( __FILE__, 'wc_autoship_noni_pricing_deactivate' );
	
	/**
	 * Uninstall hook
	 */
	function wc_autoship_noni_pricing_uninstall() {
		
	}
	register_uninstall_hook( __FILE__, 'wc_autoship_noni_pricing_uninstall' );
	
	/**
	 * Get autoship price for current role
	 * @param double $autoship_price
	 * @param int $product_id
	 */
	function wc_autoship_noni_pricing_current_role( $autoship_price, $product_id, $autoship_frequency, $customer_id ) {
		if ( empty( $customer_id ) ) {
			// User is not logged in
			return $autoship_price;
		}
		// Get current user
		$user = get_user_by( 'id', $customer_id );
		if ( ! $user->ID ) {
			// User is not logged in
			return $autoship_price;
		}
		// Map product prices to roles
		// sorted from lowest price to highest price
		$prices = array(
			'F1-0001' => array( 'salesrep' => 35.0 ),
			'F1-0002' => array( 'salesrep' => 45.0 ),
			'F1-0004' => array( 'salesrep' => 0.0 ),
			'F1-0008' => array( 'salesrep' => 140.0 ),
			'F1-0009' => array( 'salesrep' => 180.0 ),
			'F1-0011' => array( 'salesrep' => 8.0 ),
			'F1-0012' => array( 'salesrep' => 12.0 ),
			'F1-0021' => array( 'salesrep' => 16.0 ),
			'F1-0022' => array( 'salesrep' => 20.0 )
		);
		// Get product
		$product = wc_get_product( $product_id );
		$product_sku = $product->get_sku();
		if ( ! isset( $prices[ $product_sku ] ) ) {
			// No prices set, return default
			return $autoship_price;
		}
		foreach ( $prices[ $product_sku ] as $role => $price ) {
			if ( in_array( $role, $user->roles ) ) {
				// User has role
				return $price;
			}
		}
		// Default
		return $autoship_price;
	}
	add_filter( 'wc_autoship_price', 'wc_autoship_noni_pricing_current_role', 10, 4 );
}
