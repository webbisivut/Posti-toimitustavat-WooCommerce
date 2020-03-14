<?php
/*
 * Plugin Name: Posti toimitustavat
 * Version: 2.7.0.5
 * Plugin URI: http://www.webbisivut.org/
 * Description: Postin toimitustavat WooCommerce verkkokaupalle.
 * Author: Webbisivut.org
 * Author URI: http://www.webbisivut.org/
 * Requires at least: 4.0
 * Tested up to: 5.2
 * Text Domain: wb-posti
 * Domain Path: /lang
 * WC requires at least: 3.0
 * WC tested up to: 3.5.4
 *
 * @package WordPress
 * @author Webbisivut.org
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

	if(!is_multisite()) {
		if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			
			function wb_posti_woo_admin_notice__error() {
				$class = 'notice notice-error';
				$message = __( 'Virhe Posti toimitustavat lisäosan käyttöönotossa! WooCommerce ei ole aktivoituna!', 'wb-posti' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
			}
			add_action( 'admin_notices', 'wb_posti_woo_admin_notice__error' );

		} else {
			wb_posti_toim_requirements();
		}
	} else {
		wb_posti_toim_requirements();
	}

	function wb_posti_toim_requirements() {
		// Include plugin class files
		require_once( 'includes/class-wb-posti.php' );

		// Shipping zone methods
		require_once( 'includes/class-wb-kirje-shipment-methods-sz.php' );
		require_once( 'includes/class-wb-postiennakko-shipment-methods-sz.php' );
		require_once( 'includes/class-wb-postiennakko-maksu.php' );
		require_once( 'includes/class-wb-ovelle-paketti-shipment-methods-sz.php' );
		require_once( 'includes/class-wb-posti-paketti-shipment-methods-sz.php' );
		require_once( 'includes/class-wb-smartpost-shipment-methods-sz.php' );
		require_once( 'includes/class-wb-ems-shipment-methods-sz.php' );
		require_once( 'includes/class-wb-rahti-shipment-methods-sz.php' );

		/**
		 * Returns the main instance of WB_Posti_Toimitustavat to prevent the need to use globals.
		 *
		 * @since  1.0.0
		 * @return object WB_Posti_Toimitustavat
		 */
		function WB_Posti_Toimitustavat () {
			$instance = WB_Posti_Toimitustavat::instance( __FILE__, '1.0.0' );

			return $instance;
		}

		WB_Posti_Toimitustavat();

	}

	function formatPricePostiToimitustavat($haystack, $replace, $needle) {
		$pos = strpos($haystack, $needle);

		if ($pos !== false) {
			$newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
		} else {
			$newstring = $haystack;
		}

		return $newstring;
	}

?>
