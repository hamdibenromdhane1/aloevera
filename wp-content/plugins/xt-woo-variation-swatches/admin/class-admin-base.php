<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Variation_Swatches
 * @subpackage XT_Woo_Variation_Swatches/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    XT_Woo_Variation_Swatches
 * @subpackage XT_Woo_Variation_Swatches/admin
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class XT_Woo_Variation_Swatches_Admin_Base {

	/**
	 * Core class reference.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      XT_Woo_Variation_Swatches    core    Core Class
	 */
	protected $core;

    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   public
     * @var      XT_Woo_Variation_Swatches_Welcome    welcome    Welcome Class
     */
    public $welcome;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    obj    $core    Plugin core class.
	 */
	public function __construct( &$core ) {

		$this->core = $core;

        $this->welcome = $this->init_welcome_page();
    }

	function admin_body_class( $classes ) {
		
		$screen = get_current_screen();
		
		if(!empty($screen) && strpos($screen->base, $this->core->plugin_slug()) !== false) {
	    	$classes .= ' '.$this->core->plugin_slug('admin');
	    }
	    
	    return $classes;
	}

    public function auto_update ( $update, $item ) {
        // Array of plugin slugs to always auto-update
        $plugins = array ($this->core->plugin()->freemium_slug);

        if ( in_array( $item->slug, $plugins ) ) {
            return true; // Always update plugins in this array
        } else {
            return $update; // Else, use the normal API response to decide whether to update or not
        }
    }

    function init_welcome_page() {

        require_once 'welcome/class-welcome.php';

        $sections = array();

        $sections[] = array(
            'id' => 'changelog',
            'title' => esc_html__( 'Change Log', 'woo-variation-swatches' ),
            'menu_title' => esc_html__( 'About', 'woo-variation-swatches' ),
            'show_menu' => true,
            'content' => array(
                'type' => 'changelog',
                'show_refresh' => true
            )
        );

        $sections[] = array(
            'id' => 'customizer',
            'title' => esc_html__( 'Customize', 'woo-variation-swatches' ),
            'show_menu' => true,
            'action_link' => true,
            'redirect' => XT_Woo_Variation_Swatches_Customizer::customizer_link()
        );

        if($this->core->plugin_market() !== 'freemius') {

            $sections[] = array(
                'id' => 'support',
                'title' => esc_html__('Support', 'woo-variation-swatches'),
                'show_menu' => true,
                'external' => 'https://xplodedthemes.com/support'
            );

        }else{

            $sections[] = array(
                'id' => 'support',
                'title' => esc_html__('Support', 'woo-variation-swatches'),
                'show_menu' => false,
                'redirect' => $this->core->plugin_admin_url('contact')
            );
        }

        $sections[] = array(
            'id' => 'shop',
            'title' => esc_html__( 'Shop', 'woo-variation-swatches' ),
            'show_menu' => false,
            'content' => array(
                'type' => 'url',
                'url' => 'http://xplodedthemes.com/api/products.php?format=html&exclude='.$this->core->plugin_slug(),
                'title' => esc_html__( 'Products you might like', 'woo-variation-swatches' ),
                'show_refresh' => true,
            )
        );

        if(!$this->core->fs()->is_paying() && $this->core->plugin_market() === 'freemius') {

            $sections[] = array(
                'id' => 'upgrade',
                'title' => esc_html__( 'Upgrade', 'woo-variation-swatches' ),
                'show_menu' => false,
                'featured' => true,
                'redirect' => $this->core->fs()->get_upgrade_url()
            );

        }

        $sections = apply_filters('xt_woovs_welcome_sections', $sections, $this->core);

        return new XT_Woo_Variation_Swatches_Welcome($this->core, $sections);
    }

	
	/**
	 * Check if woocommerce is activated, error if not
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_missing_notice() {
		
		if ( ! class_exists( 'WooCommerce' ) ) {
			
			$class = 'notice notice-error';
			$message = sprintf(
				__( '<strong>%1$s</strong> plugin requires %2$s to be installed and active.', 'xt-woo-variation-swatches' ),
				$this->core->plugin_name(),
				'<a target="_blank" href="https://en-ca.wordpress.org/plugins/woocommerce/">WooCommerce</a>'
			);
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
			
			deactivate_plugins( $this->core->plugin_file() );
		} 
	}

    /**
     * Check if conflicting plugins are enabled, notify in that case
     *
     * @since    1.0.0
     */
    public function disable_conflict_plugins() {

        // Check if Woo Variation Swatches (Emran Ahmed) is enabled
        if (defined('WVS_VERSION') ) {

            deactivate_plugins('woo-variation-swatches/woo-variation-swatches.php');
            wp_redirect(admin_url('plugins.php'));
        }
    }


    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in XT_Woo_Variation_Swatches_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The XT_Woo_Variation_Swatches_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->core->plugin_slug('admin'), $this->core->plugin_url( 'admin/assets/css', 'admin.css' ), array('wp-color-picker'), $this->core->plugin_version(), 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in XT_Woo_Variation_Swatches_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The XT_Woo_Variation_Swatches_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_media();
        wp_register_script( $this->core->plugin_slug('admin'), $this->core->plugin_url( 'admin/assets/js', 'admin'.$this->core->script_suffix.'.js'), array( 'jquery', 'wp-color-picker', 'wp-util'), $this->core->plugin_version(), false );

        wp_localize_script(
            $this->core->plugin_slug('admin'),
            'xt_woovs',
            array(
                'i18n'        => array(
                    'mediaTitle'  => esc_html__( 'Choose an image', 'xt-woo-variation-swatches' ),
                    'mediaButton' => esc_html__( 'Use image', 'xt-woo-variation-swatches' ),
                ),
                'placeholder' => $this->core->plugin_url('admin/assets/images', 'placeholder.png'),
                'color_placeholder' => "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE4AAABQAQMAAACNuNG1AAAABlBMVEXMzMz////TjRV2AAAAHElEQVR4AWNg+A+Ff6jL/A+HVGUOZfeOunfUvQAbQI4IO7xuxwAAAABJRU5ErkJggg=="
            )
        );

        wp_enqueue_script($this->core->plugin_slug('admin'));
    }

}
