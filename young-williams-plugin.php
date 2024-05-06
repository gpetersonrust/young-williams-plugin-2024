<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gpeterson@moxcar.com
 * @since             1.0.0
 * @package           Young_Williams_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Young Williams  Plugin
 * Plugin URI:        https://gpeterson@moxcar.com
 * Description:       This is the official core plugin for Young Williams
 * Version:           1.0.0
 * Author:            Gino Peterson
 * Author URI:        https://gpeterson@moxcar.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       young-williams-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define("PLUGIN_URL", plugin_dir_url(__FILE__));
define("PLUGIN_DIR", plugin_dir_path(__FILE__));
define("PLUGIN_BASENAME", plugin_basename(__FILE__));
define("PLUGIN_VERSION", '1.0.0');

// add /utils/utils.php
require_once(PLUGIN_DIR . 'utils/utils.php');
require_once(PLUGIN_DIR . 'api/api.php');
require_once(PLUGIN_DIR . 'gravity-form/gravity-forms.php');
// pages/admin-pages.php
require_once(PLUGIN_DIR . 'pages/admin-pages.php');


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'YOUNG_WILLIAMS_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-young-williams-plugin-activator.php
 */
function activate_young_williams_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-young-williams-plugin-activator.php';
	Young_Williams_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-young-williams-plugin-deactivator.php
 */
function deactivate_young_williams_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-young-williams-plugin-deactivator.php';
	Young_Williams_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_young_williams_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_young_williams_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-young-williams-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_young_williams_plugin() {

	$plugin = new Young_Williams_Plugin();
	$plugin->run();

}
run_young_williams_plugin();
