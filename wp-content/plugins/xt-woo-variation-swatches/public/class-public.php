<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Variation_Swatches
 * @subpackage XT_Woo_Variation_Swatches/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    XT_Woo_Variation_Swatches
 * @subpackage XT_Woo_Variation_Swatches/public
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class XT_Woo_Variation_Swatches_Public
{
    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      XT_Woo_Variation_Swatches    core    Core Class
     */
    private  $core ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    obj    $core    Plugin core class
     */
    public function __construct( &$core )
    {
        $this->core = $core;
    }
    
    public function enabled( $type = null )
    {
        $type = ( empty($type) ? xt_woovs_swatch_type() : $type );
        $default_value = XT_Woo_Variation_Swatches_Customizer::types_default_values( $type, true, false );
        return xt_woovs_option( $type . '_swatches_enabled', $default_value );
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
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
        wp_enqueue_style(
            $this->core->plugin_slug(),
            $this->core->plugin_url( 'public/assets/css', 'frontend.css' ),
            array(),
            filemtime( $this->core->plugin_path( 'public/assets/css', 'frontend.css' ) ),
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
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
        wp_register_script(
            $this->core->plugin_slug(),
            $this->core->plugin_url( 'public/assets/js', 'frontend' . $this->core->script_suffix . '.js' ),
            array( 'jquery' ),
            filemtime( $this->core->plugin_path( 'public/assets/js', 'frontend' . $this->core->script_suffix . '.js' ) ),
            false
        );
        $vars = array(
            'can_use_premium_code' => $this->core->fs()->can_use_premium_code__premium_only(),
        );
        wp_localize_script( $this->core->plugin_slug(), 'XT_WOOVS', $vars );
        wp_enqueue_script( $this->core->plugin_slug() );
    }
    
    public function body_class( $classes )
    {
        
        if ( is_product() ) {
            $classes[] = 'xt_woovs-single';
        } else {
            $classes[] = 'xt_woovs-archives';
        }
        
        if ( $this->enabled( 'single' ) ) {
            $classes[] = 'xt_woovs-single-enabled';
        }
        if ( $this->enabled( 'archives' ) ) {
            $classes[] = 'xt_woovs-archives-enabled';
        }
        return $classes;
    }
    
    public function product_post_class( $classes )
    {
        if ( 'product' == get_post_type() ) {
            
            if ( xt_woovs_is_single_product() ) {
                $classes[] = 'xt_woovs-single-product';
            } else {
                $classes[] = 'xt_woovs-archives-product';
            }
        
        }
        return $classes;
    }
    
    /**
     * Filter function to add swatches bellow the default selector
     *
     * @param $html
     * @param $args
     *
     * @return string
     */
    public function variation_attribute_options_html( $html, $args )
    {
        if ( !xt_woovs_enabled_in_quick_views() ) {
            return $html;
        }
        if ( !$this->enabled() ) {
            return $html;
        }
        $attr = $this->core->backend()->get_tax_attribute( $args['attribute'] );
        // Return if this is normal attribute
        $options = $args['options'];
        $product = $args['product'];
        $attribute = $args['attribute'];
        $class = ( isset( $attr->attribute_type ) ? "variation-selector variation-select-{$attr->attribute_type}" : '' );
        $swatches = '';
        $product_swatch_options = $product->get_meta( '_xt_woovs_swatch_type_options', true );
        
        if ( empty($options) && !empty($product) && !empty($attribute) ) {
            $attributes = $product->get_variation_attributes();
            $options = $attributes[$attribute];
        }
        
        
        if ( !empty($options) ) {
            $slug = sanitize_title( $attribute );
            $key = md5( $slug );
            $type = null;
            
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms( $product->get_id(), $attribute, array(
                    'fields' => 'all',
                ) );
                foreach ( $terms as $term ) {
                    
                    if ( in_array( $term->slug, $options ) ) {
                        $meta_key = md5( $term->slug );
                        $selected = ( sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '' );
                        // Check if product has quick attributes with custom swatches, if yes use those instead.
                        $meta_values = $this->get_global_attribute_term_options(
                            $args,
                            $attr,
                            $term,
                            $product_swatch_options,
                            $key,
                            $meta_key
                        );
                        $swatches .= apply_filters(
                            'xt_woovs_swatch_html',
                            '',
                            $term,
                            $meta_values,
                            $selected
                        );
                    }
                
                }
            } else {
                foreach ( $options as $option ) {
                    $meta_key = md5( sanitize_title( strtolower( $option ) ) );
                    $selected = ( sanitize_title( $args['selected'] ) === sanitize_title( $option ) ? 'selected' : '' );
                    // Check if product has quick attributes with custom swatches, if yes use those instead.
                    $meta_values = $this->get_quick_attribute_term_options(
                        $args,
                        $attr,
                        $option,
                        $product_swatch_options,
                        $key,
                        $meta_key
                    );
                    $swatches .= apply_filters(
                        'xt_woovs_swatch_meta_html',
                        '',
                        $option,
                        $meta_values,
                        $selected
                    );
                }
            }
            
            
            if ( !empty($swatches) ) {
                $class .= ( !empty($class) ? $class . ' ' : $class );
                $class .= 'xt_woovs-hidden';
                $swatches_classes[] = 'xt_woovs-swatches';
                $swatches_classes = apply_filters( 'xt_woovs_classes', $swatches_classes );
                $swatches_classes = implode( " ", $swatches_classes );
                // $swatches output has been sanitized earlier via below function "get_label_swatch_html", "get_color_swatch_html", "get_image_swatch_html"
                $swatches = '<ul class="' . esc_attr( $swatches_classes ) . '" data-attribute_name="attribute_' . esc_attr( $slug ) . '">' . $swatches . '</ul>';
                // $html value is the result of a woocommerce filter "woocommerce_dropdown_variation_attribute_options_html" already sanitized.
                // The filter is defined in class-core.php
                // We are simply wrapping the result.
                $html = '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' . $swatches;
            }
        
        }
        
        return $html;
    }
    
    public function get_global_attribute_term_options(
        &$args,
        &$attr,
        &$term,
        &$product_swatch_options,
        $key,
        $meta_key
    )
    {
        $meta_values = $this->get_quick_attribute_term_options(
            $args,
            $attr,
            $term->slug,
            $product_swatch_options,
            $key,
            $meta_key,
            false,
            $term
        );
        $overridden = !empty($meta_values['value']);
        $meta_values['type'] = ( !empty($meta_values['type']) ? $meta_values['type'] : $attr->attribute_type );
        $type = $meta_values['type'];
        
        if ( $type === 'color' ) {
            if ( empty($meta_values['value']) ) {
                $meta_values['value'] = get_term_meta( $term->term_id, 'color', true );
            }
        } else {
            
            if ( $type === 'image' ) {
                if ( empty($meta_values['value']) ) {
                    $meta_values['value'] = get_term_meta( $term->term_id, 'image', true );
                }
                if ( !empty($meta_values['value']) ) {
                    $meta_values['value'] = ( wp_attachment_is_image( $meta_values['value'] ) ? $meta_values['value'] : null );
                }
            }
        
        }
        
        if ( !$overridden ) {
            $this->swatch_type_override(
                $meta_values,
                $args,
                $attr,
                $term->slug,
                true
            );
        }
        return $meta_values;
    }
    
    public function get_quick_attribute_term_options(
        &$args,
        &$attr,
        &$option,
        &$product_swatch_options,
        $key,
        $meta_key,
        $enable_auto_convert = true,
        $term = null
    )
    {
        $attribute_slug = sanitize_title( $args['attribute'] );
        $variations = $this->get_product_variations( $args );
        $meta_values = array(
            'type'          => ( !empty($attr->attribute_type) ? $attr->attribute_type : null ),
            'value'         => null,
            'style'         => null,
            'image'         => null,
            'tooltip'       => null,
            'tooltip_image' => null,
            'tooltip_text'  => null,
        );
        extract( $meta_values );
        /** @var $type */
        /** @var $value */
        /** @var $image */
        /** @var $style */
        /** @var $tooltip */
        
        if ( $enable_auto_convert && empty($meta_values['value']) ) {
            $this->swatch_type_override(
                $meta_values,
                $args,
                $attr,
                $option
            );
            $type = $meta_values['type'];
        }
        
        
        if ( $type === 'color' ) {
            $meta_values['image'] = $this->get_variation_color_image( $variations, $attribute_slug, $option );
            $meta_values['style'] = $this->get_option_value(
                $product_attr_options,
                $product_attr_term_options,
                'color_swatch_style',
                'xt_woovs-round',
                $term
            );
            $meta_values['tooltip'] = $this->get_option_value(
                $product_attr_options,
                $product_attr_term_options,
                'color_swatch_tooltip',
                'disabled',
                $term
            );
        } else {
            
            if ( $type === 'image' ) {
                $meta_values['image'] = $meta_values['value'];
                $meta_values['style'] = $this->get_option_value(
                    $product_attr_options,
                    $product_attr_term_options,
                    'image_swatch_style',
                    'xt_woovs-round_corner',
                    $term
                );
                $meta_values['tooltip'] = $this->get_option_value(
                    $product_attr_options,
                    $product_attr_term_options,
                    'image_swatch_tooltip',
                    'disabled',
                    $term
                );
            } else {
                
                if ( $type === 'label' ) {
                    $meta_values['image'] = $this->get_variation_color_image( $variations, $attribute_slug, $option );
                    $meta_values['style'] = $this->get_option_value(
                        $product_attr_options,
                        $product_attr_term_options,
                        'label_swatch_style',
                        'xt_woovs-square',
                        $term
                    );
                    $meta_values['tooltip'] = $this->get_option_value(
                        $product_attr_options,
                        $product_attr_term_options,
                        'label_swatch_tooltip',
                        'disabled',
                        $term
                    );
                }
            
            }
        
        }
        
        
        if ( in_array( $type, array_keys( $this->core->types ) ) ) {
            if ( $meta_values['tooltip'] === 'image' ) {
                $meta_values['tooltip_image'] = $this->get_option_value(
                    $product_attr_options,
                    $product_attr_term_options,
                    'swatch_tooltip_image',
                    null,
                    $term
                );
            }
            if ( $meta_values['tooltip'] === 'text' ) {
                $meta_values['tooltip_text'] = $this->get_option_value(
                    $product_attr_options,
                    $product_attr_term_options,
                    'swatch_tooltip_text',
                    null,
                    $term
                );
            }
        }
        
        return $meta_values;
    }
    
    public function get_option_value(
        &$product_attr_options,
        &$product_attr_term_options,
        $key,
        $default,
        $term
    )
    {
        // Product Level Attribute Term / Individual Swatch Options
        
        if ( !empty($product_attr_term_options[$key]) ) {
            return $product_attr_term_options[$key];
        } else {
            // Product Level Attribute Swatch Options
            
            if ( !empty($product_attr_options[$key]) ) {
                return $product_attr_options[$key];
            } else {
                // Global Attribute Term Level Swatch Options
                
                if ( !empty($term) ) {
                    $attr_term_option_value = get_term_meta( $term->term_id, $key, true );
                    if ( !empty($attr_term_option_value) ) {
                        return $attr_term_option_value;
                    }
                }
            
            }
        
        }
        
        // Global Customizer Swatch Options
        return xt_woovs_type_option( $key, $default );
    }
    
    public function swatch_type_override(
        &$meta_values,
        &$args,
        &$attr,
        $option,
        $is_term = false
    )
    {
        extract( $meta_values );
        /** @var $type */
        /** @var $value */
        /** @var $image */
        /** @var $style */
        /** @var $tooltip */
        /** @var $tooltip_image */
        /** @var $tooltip_text */
        
        if ( (empty($type) || $type === 'select') && !empty(xt_woovs_type_option( 'other_to_label', true )) ) {
            $meta_values['value'] = '';
            $meta_values['type'] = 'label';
        }
        
        if ( $type === 'image' && !empty($value) ) {
            return false;
        }
        
        if ( !empty(xt_woovs_type_option( 'color_to_image', true )) ) {
            $attribute = $args['attribute'];
            $attribute_slug = sanitize_title( $attribute );
            $attribute = ( $is_term ? $attr->attribute_name : $attribute );
            $variations = $this->get_product_variations( $args );
            $custom_attributes = xt_woovs_type_option( 'color_to_image_custom_attributes', array() );
            
            if ( empty($type) || $type === 'select' ) {
                $attribute = strtolower( $attribute );
                $has_color = strpos( $attribute, 'color' ) !== false;
                $has_image = strpos( $attribute, 'image' ) !== false;
                
                if ( xt_woovs_search_array( $custom_attributes, 'attribute', $attribute ) !== null || $has_color || $has_image ) {
                    $new_value = $this->get_variation_color_image( $variations, $attribute_slug, $option );
                    $meta_values['type'] = ( !empty($new_value) ? 'image' : $type );
                    $meta_values['value'] = ( !empty($new_value) ? $new_value : $meta_values['value'] );
                }
            
            } else {
                
                if ( in_array( $type, array( 'color', 'image' ) ) ) {
                    $new_value = $this->get_variation_color_image( $variations, $attribute_slug, $option );
                    $meta_values['type'] = ( !empty($new_value) ? 'image' : $type );
                    $meta_values['value'] = ( !empty($new_value) ? $new_value : $meta_values['value'] );
                }
            
            }
        
        }
    
    }
    
    public function get_variation_color_image( &$variations, $attribute_slug, $option )
    {
        $attribute_key = 'attribute_' . $attribute_slug;
        foreach ( $variations as $variation ) {
            if ( !empty($variation['attributes'][$attribute_key]) && $variation['attributes'][$attribute_key] === $option ) {
                return $variation["image_id"];
            }
        }
    }
    
    /**
     * Print HTML of a single swatch
     *
     * @param $html
     * @param $type
     * @param $meta_option
     * @param $meta_value
     * @param $selected
     *
     * @return string
     */
    public function swatch_meta_html(
        $html,
        $option,
        $meta_values = array(),
        $selected = null
    )
    {
        extract( $meta_values );
        /** @var $type */
        /** @var $value */
        /** @var $image */
        /** @var $style */
        /** @var $tooltip */
        /** @var $tooltip_image */
        /** @var $tooltip_text */
        $tooltip_data = array(
            'tooltip'       => $tooltip,
            'tooltip_image' => ( $tooltip_image ? $tooltip_image : $image ),
            'tooltip_text'  => $tooltip_text,
        );
        switch ( $type ) {
            case 'color':
                $html = $this->get_color_swatch_html(
                    $option,
                    $option,
                    $value,
                    $tooltip_data,
                    $selected,
                    $style
                );
                break;
            case 'image':
                $html = $this->get_image_swatch_html(
                    $option,
                    $option,
                    $value,
                    $tooltip_data,
                    $selected,
                    $style
                );
                break;
            case 'label':
                $html = $this->get_label_swatch_html(
                    $option,
                    $option,
                    $tooltip_data,
                    $selected,
                    $style
                );
                break;
        }
        return $html;
    }
    
    /**
     * Print HTML of a single swatch
     *
     * @param $html
     * @param $term
     * @param $attr
     * @param $args
     *
     * @return string
     */
    public function swatch_html(
        $html,
        $term,
        $meta_values = array(),
        $selected = null
    )
    {
        $option = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
        $slug = $term->slug;
        extract( $meta_values );
        /** @var $type */
        /** @var $value */
        /** @var $image */
        /** @var $style */
        /** @var $tooltip */
        /** @var $tooltip_image */
        /** @var $tooltip_text */
        $tooltip_data = array(
            'tooltip'       => $tooltip,
            'tooltip_image' => ( $tooltip_image ? $tooltip_image : $image ),
            'tooltip_text'  => $tooltip_text,
        );
        switch ( $type ) {
            case 'color':
                $html = $this->get_color_swatch_html(
                    $slug,
                    $option,
                    $value,
                    $tooltip_data,
                    $selected,
                    $style
                );
                break;
            case 'image':
                $html = $this->get_image_swatch_html(
                    $slug,
                    $option,
                    $value,
                    $tooltip_data,
                    $selected,
                    $style
                );
                break;
            case 'label':
                $html = $this->get_label_swatch_html(
                    $slug,
                    $option,
                    $tooltip_data,
                    $selected,
                    $style
                );
                break;
        }
        return $html;
    }
    
    /**
     * Replace add to cart button in the loop.
     */
    function change_loop_add_to_cart()
    {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        add_action( 'woocommerce_after_shop_loop_item', array( $this, 'template_loop_add_to_cart' ), 99 );
    }
    
    /**
     * Use single add to cart button for variable products.
     */
    function template_loop_add_to_cart()
    {
        global  $product ;
        
        if ( !$this->enabled() ) {
            woocommerce_template_loop_add_to_cart();
            return;
        }
        
        $catalog_mode = xt_woovs_type_option_bool( 'catalog_mode', false );
        
        if ( !$product->is_type( 'variable' ) ) {
            if ( !$catalog_mode ) {
                woocommerce_template_loop_add_to_cart();
            }
            return;
        }
        
        remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
        add_action( 'woocommerce_single_variation', array( $this, 'loop_variation_add_to_cart_button' ), 20 );
        woocommerce_template_single_add_to_cart();
    }
    
    /**
     * Customise variable add to cart button for loop.
     *
     * Remove qty selector and simplify.
     */
    function loop_variation_add_to_cart_button()
    {
        global  $product ;
        ?>
		<div class="woocommerce-variation-add-to-cart xt_woovs-variation-add-to-cart variations_button">
			<?php 
        echo  apply_filters(
            'woocommerce_loop_add_to_cart_link',
            sprintf( '<button type="submit" class="single_add_to_cart_button button">%s</button>', esc_html( $product->single_add_to_cart_text() ) ),
            $product,
            []
        ) ;
        ?>
			<input type="hidden" name="add-to-cart" value="<?php 
        echo  absint( $product->get_id() ) ;
        ?>" />
			<input type="hidden" name="product_id" value="<?php 
        echo  absint( $product->get_id() ) ;
        ?>" />
			<input type="hidden" name="variation_id" class="variation_id" value="0" />
		</div>
		<?php 
    }
    
    public function get_color_swatch_html(
        $slug,
        $name,
        $color,
        $tooltip_data,
        $selected,
        $style
    )
    {
        extract( $tooltip_data );
        /** @var $tooltip */
        /** @var $tooltip_image */
        /** @var $tooltip_text */
        if ( empty($color) ) {
            $color = '#eaeaea';
        }
        $tooltip_class = '';
        $tooltip_value = '';
        return sprintf(
            '<li class="swatch swatch-color swatch-%s %s %s %s" title="%s" data-value="%s" data-tooltip_type="%s" data-tooltip_value="%s"><span class="swatch-inner swatch-color-inner"><span class="swatch-color-inner-inner" style="background-color:%s;"></span></span></li>',
            esc_attr( $slug ),
            esc_attr( $selected ),
            esc_attr( $style ),
            esc_attr( $tooltip_class ),
            esc_attr( $name ),
            esc_attr( $slug ),
            esc_attr( $tooltip ),
            esc_attr( $tooltip_value ),
            esc_attr( $color )
        );
    }
    
    public function get_image_swatch_html(
        $slug,
        $name,
        $image,
        $tooltip_data,
        $selected,
        $style
    )
    {
        extract( $tooltip_data );
        /** @var $tooltip */
        /** @var $tooltip_image */
        /** @var $tooltip_text */
        $image = ( $image ? wp_get_attachment_image_src( $image ) : '' );
        $image = ( $image ? $image[0] : $this->core->plugin_url( 'admin/assets/images', 'placeholder.png' ) );
        $tooltip_class = '';
        $tooltip_value = '';
        return sprintf(
            '<li class="swatch swatch-image swatch-%s %s %s %s" title="%s" data-value="%s" data-tooltip_type="%s" data-tooltip_value="%s"><span class="swatch-inner swatch-image-inner"><img src="%s" alt="%s"></span></li>',
            esc_attr( $slug ),
            esc_attr( $selected ),
            esc_attr( $style ),
            esc_attr( $tooltip_class ),
            esc_attr( $name ),
            esc_attr( $slug ),
            esc_attr( $tooltip ),
            esc_attr( $tooltip_value ),
            esc_url( $image ),
            esc_attr( $name )
        );
    }
    
    public function get_label_swatch_html(
        $slug,
        $name,
        $tooltip_data,
        $selected,
        $style
    )
    {
        extract( $tooltip_data );
        /** @var $tooltip */
        /** @var $tooltip_image */
        /** @var $tooltip_text */
        $tooltip_class = '';
        $tooltip_value = '';
        return sprintf(
            '<li class="swatch swatch-label swatch-%s %s %s %s" title="%s" data-value="%s" data-tooltip_type="%s" data-tooltip_value="%s"><span class="swatch-inner swatch-label-inner">%s</span></li>',
            esc_attr( $slug ),
            esc_attr( $selected ),
            esc_attr( $style ),
            esc_attr( $tooltip_class ),
            esc_attr( $name ),
            esc_attr( $slug ),
            esc_attr( $tooltip ),
            esc_attr( $tooltip_value ),
            esc_html( $name )
        );
    }
    
    public function get_product_variations( &$args )
    {
        $product = $args['product'];
        $cache_key = 'xt_woovs_product_variations_' . $product->get_ID();
        $variations = wp_cache_get( $cache_key );
        
        if ( $variations === false ) {
            $variations = $product->get_available_variations();
            wp_cache_set( $cache_key, $variations );
        }
        
        return $variations;
    }

}