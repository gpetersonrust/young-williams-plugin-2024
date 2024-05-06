<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://gpeterson@moxcar.com
 * @since      1.0.0
 *
 * @package    Young_Williams_Plugin
 * @subpackage Young_Williams_Plugin/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Young_Williams_Plugin
 * @subpackage Young_Williams_Plugin/includes
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class Young_Williams_Plugin_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}
