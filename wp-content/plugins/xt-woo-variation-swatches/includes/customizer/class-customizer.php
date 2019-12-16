<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Woo Variation Swatches Xirki Options Class.
 */
class XT_Woo_Variation_Swatches_Customizer {

	public static $parent;
	public static $config_id = 'xt_woovs';
	public static $options = null;
	public static $path;
	public static $types = array(
		array(
			'id' => 'single', 
			'title' => 'Single Product',
			'icon' => 'dashicons-align-left'
		),
		array(
			'id' => 'archives',
			'title' => 'Archives / Shop',
			'icon' => 'dashicons-grid-view'
		)
	);	
	
	/**
	 * Class constructor
	 */
	public function __construct($parent) {
		
		/**
		 * Exit early if Xirki does not exist or is not installed and activated.
		 */
		if ( ! class_exists( 'Xirki' ) ) {
			return;
		}
		
		self::$parent = $parent;
		self::$path = dirname(__FILE__);
		
		self::add_config();
		self::add_panels();
		self::add_sections();
		self::add_fields();

        add_action( 'customize_preview_init', array(__CLASS__, 'customizer_preview_script' ));
        add_action( 'customize_controls_enqueue_scripts', array(__CLASS__, 'customizer_styles' ));

		add_filter( 'wp_check_filetype_and_ext', array(__CLASS__, 'check_filetype_and_ext'), 10, 4 );
		add_filter( 'upload_mimes', array(__CLASS__, 'allow_myme_types'), 1, 1);

	}

	public static function customizer_link() {
		
		return admin_url('customize.php?autofocus[panel]='.self::$config_id	);
	}

	/**
	 * Xirki Config
	 */
	public static function add_config() {

		Xirki::add_config( self::$config_id, array(
		    'capability'    => 'edit_theme_options',
		    'option_type'   => 'option',
		    'option_name'	=> self::$config_id	    
		));	
	}

	/**
	 * Add panels to Xirki.
	 */
	public static function add_panels() {

		Xirki::add_panel( self::panel_id(), array(
		    'priority'    => 130,
            'title'       => self::$parent->plugin_menu_name(),
		    'icon' 		  => 'dashicons-screenoptions'
		));

		foreach(self::$types as $type) {
			
			Xirki::add_panel( self::panel_id($type['id']), array(
			    'priority'    => 130,
			    'title'       => esc_html__( $type['title'], 'xt-woo-variation-swatches' ),
			    'panel' 	  => self::panel_id(),
			    'icon' 		  => $type['icon']
			));
	
		}
	
	}

	/**
	 * Add sections to Xirki.
	 */
	public static function add_sections() {

		Xirki::add_section(
			self::section_id('swatch-global'),
			array(
			    'title'          => esc_html__( 'Global Swatch Settings', 'xt-woo-variation-swatches'),
			    'panel'		 	 => self::panel_id(),
			    'priority'       => 160,
			    'capability'     => 'edit_theme_options',
			    'icon' 			 => 'dashicons-admin-generic'
			)
		);

		
		foreach(self::$types as $_type) {
			
			$type = $_type['id'];
			
			Xirki::add_section(
				self::section_id($type.'-swatch-general'),
				array(
				    'title'          => esc_html__( 'General Swatch Settings', 'xt-woo-variation-swatches'),
				    'panel'		 	 => self::panel_id($type),
				    'priority'       => 160,
				    'capability'     => 'edit_theme_options',
				    'icon' 			 => 'dashicons-admin-generic'
				)
			);

			Xirki::add_section(
				self::section_id($type.'-swatch-label'), 
				array(
				    'title'          => esc_html__( 'Label Swatch Settings', 'xt-woo-variation-swatches'),
				    'panel'		 	 => self::panel_id($type),
				    'priority'       => 160,
				    'capability'     => 'edit_theme_options',
				    'icon' 			 => 'dashicons-editor-bold'
				)
			);
	
			Xirki::add_section(
				self::section_id($type.'-swatch-color'), 
				array(
				    'title'          => esc_html__( 'Color Swatch Settings', 'xt-woo-variation-swatches'),
				    'panel'		 	 => self::panel_id($type),
				    'priority'       => 160,
				    'capability'     => 'edit_theme_options',
				    'icon' 			 => 'dashicons-admin-appearance'
				)
			);
			
			Xirki::add_section(
				self::section_id($type.'-swatch-image'), 
				array(
				    'title'          => esc_html__( 'Image Swatch Settings', 'xt-woo-variation-swatches'),
				    'panel'		 	 => self::panel_id($type),
				    'priority'       => 160,
				    'capability'     => 'edit_theme_options',
				    'icon' 			 => 'dashicons-format-image'
				)
			);
			
		}

	}

	/**
	 * Add fields to Xirki.
	 */
	public static function add_fields() {
		
		// General Settings.
		
		require self::$path . '/fields/swatch-global.php';
		
		foreach(self::$types as $_type) {

			$type = $_type['id'];
			$element_prefix = '.xt_woovs-'.$type.'-product';
            $page_prefix = '.xt_woovs-'.$type;

			require self::$path . '/fields/swatch-general.php';
			require self::$path . '/fields/swatch-label.php';
			require self::$path . '/fields/swatch-color.php';
			require self::$path . '/fields/swatch-image.php';
		}

	}
		
	public static function panel_id($id = null) {
		
		$panel_id = self::$config_id;
		if(!empty($id)) {
			$panel_id .= '-'.$id;
		}
		
		return $panel_id;
	}
	
	public static function section_id($id) {
		
		return self::$config_id.'-'.$id;
	}

	public static function field_id($id) {
		
		return $id;
	}

    public static function get_option_exists($id) {

        return isset(Xirki::$fields[ Xirki::$config[ self::$config_id ]['option_name'] . '[' . $id . ']' ]);
    }

    public static function get_option($id, $default = null) {

        if(!self::get_option_exists($id)) {

            return $default;
        }

        return Xirki::get_option(self::$config_id, $id);
    }
	
	public static function types_default_values($type, $single_value, $archive_value) {
		
		return $type === 'single' ? $single_value : $archive_value;
	}

    public static function customizer_preview_script() {

        wp_enqueue_script(
            'xirki-customizer',
            self::$parent->plugin_url(). 'includes/customizer/assets/js/customizer-min.js',
            array( 'jquery','customize-preview' ),
            filemtime(self::$parent->plugin_path( 'includes/customizer/assets/js', 'customizer-min.js')),
            true
        );
    }

	public static function customizer_styles() {
		
		wp_enqueue_style( 
			'xirki-customizer',
			self::$parent->plugin_url(). 'includes/customizer/assets/css/customizer.css', 
			array(),
            filemtime(self::$parent->plugin_path( 'includes/customizer/assets/css', 'customizer.css'))
		);

        wp_enqueue_style(
            self::$config_id.'-customizer',
            self::$parent->plugin_url(). 'includes/customizer/assets/css/customizer-custom.css',
            array(),
            filemtime(self::$parent->plugin_path( 'includes/customizer/assets/css', 'customizer-custom.css'))
        );

		wp_register_script(
			self::$config_id.'-customizer-controls',
			self::$parent->plugin_url(). 'includes/customizer/assets/js/customizer-controls-min.js',
			array( 'jquery','customize-preview' ),
            filemtime(self::$parent->plugin_path( 'includes/customizer/assets/js', 'customizer-controls-min.js')),
			true
		);

        $variations = get_posts(array(
            'post_type' => 'product_variation',
            'numberposts' => 1
        ));

        $single_url = '';
        $archives_url = get_permalink( wc_get_page_id( 'shop' ) );

        if(!empty($variations)) {
            $variation = array_shift($variations);
            $product_id = $variation->post_parent;
            $single_url = get_permalink($product_id);
        }

        wp_localize_script( self::$config_id.'-customizer-controls', 'woovs_controls', array(
            'single_url' => $single_url,
            'archive_url' => $archives_url,
            'is_shop' => is_shop()
        ));

		wp_enqueue_script(self::$config_id.'-customizer-controls');
	}


	// Allow SVG
	public static function check_filetype_and_ext($data, $file, $filename, $mimes) {
	
	  global $wp_version;
	  if ( $wp_version <= '4.7.1' ) {
	     return $data;
	  }
	
	  $filetype = wp_check_filetype( $filename, $mimes );
	
	  return [
	      'ext'             => $filetype['ext'],
	      'type'            => $filetype['type'],
	      'proper_filename' => $data['proper_filename']
	  ];
	
	}

	public static function allow_myme_types($mime_types){

		$mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
		$mime_types['svgz'] = 'image/svg+xml';

		return $mime_types;

	}
	
	public static function product_attributes_options() {

		$attributes = function_exists('wc_get_attribute_taxonomies') ? wc_get_attribute_taxonomies() : array();
		
		$options = array();
		foreach($attributes as $attribute) {
	
			$options['pa_'.$attribute->attribute_name] = $attribute->attribute_label;
		}
		
		return $options;
	}
				
} // End Class
	
