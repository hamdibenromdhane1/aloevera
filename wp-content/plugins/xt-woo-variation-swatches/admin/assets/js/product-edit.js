;( function( $ ) {

    'use strict';

    var XT_WOOVS_TAB = {};
    var $document = $(document);

    XT_WOOVS_TAB = {

        _mainEventsLoaded: false,

        init: function() {

            this.insertLoadingSpinner();
            this.tabEvents();
            this.postSaveEvents();
            this.initialize();
        },

        insertLoadingSpinner: function() {

            $('.xt_woovs_tab a').append('<span class="xt_woovs_spinner"><span></span><span></span><span></span><span></span></span>');
        },

        tabEvents: function() {

            var self = this;

            if( $('._xt_woovs_swatch_type_options_type').length ){

                var toggleType = function(type, $row) {

                    $row.find('.field_option').hide().find(':input').prop('disabled', true);

                    if( type === 'product_color' ){

                        $row.find('.xt_woovs_preview_td').show();
                        $row.find('.field_option_color').show().find(':input').removeProp('disabled');

                    }else if( type === 'product_image' ){

                        $row.find('.xt_woovs_preview_td').show();
                        $row.find('.field_option_image').show().find(':input').removeProp('disabled');

                    }else if( type === 'product_label' ){

                        $row.find('.xt_woovs_preview_td').hide();
                        $row.find('.field_option_label').show().find(':input').removeProp('disabled');
                    }
                }


                $('._xt_woovs_swatch_type_options_type').each(function() {

                    var type = $(this).val();
                    var $attr_row = $(this).parents('.xt_woovs_meta_wrp');

                    toggleType(type, $attr_row);

                });

                $document.on('change', '._xt_woovs_swatch_type_options_type', function(e) {

                    var type = $(this).val();
                    var $attr_row = $(this).parents('.xt_woovs_meta_wrp');

                    if( type !== 'term_options' &&  type !== 'default'){
                        $attr_row.find('.xt_woovs_fields').show();
                    }else{
                        $attr_row.find('.xt_woovs_fields').hide();
                    }

                    toggleType(type, $attr_row);

                    $attr_row.find('.attribute_swatch_type select').val(type);

                    $(document).trigger('woovs_ajax_product_save');

                });


                // On color selected event, update accordion handle preview
                $(document).on('xt_woovs_color_selected', function(e, elem, color) {

                    var $preview = elem.closest('.xt_woovs_meta').find('.xs_woovs_accordion_handle .xt_woovs_color_preview');

                    $preview.css('background-color', color);
                    $preview.css('background-image', '');
                });

                // On color remove event, update accordion handle preview
                $(document).on('xt_woovs_color_removed', function(e, elem, placeholder) {

                    var $preview = elem.closest('.xt_woovs_meta').find('.xs_woovs_accordion_handle .xt_woovs_color_preview');

                    $preview.css('background-color', '');
                    $preview.css('background-image', 'url('+placeholder+')');
                });

                // On image select event, update accordion handle preview
                $(document).on('xt_woovs_image_selected', function(e, elem, image) {

                    var $preview = elem.closest('.xt_woovs_meta').find('.xs_woovs_accordion_handle .xt_woovs_image_preview');

                    $preview.css('background-image', 'url('+image+')');
                });

                // On image remove event, update accordion handle preview
                $(document).on('xt_woovs_image_removed', function(e, elem, placeholder) {

                    var $preview = elem.closest('.xt_woovs_meta').find('.xs_woovs_accordion_handle .xt_woovs_image_preview');

                    $preview.css('background-image', 'url('+placeholder+')');
                });


                $document.on('change', '._xt_woovs_swatch_term_type_options_type', function(e) {

                    var type = $(this).val();
                    var $term_row = $(this).closest('.xt_woovs_field_meta');

                    toggleType(type, $term_row);

                    $(document).trigger('woovs_ajax_product_save');

                });

                self._mainEventsLoaded = true;

            }


            $('#woocommerce-product-data').on('woocommerce_variations_loaded', function(event) {

                self.reloadProductSwatchTabContent();
            });

        },

        postSaveEvents: function() {

            var save_timeout;

            // Make sure the document is ready
            $(document).on('woovs_ajax_product_save', function() {

                if(save_timeout) {
                    clearTimeout(save_timeout);
                }
                save_timeout = setTimeout(function() {

                    // This is the post.php url we localized (via php) above
                    var url = XT_WOOVS_AJAX.post_url;
                    // Serialize form data
                    var data = $('form#post').serializeArray();
                    // Tell PHP what we're doing
                    // NOTE: "name" and "value" are the array keys. This is important. I use int(1) for the value to make sure we don't get a string server-side.
                    data.push({name: 'save_post_ajax', value: 1});

                    // Replaces wp.autosave.initialCompareString
                    var ajax_updated = false;

                    /**
                     * Supercede the WP beforeunload function to remove
                     * the confirm dialog when leaving the page (if we saved via ajax)
                     *
                     * The following line of code SHOULD work in $.post.done(), but
                     *     for some reason, wp.autosave.initialCompareString isn't changed
                     *     when called from wp-includes/js/autosave.js
                     * wp.autosave.initialCompareString = wp.autosave.getCompareString();
                     */
                    $(window).unbind('beforeunload.edit-post');
                    $(window).on( 'beforeunload.edit-post', function() {
                        var editor = typeof tinymce !== 'undefined' && tinymce.get('content');

                        // Use our "ajax_updated" var instead of wp.autosave.initialCompareString
                        if ( ( editor && !editor.isHidden() && editor.isDirty() ) ||
                            ( wp.autosave && wp.autosave.getCompareString() != ajax_updated) ) {
                            return postL10n.saveAlert;
                        }
                    });

                    $('.xt_woovs_tab').addClass('loading');

                    // Post it
                    $.post(url, data, function(response) {
                        // Validate response
                        if (response.success) {
                            // Mark TinyMCE as saved
                            if (typeof tinyMCE !== 'undefined') {
                                tinyMCE.editors.forEach(function(editor) {
                                    editor.isNotDirty = true;
                                });
                            }
                            // Update the saved content for the beforeunload check
                            ajax_updated = wp.autosave.getCompareString();

                        } else {
                            console.log('ERROR: Server returned false. ', response);
                        }
                    }).fail(function(response) {
                        console.log('ERROR: Could not contact server. ', response);
                    }).always(function() {
                        $('.xt_woovs_tab').removeClass('loading');
                    });

                }, 250);
            });


            // On color selected event, update accordion handle preview

            $(document).on('xt_woovs_color_selected', function(e, elem, color) {
                $(document).trigger('woovs_ajax_product_save');
            });

            // On color remove event, update accordion handle preview
            $(document).on('xt_woovs_color_removed', function(e, elem, placeholder) {
                $(document).trigger('woovs_ajax_product_save');
            });

            // On image select event, update accordion handle preview
            $(document).on('xt_woovs_image_selected', function(e, elem, image) {
                $(document).trigger('woovs_ajax_product_save');
            });

            // On image remove event, update accordion handle preview
            $(document).on('xt_woovs_image_removed', function(e, elem, placeholder) {
                $(document).trigger('woovs_ajax_product_save');
            });

        },

        initialize: function() {

            if($( '.xs_woovs_accordion_handle').length) {

                $('.xs_woovs_accordion_handle').next('div').hide();

                $('.xs_woovs_accordion_handle').on('click', function(e) {

                    if($(e.target).is('select')) {
                        return false;
                    }

                    $('.xs_woovs_accordion_handle').not(this).removeClass('active').next('div').slideUp('fast');

                    $(this).toggleClass('active');
                    $(this).next('div').slideToggle('fast');
                });

            }

            $('.xt_woovs_field_row[data-conditions]').each(function() {

                var $this = $(this);
                var key_prefix = $this.data('key_prefix');
                var type = $this.data('type');
                var conditions = $this.data('conditions');
                var $field = $this.find('.xt_woovs_field');

                $field.on('change', function() {
                    $(document).trigger('woovs_ajax_product_save');
                });

                if(conditions.length) {

                    conditions.forEach(function (condition) {

                        var target_value = condition.value;
                        var target_key = condition.key;
                        var type_prefix = condition.type_prefix;

                        if(type_prefix) {
                            target_key = type+'_'+target_key;
                        }

                        var $target_field = $('[data-key_prefix="'+key_prefix+'"][data-field="'+target_key+'"]').find('.xt_woovs_field');

                        $target_field.on('change', function() {

                            var visible = true;

                            var current_value = $(this).val();

                            var op = typeof(condition.op) !== 'undefined' ? condition.op : '==';

                            if(op === '==' && target_value !== current_value) {

                                visible = false;

                            }else if(op === '!=' && target_value === current_value) {

                                visible = false;

                            }else if(op === 'in' && target_value.isArray() && !target_value.includes(current_value)) {

                                visible = false;
                            }

                            if(visible) {
                                $this.removeClass('hidden');
                            }else{
                                $this.addClass('hidden');
                                $field.val('');
                                if($field.attr('type') === 'hidden') {
                                    $field.trigger('change');
                                }
                            }

                        });

                    });
                }

            });
        },

        reloadProductSwatchTabContent: function() {

            var self = this;

            $('.xt_woovs_tab').addClass('loading');

            var data = {
                action: 'xt_woovs_admin_tab_content',
                post_id: XT_WOOVS_AJAX.post_id
            };

            // Post it
            $.post(ajaxurl, data, function(response) {

                // Validate response
                if (response !== '') {

                    $('.xt_woovs_fields').html($(response));

                    if(!self._mainEventsLoaded) {
                        self.tabEvents();
                    }

                    XT_WOOVS_ADMIN.init();
                    self.initialize();

                } else {
                    console.log('ERROR: Server returned false. ', response);
                }

            }).fail(function(response) {
                console.log('ERROR: Could not contact server. ', response);
            }).always(function() {
                $('.xt_woovs_tab').removeClass('loading');
            });
        }

    };


    $(document).ready(function() {

        XT_WOOVS_TAB.init();
        window.XT_WOOVS_TAB = XT_WOOVS_TAB;
    });

})( jQuery );
