<?php

/**
 * Fired during plugin activation
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Variation_Swatches
 * @subpackage XT_Woo_Variation_Swatches/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    XT_Woo_Variation_Swatches
 * @subpackage XT_Woo_Variation_Swatches/includes
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class XT_Woo_Variation_Swatches_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        deactivate_plugins('woo-variation-swatches/woo-variation-swatches.php');
	}

}
