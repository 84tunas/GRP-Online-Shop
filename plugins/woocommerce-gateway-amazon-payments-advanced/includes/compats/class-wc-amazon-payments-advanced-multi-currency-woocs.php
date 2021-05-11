<?php
/**
 * Main class to handle WOOCS – Currency Switcher for WooCommerce compatibility.
 * https://wordpress.org/plugins/woocommerce-currency-switcher/
 * Tested up to: 1.2.7
 *
 * @package WC_Gateway_Amazon_Pay\Compats
 */

/**
 * WooCommerce WOOCS – Currency Switcher for WooCommerce Multi-currency compatibility.
 */
class WC_Amazon_Payments_Advanced_Multi_Currency_Woocs extends WC_Amazon_Payments_Advanced_Multi_Currency_Abstract {

	/**
	 * Holds WOOCS instance.
	 *
	 * @var WOOCS
	 */
	protected $woocs;

	/**
	 * Specify hooks where compatibility action takes place.
	 */
	public function __construct() {
		global $WOOCS;
		$this->woocs = $WOOCS;
		// Option woocs_restrike_on_checkout_page === 1 will hide switcher on checkout.
		add_filter( 'option_woocs_restrike_on_checkout_page', array( $this, 'remove_currency_switcher_on_order_reference_suspended' ) );
		add_filter( 'init', array( $this, 'remove_shortcode_currency_switcher_on_order_reference_suspended' ) );

		parent::__construct();
	}


	/**
	 * Get Woocs selected currency.
	 *
	 * @return string
	 */
	public function get_selected_currency() {
		return $this->woocs->current_currency;
	}

	/**
	 * Woocs has 2 ways of work:
	 * Settings > Advanced > Is multiple allowed
	 * If it is set, users will pay on selected currency (where we hook)
	 * otherwise it will just change currency on frontend, but order will be taken on original shop currency.
	 *
	 * @return bool
	 */
	public function is_front_end_compatible() {
		return get_option( 'woocs_is_multiple_allowed' ) ? false : true;
	}

	/**
	 * On OrderReferenceStatus === Suspended, hide currency switcher.
	 */
	public function remove_currency_switcher_on_order_reference_suspended( $value ) {
		if ( $this->is_order_reference_checkout_suspended() ) {
			// By Pass Multi-currency, so we don't trigger a new set_order_reference_details on process_payment
			$this->bypass_currency_session();
			return 1;
		}
		return $value;
	}

	/**
	 * On OrderReferenceStatus === Suspended, hide currency switcher.
	 */
	public function remove_shortcode_currency_switcher_on_order_reference_suspended( $value ) {
		if ( $this->is_order_reference_checkout_suspended() ) {
			// By Pass Multi-currency, so we don't trigger a new set_order_reference_details on process_payment
			$this->bypass_currency_session();
			remove_shortcode( 'woocs' );
		}
	}

}
