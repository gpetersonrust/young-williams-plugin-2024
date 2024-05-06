<?php

/**
 * Fired during plugin activation
 *
 * @link       https://gpeterson@moxcar.com
 * @since      1.0.0
 *
 * @package    Young_Williams_Plugin
 * @subpackage Young_Williams_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Young_Williams_Plugin
 * @subpackage Young_Williams_Plugin/includes
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class Young_Williams_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		 

		
		flush_rewrite_rules();

	}

}


 