<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WB_Posti_Toimitustavat {

	/**
	 * The single instance of WB_Posti_Toimitustavat.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {

		$this->_version = $version;
		$this->_token = 'WB_Posti_Toimitustavat';

		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		add_action( 'wp_enqueue_scripts', array($this, 'wb_enqueue_styles_posti_frontend' ) );
		add_action( 'wp_enqueue_scripts', array($this, 'wb_enqueue_posti_scripts_frontend_js' ), 20 );
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'posti_toim_tavat_woo_actions'), 10, 1);
		add_action( 'load-edit.php', array( $this, 'posti_toim_tavat_custom_action' ), 4 );

		// Load backend CSS
		add_action( 'admin_enqueue_scripts', array($this, 'wb_enqueue_styles_posti_toim_backend' ), 20 );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

	}

	/**
	 * Load Frontend CSS
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	function wb_enqueue_styles_posti_frontend () {

		wp_enqueue_style('posti-frontend-css', plugins_url().'/wb-posti-toimitustavat/assets/css/frontend.css', true );

	}

	/**
	 * Load frontend Javascript.
	 */
	function wb_enqueue_posti_scripts_frontend_js () {

		global $woocommerce;

		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );

		wp_localize_script( $this->_token . '-frontend', 'smartPostAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

		if( is_checkout() OR is_cart() ) {
			wp_register_script( $this->_token . '-frontend-posti-js', esc_url( $this->assets_url ) . 'js/wb.posti.frontend.default.js', array( 'jquery' ), $this->_version );
			wp_enqueue_script( $this->_token . '-frontend-posti-js' );
		}

	}

	/**
	 * Load Backend CSS
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	function wb_enqueue_styles_posti_toim_backend () {

		wp_enqueue_style('posti-toim-backend-css', plugins_url().'/wb-posti-toimitustavat/assets/css/backend.css', true );

	}

	/**
	 * Load postiennakkomaksu Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
		public function wb_enqueue_scripts_postiennakko_js () {
			$postiennakko_class = new WB_Posti_Gateway_Postiennakko();
			if( $postiennakko_class->settings['pe_lisamaksu_on_off'] == 'kylla' && is_checkout() OR is_cart() ) {
				wp_register_script( $this->_token . '-frontend-posti-pe-maksu-js', esc_url( $this->assets_url ) . 'js/postiennakkomaksu.js', array( 'jquery' ), $this->_version );
				wp_enqueue_script( $this->_token . '-frontend-posti-pe-maksu-js' );
			}
		}

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wb-posti', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wb-posti';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Posti Toimitustavat WooCommerce Actions
	 *
	 * @since 2.4.0
	 * @access public
	 * @return void
	 */
	 public function posti_toim_tavat_woo_actions($array) {
		
		global $post;
		global $post_type;

		if($post_type == 'shop_order') {
			$order = new WC_Order($post->ID);
			$valid_methods = $this->validMethods();

			$shipping_methods = $order->get_shipping_methods();

			foreach ($shipping_methods as $shipping_method) {
				$shipping_id = $shipping_method['method_id'];
			}

			if(isset($shipping_id)) {
				foreach($valid_methods as $method) {
					if(strpos($shipping_id, $method) !== false) {
						$array['tulostakirje_posti_toim'] = array(
							'url'       => admin_url("edit.php?post=$post->ID&post_type=shop_order&action=tulostakirje_posti_toim"),
							'name'      => __( 'Hae osoitetiedot', 'wb-posti' ),
							'action'    => "tulostakirje_posti_toim"
						);
					}
				}
			}

			return $array;
		}

	}

	/**
	 * Custom Action
	 *
	 * @since 2.4.0
	 * @return void
	 */
	public function posti_toim_tavat_custom_action() {
		
		// Käynnistetään Custom Order Status
		global $typenow;
		$post_type = $typenow;

		if($post_type == 'shop_order') {
			$wp_list_table = _get_list_table('WP_Posts_List_Table');
			$action = $wp_list_table->current_action();

			$allowed_actions = array("tulostakirje_posti_toim");

			if(!in_array($action, $allowed_actions)) return;

			if ($action == 'tulostakirje_posti_toim') {
				$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

				foreach ( $post_ids as $post_id ) {

					$order = wc_get_order( $post_id );

					$etunimi = ucfirst( $order->get_shipping_first_name() );
					$sukunimi = ucfirst( $order->get_shipping_last_name() );
					$osoite1 = ucfirst( $order->get_shipping_address_1() );
					$osoite2 = ucfirst( $order->get_shipping_address_2() );
					$kaupunki = mb_strtoupper( $order->get_shipping_city(), 'UTF-8' );
					$postinumero = $order->get_shipping_postcode();
					$yritys = ucfirst( $order->get_shipping_company() );

					$hae_tilauksen_noutopiste = get_post_meta($order->get_order_number(), 'smartpost_posti_noutopiste-result-sz', true);
					$hae_tilauksen_noutopiste2 = get_post_meta($order->get_order_number(), 'smartpost_posti_noutopiste-result-sz-b', true);

					if($hae_tilauksen_noutopiste != '' && $hae_tilauksen_noutopiste2 !='Ei käytössä' && preg_match('/,/', $hae_tilauksen_noutopiste2)) {
						$noutopiste_array = explode(";", $hae_tilauksen_noutopiste);

						$osoite1 = ucfirst( ltrim($noutopiste_array[0]));
						$osoite2 = ucfirst( ltrim($noutopiste_array[1]));
						$postinumero = ltrim($noutopiste_array[2]);
						$kaupunki = ucfirst( ltrim($noutopiste_array[3]));
					}

					echo $etunimi . ' ' . $sukunimi . ' ' . $yritys . '<br>';
					echo $osoite1 . '<br>';
					if($osoite2 != '') {
						echo $osoite2 . '<br>';
					}
					echo $postinumero . ' ' . $kaupunki . '<br>';

				}

			exit;
			}

		}
	}

	/**
	* Valid Posti toimitustavat shipping methods
	*
	* @since 2.4.0
	* @return array
	*/
	public function validMethods() {
		$valid_methods = array(
			'wb_ems_posti_shipping_method',
			'wb_kirje_shipping_method_sz',
			'wb_posti_ovelle_shipping_method_sz',
			'wb_posti_nouto_shipping_method_sz',
			'wb_posti_postiennakko_shipping_method_sz',
			'wb_rahti_posti_shipping_method',
			'sz_wb_posti_smartpost_shipping_method'
		);

		return $valid_methods;
	}

	/**
	 * Main WB_Posti_Toimitustavat Instance
	 *
	 * Ensures only one instance of WB_Posti_Toimitustavat is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WB_Posti_Toimitustavat()
	 * @return Main WB_Posti_Toimitustavat instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;

	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {

		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );

	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {

		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );

	}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {

		$this->_log_version_number();

	}

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {

		update_option( $this->_token . '_version', $this->_version );

	}
}
