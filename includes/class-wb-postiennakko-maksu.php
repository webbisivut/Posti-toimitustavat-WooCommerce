<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * WB_Posti_Gateway_Postiennakko Class
 *
 * @class WB_Posti_Gateway_Postiennakko
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Posti_Toimitustavat
 * @author Webbisivut.org
 */
function WB_Init_Posti_Postiennakkomaksu_Gateway_Class() {
	class WB_Posti_Gateway_Postiennakko extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id                 = 'wb_postiennakko_maksu';
			$this->method_title       = __( 'Maksu Postiin', 'wb-posti' );
			$this->method_description = __( 'Maksu Postin toimipisteeseen.', 'wb-posti' );
			$this->has_fields         = false;

			// Load the settings
			$this->init_form_fields();
			$this->init_settings();

			// Get settings
			$this->title              = esc_attr( $this->get_option( 'title' ) );
			$this->description        = esc_attr( $this->get_option( 'description' ) );
			$this->instructions       = esc_attr( $this->get_option( 'instructions', $this->description ) );
			$this->pe_lisamaksu_on_off     = esc_attr( $this->get_option( 'pe_lisamaksu_on_off' ) );
			$this->pe_lisamaksu            = esc_attr( $this->get_option( 'pe_lisamaksu' ) );
			$this->pe_lisamaksu_nimi       = esc_attr( $this->get_option( 'pe_lisamaksu_nimi' ) );
			$this->tax_status_pe_lisamaksu = esc_attr( $this->get_option( 'tax_status_pe_lisamaksu' ) );
			$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'posti_thankyou_page' ), 1 );

			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}

		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields() {
			$shipping_methods = array();

			if ( is_admin() )
				foreach ( WC()->shipping()->load_shipping_methods() as $method ) {

					$shipping_methods[ $method->id ] = $method->id;
				}

			$this->form_fields = array(
				'enabled' => array(
					'title'       => __( 'Ota toimitustapa käyttöön', 'wb-posti' ),
					'label'       => __( 'Ota käyttöön maksu Postin toimipisteeseen', 'wb-posti' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				),
				'title' => array(
					'title'       => __( 'Nimi', 'wb-posti' ),
					'type'        => 'text',
					'description' => __( 'Näytetään kassalla', 'wb-posti' ),
					'default'     => __( 'Maksu Postin toimipisteeseen', 'wb-posti' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Kuvaus', 'wb-posti' ),
					'type'        => 'textarea',
					'description' => __( 'Maksutavan kuvaus kassalla.', 'wb-posti' ),
					'default'     => __( 'Maksu Postin toimipisteeseen noudettaessa.', 'wb-posti' ),
					'desc_tip'    => true,
				),
				'instructions' => array(
					'title'       => __( 'Maksuohjeet', 'wb-posti' ),
					'type'        => 'textarea',
					'description' => __( 'Maksuohjeet jotka näytetään kiitos-sivulla.', 'wb-posti' ),
					'default'     => __( 'Maksa toimituksen maksu Postin toimipisteeseen.', 'wb-posti' ),
					'desc_tip'    => true,
				),
				'pe_lisamaksu_on_off' => array(
						'title' 		=> __( 'Postiennakkomaksu', 'wb-posti' ),
						'type' 			=> 'select',
						'description' 	=> 'Otetaanko käyttöön postiennakkomaksu tälle maksutavalle?',
						'default' 		=> 'ei',
						'options'		=> array(
							'ei' 	    => __( 'Ei', 'wb-posti' ),
							'kylla'	    => __( 'Kyllä', 'wb-posti' ),
				)),
				'pe_lisamaksu'   => array(
					'title'            => __('Postiennakkomaksu', 'wb-posti'),
					'type'             => 'text',
					'placeholder'	   => '4.20',
					'description'      => __('Anna tähän postiennakkomaksu', 'wb-posti'),
					'default'          => __('')
				),
				'pe_lisamaksu_nimi'   => array(
					'title'            => __('Maksun nimi', 'wb-posti'),
					'type'             => 'text',
					'placeholder'	   => 'Postiennakko',
					'description'      => __('Anna maksun nimi joka näytetään kassalla', 'wb-posti'),
					'default'          => __('')
				),
				'tax_status_pe_lisamaksu' => array(
						'title' 		=> __( 'Maksun verotus', 'wb-posti' ),
						'type' 			=> 'select',
						'description' 	=> '',
						'default' 		=> 'none',
						'options'		=> array(
							'taxable' 	=> __( 'Verotettava', 'wb-posti' ),
							'none' 		=> __( 'Ei verotettava', 'wb-posti' ),
				)),
				'enable_for_methods' => array(
					'title'             => __( 'Ota käyttöön seuraaville toimitustavoille', 'wb-posti' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'css'               => 'width: 450px;',
					'default'           => '',
					'description'       => __( 'Valitse mille toimitustavoille haluat maksutavan näkyvän, tai jätä tyhjäksi jos haluat sen näkyvän kaikille toimitustavoille.', 'wb-posti' ),
					'options'           => $shipping_methods,
					'desc_tip'          => true,
					'custom_attributes' => array(
						'data-placeholder' => __( 'Valitse toimitustavat', 'wb-posti' )
					)
				)
		   );
		}

		/**
		 * Check If The Gateway Is Available For Use
		 *
		 * @return bool
		 */
		public function is_available() {
			$order          = null;
			$needs_shipping = false;

			// Test if shipping is needed first
			if ( WC()->cart && WC()->cart->needs_shipping() ) {
				$needs_shipping = true;
			} elseif ( is_page( wc_get_page_id( 'checkout' ) ) && 0 < get_query_var( 'order-pay' ) ) {
				$order_id = absint( get_query_var( 'order-pay' ) );
				$order    = wc_get_order( $order_id );

				// Test if order needs shipping.
				if ( 0 < sizeof( $order->get_items() ) ) {
					foreach ( $order->get_items() as $item ) {
						$_product = $item->get_product();
						if ( $_product && $_product->needs_shipping() ) {
							$needs_shipping = true;
							break;
						}
					}
				}
			}

			$needs_shipping = apply_filters( 'woocommerce_cart_needs_shipping', $needs_shipping );

			// Check methods
			if ( ! empty( $this->enable_for_methods ) && $needs_shipping ) {

				// Only apply if all packages are being shipped via chosen methods
				$chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );

				if ( isset( $chosen_shipping_methods_session ) ) {
					$chosen_shipping_methods = array_unique( $chosen_shipping_methods_session );
				} else {
					$chosen_shipping_methods = array();
				}

				$check_method = false;

				if ( is_object( $order ) ) {
					if ( $order->get_shipping_method() ) {
						$check_method = $order->get_shipping_method();
					}
				} elseif ( empty( $chosen_shipping_methods ) || sizeof( $chosen_shipping_methods ) > 1 ) {
					$check_method = false;
				} elseif ( sizeof( $chosen_shipping_methods ) == 1 ) {
					$check_method = $chosen_shipping_methods[0];
				}

				if ( ! $check_method ) {
					return false;
				}

				$found = false;

				foreach ( $this->enable_for_methods as $method_id ) {
					if ( strpos( $check_method, $method_id ) === 0 ) {
						$found = true;
						break;
					}
				}

				if ( ! $found ) {
					return false;
				}
			}

			return parent::is_available();
		}


		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {

			$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

			if( $chosen_payment_method == 'wb_postiennakko_maksu') {

				$order = wc_get_order( $order_id );

				// Mark as processing (payment won't be taken until delivery)
				$order->update_status( 'processing', __( 'Maksetaan noudon yhteydessä', 'wb-posti' ) );

				// Reduce stock levels
				$order->reduce_order_stock();

				// Remove cart
				WC()->cart->empty_cart();

				// Return thankyou redirect
				return array(
					'result' 	=> 'success',
					'redirect'	=> $this->get_return_url( $order )
				);
			}
		}

		/**
		 * Output for the order received page.
		 */
		public function posti_thankyou_page($order_id) {

			$order = new WC_Order( $order_id );

			if ( $this->instructions && 'wb_postiennakko_maksu' == $order->get_payment_method() ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}

		}

		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

			if ( $this->instructions && ! $sent_to_admin && 'wb_postiennakko_maksu' === $order->get_payment_method() ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}

		}
	}

}
add_action( 'plugins_loaded', 'WB_Init_Posti_Postiennakkomaksu_Gateway_Class' );

/**
 * Add fee on article specifics
 * @param WC_Cart $cart
 */
function add_wb_pen_lisafees(){
		$obj = new WB_Posti_Gateway_Postiennakko();

		if( $obj->settings['pe_lisamaksu_on_off'] == 'kylla' ) {
			global $woocommerce;

			$chosen_methods = WC()->session->get( 'chosen_payment_method' );
			$chosen_method = $chosen_methods;

			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping_method = $chosen_shipping_methods[0];

			$available_methods = $obj->enable_for_methods;

			$maksun_nimi = $obj->pe_lisamaksu_nimi;
			$pe_maksu = $obj->pe_lisamaksu;
			$pe_tax_status = $obj->tax_status_pe_lisamaksu;
			$pe_lisamaksu_on_off = $obj->pe_lisamaksu_on_off;

			if( $pe_tax_status == 'taxable' ) {
				$pe_verot = true;
			} else if ( $pe_tax_status == 'none' ) {
				$pe_verot = false;
			}

			foreach($available_methods as $method) {
				if ($pe_maksu > 0 && $chosen_method == 'wb_postiennakko_maksu' && strpos($chosen_shipping_method, $method) !== false) {
					WC()->cart->add_fee( $maksun_nimi, $pe_maksu, $pe_verot, '' );
				}
			}

		}
}

// Lisätään maksu
add_action( 'woocommerce_cart_calculate_fees' , 'add_wb_pen_lisafees');
add_action( 'woocommerce_after_cart_item_quantity_update', 'add_wb_pen_lisafees');

function add_wb_posti_postiennakko_gateway_class( $methods ) {
	$methods[] = 'WB_Posti_Gateway_Postiennakko';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_wb_posti_postiennakko_gateway_class' );
