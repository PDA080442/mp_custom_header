<?php
/**
 * Plugin Name: Metaphysica Header
 * Description: Плавающий хедер Metaphysica (капсула + выпадающее меню). Адаптив desktop/tablet/mobile. Настраивается в админке. Вставка шорткодом [mp_header].
 * Version: 2.0.0
 * Author: Metaphysica
 * Text Domain: mp-header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MP_HEADER_VERSION', '2.3.0' );
define( 'MP_HEADER_FILE', __FILE__ );
define( 'MP_HEADER_DIR', plugin_dir_path( __FILE__ ) );
define( 'MP_HEADER_URL', plugin_dir_url( __FILE__ ) );
define( 'MP_HEADER_OPTION', 'mp_header_settings' );

require_once MP_HEADER_DIR . 'includes/class-mp-header-settings.php';
require_once MP_HEADER_DIR . 'includes/class-mp-header-admin.php';
require_once MP_HEADER_DIR . 'includes/class-mp-header-shortcodes.php';
require_once MP_HEADER_DIR . 'includes/class-mp-header-frontend.php';

add_action( 'plugins_loaded', function () {
	$flag = 'mp_header_migrated_2_1_0';
	if ( ! get_option( $flag ) ) {
		$opts = get_option( MP_HEADER_OPTION );
		if ( is_array( $opts ) ) {
			$opts['strip_bg'] = '';
			update_option( MP_HEADER_OPTION, $opts );
		}
		update_option( $flag, 1 );
	}

	( new MP_Header_Admin() )->init();
	( new MP_Header_Shortcodes() )->init();
	( new MP_Header_Frontend() )->init();
} );

register_activation_hook( __FILE__, function () {
	$existing = get_option( MP_HEADER_OPTION );
	if ( ! is_array( $existing ) ) {
		update_option( MP_HEADER_OPTION, MP_Header_Settings::defaults() );
	}
} );
