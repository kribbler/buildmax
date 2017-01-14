<?php
/*
Plugin Name: WooCommerce Payment Express Gateway
Plugin URI: http://woothemes.com/woocommerce
Description: A payment gateway for Payment Express. Uses PX-Pay method. Version Date: 8 August 2013
Version: 1.6.0
Author: OPMC
Author URI: http://www.opmc.com.au/
*/

/*  Copyright 2011  Garman Technical Services  (email : contact@garmantech.com)

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

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '698f37b20ad1d121c3f14fe2b2f8c104', '18640' );

require_once("includes/pxpay.inc.php");

add_action('plugins_loaded', 'woocommerce_paymentexpress', 0);

function woocommerce_paymentexpress() {

	// If the WooCommerce payment gateway class is not available, do nothing
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

	class WC_Gateway_Payment_Express extends WC_Payment_Gateway {

		public $pxpay;
		public function __construct() {
			global $woocommerce;

			$this->id   = 'Payment_Express';
			$this->method_title = __('Payment Express', 'woothemes');
			$this->icon   = '';
			$this->has_fields  = false;

			// Load the form fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

            $PxPay_Url = 'https://sec.paymentexpress.com/pxpay/pxaccess.aspx' ;

			// Define user set variables
			$this->title       = $this->settings['title'];
			$this->description      = $this->settings['description'];
			//$this->access_url    = $PxPay_Url ; // $this->settings['access_url'];
			$this->site_name       = $this->settings['site_name'];
			$this->access_userid = $this->settings['access_userid'];
			$this->access_key       = $this->settings['access_key'];

			//$PxPay_Url    = $this->access_url;
			$PxPay_Userid = $this->access_userid;
			$PxPay_Key    = $this->access_key;
			$this->pxpay  = new PxPay_OpenSSL( $PxPay_Url, $PxPay_Userid, $PxPay_Key );

			// Actions
			add_action( 'init', array(&$this, 'check_callback') ); // 1.6.6
			add_action( 'woocommerce_api_wc_gateway_payment_express', array(&$this, 'check_callback') ); // 2.0.0

			add_action( 'woocommerce_receipt_Payment_Express', array(&$this, 'receipt_page'));
			add_action( 'valid-dps-callback', array(&$this, 'successful_request') );
			add_action( 'woocommerce_email_before_order_table', array(&$this, 'email_instructions'), 10, 2);

			/* 1.6.6 */
			add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );

			/* 2.0.0 */
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		public function Payment_Express_success_result($enc_hex) {

			$rsp               = $this->pxpay->getResponse($enc_hex);
			$Success           = $rsp->getSuccess();   // =1 when request succeeds
			$AmountSettlement  = $rsp->getAmountSettlement();
			$AuthCode          = $rsp->getAuthCode();  // from bank
			$CardName          = $rsp->getCardName();  // e.g. "Visa"
			$CardNumber        = $rsp->getCardNumber(); // Truncated card number
			$DateExpiry        = $rsp->getDateExpiry(); // in mmyy format
			$DpsBillingId      = $rsp->getDpsBillingId();
			$BillingId         = $rsp->getBillingId();
			$CardHolderName    = $rsp->getCardHolderName();
			$DpsTxnRef         = $rsp->getDpsTxnRef();
			$TxnType           = $rsp->getTxnType();
			$orderId           = $rsp->getTxnData1();
			$TxnData2          = $rsp->getTxnData2();
			$TxnData3          = $rsp->getTxnData3();
			$CurrencySettlement= $rsp->getCurrencySettlement();
			$ClientInfo        = $rsp->getClientInfo(); // The IP address of the user who submitted the transaction
			$TxnId             = $rsp->getTxnId();
			$CurrencyInput     = $rsp->getCurrencyInput();
			$EmailAddress      = $rsp->getEmailAddress();
			$MerchantReference = $rsp->getMerchantReference();
			$ResponseText      = $rsp->getResponseText();
			$TxnMac            = $rsp->getTxnMac(); // An indication as to the uniqueness of a card used in relation to others
			$orderKey          = str_replace(home_url()." - ", "", $MerchantReference);
			$result_orderId    = str_replace("Order number : ", "", $orderId);

			$order = new WC_Order( (int) $result_orderId );

			if ($rsp->getSuccess() == "1") {
				$order->payment_complete();
				wp_redirect( $this->get_return_url( $order ) );
				exit();
			}
			if  ($rsp->getSuccess() == "0") {
				$order->update_status('failed', sprintf(__('Payment %s via Payment Express.', 'woothemes'), strtolower($ResponseText) ) );
				wp_redirect( $this->get_return_url( $order ) );
				exit();
			}

		}

		/**
		 * Initialise Gateway Settings Form Fields
		 */
		function init_form_fields() {

            $default_site_name = home_url() ;

			$this->form_fields = array(

				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woothemes' ),
					'type' => 'checkbox',
					'label' => __( 'Enable Payment Express', 'woothemes' ),
					'default' => 'yes'
				),

				'title' => array(
					'title' => __( 'Title', 'woothemes' ),
					'type' => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woothemes' ),
					'default' => __( 'Payment Express', 'woothemes' ),
					'css' => 'width: 400px;'
				),

				'description' => array(
					'title' => __( 'Description', 'woothemes' ),
					'type' => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'woothemes' ),
					'default' => __("Allows credit card payments by Payment Express PX-Pay method", 'woothemes')
				),

				'site_name' => array(
					//'title' => __( 'Px-Pay Access Key', 'woothemes' ),
                    'title' => 'Merchant Reference',
                    'description' => 'A name (or URL) to identify this site in the "Merchant Reference" field (shown when viewing transactions in the site\'s Digital Payment Express back-end). This name <b>plus</b> the longest Order/Invoice Number used by the site must be <b>no longer than 53 characters</b>.',
					'type' => 'text',
					'default' => $default_site_name,
					'css' => 'width: 400px;',
                    'custom_attributes' => array( 'maxlength' => '53' )
                ),

				'access_userid' => array(
					//'title' => __( 'Access User Id', 'woothemes' ),
					'title' => __( 'Px-Pay Access User ID', 'woothemes' ),
					'type' => 'text',
					'default' => '',
					'css' => 'width: 400px;'
				),

				'access_key' => array(
					//'title' => __( 'Access Key', 'woothemes' ),
					'title' => __( 'Px-Pay Access Key', 'woothemes' ),
					'type' => 'text',
					'default' => '',
					'css' => 'width: 400px;'
				)

			);

		} // End init_form_fields()

		/**
		 * Admin Panel Options
		 * - Options for bits like 'title' and availability on a country-by-country basis
		 *
		 * @since 1.0.0
		 */
		public function admin_options() {
			?>
	    	<h3><?php _e('Payment Express', 'woothemes'); ?></h3>
	    	<p><?php _e('Allows credit card payments by Payment Express PX-Pay method', 'woothemes'); ?></p>
	    	<table class="form-table">
	    	<?php
				// Generate the HTML For the settings form.
				$this->generate_settings_html();
			?>
			</table><!--/.form-table-->
	    	<?php
		} // End admin_options()

		/**
		 * There are no payment fields for paypal, but we want to show the description if set.
		 **/
		function payment_fields() {
			if ($this->description) echo wpautop(wptexturize($this->description));
		}

		/**
		 * Generate the dps button link
		 **/
		public function generate_Payment_Express_form( $order_id ) {
			global $woocommerce;

			$order = new WC_Order( $order_id );
			$billing_name = $order->billing_first_name." ".$order->billing_last_name;
			$shipping_name = explode(' ', $order->shipping_method);
			$request = new PxPayRequest();

			$http_host   = getenv("HTTP_HOST");
			$request_uri = getenv("SCRIPT_NAME");
			$server_url  = "http://$http_host";

			if ( method_exists( $woocommerce, 'api_request_url' ) ) {
				$script_url = $woocommerce->api_request_url( 'WC_Gateway_Payment_Express' );
			} else {
				$script_url = $this->get_return_url();
			}

			$currency    = get_option('woocommerce_currency');

			//$MerchantRef = home_url();
			//$MerchantRef.= " # ".$order->order_key;
            $MerchantRef = $this->site_name . ' - Order # ' . $order->id ;
            if ( strlen( $MerchantRef ) > 64 ) {
                $MerchantRef = substr( $this->site_name , 0 , max( 50 - strlen( $order->id ) , 0 ) ) . '... - Order # ' . $order->id ;
                if ( strlen( $MerchantRef ) > 64 ) {
                    $MerchantRef = 'Order # ' . substr( $order->id , 0 , 53 ) . '...' ;
                }
            }

			$txndata1 =  "Order number : ". $order->id;
			//Generate a unique identifier for the transaction
			$TxnId = uniqid("ID");

			//Set PxPay properties
			$request->setMerchantReference($MerchantRef);
			$request->setAmountInput($order->order_total);
			$request->setTxnData1($txndata1);
			$request->setTxnData2($billing_name);
			$request->setTxnData3($order->billing_email);
			$request->setTxnType("Purchase");
			$request->setCurrencyInput($currency);
			$request->setEmailAddress($order->billing_email);
			$request->setUrlFail($script_url);   // can be a dedicated failure page
			$request->setUrlSuccess($script_url);   // can be a dedicated success page
			$request->setTxnId($TxnId);

			//Call makeRequest function to obtain input XML
			$request_string = $this->pxpay->makeRequest($request);

			//Obtain output XML
			$response = new MifMessage($request_string);

			//Parse output XML
			$url = $response->get_element_text("URI");
			$valid = $response->get_attribute("valid");

			$dps_adr =  $url;
$dps_adr = (strstr($url, '&request=')) ? $url : str_replace('request=', '&request=', $url);
			return '<form action="'.esc_url( $dps_adr ).'" method="post" id="dps_payment_form">
				<input type="submit" class="button-alt" id="submit_Payment_Express_payment_form" value="'.__('Pay via Payment_Express', 'woothemes').'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__('Cancel order &amp; restore cart', 'woothemes').'</a>
				<script type="text/javascript">
					jQuery(function(){
						jQuery("body").block(
							{
								message: "<img src=\"'.esc_url( $woocommerce->plugin_url() ).'/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" />'.__('Thank you for your order. We are now redirecting you to Payment Express to make payment.', 'woothemes').'",
								overlayCSS:
								{
									background: "#fff",
									opacity: 0.6
								},
								css: {
							        padding:        20,
							        textAlign:      "center",
							        color:          "#555",
							        border:         "3px solid #aaa",
							        backgroundColor:"#fff",
							        cursor:         "wait",
							        lineHeight:		"32px"
							    }
							});
						jQuery("#submit_Payment_Express_payment_form").click();
					});
				</script>
			</form>';

		}

		/**
		 * Process the payment and return the result
		 **/
		function process_payment( $order_id ) {

			$order = new WC_Order( $order_id );

			return array(
				'result'  => 'success',
				'redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id'))))
			);

		}

		function email_instructions( $order, $sent_to_admin ) {
			if ( $sent_to_admin ) return;

			if ( $order->status !== 'on-hold') return;

			if ( $order->payment_method !== 'Payment_Express') return;

			if ($this->description) echo wpautop(wptexturize($this->description));
		}

		/**
		 * receipt_page
		 **/
		function receipt_page( $order ) {
			echo '<p>'.__('Thank you for your order, please click the button below to pay with Payment Express.', 'woothemes').'</p>';

			echo $this->generate_Payment_Express_form( $order );
		}

		function check_callback() {
		   	if ( isset($_REQUEST["userid"]) ) :
				$uri  = explode('result=', $_SERVER['REQUEST_URI']);
				$uri1 = $uri[1];
				$uri2  = explode('&', $uri1);
				$enc_hex = $uri2[0];

				do_action("valid-dps-callback", $enc_hex);
			endif;
		}

		function successful_request ($enc_hex) {
			$this->Payment_Express_success_result($enc_hex);
		}

	}
$myplugin = new WC_Gateway_Payment_Express();
add_action('init', array(&$myplugin, 'check_callback'));
}


/**
 * Add the gateway to WooCommerce
 **/
function add_payment_express_gateway( $methods ) {
	$methods[] = 'WC_Gateway_Payment_Express'; return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_payment_express_gateway' );
