<?php
// WooCommerce Hooks

if ( ! function_exists( 'woocommerce_variable_add_to_cart' ) ) {

	/**
	 * Output the variable product add to cart area.
	 */
	function woocommerce_variable_add_to_cart() {
		global $product;

        // Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

        $available_variations = $get_variations ? $product->get_available_variations() : false;
        $attributes = $product->get_variation_attributes();
        $selected_attributes = $product->get_default_attributes();

        if(xt_woovs_enabled_in_quick_views()) {

            $is_single_product = xt_woovs_is_single_product();
            $catalog_mode = false;

            $classes[] = 'xt_woovs-swatches-wrap';
            $classes[] = 'xt_woovs-align-' . xt_woovs_type_option('swatches_align', 'left');
            $classes[] = 'xt_woovs-reset-' . xt_woovs_type_option('variation_reset', 'visible');
            $classes[] = 'xt_woovs-behavior-' . xt_woovs_type_option('swatch_behavior', 'hide');


            if ($is_single_product) {
                $classes[] = 'xt_woovs-attr-label-' . xt_woovs_type_option('attr_label_position', 'inherit');
            } else {

                $catalog_mode = xt_woovs_type_option_bool('catalog_mode', false);

                $catalog_attributes = xt_woovs_type_option('catalog_mode_attributes');

                // <= v1.1.3 / Legacy Code
                $catalog_attribute = xt_woovs_type_option('catalog_mode_attribute');
                if(empty($catalog_attributes) && !empty($catalog_attribute)) {
                    $catalog_attributes = array($catalog_attribute);
                }
                //

                $catalog_custom_attributes = xt_woovs_type_option('catalog_mode_custom_attributes', array());

                if ($catalog_mode && (!empty($catalog_attributes) || !empty($catalog_custom_attributes))) {

                    $classes[] = 'xt_woovs-catalog-mode';

                    $catalog_attribute_found = false;
                    foreach ($attributes as $attribute_name => $options) {

                        if (xt_woovs_search_array($catalog_attributes, 'attribute', $attribute_name) !== null || xt_woovs_search_array($catalog_custom_attributes, 'attribute', $attribute_name) !== null) {

                            $attributes = array();
                            $attributes[$attribute_name] = $options;
                            $catalog_attribute_found = true;
                            break;
                        }
                    }

                    if (!$catalog_attribute_found) {
                        $attributes = array();
                    }
                }
            }

            $classes = apply_filters('xt_woovs_wrap_classes', $classes);
            $classes = implode(" ", $classes);

            $attribute_keys = array_keys($attributes);

            do_action('woocommerce_before_add_to_cart_form'); ?>

            <form class="variations_form cart"
                  action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
                  method="post" enctype='multipart/form-data'
                  data-product_id="<?php echo absint($product->get_id()); ?>"
                  data-product_variations="<?php echo htmlspecialchars(wp_json_encode($available_variations)); // WPCS: XSS ok. ?>">
                <?php do_action('woocommerce_before_variations_form'); ?>

                <?php
                if (empty($available_variations) && false !== $available_variations) : ?>
                    <p class="stock out-of-stock"><?php esc_html_e('This product is currently out of stock and unavailable.', 'woocommerce'); ?></p>
                <?php else : ?>

                    <div class="<?php echo esc_attr($classes); ?>">
                        <?php if ($is_single_product): ?>

                            <table class="variations" cellspacing="0">
                                <tbody>
                                <?php foreach ($attributes as $attribute_name => $options) : ?>
                                    <tr>
                                        <td class="label">
	                                        <label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"><?php echo wc_attribute_label($attribute_name); // WPCS: XSS ok. ?></label>
                                        </td>
                                        <td class="value">
                                            <?php
                                            wc_dropdown_variation_attribute_options(array(
                                                'options' => $options,
                                                'attribute' => $attribute_name,
                                                'product' => $product,
                                            ));
                                            echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>')) : '';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php else: ?>

                            <div class="variations">
                                <?php
                                foreach ($attributes as $attribute_name => $options) :

                                    wc_dropdown_variation_attribute_options(array(
                                        'options' => $options,
                                        'attribute' => $attribute_name,
                                        'product' => $product,
                                    ));
                                    echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>')) : '';

                                endforeach;
                                ?>
                            </div>

                        <?php endif; ?>
                    </div>
                    <?php if (!$catalog_mode): ?>
                        <div class="single_variation_wrap">
                            <?php
                            /**
                             * Hook: woocommerce_before_single_variation.
                             */
                            do_action('woocommerce_before_single_variation');

                            /**
                             * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
                             *
                             * @since 2.4.0
                             * @hooked woocommerce_single_variation - 10 Empty div for variation data.
                             * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
                             */
                            do_action('woocommerce_single_variation');

                            /**
                             * Hook: woocommerce_after_single_variation.
                             */
                            do_action('woocommerce_after_single_variation');
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php do_action('woocommerce_after_variations_form'); ?>
            </form>

            <?php
            do_action('woocommerce_after_add_to_cart_form');

        }else {

            // Load default template.
            wc_get_template('single-product/add-to-cart/variable.php', array(
                'available_variations' => $available_variations,
                'attributes' => $attributes,
                'selected_attributes' => $selected_attributes,
            ));
        }
		
	}
}
