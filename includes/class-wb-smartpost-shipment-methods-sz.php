<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * sz_WB_Posti_SmartPost_Shipping_Method Class
 *
 * @class sz_WB_Posti_SmartPost_Shipping_Method
 * @version	1.0.0
 * @since 1.0.0
 * @package	WB_Posti
 * @author Webbisivut.org
 */
 /**
  * Get pickup points
  *
  * @access public
  * @return string
  */
 function smartpost_posti_noutopisteet_function_sz() {

 	$noutopiste = esc_attr($_POST['datedata']);

	if (preg_match('/^\d{5}$/', $noutopiste)) {
 		$zipcode = $noutopiste;
 	} else if (preg_match('/^[a-zA-ZäöåÄÖÅ]+$/', $noutopiste)) {
 		$zipname = $noutopiste;
 	} else {
 		echo '<span class="smartpost-posti-return-sz-bad-input">';
 		echo __('Anna toimiva postinumero tai paikkakunta ja hae uudelleen.', 'wb-posti');
 		echo '</span>';
 		return ;
 	}

 	if (isset($zipcode)) {
		$curl_url_1 = "https://locationservice.posti.com/api/2/location?zipCode=$zipcode";
	}

	if (isset($zipname)) {
		$curl_url_1 = "https://locationservice.posti.com/api/2/location?city=$zipname";
	}

 	ini_set('default_charset', 'utf-8');

 	$ch_1 = curl_init($curl_url_1);
 	curl_setopt($ch_1, CURLOPT_SSL_VERIFYHOST, 2);
 	curl_setopt($ch_1, CURLOPT_SSL_VERIFYPEER, 1);
 	curl_setopt($ch_1, CURLOPT_POST, 0);
 	curl_setopt($ch_1, CURLOPT_HEADER, 0);
 	curl_setopt($ch_1, CURLOPT_TIMEOUT, 120);
 	curl_setopt($ch_1, CURLOPT_RETURNTRANSFER, 1);
 	curl_setopt($ch_1, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
 	$output_1 = curl_exec($ch_1);
 	curl_close($ch_1);

	$json_1 = json_decode($output_1, true);
	 
    // just fetch long & lat from the first item, then break
	foreach ($json_1['locations'] as $postipiste) {
		$maplongitude = str_replace(',', '.', $postipiste['location']['lon']);
		$maplatitude = str_replace(',', '.', $postipiste['location']['lat']);
		break;
	}

 	if (!isset($maplongitude, $maplatitude)) {
 		echo '<span class="smartpost-posti-return-sz-bad-input">';
 		echo __("Ei SmartPOST noutopisteitä saatavilla kyseiselle paikkakunnalle. Voit kokeilla toista paikkakuntaa tai valitse toinen toimitustapa.", "wb-posti");
 		echo '</span>';
 		return ;
 	}

 	$pickupPointsUrl = apply_filters('wb_posti_smartpost_pickup_points_url', 'https://locationservice.posti.com/api/2/location?types=SMARTPOST&');
 	$max_results = 30;
	$curl_url_2 = $pickupPointsUrl . "lng=$maplongitude&lat=$maplatitude&top=$max_results";

 	$ch_2 = curl_init($curl_url_2);
 	curl_setopt($ch_2, CURLOPT_SSL_VERIFYHOST, 2);
 	curl_setopt($ch_2, CURLOPT_SSL_VERIFYPEER, 1);
 	curl_setopt($ch_2, CURLOPT_POST, 0);
 	curl_setopt($ch_2, CURLOPT_HEADER, 0);
 	curl_setopt($ch_2, CURLOPT_TIMEOUT, 120);
 	curl_setopt($ch_2, CURLOPT_RETURNTRANSFER, 1);
 	curl_setopt($ch_2, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
 	$output_2 = curl_exec($ch_2);
 	curl_close($ch_2);

	$json2 = json_decode($output_2, true);
	 
    echo '<span class="required-smartpost-posti-pickup-point-sz">'. __('Valitse haluamasi noutopiste:','wb-posti').'</span>';
 	echo '<select id="smartpost_posti_noutopiste-result-sz" name="smartpost_posti_noutopiste-result-sz" class="smartpost_posti_noutopiste-result-sz">';
 		foreach ($json2['locations'] as $v) {
			$pickupAdd = mb_strtolower($v['publicName']['fi'] .', '. $v['address']['fi']['streetName'] . ' ' . $v['address']['fi']['streetNumber'] .', '. $v['postalCode'] .' '. $v['address']['fi']['postalCodeName'], 'UTF-8');
			$pickupAdd = mb_convert_case($pickupAdd, MB_CASE_TITLE, "UTF-8");

			$pickupAddValue = mb_strtolower($v['pupCode'] . '; ' . $v['publicName']['fi'] .'; '. $v['address']['fi']['streetName'] . ' ' . $v['address']['fi']['streetNumber'] .';'. $v['postalCode'] .';'. $v['address']['fi']['postalCodeName'] .';' . $v['pupCode'], 'UTF-8');
			$pickupAddValue = mb_convert_case($pickupAddValue, MB_CASE_TITLE, "UTF-8");

			echo '<option value="' . $pickupAddValue . '">' . $pickupAdd . '</option>';
		}
 	echo '</select>';

 }

 add_action( 'wp_ajax_nopriv_smartpost_posti_noutopisteet_sz', 'smartpost_posti_noutopisteet_function_sz' );
 add_action( 'wp_ajax_smartpost_posti_noutopisteet_sz', 'smartpost_posti_noutopisteet_function_sz' );


/**
 * Get cart items
 *
 * @access public
 * @return string
 */
class cart_items_posti_sz {

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

	// Kertoo yhteen tuotteiden korkeuden perustuen qty määrään
	public function qty() {
		$kaikki_tuotteet_qty = array();
		$kaikki_tuotteet_qty_korkeus = array();

		$items_qty = $this->hae_woo();
		foreach ($items_qty as $item_qty) {
			$height_qty = floatval($item_qty['data']->get_height());

			if($height_qty == '' OR $height_qty == null) {
				$height_qty = 0;
			}
			array_push($kaikki_tuotteet_qty_korkeus, $height_qty);
		}

		$items = $this->hae_woo();
		foreach ($items as $item => $values) {
			$qty = $values['quantity'];

			array_push($kaikki_tuotteet_qty, $qty);
		}

		$kaikki_qty_yht = array();

		for($i = 0; $i < count($kaikki_tuotteet_qty_korkeus); $i++) {
			array_push($kaikki_qty_yht, $kaikki_tuotteet_qty_korkeus[$i] * $kaikki_tuotteet_qty[$i]);
		}

		return $kaikki_qty_yht;
	}

}

/**
 * Our main function
 *
 * @access public
 * @return void
 */
function sz_WB_Posti_SmartPost_Shipping_Method_Init() {

	if ( ! class_exists( 'sz_WB_Posti_SmartPost_Shipping_Method' ) ) {

		class sz_WB_Posti_SmartPost_Shipping_Method extends WC_Shipping_Method {


			/**
			* Constructor for Posti shipping class
			*
			* @access public
			* @return void
			*/
			public function __construct( $instance_id = 0 ) {

				$this->id = 'sz_wb_posti_smartpost_shipping_method'; // Id for your shipping method. Should be uunique.
				$this->instance_id = absint( $instance_id );
				$this->method_title = __( 'Postipaketti noutopistevalinnalla (Posti Toimitustavat)', 'wb-posti' ); // Title shown in admin
				$this->method_description = __( 'Postipaketti noutopisterekisterillä toimitustapa', 'wb-posti' ); // Description shown in admin
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

				$this->smartpost_korkeus1	   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_korkeus1') ));
				$this->smartpost_hinta1		   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_hinta1') ));

				$this->smartpost_korkeus2	   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_korkeus2') ));
				$this->smartpost_hinta2		   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_hinta2') ));

				$this->smartpost_korkeus3	   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_korkeus3') ));
				$this->smartpost_hinta3		   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_hinta3') ));

				$this->smartpost_korkeus4	   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_korkeus4') ));
				$this->smartpost_hinta4		   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_hinta4') ));

				$this->smartpost_kas_kulut	   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_kas_kulut') ));
				$this->smartpost_ilm_toim  	   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_ilm_toim') ));

				$this->smartpost_max_korkeus   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_max_korkeus') ));
				$this->smartpost_max_pituus    = str_replace(",", ".", esc_attr( $this->get_option('smartpost_max_pituus') ));
				$this->smartpost_max_leveys    = str_replace(",", ".", esc_attr( $this->get_option('smartpost_max_leveys') ));
				$this->smartpost_max_paino     = str_replace(",", ".", esc_attr( $this->get_option('smartpost_max_paino') ));

				$this->smartpost_yht_korkeus   = str_replace(",", ".", esc_attr( $this->get_option('smartpost_yht_korkeus') ));

				$this->smartpost_kuponki	   = esc_attr( $this->get_option('smartpost_kuponki') );
				$this->sp_kuponki_kaikki	   = esc_attr( $this->get_option('sp_kuponki_kaikki') );

				$this->tax_status	  	  	   = $this->get_option('tax_status');

				$this->title 				   = $this->get_option( 'title' );
				$this->availability 		   = $this->get_option( 'availability' );
				$this->countries 			   = $this->get_option( 'countries' );
				$this->max_weight 			   = $this->get_option( 'smartpost_max_weight' );

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

			}

			function init_form_fields() {
            global $woocommerce;

				$this->instance_form_fields = array(
					'title'	   	  		   => array(
						'title'            => __('Toimitustavan nimi', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => 'Nouto pakettiautomaatista',
						'description'      => __('Anna toimitustavalle nimi jonka asiakas näkee kassalla.', 'wb-posti'),
						'default'          => __('Nouto pakettiautomaatista')
					),
					'smartpost_korkeus1'   => array(
						'title'            => __('Paketti-S korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '12',
						'description'      => __('Ilmoita S-paketin max-korkeus joko Esim. 12. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('12')
					),
					'smartpost_hinta1'	   	   => array(
						'title'            => __('Paketti-S Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '6.90',
						'description'      => __('Ilmoita S-paketin hinta Esim. 6.90 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('6.90')
					),
					'smartpost_korkeus2'   => array(
						'title'            => __('Paketti-M korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '20',
						'description'      => __('Ilmoita M-paketin max-korkeus Esim. 20. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('20')
					),
					'smartpost_hinta2'	   	   => array(
						'title'            => __('Paketti-M Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '7.90',
						'description'      => __('Ilmoita M-paketin hinta Esim. 7.90 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('7.90')
					),
					'smartpost_korkeus3'   => array(
						'title'            => __('Paketti-L korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '38',
						'description'      => __('Ilmoita L-paketin max-korkeus Esim. 38. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('38')
					),
					'smartpost_hinta3'	   	   => array(
						'title'            => __('Paketti-L Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '10.00',
						'description'      => __('Ilmoita L-paketin hinta Esim. 10.00 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('10.00')
					),
					'smartpost_korkeus4'   => array(
						'title'            => __('Paketti-XL korkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '60',
						'description'      => __('Ilmoita XL-paketin max-korkeus Esim. 60. Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>. Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('60')
					),
					'smartpost_hinta4'	   	   => array(
						'title'            => __('Paketti-XL Hinta', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '12.00',
						'description'      => __('Ilmoita XL-paketin hinta Esim. 12.00 Voit käyttää referenssinä: <a href="http://www.posti.fi/henkiloasiakkaat/ohjeet/hinnat.html" target="_blank">Postin hinnastoa</a>', 'wb-posti'),
						'default'          => __('12.00')
					),
					'smartpost_ilm_toim'	   => array(
						'title'            => __('SmartPost ilmaisen toimituksen raja', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '100',
						'description'      => __('Anna summa jonka jälkeen ei lisätä toimituskuluja. Jätä tyhjäksi jos et halua käyttää tätä toimintoa.', 'wb-posti'),
						'default'          => __('')
					),
					'smartpost_kas_kulut'	   => array(
						'title'            => __('Käsittelykulut', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '0',
						'description'      => __('Lisää tähän mahdolliset käsittelykulut.', 'wb-posti'),
						'default'          => __('0')
					),
					'smartpost_max_weight' => array(
						'title'            => __('Piilota SmartPost kokonaispainon ylittäessä maksimipainoraja?', 'wb-posti'),
						'type'             => 'select',
						'options'		   => array( 'kylla' => 'Kyllä', 'ei' => 'Ei' ),
						'description'      => __('Jos haluat piilottaa SmartPost toimitustavan kun kassan kokonaispaino ylittää maksimipainorajan, valitse tähän "Kyllä". Muussa tapauksessa, valitse tähän "Ei"', 'wb-posti'),
						'default'		   => 'kylla'
					),
					'smartpost_yht_korkeus' => array(
						'title'            => __('Ota huomioon yhteenlaskettu maksimikorkeus?', 'wb-posti'),
						'type'             => 'select',
						'options'		   => array( 'kylla' => 'Kyllä', 'ei' => 'Ei' ),
						'description'      => __('Jos haluat että ohjelma laskee yhteen tuotteiden korkeuden, ja ottaa tämän huomioon toimituskuluja laskettaessa, valitse "Kyllä". Muussa tapauksessa, valitse tähän "Ei". Kokonaiskorkeuden ylittäessä sallitun rajan, poistetaan toimitustapa kassalla käytöstä.', 'wb-posti'),
						'default'		   => 'ei'
					),
					'smartpost_max_korkeus' => array(
						'title'            => __('Maksimikorkeus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '60',
						'description'      => __('Tuotteen maksimikorkeus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 60cm. . Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('60')
					),
					'smartpost_max_pituus' => array(
						'title'            => __('Maksimipituus', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '60',
						'description'      => __('Tuotteen maksimipituus, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 60cm. . Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('60')
					),
					'smartpost_max_leveys' => array(
						'title'            => __('Maksimileveys', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '36',
						'description'      => __('Tuotteen maksimileveys, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 36cm. . Huom!! Mitat tulee antaa samassa mittayksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('36')
					),
					'smartpost_max_paino' => array(
						'title'            => __('Maksimipaino', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '35',
						'description'      => __('Tuotteen maksimipaino, jolloin toimitustapaa ei enää näytetä kassalla. Oletus 35kg. . Huom!! Paino tulee antaa samassa painoyksikössä kuin ne on asetettu WooCommerceen!', 'wb-posti'),
						'default'          => __('35')
					),
					'smartpost_kuponki'	   => array(
						'title'            => __('Kuponki', 'wb-posti'),
						'type'             => 'text',
						'placeholder'	   => '',
						'description'      => __('Anna tähän kuponkikoodi joka oikeuttaa ilmaiseen toimitukseen. Jos haluat antaa useamman koodin, erottele koodit pilkulla.', 'wb-posti'),
						'default'          => __('')
					),
					'sp_kuponki_kaikki' => array(
							'title'			=> __( 'Salli kaikki kupongit', 'wb-posti' ),
							'type'			=> 'select',
							'description'	=> 'Jos valittuna, kuponkikoodeja ei tarvitse erikseen lisätä, vaan ilmainen toimitus sallitaan miltä tahansa kupongilta, jolle se on määritelty kohdassa WooCommerce - Kupongit.',
							'default'		=> 'ei',
							'options'		=> array(
								'kylla'		=> __( 'Kyllä', 'wb-posti' ),
								'ei'		=> __( 'Ei', 'wb-posti' ),
							)
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

				$kassan_tiedot = new cart_items_posti_sz();

				$length = $kassan_tiedot->pituus();
				$width = $kassan_tiedot->leveys();
				$height = $kassan_tiedot->korkeus();

				$max_weight_select = $this->max_weight;

				$korkeus1 = $this->smartpost_korkeus1;
				$korkeus2 = $this->smartpost_korkeus2;
				$korkeus3 = $this->smartpost_korkeus3;
				$korkeus4 = $this->smartpost_korkeus4;

				$qty = $kassan_tiedot->qty();

				$max_korkeus_select = $this->smartpost_yht_korkeus;

				if ( $max_korkeus_select == 'kylla' ) {
					$height = $qty;
				} else {
					$height = $kassan_tiedot->korkeus();
				}

				$ilm_toim = floatval($this->smartpost_ilm_toim);

				// Kuponki
				$has_coupon = false;
				$all_coupons = array();
				$annettu_koodi = $this->smartpost_kuponki;
				$annettu_koodi_array_or_not = false;

				$salli_kaikki_kupongit = $this->sp_kuponki_kaikki;

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

				if ( ($ilm_toim !='') && ($ilm_toim <= floatval($cart_price)) OR $has_coupon ) {
					$lopullinen_hinta = '0';
				} else {
					if( 0 <= max($height) && max($height) <= $korkeus1 ) {
					  $lopullinen_hinta = $this->smartpost_hinta1 + $this->smartpost_kas_kulut;
					} elseif ( $korkeus1 <= max($height) && max($height) <= $korkeus2 ) {
						$lopullinen_hinta = $this->smartpost_hinta2 + $this->smartpost_kas_kulut;
					} elseif ( $korkeus2 <= max($height) && max($height) <= $korkeus3 ) {
						$lopullinen_hinta = $this->smartpost_hinta3 + $this->smartpost_kas_kulut;
					} elseif ( $korkeus3 <= max($height) && max($height) <= $korkeus4 ) {
						$lopullinen_hinta = $this->smartpost_hinta4 + $this->smartpost_kas_kulut;
					} else {
						$lopullinen_hinta = $this->smartpost_hinta4 + $this->smartpost_kas_kulut;
					}
				}

				$rate = apply_filters('wb_posti_smartpost_rate_filter', array(
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

add_action( 'woocommerce_shipping_init', 'sz_WB_Posti_SmartPost_Shipping_Method_init' );

function add_sz_WB_Posti_SmartPost_Shipping_Method( $methods ) {

	$methods['sz_wb_posti_smartpost_shipping_method'] = 'sz_WB_Posti_SmartPost_Shipping_Method';
	return $methods;

}

add_filter( 'woocommerce_shipping_methods', 'add_sz_WB_Posti_SmartPost_Shipping_Method' );

/**
* Hide cart if max weight exceeds
*
* @param $rates $package
* @return void
*/
function hide_show_wb_smartpost_sz( $rates, $package ) {
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
			if (strpos($shipping_id, 'sz_wb_posti_smartpost_shipping_method') !== false) {
				$get_the_id = str_replace('sz_wb_posti_smartpost_shipping_method', '', $shipping_id);
				$get_the_id = str_replace(':', '', $get_the_id);
			}
		}

		$kassan_tiedot = new cart_items_posti_sz();

		$length = $kassan_tiedot->pituus();
		$width = $kassan_tiedot->leveys();
		$height = $kassan_tiedot->korkeus();
		$weight = $kassan_tiedot->paino();

		$total_weight = explode(",", $woocommerce->cart->cart_contents_weight);

		$smartpost_shipping_method = new sz_WB_Posti_SmartPost_Shipping_Method( $instance_id = $get_the_id );

		$max_weight_select = $smartpost_shipping_method->max_weight;
		$max_korkeus_select = $smartpost_shipping_method->smartpost_yht_korkeus;

		$qty = array_sum($kassan_tiedot->qty());

		if(!empty($smartpost_shipping_method->smartpost_max_korkeus)) {
			$max_korkeus = $smartpost_shipping_method->smartpost_max_korkeus;
		} else {
			$max_korkeus = 60;
		}

		if(!empty($smartpost_shipping_method->smartpost_max_leveys)) {
			$max_leveys = $smartpost_shipping_method->smartpost_max_leveys;
		} else {
			$max_leveys = 60;
		}

		if(!empty($smartpost_shipping_method->smartpost_max_pituus)) {
			$max_pituus = $smartpost_shipping_method->smartpost_max_pituus;
		} else {
			$max_pituus = 36;
		}

		if(!empty($smartpost_shipping_method->smartpost_max_paino)) {
			$max_paino = $smartpost_shipping_method->smartpost_max_paino;
		} else {
			$max_paino = 35;
		}

		if ( $max_weight_select == 'kylla' ) {
			$max_weight = $max_paino;
		} elseif ( $max_weight_select == 'ei' ) {
			$max_weight = 999999;
		} else {
			$max_weight = 999999;
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

		if ( max($height) > $max_korkeus OR max($length) > $max_pituus OR max($width) > $max_leveys OR max($weight) > $max_paino OR max($total_weight) > $max_weight OR $max_korkeus_select == 'kylla' && $qty > $max_korkeus ) {
			$new_rates = array();

			foreach ( $rates as $rate_id => $rate ) {
				if ( 'sz_wb_posti_smartpost_shipping_method' !== $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
				}
			}

			return $new_rates;

		} else {
			return $rates;
		}
	}

}

add_filter( 'woocommerce_package_rates', 'hide_show_wb_smartpost_sz' , 10, 2 );

/**
* Get the PickupPoints
*
* @since 1.0.0
* @return void
*/
function hide_show_wb_smartpost_sz2( $checkout ) {

	echo '
	<div class="smartpost-posti-wrap-sz">
		<div class="js-ajax-php-json-posti-sz">
			<div class="required-city-smartpost-posti-sz">'.__('Anna paikkakunta tai postinumero ja paina Hae:','wb-posti').'</div>
			<input type="text" id="smartpost_noutopiste_posti-sz" class="smartpost_noutopiste_posti-sz" name="smartpost_noutopiste_posti-sz" value="" placeholder="'. __('Paikkakunta tai postinumero*','wb-posti').'" />
			<button class="js-ajax-php-json-posti-button-sz">'. __('Hae','wb-posti') .'</button><span class="loading-img-smartpost-posti"></span>
		</div>

		<div class="smartpost-posti-return-sz"></div>
	</div>
	';
}

add_action( 'woocommerce_checkout_order_review','hide_show_wb_smartpost_sz2', 20);

/**
 * Detect Klarna pickup point plugin. For use on Front End only.
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'wb-klarna-pickup/wb-klarna-pickup.php' ) OR is_plugin_active( 'wb-klarna-pickup-master/wb-klarna-pickup.php' ) ) {
	add_action('show_pickup_point_action_hook', 'hide_show_wb_smartpost_sz2', 20);
	//add_action('woocommerce_proceed_to_checkout','hide_show_wb_smartpost_sz2', 10);
}

/**
* Update the Order Meta With Field Value
*
* @since 1.0.0
* @return void
*/
function hide_show_wb_posti_smartpost_sz_update_order_meta( $order_id ) {
	global $woocommerce;

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_method = $chosen_methods[0];

	if ( $chosen_method == stristr($chosen_method, 'sz_wb_posti_smartpost_shipping_method') && ! empty( $_POST['smartpost_posti_noutopiste-result-sz'] ) ) {
		update_post_meta( $order_id, 'smartpost_posti_noutopiste-result-sz', sanitize_text_field( $_POST['smartpost_posti_noutopiste-result-sz'] ) );

		$getPickupLocation = sanitize_text_field( $_POST['smartpost_posti_noutopiste-result-sz'] );
		$getPickupLocation2 = str_replace(";", ", ", $getPickupLocation);

		update_post_meta( $order_id, 'smartpost_posti_noutopiste-result-sz-b',  $getPickupLocation2);

		$arr = explode(';', sanitize_text_field( $_POST['smartpost_posti_noutopiste-result-sz'] ));
		if(array_key_exists(5, $arr)) {
			update_post_meta( $order_id, 'PupCode',  str_replace(' ', '', $arr[5]));
		}
		
	}


}

add_action( 'woocommerce_checkout_update_order_meta', 'hide_show_wb_posti_smartpost_sz_update_order_meta' );

/**
* Display field value on the order edit page
*
* @since 1.0.0
* @return void
*/
function hide_show_wb_smartpost_sz_display_admin_order_meta($order){

	$value = get_post_meta( $order->get_order_number(), 'smartpost_posti_noutopiste-result-sz-b', true );
	if ( ! empty($value) ) {
		echo '<p><strong>'.__('Postin noutopiste', 'wb-posti').':</strong> ' . $value . '</p>';
	}

}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'hide_show_wb_smartpost_sz_display_admin_order_meta', 10, 1 );

/**
 * Display meta field value on order details (shown on thankyou page)
 *
 * @since 1.0.0
 * @return void
 */
function wb_smartpost_sz_display_order_details_meta_thankyou($order){
	$value = get_post_meta( $order->get_order_number(), 'smartpost_posti_noutopiste-result-sz', true );
	if ( ! empty ($value) && $value != 'Ei käytössä' ) {
		echo 
		'<tr>
			<th>'.__('Postin noutopiste-b', 'wb-posti').':</th>
			<td>'.$value.'</td>
		</tr>';
	}
}

add_action( 'woocommerce_order_details_after_customer_details', 'wb_smartpost_sz_display_order_details_meta_thankyou', 10, 1 );

/**
* Process the checkout
*
* @since 1.0.0
* @return void
*/
function WB_Posti_checkout_field_process_sz() {
	// Check if set, if its not set add an error.
  global $woocommerce;

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_method = $chosen_methods[0];

	if( stristr($chosen_method, 'sz_wb_posti_smartpost_shipping_method') && ! $_POST['smartpost_posti_noutopiste-result-sz'] && $chosen_method !== NULL ) {
		wc_add_notice( __( 'Virhe! SmartPOST noutopiste puuttuu!', 'wb-posti' ), 'error' );
	}
}

add_action('woocommerce_checkout_process', 'WB_Posti_checkout_field_process_sz');

/**
* Display field value on mails
*
* @since 1.4.8
* @return void
*/
function wb_posti_woocommerce_email_order_meta_keys( $order ) {
	$hae_tilauksen_noutopiste = get_post_meta($order->get_order_number(), 'smartpost_posti_noutopiste-result-sz', true);
	$hae_tilauksen_noutopiste2 = get_post_meta($order->get_order_number(), 'smartpost_posti_noutopiste-result-sz-b', true);
	$noutopiste_array = explode(";", $hae_tilauksen_noutopiste);

	if($hae_tilauksen_noutopiste != '' && $hae_tilauksen_noutopiste2 !='Ei käytössä' && preg_match('/,/', $hae_tilauksen_noutopiste2)) {
		echo '<div style="margin-top: 10px; margin-bottom: 10px;"></div>';
		echo '<b>' . __('Postin noutopiste: ', 'wb-posti') . '</b>' . ltrim($noutopiste_array[1]) .', '. ltrim($noutopiste_array[2]) .', '. ltrim($noutopiste_array[3]) .' '. ltrim($noutopiste_array[4]);					
	}
}

add_action('woocommerce_email_after_order_table', 'wb_posti_woocommerce_email_order_meta_keys', 10 , 1);

/**
* Update pickup points after checkout
*
* @since 2.0
* @return void
*/
function paivita_smartship_noutopiste_posti($order_id) {

	global $woocommerce;
	$order = wc_get_order( $order_id );

	$items = $order->get_items();

	$itemsArray = array();

	$is_virtual = false;

	foreach ( $items as $item ) {
		$product_id = $item['product_id'];
		array_push($itemsArray, $product_id);
	}

	foreach($itemsArray as $id) {
		$get_virtual = get_post_meta( $id, '_virtual', true );

		if($get_virtual == 'true') {
			$is_virtual = true;
		}
	}

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

	foreach( $chosen_methods as $shipping_method_used) {
		if(!stristr($shipping_method_used, 'sz_wb_posti_smartpost_shipping_method') OR $is_virtual == true) {
			update_post_meta( $order_id, 'smartpost_posti_noutopiste-result-sz-b', 'Ei käytössä' );
		}
	}

}

add_action("woocommerce_payment_complete", "paivita_smartship_noutopiste_posti", 10, 1);