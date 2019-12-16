<?php

if ( $type === 'archives' && self::$parent->fs()->can_use_premium_code__premium_only() || $type === 'single' ) {
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_label_swatch_style' ),
        'section'   => self::section_id( $type . '-swatch-label' ),
        'label'     => esc_html__( 'Label Swatch Style', 'xt-woo-variation-swatches' ),
        'type'      => 'radio',
        'default'   => 'xt_woovs-square',
        'choices'   => array(
        'xt_woovs-square'       => esc_html__( 'Square', 'xt-woo-variation-swatches' ),
        'xt_woovs-round'        => esc_html__( 'Circle', 'xt-woo-variation-swatches' ),
        'xt_woovs-round_corner' => esc_html__( 'Rounded', 'xt-woo-variation-swatches' ),
    ),
        'transport' => 'postMessage',
        'js_vars'   => array( array(
        'element'  => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-label',
        'function' => 'class',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_label_swatch_min_width' ),
        'section'   => self::section_id( $type . '-swatch-label' ),
        'label'     => esc_html__( 'Label Swatch Min Width', 'xt-woo-variation-swatches' ),
        'default'   => self::types_default_values( $type, 50, 25 ),
        'type'      => 'slider',
        'choices'   => array(
        'min'  => '10',
        'max'  => '100',
        'step' => '1',
    ),
        'transport' => 'auto',
        'output'    => array( array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-label',
        'property'      => 'min-width',
        'value_pattern' => '$px',
    ), array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-label',
        'property'      => 'min-width',
        'value_pattern' => '$px',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_label_swatch_height' ),
        'section'   => self::section_id( $type . '-swatch-label' ),
        'label'     => esc_html__( 'Label Swatch Height', 'xt-woo-variation-swatches' ),
        'default'   => self::types_default_values( $type, 30, 20 ),
        'type'      => 'slider',
        'choices'   => array(
        'min'  => '10',
        'max'  => '100',
        'step' => '1',
    ),
        'transport' => 'auto',
        'output'    => array( array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-label',
        'property'      => 'height',
        'value_pattern' => '$px',
    ), array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-label',
        'property'      => 'line-height',
        'value_pattern' => '$px',
    ) ),
    ) );
    Xirki::add_field( self::$config_id, array(
        'settings'  => self::field_id( $type . '_label_swatch_size' ),
        'section'   => self::section_id( $type . '-swatch-label' ),
        'label'     => esc_html__( 'Label Swatch Font Size', 'xt-woo-variation-swatches' ),
        'default'   => self::types_default_values( $type, 13, 10 ),
        'type'      => 'slider',
        'choices'   => array(
        'min'  => '10',
        'max'  => '60',
        'step' => '1',
    ),
        'transport' => 'auto',
        'output'    => array( array(
        'element'       => $element_prefix . ' .xt_woovs-swatches-wrap .xt_woovs-swatches .swatch.swatch-label',
        'property'      => 'font-size',
        'value_pattern' => '$px',
    ) ),
    ) );
}

Xirki::add_field( self::$config_id, array(
    'settings' => self::field_id( $type . '_label_features' ),
    'section'  => self::section_id( $type . '-swatch-label' ),
    'type'     => 'xt-premium',
    'default'  => array(
    'type'  => 'image',
    'value' => self::$parent->plugin_url() . 'includes/customizer/assets/images/' . $type . '-label-swatch.png',
    'link'  => self::$parent->fs()->get_upgrade_url(),
),
) );