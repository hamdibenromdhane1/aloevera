<?php

if ( $type === 'archives' && self::$parent->fs()->can_use_premium_code__premium_only() || $type === 'single' ) {
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_image_swatch_style' ),
        'section'   => self::section_id( $type . '-swatch-image' ),
        'label'     => esc_html__( 'Image Swatch Style', 'xt-woo-variation-swatches' ),
        'type'      => 'radio',
        'default'   => 'xt_woovs-round_corner',
        'choices'   => array(
        'xt_woovs-square'       => esc_html__( 'Square', 'xt-woo-variation-swatches' ),
        'xt_woovs-round'        => esc_html__( 'Circle', 'xt-woo-variation-swatches' ),
        'xt_woovs-round_corner' => esc_html__( 'Rounded', 'xt-woo-variation-swatches' ),
    ),
        'transport' => 'postMessage',
        'js_vars'   => array( array(
        'element'  => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-image',
        'function' => 'class',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_image_swatch_size' ),
        'section'   => self::section_id( $type . '-swatch-image' ),
        'label'     => esc_html__( 'Image Swatch Size', 'xt-woo-variation-swatches' ),
        'default'   => self::types_default_values( $type, 50, 25 ),
        'type'      => 'slider',
        'choices'   => array(
        'min'  => '10',
        'max'  => '120',
        'step' => '1',
    ),
        'transport' => 'auto',
        'output'    => array( array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-image',
        'property'      => 'width',
        'value_pattern' => '$px',
    ), array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-image .swatch-inner',
        'property'      => 'width',
        'value_pattern' => '$px',
    ) ),
    ) );
}

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( $type . '_image_features' ),
    'section'  => self::section_id( $type . '-swatch-image' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/' . $type . '-image-swatch.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );