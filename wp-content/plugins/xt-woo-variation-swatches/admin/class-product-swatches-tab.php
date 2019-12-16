<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class XT_Woo_Variation_Swatches_Tab
{
    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      obj    core    Core Class
     */
    private  $core ;
    public  $meta_name = '__xt_woovs_woo_meta_settings' ;
    public  $fields = array() ;
    /**
     * Class constructor.
     */
    public function __construct( &$core )
    {
        $this->core = $core;
        $this->registerFields();
    }
    
    public function registerFields()
    {
        $this->fields[] = array(
            'id'          => 'swatch_style',
            'label'       => esc_html__( 'Swatch Style', 'xt-woo-variation-swatches' ),
            'type'        => 'select',
            'options'     => array(
            'xt_woovs-square'       => esc_html__( 'Square', 'xt-woo-variation-swatches' ),
            'xt_woovs-round'        => esc_html__( 'Circle', 'xt-woo-variation-swatches' ),
            'xt_woovs-round_corner' => esc_html__( 'Rounded', 'xt-woo-variation-swatches' ),
        ),
            'location'    => array( 'attribute', 'term' ),
            'type_prefix' => true,
        );
        $this->fields[] = array(
            'id'          => 'swatch_tooltip',
            'label'       => esc_html__( 'Swatch Tooltip', 'xt-woo-variation-swatches' ),
            'type'        => 'select',
            'options'     => array(
            'disabled' => esc_html__( 'Disabled', 'xt-woo-variation-swatches' ),
            'text'     => esc_html__( 'Text', 'xt-woo-variation-swatches' ),
            'image'    => esc_html__( 'Image', 'xt-woo-variation-swatches' ),
        ),
            'location'    => array( 'attribute', 'term' ),
            'type_prefix' => true,
        );
        $this->fields[] = array(
            'id'          => 'swatch_tooltip_image',
            'label'       => esc_html__( 'Swatch Tooltip Image', 'xt-woo-variation-swatches' ),
            'type'        => 'image',
            'location'    => array( 'attribute', 'term' ),
            'type_prefix' => false,
            'visible'     => array( array(
            'key'         => 'swatch_tooltip',
            'value'       => 'image',
            'type_prefix' => true,
        ) ),
        );
        $this->fields[] = array(
            'id'          => 'swatch_tooltip_text',
            'label'       => esc_html__( 'Swatch Tooltip Text', 'xt-woo-variation-swatches' ),
            'type'        => 'text',
            'location'    => array( 'attribute', 'term' ),
            'type_prefix' => false,
            'visible'     => array( array(
            'key'         => 'swatch_tooltip',
            'value'       => 'text',
            'type_prefix' => true,
        ) ),
        );
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_script( 'jquery-ui-accordion' );
    }
    
    public function product_swatches_data_tab( $product_data_tabs )
    {
        $product_data_tabs['xt_woovs'] = array(
            'label'  => esc_html__( 'Swatches', 'xt-woo-variation-swatches' ),
            'target' => 'xt_woovs_tab_content',
            'class'  => array( 'show_if_variable' ),
        );
        return $product_data_tabs;
    }
    
    public function product_data_panel_wrap()
    {
        global  $post ;
        ?>
        <div id="xt_woovs_tab_content" class="panel xt_woovs_tab_content woocommerce_options_panel hidden">
            <div class="xt_woovs_fields">
                <?php 
        $this->load_tab_content( $post->ID );
        ?>
            </div>
        </div>
	    <?php 
    }
    
    // Load tab content
    function load_tab_content( $post_id, $return = false )
    {
        if ( $return ) {
            ob_start();
        }
        $this->render_premium_features();
        if ( $return ) {
            return ob_get_clean();
        }
    }
    
    // Load tab content
    function load_tab_content_ajax()
    {
        $post_id = ( !empty($_POST['post_id']) ? intval( $_POST['post_id'] ) : 0 );
        $response = $this->load_tab_content( $post_id, true );
        wp_die( $response );
    }
    
    public function render_premium_features()
    {
        ?>
        <div class="xt_woovs-tab-premium">
            <p>
	            <?php 
        echo  sprintf( esc_html__( 'The %s will unlock product level swatch customizations that overrides global settings.', 'woo-variation-swatches' ), wp_kses_post( '<strong>' . esc_html__( 'Premium Version', 'woo-variation-swatches' ) . '</strong>' ) ) ;
        ?>
            </p>
            <p>
                <a href="<?php 
        echo  esc_url( $this->core->fs()->get_upgrade_url() ) ;
        ?>" class="button button-primary button-hero">
                    <?php 
        echo  esc_html__( 'Upgrade Now', 'woo-variation-swatches' ) ;
        ?>
                </a>
            </p>
        </div>
        <?php 
    }
    
    public function render_product_tab_content( $post_id )
    {
        /* default Value Added */
        $default_type = 'default';
        $this->core->plugin_loader()->add_filter( 'woocommerce_variation_is_visible', $this, 'return_true' );
        $product = wc_get_product( $post_id );
        /* Product Options */
        $swatch_type_options = $product->get_meta( '_xt_woovs_swatch_type_options', true );
        $product_type_array = array( 'variable', 'variable-subscription' );
        if ( !in_array( $product->get_type(), $product_type_array ) ) {
            return;
        }
        $woocommerce_taxonomies = wc_get_attribute_taxonomies();
        $woocommerce_taxonomy_infos = array();
        foreach ( $woocommerce_taxonomies as $tax ) {
            $woocommerce_taxonomy_infos[wc_attribute_taxonomy_name( $tax->attribute_name )] = $tax;
        }
        $tax = null;
        $attributes = $product->get_variation_attributes();
        //Attributes configured on this product already.
        
        if ( $attributes && count( $attributes ) > 0 ) {
            $attribute_names = array_keys( $attributes );
            foreach ( $attribute_names as $name ) {
                $key = md5( sanitize_title( $name ) );
                $current_is_taxonomy = taxonomy_exists( $name );
                $attribute_terms = array();
                
                if ( taxonomy_exists( $name ) ) {
                    $woocommerce_taxonomy = $woocommerce_taxonomy_infos[$name];
                    $current_label = ( isset( $woocommerce_taxonomy->attribute_label ) && !empty($woocommerce_taxonomy->attribute_label) ? $woocommerce_taxonomy->attribute_label : $woocommerce_taxonomy->attribute_name );
                    $terms = get_terms( $name, array(
                        'hide_empty' => false,
                    ) );
                    $selected_terms = ( isset( $attributes[$name] ) ? $attributes[$name] : array() );
                    foreach ( $terms as $term ) {
                        if ( in_array( $term->slug, $selected_terms ) ) {
                            $attribute_terms[] = array(
                                'id'     => md5( $term->slug ),
                                'label'  => $term->name,
                                'old_id' => $term->slug,
                            );
                        }
                    }
                } else {
                    $current_label = esc_html( $name );
                    foreach ( $attributes[$name] as $term ) {
                        $attribute_terms[] = array(
                            'id'     => md5( sanitize_title( strtolower( $term ) ) ),
                            'label'  => esc_html( $term ),
                            'old_id' => esc_attr( sanitize_title( $term ) ),
                        );
                    }
                }
                
                $attribute_options = ( !empty($swatch_type_options[$key]) ? $swatch_type_options[$key] : array() );
                $current_type = ( !empty($attribute_options['type']) ? $attribute_options['type'] : $default_type );
                $active_fields = ( isset( $current_type ) && ($current_type == "default" || $current_type == "term_options") ? 'hidden' : '' );
                $attr_meta_prefix = '_xt_woovs_swatch_type_options[' . $key . ']';
                // COLOR
                $active_color = ( $current_type != "product_color" ? 'hidden' : '' );
                // IMAGE
                $active_image = ( $current_type != "product_image" ? 'hidden' : '' );
                // LABEL
                $active_label = ( $current_type != "product_label" ? 'hidden' : '' );
                $image_placeholder_src = $this->core->plugin_url( 'admin/assets/images', 'placeholder.png' );
                $image_placeholder_bg = "url('" . $image_placeholder_src . "')";
                $color_placeholder_src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAE4AAABQAQMAAACNuNG1AAAABlBMVEXMzMz////TjRV2AAAAHElEQVR4AWNg+A+Ff6jL/A+HVGUOZfeOunfUvQAbQI4IO7xuxwAAAABJRU5ErkJggg==";
                $color_placeholder_bg = "url('" . $color_placeholder_src . "')";
                ?>

                <div class="field xt_woovs_meta_wrp">
                    <div class="xt_woovs_field_meta xt_woovs_sub_heading">
                        <table class="xt_woovs widefat">
                            <tbody>
                                <tr>
                                    <td class="attribute_swatch_label">
                                        <strong>
                                            <span class="xt_woovs_edit_field row-title"><?php 
                echo  sanitize_text_field( $current_label ) ;
                ?></span>
                                            <span class="xt_woovs_badge"><?php 
                echo  ( $current_is_taxonomy ? esc_html__( 'Global Attribute', 'xt-woo-variation-swatches' ) : esc_html__( 'Quick Attribute', 'xt-woo-variation-swatches' ) ) ;
                ?></span>
                                        </strong>
                                    </td>
                                    <td class="attribute_swatch_type">
                                        <strong><?php 
                echo  esc_html__( 'Swatch Type', 'xt-woo-variation-swatches' ) ;
                ?></strong>
                                        <select class="_xt_woovs_swatch_type_options_type" id="_xt_woovs_swatch_type_options_<?php 
                echo  esc_attr( $key ) ;
                ?>_type" name="<?php 
                echo  esc_attr( $attr_meta_prefix ) ;
                ?>[type]">
                                            <option <?php 
                selected( $current_type, 'default' );
                ?> value="default"><?php 
                echo  esc_html__( 'Inherit Global', 'xt-woo-variation-swatches' ) ;
                ?></option>
                                            <option <?php 
                selected( $current_type, 'product_color' );
                ?> value="product_color"><?php 
                echo  esc_html__( 'Custom Colors', 'xt-woo-variation-swatches' ) ;
                ?></option>
                                            <option <?php 
                selected( $current_type, 'product_image' );
                ?> value="product_image"><?php 
                echo  esc_html__( 'Custom Images', 'xt-woo-variation-swatches' ) ;
                ?></option>
                                            <option <?php 
                selected( $current_type, 'product_label' );
                ?> value="product_label"><?php 
                echo  esc_html__( 'Custom Labels', 'xt-woo-variation-swatches' ) ;
                ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="xt_woovs_fields xt_woovs_accordion <?php 
                echo  esc_attr( $active_fields ) ;
                ?>">

                        <div class="field_option field_option_color section-color-swatch <?php 
                echo  esc_attr( $active_color ) ;
                ?>">
                            <table class="xt_woovs_attribute_options widefat">
                                <tbody>
                                    <?php 
                $this->render_options(
                    $attr_meta_prefix,
                    $attribute_options,
                    'color',
                    'attribute'
                );
                ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="field_option field_option_image <?php 
                echo  esc_attr( $active_image ) ;
                ?>">

                            <table class="xt_woovs_attribute_options widefat">
                                <tbody>
                                    <?php 
                $this->render_options(
                    $attr_meta_prefix,
                    $attribute_options,
                    'image',
                    'attribute'
                );
                ?>
                                </tbody>
                            </table>

                        </div>

                        <div class="field_option field_option_label <?php 
                echo  esc_attr( $active_label ) ;
                ?>">

                            <table class="xt_woovs_attribute_options widefat">
                                <tbody>
                                    <?php 
                $this->render_options(
                    $attr_meta_prefix,
                    $attribute_options,
                    'label',
                    'attribute'
                );
                ?>
                                </tbody>
                            </table>

                        </div>

                        <?php 
                foreach ( $attribute_terms as $attribute_term ) {
                    $term_id = $attribute_term['id'];
                    $term_options = ( !empty($attribute_options[$term_id]) ? $attribute_options[$term_id] : array() );
                    $current_type = ( !empty($term_options['type']) ? $term_options['type'] : $current_type );
                    $term_meta_prefix = $attr_meta_prefix . '[' . esc_attr( $term_id ) . ']';
                    // COLOR
                    $active_color = ( $current_type != "product_color" ? 'hidden' : '' );
                    $color = ( !empty($term_options['color']) ? $term_options['color'] : '' );
                    $color_preview = ( !empty($color) ? 'style="background-color:' . esc_attr( $color ) . '"' : 'style="background-image:' . esc_attr( $color_placeholder_bg ) . '"' );
                    //IMAGE
                    $active_image = ( $current_type != "product_image" ? 'hidden' : '' );
                    $img_id = ( !empty($term_options['image']) ? $term_options['image'] : null );
                    $image_array = ( !empty($img_id) ? wp_get_attachment_image_src( $img_id ) : null );
                    $image = ( !empty($image_array[0]) ? $image_array[0] : null );
                    $image_preview = ( !empty($image) ? 'style="background-image:url(' . esc_url( $image ) . ')"' : 'style="background-image:' . esc_attr( $image_placeholder_bg ) . '"' );
                    $image_picker_preview = ( !empty($image) ? $image : $image_placeholder_src );
                    $remove_image_hidden = ( !empty($image) ? '' : 'hidden' );
                    //LABEL
                    $active_label = ( $current_type != "product_label" ? 'hidden' : '' );
                    $hide_preview = ( !$active_label ? 'hidden' : '' );
                    ?>

                            <div class="xt_woovs_field_meta xt_woovs_meta" data-id="<?php 
                    echo  esc_attr( $term_id ) ;
                    ?>">
                                <div class="xs_woovs_accordion_handle">
                                    <table class="xt_woovs_input widefat">
                                        <tbody>

                                            <tr>
                                                <td width="30" class="xt_woovs_preview_td <?php 
                    echo  esc_attr( $hide_preview ) ;
                    ?>">
                                                    <div class="field_option field_option_color section-color-swatch <?php 
                    echo  esc_attr( $active_color ) ;
                    ?>">
                                                        <div class="xt_woovs_preview xt_woovs_color_preview" <?php 
                    echo  $color_preview ;
                    ?>></div>
                                                    </div>

                                                    <div class="field_option field_option_image xt_woovs_image_picker <?php 
                    echo  esc_attr( $active_image ) ;
                    ?>">
                                                        <div class="xt_woovs_preview xt_woovs_image_preview" <?php 
                    echo  $image_preview ;
                    ?>></div>
                                                    </div>
                                                </td>
                                                <td class="label" width="25%">
                                                   <?php 
                    echo  esc_html( $attribute_term['label'] ) ;
                    ?>
                                                </td>
                                                <td class="attribute_swatch_type">

                                                    <strong><?php 
                    echo  esc_html__( 'Swatch Type', 'xt-woo-variation-swatches' ) ;
                    ?></strong>

                                                    <select class="_xt_woovs_swatch_term_type_options_type" id="_xt_woovs_swatch_term_type_options_<?php 
                    echo  esc_attr( $key ) ;
                    ?>_<?php 
                    echo  esc_attr( $attribute_term['id'] ) ;
                    ?>_type" name="<?php 
                    echo  esc_attr( $term_meta_prefix ) ;
                    ?>[type]">
                                                        <option <?php 
                    selected( $current_type, 'product_color' );
                    ?> value="product_color"><?php 
                    echo  esc_html__( 'Color', 'xt-woo-variation-swatches' ) ;
                    ?></option>
                                                        <option <?php 
                    selected( $current_type, 'product_image' );
                    ?> value="product_image"><?php 
                    echo  esc_html__( 'Image', 'xt-woo-variation-swatches' ) ;
                    ?></option>
                                                        <option <?php 
                    selected( $current_type, 'product_label' );
                    ?> value="product_label"><?php 
                    echo  esc_html__( 'Label', 'xt-woo-variation-swatches' ) ;
                    ?></option>
                                                    </select>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div>

                                    <table class="xt_woovs_input widefat">
                                        <tbody>
                                            <tr>
                                                <td colspan="2">

                                                    <div class="field_option field_option_color section-color-swatch <?php 
                    echo  esc_attr( $active_color ) ;
                    ?>">
                                                        <table class="xt_woovs_input widefat">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label" width="25%">
                                                                        <?php 
                    echo  esc_html__( 'Color' ) ;
                    ?>
                                                                    </td>
                                                                    <td>
                                                                         <input type="text" class="xt_woovs-term-color xt_woovs-color-picker" name="_xt_woovs_swatch_type_options[<?php 
                    echo  esc_attr( $key ) ;
                    ?>][<?php 
                    echo  esc_attr( $attribute_term['id'] ) ;
                    ?>][color]" value="<?php 
                    echo  esc_attr( $color ) ;
                    ?>">
                                                                    </td>
                                                                </tr>
                                                                <?php 
                    $this->render_options(
                        $term_meta_prefix,
                        $term_options,
                        'color',
                        'term'
                    );
                    ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="field_option field_option_image <?php 
                    echo  esc_attr( $active_image ) ;
                    ?>">

                                                        <table class="xt_woovs_input widefat">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label" width="25%">
                                                                        <?php 
                    echo  esc_html__( 'Image' ) ;
                    ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="xt_woovs_image_picker">
                                                                            <img src="<?php 
                    echo  esc_url( $image_picker_preview ) ;
                    ?>" width="60px" height="60px" />

                                                                            <input type="hidden" class="xt_woovs-term-image" name="_xt_woovs_swatch_type_options[<?php 
                    echo  esc_attr( $key ) ;
                    ?>][<?php 
                    echo  esc_attr( $attribute_term['id'] ) ;
                    ?>][image]" value="<?php 
                    echo  esc_attr( $img_id ) ;
                    ?>" />

                                                                            <a href="#" class="button xt_woovs-meta-uploader" data-uploader-title="<?php 
                    echo  sprintf( esc_html__( 'Add image(s) to %s', 'xt-woo-variation-swatches' ), esc_attr( $attribute_term['label'] ) ) ;
                    ?>" data-uploader-button-text="<?php 
                    echo  esc_html__( 'Add image(s)', 'xt-woo-variation-swatches' ) ;
                    ?>"> <?php 
                    echo  esc_html__( 'Upload/Add image', 'xt-woo-variation-swatches' ) ;
                    ?></a>
                                                                            <a href="#" class="button xt_woovs_remove_meta_img <?php 
                    echo  esc_attr( $remove_image_hidden ) ;
                    ?>"><?php 
                    echo  esc_html__( 'Remove image', 'xt-woo-variation-swatches' ) ;
                    ?></a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php 
                    $this->render_options(
                        $term_meta_prefix,
                        $term_options,
                        'image',
                        'term'
                    );
                    ?>
                                                            </tbody>
                                                        </table>

                                                    </div>

                                                    <div class="field_option field_option_label <?php 
                    echo  esc_attr( $active_label ) ;
                    ?>">

                                                        <table class="xt_woovs_input widefat">
                                                            <tbody>
                                                                <?php 
                    $this->render_options(
                        $term_meta_prefix,
                        $term_options,
                        'label',
                        'term'
                    );
                    ?>
                                                            </tbody>
                                                        </table>

                                                    </div>

                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <?php 
                }
                ?>
                    </div>

                </div>
                <?php 
            }
        } else {
            echo  '<div class="xt_woovs_tab_no_attr_found">' ;
            echo  '    <p><strong>' . esc_html__( 'No attributes found!', 'xt-woo-variation-swatches' ) . '</strong></p>' ;
            echo  '    <p>' . esc_html__( 'Add a at least one attribute / variation combination to this product. You will then be able to see the swatch configuration over here.', 'xt-woo-variation-swatches' ) . '</p>' ;
            echo  '</div>' ;
        }
        
        remove_filter( 'woocommerce_variation_is_visible', array( $this, 'return_true' ) );
    }
    
    public function render_options(
        $key_prefix,
        $options,
        $type,
        $scope
    )
    {
        foreach ( $this->fields as $field ) {
            $meta_key = $field['id'];
            $type_prefix = $field['type_prefix'];
            if ( !empty($type_prefix) ) {
                $meta_key = $type . '_' . $meta_key;
            }
            $label = $field['label'];
            $location = $field['location'];
            if ( !in_array( $scope, $location ) ) {
                continue;
            }
            $value = ( !empty($options[$meta_key]) ? $options[$meta_key] : null );
            $visibleConditions = ( !empty($field['visible']) ? $field['visible'] : array() );
            $visible = $this->isFieldVisible( $type, $field, $options );
            $visibility = ( !$visible ? 'hidden' : '' );
            ?>
            <tr class="xt_woovs_field_row <?php 
            echo  esc_attr( $visibility ) ;
            ?>" data-type="<?php 
            echo  esc_attr( $type ) ;
            ?>" data-type_prefix="<?php 
            echo  esc_attr( $type_prefix ) ;
            ?>" data-key_prefix="<?php 
            echo  esc_attr( $key_prefix ) ;
            ?>" data-field="<?php 
            echo  esc_attr( $meta_key ) ;
            ?>" data-conditions="<?php 
            echo  htmlspecialchars( json_encode( $visibleConditions ), ENT_QUOTES, 'UTF-8' ) ;
            ?>">
                <td class="label" width="25%">
                    <?php 
            echo  esc_html__( $label ) ;
            ?>
                </td>
                <td>
                <?php 
            
            if ( $field['type'] === 'select' ) {
                $this->render_dropdown_field(
                    $field,
                    $key_prefix,
                    $meta_key,
                    $value
                );
            } else {
                
                if ( $field['type'] === 'text' ) {
                    $this->render_text_field(
                        $field,
                        $key_prefix,
                        $meta_key,
                        $value
                    );
                } else {
                    if ( $field['type'] === 'image' ) {
                        $this->render_image_field(
                            $field,
                            $key_prefix,
                            $meta_key,
                            $value
                        );
                    }
                }
            
            }
            
            ?>
                </td>
            </tr>
            <?php 
        }
    }
    
    public function render_dropdown_field(
        $field,
        $key_prefix,
        $key,
        $value
    )
    {
        $options = $field['options'];
        ?>
        <select class="xt_woovs_field" name="<?php 
        echo  esc_attr( $key_prefix ) ;
        ?>[<?php 
        echo  esc_attr( $key ) ;
        ?>]">

            <option <?php 
        selected( $value, null );
        ?> value="">Inherit</option>

            <?php 
        foreach ( $options as $option_key => $option ) {
            ?>
                <option <?php 
            selected( $value, $option_key );
            ?> value="<?php 
            echo  esc_attr( $option_key ) ;
            ?>"><?php 
            echo  sanitize_text_field( $option ) ;
            ?></option>
            <?php 
        }
        ?>

        </select>
        <?php 
    }
    
    public function render_text_field(
        $field,
        $key_prefix,
        $key,
        $value
    )
    {
        ?>
        <input class="xt_woovs_field" name="<?php 
        echo  esc_attr( $key_prefix ) ;
        ?>[<?php 
        echo  esc_attr( $key ) ;
        ?>]" value="<?php 
        echo  esc_attr( $value ) ;
        ?>" />
        <?php 
    }
    
    public function render_image_field(
        $field,
        $key_prefix,
        $key,
        $value
    )
    {
        $label = $field['label'];
        $image_placeholder_src = $this->core->plugin_url( 'admin/assets/images', 'placeholder.png' );
        $image = ( !empty($value) ? wp_get_attachment_image_src( $value ) : null );
        $image_picker_preview = ( !empty($image[0]) ? $image[0] : $image_placeholder_src );
        $remove_image_hidden = ( !empty($value) ? '' : 'hidden' );
        ?>
        <div class="xt_woovs_image_picker">

            <img src="<?php 
        echo  esc_url( $image_picker_preview ) ;
        ?>" width="60px" height="60px" />

            <input type="hidden" class="xt_woovs_field xt_woovs-term-image" name="<?php 
        echo  esc_attr( $key_prefix ) ;
        ?>[<?php 
        echo  esc_attr( $key ) ;
        ?>]" value="<?php 
        echo  esc_attr( $value ) ;
        ?>" />

            <a href="#" class="button xt_woovs-meta-uploader" data-uploader-title="<?php 
        echo  sprintf( esc_html__( 'Add image(s) to %s', 'xt-woo-variation-swatches' ), esc_attr( $label ) ) ;
        ?>" data-uploader-button-text="<?php 
        echo  esc_html__( 'Add image(s)', 'xt-woo-variation-swatches' ) ;
        ?>"> <?php 
        echo  esc_html__( 'Upload/Add image', 'xt-woo-variation-swatches' ) ;
        ?></a>
            <a href="#" class="button xt_woovs_remove_meta_img <?php 
        echo  esc_attr( $remove_image_hidden ) ;
        ?>"><?php 
        echo  esc_html__( 'Remove image', 'xt-woo-variation-swatches' ) ;
        ?></a>

        </div>
        <?php 
    }
    
    public function isFieldVisible( $type, &$field, &$options )
    {
        if ( empty($field['visible']) ) {
            return true;
        }
        foreach ( $field['visible'] as $condition ) {
            $target_key = $condition['key'];
            $type_prefix = $condition['type_prefix'];
            if ( !empty($type_prefix) ) {
                $target_key = $type . '_' . $target_key;
            }
            $target_value = $condition['value'];
            $current_value = ( !empty($options[$target_key]) ? $options[$target_key] : '' );
            $op = ( !empty($condition['op']) ? $condition['op'] : '==' );
            
            if ( $op == '==' && $target_value !== $current_value ) {
                return false;
            } else {
                
                if ( $op == '!=' && $target_value === $current_value ) {
                    return false;
                } else {
                    if ( $op == 'in' && is_array( $target_value ) && !in_array( $current_value, $target_value ) ) {
                        return false;
                    }
                }
            
            }
        
        }
        return true;
    }
    
    /**
     * Recursive sanitation of swatch options array post
     * 
     * @param $array_or_string (array|string)
     * @since  0.1
     * @return mixed
     */
    public function sanitize_swatch_options( $options )
    {
        foreach ( $options as $key => &$value ) {
            
            if ( is_array( $value ) ) {
                $value = $this->sanitize_swatch_options( $value );
            } else {
                $value = sanitize_text_field( $value );
            }
        
        }
        return $options;
    }
    
    public function process_meta_box( $post_id, $post )
    {
        $options_key = '_xt_woovs_swatch_type_options';
        $product = wc_get_product( $post_id );
        $swatch_type_options = ( isset( $_POST[$options_key] ) ? $this->sanitize_swatch_options( $_POST[$options_key] ) : false );
        $swatch_type = 'default';
        
        if ( $swatch_type_options && is_array( $swatch_type_options ) ) {
            foreach ( $swatch_type_options as $options ) {
                
                if ( isset( $options['type'] ) && $options['type'] != 'default' && $options['type'] != 'radio' ) {
                    $swatch_type = 'pickers';
                    break;
                }
            
            }
            $product->update_meta_data( $options_key, $swatch_type_options );
        }
        
        $product->update_meta_data( '_swatch_type', $swatch_type );
        $product->save_meta_data();
    }
    
    public function return_true()
    {
        return true;
    }
    
    // Saving the post via AJAX
    function save_post_ajax( $post_id )
    {
        # Ignore autosaves
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( empty($_POST['post_type']) ) {
            return;
        }
        # Only enabled for one post type
        # Remove this if statement if you want to enable for all post types
        if ( $_POST['post_type'] == 'product' ) {
            # Send JSON response
            # NOTE: We use ==, not ===, because the value may be String("true")
            
            if ( isset( $_POST['save_post_ajax'] ) && $_POST['save_post_ajax'] == TRUE ) {
                header( 'Content-type: application/json' );
                echo  json_encode( array(
                    'success' => true,
                ) ) ;
                # Don't return full wp-admin
                exit;
            }
        
        }
    }
    
    function enqueue_product_ajax_save_script()
    {
        global  $post ;
        # Only for one post type.
        
        if ( $post->post_type == 'product' ) {
            # Register and enqueue the script, dependent on jquery
            wp_enqueue_script(
                $this->core->plugin_slug( 'ajax-product-save' ),
                $this->core->plugin_url( 'admin' ) . 'assets/js/product-edit' . $this->core->script_suffix . '.js',
                array( 'jquery' ),
                $this->core->plugin_version(),
                false
            );
            # Localize our variables for use in our js script
            wp_localize_script( $this->core->plugin_slug( 'ajax-product-save' ), 'XT_WOOVS_AJAX', array(
                'post_id'  => $post->ID,
                'post_url' => admin_url( 'post.php' ),
            ) );
        }
    
    }

}