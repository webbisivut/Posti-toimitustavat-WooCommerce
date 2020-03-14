<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WB_Kirje_Shipping_Method_sz Class
 *
 * @class WB_Kirje_Shipping_Method_sz
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Posti_Toimitustavat
 * @author Webbisivut.org
 */

 /**
 * Get cart items
 *
 * @access public
 * @return string
 */
class cart_items_kirje_toim_sz {

	public function hae_woo() {
		global $woocommerce;
		$items = $woocommerce->cart->get_cart();

		return $items;
	}

	public function korkeus() {
		 $kaikki_tuotteet_korkeus = array();

		 $items = $this->hae_woo();
		 foreach ($items as $item) {
			 $height = floatval($item['data']->get_height());
			 array_push($kaikki_tuotteet_korkeus, $height);
		 }
		 return $kaikki_tuotteet_korkeus;
	 }

	 public function pituus() {
		 $kaikki_tuotteet_pituus = array();

		 $items = $this->hae_woo();
		 foreach ($items as $item) {
			 $length = floatval($item['data']->get_length());
			 array_push($kaikki_tuotteet_pituus, $length);
		 }
		 return $kaikki_tuotteet_pituus;
	 }

	 public function leveys() {
		 $kaikki_tuotteet_leveys = array();

		 $items = $this->hae_woo();
		 foreach ($items as $item) {
			 $width = floatval($item['data']->get_width());
			 array_push($kaikki_tuotteet_leveys, $width);
		 }
		 return $kaikki_tuotteet_leveys;
	 }

	 public function paino() {
		 $kaikki_tuotteet_paino = array();

		 $items = $this->hae_woo();
		 foreach ($items as $item) {
			 $weight = floatval($item['data']->get_weight());
			 array_push($kaikki_tuotteet_paino, $weight);
		 }
		 return $kaikki_tuotteet_paino;
	 }

}

/**
 * Our main function
 *
 * @access public
 * @return void
 */
function WB_Kirje_Shipping_Method_sz_Init() {

	if ( ! class_exists( 'WB_Kirje_Shipping_Method_sz' ) ) {

		class WB_Kirje_Shipping_Method_sz extends WC_Shipping_Method {

			/**
			* Constructor for Posti shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {

				$this->id = 'wb_kirje_shipping_method_sz'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Posti Kirje (Posti Toimitustavat)', 'wb-posti' ); // Title shown in admin
				$this->method_description = __( 'Posti Kirje toimitustapa.', 'wb-posti' ); // Description shown in admin
				$this->supports = array(
					'shipping-zones',
					'instance-settings',
				);

				$this->init();

			}

			/**
			* Init settings
			*
			* @access public
			* @return void
			*/
			function init() {

				$this->init_form_fields();
				$this->init_settings();

				$this->kirje_paino1		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_paino1') ));
				$this->kirje_hinta1			   = str_replace(",", ".", esc_attr( $this->get_option('kirje_hinta1') ));

				$this->kirje_paino2		   	   = str_replace(",", ".", esc_attr( $this->get_option('kirje_paino2') ));
				$this->kirje_hinta2		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_hinta2') ));

				$this->kirje_paino3		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_paino3') ));
				$this->kirje_hinta3		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_hinta3') ));

				$this->kirje_paino4		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_paino4') ));
				$this->kirje_hinta4		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_hinta4') ));

				$this->kirje_paino5		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_paino5') ));
				$this->kirje_hinta5		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_hinta5') ));

				$this->kirje_paino6		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_paino6') ));
				$this->kirje_hinta6		       = str_replace(",", ".", esc_attr( $this->get_option('kirje_hinta6') ));

        		$this->kirje_max_korkeus   	   = str_replace(",", ".", esc_attr( $this->get_option('kirje_max_korkeus') ));
				$this->kirje_max_pituus    	   = str_replace(",", ".", esc_attr( $this->get_option('kirje_max_pituus') ));
				$this->kirje_max_leveys    	   = str_replace(",", ".", esc_attr( $this->get_option('kirje_max_leveys') ));

				$this->kirje_max_paino		   = str_replace(",", ".", esc_attr( $this->get_option('kirje_max_paino') ));

				$this->kirje_kas_kulut	  	   = str_replace(",", ".", esc_attr( $this->get_option('kirje_kas_kulut') ));

				$this->tax_status	  	 	   = $this->get_option('tax_status');

				$this->title 				   = $this->get_option( 'title' );

				$this->availability 	  	   = $this->get_option( 'availability' );
				$this->countries 		   	   = $this->get_option( 'countries' );

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			function init_form_fields() {
				global $woocommerce;
				
				$this->instance_form_fields = array(
					'title'	   	   => array(
						'title'            => __('Toimitustavan nimi', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => 'Posti Kirje',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-posti'),
						'default'          => __('Posti Kirje')
					),
					'kirje_paino1'	   	   => array(
						'title'            => __('Kirje1 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0.050',
						'description'      => __('Ilmoita ensimmäisen kirjeen max-paino Esim. 0.050. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('0.050')
					),
					'kirje_hinta1'	   	   => array(
						'title'            => __('Kirje1 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '1.10',
						'description'      => __('Ilmoita ensimmäisen kirjeen hinta Esim. 1.10 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('1.10')
					),
					'kirje_paino2'	   	   => array(
						'title'            => __('Kirje2 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0.100',
						'description'      => __('Ilmoita toisen kirjeen max-paino Esim. 0.100. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('0.100')
					),
					'kirje_hinta2'	   	   => array(
						'title'            => __('Kirje2 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '1.60',
						'description'      => __('Ilmoita toisen kirjeen hinta Esim. 1.60 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('1.60')
					),
					'kirje_paino3'	   	   => array(
						'title'            => __('Kirje3 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0.250',
						'description'      => __('Ilmoita kolmannen kirjeen max-paino Esim. 0.250. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('0.250')
					),
					'kirje_hinta3'	   	   => array(
						'title'            => __('Kirje3 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '2.20',
						'description'      => __('Ilmoita kolmannen kirjeen hinta Esim. 2.20 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('2.20')
					),
					'kirje_paino4'	   	   => array(
						'title'            => __('Kirje4 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0.500',
						'description'      => __('Ilmoita neljännen kirjeen max-paino Esim. 0.500. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('0.500')
					),
					'kirje_hinta4'	   	   => array(
						'title'            => __('Kirje4 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '4.40',
						'description'      => __('Ilmoita neljännen kirjeen hinta Esim. 4.40 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('4.40')
					),
					'kirje_paino5'	   	   => array(
						'title'            => __('Kirje5 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '1',
						'description'      => __('Ilmoita viidennen kirjeen max-paino Esim. 1. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('1')
					),
					'kirje_hinta5'	   	   => array(
						'title'            => __('Kirje5 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '6.60',
						'description'      => __('Ilmoita viidennen kirjeen hinta Esim. 6.60 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('6.60')
					),
					'kirje_paino6'	   	   => array(
						'title'            => __('Kirje6 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Ilmoita kuudennen kirjeen max-paino Esim. 2. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('2')
					),
					'kirje_hinta6'	   	   => array(
						'title'            => __('Kirje6 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '11.00',
						'description'      => __('Ilmoita kuudennen kirjeen hinta Esim. 11.00 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('11.00')
					),
          			'kirje_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '3',
						'description'      => __('Tuotteen maksimikorkeus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 3cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('3')
					),
					'kirje_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '25',
						'description'      => __('Tuotteen maksimipituus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 25cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('25')
					),
					'kirje_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '35.3',
						'description'      => __('Tuotteen maksimileveys, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 35.3cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('35.3')
					),
					'kirje_max_paino'	   => array(
						'title'            => __('Kirje maksimi paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Anna maksimi paino, jonka jälkeen toimitustapaa ei näytetä enää kassalla. Oletus 2kg. Huom!! Paino tulee antaa samassa painoyksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('2')
					),
					'kirje_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-posti'),
						'default'          => __('0')
					),
					'availability' 			=> array(
							'title'			=> __( 'Saatavuus', 'wb-posti' ),
							'type'			=> 'select',
							'class'         => 'wc-enhanced-select',
							'description'	=> '',
							'default'		=> 'all',
							'options'		=> array(
								'including'		=> __( 'Toimitetaan valittuihin maihin', 'wb-posti' ),
								'excluding'		=> __( 'Ei toimiteta valittuihin maihin', 'wb-posti' ),
								'all'		    => __( 'Toimitetaan kaikkiin maihin joihin myydään', 'wb-posti' ),
							)
					),
					'countries'			 	=> array(
							'title'			=> __( 'Valitut maat', 'wb-posti' ),
							'type'			=> 'multiselect',
							'class'			=> 'wc-enhanced-select',
							'css'			=> 'width: 450px;',
							'default'		=> '',
							'options'		=> WC()->countries->get_shipping_countries(),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Valitse maat', 'wb-posti' )
							)
					),
					'tax_status' => array(
							'title' 		=> __( 'Verotettava', 'wb-posti' ),
							'type' 			=> 'select',
							'description' 	=> '',
							'default' 		=> 'taxable',
							'options'		=> array(
								'taxable' 	=> __( 'Verotettava', 'wb-posti' ),
								'none' 		=> __( 'Ei verotettava', 'wb-posti' ),
							),
					)
				);
			}

			/**
			 * is_available function.
			 *
			 * @access public
			 * @param mixed $package
			 * @return bool
			 */
			public function is_available( $package ) {

				if ( "no" === $this->enabled ) {
					return false;
				}

				if ( 'including' === $this->availability ) {

					if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
						return false;
					}

				} elseif ( 'excluding' === $this->availability ) {

					if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
						return false;
					}

				} elseif ( 'all' === $this->availability ) {
					$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
				}

				if ( isset($ship_to_countries) && is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
					return false;
				}

				return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
			}

			/**
			* calculate_shipping function.
			*
			* @access public
			* @param mixed $package
			* @return void
			*/
			public function calculate_shipping( $package = array() ) {

				$woocommerce = function_exists('WC') ? WC() : $GLOBALS['woocommerce'];
				$weight     = $woocommerce->cart->cart_contents_weight;
				$cart_price = floatval( preg_replace( '#[^\d.,]#', '', $woocommerce->cart->get_cart_total() ) );

				$paino1 = $this->kirje_paino1;
				$paino2 = $this->kirje_paino2;
				$paino3 = $this->kirje_paino3;
				$paino4 = $this->kirje_paino4;
				$paino5 = $this->kirje_paino5;
				$paino6 = $this->kirje_paino6;

				if ( (0 <= $weight) && ($weight <= $paino1) ) {
					$lopullinen_hinta = $this->kirje_hinta1 + $this->kirje_kas_kulut;
				} elseif ( ($paino1 <= $weight) && ($weight <= $paino2) ) {
					$lopullinen_hinta = $this->kirje_hinta2 + $this->kirje_kas_kulut;
				} elseif ( ($paino2 <= $weight) && ($weight <= $paino3) ) {
					$lopullinen_hinta = $this->kirje_hinta3 + $this->kirje_kas_kulut;
				} elseif ( ($paino3 <= $weight) && ($weight <= $paino4) ) {
					$lopullinen_hinta = $this->kirje_hinta4 + $this->kirje_kas_kulut;
				} elseif ( ($paino4 <= $weight) && ($weight <= $paino5) ) {
					$lopullinen_hinta = $this->kirje_hinta5 + $this->kirje_kas_kulut;
				} elseif ( ($paino5 <= $weight) && ($weight <= $paino6) ) {
					$lopullinen_hinta = $this->kirje_hinta6 + $this->kirje_kas_kulut;
				} else {
					$lopullinen_hinta = $this->kirje_hinta6 + $this->kirje_kas_kulut;
				}

				$rate = apply_filters('wb_posti_kirje_rate_filter', array(
					'id' => $this->id . $this->instance_id,
					'label' => $this->title,
					'cost' => $lopullinen_hinta,
					'package' => $package,
					'taxes'     => '',
					'calc_tax' => 'per_order'
				) );

				// Register the rate
				$this->add_rate( $rate );
			}
		}
	}
}

add_action( 'woocommerce_shipping_init', 'WB_Kirje_Shipping_Method_sz_init' );

function add_WB_Kirje_Shipping_Method_sz( $methods ) {

	$methods['wb_kirje_shipping_method_sz'] = 'WB_Kirje_Shipping_Method_sz';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_WB_Kirje_Shipping_Method_sz' );

/**
* Hide cart if max weight exceeds
*
* @param $rates $package
* @return void
*/

function hide_show_posti_kirje_sz( $rates, $package ) {
  $woocommerce = function_exists('WC') ? WC() : $GLOBALS['woocommerce'];

  $shippingIds = array();

  if(isset($rates)) {
  	  $get_the_id = null;
  	  // Get all shipping methods in use
	  foreach ( $rates as $rate_id => $rate ) {
		array_push($shippingIds, $rate->id);
	  }

	  // Get the instance id
	  foreach ($shippingIds as $shipping_id) {
		if (strpos($shipping_id, 'wb_kirje_shipping_method_sz') !== false) {
			$get_the_id = str_replace('wb_kirje_shipping_method_sz', '', $shipping_id);
			$get_the_id = str_replace(':', '', $get_the_id);
		}
	  }

	  $kassan_tiedot = new cart_items_kirje_toim_sz();

	  $length = $kassan_tiedot->pituus();
	  $width = $kassan_tiedot->leveys();
	  $height = $kassan_tiedot->korkeus();
	  $weight = $kassan_tiedot->paino();

	  $cart_weight     = $woocommerce->cart->cart_contents_weight;

	  $kirje_shipping_method = new WB_Kirje_Shipping_Method_sz( $instance_id = $get_the_id );

	  $max_paino = $kirje_shipping_method->kirje_max_paino;

	  if($kirje_shipping_method->kirje_max_korkeus != '') {
			$max_korkeus = $kirje_shipping_method->kirje_max_korkeus;
		} else {
			$max_korkeus = 3;
		}

		if($kirje_shipping_method->kirje_max_leveys != '') {
			$max_leveys = $kirje_shipping_method->kirje_max_leveys;
		} else {
			$max_leveys = 35.3;
		}

		if($kirje_shipping_method->kirje_max_pituus != '') {
			$max_pituus = $kirje_shipping_method->kirje_max_pituus;
		} else {
			$max_pituus = 25;
		}

		if( $max_paino == '') {
			$max_paino = 2.01;
		} else {
			$max_paino = $max_paino;
		}

		if($height == null OR $height == '') {
			$height = array(0);
		}

		if($length == null OR $length == '') {
			$length = array(0);
		}

		if($width == null OR $width == '') {
			$width = array(0);
		}

		if($cart_weight == null OR $cart_weight == '') {
			$cart_weight = array(0);
		}

		if( max($height) > $max_korkeus OR max($length) > $max_pituus OR max($width) > $max_leveys OR max($weight) > $max_paino OR $cart_weight > $max_paino) {
			$new_rates = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( 'wb_kirje_shipping_method_sz' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}

			return $new_rates;

		} else {
			return $rates;
		}
  }

}

add_filter( 'woocommerce_package_rates', 'hide_show_posti_kirje_sz' , 10, 2 );