<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get cart items
 *
 * @access public
 * @return array
 */
class rahti_posti_cart_items {

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
 * WB_Rahti_Posti_Shipping_Method Class
 *
 * @class WB_Rahti_Posti_Shipping_Method
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Prinetti
 * @author Webbisivut.org
 */
function WB_Rahti_Posti_Shipping_Method_Init() {

	if ( ! class_exists( 'WB_Rahti_Posti_Shipping_Method' ) ) {

		class WB_Rahti_Posti_Shipping_Method extends WC_Shipping_Method {

			/**
			* Constructor for Posti shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {

				$this->id = 'wb_rahti_posti_shipping_method'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Posti Rahti (Posti Toimitustavat)', 'wb-posti' ); // Title shown in admin
				$this->method_description = __( 'Posti Rahti paketti', 'wb-posti' ); // Description shown in admin
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

				$this->korkeus_vai_paino   	   = $this->get_option('korkeus_vai_paino');

				$this->rahti_paino1		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_paino1') ));
				$this->rahti_hinta1		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_hinta1') ));

				$this->rahti_paino2		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_paino2') ));
				$this->rahti_hinta2		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_hinta2') ));

				$this->rahti_paino3		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_paino3') ));
				$this->rahti_hinta3		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_hinta3') ));

				$this->rahti_paino4		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_paino4') ));
				$this->rahti_hinta4		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_hinta4') ));

				$this->rahti_paino5		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_paino5') ));
				$this->rahti_hinta5		   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_hinta5') ));

				$this->rahti_kas_kulut	   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_kas_kulut') ));
				$this->rahti_ilm_toim	   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_ilm_toim') ));

				$this->rahti_max_korkeus   	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_max_korkeus') ));
				$this->rahti_max_pituus    	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_max_pituus') ));
				$this->rahti_max_leveys    	   = str_replace(",", ".", esc_attr( $this->get_option('rahti_max_leveys') ));

				$this->rahti_max_paino		   = str_replace(",", ".", esc_attr( $this->get_option('rahti_max_paino') ));

				$this->tax_status	  	 	   = $this->get_option('tax_status');

				$this->title 			   	   = $this->get_option( 'title' );
				$this->availability 	   	   = $this->get_option( 'availability' );
				$this->countries 		  	   = $this->get_option( 'countries' );

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			function init_form_fields() {

				$this->instance_form_fields = array(
					'title'	   	  		   => array(
						'title'            => __('Toimitustavan nimi', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => 'Posti Rahti',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-posti'),
						'default'          => __('Posti Rahti')
					),
					'korkeus_vai_paino' => array(
						'title' 		=> __( 'Hinnoitteluyksikkö', 'wb-posti' ),
						'type' 			=> 'select',
						'description' 	=> 'Valitse haluatko hinnoitella toimituskulut tuotteiden painon vaiko korkeuden mukaan. Tuotteiden korkeus/paino yksiköt tulee olla samassa yksikössä kuin ne on asetettu WooCommercen asetuksiin!',
						'default' 		=> 'paino',
						'options'		=> array(
							'paino' 	=> __( 'Paino', 'wb-posti' ),
							'korkeus' 	=> __( 'Korkeus', 'wb-posti' ),
						),
					),
					'rahti_paino1'	   	   => array(
						'title'            => __('Paketti1 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Ilmoita ensimmäisen paketin max-paino tai korkeus Esim. 2. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('2')
					),
					'rahti_hinta1'	   	   => array(
						'title'            => __('Paketti1 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '19.90',
						'description'      => __('Ilmoita ensimmäisen paketin hinta Esim. 19.90 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('19.90')
					),
					'rahti_paino2'	   	   => array(
						'title'            => __('Paketti2 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '5',
						'description'      => __('Ilmoita toisen paketin max-paino tai korkeus Esim. 5. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('5')
					),
					'rahti_hinta2'	   	   => array(
						'title'            => __('Paketti2 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '20.30',
						'description'      => __('Ilmoita toisen paketin hinta Esim. 20.30 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('20.30')
					),
					'rahti_paino3'	   	   => array(
						'title'            => __('Paketti3 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '10',
						'description'      => __('Ilmoita kolmannen paketin max-paino tai korkeus Esim. 10. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('10')
					),
					'rahti_hinta3'	   	   => array(
						'title'            => __('Paketti3 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '21.30',
						'description'      => __('Ilmoita kolmannen paketin hinta Esim. 21.30 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('21.30')
					),
					'rahti_paino4'	   	   => array(
						'title'            => __('Paketti4 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '15',
						'description'      => __('Ilmoita neljännen paketin max-paino tai korkeus Esim. 15. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('15')
					),
					'rahti_hinta4'	   	   => array(
						'title'            => __('Paketti4 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '22.30',
						'description'      => __('Ilmoita neljännen paketin hinta Esim. 22.30 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('22.30')
					),
					'rahti_paino5'	   	   => array(
						'title'            => __('Paketti5 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '35',
						'description'      => __('Ilmoita viidennen paketin max-paino tai korkeus Esim. 35. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('35')
					),
					'rahti_hinta5'	   	   => array(
						'title'            => __('Paketti5 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '27.10',
						'description'      => __('Ilmoita viidennen paketin hinta Esim. 27.10 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('27.10')
					),
					'rahti_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Tuotteen maksimikorkeus, jolloin tuotetta ei enää näytetä kassalla. Oletus 100cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('100')
					),
					'rahti_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Tuotteen maksipituus, jolloin tuotetta ei enää näytetä kassalla. Oletus 100cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('100')
					),
					'rahti_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Tuotteen maksileveys, jolloin tuotetta ei enää näytetä kassalla. Oletus 100cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('100')
					),
					'rahti_max_paino'	   => array(
						'title'            => __('Paketin maksimi paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '30',
						'description'      => __('Anna maksimi paino, jonka jälkeen toimitustapaa ei näytetä enää kassalla. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('30')
					),
					'rahti_ilm_toim'	   => array(
						'title'            => __('Ilmaisen toimituksen raja', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Anna summa jonka jälkeen ei lisätä toimituskuluja. Jätä tyhjäksi jos et halua käyttää tätä toimintoa.', 'wb-posti'),
						'default'          => __('')
					),
					'rahti_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-posti'),
						'default'          => __('0')
					),
					'availability' => array(
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
					'countries' => array(
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
								'taxable' 	=> __( 'Verotettava', 'woocommerce' ),
								'none' 		=> __( 'Ei verotettava', 'woocommerce' ),
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

				$cart_price = preg_replace( '#[^\d.,]#', '', $woocommerce->cart->get_cart_total() );

				$woocommerce_price_thousand_sep = esc_attr(get_option('woocommerce_price_thousand_sep'));
				$woocommerce_price_decimal_sep = esc_attr(get_option('woocommerce_price_decimal_sep'));

				if($woocommerce_price_thousand_sep == ',') {
					$replace = '';
					$needle = ',';

					$cart_price = formatPricePostiToimitustavat($cart_price, $replace, $needle);

					if($woocommerce_price_decimal_sep == ',') {
						$cart_price = str_replace(',', '.', $cart_price);
					}
				} else if($woocommerce_price_thousand_sep == '.') {
					$replace = '';
					$needle = '.';

					$cart_price = formatPricePostiToimitustavat($cart_price, $replace, $needle);

					if($woocommerce_price_decimal_sep == ',') {
						$cart_price = str_replace(',', '.', $cart_price);
					}
				} else if($woocommerce_price_thousand_sep == '' OR $woocommerce_price_thousand_sep == ' ') {
					if($woocommerce_price_decimal_sep == ',') {
						$cart_price = str_replace(',', '.', $cart_price);
					}
				}
				
				$cart_price = floatval($cart_price);
				$cart_price = number_format($cart_price, 2, '.', '');

				$cart_tax = $woocommerce->cart->get_taxes();
				$cart_tax = array_sum($cart_tax);
				$cart_tax = floatval($cart_tax);

				$cart_total_price = $cart_price + $cart_tax;

				$paino1_hinta = $this->rahti_paino1;
				$paino2_hinta = $this->rahti_paino2;
				$paino3_hinta = $this->rahti_paino3;
				$paino4_hinta = $this->rahti_paino4;
				$paino5_hinta = $this->rahti_paino5;

				// Katsotaan kummanko yksikön mukaan hinnoitellaan
				$korkeus_vai_paino = $this->korkeus_vai_paino;
				
				$kassan_tiedot = new rahti_posti_cart_items();
				$height = $kassan_tiedot->korkeus();

				if($korkeus_vai_paino == 'korkeus') {
					$yksikko = max($height);
				} else {
					$yksikko = $weight;
				}

				$ilm_toim = floatval($this->rahti_ilm_toim);

				if ( ($ilm_toim !='') && ($ilm_toim <= floatval($cart_price))  ) {
					$lopullinen_hinta = '0';
				} else {
					if ( (0 <= $yksikko) && ($yksikko <= $paino1_hinta) ) {
						$lopullinen_hinta = $this->rahti_hinta1 + $this->rahti_kas_kulut;
					} elseif ( ($paino1_hinta <= $yksikko) && ($yksikko <= $paino2_hinta) ) {
						$lopullinen_hinta = $this->rahti_hinta2 + $this->rahti_kas_kulut;
					} elseif ( ($paino2_hinta <= $yksikko) && ($yksikko <= $paino3_hinta) ) {
						$lopullinen_hinta = $this->rahti_hinta3 + $this->rahti_kas_kulut;
					} elseif ( ($paino3_hinta <= $yksikko) && ($yksikko <= $paino4_hinta) ) {
						$lopullinen_hinta = $this->rahti_hinta4 + $this->rahti_kas_kulut;
					} elseif ( ($paino4_hinta <= $yksikko) && ($yksikko <= $paino5_hinta) ) {
						$lopullinen_hinta = $this->rahti_hinta5 + $this->rahti_kas_kulut;
					} else {
						$lopullinen_hinta = $this->rahti_hinta5 + $this->rahti_kas_kulut;
					}
				}

				$rate = apply_filters('wb_posti_rahti_rate_filter', array(
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

add_action( 'woocommerce_shipping_init', 'WB_Rahti_Posti_Shipping_Method_init' );

function add_WB_Rahti_Posti_Shipping_Method( $methods ) {

	$methods['wb_rahti_posti_shipping_method'] = 'WB_Rahti_Posti_Shipping_Method';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_WB_Rahti_Posti_Shipping_Method' );

/**
* Hide cart if max weight exceeds
*
* @param $rates $package
* @return void
*/
function hide_show_posti_rahti( $rates, $package ) {
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
			if (strpos($shipping_id, 'wb_rahti_posti_shipping_method') !== false) {
				$get_the_id = str_replace('wb_rahti_posti_shipping_method', '', $shipping_id);
				$get_the_id = str_replace(':', '', $get_the_id);
			}
		}

		$kassan_tiedot = new rahti_posti_cart_items();

		$length = $kassan_tiedot->pituus();
		$width = $kassan_tiedot->leveys();
		$height = $kassan_tiedot->korkeus();
		$weight = $kassan_tiedot->paino();

		$weight = explode(",", $woocommerce->cart->cart_contents_weight);

		$rahti_shipping_method = new WB_Rahti_Posti_Shipping_Method( $instance_id = $get_the_id );

		$max_paino = $rahti_shipping_method->rahti_max_paino;

		if($rahti_shipping_method->rahti_max_korkeus != '') {
			$max_korkeus = $rahti_shipping_method->rahti_max_korkeus;
		} else {
			$max_korkeus = 60;
		}

		if($rahti_shipping_method->rahti_max_leveys != '') {
			$max_leveys = $rahti_shipping_method->rahti_max_leveys;
		} else {
			$max_leveys = 60;
		}

		if($rahti_shipping_method->rahti_max_pituus != '') {
			$max_pituus = $rahti_shipping_method->rahti_max_pituus;
		} else {
			$max_pituus = 100;
		}

		if( $max_paino == '') {
			$max_paino = 30;
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

		if($weight == null OR $weight == '') {
			$weight = array(0);
		}

		if( max($height) > $max_korkeus OR max($length) > $max_pituus OR max($width) > $max_leveys OR max($weight) > $max_paino ) {
		$new_rates = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( 'wb_rahti_posti_shipping_method' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}
			
			return $new_rates;

		} else {
			return $rates;
		}
	}

}

add_filter( 'woocommerce_package_rates', 'hide_show_posti_rahti' , 10, 2 );
