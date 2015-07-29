<?php
/**
 * An extension for the Connections plugin which adds back legacy templates that have been removed from the core plugin.
 *
 * @package   Connections Legacy Templates
 * @category  Extension
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      http://connections-pro.com
 * @copyright 2015 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Connections Legacy Templates
 * Plugin URI:        http://connections-pro.com
 * Description:       An extension for the Connections plugin which adds back legacy templates that have been removed from the core plugin.
 * Version:           1.0
 * Author:            Steven A. Zahm
 * Author URI:        http://connections-pro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connections
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists('Connections_Legacy_Templates') ) {

	class Connections_Legacy_Templates {

		public function __construct() {

			remove_filter( 'cn_get_template_slug', array( 'cnTemplate_Compatibility', 'deprecatedTemplates' ) );

			self::defineConstants();
			self::loadDependencies();

			// This should run on the `plugins_loaded` action hook. Since the extension loads on the
			// `plugins_loaded action hook, call immediately.
			self::loadTextdomain();
		}

		/**
		 * Define the constants.
		 *
		 * @access  private
		 * @static
		 * @since  1.0
		 * @return void
		 */
		private static function defineConstants() {

			define( 'CN_LEGACY_TEMPLATES_CURRENT_VERSION', '1.0' );
			define( 'CN_LEGACY_TEMPLATES_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'CN_LEGACY_TEMPLATES_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'CN_LEGACY_TEMPLATES_PATH', plugin_dir_path( __FILE__ ) );
			define( 'CN_LEGACY_TEMPLATES_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * The widget.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @return void
		 */
		private static function loadDependencies() {

			include_once CN_LEGACY_TEMPLATES_PATH . 'templates/card-bio/card-bio.php';
			include_once CN_LEGACY_TEMPLATES_PATH . 'templates/card-single/card-single-default.php';
			include_once CN_LEGACY_TEMPLATES_PATH . 'templates/card-tableformat/card-table-format.php';
		}

		/**
		 * Load the plugin translation.
		 *
		 * Credit: Adapted from Ninja Forms / Easy Digital Downloads.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @uses   apply_filters()
		 * @uses   get_locale()
		 * @uses   load_textdomain()
		 * @uses   load_plugin_textdomain()
		 *
		 * @return void
		 */
		public static function loadTextdomain() {

			// Plugin textdomain. This should match the one set in the plugin header.
			$domain = 'connections';

			// Set filter for plugin's languages directory
			$languagesDirectory = apply_filters( "cn_{$domain}_languages_directory", CN_LEGACY_TEMPLATES_DIR_NAME . '/languages/' );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale', get_locale(), $domain );
			$fileName = sprintf( '%1$s-%2$s.mo', $domain, $locale );

			// Setup paths to current locale file
			$local  = $languagesDirectory . $fileName;
			$global = WP_LANG_DIR . "/{$domain}/" . $fileName;

			if ( file_exists( $global ) ) {

				// Look in global `../wp-content/languages/{$domain}/` folder.
				load_textdomain( $domain, $global );

			} elseif ( file_exists( $local ) ) {

				// Look in local `../wp-content/plugins/{plugin-directory}/languages/` folder.
				load_textdomain( $domain, $local );

			} else {

				// Load the default language files
				load_plugin_textdomain( $domain, FALSE, $languagesDirectory );
			}
		}
	}

	/**
	 * Start up the extension.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @return mixed object | bool
	 */
	function Connections_Legacy_Templates() {

		if ( class_exists('connectionsLoad') ) {

			return new Connections_Legacy_Templates();

		} else {

			add_action(
				'admin_notices',
				create_function(
					'',
					'echo \'<div id="message" class="error"><p><strong>ERROR:</strong> Connections must be installed and active in order use Connections Legacy Templates.</p></div>\';'
				)
			);

			return FALSE;
		}
	}

	/**
	 * Since Connections fires the `cn_register_template` action on the `plugins_loaded` hook at
	 * priority 10 this extension needs to be kicked off on `plugins_loaded` hook at
	 * priority 9.
	 */
	add_action( 'plugins_loaded', 'Connections_Legacy_Templates', 9 );
}
