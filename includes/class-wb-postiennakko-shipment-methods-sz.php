<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get cart items
 *
 * @access public
 * @return array
 */
class pe_cart_items_sz {

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
 * WB_Posti_Postiennakko_Shipping_Method_sz Class
 *
 * @class WB_Posti_Postiennakko_Shipping_Method_sz
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Posti_Toimitustavat
 * @author Webbisivut.org
 */

function WB_Posti_Postiennakko_Shipping_Method_sz_Init() {

	if ( ! class_exists( 'WB_Posti_Postiennakko_Shipping_Method_sz' ) ) {

		class WB_Posti_Postiennakko_Shipping_Method_sz extends WC_Shipping_Method {

			/**
			* Constructor for Posti shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {
				$this->id = 'wb_posti_postiennakko_shipping_method_sz'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Postiennakko (Posti Toimitustavat)', 'wb-posti' ); // Title shown in admin
				$this->method_description = __( 'Postin Postiennakko toimitustapa.', 'wb-posti' ); // Description shown in admin
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

				$this->korkeus_vai_paino   			    = $this->get_option('korkeus_vai_paino');

				$this->postiennakko_paino1		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_paino1') ));
				$this->postiennakko_hinta1		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_hinta1') ));

				$this->postiennakko_paino2		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_paino2') ));
				$this->postiennakko_hinta2		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_hinta2') ));

				$this->postiennakko_paino3		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_paino3') ));
				$this->postiennakko_hinta3		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_hinta3') ));

				$this->postiennakko_paino4		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_paino4') ));
				$this->postiennakko_hinta4		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_hinta4') ));

				$this->postiennakko_paino5		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_paino5') ));
				$this->postiennakko_hinta5		   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_hinta5') ));

				$this->postiennakko_ilm_toim    		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_ilm_toim') ));
				$this->postiennakko_kas_kulut   		= str_replace(",", ".", esc_attr( $this->get_option('postiennakko_kas_kulut') ));

        		$this->pe_max_korkeus   			    = str_replace(",", ".", esc_attr( $this->get_option('pe_max_korkeus') ));
				$this->pe_max_pituus   				    = str_replace(",", ".", esc_attr( $this->get_option('pe_max_pituus') ));
				$this->pe_max_leveys    				= str_replace(",", ".", esc_attr( $this->get_option('pe_max_leveys') ));
				$this->pe_max_paino		   				= str_replace(",", ".", esc_attr( $this->get_option('pe_max_paino') ));

				$this->postiennakko_kuponki	   	   		= esc_attr( $this->get_option('postiennakko_kuponki') );
				$this->pe_kuponki_kaikki	   	   		= esc_attr( $this->get_option('pe_kuponki_kaikki') );

				$this->tax_status	  	   			    = $this->get_option('tax_status');

				$this->title 				  		    = $this->get_option( 'title' );
				$this->availability 	  	   			= $this->get_option( 'availability' );
				$this->countries 		   	  			= $this->get_option( 'countries' );

				// Tallennetaan admin asetukset
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

			}

			function init_form_fields() {
				global $woocommerce;

				$this->instance_form_fields = array(
					'title'	   	  		   => array(
						'title'            => __('Toimitustavan nimi', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => 'Postiennakko',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-posti'),
						'default'          => __('Postiennakko')
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
					'postiennakko_paino1'	   	   => array(
						'title'            => __('Paketti1 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Ilmoita ensimmäisen paketin max-paino tai korkeus Esim. 2. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('2')
					),
					'postiennakko_hinta1'	   	   => array(
						'title'            => __('Paketti1 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '7.90',
						'description'      => __('Ilmoita ensimmäisen paketin hinta Esim. 7.90 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('7.90')
					),
					'postiennakko_paino2'	   	   => array(
						'title'            => __('Paketti2 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '5',
						'description'      => __('Ilmoita toisen paketin max-paino tai korkeus Esim. 5. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('5')
					),
					'postiennakko_hinta2'	   	   => array(
						'title'            => __('Paketti2 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '9.30',
						'description'      => __('Ilmoita toisen paketin hinta Esim. 7.90 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('9.30')
					),
					'postiennakko_paino3'	   	   => array(
						'title'            => __('Paketti3 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '10',
						'description'      => __('Ilmoita kolmannen paketin max-paino tai korkeus Esim. 10. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('10')
					),
					'postiennakko_hinta3'	   	   => array(
						'title'            => __('Paketti3 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '10.50',
						'description'      => __('Ilmoita kolmannen paketin hinta Esim. 10.50 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('10.50')
					),
					'postiennakko_paino4'	   	   => array(
						'title'            => __('Paketti4 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '15',
						'description'      => __('Ilmoita neljännen paketin max-paino tai korkeus Esim. 15. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('15')
					),
					'postiennakko_hinta4'	   	   => array(
						'title'            => __('Paketti4 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '14.00',
						'description'      => __('Ilmoita neljännen paketin hinta Esim. 14.00 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('14.00')
					),
					'postiennakko_paino5'	   	   => array(
						'title'            => __('Paketti5 Paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '35',
						'description'      => __('Ilmoita viidennen paketin max-paino tai korkeus Esim. 35. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('35')
					),
					'postiennakko_hinta5'	   	   => array(
						'title'            => __('Paketti5 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '19.70',
						'description'      => __('Ilmoita viidennen paketin hinta Esim. 19.70 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('19.70')
					),
					'postiennakko_ilm_toim'	   => array(
						'title'            => __('Postiennakko ilmaisen toimituksen raja', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Anna summa jonka jälkeen ei lisätä toimituskuluja. Jätä tyhjäksi jos et halua käyttää tätä toimintoa.', 'wb-posti'),
						'default'          => __('')
					),
          			'pe_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Tuotteen maksimikorkeus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 100cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('100')
					),
					'pe_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '60',
						'description'      => __('Tuotteen maksimipituus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 60cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('60')
					),
					'pe_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '60',
						'description'      => __('Tuotteen maksimileveys, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 60cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('60')
					),
					'pe_max_paino'	   => array(
						'title'            => __('Postiennakko maksimi paino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '30',
						'description'      => __('Anna maksimi paino, jonka jälkeen toimitustapaa ei näytetä enää kassalla. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('30')
					),
					'postiennakko_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-posti'),
						'default'          => __('0')
					),
					'postiennakko_kuponki'		   => array(
						'title'            => __('Kuponki', 'wb-prinetti'),
						'type'             => 'text',
						'placeholder'	   => '',
						'description'      => __('Anna tähän kuponkikoodi joka oikeuttaa ilmaiseen toimitukseen. Jos haluat antaa useamman koodin, erottele koodit pilkulla.', 'wb-prinetti'),
						'default'          => __('')
					),
					'pe_kuponki_kaikki' => array(
							'title'			=> __( 'Salli kaikki kupongit', 'wb-prinetti' ),
							'type'			=> 'select',
							'description'	=> 'Jos valittuna, kuponkikoodeja ei tarvitse erikseen lisätä, vaan ilmainen toimitus sallitaan miltä tahansa kupongilta, jolle se on määritelty kohdassa WooCommerce - Kupongit.',
							'default'		=> 'ei',
							'options'		=> array(
								'kylla'		=> __( 'Kyllä', 'wb-prinetti' ),
								'ei'		=> __( 'Ei', 'wb-prinetti' ),
							)
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
					'countries' 			=> array(
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
					))
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
				$weight      = $woocommerce->cart->cart_contents_weight;

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
				$cart_total_price = $cart_price + $cart_tax;

				$paino1_korkeus = $this->postiennakko_paino1;
				$paino2_korkeus = $this->postiennakko_paino2;
				$paino3_korkeus = $this->postiennakko_paino3;
				$paino4_korkeus = $this->postiennakko_paino4;
				$paino5_korkeus = $this->postiennakko_paino5;

				// Katsotaan kummanko yksikön mukaan hinnoitellaan
				$korkeus_vai_paino = $this->korkeus_vai_paino;
				
				$kassan_tiedot = new pe_cart_items_sz();
				$height = $kassan_tiedot->korkeus();

				if($korkeus_vai_paino == 'korkeus') {
					$yksikko = max($height);
				} else {
					$yksikko = $weight;
				}

				$ilm_toim = $this->postiennakko_ilm_toim;

				// Kuponki
				$has_coupon = false;
				$all_coupons = array();
				$annettu_koodi = $this->postiennakko_kuponki;
				$annettu_koodi_array_or_not = false;

				$salli_kaikki_kupongit = $this->pe_kuponki_kaikki;

				if ( $coupons = WC()->cart->get_coupons() ) {
					if($salli_kaikki_kupongit == 'kylla') {
						foreach ( $coupons as $coupon ) {
							if ( $coupon->get_free_shipping() ) {
								$has_coupon = true;
							}
						}
					} else {
						// Tarkistetaan onko useampia koodeja
						if (strpos($annettu_koodi, ',') != false) {
							// On useampia koodeja. Tehdään array
						 $annettu_koodi_array_or_not = true;
						} else {
							// Ei useampia koodeja
						 $annettu_koodi_array_or_not = false;
						}

						foreach ( $coupons as $code => $coupon ) { 
							array_push($all_coupons, $code);
						}

						if($annettu_koodi_array_or_not) {
							$annetut_koodit = explode(",", $annettu_koodi);

							foreach( $annetut_koodit as $koodi) {
								if(isset($coupon) && $coupon->is_valid() && in_array($koodi, $all_coupons)) {
									$has_coupon = true;
								}
							}
						} else {
							if(isset($coupon) && $coupon->is_valid() && in_array($annettu_koodi, $all_coupons)) {
								$has_coupon = true;
							}
						}

					}
				}

				if ( ($ilm_toim !='') && ($ilm_toim <= floatval($cart_price)) OR $has_coupon ) {
					$lopullinen_hinta = '0';
				} else {
					if ( (0 <= $yksikko) && ($yksikko <= $paino1_korkeus) ) {
						$lopullinen_hinta = $this->postiennakko_hinta1 + $this->postiennakko_kas_kulut;
					} elseif ( ($paino1_korkeus <= $yksikko) && ($yksikko <= $paino2_korkeus) ) {
						$lopullinen_hinta = $this->postiennakko_hinta2 + $this->postiennakko_kas_kulut;
					} elseif ( ($paino2_korkeus <= $yksikko) && ($yksikko <= $paino3_korkeus) ) {
						$lopullinen_hinta = $this->postiennakko_hinta3 + $this->postiennakko_kas_kulut;
					} elseif ( ($paino3_korkeus <= $yksikko) && ($yksikko <= $paino4_korkeus) ) {
						$lopullinen_hinta = $this->postiennakko_hinta4 + $this->postiennakko_kas_kulut;
					} elseif ( ($paino4_korkeus <= $yksikko) && ($yksikko <= $paino5_korkeus) ) {
						$lopullinen_hinta = $this->postiennakko_hinta5 + $this->postiennakko_kas_kulut;
					} else {
						$lopullinen_hinta = $this->postiennakko_hinta5 + $this->postiennakko_kas_kulut;
					}
				}

				$rate = apply_filters('wb_posti_postiennakko_rate_filter', array(
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

add_action( 'woocommerce_shipping_init', 'WB_Posti_Postiennakko_Shipping_Method_sz_init' );

function add_WB_Posti_Postiennakko_Shipping_Method_sz( $methods ) {

	$methods['wb_posti_postiennakko_shipping_method_sz'] = 'WB_Posti_Postiennakko_Shipping_Method_sz';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_WB_Posti_Postiennakko_Shipping_Method_sz' );

/**
* Hide cart if max weight exceeds
*
* @param $rates $package
* @return void
*/
function hide_show_wb_pe_sz( $rates, $package ) {
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
			if (strpos($shipping_id, 'wb_posti_postiennakko_shipping_method_sz') !== false) {
				$get_the_id = str_replace('wb_posti_postiennakko_shipping_method_sz', '', $shipping_id);
				$get_the_id = str_replace(':', '', $get_the_id);
			}
		}

	    $kassan_tiedot = new pe_cart_items_sz();

		$length = $kassan_tiedot->pituus();
		$width = $kassan_tiedot->leveys();
		$height = $kassan_tiedot->korkeus();
		$weight = $kassan_tiedot->paino();

		$weight = explode(",", $woocommerce->cart->cart_contents_weight);

		$pe_shipping_method = new WB_Posti_Postiennakko_Shipping_Method_sz( $instance_id = $get_the_id );

		$max_paino = $pe_shipping_method->pe_max_paino;

	  if($pe_shipping_method->pe_max_korkeus != '') {
			$max_korkeus = $pe_shipping_method->pe_max_korkeus;
		} else {
			$max_korkeus = 60;
		}

		if($pe_shipping_method->pe_max_leveys != '') {
			$max_leveys = $pe_shipping_method->pe_max_leveys;
		} else {
			$max_leveys = 60;
		}

		if($pe_shipping_method->pe_max_pituus != '') {
			$max_pituus = $pe_shipping_method->pe_max_pituus;
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
				if ( 'wb_posti_postiennakko_shipping_method_sz' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}

			return $new_rates;

		} else {
			return $rates;
		}
	}

}

add_filter( 'woocommerce_package_rates', 'hide_show_wb_pe_sz' , 10, 2 );
