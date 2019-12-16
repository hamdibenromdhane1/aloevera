<?php

function xt_woovs_swatch_type() {
    return (xt_woovs_is_single_product()) ? 'single' : 'archives';
}

function xt_woovs_option($id, $default = null) {

    $config_id = XT_Woo_Variation_Swatches_Customizer::$config_id;

    $value = XT_Woo_Variation_Swatches_Customizer::get_option($id, $default);

    if (!empty($_POST['customized'])) {

        $options = json_decode(stripslashes(sanitize_text_field($_POST['customized'])), true);

        if (isset($options[$config_id.'[' . $id . ']'])) {

            $value = $options[$config_id.'[' . $id . ']'];
            if (strpos($options[$config_id.'[' . $id . ']'], '%22') !== false) {
                $value = json_decode(urldecode($value), true);
            }

        }
    }

    return apply_filters('xt_woovs_option', $value, $id, $default, 'xt_woovs');
}

function xt_woovs_option_bool($id, $default = null) {

    return (bool)xt_woovs_option($id, $default);
}

function xt_woovs_type_option($id, $default = null) {

    $type = xt_woovs_swatch_type();
    return xt_woovs_option($type.'_'.$id, $default);
}

function xt_woovs_type_option_bool($id, $default = null) {

    return (bool)xt_woovs_type_option($id, $default);
}

function xt_woovs_option_style($attribute, $id, $default = null, $prefix = null, $suffix = null) {

    $value = xt_woovs_option($id, $default);

    if(empty($value)) {
        return "";
    }

    if($prefix) {
        $value = $prefix.$value;
    }

    if($suffix) {
        $value .= $suffix;
    }

    return esc_attr($attribute.':'.$value.';');
}

function xt_woovs_type_option_style($attribute, $id, $default = null, $prefix = null, $suffix = null) {

    $type = xt_woovs_swatch_type();
    return xt_woovs_option_style($attribute, $type.'_'.$id, $default, $prefix, $suffix);
}

function xt_woovs_update_option($id, $value) {

    $config_id = XT_Woo_Variation_Swatches_Customizer::$config_id;

    $options = get_option($config_id);

    $options[$id] = $value;

    update_option($config_id, $options);
}

function xt_woovs_delete_option($id) {

    $config_id = XT_Woo_Variation_Swatches_Customizer::$config_id;

    $options = get_option($config_id);

    if(isset($options[$id])) {
        unset($options[$id]);
    }

    update_option($config_id, $options);
}

function xt_woovs_is_action($action) {
	
	if(!empty($_GET['xt_woovsaction']) && $_GET['xt_woovsaction'] == $action) {
		return true;
	}
	return false;
}

function xt_woovs_doing_ajax() {

    return (defined('DOING_AJAX') && DOING_AJAX) || (defined('WC_DOING_AJAX') && WC_DOING_AJAX);
}

function xt_woovs_enabled_in_quick_views() {

    $is_ajax = xt_woovs_doing_ajax();

    if($is_ajax && !xt_woovs_fs()->can_use_premium_code__premium_only()) {
        return false;
    }
    return true;
}

function xt_woovs_is_single_product() {

    global $product;

    $queried_object = get_queried_object();

    $is_single = (is_admin()) || (is_product() && method_exists($product, 'get_id') && $queried_object && ($queried_object->ID === $product->get_id()));

    if($is_single && !xt_woovs_doing_ajax()) {

        if(did_action( 'woocommerce_template_single_add_to_cart' ) > 0) {
            $is_single = false;
        }
    }

    return $is_single;
}

function xt_woovs_search_array(&$array, $k, $v) {

    if(!is_array($array)) {
        return null;
    }

    foreach ($array as $key => $value) {
        if ($value[$k] === $v) {
            return $key;
        }
    }
    return null;
}
