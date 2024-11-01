<?php
/**
 *
 * @since      1.0.0
 * @package    Sip_Front_End_Bundler_Woocommerce
 * @author     shopitpress <hello@shopitpress.com>
 *
 * Plugin Name:		SIP Front End Bundler for WooCommerce
 * Plugin URI:		https://shopitpress.com/plugins/sip-front-end-bundler-woocommerce/
 * Description:		Front end bundle maker for WooCommerce with real-time offers
 * Version:			1.1.0
 * Author:			ShopitPress <hello@shopitpress.com>
 * Author URI:		https://shopitpress.com
 * License:			GPL-2.0+
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 * Copyright:		© 2015 ShopitPress(email: hello@shopitpress.com)
 * Text Domain:		WB
 * Domain Path:		sip-front-end-bundler-woocommerce
 * Requires:		PHP5, WooCommerce Plugin
 * WC requires at least: 2.6.0
 * WC tested up to: 3.4.3
 * Last updated on: 20 June, 2018
*/
if(!function_exists('add_action'))
	exit;

// define plugin constants
define( 'SIP_FEBWC_NAME', 'SIP Front End Bundler for WooCommerce' );
define( 'SIP_FEBWC_VERSION', '1.1.0' );
define( 'SIP_FEBWC_PLUGIN_SLUG', 'sip-front-end-bundler-woocommerce' );
define( 'SIP_FEBWC_BASENAME', plugin_basename( __FILE__ ) );
define( 'SIP_FEBWC_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SIP_FEBWC_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SIP_FEBWC_PLUGIN_PURCHASE_URL', 'https://shopitpress.com/plugins/sip-front-end-bundler-woocommerce/' );

// add CSS & JS scripts
function sip_febwc_scripts() {
	wp_enqueue_script( 'sip_febwc-app', plugin_dir_url( __FILE__ ) .  'assets/js/app.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'sip_febwc_scripts' );

function deactivate_febwc_lite_version() {
	deactivate_plugins(plugin_basename( __FILE__ ));
}

/**
 * To chek the woocommerce is active or not
 *
 * @since    1.0.7
 * @access   public
 */
function febwc_activate () {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin = plugin_basename( __FILE__ );

	if( !class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( $plugin );
		add_action( 'admin_notices', 'sip_febwc_admin_notice_error' );
	}
}
add_action( 'plugins_loaded', 'febwc_activate' );

function sip_febwc_admin_notice_error() {
	$class = 'notice notice-error';
	$message = __( 'SIP Front End Bundler for WooCommerce requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be active!', 'sip-front-end-bundler-woocommerce' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
}

/**
 * Register credit/affiliate link options
 *
 * @since  1.0.1
 *
 */
function sip_febwc_affiliate_register_admin_settings() {
	register_setting( 'sip-febwc-affiliate-settings-group', 'sip-febwc-affiliate-check-box' );
	register_setting( 'sip-febwc-affiliate-settings-group', 'sip-febwc-affiliate-radio' );
	register_setting( 'sip-febwc-affiliate-settings-group', 'sip-febwc-affiliate-affiliate-username' );
}
add_action( 'admin_init', 'sip_febwc_affiliate_register_admin_settings' );

// on plugin deactivation, delete the SIP version
register_deactivation_hook( __FILE__, array( 'Sip_Front_End_Bundler_WC_Admin' , 'sip_febwc_deactivate') );

// load core plugin class and admin functions
if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

require_once( SIP_FEBWC_DIR . 'config/core.php' );
require_once( SIP_FEBWC_DIR . 'admin/sip-front-end-bundler-admin.php' );

global $core;

// CORE INSTANCE
$core = new Core;