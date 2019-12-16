<?php

if ( $type === 'archives' && self::$parent->fs()->can_use_premium_code__premium_only() || $type === 'single' ) {
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_color_swatch_style' ),
        'section'   => self::section_id( $type . '-swatch-color' ),
        'label'     => esc_html__( 'Color Swatch Style', 'xt-woo-variation-swatches' ),
        'type'      => 'radio',
        'default'   => 'xt_woovs-round',
        'choices'   => array(
        'xt_woovs-square'       => esc_html__( 'Square', 'xt-woo-variation-swatches' ),
        'xt_woovs-round'        => esc_html__( 'Circle', 'xt-woo-variation-swatches' ),
        'xt_woovs-round_corner' => esc_html__( 'Rounded', 'xt-woo-variation-swatches' ),
    ),
        'transport' => 'postMessage',
        'js_vars'   => array( array(
        'element'  => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-color',
        'function' => 'class',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_color_swatch_width' ),
        'section'   => self::section_id( $type . '-swatch-color' ),
        'label'     => esc_html__( 'Color Swatch Width', 'xt-woo-variation-swatches' ),
        'default'   => self::types_default_values( $type, 50, 25 ),
        'type'      => 'slider',
        'choices'   => array(
        'min'  => '10',
        'max'  => '80',
        'step' => '1',
    ),
        'transport' => 'auto',
        'output'    => array( array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-color .swatch-inner',
        'property'      => 'width',
        'value_pattern' => '$px',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_color_swatch_height' ),
        'section'   => self::section_id( $type . '-swatch-color' ),
        'label'     => esc_html__( 'Color Swatch Height', 'xt-woo-variation-swatches' ),
        'default'   => self::types_default_values( $type, 50, 25 ),
        'type'      => 'slider',
        'choices'   => array(
        'min'  => '10',
        'max'  => '80',
        'step' => '1',
    ),
        'transport' => 'auto',
        'output'    => array( array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-color .swatch-inner',
        'property'      => 'height',
        'value_pattern' => '$px',
    ) ),
    ) );
}

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( $type . '_color_features' ),
    'section'  => self::section_id( $type . '-swatch-color' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/' . $type . '-color-swatch.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );