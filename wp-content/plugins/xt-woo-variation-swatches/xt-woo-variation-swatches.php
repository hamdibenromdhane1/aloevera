<?php
/**
 * XT WooCommerce Variation Swatches
 *
 * @package     XT_Woo_Variation_Swatches
 * @author      XplodedThemes
 * @copyright   2018 XplodedThemes
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: XT WooCommerce Variation Swatches
 * Plugin URI:  https://xplodedthemes.com/products/woo-variation-swatches/
 * Description: A WooCommerce extension that transforms variation dropdowns to beautiful color, image or label swatches. Image swatches will automatically be applied for variation color attributes that contains an image.
 * Version:     1.1.7
 * WC requires at least: 3.0.0
 * WC tested up to: 3.8.0
 * Author:      XplodedThemes
 * Author URI:  https://xplodedthemes.com
 * Text Domain: xt-woo-variation-swatches
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $xt_woovs_plugin;

$market = '##XT_MARKET##';
$market = (strpos($market, 'XT_MARKET') !== false) ? 'freemius' : $market;
$market = (defined('XT_MARKET')) ? XT_MARKET : $market;

$xt_woovs_plugin = (object) array(
    'version'       => '1.1.7',
    'name'          => esc_html__('XT WooCommerce Variation Swatches', 'xt-woo-variation-swatches'),
    'menu_name'     => esc_html__('Woo Variation Swatches', 'xt-woo-variation-swatches'),
    'icon'          => 'dashicons-image-filter',
    'slug'          => 'xt-woo-variation-swatches',
    'premium_slug'  => 'xt-woo-variation-swatches-pro',
    'freemium_slug' => 'xt-woo-variation-swatches',
    'freemius_id'   => '2908',
    'market'        => $market,
    'markets'       => array(
        'envato' => array(
            'id' => 23358604,
            'buy_url' => 'https://codecanyon.net/item/woocommerce-variation-swatches/23358604'
        )
    ),
    'license_section_slug' => 'xt-woo-variation-swatches',
    'file'          => __FILE__
);

if ( function_exists( 'xt_woovs_fs' ) ) {

    xt_woovs_fs()->set_basename( false, __FILE__ );

} else {

    // Load sdk bootstrap file.
    require_once plugin_dir_path(__FILE__) . 'includes/class-bootstrap.php';

    /**
     * Freemius helper function for easy SDK access.
     *
     * @since    1.0.0
     */

    function xt_woovs_fs()
    {
        global $xt_woovs_plugin;

        return XT_Woo_Variation_Swatches_Bootstrap::boot($xt_woovs_plugin)->sdk;
    }

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function xt_woo_variation_swatches() {

        global $xt_woovs_plugin;

        return XT_Woo_Variation_Swatches::instance($xt_woovs_plugin);
    }

    // Init Freemius.
    xt_woovs_fs();

    // Run Plugin.
    xt_woo_variation_swatches();

}