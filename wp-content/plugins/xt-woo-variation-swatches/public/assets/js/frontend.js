;(function( $, window ) {
	'use strict';

    $.extend({
        replaceTag: function (element, tagName, withDataAndEvents, deepWithDataAndEvents) {
            var newTag = $("<" + tagName + ">")[0];
            // From [Stackoverflow: Copy all Attributes](http://stackoverflow.com/a/6753486/2096729)
            $.each(element.attributes, function() {
                newTag.setAttribute(this.name, this.value);
            });
            $(element).children().clone(withDataAndEvents, deepWithDataAndEvents).appendTo(newTag);
            return newTag;
        }
    })
    $.fn.extend({
        replaceTag: function (tagName, withDataAndEvents, deepWithDataAndEvents) {
            // Use map to reconstruct the selector with newly created elements
            return this.map(function() {
                return jQuery.replaceTag(this, tagName, withDataAndEvents, deepWithDataAndEvents);
            })
        }
    });

    $.xt_woovs_variation_form = function(form, options){
        // To avoid scope issues, use 'self' instead of 'this'
        // to reference this class from internal events and functions.
        var self = this;
        self.events = {};

        // Access to jQuery and DOM versions of element
        self.$form = $(form);
        self.form = form;

        // If already loaded, stop;
        if(self.$form.hasClass('xt_woovs-support')) {
            return;
        }

        self.$form.addClass('xt_woovs-support');

        // Initialize native wc_variation_form if not yet initialized
        var currentEvents = self.$form.data('events');
        if(currentEvents && typeof(currentEvents.found_variation) === 'undefined') {

            self.$form.wc_variation_form();
        }

        // Initialization
        self.options = $.extend({}, $.xt_woovs_variation_form.defaultOptions, options);

        self.product_id = self.$form.data("product_id");
        self.$product = self.$form.closest('.product');

        self.$swatches_wrap = self.$form.find('.xt_woovs-swatches-wrap');
        self.$swatches = self.$swatches_wrap.find('.xt_woovs-swatches');

        self.is_archive_product = self.$product.hasClass('xt_woovs-archives-product');
        self.is_single_product = !self.is_archive_product;

        self.original_price = self.$product.find('.price').first().html();
        self.price_selector = self.is_archive_product ? '.price' : '.summary .price';

        self.product_variations = self.$form.data("product_variations");
        self.is_ajax_variation = !self.product_variations;

        self.hidden_behaviour =  self.$swatches_wrap.hasClass("xt_woovs-behavior-hide");
        self.is_mobile = $("body").hasClass("xt_woovs-is-mobile");
        self.reselect_clear = $("body").hasClass("xt_woovs-reselect-clear");
        self.reselect_clear = true;

        self.tooltip_enabled = false;
        self.generated = {};
        self.out_of_stock = {};

        // Add a reverse reference to the DOM object
        self.$form.data('xt_woovs', self);

        self.init = function(){

            self.fixSingleProductPriceDisplay();
            self.initEvents();

            _.delay(function() {
                self.$form.trigger("reload_product_variations");
                self.$form.trigger("xt_woovs_loaded", [self]);
                $(document).trigger("xt_woovs_loaded", [self.$form]);
            }, 1);
        };

        self.fixSingleProductPriceDisplay = function () {

            // on single product page, if product price found, hide the variation price to avoid showing both.

            if(self.is_single_product) {

                var $price = self.$product.find('.summary .price').first();
                var is_variation_price = $price.closest('.woocommerce-variation-price').length > 0;

                if ($price.length && !is_variation_price) {
                    self.$form.addClass('xt_woovs-hide-variation-price');
                }
            }
        };

        self.autoSelectAttributes = function() {

            // If has other attributes without any options selected, select first option of each.
            // This will force the image to switch without having to manually select options.

            _.delay(function() {

                self.$swatches.each(function () {

                    var has_selected = $(this).find('.xt_woovs-selected').length > 0;

                    if (!has_selected) {
                        var swatch = $(this).find('.swatch:not(.xt_woovs-disabled):not(.xt_woovs-selected)').first();

                        if (swatch.length) {
                            swatch.addClass('xt_woovs-selected');
                            swatch.parent().prev().find('select').val(swatch.data('value'));
                        }
                    }
                });

                self.$swatches_wrap.find(':input').first().trigger('change');

            }, 10);
        };

        self.setClearButtonVisibility = function() {

            var $reset = self.$form.find( '.reset_variations' );

            _.delay(function() {

                if ( $reset.css( 'visibility' ) !== 'hidden' ) {

                    $reset.addClass( 'xt_woovs-reset-visible' );

                } else {

                    $reset.removeClass( 'xt_woovs-reset-visible' );
                }

            },2);
        };

        self.initEvents = function() {

            self.resetEvents();

            if(self.is_ajax_variation) {
                self.$form.on('woocommerce_variation_has_changed', self.events.onVariationChanged);
            }
            self.$form.on('woocommerce_update_variation_values', self.events.onUpdateVariationValues);
            self.$form.on('found_variation', self.events.onVariationFound);
            self.$form.on('check_variations', self.events.onCheckVariations);
            self.$form.on('reset_data', self.events.onReset);

            self.$form.on('click', '.swatch', self.events.onSwatchClick);
            self.$form.on('change', '.swatch.swatch-radio', self.events.onSwatchRadioChange);
            self.$form.on('click', '.reset_variations', self.events.onReset);

            self.$form.on('xt_woovs_loaded', self.events.onLoaded);
        };

        self.resetEvents = function() {

            if(self.is_ajax_variation) {
                self.$form.off('woocommerce_variation_has_changed', self.events.onVariationChanged);
            }
            self.$form.off('woocommerce_update_variation_values', self.events.onUpdateVariationValues);
            self.$form.off('found_variation', self.events.onVariationFound);
            self.$form.off('check_variations', self.events.onCheckVariations);
            self.$form.off('reset_data', self.events.onReset);

            self.$form.off('click', '.swatch', self.events.onSwatchClick);
            self.$form.off('change', '.swatch.swatch-radio', self.events.onSwatchRadioChange);
            self.$form.off('click', '.reset_variations', self.events.onReset);

            self.$form.off('xt_woovs_loaded', self.events.onLoaded);
        };

        self.initTooltip = function () {

            var $tooltip = $('.xt_woovs-has-tooltip', self.$form);

            if( $tooltip.length > 0 ){

                $tooltip.on('mouseenter', function(e){ // Hover event

                    if(!self.tooltip_enabled) {

                        self.tooltip_enabled = true;
                        var title = $(this).attr('title');
                        var type = $(this).data('tooltip_type');
                        var value = $(this).data('tooltip_value');

                        value = type === 'text' ? value : '<img src="' + value + '" />';

                        $(this).data('tiptext', title).removeAttr('title');

                        $('<span class="xt_woovs-tooltip">')
                            .addClass('tooltip-' + type)
                            .html(value)
                            .appendTo('body')
                            .css('top', (e.pageY - 10) + 'px')
                            .css('left', (e.pageX + 20) + 'px')
                            .fadeIn('slow');

                    }

                })
                .on('mouseleave', function(e){ // Hover off event

                    $(this).attr('title', $(this).data('tiptext'));
                    $('.xt_woovs-tooltip').remove();

                    self.tooltip_enabled = false;
                })
                .on('mousemove', function(e){ // Mouse move event

                    var mousex = e.pageX ; //Get X coordinates
                    var mousey = e.pageY - $(this).outerHeight() - $('.xt_woovs-tooltip').outerHeight(); //Get Y coordinates

                    $('.xt_woovs-tooltip').css({ top: mousey, left: mousex });

                });
            }
        };

        self.findLoopImage = function () {

            var $image = self.$product.find('img.attachment-shop_catalog');
            if($image.length === 0) {

                $image = self.$product.find('.woocommerce-LoopProduct-link > img');

                if($image.length === 0) {
                    $image = self.$product.find('.woocommerce-LoopProduct-link img').first();

                    if($image.length === 0) {
                        $image = self.$product.find('img.attachment-woocommerce_thumbnail').first();

                        if($image.length === 0) {
                            $image = self.$product.find('img.woocommerce-LoopProduct-link').first();

                            if($image.length === 0) {
                                $image = self.$product.find('img.wp-post-image').first();
                            }
                        }
                    }
                }
            }

            return $image.length > 0 ? $image : null;
        };

        // Form Events

        self.events.onLoaded = function(e) {
            self.initTooltip();
        };

        self.events.onSwatchClick = function(e) {

            e.preventDefault();
            e.stopPropagation();

            var $el = $( this ),
                $select = $el.parent().prev().find( 'select' ),
                value = $el.data( 'value' );

            if(self.reselect_clear) {

                if($el.hasClass('xt_woovs-selected')) {
                    value = "";
                }
            }

            $select.val(value).trigger("change");
            $select.trigger("click");
            $select.trigger("focusin");

            $el.trigger("focus");
            $el.trigger("xt_woovs_selected_item", [value, $select, self.$swatches]);

            if(value === "") {

                $el.removeClass( 'xt_woovs-selected' );

            }else{

                self.autoSelectAttributes();
            }

        };

        self.events.onSwatchRadioChange = function(e) {

            e.preventDefault();
            e.stopPropagation();

            var $el = $( this ),
                $select = $el.parent().prev().find( 'select' ),
                value = $el.val();

            $select.val(e).trigger("change");
            $select.trigger("click");
            $select.trigger("focusin");

            if (self.is_mobile) {
                $select.trigger("touchstart");
            }
            $el.parent("li.swatch-radio").removeClass("xt_woovs-selected xt_woovs-disabled").addClass("xt_woovs-selected");
            $el.parent("li.swatch-radio").trigger("xt_woovs_selected_item", [value, $select, self.$swatches]);

        };

        self.events.onVariationChanged = function(e) {

            self.$swatches.each(function () {

                var $this = $(this);

                var selected = '',
                    options = $this.prev().find('select').find('option'),
                    current = $this.prev().find('select').find('option:selected'),
                    eq = $this.prev().find('select').find('option').eq(1),
                    li = $this.find('li'),
                    selects = [];

                options.each(function () {
                    if ($(this).val() !== '') {
                        selects.push($(this).val());
                        selected = current ? current.val() : eq.val();
                    }
                });

                _.delay(function () {
                    li.each(function () {
                        var attribute_value = $(this).attr('data-value');
                        $(this).removeClass('xt_woovs-selected xt_woovs-disabled');

                        if (attribute_value === selected) {
                            $(this).addClass('xt_woovs-selected');
                            if ($(this).hasClass('radio-variable-item')) {
                                $(this).find('input:radio').prop('disabled', false).prop('checked', true);
                            }
                        }
                    });

                    // Items Updated
                    $this.trigger('xt_woovs_items_updated');
                }, 1);
            });
        };

        self.events.onUpdateVariationValues = function(e) {

            self.$swatches.each(function () {

                var $this = $(this);

                var selected = '',
                    options = $this.prev().find('select').find('option'),
                    current = $this.prev().find('select').find('option:selected'),
                    eq = $this.prev().find('select').find('option').eq(1),
                    li = $this.find('li'),
                    selects = [];

                options.each(function () {
                    if ($(this).val() !== '') {

                        selects.push($(this).val());
                        selected = current ? current.val() : eq.val();
                    }
                });

                _.delay(function () {

                    li.each(function () {
                        var attribute_value = $(this).attr('data-value');
                        $(this).removeClass('xt_woovs-selected').addClass('xt_woovs-disabled');

                        if (_.indexOf(selects, attribute_value) !== -1) {

                            $(this).removeClass('xt_woovs-disabled');

                            if ($(this).hasClass('watch-radio')) {
                                $(this).find('input:radio').prop('disabled', false).prop('checked', false);
                            }

                            if (attribute_value === selected) {

                                $(this).addClass('xt_woovs-selected');

                                if ($(this).hasClass('watch-radio')) {
                                    $(this).find('input:radio').prop('disabled', false).prop('checked', true);
                                }
                            }

                        }else{

                            if ($(this).hasClass('watch-radio')) {
                                $(this).find('input:radio').prop('disabled', true).prop('checked', false);
                            }
                        }

                    });


                    // Items Updated
                    $this.trigger('xt_woovs_items_updated');
                }, 0);
            });

        };

        self.events.onVariationFound = function(e, variation) {

            if(self.$product.length) {

                _.delay(function() {

                    var $price = self.$product.find(self.price_selector).first();
                    var is_variation_price = $price.closest('.woocommerce-variation-price').length > 0;

                    if((variation.price_html !== '') && $price.length && !is_variation_price) {

                        var price_tag = $price.prop("tagName");

                        $price.replaceWith($(variation.price_html).filter('.price'));
                        $price = self.$product.find(self.price_selector).first();
                        $price.replaceWith($price.clone().replaceTag(price_tag, true, true));

                    }

                    if(self.is_archive_product) {
                        var $img = self.findLoopImage();
                        if ($img) {
                            $img.attr('alt', variation.image.alt);
                            $img.attr('thumb_src', variation.image.thumb_src);
                            $img.attr('width', variation.image.thumb_src_h);
                            $img.attr('height', variation.image.thumb_src_w);
                            $img.attr('srcset', variation.image.srcset);
                            $img.attr('sizes', variation.image.sizes);
                        }
                    }

                    self.$form.trigger('xt_woovs_found_variation');

                }, 20);
            }
        };

        self.events.onCheckVariations = function(e) {

            self.setClearButtonVisibility();
        };

        self.events.onReset = function(e) {

            self.$product.find(self.price_selector).first().html(self.original_price);

            self.$swatches.each(function () {
                var li = $(this).find('li');
                li.each(function () {

                    if (!self.is_ajax_variation) {
                        $(this).removeClass('xt_woovs-selected xt_woovs-disabled');

                        if ($(this).hasClass('swatch-radio')) {
                            $(this).find('input:radio').prop('disabled', false).prop('checked', false);
                        }
                    } else {
                        if ($(this).hasClass('swatch-radio')) {
                            //$(this).find('input:radio').prop('checked', false);
                        }
                    }
                });
            });

            self.$form.find( '.reset_variations' ).removeClass( 'xt_woovs-reset-visible' );
            self.$form.trigger('xt_woovs_reset_data');
        };


        // Run initializer
        self.init();


        // Trigger
        $(document).trigger('xt_woovs', [self.$form]);

        return self;
    };

    $.xt_woovs_variation_form.defaultOptions = {};
    $.xt_woovs_variation_form.forms = [];

    $.fn.xt_woovs_variation_form = function (options) {

        return this.each( function() {

            $.xt_woovs_variation_form.forms.push((new $.xt_woovs_variation_form(this, options)));
        });
    };

	$( function () {

	    var init = function() {

            $('.variations_form').each(function () {
                $(this).xt_woovs_variation_form();
            });
        };

        init();

		$( document ).ajaxComplete(function( event, request, settings ) {
            init();
		});

        $( document.body ).trigger( 'xt_woovs_initialized' );

        window.xt_woovs_init = init;
    });

})( jQuery, window );
