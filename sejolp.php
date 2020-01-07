<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ridwan-arifandi.com
 * @since             1.0.0
 * @package           Sejolp
 *
 * @wordpress-plugin
 * Plugin Name:       Sejoli - LearnPress
 * Plugin URI:        https://sejoli.co.id
 * Description:       Integration sejoli with learnpress
 * Version:           1.0.0
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sejolp
 * Domain Path:       /languages
 */

global $sejolp;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action('muplugins_loaded', 'sejolp_check_sejoli');

function sejolp_check_sejoli() {

	if(!defined('SEJOLISA_VERSION')) :

		add_action('admin_notices', 'sejolp_no_sejoli_functions');

		function sejolp_no_sejoli_functions() {
			?><div class='notice notice-error'>
			<p><?php _e('Anda belum menginstall atau mengaktifkan SEJOLI terlebih dahulu.', 'sejolp'); ?></p>
			</div><?php
		}

		return;
	endif;

}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SEJOLP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sejolp-activator.php
 */
function activate_sejolp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejolp-activator.php';
	Sejolp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sejolp-deactivator.php
 */
function deactivate_sejolp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sejolp-deactivator.php';
	Sejolp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sejolp' );
register_deactivation_hook( __FILE__, 'deactivate_sejolp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sejolp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sejolp() {

	$plugin = new Sejolp();
	$plugin->run();

}
run_sejolp();