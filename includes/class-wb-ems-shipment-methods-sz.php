<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WB_EMS_Posti_Shipping_Method Class
 *
 * @class WB_EMS_Posti_Shipping_Method
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Prinetti
 * @author Webbisivut.org
 */

 /**
 * Get cart items
 *
 * @access public
 * @return string
 */
class cart_items_ems_posti {

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
 */
function WB_EMS_Posti_Shipping_Method_Init() {

	if ( ! class_exists( 'WB_EMS_Posti_Shipping_Method' ) ) {

		class WB_EMS_Posti_Shipping_Method extends WC_Shipping_Method {

			/**
			* Constructor for Posti shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {

				$this->id = 'wb_ems_posti_shipping_method'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Posti EMS Ulkomaan toimitus (Posti Toimitustavat)', 'wb-posti' ); // Title shown in admin
				$this->method_description = __( 'Posti EMS Ulkomaan toimitus', 'wb-posti' ); // Description shown in admin
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

				$this->korkeus_vai_paino   = $this->get_option('korkeus_vai_paino');
				
				$this->ems_paino1		   = str_replace(",", ".", esc_attr( $this->get_option('ems_paino1') ));
				$this->ems_hinta1		   = str_replace(",", ".", esc_attr( $this->get_option('ems_hinta1') ));

				$this->ems_paino2		   = str_replace(",", ".", esc_attr( $this->get_option('ems_paino2') ));
				$this->ems_hinta2		   = str_replace(",", ".", esc_attr( $this->get_option('ems_hinta2') ));

				$this->ems_paino3		   = str_replace(",", ".", esc_attr( $this->get_option('ems_paino3') ));
				$this->ems_hinta3		   = str_replace(",", ".", esc_attr( $this->get_option('ems_hinta3') ));

				$this->ems_paino4		   = str_replace(",", ".", esc_attr( $this->get_option('ems_paino4') ));
				$this->ems_hinta4		   = str_replace(",", ".", esc_attr( $this->get_option('ems_hinta4') ));

				$this->ems_paino5		   = str_replace(",", ".", esc_attr( $this->get_option('ems_paino5') ));
				$this->ems_hinta5		   = str_replace(",", ".", esc_attr( $this->get_option('ems_hinta5') ));

				$this->ems_kas_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('ems_kas_kulut') ));
				$this->ems_ilm_toim	  	   = str_replace(",", ".", esc_attr( $this->get_option('ems_ilm_toim') ));

				$this->ems_area1_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('ems_area1_kulut') ));
				$this->ems_area2_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('ems_area2_kulut') ));
				$this->ems_area3_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('ems_area3_kulut') ));
				$this->ems_area4_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('ems_area4_kulut') ));

        		$this->ems_max_korkeus     = str_replace(",", ".", esc_attr( $this->get_option('ems_max_korkeus') ));
				$this->ems_max_pituus      = str_replace(",", ".", esc_attr( $this->get_option('ems_max_pituus') ));
				$this->ems_max_leveys      = str_replace(",", ".", esc_attr( $this->get_option('ems_max_leveys') ));
				$this->ems_max_paino	   = str_replace(",", ".", esc_attr( $this->get_option('ems_max_paino') ));

				$this->tax_status	  	   = $this->get_option('tax_status');

				$this->title 			   = $this->get_option( 'title' );
				$this->availability 	   = $this->get_option( 'availability' );
				$this->countries 		   = $this->get_option( 'countries' );

				$this->ems_area1_maat	   = $this->get_option( 'ems_area1_maat' );
				$this->ems_area2_maat	   = $this->get_option( 'ems_area2_maat' );
				$this->ems_area3_maat	   = $this->get_option( 'ems_area3_maat' );
				$this->ems_area4_maat	   = $this->get_option( 'ems_area4_maat' );

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			}

			function init_form_fields() {

				$this->instance_form_fields = array(
					'title'	   	  		   => array(
						'title'            => __('Toimitustavan nimi', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => 'Posti EMS Ulkomaan toimitus',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-posti'),
						'default'          => __('Posti EMS Ulkomaan toimitus')
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
					'ems_paino1'	   	   => array(
						'title'            => __('Paketti1 Paino / Korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '2',
						'description'      => __('Ilmoita ensimmäisen paketin max-paino tai korkeus Esim. 2. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('2')
					),
					'ems_hinta1'	   	   => array(
						'title'            => __('Paketti1 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '52',
						'description'      => __('Ilmoita ensimmäisen paketin hinta Esim. 52 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('52')
					),
					'ems_paino2'	   	   => array(
						'title'            => __('Paketti2 Paino / Korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '5',
						'description'      => __('Ilmoita toisen paketin max-paino tai korkeus Esim. 5. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('5')
					),
					'ems_hinta2'	   	   => array(
						'title'            => __('Paketti2 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '65',
						'description'      => __('Ilmoita toisen paketin hinta Esim. 65 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('65')
					),
					'ems_paino3'	   	   => array(
						'title'            => __('Paketti3 Paino / Korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '10',
						'description'      => __('Ilmoita kolmannen paketin max-paino tai korkeus Esim. 10. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('10')
					),
					'ems_hinta3'	   	   => array(
						'title'            => __('Paketti3 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '80',
						'description'      => __('Ilmoita kolmannen paketin hinta Esim. 80 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('80')
					),
					'ems_paino4'	   	   => array(
						'title'            => __('Paketti4 Paino / Korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '15',
						'description'      => __('Ilmoita neljännen paketin max-paino tai korkeus Esim. 15. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('15')
					),
					'ems_hinta4'	   	   => array(
						'title'            => __('Paketti4 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Ilmoita neljännen paketin hinta Esim. 100 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('100')
					),
					'ems_paino5'	   	   => array(
						'title'            => __('Paketti5 Paino / Korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '35',
						'description'      => __('Ilmoita viidennen paketin max-paino tai korkeus Esim. 35. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('35')
					),
					'ems_hinta5'	   	   => array(
						'title'            => __('Paketti5 Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '160',
						'description'      => __('Ilmoita viidennen paketin hinta Esim. 160 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('160')
					),
					'ems_ilm_toim'	   => array(
						'title'            => __('EMS-Paketti ilmaisen toimituksen raja', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Anna summa jonka jälkeen ei lisätä toimituskuluja. Jätä tyhjäksi jos et halua käyttää tätä toimintoa.', 'wb-posti'),
						'default'          => __('')
					),
					'ems_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-posti'),
						'default'          => __('0')
					),
          			'ems_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Tuotteen maksimikorkeus, jolloin tuotetta ei enää näytetä kassalla. Oletus 100cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('100')
					),
					'ems_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '200',
						'description'      => __('Tuotteen maksipituus, jolloin tuotetta ei enää näytetä kassalla. Oletus 200cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('200')
					),
					'ems_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '200',
						'description'      => __('Tuotteen maksileveys, jolloin tuotetta ei enää näytetä kassalla. Oletus 200cm. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('200')
					),
					'ems_max_paino'	   => array(
						'title'            => __('EMS Paketti maksimi Paino / Korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '30',
						'description'      => __('Anna maksimi paino, jonka jälkeen toimitustapaa ei näytetä enää kassalla. Huom!! Paino / korkeus tulee antaa samassa yksikössä kuin se on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('30')
					),
					'ems_area1_kulut'	   => array(
						'title'            => __('Maksuvyöhyke 1 lisähinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän maksuvyöhykkeen lisähinta', 'wb-posti'),
						'default'          => __('0')
					),
					'ems_area1_maat'	   => array(
						'title'            => __('Maksuvyöhyke 1 maat', 'wb-posti'),
						'description'      => __('Lisää tähän maksuvyöhykkeen maat', 'wb-posti'),
						'type'			=> 'multiselect',
							'class'			=> 'wc-enhanced-select',
							'css'			=> 'width: 450px;',
							'default'		=> '',
							'options'		=> WC()->countries->get_shipping_countries(),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Valitse maat', 'wb-posti' )
							)
					),
					'ems_area2_kulut'	   => array(
						'title'            => __('Maksuvyöhyke 2 lisähinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän maksuvyöhykkeen lisähinta', 'wb-posti'),
						'default'          => __('0')
					),
					'ems_area2_maat'	   => array(
						'title'            => __('Maksuvyöhyke 2 maat', 'wb-posti'),
						'description'      => __('Lisää tähän maksuvyöhykkeen maat', 'wb-posti'),
						'type'			=> 'multiselect',
							'class'			=> 'wc-enhanced-select',
							'css'			=> 'width: 450px;',
							'default'		=> '',
							'options'		=> WC()->countries->get_shipping_countries(),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Valitse maat', 'wb-posti' )
							)
					),
					'ems_area3_kulut'	   => array(
						'title'            => __('Maksuvyöhyke 3 lisähinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän maksuvyöhykkeen lisähinta', 'wb-posti'),
						'default'          => __('0')
					),
					'ems_area3_maat'	   => array(
						'title'            => __('Maksuvyöhyke 3 maat', 'wb-posti'),
						'description'      => __('Lisää tähän maksuvyöhykkeen maat', 'wb-posti'),
						'type'			=> 'multiselect',
							'class'			=> 'wc-enhanced-select',
							'css'			=> 'width: 450px;',
							'default'		=> '',
							'options'		=> WC()->countries->get_shipping_countries(),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Valitse maat', 'wb-posti' )
							)
					),
					'ems_area4_kulut'	   => array(
						'title'            => __('Maksuvyöhyke 4 lisähinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän maksuvyöhykkeen lisähinta', 'wb-posti'),
						'default'          => __('0')
					),
					'ems_area4_maat'	   => array(
						'title'            => __('Maksuvyöhyke 4 maat', 'wb-posti'),
						'description'      => __('Lisää tähän maksuvyöhykkeen maat', 'wb-posti'),
						'type'			=> 'multiselect',
							'class'			=> 'wc-enhanced-select',
							'css'			=> 'width: 450px;',
							'default'		=> '',
							'options'		=> WC()->countries->get_shipping_countries(),
							'custom_attributes' => array(
								'data-placeholder' => __( 'Valitse maat', 'wb-posti' )
							)
					),
					'availability' => array(
							'title'			=> __( 'Saatavuus', 'wb-posti' ),
							'type'			=> 'select',
							'class'         => 'wc-enhanced-select',
							'description'	=> '',
							'default'		=> 'including',
							'options'		=> array(
								'including'		=> __( 'Toimitetaan valittuihin maihin', 'wb-posti' ),
								'excluding'		=> __( 'Ei toimiteta valittuihin maihin', 'wb-posti' ),
								'all'		    => __( 'Toimitetaan kaikkiin maihin', 'wb-posti' ),
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

				$woocommerce = function_exists('WC') ? WC() : $GLOBALS['wb-posti'];
				$weight     = $woocommerce->cart->cart_contents_weight;

				$area1_maat = $this->ems_area1_maat;
				$area2_maat = $this->ems_area2_maat;
				$area3_maat = $this->ems_area3_maat;
				$area4_maat = $this->ems_area4_maat;

				$area1_hinta = $this->ems_area1_kulut;
				$area2_hinta = $this->ems_area2_kulut;
				$area3_hinta = $this->ems_area3_kulut;
				$area4_hinta = $this->ems_area4_kulut;

				$country = $package['destination']['country'];

				if( is_array($area1_maat) && in_array( $package['destination']['country'], $area1_maat ) ) {
					$area_hinta = $area1_hinta;
				} elseif ( is_array($area2_maat) && in_array( $package['destination']['country'], $area2_maat ) ) {
					$area_hinta = $area2_hinta;
				} elseif ( is_array($area3_maat) && in_array( $package['destination']['country'], $area3_maat ) ) {
					$area_hinta = $area3_hinta;
				} elseif ( is_array($area4_maat) && in_array( $package['destination']['country'], $area4_maat ) ) {
					$area_hinta = $area4_hinta;
				} else {
					$area_hinta = 0;
				}

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

				$paino1_korkeus = $this->ems_paino1;
				$paino2_korkeus = $this->ems_paino2;
				$paino3_korkeus = $this->ems_paino3;
				$paino4_korkeus = $this->ems_paino4;
				$paino5_korkeus = $this->ems_paino5;

				// Katsotaan kummanko yksikön mukaan hinnoitellaan
				$korkeus_vai_paino = $this->korkeus_vai_paino;

				$kassan_tiedot = new cart_items_ems_posti();
				$height = $kassan_tiedot->korkeus();

				if($korkeus_vai_paino == 'korkeus') {
					$yksikko = max($height);
				} else {
					$yksikko = $weight;
				}

				$ilm_toim = floatval($this->ems_ilm_toim);

				if ( ($ilm_toim !='') && ($ilm_toim <= floatval($cart_price))  ) {
					$lopullinen_hinta = '0';
				} else {
					if ( (0 <= $yksikko) && ($yksikko <= $paino1_korkeus) ) {
						$lopullinen_hinta = $this->ems_hinta1 + $this->ems_kas_kulut + $area_hinta;
					} elseif ( ($paino1_korkeus <= $yksikko) && ($yksikko <= $paino2_korkeus) ) {
						$lopullinen_hinta = $this->ems_hinta2 + $this->ems_kas_kulut + $area_hinta;
					} elseif ( ($paino2_korkeus <= $yksikko) && ($yksikko <= $paino3_korkeus) ) {
						$lopullinen_hinta = $this->ems_hinta3 + $this->ems_kas_kulut + $area_hinta;
					} elseif ( ($paino3_korkeus <= $yksikko) && ($yksikko <= $paino4_korkeus) ) {
						$lopullinen_hinta = $this->ems_hinta4 + $this->ems_kas_kulut + $area_hinta;
					} elseif ( ($paino4_korkeus <= $yksikko) && ($yksikko <= $paino5_korkeus) ) {
						$lopullinen_hinta = $this->ems_hinta5 + $this->ems_kas_kulut + $area_hinta;
					} else {
						$lopullinen_hinta = $this->ems_hinta5 + $this->ems_kas_kulut + $area_hinta;
					}
				}

				$rate = apply_filters('wb_posti_ems_rate_filter', array(
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

add_action( 'woocommerce_shipping_init', 'WB_EMS_Posti_Shipping_Method_init' );

function add_WB_EMS_Posti_Shipping_Method( $methods ) {

	$methods['wb_ems_posti_shipping_method'] = 'WB_EMS_Posti_Shipping_Method';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_WB_EMS_Posti_Shipping_Method' );

function hide_show_posti_ems( $rates, $package ) {
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
			if (strpos($shipping_id, 'wb_ems_posti_shipping_method') !== false) {
				$get_the_id = str_replace('wb_ems_posti_shipping_method', '', $shipping_id);
				$get_the_id = str_replace(':', '', $get_the_id);
			}
		}

		$total_weight = explode(",", $woocommerce->cart->cart_contents_weight);

		$kassan_tiedot = new cart_items_ems_posti();

		$length = $kassan_tiedot->pituus();
		$width = $kassan_tiedot->leveys();
		$height = $kassan_tiedot->korkeus();
		$weight = $kassan_tiedot->paino();

		$ems_shipping_method = new WB_EMS_Posti_Shipping_Method( $instance_id = $get_the_id );

		$max_paino = $ems_shipping_method->ems_max_paino;

		if( $max_paino == '') {
			$max_paino = 30;
		} else {
			$max_paino = $max_paino;
		}

	  if($ems_shipping_method->ems_max_korkeus != '') {
			$max_korkeus = $ems_shipping_method->ems_max_korkeus;
		} else {
			$max_korkeus = 100;
		}

		if($ems_shipping_method->ems_max_leveys != '') {
			$max_leveys = $ems_shipping_method->ems_max_leveys;
		} else {
			$max_leveys = 200;
		}

		if($ems_shipping_method->ems_max_pituus != '') {
			$max_pituus = $ems_shipping_method->ems_max_pituus;
		} else {
			$max_pituus = 200;
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

		if ( max($height) > $max_korkeus OR max($length) > $max_pituus OR max($width) > $max_leveys OR max($weight) > $max_paino ) {
			$new_rates = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( 'wb_ems_posti_shipping_method' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}
			
			return $new_rates;

		} else {
			return $rates;
		}
	}

}

add_filter( 'woocommerce_package_rates', 'hide_show_posti_ems' , 10, 2 );