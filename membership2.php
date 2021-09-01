<?php
/**
 * Plugin Name: PS Mitgliedschaften Basic
 * Plugin URI:  https://n3rds.work/piestingtal_source/ps-mitgliedschaften-plugin/
 * Version:     1.1.9
 * Description: Das leistungsstärkste, benutzerfreundlichste und flexibelste Mitgliedschafts-Plugin für WordPress-Seiten.
 * Author:      WMS N@W
 * Author URI:  https://n3rds.work
 * License:     GPL2
 * License URI: http://opensource.org/licenses/GPL-2.0
 * Text Domain: membership2
 *
 * @package Membership2
 */

/**
 * Copyright notice
 *
 * @copyright WMS N@W (https://n3rds.work/)
 *
 * Authors: DerN3rd, Philipp Stracker, Fabio Jun Onishi, Victor Ivanov, Jack Kitterhing, Rheinard Korf, Ashok Kumar Nath, Paul Kevin
 * 
 *
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 * MA 02110-1301 USA
 */

/**
 * Initializes constants and create the main plugin object MS_Plugin.
 * This function is called *instantly* when this file was loaded.
 *
 * @since  1.0.0
 */
require 'lib/psource-plugin-update/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://n3rds.work//wp-update-server/?action=get_metadata&slug=mitgliedschaften-basic', 
	__FILE__, 
	'mitgliedschaften-basic' 
);
function membership2_pro_init_app() {
	if ( defined( 'MS_PLUGIN' ) ) {
		$plugin_name = 'Mitgliedschaften Basic';
		if ( is_admin() ) {
			// Can happen in Multisite installs where a sub-site has activated the
			// plugin and then the plugin is also activated in network-admin.
			printf(
				'<div class="notice error"><p><strong>%s</strong>: %s</p></div>',
				sprintf(
					esc_html__( 'Das Plugin %s konnte nicht geladen werden, da bereits eine andere Version des Plugins geladen ist', 'membership2' ),
					$plugin_name
				),
				esc_html( MS_PLUGIN . ' (v' . MS_PLUGIN_VERSION . ')' )
			);
		}
		return;
	}

	/**
	 * Plugin version
	 *
	 * @since  1.0.0
	 */
	define( 'MS_PLUGIN_VERSION', '1.1.9' );

	/**
	 * Free or pro plugin?
	 * This only affects some display settings, it does not really lock/unlock
	 * any premium features...
	 *
	 * @since  1.0.3.2
	 */
	define( 'MS_IS_PRO', false );

	/**
	 * Plugin main-file.
	 *
	 * @since  1.0.3.0
	 */
	define( 'MS_PLUGIN_FILE', __FILE__ );

	/**
	 * Plugin identifier constant.
	 *
	 * @since  1.0.0
	 */
	define( 'MS_PLUGIN', plugin_basename( __FILE__ ) );

	/**
	 * Plugin name dir constant.
	 *
	 * @since  1.0.0
	 */
	define( 'MS_PLUGIN_NAME', dirname( MS_PLUGIN ) );

	/**
	 * Plugin name dir constant.
	 *
	 * @since  1.0.3
	 */
	define( 'MS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

	/**
	 * Plugin base dir
	 */
	define( 'MS_PLUGIN_BASE_DIR', dirname( __FILE__ ) );

	$externals = array(
		dirname( __FILE__ ) . '/lib/wpmu-lib/core.php',
		dirname( __FILE__ ) . '/lib/wdev-frash/module.php',
	);

	$cta_label 	= false;
	$drip_param = false;


	foreach ( $externals as $path ) {
		if ( file_exists( $path ) ) { require_once $path; }
	}


	/**
	 * Translation.
	 *
	 * Tip:
	 *   The translation files must have the filename [TEXT-DOMAIN]-[locale].mo
	 *   Example: membership2-en_EN.mo  /  membership2-de_DE.mo
	 */
	function _membership2_translate_plugin() {
		load_plugin_textdomain(
			'membership2',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}
	add_action( 'plugins_loaded', '_membership2_translate_plugin' );

	if ( (defined( 'WP_DEBUG' ) && WP_DEBUG) || (defined( 'WDEV_DEBUG' ) && WDEV_DEBUG) ) {
		// Load development/testing code before the plugin is initialized.
		$testfile = dirname( __FILE__ ) . '/tests/wp/init.php';
		if ( file_exists( $testfile ) ) { include $testfile; }
	}


	include MS_PLUGIN_BASE_DIR . '/app/ms-loader.php';

	if ( is_dir( MS_PLUGIN_BASE_DIR . '/premium' ) ) {
		include MS_PLUGIN_BASE_DIR . '/premium/ms-premium-loader.php';

		MS_Premium_Loader::instance();
	}

	// Initialize the M2 class loader.
	$loader = new MS_Loader();
	/**
	 * Create an instance of the plugin object.
	 *
	 * This is the primary entry point for the Membership plugin.
	 *
	 * @since  1.0.0
	 */
	MS_Plugin::instance();

	/**
	 * Ajax Logins
	 *
	 * @since 1.0.4
	 */
	MS_Auth::check_ms_ajax();
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

//Deactivate free
if ( is_plugin_active( 'membership/membership.php' ) ) {
	deactivate_plugins( array( 'membership/membership.php' ) );
}

membership2_pro_init_app();
