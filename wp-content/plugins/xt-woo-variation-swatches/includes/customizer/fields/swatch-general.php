<?php

if ( $type === 'archives' && self::$parent->fs()->can_use_premium_code__premium_only() || $type === 'single' ) {
    Xirki::add_field( self::$config_id, array(
        'settings' => self::field_id( $type . '_swatches_enabled' ),
        'section'  => self::section_id( $type . '-swatch-general' ),
        'label'    => esc_html__( 'Enable Swatches.', 'xt-woo-variation-swatches' ),
        'type'     => 'toggle',
        'default'  => self::types_default_values( $type, '1', '0' ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings' => self::field_id( $type . '_other_to_label' ),
        'section'  => self::section_id( $type . '-swatch-general' ),
        'label'    => esc_html__( 'Automatically convert Dropdowns to Label Swatch by default.', 'xt-woo-variation-swatches' ),
        'type'     => 'toggle',
        'default'  => '1',
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings' => self::field_id( $type . '_color_to_image' ),
        'section'  => self::section_id( $type . '-swatch-general' ),
        'label'    => esc_html__( 'Automatically convert Dropdowns to Image Swatch if variation has an image.', 'xt-woo-variation-swatches' ),
        'type'     => 'toggle',
        'default'  => '1',
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'        => self::field_id( $type . '_color_to_image_custom_attributes' ),
        'section'         => self::section_id( $type . '-swatch-general' ),
        'label'           => esc_html__( 'Select Custom Attributes', 'xt-woo-variation-swatches' ),
        'description'     => esc_html__( 'Enter attribute names that will be converted to image swatches. If more than one attribute is available, only the first one will be converted. Note: this will only work if each variation has an image assigned.', 'xt-woo-variation-swatches' ),
        'type'            => 'repeater',
        'row_label'       => array(
        'type'  => 'text',
        'value' => esc_html__( 'Custom attribute', 'xt-woo-variation-swatches' ),
    ),
        'default'         => array( array(
        'attribute' => 'color',
    ), array(
        'attribute' => 'image',
    ) ),
        'fields'          => array(
        'attribute' => array(
        'type'    => 'text',
        'label'   => esc_html__( 'Custom Attribute', 'xt-woo-variation-swatches' ),
        'default' => '',
    ),
    ),
        'active_callback' => array( array(
        'setting'  => self::field_id( $type . '_color_to_image' ),
        'operator' => '==',
        'value'    => '1',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_swatches_align' ),
        'section'   => self::section_id( $type . '-swatch-general' ),
        'label'     => esc_html__( 'Swatches Alignment', 'xt-woo-variation-swatches' ),
        'type'      => 'radio-buttonset',
        'choices'   => array(
        'left'   => esc_attr__( 'Left', 'xt-woo-variation-swatches' ),
        'center' => esc_attr__( 'Center', 'xt-woo-variation-swatches' ),
        'right'  => esc_attr__( 'Right', 'xt-woo-variation-swatches' ),
    ),
        'default'   => self::types_default_values( $type, 'left', 'center' ),
        'transport' => 'postMessage',
        'js_vars'   => array( array(
        'element'  => $element_prefix . ' .xt_woovs-swatches-wrap',
        'function' => 'class',
        'prefix'   => 'xt_woovs-align-',
    ), array(
        'element'  => '.xt_woovs-archives-product .variations_form.xt_woovs-support',
        'property' => 'text-align',
    ) ),
        'output'    => array( array(
        'element'  => '.xt_woovs-archives-product .variations_form.xt_woovs-support',
        'property' => 'text-align',
    ) ),
    ) );
}

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( $type . '_general_features' ),
    'section'  => self::section_id( $type . '-swatch-general' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/' . $type . '-general.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );