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
class XT_Woo_Variation_Swatches_Admin extends XT_Woo_Variation_Swatches_Admin_Base
{
    /**
     * Init hooks for adding fields to attribute screen
     * Save new term meta
     * Add thumbnail column for attribute term
     */
    public function init_attribute_hooks()
    {
        $attribute_taxonomies = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
        if ( empty($attribute_taxonomies) ) {
            return;
        }
        foreach ( $attribute_taxonomies as $tax ) {
            add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
            add_action(
                'pa_' . $tax->attribute_name . '_edit_form_fields',
                array( $this, 'edit_attribute_fields' ),
                10,
                2
            );
            add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array( $this, 'add_attribute_columns' ) );
            add_filter(
                'manage_pa_' . $tax->attribute_name . '_custom_column',
                array( $this, 'add_attribute_column_content' ),
                10,
                3
            );
        }
        add_action(
            'created_term',
            array( $this, 'save_term_meta' ),
            10,
            2
        );
        add_action(
            'edit_term',
            array( $this, 'save_term_meta' ),
            10,
            2
        );
        add_action(
            'quick_edit_custom_box',
            array( $this, 'quick_edit_attribute_type_field' ),
            10,
            2
        );
    }
    
    /**
     * Add extra attribute types
     * Add color, image and label type
     *
     * @param array $types
     *
     * @return array
     */
    public function add_attribute_types( $types )
    {
        if ( !empty($_POST['action']) && $_POST['action'] === 'woocommerce_save_attributes' ) {
            return $types;
        }
        $types = array_merge( $types, $this->core->types );
        return $types;
    }
    
    /**
     * Get attribute's properties
     *
     * @param string $taxonomy
     *
     * @return object
     */
    public function get_tax_attribute( $taxonomy )
    {
        global  $wpdb ;
        $attr = substr( $taxonomy, 3 );
        $attr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s", $attr ) );
        return $attr;
    }
    
    /**
     * Create hook to add fields to add attribute term screen
     *
     * @param string $taxonomy
     */
    public function add_attribute_fields( $taxonomy )
    {
        $attr = $this->get_tax_attribute( $taxonomy );
        if ( !in_array( $attr->attribute_type, array_keys( $this->core->types ) ) ) {
            return false;
        }
        do_action(
            'xt_woovs_product_attribute_field',
            $attr->attribute_type,
            $attr->attribute_type,
            ucfirst( $attr->attribute_type ),
            '',
            'add'
        );
        do_action(
            'xt_woovs_product_attribute_field',
            'tooltip',
            $attr->attribute_type . '_swatch_tooltip',
            'Tooltip',
            '',
            'add'
        );
    }
    
    /**
     * Create hook to fields to edit attribute term screen
     *
     * @param object $term
     * @param string $taxonomy
     */
    public function edit_attribute_fields( $term, $taxonomy )
    {
        $attr = $this->get_tax_attribute( $taxonomy );
        if ( !in_array( $attr->attribute_type, array_keys( $this->core->types ) ) ) {
            return false;
        }
        $value = get_term_meta( $term->term_id, $attr->attribute_type, true );
        $tooltip = get_term_meta( $term->term_id, $attr->attribute_type . '_swatch_tooltip', true );
        do_action(
            'xt_woovs_product_attribute_field',
            $attr->attribute_type,
            $attr->attribute_type,
            ucfirst( $attr->attribute_type ),
            $value,
            'edit'
        );
        do_action(
            'xt_woovs_product_attribute_field',
            'tooltip',
            $attr->attribute_type . '_swatch_tooltip',
            'Tooltip',
            $tooltip,
            'edit'
        );
        
        if ( $tooltip === 'image' ) {
            $tooltip_image = get_term_meta( $term->term_id, 'swatch_tooltip_image', true );
            do_action(
                'xt_woovs_product_attribute_field',
                'tooltip_image',
                'swatch_tooltip_image',
                'Tooltip Image',
                $tooltip_image,
                'edit'
            );
        } else {
            
            if ( $tooltip === 'text' ) {
                $tooltip_text = get_term_meta( $term->term_id, 'swatch_tooltip_text', true );
                do_action(
                    'xt_woovs_product_attribute_field',
                    'tooltip_text',
                    'swatch_tooltip_text',
                    'Tooltip Text',
                    $tooltip_text,
                    'edit'
                );
            }
        
        }
    
    }
    
    /**
     * Print HTML of custom fields on attribute term screens
     *
     * @param $type
     * @param $value
     * @param $form
     */
    public function attribute_fields(
        $id,
        $meta_key,
        $label,
        $value,
        $form
    )
    {
        // Return if this is a default attribute type
        if ( in_array( $id, array( 'select', 'text', 'label' ) ) ) {
            return;
        }
        // Print the open tag of field container
        printf(
            '<%s class="form-field">%s<label for="xt_woovs-term-%s">%s</label>%s',
            ( 'edit' == $form ? 'tr' : 'div' ),
            ( 'edit' == $form ? '<th>' : '' ),
            $id,
            $label,
            ( 'edit' == $form ? '</th><td>' : '' )
        );
        $this->attribute_swatch_type_field( $id, $meta_key, $value );
        // Print the close tag of field container
        echo  ( 'edit' == $form ? '</td></tr>' : '</div>' ) ;
    }
    
    /**
     * Display markup or template for custom field
     */
    function quick_edit_attribute_type_field( $column_name, $screen )
    {
        // If we're not iterating over our custom column, then skip
        if ( $screen == 'edit-tag' && $column_name != 'thumb' ) {
            return false;
        }
        $post_type = $_REQUEST['post_type'];
        if ( $post_type !== 'product' ) {
            return false;
        }
        $attr = $this->get_tax_attribute( $_REQUEST['taxonomy'] );
        if ( !in_array( $attr->attribute_type, array_keys( $this->core->types ) ) ) {
            return false;
        }
        ?>
	    <fieldset>
	        <div id="gwp-first-appeared" class="inline-edit-col">
	            <label>
	                <span class="title"><?php 
        echo  esc_html__( 'Swatch', 'xt-woo-variation-swatches' ) ;
        ?></span>
	                <span class="input-text-wrap">
	                	<?php 
        $this->attribute_swatch_type_field( $attr->attribute_type, $attr->attribute_type );
        ?>
	                </span>
	            </label>
                <label>
                    <span class="title"><?php 
        echo  esc_html__( 'Tooltip', 'xt-woo-variation-swatches' ) ;
        ?></span>
                    <span class="input-text-wrap">
	                	<?php 
        $this->attribute_swatch_type_field( 'tooltip', $attr->attribute_type . '_swatch_tooltip' );
        ?>
	                </span>
                </label>
                <label class="xt_woovs-inline-edit-hidden">
                    <span class="title"><?php 
        echo  esc_html__( 'Tooltip Image', 'xt-woo-variation-swatches' ) ;
        ?></span>
                    <span class="input-text-wrap">
	                	<?php 
        $this->attribute_swatch_type_field( 'tooltip_image', 'swatch_tooltip_image' );
        ?>
	                </span>
                </label>
                <label class="xt_woovs-inline-edit-hidden">
                    <span class="title"><?php 
        echo  esc_html__( 'Tooltip Text', 'xt-woo-variation-swatches' ) ;
        ?></span>
                    <span class="input-text-wrap">
	                	<?php 
        $this->attribute_swatch_type_field( 'tooltip_text', 'swatch_tooltip_text' );
        ?>
	                </span>
                </label>
            </div>
        </fieldset>
	    <?php 
    }
    
    public function attribute_swatch_type_field( $id, $meta_key, $value = '' )
    {
        $upgrade_notice = '
        <div>
            <span>
                <strong>' . esc_html__( 'Premium Feature!', 'xt-woo-variation-swatches' ) . '</strong>
                <a href="' . esc_url( $this->core->fs()->get_upgrade_url() ) . '">' . esc_html__( 'Upgrade to Unlock!', 'woo-variation-swatches' ) . '</a>
            </span>
        </div><br>';
        switch ( $id ) {
            case 'image':
                $image = ( $value ? wp_get_attachment_image_src( $value ) : '' );
                $image_preview = ( $image ? $image[0] : $this->core->plugin_url( 'admin/assets/images', 'placeholder.png' ) );
                $image_remove_hidden = ( $image ? '' : 'hidden' );
                ?>
               
				<div class="xt_woovs_image_picker swatch_image">
					<img src="<?php 
                echo  esc_url( $image_preview ) ;
                ?>" width="60px" height="60px" />
					<input type="hidden" class="xt_woovs-term-image" name="<?php 
                echo  esc_attr( $meta_key ) ;
                ?>" value="<?php 
                echo  esc_attr( $value ) ;
                ?>" />
					<a href="#" class="button xt_woovs-meta-uploader" data-uploader-title="<?php 
                echo  esc_html__( 'Add image to Attribute ', 'xt-woo-variation-swatches' ) ;
                ?>" data-uploader-button-text="<?php 
                echo  esc_html__( 'Add image to Attribute ', 'xt-woo-variation-swatches' ) ;
                ?>  "> <?php 
                echo  esc_html__( 'Upload/Add image', 'xt-woo-variation-swatches' ) ;
                ?></a>
					<a href="#" class="xt_woovs_remove_meta_img button <?php 
                echo  esc_attr( $image_remove_hidden ) ;
                ?>"><?php 
                echo  esc_html__( 'Remove image', 'xt-woo-variation-swatches' ) ;
                ?></a>
				</div>
				<div class="xt_woovs-clearfix"></div>
				<?php 
                break;
            case 'color':
                ?>
				<input type="text" class="xt_woovs-term-<?php 
                echo  esc_attr( $meta_key ) ;
                ?> xt_woovs-color-picker" name="<?php 
                echo  esc_attr( $meta_key ) ;
                ?>" value="<?php 
                echo  esc_attr( $value ) ;
                ?>" />
				<?php 
                break;
            case 'tooltip':
                echo  $upgrade_notice ;
                break;
            case 'tooltip_image':
                echo  $upgrade_notice ;
                break;
            case 'tooltip_text':
                echo  $upgrade_notice ;
                break;
            default:
                break;
        }
    }
    
    /**
     * Save term meta
     *
     * @param int $term_id
     * @param int $tt_id
     */
    public function save_term_meta( $term_id )
    {
        foreach ( xt_woo_variation_swatches()->types as $type => $label ) {
            $meta_key = $type;
            if ( isset( $_POST[$meta_key] ) ) {
                update_term_meta( $term_id, $meta_key, sanitize_text_field( $_POST[$meta_key] ) );
            }
            $meta_key = $type . '_swatch_tooltip';
            if ( isset( $_POST[$meta_key] ) ) {
                update_term_meta( $term_id, $meta_key, sanitize_text_field( $_POST[$meta_key] ) );
            }
            $meta_key = 'swatch_tooltip_text';
            if ( isset( $_POST[$meta_key] ) ) {
                update_term_meta( $term_id, $meta_key, sanitize_text_field( $_POST[$meta_key] ) );
            }
            $meta_key = 'swatch_tooltip_image';
            if ( isset( $_POST[$meta_key] ) ) {
                update_term_meta( $term_id, $meta_key, sanitize_text_field( $_POST[$meta_key] ) );
            }
        }
    }
    
    /**
     * Add selector for extra attribute types
     *
     * @param $taxonomy
     * @param $index
     */
    public function product_option_terms( $taxonomy, $index )
    {
        if ( !array_key_exists( $taxonomy->attribute_type, xt_woo_variation_swatches()->types ) ) {
            return;
        }
        $taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
        global  $thepostid ;
        ?>

		<select multiple="multiple" data-placeholder="<?php 
        esc_attr_e( 'Select terms', 'xt-woo-variation-swatches' );
        ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php 
        echo  esc_attr( $index ) ;
        ?>][]">
			<?php 
        $all_terms = get_terms( $taxonomy_name, apply_filters( 'woocommerce_product_attribute_terms', array(
            'orderby'    => 'name',
            'hide_empty' => false,
        ) ) );
        if ( $all_terms ) {
            foreach ( $all_terms as $term ) {
                echo  '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy_name, $thepostid ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>' ;
            }
        }
        ?>
		</select>
		<button class="button plus select_all_attributes"><?php 
        esc_html_e( 'Select all', 'xt-woo-variation-swatches' );
        ?></button>
		<button class="button minus select_no_attributes"><?php 
        esc_html_e( 'Select none', 'xt-woo-variation-swatches' );
        ?></button>

		<?php 
    }
    
    /**
     * Add thumbnail column to column list
     *
     * @param array $columns
     *
     * @return array
     */
    public function add_attribute_columns( $columns )
    {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumb'] = esc_html( 'Preview', 'xt-woo-variation-swatches' );
        unset( $columns['cb'] );
        return array_merge( $new_columns, $columns );
    }
    
    /**
     * Render thumbnail HTML depend on attribute type
     *
     * @param $columns
     * @param $column
     * @param $term_id
     */
    public function add_attribute_column_content( $columns, $column, $term_id )
    {
        $attr = $this->get_tax_attribute( $_REQUEST['taxonomy'] );
        echo  $this->get_attribute_swatch_type_field_value( $term_id, $attr->attribute_type, $attr->attribute_type ) ;
        echo  $this->get_attribute_swatch_type_field_value( $term_id, 'tooltip', $attr->attribute_type . '_swatch_tooltip' ) ;
        echo  $this->get_attribute_swatch_type_field_value( $term_id, 'tooltip_image', 'swatch_tooltip_image' ) ;
        echo  $this->get_attribute_swatch_type_field_value( $term_id, 'tooltip_text', 'swatch_tooltip_text' ) ;
    }
    
    public function get_attribute_swatch_type_field_value( $term_id, $id, $meta_key )
    {
        $value = get_term_meta( $term_id, $meta_key, true );
        switch ( $id ) {
            case 'color':
                return sprintf( '<div class="swatch-preview swatch-color" style="background-color:%1$s;"></div>', esc_attr( $value ) );
            case 'image':
                $image = ( is_numeric( $value ) ? wp_get_attachment_image_src( $value ) : $value );
                $image = ( is_array( $image ) ? $image[0] : $image );
                $image = ( empty($image) ? $this->core->plugin_url( 'admin/assets/images', 'placeholder.png' ) : $image );
                return sprintf(
                    '
                    <img class="swatch-preview swatch-image" src="%1$s" width="50px" height="50px">
                    <input class="swatch-image-input" type="hidden" data-url="%2$s" value="%3$s"  />',
                    esc_url( $image ),
                    esc_url( $image ),
                    esc_attr( $value )
                );
            case 'radio':
                return sprintf( '<input type="radio" name="radio" value="%1$s" />', $value );
            case 'tooltip':
                return sprintf( '<input class="swatch-tooltip-input" type="hidden" value="%1$s" />', $value );
            case 'tooltip_image':
                $image = ( is_numeric( $value ) ? wp_get_attachment_image_src( $value ) : $value );
                $image = ( is_array( $image ) ? $image[0] : $image );
                $image = ( empty($image) ? $this->core->plugin_url( 'admin/assets/images', 'placeholder.png' ) : $image );
                return sprintf( '<input class="swatch-tooltip-image-input" type="hidden" data-url="%1$s" value="%2$s" />', esc_url( $image ), esc_attr( $value ) );
            case 'tooltip_text':
                return sprintf( '<input class="swatch-tooltip-text-input" type="hidden" value="%1$s" />', $value );
        }
    }

}